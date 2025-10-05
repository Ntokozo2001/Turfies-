<?php
require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';
require_once __DIR__ . '/email-config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email notification to admin about new contact request
 * 
 * @param int $request_id
 * @param string $guest_name
 * @param string $guest_email
 * @param string $whatsapp_contact
 * @param string $message
 * @param string $created_at
 * @return bool
 */
function sendAdminNotification($request_id, $guest_name, $guest_email, $whatsapp_contact, $message, $created_at) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);
        $mail->addReplyTo($guest_email, $guest_name);
        
        // Make it appear as if it's from the customer
        $mail->addCustomHeader('X-Original-From', $guest_email);
        $mail->addCustomHeader('X-Sender', $guest_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Contact Request from ' . $guest_name . ' - Request #' . $request_id;
        
        $mail->Body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: #6c3fa7; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ffbe19; }
                .message-box { background: white; padding: 15px; margin: 20px 0; border: 1px solid #ddd; border-radius: 5px; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
                h1 { margin: 0; }
                .label { font-weight: bold; color: #6c3fa7; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📧 New Contact Request</h1>
                <p>From: ' . htmlspecialchars($guest_name) . ' (' . htmlspecialchars($guest_email) . ')</p>
                <p>Request ID: #' . $request_id . '</p>
            </div>
            
            <div class="content">
                <div class="info-box">
                    <p><span class="label">Name:</span> ' . htmlspecialchars($guest_name) . '</p>
                    <p><span class="label">Email:</span> ' . htmlspecialchars($guest_email) . '</p>
                    <p><span class="label">WhatsApp:</span> ' . ($whatsapp_contact ? htmlspecialchars($whatsapp_contact) : 'Not provided') . '</p>
                    <p><span class="label">Submitted:</span> ' . $created_at . '</p>
                </div>
                
                <div class="message-box">
                    <p class="label">Message:</p>
                    <p>' . nl2br(htmlspecialchars($message)) . '</p>
                </div>
                
                <p><strong>Action Required:</strong> Please respond to this contact request promptly.</p>
                <p><strong>To Reply:</strong> Simply reply to this email and your response will go directly to ' . htmlspecialchars($guest_email) . '</p>
            </div>
            
            <div class="footer">
                <p>Turfies Exam Care - Contact Management System</p>
                <p>This is an automated notification</p>
            </div>
        </body>
        </html>';

        // Plain text version
        $mail->AltBody = "New Contact Request #$request_id\n\n" .
                        "Name: $guest_name\n" .
                        "Email: $guest_email\n" .
                        "WhatsApp: " . ($whatsapp_contact ?: 'Not provided') . "\n" .
                        "Submitted: $created_at\n\n" .
                        "Message:\n$message\n\n" .
                        "Please respond to this contact request promptly.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error
        error_log("Email failed to send. Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send confirmation email to the customer
 * 
 * @param string $customer_email
 * @param string $customer_name
 * @param int $request_id
 * @return bool
 */
function sendCustomerConfirmation($customer_email, $customer_name, $request_id) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($customer_email, $customer_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for contacting Turfies Exam Care - Request #' . $request_id;
        
        $mail->Body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: #6c3fa7; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ffbe19; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
                h1 { margin: 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>✅ Thank You for Contacting Us!</h1>
            </div>
            
            <div class="content">
                <p>Dear ' . htmlspecialchars($customer_name) . ',</p>
                
                <div class="info-box">
                    <p>Thank you for reaching out to <strong>Turfies Exam Care</strong>. We have received your message and will get back to you as soon as possible.</p>
                    
                    <p><strong>Your Request ID:</strong> #' . $request_id . '</p>
                    <p><strong>Expected Response Time:</strong> Within 24 hours</p>
                </div>
                
                <p>If you have any urgent questions, please feel free to contact us directly:</p>
                <ul>
                    <li><strong>Email:</strong> freshprincemaxi@gmail.com</li>
                    <li><strong>Phone:</strong> +27 82 532 2346</li>
                </ul>
                
                <p>Thank you for choosing Turfies Exam Care!</p>
                
                <p>Best regards,<br>
                The Turfies Team</p>
            </div>
            
            <div class="footer">
                <p>Turfies Exam Care - Your Success is Our Priority</p>
            </div>
        </body>
        </html>';

        // Plain text version
        $mail->AltBody = "Dear $customer_name,\n\n" .
                        "Thank you for reaching out to Turfies Exam Care. We have received your message and will get back to you as soon as possible.\n\n" .
                        "Your Request ID: #$request_id\n" .
                        "Expected Response Time: Within 24 hours\n\n" .
                        "If you have any urgent questions, please contact us:\n" .
                        "Email: freshprincemaxi@gmail.com\n" .
                        "Phone: +27 82 532 2346\n\n" .
                        "Thank you for choosing Turfies Exam Care!\n\n" .
                        "Best regards,\nThe Turfies Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Customer confirmation email failed. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>