<?php
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new mysqli("localhost", "root", "", "hotel_management");

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Read SQL file
    $sqlFile = __DIR__ . '/setup.sql';
    $sql = file_get_contents($sqlFile);

    // Split SQL by semicolon and execute each query separately
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($queries as $query) {
        if (empty($query)) continue;
        
        if ($conn->query($query) === false) {
            throw new Exception("Query failed: " . $conn->error . "\nQuery: " . $query);
        }
        echo "Executed query successfully.\n";
    }

    echo "\nDatabase setup completed successfully!\n";
    echo "Tables created: users, bills\n";
    echo "Sample data inserted\n";

} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}