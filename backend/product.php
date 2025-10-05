<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

/**
 * Get all products from database
 * @return array Array of products with all details
 */
function getAllProducts() {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM product ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get single product by ID
 * @param int $product_id Product ID
 * @return array|null Product details or null if not found
 */
function getProductById($product_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM product WHERE product_id = ?');
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching product by ID: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get products by category (if you add category column later)
 * @param string $category Category name
 * @return array Array of products in the category
 */
function getProductsByCategory($category) {
    global $pdo;
    try {
        // Note: Category column doesn't exist in product table, returning all products
        $stmt = $pdo->prepare('SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM product ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching products by category: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get featured products (products with high stock or marked as featured)
 * @param int $limit Number of featured products to return
 * @return array Array of featured products
 */
function getFeaturedProducts($limit = 3) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM product WHERE stock > 0 ORDER BY stock DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching featured products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get latest products
 * @param int $limit Number of latest products to return
 * @return array Array of latest products
 */
function getLatestProducts($limit = 5) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT product_id, name, description, price, stock, image_url, hover_image_url, created_at, updated_at FROM product ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching latest products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get product details from product_details table by product ID
 * @param int $product_id Product ID
 * @return array|null Product details or null if not found
 */
function getProductDetailsById($product_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT detail_id, product_id, long_description FROM product_details WHERE product_id = ?');
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching product details by ID: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get all product details from product_details table
 * @return array Array of all product details
 */
function getAllProductDetails() {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT detail_id, product_id, long_description FROM product_details ORDER BY product_id ASC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching all product details: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get product with its detailed information (combines products and product_details tables)
 * @param int $product_id Product ID
 * @return array|null Complete product information or null if not found
 */
function getProductWithDetails($product_id) {
    global $pdo;
    try {
        $sql = 'SELECT p.product_id, p.name, p.description, p.price, p.stock, p.image_url, p.hover_image_url, 
                       p.created_at, p.updated_at, pd.detail_id, pd.long_description
                FROM product p 
                LEFT JOIN product_details pd ON p.product_id = pd.product_id 
                WHERE p.product_id = ?';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching product with details: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get all products with their detailed information (combines products and product_details tables)
 * @return array Array of products with their details
 */
function getAllProductsWithDetails() {
    global $pdo;
    try {
        $sql = 'SELECT p.product_id, p.name, p.description, p.price, p.stock, p.image_url, p.hover_image_url, 
                       p.created_at, p.updated_at, pd.detail_id, pd.long_description
                FROM product p 
                LEFT JOIN product_details pd ON p.product_id = pd.product_id 
                ORDER BY p.created_at DESC';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching all products with details: ' . $e->getMessage());
        return [];
    }
}

/**
 * Insert or update product details
 * @param int $product_id Product ID
 * @param string $long_description Long description content
 * @return bool True on success, false on failure
 */
function saveProductDetails($product_id, $long_description) {
    global $pdo;
    try {
        // Check if details already exist for this product
        $existingDetails = getProductDetailsById($product_id);
        
        if ($existingDetails) {
            // Update existing details
            $stmt = $pdo->prepare('UPDATE product_details SET long_description = ? WHERE product_id = ?');
            $stmt->execute([$long_description, $product_id]);
        } else {
            // Insert new details
            $stmt = $pdo->prepare('INSERT INTO product_details (product_id, long_description) VALUES (?, ?)');
            $stmt->execute([$product_id, $long_description]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log('Error saving product details: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete product details by product ID
 * @param int $product_id Product ID
 * @return bool True on success, false on failure
 */
function deleteProductDetails($product_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('DELETE FROM product_details WHERE product_id = ?');
        $stmt->execute([$product_id]);
        return true;
    } catch (PDOException $e) {
        error_log('Error deleting product details: ' . $e->getMessage());
        return false;
    }
}

/**
 * Format price for display
 * @param float $price Raw price
 * @return string Formatted price
 */
function formatPrice($price) {
    return 'R' . number_format($price, 2);
}

/**
 * Check if product is in stock
 * @param int $stock Stock quantity
 * @return bool True if in stock
 */
function isInStock($stock) {
    return $stock > 0;
}

/**
 * Get stock status text
 * @param int $stock Stock quantity
 * @return string Stock status text
 */
function getStockStatus($stock) {
    if ($stock > 10) {
        return 'In Stock';
    } elseif ($stock > 0) {
        return 'Low Stock (' . $stock . ' left)';
    } else {
        return 'Out of Stock';
    }
}

/**
 * Get stock status class for styling
 * @param int $stock Stock quantity
 * @return string CSS class name
 */
function getStockStatusClass($stock) {
    if ($stock > 10) {
        return 'stock-good';
    } elseif ($stock > 0) {
        return 'stock-low';
    } else {
        return 'stock-out';
    }
}

/**
 * Get hover image URL with fallback
 * @param string $hover_image_url Hover image URL from database
 * @param string $main_image_url Main image URL as fallback
 * @return string Image URL to use for hover effect
 */
function getHoverImageUrl($hover_image_url, $main_image_url) {
    // If hover image exists, use it; otherwise, use main image as fallback
    return !empty($hover_image_url) ? $hover_image_url : $main_image_url;
}

/**
 * Check if product has a separate hover image
 * @param string $hover_image_url Hover image URL from database
 * @return bool True if product has a separate hover image
 */
function hasSeparateHoverImage($hover_image_url) {
    return !empty($hover_image_url);
}

// Handle AJAX requests for product data retrieval
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_all':
            echo json_encode(getAllProducts());
            break;
            
        case 'get_product':
            $product_id = intval($_GET['id'] ?? 0);
            if ($product_id > 0) {
                echo json_encode(getProductById($product_id));
            } else {
                echo json_encode(['error' => 'Invalid product ID']);
            }
            break;
            
        case 'get_featured':
            $limit = intval($_GET['limit'] ?? 3);
            echo json_encode(getFeaturedProducts($limit));
            break;
            
        case 'get_latest':
            $limit = intval($_GET['limit'] ?? 5);
            echo json_encode(getLatestProducts($limit));
            break;
            
        case 'get_by_category':
            $category = $_GET['category'] ?? '';
            if (!empty($category)) {
                echo json_encode(getProductsByCategory($category));
            } else {
                echo json_encode(['error' => 'Category required']);
            }
            break;
            
        case 'get_product_details':
            $product_id = intval($_GET['id'] ?? 0);
            if ($product_id > 0) {
                echo json_encode(getProductDetailsById($product_id));
            } else {
                echo json_encode(['error' => 'Invalid product ID']);
            }
            break;
            
        case 'get_all_details':
            echo json_encode(getAllProductDetails());
            break;
            
        case 'get_product_with_details':
            $product_id = intval($_GET['id'] ?? 0);
            if ($product_id > 0) {
                echo json_encode(getProductWithDetails($product_id));
            } else {
                echo json_encode(['error' => 'Invalid product ID']);
            }
            break;
            
        case 'get_all_with_details':
            echo json_encode(getAllProductsWithDetails());
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

// For regular page loads, get all products
$products = getAllProducts();
$total_products = count($products);
?>
?>
