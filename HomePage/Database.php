<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // Update these credentials according to your database configuration
        $host = 'localhost';
        $username = 'root';  // default XAMPP username
        $password = '';      // default XAMPP password
        $database = 'hotel_management'; // your database name

        $this->connection = new mysqli($host, $username, $password, $database);

        if ($this->connection->connect_error) {
            throw new Exception('Database connection failed: ' . $this->connection->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Failed to prepare statement: ' . $this->connection->error);
        }
        
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute statement: ' . $stmt->error);
        }
        
        return $stmt->get_result();
    }

    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function rollback() {
        $this->connection->rollback();
    }
}