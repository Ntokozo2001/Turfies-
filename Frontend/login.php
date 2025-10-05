<?php
session_start();

// Handle messages
$errors = [];
$login_data = [];
$success_message = '';

if (isset($_SESSION['login_errors'])) {
    $errors = $_SESSION['login_errors'];
    unset($_SESSION['login_errors']);
}

if (isset($_SESSION['login_data'])) {
    $login_data = $_SESSION['login_data'];
    unset($_SESSION['login_data']);
}

if (isset($_SESSION['signup_success'])) {
    $success_message = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

if (isset($_SESSION['login_success'])) {
    $success_message = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['logout_message'])) {
    $success_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <style>
        body {
            background: #ffbe19;
        }
        .login-container {
            background: #6c3fa7;
            color: #fff;
            max-width: 400px;
            margin: 40px auto;
            border-radius: 32px;
            padding: 32px 32px 18px 32px;
            box-shadow: 0 2px 16px #0002;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .login-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: #ffbe19;
            margin-bottom: 18px;
            text-shadow: 1px 1px 2px #3d2176;
        }
        .login-label {
            margin-top: 12px;
            margin-bottom: 4px;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .login-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            margin-bottom: 2px;
        }
        .login-btn {
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
        .login-btn:hover {
            background: #fff;
            color: #6c3fa7;
        }
        .login-footer {
            text-align: center;
            margin-top: 16px;
            color: #fff;
        }
        .login-footer a {
            color: #ffbe19;
            text-decoration: underline;
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
        .main-header {
            position: relative;
            z-index: 2;
        }
        .signup-container, .login-container {
            position: relative;
            z-index: 1;
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
        .main-footer {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <form class="login-container" method="post" action="../backend/userLogin.php" style="position:relative;z-index:1;">
        <div class="login-title">User Login</div>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <label class="login-label" for="email">Email:</label>
        <input class="login-input" type="email" id="email" name="email" value="<?php echo htmlspecialchars($login_data['email'] ?? ''); ?>" required>
        
        <label class="login-label" for="password">Password:</label>
        <input class="login-input" type="password" id="password" name="password" required>
        
        <button class="login-btn" type="submit">Login</button>
        <div class="login-footer">
            Don't have an account? <a href="signup.php">Sign Up</a>
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
    </script>
</body>
</html>
