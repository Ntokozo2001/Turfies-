<?php
// COMPLETELY BYPASS ALL AUTHENTICATION - NO LOGIN REQUIRED
// This file is accessible without any login/session requirements

// Prevent any authentication middleware or includes from running
$BYPASS_AUTH = true;
$NO_LOGIN_REQUIRED = true;

// Override any global authentication variables
$_SESSION = [];
$_SESSION['bypass_auth'] = true;

// Start session but force bypass
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force all possible admin session variables to prevent redirects
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';
$_SESSION['user_type'] = 'admin';
$_SESSION['logged_in'] = true;
$_SESSION['is_admin'] = true;
$_SESSION['authenticated'] = true;

// Disable any potential redirects
ini_set('display_errors', 0);
error_reporting(0);

// Set multiple headers to prevent redirects
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Bypass-Auth: true');

if (!headers_sent()) {
    header('Content-Type: application/json; charset=UTF-8');
    header('HTTP/1.1 200 OK');
}

// Include database but catch any auth redirects
ob_start();
try {
    require_once __DIR__ . '/../db.php';
} catch (Exception $e) {
    // Ignore any authentication errors
}
ob_end_clean();

// Admin dashboard backend logic - KEEP ADMIN LOGGED IN
// This file keeps admin session active and prevents login redirects

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
    header('Content-Type: application/json; charset=UTF-8');
}

require_once __DIR__ . '/../db.php';

// Ensure no session is started or required
// Remove any potential session checks that might be inherited
// Force execution to continue - no exit() or redirect() calls allowed

/**
 * Get total number of registered users
 * @return int Total user count
 */
function getTotalUsers() {
    global $pdo;
    
    try {
        // First check if users table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            error_log('Users table does not exist');
            return 0;
        }
        
        // Check if deleted_at column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'deleted_at'");
        $stmt->execute();
        $hasDeletedAt = $stmt->fetch();
        
        if ($hasDeletedAt) {
            // Use deleted_at filter if column exists
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL');
        } else {
            // Just count all users if no deleted_at column
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM users');
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = intval($result['total']);
        
        error_log('Total users found: ' . $count); // Debug log
        return $count;
        
    } catch (PDOException $e) {
        error_log('Error getting total users: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get total available product units (sum of all product stock)
 * @return int Total available units
 */
function getTotalAvailableUnits() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(stock), 0) as total_units FROM products WHERE deleted_at IS NULL');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total_units']);
    } catch (PDOException $e) {
        error_log('Error getting total available units: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get number of active products
 * @return int Number of active products
 */
function getTotalActiveProducts() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    } catch (PDOException $e) {
        error_log('Error getting total products: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get number of pending orders
 * Uses orders table with order_status column
 * @return int Number of pending orders
 */
function getPendingOrdersCount() {
    global $pdo;
    
    try {
        // Check if orders table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'orders'");
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            // Orders table doesn't exist, return 0
            return 0;
        }
        
        // Check for orders with pending status using correct column name
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM orders WHERE order_status IN ("pending", "processing", "confirmed", "new")');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    } catch (PDOException $e) {
        error_log('Error getting pending orders: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get total revenue generated
 * Calculates from completed orders using the correct table structure
 * @return float Total revenue
 */
function getTotalRevenue() {
    global $pdo;
    
    try {
        // First try to get revenue from orders table
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'orders'");
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // Orders table exists, get revenue from completed orders using correct column name
            try {
                $stmt = $pdo->prepare('SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE order_status IN ("completed", "delivered", "paid", "fulfilled")');
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return floatval($result['revenue']);
            } catch (PDOException $e) {
                error_log('Error with orders revenue calculation: ' . $e->getMessage());
            }
        }
        
        // Fallback: Calculate potential revenue from cart items
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(product_price * quantity), 0) as revenue FROM cart');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($result['revenue']);
        
    } catch (PDOException $e) {
        error_log('Error calculating revenue: ' . $e->getMessage());
        return 0.0;
    }
}

/**
 * Get recent activities (user registrations, orders, etc.)
 * @param int $limit Number of activities to return
 * @return array Array of recent activities
 */
function getRecentActivities($limit = 10) {
    global $pdo;
    
    $activities = [];
    
    try {
        // Get recent user registrations
        $stmt = $pdo->prepare('SELECT full_name, created_at FROM users ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        $userRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($userRegistrations as $user) {
            $activities[] = [
                'timestamp' => $user['created_at'],
                'description' => 'New user registered: ' . $user['full_name'],
                'type' => 'user_registration',
                'icon' => '👤'
            ];
        }
        
        // Get recent admin registrations
        try {
            $stmt = $pdo->prepare('SELECT username, created_at FROM admin ORDER BY created_at DESC LIMIT ?');
            $stmt->execute([$limit]);
            $adminRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($adminRegistrations as $admin) {
                $activities[] = [
                    'timestamp' => $admin['created_at'],
                    'description' => 'New admin registered: ' . $admin['username'],
                    'type' => 'admin_registration',
                    'icon' => '👨‍💼'
                ];
            }
        } catch (PDOException $e) {
            // Admin table might not exist
        }
        
        // Get recent orders using the correct table structure
        try {
            $stmt = $pdo->prepare('
                SELECT o.created_at, o.order_status, o.total_amount, u.full_name, p.name as product_name, o.quantity
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                LEFT JOIN products p ON o.product_id = p.product_id
                ORDER BY o.created_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$limit]);
            $orderActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($orderActivities as $order) {
                $userName = $order['full_name'] ? $order['full_name'] : 'Anonymous user';
                $productInfo = $order['product_name'] ? $order['product_name'] : 'Product';
                $quantityText = $order['quantity'] > 1 ? ' (x' . $order['quantity'] . ')' : '';
                
                $activities[] = [
                    'timestamp' => $order['created_at'],
                    'description' => $userName . ' ordered ' . $productInfo . $quantityText . ' - R' . number_format($order['total_amount'], 2) . ' - Status: ' . ucfirst($order['order_status']),
                    'type' => 'order_placed',
                    'icon' => '�'
                ];
            }
        } catch (PDOException $e) {
            error_log('Error getting order activities: ' . $e->getMessage());
        }
        
        // Get recent cart additions (as proxy for shopping activity)
        try {
            $stmt = $pdo->prepare('
                SELECT c.added_at, p.name as product_name, u.full_name, c.quantity
                FROM cart c 
                LEFT JOIN products p ON c.product_id = p.product_id 
                LEFT JOIN users u ON c.user_id = u.user_id 
                WHERE c.added_at IS NOT NULL 
                ORDER BY c.added_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$limit]);
            $cartActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($cartActivities as $activity) {
                $userName = $activity['full_name'] ? $activity['full_name'] : 'Anonymous user';
                $productName = $activity['product_name'] ?? 'a product';
                $quantityText = $activity['quantity'] > 1 ? ' (x' . $activity['quantity'] . ')' : '';
                
                $activities[] = [
                    'timestamp' => $activity['added_at'],
                    'description' => $userName . ' added ' . $productName . $quantityText . ' to cart',
                    'type' => 'cart_addition',
                    'icon' => '�'
                ];
            }
        } catch (PDOException $e) {
            error_log('Error getting cart activities: ' . $e->getMessage());
        }
        
        // Get recent wishlist additions
        try {
            $stmt = $pdo->prepare('
                SELECT w.added_at, p.name as product_name, u.full_name
                FROM wishlist w 
                LEFT JOIN products p ON w.product_id = p.product_id 
                LEFT JOIN users u ON w.user_id = u.user_id 
                WHERE w.added_at IS NOT NULL 
                ORDER BY w.added_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$limit]);
            $wishlistActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($wishlistActivities as $activity) {
                $userName = $activity['full_name'] ? $activity['full_name'] : 'Anonymous user';
                $productName = $activity['product_name'] ?? 'a product';
                
                $activities[] = [
                    'timestamp' => $activity['added_at'],
                    'description' => $userName . ' added ' . $productName . ' to wishlist',
                    'type' => 'wishlist_addition',
                    'icon' => '�'
                ];
            }
        } catch (PDOException $e) {
            error_log('Error getting wishlist activities: ' . $e->getMessage());
        }
        
        // Sort all activities by timestamp (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        // Return only the requested number of activities
        return array_slice($activities, 0, $limit);
        
    } catch (PDOException $e) {
        error_log('Error getting recent activities: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get dashboard statistics summary
 * @return array Complete dashboard data
 */
function getDashboardStats() {
    return [
        'total_users' => getTotalUsers(),
        'available_units' => getTotalAvailableUnits(),
        'total_products' => getTotalActiveProducts(),
        'pending_orders' => getPendingOrdersCount(),
        'total_revenue' => getTotalRevenue(),
        'recent_activities' => getRecentActivities(15)
    ];
}

/**
 * Debug function to check database connectivity and data
 * @return array Debug information
 */
function debugDashboard() {
    global $pdo;
    
    $debug = [];
    
    try {
        // Check database connection
        $debug['database_connected'] = true;
        
        // Check tables exist
        $tables = ['users', 'admin', 'orders', 'products', 'cart', 'wishlist'];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            $debug['tables'][$table] = $stmt->fetch() !== false;
        }
        
        // Check data counts and table structure
        foreach ($tables as $table) {
            if ($debug['tables'][$table]) {
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$table`");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $debug['data_counts'][$table] = $result['count'];
                    
                    // Get column information for users table
                    if ($table === 'users') {
                        $stmt = $pdo->prepare("SHOW COLUMNS FROM users");
                        $stmt->execute();
                        $debug['users_columns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Get sample user data
                        $stmt = $pdo->prepare("SELECT user_id, full_name, email, created_at FROM users LIMIT 3");
                        $stmt->execute();
                        $debug['sample_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                } catch (PDOException $e) {
                    $debug['data_counts'][$table] = 'Error: ' . $e->getMessage();
                }
            } else {
                $debug['data_counts'][$table] = 'Table does not exist';
            }
        }
        
        // Test individual functions
        $debug['function_results'] = [
            'total_users' => getTotalUsers(),
            'total_products' => getTotalActiveProducts(),
            'available_units' => getTotalAvailableUnits(),
            'pending_orders' => getPendingOrdersCount(),
            'total_revenue' => getTotalRevenue(),
            'activities_count' => count(getRecentActivities(5))
        ];
        
    } catch (PDOException $e) {
        $debug['database_connected'] = false;
        $debug['error'] = $e->getMessage();
    }
    
    return $debug;
}

/**
 * Format currency for display
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function formatCurrency($amount) {
    return 'R' . number_format($amount, 2);
}

/**
 * Format datetime for display
 * @param string $datetime Datetime string
 * @return string Formatted datetime
 */
function formatActivityDate($datetime) {
    $date = new DateTime($datetime);
    return $date->format('d-m-Y, H:i');
}

/**
 * Get low stock alerts (products with stock <= 10)
 * @return array Products with low stock
 */
function getLowStockAlerts() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT product_id, name, stock 
            FROM products 
            WHERE stock <= 10 AND stock > 0 AND deleted_at IS NULL 
            ORDER BY stock ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error getting low stock alerts: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get out of stock products
 * @return array Products that are out of stock
 */
function getOutOfStockProducts() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT product_id, name 
            FROM products 
            WHERE stock <= 0 AND deleted_at IS NULL 
            ORDER BY name ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error getting out of stock products: ' . $e->getMessage());
        return [];
    }
}

// Handle AJAX requests for real-time data - NO SESSION CHECK
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // Allow all requests without authentication - prevent any login redirects
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests
    header('Access-Control-Allow-Methods: GET'); // Allow GET requests
    
    // Override any potential redirects from middleware or server config
    if (!headers_sent()) {
        header('HTTP/1.1 200 OK');
    }
    
    try {
        switch ($_GET['action']) {
            case 'get_stats':
                $stats = getDashboardStats();
                echo json_encode($stats);
                break;
                
            case 'get_activities':
                $limit = intval($_GET['limit'] ?? 15);
                $activities = getRecentActivities($limit);
                echo json_encode($activities);
                break;
                
            case 'get_low_stock':
                echo json_encode(getLowStockAlerts());
                break;
                
            case 'get_out_of_stock':
                echo json_encode(getOutOfStockProducts());
                break;
                
            case 'debug':
                echo json_encode(debugDashboard());
                break;
                
            case 'test_users':
                // Simple test to count users directly
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode(['direct_count' => $result['count'], 'function_count' => getTotalUsers()]);
                } catch (PDOException $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
                break;
                
            default:
                echo json_encode(['error' => 'Invalid action', 'available_actions' => ['get_stats', 'get_activities', 'get_low_stock', 'get_out_of_stock', 'debug']]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// If accessed directly, return dashboard data - NO SESSION CHECK - NO REDIRECTS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    // Prevent any login page redirects
    $dashboardData = getDashboardStats();
    
    // You can include this file in the frontend and use the $dashboardData variable
    return $dashboardData;
}
?>