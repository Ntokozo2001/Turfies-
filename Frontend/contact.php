<?php
session_start();

// Handle messages
$errors = [];
$contact_data = [];
$success_message = '';

if (isset($_SESSION['contact_errors'])) {
    $errors = $_SESSION['contact_errors'];
    unset($_SESSION['contact_errors']);
}

if (isset($_SESSION['contact_data'])) {
    $contact_data = $_SESSION['contact_data'];
    unset($_SESSION['contact_data']);
}

if (isset($_SESSION['contact_success'])) {
    $success_message = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Contact Us - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .error-messages {
            background: #ff4757;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
        .success-message {
            background: #2ed573;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
        .form-field {
            margin-bottom: 18px;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        .form-input, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            border: 2px solid #a97be0;
            font-size: 1rem;
            transition: border-color 0.3s;
            box-sizing: border-box;
            resize: vertical;
        }
        .form-input::placeholder, .form-textarea::placeholder {
            font-size: 0.9rem;
            color: #888;
            opacity: 0.8;
        }
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #6c3fa7;
            box-shadow: 0 0 0 3px rgba(169, 123, 224, 0.1);
        }
        .form-submit {
            background: #a97be0;
            color: #fff;
            padding: 14px 0;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 15px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        .form-submit:hover {
            background: #6c3fa7;
        }
        .optional-label {
            color: #888;
            font-size: 0.9rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .container {
                max-width: 95% !important;
                margin: 20px auto 0 auto !important;
                padding: 20px 16px !important;
            }
            h1 {
                font-size: 1.8rem !important;
                margin-bottom: 20px !important;
            }
            .form-input, .form-textarea {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
            .form-input::placeholder, .form-textarea::placeholder {
                font-size: 0.8rem;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .form-submit {
                padding: 12px 0;
                font-size: 1rem;
            }
            .error-messages, .success-message {
                font-size: 0.8rem;
                padding: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                max-width: 98% !important;
                margin: 10px auto 0 auto !important;
                padding: 16px 12px !important;
            }
            h1 {
                font-size: 1.5rem !important;
            }
            .form-input, .form-textarea {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
            .form-input::placeholder, .form-textarea::placeholder {
                font-size: 0.75rem;
            }
            .form-field {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <main class="container" style="max-width:600px;margin:40px auto 0 auto;padding:32px 18px;background:#faf8fd;border-radius:16px;box-shadow:0 2px 16px rgba(169,123,224,0.08);">
        <h1 style="color:#a97be0;text-align:center;margin-bottom:24px;">Contact Us</h1>
        
        <!-- Show success message if present -->
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Show error messages if present -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="post" action="../backend/contact.php">
            <div class="form-field">
                <label for="name" class="form-label">Full Name:</label>
                <input type="text" id="name" name="name" class="form-input" 
                       value="<?php echo htmlspecialchars($contact_data['name'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-field">
                <label for="email" class="form-label">Email Address:</label>
                <input type="email" id="email" name="email" class="form-input" 
                       value="<?php echo htmlspecialchars($contact_data['email'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-field">
                <label for="subject" class="form-label">Subject:</label>
                <input type="text" id="subject" name="subject" class="form-input" 
                       value="<?php echo htmlspecialchars($contact_data['subject'] ?? ''); ?>" 
                       required placeholder="Brief topic of your inquiry">
            </div>
            
            <div class="form-field">
                <label for="whatsapp" class="form-label">
                    WhatsApp Number <span class="optional-label">(Optional)</span>:
                </label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-input" 
                       value="<?php echo htmlspecialchars($contact_data['whatsapp'] ?? ''); ?>" 
                       placeholder="27825322346">
                <small style="color: #666; font-size: 0.8rem;">Numbers only (country code + number)</small>
            </div>
            
            <div class="form-field">
                <label for="message" class="form-label">Your Message:</label>
                <textarea id="message" name="message" rows="6" class="form-textarea" 
                          required placeholder="How can we help you?"><?php echo htmlspecialchars($contact_data['message'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="form-submit">📧 Send Message</button>
        </form>
        
        <div style="margin-top:32px;padding-top:24px;border-top:2px solid #e0d0f0;text-align:center;color:#6c3fa7;">
            <h3 style="color:#a97be0;margin-bottom:16px;font-size:1.2rem;">Other Ways to Reach Us</h3>
            <p style="margin:8px 0;font-size:0.95rem;"><strong>📧 Email:</strong> jazackaabdulmajeed1@gmail.com</p>
            <p style="margin:8px 0;font-size:0.95rem;"><strong>📱 WhatsApp:</strong> +27 82 532 2346</p>
            <p style="margin:8px 0;font-size:0.95rem;"><strong>⏰ Response Time:</strong> Within 24 hours</p>
        </div>
    </main>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
