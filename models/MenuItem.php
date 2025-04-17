<?php
require_once '../includes/db_connect.php';

class MenuItem {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllItems() {
        return $this->db->query("SELECT * FROM menu_items ORDER BY category, name");
    }

    public function updateStock($id, $quantity, $available) {
        return $this->db->query(
            "UPDATE menu_items SET stock_quantity = ?, available = ? WHERE id = ?",
            [$quantity, $available, $id]
        );
    }

    public function checkAvailability($items) {
        $unavailableItems = [];
        
        foreach ($items as $item) {
            $result = $this->db->query(
                "SELECT available, stock_quantity FROM menu_items WHERE name = ?",
                [$item['name']]
            );
            
            if ($row = $result->fetch_assoc()) {
                if (!$row['available'] || $row['stock_quantity'] < $item['quantity']) {
                    $unavailableItems[] = [
                        'name' => $item['name'],
                        'available' => $row['available'],
                        'stock' => $row['stock_quantity']
                    ];
                }
            }
        }
        
        return $unavailableItems;
    }

    public function addNewItem($data) {
        return $this->db->query(
            "INSERT INTO menu_items (name, category, price, stock_quantity, alert_threshold, available) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$data['name'], $data['category'], $data['price'], $data['stock'], $data['threshold'], $data['available']]
        );
    }
}