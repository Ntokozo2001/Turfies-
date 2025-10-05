<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

/**
 * Search for products based on various criteria
 * @param string $query Search query (searches name and description)
 * @param array $filters Additional filters (price_min, price_max, stock_filter)
 * @param string $sort_by Sort field (name, price, stock, created_at)
 * @param string $sort_order Sort order (ASC, DESC)
 * @return array Array of matching products
 */
function searchProducts($query = '', $filters = [], $sort_by = 'name', $sort_order = 'ASC') {
    global $pdo;
    try {
        $sql = 'SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM products WHERE 1=1';
        $params = [];
        
        // Search query in name and description
        if (!empty($query)) {
            $sql .= ' AND (name LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . trim($query) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Price range filter
        if (!empty($filters['price_min'])) {
            $sql .= ' AND price >= ?';
            $params[] = floatval($filters['price_min']);
        }
        
        if (!empty($filters['price_max'])) {
            $sql .= ' AND price <= ?';
            $params[] = floatval($filters['price_max']);
        }
        
        // Stock availability filter
        if (!empty($filters['stock_filter'])) {
            switch ($filters['stock_filter']) {
                case 'in_stock':
                    $sql .= ' AND stock > 0';
                    break;
                case 'low_stock':
                    $sql .= ' AND stock > 0 AND stock <= 10';
                    break;
                case 'out_of_stock':
                    $sql .= ' AND stock = 0';
                    break;
            }
        }
        
        // Sorting
        $valid_sort_fields = ['name', 'price', 'stock', 'created_at'];
        $valid_sort_orders = ['ASC', 'DESC'];
        
        if (in_array($sort_by, $valid_sort_fields)) {
            $sort_order = in_array(strtoupper($sort_order), $valid_sort_orders) ? strtoupper($sort_order) : 'ASC';
            $sql .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;
        } else {
            $sql .= ' ORDER BY name ASC';
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error searching products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get search suggestions based on partial query
 * @param string $query Partial search query
 * @param int $limit Maximum number of suggestions
 * @return array Array of product name suggestions
 */
function getSearchSuggestions($query, $limit = 5) {
    global $pdo;
    try {
        if (empty($query) || strlen(trim($query)) < 2) {
            return [];
        }
        
        $sql = 'SELECT DISTINCT name FROM products WHERE name LIKE ? ORDER BY name ASC LIMIT ?';
        $searchTerm = '%' . trim($query) . '%';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $limit]);
        
        $suggestions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $suggestions[] = $row['name'];
        }
        
        return $suggestions;
    } catch (PDOException $e) {
        error_log('Error getting search suggestions: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get popular search terms (most searched product names)
 * @param int $limit Maximum number of popular terms
 * @return array Array of popular search terms
 */
function getPopularSearchTerms($limit = 5) {
    global $pdo;
    try {
        // For now, return most popular products by name
        // In a real app, you'd track search history
        $sql = 'SELECT name FROM products ORDER BY stock DESC LIMIT ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        
        $terms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $terms[] = $row['name'];
        }
        
        return $terms;
    } catch (PDOException $e) {
        error_log('Error getting popular search terms: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get price range for products (min and max prices)
 * @return array Array with min_price and max_price
 */
function getPriceRange() {
    global $pdo;
    try {
        $sql = 'SELECT MIN(price) as min_price, MAX(price) as max_price FROM products';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'min_price' => floatval($result['min_price'] ?? 0),
            'max_price' => floatval($result['max_price'] ?? 0)
        ];
    } catch (PDOException $e) {
        error_log('Error getting price range: ' . $e->getMessage());
        return ['min_price' => 0, 'max_price' => 0];
    }
}

/**
 * Count total search results without fetching all data
 * @param string $query Search query
 * @param array $filters Additional filters
 * @return int Total number of matching products
 */
function countSearchResults($query = '', $filters = []) {
    global $pdo;
    try {
        $sql = 'SELECT COUNT(*) as total FROM products WHERE 1=1';
        $params = [];
        
        // Search query in name and description
        if (!empty($query)) {
            $sql .= ' AND (name LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . trim($query) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Price range filter
        if (!empty($filters['price_min'])) {
            $sql .= ' AND price >= ?';
            $params[] = floatval($filters['price_min']);
        }
        
        if (!empty($filters['price_max'])) {
            $sql .= ' AND price <= ?';
            $params[] = floatval($filters['price_max']);
        }
        
        // Stock availability filter
        if (!empty($filters['stock_filter'])) {
            switch ($filters['stock_filter']) {
                case 'in_stock':
                    $sql .= ' AND stock > 0';
                    break;
                case 'low_stock':
                    $sql .= ' AND stock > 0 AND stock <= 10';
                    break;
                case 'out_of_stock':
                    $sql .= ' AND stock = 0';
                    break;
            }
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    } catch (PDOException $e) {
        error_log('Error counting search results: ' . $e->getMessage());
        return 0;
    }
}

// Handle AJAX requests for search functionality
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'search':
            $query = $_GET['query'] ?? '';
            $filters = [];
            
            // Parse filters from request
            if (!empty($_GET['price_min'])) {
                $filters['price_min'] = $_GET['price_min'];
            }
            if (!empty($_GET['price_max'])) {
                $filters['price_max'] = $_GET['price_max'];
            }
            if (!empty($_GET['stock_filter'])) {
                $filters['stock_filter'] = $_GET['stock_filter'];
            }
            
            $sort_by = $_GET['sort_by'] ?? 'name';
            $sort_order = $_GET['sort_order'] ?? 'ASC';
            
            $results = searchProducts($query, $filters, $sort_by, $sort_order);
            $total_results = countSearchResults($query, $filters);
            
            echo json_encode([
                'results' => $results,
                'total_results' => $total_results,
                'query' => $query,
                'filters' => $filters
            ]);
            break;
            
        case 'suggestions':
            $query = $_GET['query'] ?? '';
            $limit = intval($_GET['limit'] ?? 5);
            echo json_encode(getSearchSuggestions($query, $limit));
            break;
            
        case 'popular_terms':
            $limit = intval($_GET['limit'] ?? 5);
            echo json_encode(getPopularSearchTerms($limit));
            break;
            
        case 'price_range':
            echo json_encode(getPriceRange());
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}
?>
