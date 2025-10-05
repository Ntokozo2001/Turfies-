<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/product.php';

/**
 * Get or create a session identifier for anonymous users
 * @return string Session identifier
 */
function getSessionIdentifier() {
    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = session_id();
    }
    return $_SESSION['session_id'];
}

/**
 * Get user identifier (user_id if logged in, session_id if anonymous)
 * @return array Array with 'user_id' and 'session_id'
 */
function getUserIdentifier() {
    $user_id = $_SESSION['user_id'] ?? null;
    $session_id = getSessionIdentifier();
    
    return [
        'user_id' => $user_id,
        'session_id' => $session_id,
        'is_logged_in' => $user_id !== null
    ];
}

/**
 * Migrate anonymous wishlist items to logged-in user
 * @param string $session_id Session ID of anonymous user
 * @param int $user_id User ID to migrate to
 * @return array Response with migration results
 */
function migrateAnonymousWishlist($session_id, $user_id) {
    global $pdo;
    
    try {
        // Get anonymous wishlist items
        $stmt = $pdo->prepare('SELECT * FROM wishlist WHERE session_id = ? AND user_id IS NULL');
        $stmt->execute([$session_id]);
        $anonymousItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $migrated = 0;
        $duplicates = 0;
        
        foreach ($anonymousItems as $item) {
            // Check if user already has this product in wishlist
            $checkStmt = $pdo->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?');
            $checkStmt->execute([$user_id, $item['product_id']]);
            
            if ($checkStmt->fetch()) {
                // Product already exists for this user, remove the anonymous entry
                $deleteStmt = $pdo->prepare('DELETE FROM wishlist WHERE wishlist_id = ?');
                $deleteStmt->execute([$item['wishlist_id']]);
                $duplicates++;
            } else {
                // Migrate the item to the logged-in user
                $updateStmt = $pdo->prepare('UPDATE wishlist SET user_id = ?, session_id = NULL WHERE wishlist_id = ?');
                $updateStmt->execute([$user_id, $item['wishlist_id']]);
                $migrated++;
            }
        }
        
        return [
            'success' => true,
            'migrated' => $migrated,
            'duplicates' => $duplicates,
            'message' => "Wishlist migration completed. $migrated items migrated, $duplicates duplicates removed."
        ];
        
    } catch (PDOException $e) {
        error_log('Error migrating anonymous wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to migrate wishlist items'
        ];
    }
}

/**
 * Add product to user's wishlist (supports both logged-in and anonymous users)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID
 * @param int $admin_id Admin ID (optional, can be null)
 * @return array Response with success status and message
 */
function addToWishlist($user_id, $session_id, $product_id, $admin_id = null) {
    global $pdo;
    
    try {
        // First, check if the product exists and get its current price
        $product = getProductById($product_id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        // Check if the product is already in the wishlist (for logged-in or anonymous user)
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('SELECT wishlist_id FROM wishlist WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id, $product_id]);
        }
        
        if ($stmt->fetch()) {
            return [
                'success' => false,
                'message' => 'Product is already in your wishlist'
            ];
        }
        
        // Add product to wishlist
        $stmt = $pdo->prepare('INSERT INTO wishlist (user_id, session_id, admin_id, product_id, product_price, added_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$user_id, $session_id, $admin_id, $product_id, $product['price']]);
        
        return [
            'success' => true,
            'message' => 'Product added to wishlist successfully',
            'wishlist_id' => $pdo->lastInsertId()
        ];
        
    } catch (PDOException $e) {
        error_log('Error adding to wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to add product to wishlist'
        ];
    }
}

/**
 * Remove product from user's wishlist
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID
 * @return array Response with success status and message
 */
function removeFromWishlist($user_id, $session_id, $product_id) {
    global $pdo;
    
    try {
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('DELETE FROM wishlist WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id, $product_id]);
        }
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Product removed from wishlist successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Product not found in wishlist'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error removing from wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to remove product from wishlist'
        ];
    }
}

/**
 * Get user's wishlist with product details
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @return array Array of wishlist items with product information
 */
function getUserWishlist($user_id, $session_id) {
    global $pdo;
    
    try {
        if ($user_id) {
            // Logged-in user
            $sql = 'SELECT w.wishlist_id, w.user_id, w.session_id, w.admin_id, w.product_id, w.product_price as wishlist_price, w.added_at,
                           p.name, p.description, p.price as current_price, p.stock, p.image_url, p.hover_image_url
                    FROM wishlist w
                    LEFT JOIN products p ON w.product_id = p.product_id
                    WHERE w.user_id = ?
                    ORDER BY w.added_at DESC';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);
        } else {
            // Anonymous user
            $sql = 'SELECT w.wishlist_id, w.user_id, w.session_id, w.admin_id, w.product_id, w.product_price as wishlist_price, w.added_at,
                           p.name, p.description, p.price as current_price, p.stock, p.image_url, p.hover_image_url
                    FROM wishlist w
                    LEFT JOIN products p ON w.product_id = p.product_id
                    WHERE w.session_id = ? AND w.user_id IS NULL
                    ORDER BY w.added_at DESC';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$session_id]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error fetching user wishlist: ' . $e->getMessage());
        return [];
    }
}

/**
 * Check if product is in user's wishlist
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID
 * @return bool True if product is in wishlist
 */
function isInWishlist($user_id, $session_id, $product_id) {
    global $pdo;
    
    try {
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('SELECT wishlist_id FROM wishlist WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id, $product_id]);
        }
        
        return $stmt->fetch() !== false;
        
    } catch (PDOException $e) {
        error_log('Error checking wishlist status: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get wishlist count for a user
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @return int Number of items in wishlist
 */
function getWishlistCount($user_id, $session_id) {
    global $pdo;
    
    try {
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?');
            $stmt->execute([$user_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM wishlist WHERE session_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id]);
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['count']);
        
    } catch (PDOException $e) {
        error_log('Error getting wishlist count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Clear user's entire wishlist
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @return array Response with success status and message
 */
function clearWishlist($user_id, $session_id) {
    global $pdo;
    
    try {
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ?');
            $stmt->execute([$user_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('DELETE FROM wishlist WHERE session_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id]);
        }
        
        return [
            'success' => true,
            'message' => 'Wishlist cleared successfully',
            'items_removed' => $stmt->rowCount()
        ];
        
    } catch (PDOException $e) {
        error_log('Error clearing wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to clear wishlist'
        ];
    }
}

/**
 * Move wishlist items to cart (if you have a cart system)
 * @param int $user_id User ID
 * @param array $wishlist_ids Array of specific wishlist IDs to move (optional, moves all if empty)
 * @return array Response with success status and message
 */
function moveWishlistToCart($user_id, $wishlist_ids = []) {
    global $pdo;
    
    try {
        // Get wishlist items to move
        if (empty($wishlist_ids)) {
            // Move all items
            $sql = 'SELECT * FROM wishlist WHERE user_id = ?';
            $params = [$user_id];
        } else {
            // Move specific items
            $placeholders = str_repeat('?,', count($wishlist_ids) - 1) . '?';
            $sql = "SELECT * FROM wishlist WHERE user_id = ? AND wishlist_id IN ($placeholders)";
            $params = array_merge([$user_id], $wishlist_ids);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($wishlistItems)) {
            return [
                'success' => false,
                'message' => 'No items found to move to cart'
            ];
        }
        
        $movedCount = 0;
        
        // Note: This assumes you have a cart table. Adjust as needed.
        foreach ($wishlistItems as $item) {
            // Add to cart (you'll need to implement addToCart function)
            // For now, we'll just count the items that would be moved
            $movedCount++;
        }
        
        // Remove items from wishlist
        if (empty($wishlist_ids)) {
            $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ?');
            $stmt->execute([$user_id]);
        } else {
            $placeholders = str_repeat('?,', count($wishlist_ids) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND wishlist_id IN ($placeholders)");
            $stmt->execute(array_merge([$user_id], $wishlist_ids));
        }
        
        return [
            'success' => true,
            'message' => "$movedCount item(s) moved to cart successfully",
            'items_moved' => $movedCount
        ];
        
    } catch (PDOException $e) {
        error_log('Error moving wishlist to cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to move items to cart'
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Get JSON data if available
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    // Get action from POST, GET, or JSON data
    $action = $_POST['action'] ?? $_GET['action'] ?? ($jsonData['action'] ?? '');
    
    // If no action specified and we have JSON data with product_id, assume 'add' action
    if (empty($action) && !empty($jsonData['product_id'])) {
        $action = 'add';
    }
    
    // Get user identifier (supports both logged-in and anonymous users)
    $userInfo = getUserIdentifier();
    $user_id = $userInfo['user_id'];
    $session_id = $userInfo['session_id'];
    
    switch ($action) {
        case 'add':
            $product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? ($jsonData['product_id'] ?? 0));
            $admin_id = !empty($_POST['admin_id']) ? intval($_POST['admin_id']) : (!empty($jsonData['admin_id']) ? intval($jsonData['admin_id']) : null);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode(addToWishlist($user_id, $session_id, $product_id, $admin_id));
            break;
            
        case 'remove':
            $product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode(removeFromWishlist($user_id, $session_id, $product_id));
            break;
            
        case 'get_wishlist':
            echo json_encode(getUserWishlist($user_id, $session_id));
            break;
            
        case 'check_status':
            $product_id = intval($_GET['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode([
                'in_wishlist' => isInWishlist($user_id, $session_id, $product_id),
                'product_id' => $product_id
            ]);
            break;
            
        case 'get_count':
            echo json_encode(['count' => getWishlistCount($user_id, $session_id)]);
            break;
            
        case 'migrate':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'User must be logged in to migrate wishlist'
                ]);
                break;
            }
            
            echo json_encode(migrateAnonymousWishlist($session_id, $user_id));
            break;
            
        case 'clear':
            echo json_encode(clearWishlist($user_id, $session_id));
            break;
            
        case 'move_to_cart':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please log in to manage your wishlist'
                ]);
                break;
            }
            
            $wishlist_ids = $_POST['wishlist_ids'] ?? [];
            if (is_string($wishlist_ids)) {
                $wishlist_ids = json_decode($wishlist_ids, true) ?? [];
            }
            
            echo json_encode(moveWishlistToCart($user_id, $wishlist_ids));
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
    
    exit;
}
?>
