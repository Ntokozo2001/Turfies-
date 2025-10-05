<?php
require_once __DIR__ . '/email-helper.php';
require_once __DIR__ . '/email-config.php';

/**
 * Send comprehensive notification to admin via multiple channels
 * 
 * @param int $request_id
 * @param string $guest_name
 * @param string $guest_email
 * @param string $whatsapp_contact
 * @param string $message
 * @param string $created_at
 * @return array Results of all notification attempts
 */
function sendAdminNotifications($request_id, $guest_name, $guest_email, $whatsapp_contact, $message, $created_at) {
    $results = [
        'email_sent' => false,
        'sms_sent' => false,
        'whatsapp_sent' => false,
        'errors' => []
    ];
    
    // 1. Send Email Notification
    try {
        $results['email_sent'] = sendAdminNotification($request_id, $guest_name, $guest_email, $whatsapp_contact, $message, $created_at);
        if ($results['email_sent']) {
            error_log("Admin email notification sent successfully for request #" . $request_id);
        } else {
            $results['errors'][] = "Email notification failed";
            error_log("Admin email notification failed for request #" . $request_id);
        }
    } catch (Exception $e) {
        $results['errors'][] = "Email error: " . $e->getMessage();
        error_log("Admin email notification error: " . $e->getMessage());
    }
    
    // 2. Send SMS Notification (placeholder for SMS service integration)
    try {
        $sms_message = "🆕 New Contact Request #$request_id\n";
        $sms_message .= "From: $guest_name\n";
        $sms_message .= "Email: $guest_email\n";
        $sms_message .= "Message: " . substr($message, 0, 100) . (strlen($message) > 100 ? "..." : "") . "\n";
        $sms_message .= "Reply to: $guest_email";
        
        // TODO: Integrate with SMS service (Twilio, Clickatell, etc.)
        // $results['sms_sent'] = sendSMS(ADMIN_PHONE, $sms_message);
        
        // For now, log the SMS that would be sent
        error_log("SMS would be sent to " . ADMIN_PHONE . ": " . $sms_message);
        $results['sms_sent'] = false; // Set to false until SMS service is integrated
        
    } catch (Exception $e) {
        $results['errors'][] = "SMS error: " . $e->getMessage();
        error_log("SMS notification error: " . $e->getMessage());
    }
    
    // 3. Send WhatsApp Notification (placeholder for WhatsApp Business API)
    try {
        $whatsapp_message = "🔔 *New Contact Request*\n\n";
        $whatsapp_message .= "*Request ID:* #$request_id\n";
        $whatsapp_message .= "*From:* $guest_name\n";
        $whatsapp_message .= "*Email:* $guest_email\n";
        if ($whatsapp_contact) {
            $whatsapp_message .= "*WhatsApp:* $whatsapp_contact\n";
        }
        $whatsapp_message .= "*Time:* $created_at\n\n";
        $whatsapp_message .= "*Message:*\n$message\n\n";
        $whatsapp_message .= "💡 *Reply directly to:* $guest_email";
        
        // TODO: Integrate with WhatsApp Business API
        // $results['whatsapp_sent'] = sendWhatsApp(ADMIN_WHATSAPP, $whatsapp_message);
        
        // For now, log the WhatsApp message that would be sent
        error_log("WhatsApp would be sent to " . ADMIN_WHATSAPP . ": " . $whatsapp_message);
        $results['whatsapp_sent'] = false; // Set to false until WhatsApp API is integrated
        
    } catch (Exception $e) {
        $results['errors'][] = "WhatsApp error: " . $e->getMessage();
        error_log("WhatsApp notification error: " . $e->getMessage());
    }
    
    return $results;
}

/**
 * Simple email-to-SMS bridge (for carriers that support email-to-SMS)
 * This is a basic implementation - you might want to use a proper SMS service
 */
function sendEmailToSMS($phone_number, $message, $carrier = 'vodacom') {
    $carriers = [
        'vodacom' => '@vodamail.co.za',
        'mtn' => '@sms.mtn.co.za',
        'cellc' => '@sms.cellc.net',
        'telkom' => '@sms.telkommobile.co.za'
    ];
    
    if (!isset($carriers[$carrier])) {
        return false;
    }
    
    // Clean phone number (remove country code, spaces, etc.)
    $clean_phone = preg_replace('/[^0-9]/', '', $phone_number);
    if (substr($clean_phone, 0, 2) == '27') {
        $clean_phone = '0' . substr($clean_phone, 2);
    }
    
    $sms_email = $clean_phone . $carriers[$carrier];
    
    // Use simple mail function to send SMS
    $headers = "From: " . FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($sms_email, '', $message, $headers);
}

/**
 * Create a simple browser notification for admin (for local testing)
 */
function createAdminDashboardNotification($request_id, $guest_name, $guest_email, $message, $created_at) {
    $notification_file = __DIR__ . '/admin_notifications.json';
    
    $notification = [
        'id' => $request_id,
        'name' => $guest_name,
        'email' => $guest_email,
        'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
        'created_at' => $created_at,
        'timestamp' => time(),
        'read' => false
    ];
    
    // Read existing notifications
    $notifications = [];
    if (file_exists($notification_file)) {
        $content = file_get_contents($notification_file);
        $notifications = json_decode($content, true) ?: [];
    }
    
    // Add new notification to the beginning
    array_unshift($notifications, $notification);
    
    // Keep only last 50 notifications
    $notifications = array_slice($notifications, 0, 50);
    
    // Save back to file
    file_put_contents($notification_file, json_encode($notifications, JSON_PRETTY_PRINT));
    
    return true;
}
?>