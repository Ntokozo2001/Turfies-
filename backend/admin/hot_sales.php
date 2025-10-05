<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

/**
 * Get hot selling products based on cart additions
 * @param int $limit Number of products to return
 * @param int $days Number of days to look back (default 30)
 * @return array Hot selling products with stats
 */
function getHotSellingProducts($limit = 10, $days = 30) {
    global $pdo;
    
    try {
        $sql = '
            SELECT 
                p.product_id,
                p.name,
                p.price,
                p.stock,
                p.image_url,
                COUNT(c.cart_id) as times_added_to_cart,
                SUM(c.quantity) as total_quantity_requested,
                COUNT(DISTINCT c.user_id) as unique_customers,
                COUNT(DISTINCT c.session_id) as anonymous_additions,
                (COUNT(c.cart_id) * 0.4 + SUM(c.quantity) * 0.3 + COUNT(DISTINCT c.user_id) * 0.3) as popularity_score
            FROM products p
            LEFT JOIN cart c ON p.product_id = c.product_id 
                AND c.added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            WHERE p.deleted_at IS NULL
            GROUP BY p.product_id, p.name, p.price, p.stock, p.image_url
            HAVING times_added_to_cart > 0
            ORDER BY popularity_score DESC, times_added_to_cart DESC
            LIMIT ?
        ';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting hot selling products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get hot products based on wishlist additions
 * @param int $limit Number of products to return
 * @param int $days Number of days to look back
 * @return array Most wishlisted products
 */
function getMostWishlistedProducts($limit = 10, $days = 30) {
    global $pdo;
    
    try {
        $sql = '
            SELECT 
                p.product_id,
                p.name,
                p.price,
                p.stock,
                p.image_url,
                COUNT(w.wishlist_id) as times_wishlisted,
                COUNT(DISTINCT w.user_id) as unique_users_wishlisted
            FROM products p
            LEFT JOIN wishlist w ON p.product_id = w.product_id 
                AND w.added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            WHERE p.deleted_at IS NULL
            GROUP BY p.product_id, p.name, p.price, p.stock, p.image_url
            HAVING times_wishlisted > 0
            ORDER BY times_wishlisted DESC, unique_users_wishlisted DESC
            LIMIT ?
        ';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting most wishlisted products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get trending products (combination of cart and wishlist activity)
 * @param int $limit Number of products to return
 * @param int $days Number of days to look back
 * @return array Trending products with combined scores
 */
function getTrendingProducts($limit = 10, $days = 30) {
    global $pdo;
    
    try {
        $sql = '
            SELECT 
                p.product_id,
                p.name,
                p.price,
                p.stock,
                p.image_url,
                COALESCE(cart_stats.cart_additions, 0) as cart_additions,
                COALESCE(cart_stats.cart_quantity, 0) as cart_quantity,
                COALESCE(wishlist_stats.wishlist_additions, 0) as wishlist_additions,
                (
                    COALESCE(cart_stats.cart_additions, 0) * 2 + 
                    COALESCE(wishlist_stats.wishlist_additions, 0) * 1.5 +
                    COALESCE(cart_stats.cart_quantity, 0) * 0.5
                ) as trend_score
            FROM products p
            LEFT JOIN (
                SELECT 
                    product_id,
                    COUNT(*) as cart_additions,
                    SUM(quantity) as cart_quantity
                FROM cart 
                WHERE added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY product_id
            ) cart_stats ON p.product_id = cart_stats.product_id
            LEFT JOIN (
                SELECT 
                    product_id,
                    COUNT(*) as wishlist_additions
                FROM wishlist 
                WHERE added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY product_id
            ) wishlist_stats ON p.product_id = wishlist_stats.product_id
            WHERE p.deleted_at IS NULL
            HAVING trend_score > 0
            ORDER BY trend_score DESC
            LIMIT ?
        ';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days, $days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting trending products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get product performance summary for a specific product
 * @param int $product_id Product ID
 * @param int $days Number of days to analyze
 * @return array Product performance data
 */
function getProductPerformance($product_id, $days = 30) {
    global $pdo;
    
    try {
        // Get cart statistics
        $stmt = $pdo->prepare('
            SELECT 
                COUNT(*) as cart_additions,
                SUM(quantity) as total_quantity,
                COUNT(DISTINCT user_id) as unique_users,
                AVG(quantity) as avg_quantity_per_addition
            FROM cart 
            WHERE product_id = ? AND added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ');
        $stmt->execute([$product_id, $days]);
        $cartStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get wishlist statistics
        $stmt = $pdo->prepare('
            SELECT 
                COUNT(*) as wishlist_additions,
                COUNT(DISTINCT user_id) as unique_wishlist_users
            FROM wishlist 
            WHERE product_id = ? AND added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ');
        $stmt->execute([$product_id, $days]);
        $wishlistStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get product details
        $stmt = $pdo->prepare('SELECT name, price, stock FROM products WHERE product_id = ?');
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return array_merge($product, $cartStats, $wishlistStats, [
            'analysis_period_days' => $days,
            'conversion_rate' => $cartStats['cart_additions'] > 0 && $wishlistStats['wishlist_additions'] > 0 
                ? round(($cartStats['cart_additions'] / $wishlistStats['wishlist_additions']) * 100, 2) 
                : 0
        ]);
        
    } catch (PDOException $e) {
        error_log('Error getting product performance: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get sales performance by time periods
 * @param string $period 'daily', 'weekly', 'monthly'
 * @param int $limit Number of periods to return
 * @return array Sales data by time period
 */
function getSalesPerformanceByTime($period = 'daily', $limit = 7) {
    global $pdo;
    
    try {
        $dateFormat = '';
        $groupBy = '';
        
        switch ($period) {
            case 'daily':
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(added_at)';
                break;
            case 'weekly':
                $dateFormat = '%Y-%u';
                $groupBy = 'YEARWEEK(added_at)';
                break;
            case 'monthly':
                $dateFormat = '%Y-%m';
                $groupBy = 'DATE_FORMAT(added_at, "%Y-%m")';
                break;
            default:
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(added_at)';
        }
        
        $sql = "
            SELECT 
                DATE_FORMAT(added_at, ?) as time_period,
                COUNT(DISTINCT product_id) as unique_products_added,
                COUNT(*) as total_additions,
                SUM(quantity) as total_quantity
            FROM cart 
            WHERE added_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY $groupBy
            ORDER BY time_period DESC
            LIMIT ?
        ";
        
        $days = $period === 'monthly' ? 365 : ($period === 'weekly' ? 70 : 30);
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dateFormat, $days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting sales performance by time: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get comprehensive hot sales dashboard data
 * @return array Complete hot sales analysis
 */
function getHotSalesDashboard() {
    return [
        'hot_selling' => getHotSellingProducts(10),
        'most_wishlisted' => getMostWishlistedProducts(10),
        'trending' => getTrendingProducts(10),
        'daily_performance' => getSalesPerformanceByTime('daily', 7),
        'weekly_performance' => getSalesPerformanceByTime('weekly', 4),
    ];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'hot_selling':
            $limit = intval($_GET['limit'] ?? 10);
            $days = intval($_GET['days'] ?? 30);
            echo json_encode(getHotSellingProducts($limit, $days));
            break;
            
        case 'most_wishlisted':
            $limit = intval($_GET['limit'] ?? 10);
            $days = intval($_GET['days'] ?? 30);
            echo json_encode(getMostWishlistedProducts($limit, $days));
            break;
            
        case 'trending':
            $limit = intval($_GET['limit'] ?? 10);
            $days = intval($_GET['days'] ?? 30);
            echo json_encode(getTrendingProducts($limit, $days));
            break;
            
        case 'product_performance':
            $product_id = intval($_GET['product_id'] ?? 0);
            $days = intval($_GET['days'] ?? 30);
            if ($product_id > 0) {
                echo json_encode(getProductPerformance($product_id, $days));
            } else {
                echo json_encode(['error' => 'Invalid product ID']);
            }
            break;
            
        case 'sales_by_time':
            $period = $_GET['period'] ?? 'daily';
            $limit = intval($_GET['limit'] ?? 7);
            echo json_encode(getSalesPerformanceByTime($period, $limit));
            break;
            
        case 'dashboard':
            echo json_encode(getHotSalesDashboard());
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

// If accessed directly, return hot sales data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    return getHotSalesDashboard();
}
?>