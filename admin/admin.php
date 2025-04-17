<?php
// Step 1: Database connection
$servername = "localhost";
$username = "root";       // Update if needed
$password = "";           // Update if needed
$dbname = "hotel_management"; // Your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CSS Styling
echo "
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 100%;
        margin: 50px auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
        color: #333;
    }

    th {
        background-color: #007BFF;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .no-results {
        text-align: center;
        font-size: 18px;
        color: #888;
    }

    .controls {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-box {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 200px;
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        color: #007BFF;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #ddd;
        margin: 0 4px;
    }

    .pagination a.active {
        background-color: #007BFF;
        color: white;
    }

    .refresh-btn {
        padding: 8px 16px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
";

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Modify query with search and pagination
$where_clause = $search ? "WHERE `fullname` LIKE '%$search%' OR `email` LIKE '%$search%'" : "";
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `users` $where_clause LIMIT $offset, $per_page";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$total_rows = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
$total_pages = ceil($total_rows / $per_page);

echo "<div class='container'>";
echo "<h2>Users Table</h2>";
echo "<div class='controls'>";
echo "<input type='text' class='search-box' placeholder='Search users...' value='" . htmlspecialchars($search) . "' 
    onkeyup='if(event.key === \"Enter\") window.location.href=\"?search=\"+this.value'>";
echo "<button class='refresh-btn' onclick='window.location.href=\"\"'>Refresh</button>";
echo "</div>";

if ($result->num_rows > 0) {
    echo "<table><tr>";

    // Print column headers
    $fields = $result->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";

    // Print rows
    $result->data_seek(0); // Reset pointer
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach($row as $val) {
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    // Add pagination
    if ($total_pages > 1) {
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i == $page ? "class='active'" : "";
            echo "<a href='?page=$i&search=$search' $active>$i</a>";
        }
        echo "</div>";
    }
} else {
    echo "<p class='no-results'>No users found matching your criteria.</p>";
}
echo "</div>";

// Step 4: Close connection
$conn->close();
?>
