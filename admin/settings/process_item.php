<?php
session_start();
require_once '../../includes/Database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid security token');
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    $available = isset($_POST['available']) ? 1 : 0;

    // Validate inputs
    if (empty($name) || empty($category) || $price <= 0) {
        throw new Exception('Please fill all required fields');
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (name, category, description, price, stock_quantity, available) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement');
    }

    $stmt->bind_param("sssdii", $name, $category, $description, $price, $stock_quantity, $available);

    if (!$stmt->execute()) {
        throw new Exception('Failed to add item: ' . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Item added successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}