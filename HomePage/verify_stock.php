<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../includes/Database.php';
    
    $db = Database::getInstance();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['items']) || !is_array($data['items'])) {
        throw new Exception('Invalid request data');
    }

    $unavailableItems = [];
    
    $db->begin_transaction();

    foreach ($data['items'] as $item) {
        if (!isset($item['name']) || !isset($item['quantity'])) {
            throw new Exception('Invalid item data');
        }

        $stmt = $db->prepare("SELECT name, stock_quantity FROM menu_items WHERE name = ? FOR UPDATE");
        $stmt->bind_param('s', $item['name']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if ($row['stock_quantity'] < $item['quantity']) {
                $unavailableItems[] = [
                    'name' => $item['name'],
                    'available' => $row['stock_quantity'] > 0,
                    'stock' => $row['stock_quantity'],
                    'requested' => $item['quantity']
                ];
            }
        }
        $stmt->close();
    }

    if (empty($unavailableItems)) {
        foreach ($data['items'] as $item) {
            $stmt = $db->prepare("UPDATE menu_items SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE name = ? AND stock_quantity >= ?");
            $stmt->bind_param('isi', $item['quantity'], $item['name'], $item['quantity']);
            $stmt->execute();
            $stmt->close();
        }

        $db->commit();
        echo json_encode([
            "success" => true,
            "message" => "Stock verified"
        ]);
    } else {
        $db->rollback();
        echo json_encode([
            "success" => false,
            "message" => "Some items are not available in requested quantity",
            "unavailableItems" => $unavailableItems
        ]);
    }

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}