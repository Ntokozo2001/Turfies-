<?php
// sidebar-admin.php - Reusable admin sidebar component
?>
<aside class="admin-sidebar">
    <h2>Admin Options</h2>
    <ul>
        <li><a href="/Turfies Code/Frontend/admin-dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php') ? 'class="active"' : ''; ?>>Dashboard</a></li>
        <li><a href="/Turfies Code/Frontend/admin/manage-user.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-user.php') ? 'class="active"' : ''; ?>>Manage User</a></li>
        <li><a href="/Turfies Code/Frontend/admin/manage-products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-products.php') ? 'class="active"' : ''; ?>>Manage Products</a></li>
        <li><a href="/Turfies Code/Frontend/admin/manage-request.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-request.php') ? 'class="active"' : ''; ?>>Manage Requests</a></li>
        <li><a href="/Turfies Code/Frontend/admin/manage-orders.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-orders.php') ? 'class="active"' : ''; ?>>Manage Orders</a></li>
        <li><a href="/Turfies Code/Frontend/admin/settings.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>>Settings</a></li>
        <li><a href="/Turfies Code/backend/admin/logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a></li>
        <li><a href="/Turfies Code/Frontend/index.php">Go To Market</a></li>
    </ul>
</aside>

<style>
.admin-sidebar {
    background: #1976d2;
    color: #fff;
    width: 220px;
    padding: 24px 0;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
}
.admin-sidebar h2 {
    color: #ffbe19;
    font-size: 1.3rem;
    font-weight: bold;
    padding: 0 24px 24px 24px;
    margin: 0;
    border-bottom: 2px solid #ffbe19;
}
.admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.admin-sidebar li {
    margin: 8px 0;
}
.admin-sidebar a {
    color: #fff;
    text-decoration: none;
    display: block;
    padding: 12px 24px;
    font-size: 1rem;
    transition: background 0.2s;
}
.admin-sidebar a:hover, .admin-sidebar a.active {
    background: #ffbe19;
    color: #1976d2;
}
</style>