<?php
session_start();

// Handle form submission messages
$errors = [];
$signup_data = [];

if (isset($_SESSION['signup_errors'])) {
    $errors = $_SESSION['signup_errors'];
    unset($_SESSION['signup_errors']);
}

if (isset($_SESSION['signup_data'])) {
    $signup_data = $_SESSION['signup_data'];
    unset($_SESSION['signup_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sign Up - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
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
            max-width: 480px;
            width: 90%;
            margin: 30px auto;
            border-radius: 32px;
            padding: 40px 40px 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            min-height: auto;
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
            margin-top: 16px;
            margin-bottom: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
        }
        .signup-label:first-of-type {
            margin-top: 8px;
        }
        .signup-input, .signup-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            margin-bottom: 8px;
            box-sizing: border-box;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }
        .signup-input:focus, .signup-select:focus {
            box-shadow: 0 0 0 3px rgba(255, 190, 25, 0.3);
            transform: translateY(-1px);
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
            margin-bottom: 12px;
            transition: color 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(255, 190, 25, 0.1);
            border: 1px solid rgba(255, 190, 25, 0.3);
            width: 100%;
            box-sizing: border-box;
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
        
        /* Responsive adjustments */
        @media (max-width: 600px) {
            .signup-container {
                max-width: 95%;
                padding: 30px 25px 25px 25px;
                margin: 20px auto;
            }
            .signup-title {
                font-size: 1.7rem;
            }
            .signup-input, .signup-select {
                padding: 14px 16px;
                font-size: 1rem;
            }
            .password-field .signup-input {
                padding-right: 50px;
            }
        }
        
        @media (max-width: 400px) {
            .signup-container {
                padding: 25px 20px 20px 20px;
            }
            .signup-input, .signup-select {
                padding: 12px 14px;
            }
        }

        .signup-btn {
            background: #ffbe19;
            color: #3d2176;
            border: none;
            border-radius: 18px;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }
        .signup-btn:hover {
            background: #fff;
            color: #6c3fa7;
        }
        .signup-footer {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 8px;
            color: #fff;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .signup-footer a {
            color: #ffbe19;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .signup-footer a:hover {
            color: #fff;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(255, 190, 25, 0.6);
        }
        .password-field {
            position: relative;
        }
        .password-field .signup-input {
            padding-right: 50px; /* Make room for eye icon */
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
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <form class="signup-container" method="post" action="../backend/userRegister.php" style="position:relative;z-index:1;">
        <div class="signup-title">User Sign Up</div>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <label class="signup-label" for="fullname">Full Name:</label>
        <input class="signup-input" type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($signup_data['fullname'] ?? ''); ?>" required>
        
        <label class="signup-label" for="school">School:</label>
        <select class="signup-select" id="school" name="school" required>
            <option value="">Select School</option>
            <option value="all" <?php echo (($signup_data['school'] ?? '') === 'all') ? 'selected' : ''; ?>>All</option>
            <option value="uni limpopo" <?php echo (($signup_data['school'] ?? '') === 'uni limpopo') ? 'selected' : ''; ?>>University of Limpopo</option>
            <option value="resebank" <?php echo (($signup_data['school'] ?? '') === 'resebank') ? 'selected' : ''; ?>>Resebank</option>
            <option value="other" <?php echo (($signup_data['school'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
        </select>
        
        <label class="signup-label" for="email">Email:</label>
        <input class="signup-input" type="email" id="email" name="email" value="<?php echo htmlspecialchars($signup_data['email'] ?? ''); ?>" required>
        
        <label class="signup-label" for="whatsapp">WhatsApp Number:</label>
        <input class="signup-input" type="tel" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($signup_data['whatsapp'] ?? ''); ?>" placeholder="e.g., 0712345678" required>
        
        <label class="signup-label" for="password">Password:</label>
        <div class="password-field">
            <input class="signup-input" type="password" id="password" name="password" minlength="8" required>
            <span class="signup-eye" onclick="togglePassword('password', this)">👁️</span>
        </div>
        <div class="password-hint">Minimum 8 characters include numbers letters and symbols</div>
        
        <label class="signup-label" for="confirm_password">Confirm Password:</label>
        <div class="password-field">
            <input class="signup-input" type="password" id="confirm_password" name="confirm_password" minlength="8" required>
            <span class="signup-eye" onclick="togglePassword('confirm_password', this)">👁️</span>
        </div>
        
        <button class="signup-btn" type="submit">Sign Up</button>
        <div class="signup-footer">
            <div style="margin-bottom: 8px;">
                
            </div>
            <div style="font-size: 0.9rem; color: #e6d6f3;">
                <a href="index1.php">← Back to Market</a> |
                Have an Account? <a href="login.php" style="color: #ffbe19;">Log in here.</a>
            </div>
        </div>
    </form>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
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
                el.style.color = '#666';
                el.textContent = '👁️';
            }
        }

        // Real-time password validation for signup
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
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
            }

            // Password confirmation validation
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    const password = passwordInput.value;
                    const confirmPassword = this.value;
                    
                    if (confirmPassword && password !== confirmPassword) {
                        this.style.borderColor = '#ff6b6b';
                        this.style.boxShadow = '0 0 0 2px rgba(255, 107, 107, 0.2)';
                    } else {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                    }
                });
            }

            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match. Please check and try again.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
