<?php
header('Content-Type: application/json');
error_reporting(E_ERROR | E_PARSE);

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "hotel_management");

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if tables exist
    $tables = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tables->num_rows == 0) {
        throw new Exception("Required tables not found. Please run setup.sql");
    }

    // Get user count with error handling
    $userQuery = "SELECT COUNT(*) as count FROM users";
    $userResult = $conn->query($userQuery);
    
    if ($userResult === false) {
        throw new Exception("Error counting users: " . $conn->error);
    }
    
    $userCount = $userResult->fetch_assoc()['count'];

    // Get order count and revenue
    $orderQuery = "SELECT 
        COUNT(*) as order_count,
        COALESCE(SUM(total_amount), 0) as total_revenue 
        FROM bills 
        WHERE status != 'cancelled'";
    
    $orderResult = $conn->query($orderQuery);
    
    if ($orderResult === false) {
        throw new Exception("Error counting orders: " . $conn->error);
    }
    
    $orderStats = $orderResult->fetch_assoc();

    // Format response with default values if null
    echo json_encode([
        'success' => true,
        'counts' => [
            'users' => (int)($userCount ?? 0),
            'orders' => (int)($orderStats['order_count'] ?? 0),
            'revenue' => number_format((float)($orderStats['total_revenue'] ?? 0), 2)
        ]
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch data: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}