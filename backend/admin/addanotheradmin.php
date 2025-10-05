<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Frontend/admin/add-admin.php');
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

// Helper: Validate employee ID format
function is_valid_employee_id($employee_id) {
    return preg_match('/^[A-Z0-9]{3,20}$/', $employee_id);
}

// Get POST data from form
$input = $_POST;

// Map form field names to expected names for admin table
$required = [
    'username' => 'username', 
    'email' => 'email', 
    'whatsapp_number' => 'whatsapp', 
    'password' => 'password',
    'employee_id' => 'employee_id'
];
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
    $errors[] = 'Invalid WhatsApp number (7-15 digits only)';
}
if (!empty($input['employee_id']) && !is_valid_employee_id($input['employee_id'])) {
    $errors[] = 'Invalid Employee ID format (3-20 characters, letters and numbers only)';
}

// Check password confirmation
if (!empty($input['password']) && !empty($input['confirm_password'])) {
    if ($input['password'] !== $input['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
}

// Validate password strength
if (!empty($input['password'])) {
    $password = $input['password'];
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    if (!preg_match('/[A-Za-z]/', $password)) {
        $errors[] = 'Password must contain at least one letter';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
}

if ($errors) {
    $_SESSION['add_admin_errors'] = $errors;
    $_SESSION['add_admin_data'] = $input; // Keep form data for user convenience
    header('Location: ../../Frontend/admin/add-admin.php');
    exit;
}

$username = trim($input['username']);
$email = strtolower(trim($input['email']));
$whatsapp = trim($input['whatsapp']);
$employee_id = strtoupper(trim($input['employee_id']));
$password = $input['password'];
$now = date('Y-m-d H:i:s');

try {
    // Check for duplicate email in admin table
    $stmt = $pdo->prepare('SELECT admin_id, email FROM admin WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $existingAdminEmail = $stmt->fetch();

    if ($existingAdminEmail) {
        $_SESSION['add_admin_errors'] = ['This email is already registered as an admin account.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Check for duplicate email in users table
    $stmt = $pdo->prepare('SELECT user_id, email FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $existingUserEmail = $stmt->fetch();

    if ($existingUserEmail) {
        $_SESSION['add_admin_errors'] = ['This email is already registered as a customer account. Please use a different email.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Check for duplicate WhatsApp in admin table
    $stmt = $pdo->prepare('SELECT admin_id, whatsapp_number FROM admin WHERE whatsapp_number = ? LIMIT 1');
    $stmt->execute([$whatsapp]);
    $existingAdminWhatsApp = $stmt->fetch();

    if ($existingAdminWhatsApp) {
        $_SESSION['add_admin_errors'] = ['This WhatsApp number is already registered as an admin account.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Check for duplicate WhatsApp in users table
    $stmt = $pdo->prepare('SELECT user_id, whatsapp_number FROM users WHERE whatsapp_number = ? LIMIT 1');
    $stmt->execute([$whatsapp]);
    $existingUserWhatsApp = $stmt->fetch();

    if ($existingUserWhatsApp) {
        $_SESSION['add_admin_errors'] = ['This WhatsApp number is already registered as a customer account. Please use a different number.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Check for duplicate username
    $stmt = $pdo->prepare('SELECT admin_id, username FROM admin WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $existingUsername = $stmt->fetch();

    if ($existingUsername) {
        $_SESSION['add_admin_errors'] = ['This username is already taken. Please choose a different username.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Check for duplicate employee ID
    $stmt = $pdo->prepare('SELECT admin_id, employee_id FROM admin WHERE employee_id = ? LIMIT 1');
    $stmt->execute([$employee_id]);
    $existingEmployeeId = $stmt->fetch();

    if ($existingEmployeeId) {
        $_SESSION['add_admin_errors'] = ['This Employee ID is already registered. Please use a different Employee ID.'];
        $_SESSION['add_admin_data'] = $input;
        header('Location: ../../Frontend/admin/add-admin.php');
        exit;
    }

    // Hash password securely
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin into database
    $stmt = $pdo->prepare('INSERT INTO admin (username, email, password_hash, employee_id, whatsapp_number, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$username, $email, $password_hash, $employee_id, $whatsapp, $now, $now]);
    
    $admin_id = $pdo->lastInsertId();
    
    // Clear any previous form data and set success message
    unset($_SESSION['add_admin_errors']);
    unset($_SESSION['add_admin_data']);
    $_SESSION['add_admin_success'] = "Admin '$username' has been successfully registered with Employee ID: $employee_id";
    
    // Redirect back to add admin page with success message
    header('Location: ../../Frontend/admin/add-admin.php');
    exit;

} catch (PDOException $e) {
    error_log('Error adding admin: ' . $e->getMessage());
    $_SESSION['add_admin_errors'] = ['Registration failed. Please try again. If the problem persists, contact system administrator.'];
    $_SESSION['add_admin_data'] = $input;
    header('Location: ../../Frontend/admin/add-admin.php');
    exit;
}
?>