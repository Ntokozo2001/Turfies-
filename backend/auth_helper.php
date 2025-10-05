<?php
// Admin authentication helper
// Include this file at the top of admin pages that require admin access

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as admin
function requireAdminLogin($redirectTo = 'login.php') {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true || !isset($_SESSION['admin_id'])) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

// Check if user is logged in (either admin or regular user)
function requireLogin($redirectTo = 'login.php') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

// Get current admin info
function getCurrentAdmin() {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        return [
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null,
            'employee_id' => $_SESSION['admin_employee_id'] ?? null
        ];
    }
    return null;
}

// Get current user info
function getCurrentUser() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }
    return null;
}

// Check if current session is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}
?>