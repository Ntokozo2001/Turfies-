<?php
session_start();

// Handle form submission messages
$errors = [];
$success_message = '';
$add_admin_data = [];

if (isset($_SESSION['add_admin_errors'])) {
    $errors = $_SESSION['add_admin_errors'];
    unset($_SESSION['add_admin_errors']);
}

if (isset($_SESSION['add_admin_success'])) {
    $success_message = $_SESSION['add_admin_success'];
    unset($_SESSION['add_admin_success']);
}

if (isset($_SESSION['add_admin_data'])) {
    $add_admin_data = $_SESSION['add_admin_data'];
    unset($_SESSION['add_admin_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin - Turfies Exam Care</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: #0a2a47;
        }
        .signup-container, .login-container {
            position: relative;
            z-index: 1;
        }
        .main-header, .main-footer {
            position: relative;
            z-index: 2;
            background: rgba(44, 19, 84, 0.98); /* fallback for nav/footer visibility */
        }
        .signup-container {
            background: #6c3fa7;
            color: #fff;
            max-width: 450px;
            margin: 40px auto;
            border-radius: 32px;
            padding: 32px 32px 18px 32px;
            box-shadow: 0 2px 16px #0002;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .signup-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: #ffbe19;
            margin-bottom: 18px;
            text-shadow: 1px 1px 2px #3d2176;
        }
        .signup-label {
            margin-top: 12px;
            margin-bottom: 4px;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .signup-input, .signup-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            margin-bottom: 2px;
            box-sizing: border-box;
        }
        .signup-select {
            background: #fff;
            color: #3d2176;
        }
        .signup-input[type="password"] {
            letter-spacing: 2px;
        }
        .password-hint {
            font-size: 0.85rem;
            color: #e6d6f3;
            margin-bottom: 6px;
            transition: color 0.3s ease;
            padding: 4px 8px;
            border-radius: 6px;
            background: rgba(255, 190, 25, 0.1);
            border: 1px solid rgba(255, 190, 25, 0.3);
        }
        .password-hint.strong {
            color: #51cf66;
            background: rgba(81, 207, 102, 0.1);
            border-color: rgba(81, 207, 102, 0.3);
        }
        .password-hint.weak {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.3);
        }

        .signup-btn {
            background: #ffbe19;
            color: #3d2176;
            border: none;
            border-radius: 18px;
            padding: 10px 0;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .signup-btn:hover {
            background: #fff;
            color: #6c3fa7;
        }
        .signup-footer {
            text-align: center;
            margin-top: 16px;
            color: #fff;
        }
        .signup-footer a {
            color: #ffbe19;
            text-decoration: underline;
        }
        .password-field {
            position: relative;
        }
        .password-field .signup-input {
            padding-right: 45px; /* Make room for eye icon */
        }
        .signup-eye {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 1.2rem;
            z-index: 10;
            user-select: none;
        }
        .signup-eye:hover {
            color: #3d2176;
        }
        .error-messages {
            background: #ff4757;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
        .success-message {
            background: #2ed573;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
            text-align: center;
        }
        .admin-info {
            background: rgba(255, 190, 25, 0.1);
            border: 2px solid #ffbe19;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .admin-info h4 {
            color: #ffbe19;
            margin: 0 0 8px 0;
            font-size: 1rem;
        }
        .field-hint {
            font-size: 0.8rem;
            color: #e6d6f3;
            margin-top: 2px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <header class="main-header">
        <?php include '../navbar.php'; ?>
    </header>
    
    <form class="signup-container" method="post" action="../../backend/admin/addanotheradmin.php" style="position:relative;z-index:1;">
        <div class="signup-title">Add New Admin</div>
        
        <div class="admin-info">
            <h4>🛡️ Admin Registration Guidelines</h4>
            <p>• Admins will have full access to manage users, products, and orders<br>
            • Employee ID must be unique and follow company format<br>
            • Use official company email addresses only<br>
            • Ensure contact details are accurate for system notifications</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                ✅ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <label class="signup-label" for="username">Admin Username:</label>
        <input class="signup-input" type="text" id="username" name="username" 
               value="<?php echo htmlspecialchars($add_admin_data['username'] ?? ''); ?>" 
               placeholder="e.g., john.admin" required>
        <div class="field-hint">Used for admin login and system identification</div>
        
        <label class="signup-label" for="employee_id">Employee ID:</label>
        <input class="signup-input" type="text" id="employee_id" name="employee_id" 
               value="<?php echo htmlspecialchars($add_admin_data['employee_id'] ?? ''); ?>" 
               placeholder="e.g., ADM001, EMP123" 
               style="text-transform: uppercase;" required>
        <div class="field-hint">Company Employee ID (3-20 characters, letters and numbers only)</div>
        
        <label class="signup-label" for="email">Official Email:</label>
        <input class="signup-input" type="email" id="email" name="email" 
               value="<?php echo htmlspecialchars($add_admin_data['email'] ?? ''); ?>" 
               placeholder="admin@company.com" required>
        <div class="field-hint">Official company email address for admin communications</div>
        
        <label class="signup-label" for="whatsapp">WhatsApp Number:</label>
        <input class="signup-input" type="tel" id="whatsapp" name="whatsapp" 
               value="<?php echo htmlspecialchars($add_admin_data['whatsapp'] ?? ''); ?>" 
               placeholder="e.g., 0712345678" required>
        <div class="field-hint">Direct contact number for urgent admin notifications</div>
        
        <label class="signup-label" for="password">Admin Password:</label>
        <div class="password-field">
            <input class="signup-input" type="password" id="password" name="password" minlength="8" required>
            <span class="signup-eye" onclick="togglePassword('password', this)">👁️</span>
        </div>
        <div class="password-hint">Minimum 8 characters with letters, numbers, and symbols for security</div>
        
        <label class="signup-label" for="confirm_password">Confirm Password:</label>
        <div class="password-field">
            <input class="signup-input" type="password" id="confirm_password" name="confirm_password" minlength="8" required>
            <span class="signup-eye" onclick="togglePassword('confirm_password', this)">👁️</span>
        </div>
        
        <button class="signup-btn" type="submit">🛡️ Create Admin Account</button>
        
        <div class="signup-footer">
            <a href="../admin-dashboard.php">← Back to Dashboard</a> | 
            <a href="manage-admins.php">View All Admins</a>
        </div>
    </form>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>
    
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 40, density: { enable: true, value_area: 800 } },
                color: { value: '#fff' },
                shape: { type: 'circle' },
                opacity: { value: 0.3, random: true },
                size: { value: 6, random: true },
                line_linked: { enable: true, distance: 150, color: '#fff', opacity: 0.2, width: 1 },
                move: { enable: true, speed: 2, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' }, resize: true },
                modes: { repulse: { distance: 100, duration: 0.4 }, push: { particles_nb: 4 } }
            },
            retina_detect: true
        });

        function togglePassword(fieldId, el) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
                el.style.color = '#6c3fa7';
                el.textContent = '🙈';
            } else {
                input.type = 'password';
                el.style.color = '#ffbe19';
                el.textContent = '👁️';
            }
        }

        // Auto-uppercase employee ID
        document.getElementById('employee_id').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Real-time password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const hint = document.querySelector('.password-hint');
            
            let feedback = [];
            if (password.length < 8) feedback.push('Need 8+ characters');
            if (!/[A-Za-z]/.test(password)) feedback.push('Need letters');
            if (!/[0-9]/.test(password)) feedback.push('Need numbers');
            if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) feedback.push('Symbols recommended');
            
            if (feedback.length > 0) {
                hint.textContent = 'Missing: ' + feedback.join(', ');
                hint.className = 'password-hint weak';
            } else {
                hint.textContent = '✅ Strong password!';
                hint.className = 'password-hint strong';
            }
        });

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#ff6b6b';
                this.style.boxShadow = '0 0 0 2px rgba(255, 107, 107, 0.2)';
            } else {
                this.style.borderColor = '';
                this.style.boxShadow = '';
            }
        });

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('.signup-btn');
            submitBtn.textContent = '🔄 Creating Admin Account...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>