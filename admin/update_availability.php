<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed']));
}

$item_id = $_POST['item_id'];
$stock_quantity = $_POST['stock_quantity'];
$available = isset($_POST['available']) ? 1 : 0;

$stmt = $conn->prepare("UPDATE menu_items SET stock_quantity = ?, available = ? WHERE id = ?");
$stmt->bind_param("iii", $stock_quantity, $available, $item_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>