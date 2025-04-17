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

// Step 2: Execute SQL query
$sql = "SELECT * FROM bills"; // Your actual table name
$result = $conn->query($sql);

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
        width: 80%;
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
</style>
";

// Step 3: Print data
echo "<div class='container'>";
echo "<h2>Bill Details</h2>";

if ($result->num_rows > 0) {
    echo "<table>
        <tr>
            <th>Bill ID</th>
            <th>Customer Name</th>
            <th>Items</th>
            <th>Quantity</th>
            <th>Total Amount</th>
            <th>Order Date</th>
        </tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['bill_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['items']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>₹" . htmlspecialchars($row['total_amount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='no-results'>No bills found in the database.</p>";
}
echo "</div>";

// Step 4: Close connection
$conn->close();
?>
