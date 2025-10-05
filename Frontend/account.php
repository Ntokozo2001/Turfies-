<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Get user info from session
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? 'No email';
$user_id = $_SESSION['user_id'] ?? 'Unknown';

// Handle messages
$success_message = '';
if (isset($_SESSION['login_success'])) {
    $success_message = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f6f6; }
        .account-section { max-width: 1200px; margin: 32px auto; padding: 0 16px; }
        .account-title { font-size: 2rem; font-weight: bold; margin-bottom: 24px; color: #3d2176; }
        .account-grid { display: flex; flex-wrap: wrap; gap: 24px; }
        .account-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #0001; flex: 1 1 320px; min-width: 320px; padding: 24px 28px 18px 28px; position: relative; }
        .account-card h3 { font-size: 1.2rem; font-weight: bold; margin-bottom: 10px; color: #222; display: flex; align-items: center; gap: 8px; }
        .account-card ul { list-style: none; padding: 0; margin: 0; }
        .account-card ul li { margin-bottom: 8px; }
        .account-card ul li a { color: #1976d2; text-decoration: none; font-size: 1rem; }
        .account-card ul li a:hover { text-decoration: underline; }
        .account-icon { font-size: 1.5rem; color: #888; margin-left: 6px; }
        .user-welcome { 
            background: #6c3fa7; 
            color: white; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            text-align: center; 
        }
        .success-message {
            background: #27ae60;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .logout-btn {
            background: #ff4757;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            float: right;
            text-decoration: none;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #ff3838;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body class="accounts-bg">
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <main class="account-section">
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="user-welcome">
            <h2>👋 Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <p><strong>User ID:</strong> #<?php echo htmlspecialchars($user_id); ?></p>
            <a href="../backend/logout.php" class="logout-btn">🚪 Logout</a>
            <div style="clear: both;"></div>
        </div>

        <div class="account-title">My Account</div>
        <div class="account-grid">
            <div class="account-card">
                <h3>Orders <span class="account-icon">&#128722;</span></h3>
                <ul>
                    <li><a href="../frontend/accounts/orders.php">Orders</a></li>
                    <li><a href="../frontend/accounts/invoices.php">Invoices</a></li>
                    <li><a href="../frontend/accounts/returns.php">Returns</a></li>
                    <li><a href="../frontend/accounts/product-reviews.php">Product Reviews</a></li>
                </ul>
            </div>
            <div class="account-card">
                <h3>Payments &amp; Credit <span class="account-icon">&#128179;</span></h3>
                <ul>
                    <li><a href="../frontend/accounts/coupons.php">Coupons &amp; Offers</a></li>
                    <li><a href="../frontend/accounts/credit.php">Credit &amp; Refunds</a></li>
                    <li><a href="../frontend/accounts/redeem.php">Redeem Gift Voucher</a></li>
                </ul>
            </div>
            <div class="account-card">
                <h3>Profile <span class="account-icon">&#128100;</span></h3>
                <ul>
                    <li><a href="../frontend/accounts/personal-details.php">Personal Details</a></li>
                    <li><a href="../frontend/accounts/security.php">Security Settings</a></li>
                    <li><a href="../frontend/accounts/address-book.php">Address Book</a></li>
                    <li><a href="../frontend/accounts/newsletter.php">Newsletter Subscriptions</a></li>
                </ul>
            </div>
            <div class="account-card">
                <h3>Support <span class="account-icon">&#10067;</span></h3>
                <ul>
                    <li><a href="../frontend/accounts/help-center.php">Help Centre</a></li>
                </ul>
            </div>
        </div>
    </main>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
