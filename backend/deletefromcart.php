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
 * Delete a specific product from user's cart
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID to delete
 * @return array Response with success status and message
 */
function deleteFromCart($user_id, $session_id, $product_id) {
    global $pdo;
    
    try {
        // Get cart item details before deletion for response
        if ($user_id) {
            // Logged-in user
            $getStmt = $pdo->prepare('SELECT c.cart_id, c.quantity, c.product_price, p.name 
                                     FROM cart c 
                                     LEFT JOIN products p ON c.product_id = p.product_id 
                                     WHERE c.user_id = ? AND c.product_id = ?');
            $getStmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $getStmt = $pdo->prepare('SELECT c.cart_id, c.quantity, c.product_price, p.name 
                                     FROM cart c 
                                     LEFT JOIN products p ON c.product_id = p.product_id 
                                     WHERE c.session_id = ? AND c.product_id = ? AND c.user_id IS NULL');
            $getStmt->execute([$session_id, $product_id]);
        }
        
        $cartItem = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cartItem) {
            return [
                'success' => false,
                'message' => 'Product not found in cart'
            ];
        }
        
        // Delete the item
        if ($user_id) {
            // Logged-in user
            $deleteStmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?');
            $deleteStmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $deleteStmt = $pdo->prepare('DELETE FROM cart WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $deleteStmt->execute([$session_id, $product_id]);
        }
        
        if ($deleteStmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Product removed from cart successfully',
                'deleted_item' => [
                    'product_id' => $product_id,
                    'product_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['product_price'],
                    'total_value' => $cartItem['quantity'] * $cartItem['product_price']
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove product from cart'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error deleting from cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while removing product'
        ];
    }
}

/**
 * Delete multiple products from user's cart
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param array $product_ids Array of product IDs to delete
 * @return array Response with success status and details
 */
function deleteMultipleFromCart($user_id, $session_id, $product_ids) {
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
        $total_value_removed = 0;
        
        foreach ($product_ids as $product_id) {
            $product_id = intval($product_id);
            if ($product_id <= 0) {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => 'Invalid product ID'
                ];
                continue;
            }
            
            $result = deleteFromCart($user_id, $session_id, $product_id);
            
            if ($result['success']) {
                $deleted_items[] = $result['deleted_item'];
                $total_value_removed += $result['deleted_item']['total_value'];
            } else {
                $failed_items[] = [
                    'product_id' => $product_id,
                    'reason' => $result['message']
                ];
            }
        }
        
        return [
            'success' => count($deleted_items) > 0,
            'message' => count($deleted_items) . ' product(s) removed from cart',
            'deleted_items' => $deleted_items,
            'failed_items' => $failed_items,
            'total_value_removed' => $total_value_removed,
            'summary' => [
                'deleted_count' => count($deleted_items),
                'failed_count' => count($failed_items),
                'total_attempted' => count($product_ids)
            ]
        ];
        
    } catch (Exception $e) {
        error_log('Error deleting multiple items from cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error occurred while removing multiple products'
        ];
    }
}

/**
 * Delete cart item by cart_id (more specific deletion)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $cart_id Cart ID to delete
 * @return array Response with success status and message
 */
function deleteCartItemById($user_id, $session_id, $cart_id) {
    global $pdo;
    
    try {
        // Get cart item details before deletion
        if ($user_id) {
            // Logged-in user
            $getStmt = $pdo->prepare('SELECT c.cart_id, c.product_id, c.quantity, c.product_price, p.name 
                                     FROM cart c 
                                     LEFT JOIN products p ON c.product_id = p.product_id 
                                     WHERE c.cart_id = ? AND c.user_id = ?');
            $getStmt->execute([$cart_id, $user_id]);
        } else {
            // Anonymous user
            $getStmt = $pdo->prepare('SELECT c.cart_id, c.product_id, c.quantity, c.product_price, p.name 
                                     FROM cart c 
                                     LEFT JOIN products p ON c.product_id = p.product_id 
                                     WHERE c.cart_id = ? AND c.session_id = ? AND c.user_id IS NULL');
            $getStmt->execute([$cart_id, $session_id]);
        }
        
        $cartItem = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cartItem) {
            return [
                'success' => false,
                'message' => 'Cart item not found or does not belong to user'
            ];
        }
        
        // Delete the item
        if ($user_id) {
            // Logged-in user
            $deleteStmt = $pdo->prepare('DELETE FROM cart WHERE cart_id = ? AND user_id = ?');
            $deleteStmt->execute([$cart_id, $user_id]);
        } else {
            // Anonymous user
            $deleteStmt = $pdo->prepare('DELETE FROM cart WHERE cart_id = ? AND session_id = ? AND user_id IS NULL');
            $deleteStmt->execute([$cart_id, $session_id]);
        }
        
        if ($deleteStmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Cart item removed successfully',
                'deleted_item' => [
                    'cart_id' => $cart_id,
                    'product_id' => $cartItem['product_id'],
                    'product_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['product_price'],
                    'total_value' => $cartItem['quantity'] * $cartItem['product_price']
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove cart item'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error deleting cart item by ID: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while removing cart item'
        ];
    }
}

/**
 * Clear entire cart for user
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @return array Response with success status and message
 */
function clearCart($user_id, $session_id) {
    global $pdo;
    
    try {
        // Get cart summary before clearing
        if ($user_id) {
            // Logged-in user
            $summaryStmt = $pdo->prepare('SELECT COUNT(*) as item_count, 
                                         COALESCE(SUM(quantity), 0) as total_quantity,
                                         COALESCE(SUM(product_price * quantity), 0) as total_value
                                         FROM cart WHERE user_id = ?');
            $summaryStmt->execute([$user_id]);
        } else {
            // Anonymous user
            $summaryStmt = $pdo->prepare('SELECT COUNT(*) as item_count, 
                                         COALESCE(SUM(quantity), 0) as total_quantity,
                                         COALESCE(SUM(product_price * quantity), 0) as total_value
                                         FROM cart WHERE session_id = ? AND user_id IS NULL');
            $summaryStmt->execute([$session_id]);
        }
        
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($summary['item_count'] == 0) {
            return [
                'success' => false,
                'message' => 'Cart is already empty'
            ];
        }
        
        // Clear the cart
        if ($user_id) {
            // Logged-in user
            $clearStmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
            $clearStmt->execute([$user_id]);
        } else {
            // Anonymous user
            $clearStmt = $pdo->prepare('DELETE FROM cart WHERE session_id = ? AND user_id IS NULL');
            $clearStmt->execute([$session_id]);
        }
        
        return [
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cleared_summary' => [
                'items_removed' => $clearStmt->rowCount(),
                'total_quantity_removed' => $summary['total_quantity'],
                'total_value_removed' => $summary['total_value']
            ]
        ];
        
    } catch (PDOException $e) {
        error_log('Error clearing cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred while clearing cart'
        ];
    }
}

/**
 * Get cart items that are about to be deleted (for confirmation)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param array $product_ids Array of product IDs to check
 * @return array Cart items details
 */
function getCartItemsForDeletion($user_id, $session_id, $product_ids) {
    global $pdo;
    
    try {
        if (empty($product_ids) || !is_array($product_ids)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        if ($user_id) {
            // Logged-in user
            $sql = "SELECT c.cart_id, c.product_id, c.quantity, c.product_price, p.name, p.image_url,
                           (c.quantity * c.product_price) as total_value
                    FROM cart c 
                    LEFT JOIN products p ON c.product_id = p.product_id 
                    WHERE c.user_id = ? AND c.product_id IN ($placeholders)";
            $params = array_merge([$user_id], $product_ids);
        } else {
            // Anonymous user
            $sql = "SELECT c.cart_id, c.product_id, c.quantity, c.product_price, p.name, p.image_url,
                           (c.quantity * c.product_price) as total_value
                    FROM cart c 
                    LEFT JOIN products p ON c.product_id = p.product_id 
                    WHERE c.session_id = ? AND c.user_id IS NULL AND c.product_id IN ($placeholders)";
            $params = array_merge([$session_id], $product_ids);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error getting cart items for deletion: ' . $e->getMessage());
        return [];
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
            
            echo json_encode(deleteFromCart($user_id, $session_id, $product_id));
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
            
            echo json_encode(deleteMultipleFromCart($user_id, $session_id, $product_ids));
            break;
            
        case 'delete_by_cart_id':
            $cart_id = intval($_POST['cart_id'] ?? $_GET['cart_id'] ?? 0);
            
            if ($cart_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid cart ID'
                ]);
                break;
            }
            
            echo json_encode(deleteCartItemById($user_id, $session_id, $cart_id));
            break;
            
        case 'clear_cart':
            echo json_encode(clearCart($user_id, $session_id));
            break;
            
        case 'get_items_for_deletion':
            $product_ids = $_POST['product_ids'] ?? $_GET['product_ids'] ?? [];
            
            // Handle JSON string input
            if (is_string($product_ids)) {
                $product_ids = json_decode($product_ids, true);
            }
            
            echo json_encode(getCartItemsForDeletion($user_id, $session_id, $product_ids));
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
