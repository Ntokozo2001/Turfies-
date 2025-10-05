<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/notification-helper.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Frontend/contact.php');
    exit;
}

// Get POST data from form
$guest_name = trim($_POST['name'] ?? '');
$guest_email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$whatsapp_contact = trim($_POST['whatsapp'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields
$errors = [];
if (empty($guest_name)) {
    $errors[] = 'Name is required';
}
if (empty($guest_email)) {
    $errors[] = 'Email is required';
}
if (empty($subject)) {
    $errors[] = 'Subject is required';
}
if (empty($message)) {
    $errors[] = 'Message is required';
}

// Validate email format
if (!empty($guest_email) && !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// Validate WhatsApp number if provided (optional field)
if (!empty($whatsapp_contact) && !preg_match('/^[0-9]{7,15}$/', $whatsapp_contact)) {
    $errors[] = 'Invalid WhatsApp number format';
}

if ($errors) {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_data'] = [
        'name' => $guest_name,
        'email' => $guest_email,
        'subject' => $subject,
        'whatsapp' => $whatsapp_contact,
        'message' => $message
    ];
    header('Location: ../Frontend/contact.php');
    exit;
}

// Check if user is logged in to get user_id
$user_id = null;
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$now = date('Y-m-d H:i:s');

// Insert contact request into database
$stmt = $pdo->prepare('INSERT INTO help_request (user_id, guest_name, guest_email, subject, whatsapp_contact, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

try {
    $stmt->execute([
        $user_id,
        $guest_name,
        $guest_email,
        $subject,
        $whatsapp_contact ?: null, // Set to null if empty
        $message,
        'pending', // Default status
        $now
    ]);
    
    $request_id = $pdo->lastInsertId();
    
    // Send email notifications
    $email_sent = false;
    $customer_email_sent = false;
    
    // Send comprehensive notifications to admin
    $notification_results = [
        'email_sent' => false,
        'sms_sent' => false,
        'whatsapp_sent' => false,
        'dashboard_notification' => false
    ];
    
    try {
        // Send all admin notifications (email, SMS, WhatsApp)
        error_log("Attempting to send comprehensive admin notifications for request #" . $request_id);
        $notification_results = sendAdminNotifications($request_id, $guest_name, $guest_email, $whatsapp_contact, $message, $now);
        
        // Create dashboard notification for admin
        $notification_results['dashboard_notification'] = createAdminDashboardNotification($request_id, $guest_name, $guest_email, $message, $now);
        
        // Send confirmation to customer
        error_log("Attempting to send customer confirmation for request #" . $request_id);
        $customer_email_sent = sendCustomerConfirmation($guest_email, $guest_name, $request_id);
        error_log("Customer confirmation result: " . ($customer_email_sent ? 'SUCCESS' : 'FAILED'));
        
    } catch (Exception $e) {
        error_log('Notification failed: ' . $e->getMessage());
    }
    
    // Clear any previous contact data and set success message
    unset($_SESSION['contact_errors']);
    unset($_SESSION['contact_data']);
    
    $success_msg = 'Thank you for your message! We will get back to you soon. Request ID: #' . $request_id;
    
    // Add notification status to success message
    if ($notification_results['email_sent']) {
        $success_msg .= ' Admin has been notified via email.';
    }
    if ($customer_email_sent) {
        $success_msg .= ' A confirmation email has been sent to your email address.';
    }
    if ($notification_results['dashboard_notification']) {
        $success_msg .= ' Request logged in admin dashboard.';
    }
    
    $_SESSION['contact_success'] = $success_msg;
    
    // Redirect back to contact page with success message
    header('Location: ../Frontend/contact.php');
    exit;
    
} catch (PDOException $e) {
    // Log the error
    error_log('Contact form submission failed: ' . $e->getMessage());
    
    $_SESSION['contact_errors'] = ['Failed to submit your message. Please try again later.'];
    $_SESSION['contact_data'] = [
        'name' => $guest_name,
        'email' => $guest_email,
        'subject' => $subject,
        'whatsapp' => $whatsapp_contact,
        'message' => $message
    ];
    header('Location: ../Frontend/contact.php');
    exit;
}
?>
