<?php
// Prevent any output before headers
ob_start();

// Error handling - log to file instead of output
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', 'c:/xampp/php/logs/php_error.log');

// Clear any previous output
if (ob_get_length()) ob_clean();

// Set proper JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Get POST data
    $json = file_get_contents('php://input');
    
    if (!$json) {
        throw new Exception('No data received');
    }

    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    if (!isset($data['items']) || !isset($data['totalAmount'])) {
        throw new Exception('Missing required fields');
    }

    // Connect to database
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli('localhost', 'root', '', 'hotel_management');
    
    // Set charset to ensure proper JSON encoding
    $conn->set_charset('utf8mb4');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Format items as string
    $itemsString = '';
    foreach ($data['items'] as $item) {
        $itemsString .= $item['name'] . ' x' . $item['quantity'] . ', ';
    }
    $itemsString = rtrim($itemsString, ', ');

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO bills (customer_name, items, quantity, total_amount, order_date) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    // Bind parameters
    if (!$stmt->bind_param("ssid", 
        $data['customerName'],
        $itemsString,
        $data['quantity'],
        $data['totalAmount']
    )) {
        throw new Exception('Failed to bind parameters: ' . $stmt->error);
    }

    // Execute statement
    if ($stmt->execute()) {
        // Update stock quantities
        foreach ($data['items'] as $item) {
            $updateStmt = $conn->prepare("UPDATE menu_items SET stock_quantity = stock_quantity - ? WHERE name = ?");
            $updateStmt->bind_param("is", $item['quantity'], $item['name']);
            $updateStmt->execute();
            $updateStmt->close();
        }

        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Order processed successfully',
            'orderId' => $stmt->insert_id
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Close connections
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>