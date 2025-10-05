# Email Setup Guide for Turfies Contact System

## 📧 PHPMailer Installation
✅ **COMPLETED** - PHPMailer has been installed and configured

## 🔧 Email Configuration Setup

### Step 1: Configure Email Settings
Edit the file: `backend/email-config.php`

Update these settings with your actual email credentials:

```php
// SMTP Configuration
define('SMTP_USERNAME', 'your-actual-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password-here');

// Admin Email (where notifications will be sent)
define('ADMIN_EMAIL', 'freshprincemaxi@gmail.com');

// From Email (should match SMTP_USERNAME)
define('FROM_EMAIL', 'your-actual-email@gmail.com');
```

### Step 2: Gmail Setup (Recommended)

1. **Enable 2-Factor Authentication**:
   - Go to [Google Account Settings](https://myaccount.google.com/)
   - Security → 2-Step Verification → Turn On

2. **Generate App Password**:
   - Go to Security → 2-Step Verification → App passwords
   - Select "Mail" as the app
   - Copy the 16-character password
   - Use this password in `SMTP_PASSWORD`

### Step 3: Alternative Email Providers

#### Outlook/Hotmail:
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

#### Yahoo:
```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

## 🎯 Features Implemented

### For Admin:
- **Instant Notifications**: Get email alerts for new contact requests
- **Rich HTML Format**: Professional email with all contact details
- **Request Tracking**: Each request has a unique ID
- **Reply-To Setup**: Can reply directly to the customer

### For Customers:
- **Confirmation Emails**: Automatic confirmation with request ID
- **Professional Branding**: Branded emails with Turfies styling
- **Contact Information**: Admin contact details included

## 🚀 Testing the System

1. **Update email settings** in `backend/email-config.php`
2. **Submit a test contact form**
3. **Check that emails are received**:
   - Admin should receive notification email
   - Customer should receive confirmation email

## 🔍 Troubleshooting

### Common Issues:
1. **"Authentication failed"** → Check App Password
2. **"Connection refused"** → Check SMTP settings
3. **Emails not sending** → Check error logs in `/xampp/apache/logs/`

### Debug Mode:
To enable debug mode, add this to email-helper.php:
```php
$mail->SMTPDebug = 2; // Enable verbose debug output
```

## 📝 Email Templates

The system includes:
- **Admin Notification**: Professional HTML email with contact details
- **Customer Confirmation**: Branded confirmation with request tracking
- **Plain Text Fallback**: For email clients that don't support HTML

## 🔒 Security Features

- **Input Sanitization**: All data is properly escaped
- **Reply-To Protection**: Customer email set as reply-to
- **Error Logging**: Failed emails are logged for monitoring
- **App Passwords**: Secure authentication method

Your contact system is now ready with full email functionality! 🎉