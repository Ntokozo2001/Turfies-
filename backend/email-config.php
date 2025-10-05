<?php
// Email Configuration
// Update these settings according to your email provider

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');  // Gmail SMTP server
define('SMTP_PORT', 587);               // Gmail SMTP port for TLS
define('SMTP_SECURE', 'tls');           // TLS encryption
define('SMTP_USERNAME', 'jamdtmos1@gmail.com');  // Your Gmail address
define('SMTP_PASSWORD', 'ibxi kqct dnbw ipsr');     // Replace with your actual App Password

// Admin Email Settings
define('ADMIN_EMAIL', 'jazackaabdulmajeed1@gmail.com');
define('ADMIN_NAME', 'Turfies Admin');

// Admin Phone Settings (for SMS/WhatsApp notifications)
define('ADMIN_PHONE', '+27835145405'); // Admin's phone number
define('ADMIN_WHATSAPP', '+27835145405'); // Admin's WhatsApp number (can be same as phone)

// Email Settings
define('FROM_EMAIL', 'jamdtmos1@gmail.com');  // Should match SMTP_USERNAME
define('FROM_NAME', 'Turfies Exam Care');

/*
IMPORTANT SETUP INSTRUCTIONS:

For Gmail:
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security > 2-Step Verification > App passwords
   - Generate password for "Mail"
   - Use this App Password in SMTP_PASSWORD above

For other email providers:
- Update SMTP_HOST, SMTP_PORT, and SMTP_SECURE accordingly
- Use your provider's SMTP settings

Common SMTP Settings:
- Gmail: smtp.gmail.com, port 587, TLS
- Outlook: smtp-mail.outlook.com, port 587, TLS
- Yahoo: smtp.mail.yahoo.com, port 587, TLS
*/
?>