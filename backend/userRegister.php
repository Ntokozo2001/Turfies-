<?php
session_start();
require_once __DIR__ . '/db.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Frontend/signup.php');
    exit;
}

// Helper: Validate email
function is_valid_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper: Validate WhatsApp number (basic)
function is_valid_whatsapp($number) {
	return preg_match('/^[0-9]{7,15}$/', $number);
}

// Get POST data from form
$input = $_POST;

// Map form field names to expected names
$required = ['full_name' => 'fullname', 'school' => 'school', 'email' => 'email', 'whatsapp_number' => 'whatsapp', 'password' => 'password'];
$errors = [];

// Validate required fields
foreach ($required as $db_field => $form_field) {
	if (empty($input[$form_field])) {
		$errors[] = ucwords(str_replace('_', ' ', $db_field)) . ' is required';
	}
}

// Additional validation
if (!empty($input['email']) && !is_valid_email($input['email'])) {
	$errors[] = 'Invalid email format';
}
if (!empty($input['whatsapp']) && !is_valid_whatsapp($input['whatsapp'])) {
	$errors[] = 'Invalid WhatsApp number';
}

// Check password confirmation
if (!empty($input['password']) && !empty($input['confirm_password'])) {
    if ($input['password'] !== $input['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
}

if ($errors) {
	$_SESSION['signup_errors'] = $errors;
	$_SESSION['signup_data'] = $input; // Keep form data for user convenience
	header('Location: ../Frontend/signup.php');
	exit;
}

$full_name = trim($input['fullname']);
$school = trim($input['school']);
$email = strtolower(trim($input['email']));
$whatsapp = trim($input['whatsapp']);
$password = $input['password'];
$now = date('Y-m-d H:i:s');

// Check for duplicate email or WhatsApp in users table
$stmt = $pdo->prepare('SELECT user_id, email, whatsapp_number FROM users WHERE email = ? OR whatsapp_number = ? LIMIT 1');
$stmt->execute([$email, $whatsapp]);
$existingUser = $stmt->fetch();

if ($existingUser) {
	$duplicateField = ($existingUser['email'] === $email) ? 'email' : 'WhatsApp number';
	$_SESSION['signup_errors'] = ["This $duplicateField is already registered as a customer account."];
	$_SESSION['signup_data'] = $input;
	header('Location: ../Frontend/signup.php');
	exit;
}

// Check for duplicate email or WhatsApp in admin table
$stmt = $pdo->prepare('SELECT admin_id, email, whatsapp_number FROM admin WHERE email = ? OR whatsapp_number = ? LIMIT 1');
$stmt->execute([$email, $whatsapp]);
$existingAdmin = $stmt->fetch();

if ($existingAdmin) {
	$duplicateField = ($existingAdmin['email'] === $email) ? 'email' : 'WhatsApp number';
	$_SESSION['signup_errors'] = ["This $duplicateField is already registered as an admin account. Please use different credentials or contact support."];
	$_SESSION['signup_data'] = $input;
	header('Location: ../Frontend/signup.php');
	exit;
}

// Hash password securely
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $pdo->prepare('INSERT INTO users (full_name, school, email, whatsapp_number, password_hash, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
try {
	$stmt->execute([$full_name, $school, $email, $whatsapp, $password_hash, $now, $now]);
	$user_id = $pdo->lastInsertId();
	
	// Clear any previous signup data and set success message
	unset($_SESSION['signup_errors']);
	unset($_SESSION['signup_data']);
	$_SESSION['signup_success'] = 'Registration successful! You can now log in.';
	
	// Redirect to login page
	header('Location: ../Frontend/login.php');
	exit;
} catch (PDOException $e) {
	$_SESSION['signup_errors'] = ['Registration failed. Please try again.'];
	$_SESSION['signup_data'] = $input;
	header('Location: ../Frontend/signup.php');
	exit;
}

?>
