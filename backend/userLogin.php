<?php
session_start();
require_once __DIR__ . '/db.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Frontend/login.php');
    exit;
}

// Get POST data from form
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate required fields
$errors = [];
if (empty($email)) {
    $errors[] = 'Email is required';
}
if (empty($password)) {
    $errors[] = 'Password is required';
}

// Validate email format
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if ($errors) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_data'] = ['email' => $email];
    header('Location: ../Frontend/login.php');
    exit;
}

// First, try to login as admin
$stmt = $pdo->prepare('SELECT admin_id, username, email, password_hash, employee_id FROM admin WHERE email = ? LIMIT 1');
$stmt->execute([strtolower($email)]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password_hash'])) {
    // Admin login successful - set admin session data
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_employee_id'] = $admin['employee_id'];
    $_SESSION['is_admin'] = true;
    $_SESSION['logged_in'] = true;

    // Clear any previous login data and set success message
    unset($_SESSION['login_errors']);
    unset($_SESSION['login_data']);
    $_SESSION['login_success'] = 'Welcome back, Admin ' . $admin['username'] . '!';

    // Redirect to admin dashboard after successful admin login
    header('Location: ../Frontend/admin-dashboard.php');
    exit;
}

// If not admin, try to login as regular user
$stmt = $pdo->prepare('SELECT user_id, full_name, email, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([strtolower($email)]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    // User login successful - set user session data
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = false;
    $_SESSION['logged_in'] = true;

    // Clear any previous login data and set success message
    unset($_SESSION['login_errors']);
    unset($_SESSION['login_data']);
    $_SESSION['login_success'] = 'Welcome back, ' . $user['full_name'] . '!';

    // Redirect to user account page after successful user login
    header('Location: ../Frontend/account.php');
    exit;
}

// Neither admin nor user login was successful
$_SESSION['login_errors'] = ['Invalid email or password'];
$_SESSION['login_data'] = ['email' => $email];
header('Location: ../Frontend/login.php');
exit;
?>