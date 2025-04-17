<?php
require_once '../includes/db_connect.php';
require_once '../includes/utils.php';
require_once '../models/MenuItem.php';

try {
    $menuItem = new MenuItem();
    $result = $menuItem->getAllItems();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'name' => Utils::sanitizeInput($row['name']),
            'price' => Utils::formatPrice($row['price']),
            'description' => Utils::sanitizeInput($row['description']),
            'category' => Utils::sanitizeInput($row['category']),
            'available' => (bool)$row['available'],
            'stock_quantity' => (int)$row['stock_quantity'],
            'isLowStock' => $row['stock_quantity'] <= LOW_STOCK_THRESHOLD
        ];
    }
    
    Utils::respondJSON(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    error_log($e->getMessage());
    Utils::respondJSON(['success' => false, 'error' => 'Failed to fetch menu items']);
}