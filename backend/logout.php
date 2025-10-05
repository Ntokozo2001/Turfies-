<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../Frontend/login.php');
    exit;
}

// Store user info before destroying session
$userName = $_SESSION['user_name'] ?? ($_SESSION['admin_username'] ?? 'User');
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Destroy all session data
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Set a logout message (we need to start a new session for this)
session_start();

// Set logout message based on user type
if ($isAdmin) {
    $_SESSION['logout_message'] = "Goodbye, Admin $userName! You have been successfully logged out from the admin panel.";
} else {
    $_SESSION['logout_message'] = "Goodbye, $userName! You have been successfully logged out.";
}

// Redirect to login page
header('Location: ../Frontend/login.php');
exit;
?>