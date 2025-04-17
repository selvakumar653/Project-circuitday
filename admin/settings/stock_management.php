<?php
session_start();
require_once '../../includes/Database.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch all menu items
$result = $conn->query("SELECT id, name, stock_quantity, available FROM menu_items ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="container">
        <h2>Stock Management</h2>
        
        <!-- Add New Item Form -->
        <div class="add-item-form">
            <h3>Add New Item</h3>
            <form id="addItemForm" class="stock-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" id="item_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="initial_stock">Initial Stock</label>
                    <input type="number" id="initial_stock" name="stock_quantity" min="0" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="available" checked>
                        Available for Order
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Add Item</button>
            </form>
            <div class="status-message" id="addItemStatus"></div>
        </div>

        <!-- Existing Items Grid -->
        <div class="stock-grid">
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="stock-card" data-id="<?php echo $item['id']; ?>">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <form class="stock-form" onsubmit="updateStock(event, <?php echo $item['id']; ?>)">
                        <div class="form-group">
                            <label for="stock_<?php echo $item['id']; ?>">Stock Quantity</label>
                            <input type="number" 
                                   id="stock_<?php echo $item['id']; ?>" 
                                   name="stock_quantity" 
                                   value="<?php echo $item['stock_quantity']; ?>" 
                                   min="0" required>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" 
                                       name="available" 
                                       <?php echo $item['available'] ? 'checked' : ''; ?>>
                                Available for Order
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </form>
                    <div class="status-message"></div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
    // Add new item function
    document.getElementById('addItemForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = event.target;
        const statusDiv = document.getElementById('addItemStatus');
        const formData = new FormData(form);

        fetch('stock_insert.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusDiv.textContent = 'Item added successfully!';
                statusDiv.className = 'status-message success';
                form.reset();
                // Reload the page after 1 second to show new item
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.error || 'Failed to add item');
            }
        })
        .catch(error => {
            statusDiv.textContent = error.message;
            statusDiv.className = 'status-message error';
        });
    });

    function updateStock(event, itemId) {
        event.preventDefault();
        const form = event.target;
        const statusDiv = form.closest('.stock-card').querySelector('.status-message');
        const formData = new FormData(form);
        formData.append('item_id', itemId);

        fetch('stock_insert.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            statusDiv.textContent = data.message || data.error;
            statusDiv.className = 'status-message ' + (data.success ? 'success' : 'error');
            setTimeout(() => {
                statusDiv.textContent = '';
                statusDiv.className = 'status-message';
            }, 3000);
        })
        .catch(error => {
            statusDiv.textContent = 'Failed to update stock';
            statusDiv.className = 'status-message error';
        });
    }
    </script>

    <style>
    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .stock-card {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-message {
        margin-top: 1rem;
        padding: 0.5rem;
        border-radius: 4px;
    }

    .status-message.success {
        background: #d4edda;
        color: #155724;
    }

    .status-message.error {
        background: #f8d7da;
        color: #721c24;
    }

    /* Add these new styles */
    .add-item-form {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .add-item-form h3 {
        margin-top: 0;
        color: #333;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #666;
    }

    .form-group input[type="text"],
    .form-group input[type="number"] {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    </style>
</body>
</html>