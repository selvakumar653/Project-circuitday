<?php
// filepath: c:\xampp\htdocs\Project\admin\settings\menu.php
?>
<div class="settings-container">
    <h2>Menu Management</h2>
    <div class="menu-actions">
        <button class="add-item-btn"><i class="fas fa-plus"></i> Add New Item</button>
        <button class="manage-categories-btn"><i class="fas fa-tags"></i> Manage Categories</button>
    </div>
    <div class="menu-items-grid">
        <!-- Menu items will be loaded here -->
    </div>
</div>

<div class="item-availability-form">
    <h3>Update Item Availability</h3>
    <form id="availabilityForm">
        <div class="form-group">
            <label for="itemSelect">Select Item</label>
            <select id="itemSelect" name="item_id" required>
                <?php
                // Connect to database and fetch menu items
                $conn = new mysqli("localhost", "root", "", "hotel_management");
                $result = $conn->query("SELECT id, name, available, stock_quantity FROM menu_items");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']} (Stock: {$row['stock_quantity']})</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="stockQuantity">Stock Quantity</label>
            <input type="number" id="stockQuantity" name="stock_quantity" min="0" required>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" id="available" name="available">
                Available for Order
            </label>
        </div>
        <button type="submit" class="save-btn">Update Availability</button>
    </form>
</div>

<style>
.menu-actions {
    margin: 2rem 0;
    display: flex;
    gap: 1rem;
}

.menu-actions button {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.add-item-btn {
    background: #2ecc71;
    color: white;
}

.manage-categories-btn {
    background: #f1c40f;
    color: #2c3e50;
}
</style>

<script>
document.getElementById('availabilityForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch('update_availability.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Item availability updated successfully!');
            location.reload();
        } else {
            alert('Error updating availability: ' + data.error);
        }
    } catch (error) {
        alert('Error updating availability');
        console.error(error);
    }
});
</script>