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
 * Migrate anonymous cart items to logged-in user
 * @param string $session_id Session ID of anonymous user
 * @param int $user_id User ID to migrate to
 * @return array Response with migration results
 */
function migrateAnonymousCart($session_id, $user_id) {
    global $pdo;
    
    try {
        // Get anonymous cart items
        $stmt = $pdo->prepare('SELECT * FROM cart WHERE session_id = ? AND user_id IS NULL');
        $stmt->execute([$session_id]);
        $anonymousItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $migrated = 0;
        $merged = 0;
        $duplicates = 0;
        
        foreach ($anonymousItems as $item) {
            // Check if user already has this product in cart
            $checkStmt = $pdo->prepare('SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
            $checkStmt->execute([$user_id, $item['product_id']]);
            $existingItem = $checkStmt->fetch();
            
            if ($existingItem) {
                // Product already exists for this user, merge quantities
                $newQuantity = $existingItem['quantity'] + $item['quantity'];
                
                // Check stock availability
                $product = getProductById($item['product_id']);
                if ($product && $newQuantity <= $product['stock']) {
                    $updateStmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE cart_id = ?');
                    $updateStmt->execute([$newQuantity, $existingItem['cart_id']]);
                    $merged++;
                } else {
                    $duplicates++;
                }
                
                // Remove the anonymous entry
                $deleteStmt = $pdo->prepare('DELETE FROM cart WHERE cart_id = ?');
                $deleteStmt->execute([$item['cart_id']]);
            } else {
                // Migrate the item to the logged-in user
                $updateStmt = $pdo->prepare('UPDATE cart SET user_id = ?, session_id = NULL WHERE cart_id = ?');
                $updateStmt->execute([$user_id, $item['cart_id']]);
                $migrated++;
            }
        }
        
        return [
            'success' => true,
            'migrated' => $migrated,
            'merged' => $merged,
            'duplicates' => $duplicates,
            'message' => "Cart migration completed. $migrated items migrated, $merged items merged, $duplicates duplicates removed."
        ];
        
    } catch (PDOException $e) {
        error_log('Error migrating anonymous cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to migrate cart items'
        ];
    }
}

/**
 * Add product to user's cart (supports both logged-in and anonymous users)
 * @param int|null $user_id User ID (null for anonymous users)
 * @param string|null $session_id Session ID for anonymous users
 * @param int $product_id Product ID
 * @param int $quantity Quantity to add
 * @param int $admin_id Admin ID (optional, can be null)
 * @return array Response with success status and message
 */
function addToCart($user_id, $session_id, $product_id, $quantity = 1, $admin_id = null) {
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
        
        // Check if product has sufficient stock
        if ($product['stock'] < $quantity) {
            return [
                'success' => false,
                'message' => 'Insufficient stock available. Only ' . $product['stock'] . ' items remaining.'
            ];
        }
        
        // Check if the product is already in the cart (for logged-in or anonymous user)
        if ($user_id) {
            // Logged-in user
            $stmt = $pdo->prepare('SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Anonymous user
            $stmt = $pdo->prepare('SELECT cart_id, quantity FROM cart WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
            $stmt->execute([$session_id, $product_id]);
        }
        
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            // Product already exists in cart, update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $product['stock']) {
                return [
                    'success' => false,
                    'message' => 'Cannot add more items. Total quantity would exceed available stock (' . $product['stock'] . ').'
                ];
            }
            
            $stmt = $pdo->prepare('UPDATE cart SET quantity = ?, product_price = ? WHERE cart_id = ?');
            $stmt->execute([$newQuantity, $product['price'], $existingItem['cart_id']]);
            
            return [
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_id' => $existingItem['cart_id'],
                'new_quantity' => $newQuantity,
                'action' => 'updated'
            ];
        } else {
            // Add new product to cart
            $stmt = $pdo->prepare('INSERT INTO cart (user_id, session_id, admin_id, product_id, product_price, quantity, added_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$user_id, $session_id, $admin_id, $product_id, $product['price'], $quantity]);
            
            return [
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_id' => $pdo->lastInsertId(),
                'quantity' => $quantity,
                'action' => 'added'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error adding to cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to add product to cart'
        ];
    }
}

/**
 * Update product quantity in cart
 * @param int $user_id User ID
 * @param int $product_id Product ID
 * @param int $quantity New quantity
 * @return array Response with success status and message
 */
function updateCartQuantity($user_id, $product_id, $quantity) {
    global $pdo;
    
    try {
        if ($quantity <= 0) {
            return removeFromCart($user_id, $product_id);
        }
        
        // Check product stock
        $product = getProductById($product_id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        if ($product['stock'] < $quantity) {
            return [
                'success' => false,
                'message' => 'Insufficient stock available. Only ' . $product['stock'] . ' items remaining.'
            ];
        }
        
        $stmt = $pdo->prepare('UPDATE cart SET quantity = ?, product_price = ? WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$quantity, $product['price'], $user_id, $product_id]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Cart quantity updated successfully',
                'new_quantity' => $quantity
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Product not found in cart'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error updating cart quantity: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to update cart quantity'
        ];
    }
}

/**
 * Remove product from user's cart
 * @param int $user_id User ID
 * @param int $product_id Product ID
 * @return array Response with success status and message
 */
function removeFromCart($user_id, $product_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$user_id, $product_id]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Product removed from cart successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Product not found in cart'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error removing from cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to remove product from cart'
        ];
    }
}

/**
 * Get user's cart with product details
 * @param int $user_id User ID
 * @return array Array of cart items with product information and totals
 */
function getUserCart($user_id) {
    global $pdo;
    
    try {
        $sql = 'SELECT c.cart_id, c.user_id, c.admin_id, c.product_id, c.product_price as cart_price, c.quantity, c.added_at,
                       p.name, p.description, p.price as current_price, p.stock, p.image_url, p.hover_image_url,
                       (c.product_price * c.quantity) as item_total
                FROM cart c
                LEFT JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = ?
                ORDER BY c.added_at DESC';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate totals
        $subtotal = 0;
        $totalItems = 0;
        
        foreach ($cartItems as &$item) {
            $subtotal += $item['item_total'];
            $totalItems += $item['quantity'];
            
            // Add stock status
            $item['in_stock'] = $item['stock'] >= $item['quantity'];
            $item['stock_message'] = $item['stock'] < $item['quantity'] ? 
                'Only ' . $item['stock'] . ' items available' : null;
        }
        
        return [
            'items' => $cartItems,
            'summary' => [
                'subtotal' => $subtotal,
                'total_items' => $totalItems,
                'item_count' => count($cartItems)
            ]
        ];
        
    } catch (PDOException $e) {
        error_log('Error fetching user cart: ' . $e->getMessage());
        return [
            'items' => [],
            'summary' => [
                'subtotal' => 0,
                'total_items' => 0,
                'item_count' => 0
            ]
        ];
    }
}

/**
 * Get cart count for a user (total number of items)
 * @param int $user_id User ID
 * @return int Total quantity of items in cart
 */
function getCartCount($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['count']);
        
    } catch (PDOException $e) {
        error_log('Error getting cart count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get cart item count (number of different products)
 * @param int $user_id User ID
 * @return int Number of different products in cart
 */
function getCartItemCount($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['count']);
        
    } catch (PDOException $e) {
        error_log('Error getting cart item count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Clear user's entire cart
 * @param int $user_id User ID
 * @return array Response with success status and message
 */
function clearCart($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);
        
        return [
            'success' => true,
            'message' => 'Cart cleared successfully',
            'items_removed' => $stmt->rowCount()
        ];
        
    } catch (PDOException $e) {
        error_log('Error clearing cart: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to clear cart'
        ];
    }
}

/**
 * Get cart total amount
 * @param int $user_id User ID
 * @return float Total cart amount
 */
function getCartTotal($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(product_price * quantity), 0) as total FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($result['total']);
        
    } catch (PDOException $e) {
        error_log('Error getting cart total: ' . $e->getMessage());
        return 0.0;
    }
}

/**
 * Check if product is in user's cart
 * @param int $user_id User ID
 * @param int $product_id Product ID
 * @return array Cart item details if exists, empty array otherwise
 */
function isInCart($user_id, $product_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT cart_id, quantity, product_price FROM cart WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$user_id, $product_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : [];
        
    } catch (PDOException $e) {
        error_log('Error checking cart status: ' . $e->getMessage());
        return [];
    }
}

/**
 * Validate cart items against current stock
 * @param int $user_id User ID
 * @return array Items that have stock issues
 */
function validateCartStock($user_id) {
    global $pdo;
    
    try {
        $sql = 'SELECT c.cart_id, c.product_id, c.quantity, p.name, p.stock
                FROM cart c
                LEFT JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = ? AND (p.stock < c.quantity OR p.stock IS NULL)';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error validating cart stock: ' . $e->getMessage());
        return [];
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
            $quantity = intval($_POST['quantity'] ?? $_GET['quantity'] ?? ($jsonData['quantity'] ?? 1));
            $admin_id = !empty($_POST['admin_id']) ? intval($_POST['admin_id']) : (!empty($jsonData['admin_id']) ? intval($jsonData['admin_id']) : null);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            if ($quantity <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid quantity'
                ]);
                break;
            }
            
            echo json_encode(addToCart($user_id, $session_id, $product_id, $quantity, $admin_id));
            break;
            
        case 'update_quantity':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please log in to manage your cart'
                ]);
                break;
            }
            
            $product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? $_GET['quantity'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode(updateCartQuantity($user_id, $product_id, $quantity));
            break;
            
        case 'remove':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please log in to manage your cart'
                ]);
                break;
            }
            
            $product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            echo json_encode(removeFromCart($user_id, $product_id));
            break;
            
        case 'get_cart':
            if (!$user_id) {
                echo json_encode([
                    'items' => [],
                    'summary' => [
                        'subtotal' => 0,
                        'total_items' => 0,
                        'item_count' => 0
                    ]
                ]);
                break;
            }
            
            echo json_encode(getUserCart($user_id));
            break;
            
        case 'check_status':
            if (!$user_id) {
                echo json_encode(['in_cart' => false]);
                break;
            }
            
            $product_id = intval($_GET['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            $cartItem = isInCart($user_id, $product_id);
            echo json_encode([
                'in_cart' => !empty($cartItem),
                'cart_item' => $cartItem,
                'product_id' => $product_id
            ]);
            break;
            
        case 'migrate':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'User must be logged in to migrate cart'
                ]);
                break;
            }
            
            echo json_encode(migrateAnonymousCart($session_id, $user_id));
            break;
            
        case 'get_count':
            if (!$user_id) {
                echo json_encode(['count' => 0]);
                break;
            }
            
            echo json_encode(['count' => getCartCount($user_id)]);
            break;
            
        case 'get_item_count':
            if (!$user_id) {
                echo json_encode(['count' => 0]);
                break;
            }
            
            echo json_encode(['count' => getCartItemCount($user_id)]);
            break;
            
        case 'get_total':
            if (!$user_id) {
                echo json_encode(['total' => 0]);
                break;
            }
            
            echo json_encode(['total' => getCartTotal($user_id)]);
            break;
            
        case 'clear':
            if (!$user_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please log in to manage your cart'
                ]);
                break;
            }
            
            echo json_encode(clearCart($user_id));
            break;
            
        case 'validate_stock':
            if (!$user_id) {
                echo json_encode([]);
                break;
            }
            
            echo json_encode(validateCartStock($user_id));
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
