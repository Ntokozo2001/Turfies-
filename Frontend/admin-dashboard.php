<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true || !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Include the backend dashboard functionality
require_once __DIR__ . '/../backend/admin/dashboard.php';

// Get dashboard data from database
$dashboardData = getDashboardStats();
$lowStockAlerts = getLowStockAlerts();
$outOfStockProducts = getOutOfStockProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Turfies Exam Care</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f6f6;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: #1976d2;
            color: #fff;
            width: 220px;
            padding: 24px 0;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .admin-sidebar h2 {
            color: #ffbe19;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 0 24px 24px 24px;
            margin: 0;
            border-bottom: 2px solid #ffbe19;
        }
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .admin-sidebar li {
            margin: 8px 0;
        }
        .admin-sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px 24px;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background: #ffbe19;
            color: #1976d2;
        }
        .admin-main {
            flex: 1;
            background: #ffbe19;
            padding: 32px;
        }
        .dashboard-header {
            background: #1976d2;
            color: #ffbe19;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #1976d2;
            font-size: 1.8rem;
            margin: 0 0 8px 0;
            font-weight: bold;
        }
        .stat-card p {
            color: #666;
            margin: 0;
            font-size: 1rem;
        }
        .recent-activities {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .recent-activities h2 {
            color: #1976d2;
            margin: 0 0 20px 0;
            font-size: 1.4rem;
            font-weight: bold;
        }
        .activity-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            color: #666;
            font-size: 0.95rem;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    
    <div class="admin-container">
        <aside class="admin-sidebar">
            <h2>Admin Options</h2>
            <ul>
                <li><a href="/Turfies Code/Frontend/admin-dashboard.php" class="active">Dashboard</a></li>
                <li><a href="/Turfies Code/Frontend/admin/manage-user.php">Manage User</a></li>
                <li><a href="/Turfies Code/Frontend/admin/manage-products.php">Manage Products</a></li>
                <li><a href="/Turfies Code/Frontend/admin/manage-request.php">Manage Request</a></li>
                <li><a href="/Turfies Code/Frontend/admin/log-out.php">Log Out</a></li>
                <li><a href="/Turfies Code/Frontend/admin/settings.php">Settings</a></li>
                <li><a href="/Turfies Code/Frontend/index.php">Go To Market</a></li>
            </ul>
        </aside>
        
        <main class="admin-main">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3 id="total-users"><?php echo number_format($dashboardData['total_users']); ?></h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-card">
                    <h3 id="available-units"><?php echo number_format($dashboardData['available_units']); ?></h3>
                    <p>Units Remaining</p>
                    <?php if ($dashboardData['available_units'] <= 50): ?>
                        <small style="color:#e74c3c;">⚠️ Low stock alert</small>
                    <?php endif; ?>
                </div>
                <div class="stat-card">
                    <h3 id="pending-orders"><?php echo number_format($dashboardData['pending_orders']); ?></h3>
                    <p>Pending Orders</p>
                    <?php if ($dashboardData['pending_orders'] > 0): ?>
                        <small style="color:#f39c12;">📦 Needs attention</small>
                    <?php endif; ?>
                </div>
                <div class="stat-card">
                    <h3 id="total-revenue"><?php echo formatCurrency($dashboardData['total_revenue']); ?></h3>
                    <p>Revenue Generated</p>
                </div>
                <div class="stat-card">
                    <h3><a href="admin/add-admin.php" style="color:#1976d2;text-decoration:none;">Add Another Admin</a></h3>
                    <p>Manage Admin Users</p>
                </div>
                <div class="stat-card">
                    <h3><a href="hot-sales.php" style="color:#1976d2;text-decoration:none;">Hot Sales</a></h3>
                    <p>View Analytics</p>
                    <small style="color:#27ae60;"><?php echo $dashboardData['total_products']; ?> Active Products</small>
                </div>
            </div>
            
            <div class="recent-activities">
                <h2>Recent Activities</h2>
                <div id="activities-list">
                    <?php if (!empty($dashboardData['recent_activities'])): ?>
                        <?php foreach ($dashboardData['recent_activities'] as $activity): ?>
                            <div class="activity-item">
                                <span class="activity-icon"><?php echo $activity['icon']; ?></span>
                                <span class="activity-time"><?php echo formatActivityDate($activity['timestamp']); ?></span>
                                - <?php echo htmlspecialchars($activity['description']); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">No recent activities found.</div>
                    <?php endif; ?>
                </div>
                <div style="margin-top: 15px;">
                    <button id="refresh-activities" class="btn btn-sm" style="background:#1976d2;color:white;border:none;padding:8px 16px;border-radius:6px;">
                        🔄 Refresh Activities
                    </button>
                </div>
            </div>

            <!-- Stock Alerts Section -->
            <?php if (!empty($lowStockAlerts) || !empty($outOfStockProducts)): ?>
            <div class="stock-alerts" style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-top:20px;">
                <h2 style="color:#1976d2;margin:0 0 20px 0;font-size:1.4rem;font-weight:bold;">📊 Stock Alerts</h2>
                
                <?php if (!empty($lowStockAlerts)): ?>
                <div class="alert-section" style="margin-bottom:15px;">
                    <h4 style="color:#f39c12;margin:0 0 10px 0;">⚠️ Low Stock Items (≤10 units)</h4>
                    <?php foreach ($lowStockAlerts as $product): ?>
                        <div class="alert-item" style="padding:5px 0;border-bottom:1px solid #eee;color:#666;">
                            <?php echo htmlspecialchars($product['name']); ?> - <strong style="color:#f39c12;"><?php echo $product['stock']; ?> units left</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($outOfStockProducts)): ?>
                <div class="alert-section">
                    <h4 style="color:#e74c3c;margin:0 0 10px 0;">🚫 Out of Stock Items</h4>
                    <?php foreach ($outOfStockProducts as $product): ?>
                        <div class="alert-item" style="padding:5px 0;border-bottom:1px solid #eee;color:#e74c3c;">
                            <?php echo htmlspecialchars($product['name']); ?> - <strong>Out of Stock</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        // Auto-refresh dashboard data every 30 seconds
        let autoRefreshInterval;
        
        function refreshDashboardStats() {
            fetch('../backend/admin/dashboard.php?action=get_stats')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Stats received:', data); // Debug log
                    
                    if (data && !data.error) {
                        // Update statistics
                        document.getElementById('total-users').textContent = Number(data.total_users || 0).toLocaleString();
                        document.getElementById('available-units').textContent = Number(data.available_units || 0).toLocaleString();
                        document.getElementById('pending-orders').textContent = Number(data.pending_orders || 0).toLocaleString();
                        document.getElementById('total-revenue').textContent = 'R' + Number(data.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        
                        // Update last refresh indicator
                        updateLastRefreshTime();
                    } else {
                        console.error('Stats error:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing stats:', error);
                });
        }
        
        function refreshActivities() {
            fetch('../backend/admin/dashboard.php?action=get_activities&limit=15')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(activities => {
                    const activitiesList = document.getElementById('activities-list');
                    
                    console.log('Activities received:', activities); // Debug log
                    
                    if (activities && activities.length > 0) {
                        activitiesList.innerHTML = activities.map(activity => `
                            <div class="activity-item">
                                <span class="activity-icon">${activity.icon || '📋'}</span>
                                <span class="activity-time">${formatActivityDate(activity.timestamp)}</span>
                                - ${escapeHtml(activity.description)}
                            </div>
                        `).join('');
                    } else if (activities && activities.error) {
                        activitiesList.innerHTML = `<div class="activity-item" style="color:#e74c3c;">Error: ${escapeHtml(activities.error)}</div>`;
                    } else {
                        activitiesList.innerHTML = '<div class="activity-item">No recent activities found. <button onclick="debugDashboard()" style="margin-left:10px;padding:2px 8px;font-size:0.8rem;">Debug</button> <button onclick="testUsers()" style="margin-left:5px;padding:2px 8px;font-size:0.8rem;">Test Users</button></div>';
                    }
                    
                    updateLastRefreshTime();
                })
                .catch(error => {
                    console.error('Error refreshing activities:', error);
                    const activitiesList = document.getElementById('activities-list');
                    activitiesList.innerHTML = `<div class="activity-item" style="color:#e74c3c;">Failed to load activities. <button onclick="debugDashboard()" style="margin-left:10px;padding:2px 8px;font-size:0.8rem;">Debug</button></div>`;
                });
        }

        function debugDashboard() {
            fetch('../backend/admin/dashboard.php?action=debug')
                .then(response => response.json())
                .then(debug => {
                    console.log('Debug information:', debug);
                    alert('Debug info logged to console. Check browser developer tools.');
                })
                .catch(error => {
                    console.error('Debug error:', error);
                    alert('Debug failed. Check console for details.');
                });
        }

        function testUsers() {
            fetch('../backend/admin/dashboard.php?action=test_users')
                .then(response => response.json())
                .then(result => {
                    console.log('User count test:', result);
                    if (result.error) {
                        alert('Error: ' + result.error);
                    } else {
                        alert(`Direct SQL Count: ${result.direct_count}\nFunction Count: ${result.function_count}`);
                    }
                })
                .catch(error => {
                    console.error('User test error:', error);
                    alert('User test failed. Check console for details.');
                });
        }
        
        function formatActivityDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).substr(-2);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}-${month}-${year}, ${hours}:${minutes}`;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function updateLastRefreshTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            
            // Add or update refresh indicator
            let refreshIndicator = document.getElementById('refresh-indicator');
            if (!refreshIndicator) {
                refreshIndicator = document.createElement('small');
                refreshIndicator.id = 'refresh-indicator';
                refreshIndicator.style.cssText = 'color:#666;font-size:0.85rem;margin-left:10px;';
                document.querySelector('.dashboard-header h1').appendChild(refreshIndicator);
            }
            refreshIndicator.textContent = ` (Last updated: ${timeString})`;
        }
        
        function startAutoRefresh() {
            // Refresh every 30 seconds
            autoRefreshInterval = setInterval(() => {
                refreshDashboardStats();
                refreshActivities();
            }, 30000);
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add refresh button event listener
            document.getElementById('refresh-activities').addEventListener('click', function() {
                this.innerHTML = '🔄 Refreshing...';
                this.disabled = true;
                
                Promise.all([
                    refreshDashboardStats(),
                    refreshActivities()
                ]).then(() => {
                    this.innerHTML = '🔄 Refresh Activities';
                    this.disabled = false;
                }).catch(() => {
                    this.innerHTML = '🔄 Refresh Activities';
                    this.disabled = false;
                });
            });
            
            // Start auto-refresh
            startAutoRefresh();
            
            // Add initial refresh time
            updateLastRefreshTime();
        });
        
        // Stop auto-refresh when page is hidden/minimized
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
        
        // Add some visual enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation to stat cards
            const statCards = document.querySelectorAll('.stat-card h3');
            statCards.forEach(card => {
                card.style.transition = 'all 0.3s ease';
                card.addEventListener('mouseover', function() {
                    this.style.transform = 'scale(1.05)';
                });
                card.addEventListener('mouseout', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Add pulse effect for urgent alerts
            const urgentAlerts = document.querySelectorAll('.activity-item:contains("Order"), .alert-item:contains("Out of Stock")');
            urgentAlerts.forEach(alert => {
                alert.style.animation = 'pulse 2s infinite';
            });
        });
        
        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.7; }
                100% { opacity: 1; }
            }
            
            .activity-icon {
                margin-right: 5px;
            }
            
            .activity-time {
                font-weight: 600;
                color: #1976d2;
            }
            
            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transition: all 0.3s ease;
            }
            
            .alert-item:hover {
                background-color: #f8f9fa;
                padding-left: 10px;
                transition: all 0.2s ease;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>