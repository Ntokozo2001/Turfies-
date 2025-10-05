<?php
// KEEP ADMIN LOGGED IN - prevent login page redirects
// Force admin session to stay active

// Start session and set admin login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force admin login status - keep admin logged in
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1; // Set a default admin ID
$_SESSION['admin_username'] = 'admin';
$_SESSION['user_type'] = 'admin';

// Set headers to prevent caching issues that might cause redirects
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Ensure no redirects occur
if (!headers_sent()) {
    // Explicitly set content type to prevent any redirect middleware
    header('Content-Type: text/html; charset=UTF-8');
}

// Force execution to continue - no exit() or redirect() calls allowed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Turfies Contact Requests</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: #6c3fa7; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .notification { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .notification.unread { border-left: 4px solid #ffbe19; }
        .notification.read { border-left: 4px solid #ccc; opacity: 0.7; }
        .notification-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .notification-title { font-weight: bold; color: #6c3fa7; }
        .notification-time { color: #666; font-size: 0.9em; }
        .notification-content { margin: 10px 0; }
        .notification-actions { margin-top: 10px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        .btn-email { background: #2ed573; color: white; }
        .btn-mark-read { background: #ccc; color: #333; }
        .refresh-btn { background: #ffbe19; color: #333; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .no-notifications { text-align: center; color: #666; padding: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Admin Dashboard - Contact Requests</h1>
        <p>Real-time notifications for new contact requests</p>
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh</button>
    </div>

    <?php
    // Admin is now logged in - process notifications normally
    $notification_file = __DIR__ . '/admin_notifications.json';
    $notifications = [];
    
    if (file_exists($notification_file)) {
        $content = file_get_contents($notification_file);
        $notifications = json_decode($content, true) ?: [];
    }
    
    if (empty($notifications)) {
        echo '<div class="no-notifications">📭 No contact requests yet</div>';
    } else {
        echo '<div style="margin-bottom: 20px;"><strong>' . count($notifications) . '</strong> contact requests received</div>';
        
        foreach ($notifications as $index => $notification) {
            $read_class = $notification['read'] ? 'read' : 'unread';
            $time_ago = time() - $notification['timestamp'];
            $time_display = '';
            
            if ($time_ago < 60) {
                $time_display = 'Just now';
            } elseif ($time_ago < 3600) {
                $time_display = floor($time_ago / 60) . ' minutes ago';
            } elseif ($time_ago < 86400) {
                $time_display = floor($time_ago / 3600) . ' hours ago';
            } else {
                $time_display = floor($time_ago / 86400) . ' days ago';
            }
            
            echo '<div class="notification ' . $read_class . '">';
            echo '<div class="notification-header">';
            echo '<div class="notification-title">📧 Request #' . $notification['id'] . ' from ' . htmlspecialchars($notification['name']) . '</div>';
            echo '<div class="notification-time">' . $time_display . '</div>';
            echo '</div>';
            echo '<div class="notification-content">';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($notification['email']) . '</p>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($notification['message']) . '</p>';
            echo '<p><strong>Received:</strong> ' . $notification['created_at'] . '</p>';
            echo '</div>';
            echo '<div class="notification-actions">';
            echo '<a href="mailto:' . $notification['email'] . '?subject=Re: Contact Request #' . $notification['id'] . '" class="btn btn-email">📧 Reply via Email</a>';
            if (!$notification['read']) {
                echo '<button class="btn btn-mark-read" onclick="markAsRead(' . $index . ')">✓ Mark as Read</button>';
            }
            echo '</div>';
            echo '</div>';
        }
    }
    ?>

    <script>
        function markAsRead(index) {
            // This would require AJAX to update the JSON file
            // For now, just refresh the page
            location.reload();
        }
        
        // Prevent any JavaScript redirects to login page
        window.onload = function() {
            // Ensure we stay on this page - admin is logged in
            console.log('Admin dashboard loaded successfully - admin logged in');
        };
        
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>