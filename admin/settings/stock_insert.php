<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/Database.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

try {
    // Verify and update database connection credentials
    $mysqli = new mysqli("localhost", "root", "", "hotel_management" );

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid security token');
        }

        // Validate inputs
        $item_id = filter_var($_POST['item_id'] ?? null, FILTER_VALIDATE_INT);
        $stock_quantity = filter_var($_POST['stock_quantity'] ?? null, FILTER_VALIDATE_INT);
        $available = isset($_POST['available']) ? 1 : 0;

        if (!$item_id || $stock_quantity === false) {
            throw new Exception('Invalid input data');
        }

        // Update stock
        $stmt = $mysqli->prepare("UPDATE menu_items SET stock_quantity = ?, available = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement');
        }

        $stmt->bind_param("iii", $stock_quantity, $available, $item_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update stock');
        }

        $stmt->close();
        $success = 'Stock updated successfully';
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stock</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .error {
            color: #dc3545;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #f8d7da;
        }

        .success {
            color: #28a745;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #d4edda;
        }

        button {
            background: #3498db;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #2980b9;
        }

        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(200%);
            animation: slideIn 0.5s forwards, fadeOut 0.5s 4.5s forwards;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .alert-error {
            background: linear-gradient(135deg, #dc3545, #f72c47);
            color: white;
        }

        .alert i {
            font-size: 1.2em;
        }

        @keyframes slideIn {
            from { transform: translateX(200%); }
            to { transform: translateX(0); }
        }

        @keyframes fadeOut {
            from { 
                opacity: 1;
                transform: translateX(0);
            }
            to { 
                opacity: 0;
                transform: translateX(200%);
            }
        }

        .alert-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255,255,255,0.5);
            width: 100%;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Stock</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
                <div class="alert-progress"></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
                <div class="alert-progress"></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="item_id">Item ID *</label>
                <input type="number" id="item_id" name="item_id" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="available" checked>
                    Available for Order
                </label>
            </div>

            <button type="submit">Update Stock</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-remove alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            });

            // Add click to dismiss
            alerts.forEach(alert => {
                alert.addEventListener('click', () => {
                    alert.style.animation = 'fadeOut 0.5s forwards';
                    setTimeout(() => alert.remove(), 500);
                });
            });
        });
    </script>
</body>
</html>