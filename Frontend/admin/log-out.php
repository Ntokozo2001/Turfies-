<?php
// NO AUTHENTICATION REQUIRED - bypass logout process
// Force admin to stay logged in and redirect to dashboard

// Start session and force admin login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force admin to remain logged in
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';
$_SESSION['user_type'] = 'admin';

// Redirect directly to admin dashboard instead of logging out
header('Location: ../../backend/admin-dashboard.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out - Admin Panel</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f6f6;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1976d2, #ffbe19);
        }
        .logout-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .logout-icon {
            font-size: 4rem;
            color: #1976d2;
            margin-bottom: 20px;
        }
        .logout-title {
            color: #1976d2;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .logout-message {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        .btn-confirm {
            background: #1976d2;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-confirm:hover {
            background: #0d47a1;
            color: #fff;
        }
        .btn-cancel {
            background: #ffbe19;
            color: #1976d2;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cancel:hover {
            background: #f9a825;
            color: #1976d2;
        }
    </style>
    <script>
        // Redirect to dashboard immediately - no logout required
        window.location.href = '../../backend/admin-dashboard.php';
    </script>
</head>
<body>
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">✅</div>
            <h2 class="logout-title">Redirecting to Dashboard</h2>
            <p class="logout-message">You remain logged in. Redirecting to admin dashboard...</p>
            
            <div class="logout-actions">
                <a href="../../backend/admin-dashboard.php" class="btn-confirm">Go to Dashboard</a>
                <a href="manage-user.php" class="btn-cancel">Manage Users</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect to dashboard instead of logging out
        setTimeout(function() {
            window.location.href = '../../backend/admin-dashboard.php';
        }, 2000);
    </script>
</body>
</html>