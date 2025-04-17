<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add this validation function at the top of the file after the initial headers
function validateLocationNumber($type, $number) {
    if (!is_numeric($number)) {
        return false;
    }
    
    switch ($type) {
        case 'table':
            return $number >= 1 && $number <= 50; // Assuming max 50 tables
        case 'room':
            return $number >= 100 && $number <= 999; // Assuming room numbers 100-999
        case 'takeaway':
            return $number >= 1000 && $number <= 9999; // 4-digit waiting numbers
        default:
            return false;
    }
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Replace the existing validation block with this enhanced version
if (!isset($data['type']) || !isset($data['location']) || !isset($data['total']) || !isset($data['items'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required data',
        'debug' => $data
    ]);
    exit;
}

// Validate location number format
if (!validateLocationNumber($data['type'], $data['location'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid ' . $data['type'] . ' number format. Must be numeric and within valid range.',
        'debug' => [
            'type' => $data['type'],
            'location' => $data['location']
        ]
    ]);
    exit;
}

// Convert location to integer for storage
$data['location'] = (int)$data['location'];

// Validate items array
if (!is_array($data['items']) || empty($data['items'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or empty items array'
    ]);
    exit;
}

try {
    // Connect to database
    $conn = new mysqli("localhost", "root", "", "hotel_management");

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Log incoming data
        error_log('Processing order: ' . json_encode($data));

        // Insert order
        $sql = "INSERT INTO orders (order_type, location_number, total_amount, status) 
                VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $data['type'], $data['location'], $data['total']);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order: " . $stmt->error);
        }
        
        $orderId = $conn->insert_id;

        // Insert order items
        $sql = "INSERT INTO order_items (order_id, food_id, item_name, quantity, unit_price, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($data['items'] as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->bind_param("iisids", 
                $orderId,
                $item['id'],
                $item['name'],
                $item['quantity'],
                $item['price'],
                $subtotal
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert order item: " . $stmt->error);
            }
        }

        // Insert into bills table
        $currentDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO bills (order_id, location_type, location_number, total_amount, order_date, status) 
                VALUES (?, ?, ?, ?, ?, 'unpaid')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issds", 
            $orderId, 
            $data['type'], 
            $data['location'], 
            $data['total'], 
            $currentDate
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert bill: " . $stmt->error);
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'orderId' => $orderId,
            'message' => 'Order placed successfully'
        ]);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>