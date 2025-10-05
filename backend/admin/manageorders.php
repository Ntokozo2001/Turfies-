<?php
session_start();
require_once __DIR__ . '/../db.php';

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
try {
    $testStmt = $pdo->query("SELECT 1");
    error_log("Database connection successful for order management");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Function to get order statistics
function getOrderStats($pdo) {
    try {
        $stats = ['total' => 0, 'pending' => 0, 'processing' => 0, 'shipped' => 0, 'completed' => 0, 'cancelled' => 0];
        
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM orders');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total'] = $result['total'] ?? 0;

        $stmt = $pdo->prepare('SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status');
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $status = $row['order_status'] ?? 'pending';
            if (isset($stats[$status])) {
                $stats[$status] = $row['count'];
            }
        }

        return $stats;
    } catch (PDOException $e) {
        error_log('Error getting order stats: ' . $e->getMessage());
        return ['total' => 0, 'pending' => 0, 'processing' => 0, 'shipped' => 0, 'completed' => 0, 'cancelled' => 0];
    }
}

// Function to get orders with pagination and filters
function getOrders($pdo, $page = 1, $limit = 20, $filters = []) {
    try {
        error_log("getOrders called with page=$page, limit=$limit, filters=" . json_encode($filters));
        $offset = ($page - 1) * $limit;

        // Build base query
        $baseQuery = "FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.user_id 
                      LEFT JOIN product p ON o.product_id = p.product_id";

        $whereConditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $whereConditions[] = "o.order_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(o.order_id LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR p.name LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Get total count
        $countQuery = "SELECT COUNT(*) as total " . $baseQuery . " " . $whereClause;
        error_log("Count query: " . $countQuery);
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        error_log("Total count: " . $totalCount);

        // Get orders
        $query = "SELECT o.order_id, o.user_id, o.admin_id, o.product_id, o.product_price, o.quantity, 
                         o.total_amount, o.order_status, o.created_at, o.updated_at,
                         COALESCE(u.full_name, 'Guest Customer') as customer_name,
                         COALESCE(u.email, 'No email') as customer_email,
                         'No phone' as customer_phone,
                         COALESCE(p.name, 'Unknown Product') as product_name,
                         '' as product_description
                  " . $baseQuery . " " . $whereClause . "
                  ORDER BY o.created_at DESC 
                  LIMIT ? OFFSET ?";

        $allParams = array_merge($params, [$limit, $offset]);
        error_log("Final query: " . $query);
        $stmt = $pdo->prepare($query);
        $stmt->execute($allParams);

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($orders) . " orders");

        $formattedOrders = [];
        foreach ($orders as $row) {
            $formattedOrders[] = [
                'order_id' => $row['order_id'],
                'user_id' => $row['user_id'],
                'admin_id' => $row['admin_id'],
                'product_id' => $row['product_id'],
                'product_price' => $row['product_price'] ?? 0,
                'quantity' => $row['quantity'] ?? 1,
                'total_amount' => $row['total_amount'] ?? 0,
                'order_status' => $row['order_status'] ?? 'pending',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'] ?? $row['created_at'],
                'delivered_at' => null,
                'customer_name' => $row['customer_name'],
                'customer_email' => $row['customer_email'],
                'customer_phone' => $row['customer_phone'],
                'product_name' => $row['product_name'],
                'product_description' => $row['product_description']
            ];
        }

        $totalPages = ceil($totalCount / $limit);

        return [
            'orders' => $formattedOrders,
            'pagination' => [
                'current_page' => (int)$page,
                'total_pages' => (int)$totalPages,
                'total_count' => (int)$totalCount,
                'limit' => (int)$limit
            ]
        ];

    } catch (PDOException $e) {
        error_log("Error getting orders: " . $e->getMessage());
        throw new Exception("Error retrieving orders: " . $e->getMessage());
    }
}

// Function to get a single order
function getOrder($pdo, $orderId) {
    try {
        $query = "SELECT o.order_id, o.user_id, o.admin_id, o.product_id, o.product_price, o.quantity, 
                         o.total_amount, o.order_status, o.created_at, o.updated_at,
                         COALESCE(u.full_name, 'Guest Customer') as customer_name,
                         COALESCE(u.email, 'No email') as customer_email,
                         'No phone' as customer_phone,
                         COALESCE(p.name, 'Unknown Product') as product_name,
                         '' as product_description
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.user_id 
                  LEFT JOIN product p ON o.product_id = p.product_id
                  WHERE o.order_id = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'order_id' => $row['order_id'],
                'user_id' => $row['user_id'],
                'admin_id' => $row['admin_id'],
                'product_id' => $row['product_id'],
                'product_price' => $row['product_price'] ?? 0,
                'quantity' => $row['quantity'] ?? 1,
                'total_amount' => $row['total_amount'] ?? 0,
                'order_status' => $row['order_status'] ?? 'pending',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'] ?? $row['created_at'],
                'delivered_at' => null,
                'customer_name' => $row['customer_name'],
                'customer_email' => $row['customer_email'],
                'customer_phone' => $row['customer_phone'],
                'product_name' => $row['product_name'],
                'product_description' => $row['product_description']
            ];
        }

        return null;

    } catch (PDOException $e) {
        error_log("Error getting order: " . $e->getMessage());
        throw new Exception("Error retrieving order: " . $e->getMessage());
    }
}

// Function to update order status
function updateOrderStatus($pdo, $orderId, $status) {
    try {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status provided");
        }

        $query = "UPDATE orders SET order_status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$status, $orderId]);

        if ($result && $stmt->rowCount() > 0) {
            return true;
        } else {
            throw new Exception("Order not found or status unchanged");
        }

    } catch (PDOException $e) {
        error_log("Error updating order status: " . $e->getMessage());
        throw new Exception("Error updating order status: " . $e->getMessage());
    }
}

// Function to delete an order
function deleteOrder($pdo, $orderId) {
    try {
        $checkQuery = "SELECT order_id FROM orders WHERE order_id = ?";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$orderId]);

        if (!$checkStmt->fetch()) {
            throw new Exception("Order not found");
        }

        $query = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$orderId]);

        if ($result && $stmt->rowCount() > 0) {
            return true;
        } else {
            throw new Exception("Order could not be deleted");
        }

    } catch (PDOException $e) {
        error_log("Error deleting order: " . $e->getMessage());
        throw new Exception("Error deleting order: " . $e->getMessage());
    }
}

// Handle requests
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    error_log("Order management request - Method: $method, Action: $action");

    switch ($action) {
        case 'test':
            echo json_encode(['success' => true, 'message' => 'Backend is working', 'method' => $method, 'action' => $action]);
            break;
            
        case 'stats':
            if ($method === 'GET') {
                $stats = getOrderStats($pdo);
                echo json_encode(['success' => true, 'stats' => $stats]);
            } else {
                throw new Exception("Invalid request method for stats");
            }
            break;
            
        case 'list':
            if ($method === 'GET') {
                error_log("Processing 'list' action");
                $page = (int)($_GET['page'] ?? 1);
                $limit = (int)($_GET['limit'] ?? 20);
                
                $filters = [
                    'status' => $_GET['status'] ?? '',
                    'search' => $_GET['search'] ?? '',
                    'date_from' => $_GET['date_from'] ?? '',
                    'date_to' => $_GET['date_to'] ?? ''
                ];
                
                $filters = array_filter($filters, function($value) {
                    return !empty($value);
                });
                
                error_log("Calling getOrders with page=$page, limit=$limit, filters=" . json_encode($filters));
                $result = getOrders($pdo, $page, $limit, $filters);
                
                if ($result === null) {
                    error_log("getOrders returned null");
                    throw new Exception("Failed to retrieve orders");
                }
                
                error_log("getOrders returned: " . json_encode($result));
                
                echo json_encode([
                    'success' => true,
                    'orders' => $result['orders'],
                    'pagination' => $result['pagination']
                ]);
            } else {
                throw new Exception("Invalid request method for listing orders");
            }
            break;
            
        case 'get':
            if ($method === 'GET') {
                $orderId = (int)($_GET['order_id'] ?? 0);
                
                if ($orderId <= 0) {
                    throw new Exception("Valid order ID is required");
                }
                
                $order = getOrder($pdo, $orderId);
                
                if ($order) {
                    echo json_encode([
                        'success' => true,
                        'order' => $order
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Order not found'
                    ]);
                }
            } else {
                throw new Exception("Invalid request method for getting order");
            }
            break;
            
        case 'update_status':
            if ($method === 'POST') {
                $orderId = (int)($_POST['order_id'] ?? 0);
                $status = $_POST['status'] ?? '';
                
                if ($orderId <= 0) {
                    throw new Exception("Valid order ID is required");
                }
                
                if (empty($status)) {
                    throw new Exception("Status is required");
                }
                
                if (updateOrderStatus($pdo, $orderId, $status)) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Order status updated to '" . ucfirst($status) . "' successfully"
                    ]);
                } else {
                    throw new Exception("Failed to update order status");
                }
            } else {
                throw new Exception("Invalid request method for updating status");
            }
            break;
            
        case 'delete':
            if ($method === 'POST') {
                $orderId = (int)($_POST['order_id'] ?? 0);
                
                if ($orderId <= 0) {
                    throw new Exception("Valid order ID is required");
                }
                
                if (deleteOrder($pdo, $orderId)) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Order #$orderId deleted successfully"
                    ]);
                } else {
                    throw new Exception("Failed to delete order");
                }
            } else {
                throw new Exception("Invalid request method for deleting order");
            }
            break;
            
        default:
            throw new Exception("Invalid or missing action parameter");
    }

} catch (Exception $e) {
    error_log("Order management error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>