<?php
?>
<div class="settings-container">
    <h2>Profile Settings</h2>
    <form class="settings-form">
        <div class="form-group">
            <label>Admin Username</label>
            <input type="text" name="username" value="admin">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email">
        </div>
        <div class="form-group">
            <label>Change Password</label>
            <input type="password" name="password" placeholder="New Password">
        </div>
        <button type="submit" class="save-btn">Save Changes</button>
    </form>
</div>

<style>
.settings-container {
    padding: 2rem;
}

.settings-form {
    max-width: 500px;
    margin: 2rem 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.save-btn {
    background: #3498db;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
}

.save-btn:hover {
    background: #2980b9;
}
</style>