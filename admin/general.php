<?php
include '../db_connect.php';
?>
<div class="settings-container">
    <h2>General Settings</h2>
    <form id="generalSettingsForm">
        <div class="setting-group">
            <label>Restaurant Name</label>
            <input type="text" name="restaurant_name" value="Chellappa Hotel">
        </div>
        
        <div class="setting-group">
            <label>Contact Email</label>
            <input type="email" name="contact_email">
        </div>
        
        <div class="setting-group">
            <label>Contact Phone</label>
            <input type="tel" name="contact_phone">
        </div>
        
        <div class="setting-group">
            <label>Opening Hours</label>
            <div class="time-inputs">
                <input type="time" name="opening_time">
                <span>to</span>
                <input type="time" name="closing_time">
            </div>
        </div>
        
        <div class="setting-group">
            <label>Maintenance Mode</label>
            <label class="switch">
                <input type="checkbox" name="maintenance_mode">
                <span class="slider"></span>
            </label>
        </div>
        
        <button type="submit" class="save-btn">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </form>
</div>

<style>
.settings-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.setting-group {
    margin-bottom: 1.5rem;
}

.setting-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.setting-group input[type="text"],
.setting-group input[type="email"],
.setting-group input[type="tel"],
.setting-group input[type="time"] {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.time-inputs {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.save-btn {
    background: #2196F3;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.save-btn:hover {
    background: #1976D2;
}
</style>

<script>
document.getElementById('generalSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('settings/save_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Error saving settings');
        }
    });
});
</script>