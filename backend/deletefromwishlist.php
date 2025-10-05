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
 * Delete a specific product from user's wishlist
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID to delete
 * @return array Response with success status and message
 */
function deleteFromWishlist($user_id, $session_id, $product_id) {
    global $pdo;
    
    try {
        // Get wishlist item details before deletion for response
        if ($user_id) {
            // Logged-in user
            $getStmt = $pdo->prepare('SELECT w.wishlist_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price
                                     FROM wishlist w 
                                     LEFT JOIN products p ON w.product_id = p.product_id 
                                     WHERE w.user_id = ? AND w.product_id = ?');
            $getStmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $getStmt = $pdo->prepare('SELECT w.wishlist_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price
                                     FROM wishlist w 
                                     LEFT JOIN products p ON w.product_id = p.product_id 
                                     WHERE w.session_id = ? AND w.product_id = ? AND w.user_id IS NULL');
            $getStmt->execute([$session_id, $product_id]);
        }
        
        $wishlistItem = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$wishlistItem) {
            return [
                'success' => false,
                'message' => 'Product not found in wishlist'
            ];
        }
        
        // Delete the item
        if ($user_id) {
            // Logged-in user
            $deleteStmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?');
            $deleteStmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $deleteStmt = $pdo->prepare('DELETE FROM wishlist WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $deleteStmt->execute([$session_id, $product_id]);
        }
        
        if ($deleteStmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Product removed from wishlist successfully',
                'deleted_item' => [
                    'product_id' => $product_id,
                    'product_name' => $wishlistItem['name'],
                    'wishlist_price' => $wishlistItem['product_price'],
                    'current_price' => $wishlistItem['current_price'],
                    'image_url' => $wishlistItem['image_url'],
                    'added_at' => $wishlistItem['added_at'],
                    'price_difference' => $wishlistItem['current_price'] - $wishlistItem['product_price']
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove product from wishlist'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error deleting from wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while removing product'
        ];
    }
}

/**
 * Delete multiple products from user's wishlist
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param array $product_ids Array of product IDs to delete
 * @return array Response with success status and details
 */
function deleteMultipleFromWishlist($user_id, $session_id, $product_ids) {
    global $pdo;
    
    try {
        if (empty($product_ids) || !is_array($product_ids)) {
            return [
                'success' => false,
                'message' => 'No products specified for deletion'
            ];
        }
        
        $deleted_items = [];
        $failed_items = [];
        $total_wishlist_value = 0;
        $total_current_value = 0;
        
        foreach ($product_ids as $product_id) {
            $product_id = intval($product_id);
            if ($product_id <= 0) {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => 'Invalid product ID'
                ];
                continue;
            }
            
            $result = deleteFromWishlist($user_id, $session_id, $product_id);
            
            if ($result['success']) {
                $deleted_items[] = $result['deleted_item'];
                $total_wishlist_value += $result['deleted_item']['wishlist_price'];
                $total_current_value += $result['deleted_item']['current_price'];
            } else {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => $result['message']
                ];
            }
        }
        
        return [
            'success' => count($deleted_items) > 0,
            'message' => count($deleted_items) . ' product(s) removed from wishlist',
            'deleted_items' => $deleted_items,
            'failed_items' => $failed_items,
            'total_wishlist_value' => $total_wishlist_value,
            'total_current_value' => $total_current_value,
            'total_savings_lost' => $total_current_value - $total_wishlist_value,
            'summary' => [
                'deleted_count' => count($deleted_items),
                'failed_count' => count($failed_items),
                'total_attempted' => count($product_ids)
            ]
        ];
        
    } catch (Exception $e) {
        error_log('Error deleting multiple items from wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error occurred while removing multiple products'
        ];
    }
}

/**
 * Delete wishlist item by wishlist_id (more specific deletion)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $wishlist_id Wishlist ID to delete
 * @return array Response with success status and message
 */
function deleteWishlistItemById($user_id, $session_id, $wishlist_id) {
    global $pdo;
    
    try {
        // Get wishlist item details before deletion
        if ($user_id) {
            // Logged-in user
            $getStmt = $pdo->prepare('SELECT w.wishlist_id, w.product_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price
                                     FROM wishlist w 
                                     LEFT JOIN products p ON w.product_id = p.product_id 
                                     WHERE w.wishlist_id = ? AND w.user_id = ?');
            $getStmt->execute([$wishlist_id, $user_id]);
        } else {
            // Anonymous user
            $getStmt = $pdo->prepare('SELECT w.wishlist_id, w.product_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price
                                     FROM wishlist w 
                                     LEFT JOIN products p ON w.product_id = p.product_id 
                                     WHERE w.wishlist_id = ? AND w.session_id = ? AND w.user_id IS NULL');
            $getStmt->execute([$wishlist_id, $session_id]);
        }
        
        $wishlistItem = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$wishlistItem) {
            return [
                'success' => false,
                'message' => 'Wishlist item not found or does not belong to user'
            ];
        }
        
        // Delete the item
        if ($user_id) {
            // Logged-in user
            $deleteStmt = $pdo->prepare('DELETE FROM wishlist WHERE wishlist_id = ? AND user_id = ?');
            $deleteStmt->execute([$wishlist_id, $user_id]);
        } else {
            // Anonymous user
            $deleteStmt = $pdo->prepare('DELETE FROM wishlist WHERE wishlist_id = ? AND session_id = ? AND user_id IS NULL');
            $deleteStmt->execute([$wishlist_id, $session_id]);
        }
        
        if ($deleteStmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Wishlist item removed successfully',
                'deleted_item' => [
                    'wishlist_id' => $wishlist_id,
                    'product_id' => $wishlistItem['product_id'],
                    'product_name' => $wishlistItem['name'],
                    'wishlist_price' => $wishlistItem['product_price'],
                    'current_price' => $wishlistItem['current_price'],
                    'image_url' => $wishlistItem['image_url'],
                    'added_at' => $wishlistItem['added_at'],
                    'price_difference' => $wishlistItem['current_price'] - $wishlistItem['product_price']
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove wishlist item'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error deleting wishlist item by ID: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while removing wishlist item'
        ];
    }
}

/**
 * Clear entire wishlist for user
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @return array Response with success status and message
 */
function clearWishlist($user_id, $session_id) {
    global $pdo;
    
    try {
        // Get wishlist summary before clearing
        if ($user_id) {
            // Logged-in user
            $summaryStmt = $pdo->prepare('SELECT COUNT(*) as item_count, 
                                         COALESCE(SUM(w.product_price), 0) as total_wishlist_value,
                                         COALESCE(SUM(p.price), 0) as total_current_value
                                         FROM wishlist w
                                         LEFT JOIN products p ON w.product_id = p.product_id
                                         WHERE w.user_id = ?');
            $summaryStmt->execute([$user_id]);
        } else {
            // Anonymous user
            $summaryStmt = $pdo->prepare('SELECT COUNT(*) as item_count, 
                                         COALESCE(SUM(w.product_price), 0) as total_wishlist_value,
                                         COALESCE(SUM(p.price), 0) as total_current_value
                                         FROM wishlist w
                                         LEFT JOIN products p ON w.product_id = p.product_id
                                         WHERE w.session_id = ? AND w.user_id IS NULL');
            $summaryStmt->execute([$session_id]);
        }
        
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($summary['item_count'] == 0) {
            return [
                'success' => false,
                'message' => 'Wishlist is already empty'
            ];
        }
        
        // Clear the wishlist
        if ($user_id) {
            // Logged-in user
            $clearStmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ?');
            $clearStmt->execute([$user_id]);
        } else {
            // Anonymous user
            $clearStmt = $pdo->prepare('DELETE FROM wishlist WHERE session_id = ? AND user_id IS NULL');
            $clearStmt->execute([$session_id]);
        }
        
        return [
            'success' => true,
            'message' => 'Wishlist cleared successfully',
            'cleared_summary' => [
                'items_removed' => $clearStmt->rowCount(),
                'total_wishlist_value' => $summary['total_wishlist_value'],
                'total_current_value' => $summary['total_current_value'],
                'savings_lost' => $summary['total_current_value'] - $summary['total_wishlist_value']
            ]
        ];
        
    } catch (PDOException $e) {
        error_log('Error clearing wishlist: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while clearing wishlist'
        ];
    }
}

/**
 * Get wishlist items that are about to be deleted (for confirmation)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param array $product_ids Array of product IDs to check
 * @return array Wishlist items details
 */
function getWishlistItemsForDeletion($user_id, $session_id, $product_ids) {
    global $pdo;
    
    try {
        if (empty($product_ids) || !is_array($product_ids)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        if ($user_id) {
            // Logged-in user
            $sql = "SELECT w.wishlist_id, w.product_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price,
                           (p.price - w.product_price) as price_difference
                    FROM wishlist w 
                    LEFT JOIN products p ON w.product_id = p.product_id 
                    WHERE w.user_id = ? AND w.product_id IN ($placeholders)";
            $params = array_merge([$user_id], $product_ids);
        } else {
            // Anonymous user
            $sql = "SELECT w.wishlist_id, w.product_id, w.product_price, w.added_at, p.name, p.image_url, p.price as current_price,
                           (p.price - w.product_price) as price_difference
                    FROM wishlist w 
                    LEFT JOIN products p ON w.product_id = p.product_id 
                    WHERE w.session_id = ? AND w.user_id IS NULL AND w.product_id IN ($placeholders)";
            $params = array_merge([$session_id], $product_ids);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting wishlist items for deletion: ' . $e->getMessage());
        return [];
    }
}

/**
 * Move items from wishlist to cart before deletion (optional)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param array $product_ids Array of product IDs to move and delete
 * @return array Response with success status and details
 */
function moveWishlistItemsToCartAndDelete($user_id, $session_id, $product_ids) {
    global $pdo;
    
    try {
        if (empty($product_ids) || !is_array($product_ids)) {
            return [
                'success' => false,
                'message' => 'No products specified for moving'
            ];
        }
        
        $moved_items = [];
        $failed_items = [];
        
        foreach ($product_ids as $product_id) {
            $product_id = intval($product_id);
            if ($product_id <= 0) {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => 'Invalid product ID'
                ];
                continue;
            }
            
            // Get product details
            $product = getProductById($product_id);
            if (!$product) {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => 'Product not found'
                ];
                continue;
            }
            
            // Check stock
            if ($product['stock'] < 1) {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => 'Out of stock'
                ];
                continue;
            }
            
            // Add to cart (assuming addToCart function exists)
            if ($user_id) {
                // Check if already in cart
                $checkCartStmt = $pdo->prepare('SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
                $checkCartStmt->execute([$user_id, $product_id]);
            } else {
                // Check if already in cart for anonymous user
                $checkCartStmt = $pdo->prepare('SELECT cart_id, quantity FROM cart WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
                $checkCartStmt->execute([$session_id, $product_id]);
            }
            
            $existingCartItem = $checkCartStmt->fetch();
            
            if ($existingCartItem) {
                // Update cart quantity
                $newQuantity = $existingCartItem['quantity'] + 1;
                if ($newQuantity <= $product['stock']) {
                    $updateCartStmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE cart_id = ?');
                    $updateCartStmt->execute([$newQuantity, $existingCartItem['cart_id']]);
                    
                    // Remove from wishlist
                    $deleteResult = deleteFromWishlist($user_id, $session_id, $product_id);
                    if ($deleteResult['success']) {
                        $moved_items[] = [
                            'product_id' => $product_id,
                            'product_name' => $product['name'],
                            'action' => 'quantity_updated',
                            'new_cart_quantity' => $newQuantity
                        ];
                    }
                } else {
                    $failed_items[] = [
                        'product_id' => $product_id,
                        'reason' => 'Cart quantity would exceed stock'
                    ];
                }
            } else {
                // Add new item to cart
                $addCartStmt = $pdo->prepare('INSERT INTO cart (user_id, session_id, product_id, product_price, quantity, added_at) VALUES (?, ?, ?, ?, 1, NOW())');
                $addCartStmt->execute([$user_id, $session_id, $product_id, $product['price']]);
                
                // Remove from wishlist
                $deleteResult = deleteFromWishlist($user_id, $session_id, $product_id);
                if ($deleteResult['success']) {
                    $moved_items[] = [
                        'product_id' => $product_id,
                        'product_name' => $product['name'],
                        'action' => 'added_to_cart',
                        'cart_quantity' => 1
                    ];
                }
            }
        }
        
        return [
            'success' => count($moved_items) > 0,
            'message' => count($moved_items) . ' item(s) moved to cart and removed from wishlist',
            'moved_items' => $moved_items,
            'failed_items' => $failed_items,
            'summary' => [
                'moved_count' => count($moved_items),
                'failed_count' => count($failed_items),
                'total_attempted' => count($product_ids)
            ]
        ];
        
    } catch (Exception $e) {
        error_log('Error moving wishlist items to cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error occurred while moving items to cart'
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Get action from POST or GET
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    // Get user identifier (supports both logged-in and anonymous users)
    $userInfo = getUserIdentifier();
    $user_id = $userInfo['user_id'];
    $session_id = $userInfo['session_id'];
    
    switch ($action) {
        case 'delete':
            $product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode(deleteFromWishlist($user_id, $session_id, $product_id));
            break;
            
        case 'delete_multiple':
            $product_ids = $_POST['product_ids'] ?? [];
            
            // Handle JSON string input
            if (is_string($product_ids)) {
                $product_ids = json_decode($product_ids, true);
            }
            
            if (!is_array($product_ids)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product IDs format'
                ]);
                break;
            }
            
            echo json_encode(deleteMultipleFromWishlist($user_id, $session_id, $product_ids));
            break;
            
        case 'delete_by_wishlist_id':
            $wishlist_id = intval($_POST['wishlist_id'] ?? $_GET['wishlist_id'] ?? 0);
            
            if ($wishlist_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid wishlist ID'
                ]);
                break;
            }
            
            echo json_encode(deleteWishlistItemById($user_id, $session_id, $wishlist_id));
            break;
            
        case 'clear_wishlist':
            echo json_encode(clearWishlist($user_id, $session_id));
            break;
            
        case 'get_items_for_deletion':
            $product_ids = $_POST['product_ids'] ?? $_GET['product_ids'] ?? [];
            
            // Handle JSON string input
            if (is_string($product_ids)) {
                $product_ids = json_decode($product_ids, true);
            }
            
            echo json_encode(getWishlistItemsForDeletion($user_id, $session_id, $product_ids));
            break;
            
        case 'move_to_cart_and_delete':
            $product_ids = $_POST['product_ids'] ?? [];
            
            // Handle JSON string input
            if (is_string($product_ids)) {
                $product_ids = json_decode($product_ids, true);
            }
            
            if (!is_array($product_ids)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product IDs format'
                ]);
                break;
            }
            
            echo json_encode(moveWishlistItemsToCartAndDelete($user_id, $session_id, $product_ids));
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action specified'
            ]);
    }
    
    exit;
}
?>
