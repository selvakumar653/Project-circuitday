<?php
$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $available = isset($_POST['available']) ? 1 : 0;
    $alert_threshold = isset($_POST['alert_threshold']) ? (int)$_POST['alert_threshold'] : 5; // Default value of 5

    $stmt = $conn->prepare("UPDATE menu_items SET stock_quantity = ?, available = ?, alert_threshold = ? WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("iiii", $stock_quantity, $available, $alert_threshold, $item_id);
    
    if ($stmt->execute()) {
        $success_message = "Stock updated successfully!";
    } else {
        $error_message = "Error updating stock: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all menu items
$result = $conn->query("SELECT id, name, stock_quantity, alert_threshold, available FROM menu_items ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stock-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stock-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stock-card:hover {
            transform: translateY(-5px);
        }

        .stock-level {
            font-size: 2rem;
            font-weight: bold;
            margin: 1rem 0;
        }

        .stock-level.low {
            color: #e74c3c;
        }

        .stock-level.medium {
            color: #f1c40f;
        }

        .stock-level.high {
            color: #2ecc71;
        }

        .update-form {
            margin-top: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .stock-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-warning {
            background: #f1c40f;
            color: #2c3e50;
        }

        .btn-warning:hover {
            background: #f39c12;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stock-indicator {
            width: 100%;
            height: 6px;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .stock-indicator-fill {
            height: 100%;
            transition: width 0.3s ease;
        }

        .stock-warning {
            background: #fff3cd;
            color: #856404;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stock-warning i {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="stock-container">
        <div class="stock-header">
            <h2>Stock Management</h2>
            <button class="btn btn-primary" onclick="refreshStock()">
                <i class="fas fa-sync-alt"></i> Refresh Stock
            </button>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="stock-grid">
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="stock-card">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    
                    <form class="update-form" method="POST">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        
                        <div class="form-group">
                            <label for="stock_quantity_<?php echo $item['id']; ?>">Current Stock</label>
                            <input type="number" 
                                   id="stock_quantity_<?php echo $item['id']; ?>" 
                                   name="stock_quantity" 
                                   value="<?php echo $item['stock_quantity']; ?>" 
                                   min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="alert_threshold_<?php echo $item['id']; ?>">Alert When Stock Below</label>
                            <input type="number" 
                                   id="alert_threshold_<?php echo $item['id']; ?>" 
                                   name="alert_threshold" 
                                   value="<?php echo $item['alert_threshold']; ?>" 
                                   min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" 
                                       name="available" 
                                       <?php echo $item['available'] ? 'checked' : ''; ?>>
                                Available for Order
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Stock
                        </button>
                    </form>

                    <?php if ($item['stock_quantity'] <= $item['alert_threshold']): ?>
                        <div class="stock-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Low Stock Alert!
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function refreshStock() {
            location.reload();
        }

        function alertLowStock(itemName) {
            alert(`Low stock alert for ${itemName}! Please reorder soon.`);
        }

        // Auto-hide alerts after 3 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 3000);
    </script>
</body>
</html>