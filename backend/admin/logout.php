<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the logout attempt
error_log("Admin logout attempt - Session ID: " . session_id());

// Check if admin is logged in
if (isset($_SESSION['admin_id']) || isset($_SESSION['admin_logged_in'])) {
    // Log the admin details before logout
    $adminId = $_SESSION['admin_id'] ?? 'Unknown';
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    error_log("Admin logout: ID=$adminId, Name=$adminName");
    
    // Destroy all session data
    $_SESSION = array();
    
    // If it's desired to kill the session cookie as well
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Clear any authentication cookies if they exist
    if (isset($_COOKIE['admin_remember'])) {
        setcookie('admin_remember', '', time() - 3600, '/');
    }
    
    // Log successful logout
    error_log("Admin logout successful");
    
    // Set success message for redirect
    session_start(); // Start new session for flash message
    $_SESSION['logout_message'] = 'You have been successfully logged out.';
    
} else {
    // No active session found
    error_log("Logout attempt with no active admin session");
    
    // Start session for message
    session_start();
    $_SESSION['logout_message'] = 'No active session found.';
}

// Redirect to login page
$loginPage = '../../Frontend/login.php';

// Check if login page exists, otherwise redirect to a fallback
if (file_exists(__DIR__ . '/../../Frontend/login.php')) {
    $redirectUrl = $loginPage;
} else {
    // Fallback to index page if login page doesn't exist
    $redirectUrl = '../../Frontend/index.php';
    error_log("Login page not found, redirecting to index");
}

// Perform the redirect
header("Location: $redirectUrl");
exit();
?>
