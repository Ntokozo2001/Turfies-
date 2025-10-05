<?php
session_start();
require_once __DIR__ . '/../db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Test database connection
try {
    $testStmt = $pdo->query("SELECT 1");
    error_log("Database connection successful for manage requests");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Function to get all help requests with pagination and filters
function getHelpRequests($pdo, $limit = 20, $offset = 0, $filters = []) {
    try {
        $sql = "SELECT hr.request_id, hr.user_id, hr.subject, hr.guest_name, hr.guest_email, 
                       hr.whatsapp_contact, hr.message, hr.status, hr.created_at,
                       u.full_name as user_name
                FROM help_request hr 
                LEFT JOIN users u ON hr.user_id = u.user_id";
        
        $whereConditions = [];
        $params = [];
        
        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "hr.status = ?";
            $params[] = $filters['status'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $whereConditions[] = "(hr.subject LIKE ? OR hr.message LIKE ? OR hr.guest_name LIKE ? OR hr.guest_email LIKE ? OR u.full_name LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Date range filters
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(hr.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(hr.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Add WHERE clause if conditions exist
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $sql .= " ORDER BY 
                    CASE hr.status 
                        WHEN 'new' THEN 1 
                        WHEN 'viewed' THEN 2 
                        WHEN 'assisted' THEN 3 
                        ELSE 4 
                    END,
                    hr.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        error_log("Help requests query: " . $sql);
        error_log("Help requests params: " . json_encode($params));
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching help requests: ' . $e->getMessage());
        return false;
    }
}

// Function to get total count of help requests with filters
function getTotalRequestsCount($pdo, $filters = []) {
    try {
        $sql = "SELECT COUNT(*) as total FROM help_request hr LEFT JOIN users u ON hr.user_id = u.user_id";
        $whereConditions = [];
        $params = [];
        
        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "hr.status = ?";
            $params[] = $filters['status'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $whereConditions[] = "(hr.subject LIKE ? OR hr.message LIKE ? OR hr.guest_name LIKE ? OR hr.guest_email LIKE ? OR u.full_name LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Date range filters
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(hr.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(hr.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Add WHERE clause if conditions exist
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log('Error getting total requests count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get a single help request by ID
function getHelpRequestById($pdo, $requestId) {
    try {
        $sql = "SELECT hr.request_id, hr.user_id, hr.subject, hr.guest_name, hr.guest_email, 
                       hr.whatsapp_contact, hr.message, hr.status, hr.created_at,
                       u.full_name as user_name, u.email as user_email
                FROM help_request hr 
                LEFT JOIN users u ON hr.user_id = u.user_id
                WHERE hr.request_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$requestId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false) {
            return null;
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log('Error fetching help request: ' . $e->getMessage());
        return false;
    }
}

// Function to update help request status
function updateRequestStatus($pdo, $requestId, $status) {
    try {
        $allowedStatuses = ['new', 'viewed', 'assisted'];
        if (!in_array($status, $allowedStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $stmt = $pdo->prepare('UPDATE help_request SET status = ? WHERE request_id = ?');
        $result = $stmt->execute([$status, $requestId]);
        
        return ['success' => $result, 'message' => $result ? 'Status updated successfully' : 'Failed to update status'];
    } catch (PDOException $e) {
        error_log('Error updating request status: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Function to delete a help request
function deleteHelpRequest($pdo, $requestId) {
    try {
        $stmt = $pdo->prepare('DELETE FROM help_request WHERE request_id = ?');
        $result = $stmt->execute([$requestId]);
        
        return ['success' => $result, 'message' => $result ? 'Request deleted successfully' : 'Failed to delete request'];
    } catch (PDOException $e) {
        error_log('Error deleting help request: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Function to get request statistics
function getRequestStats($pdo) {
    try {
        $stats = [
            'total' => 0,
            'new' => 0,
            'viewed' => 0,
            'assisted' => 0
        ];
        
        // Get total count
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM help_request');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total'] = $result['total'];
        
        // Get count by status
        $stmt = $pdo->prepare('SELECT status, COUNT(*) as count FROM help_request GROUP BY status');
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = $row['count'];
            }
        }
        
        return $stats;
    } catch (PDOException $e) {
        error_log('Error getting request stats: ' . $e->getMessage());
        return ['total' => 0, 'new' => 0, 'viewed' => 0, 'assisted' => 0];
    }
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'stats':
        $stats = getRequestStats($pdo);
        echo json_encode(['success' => true, 'stats' => $stats]);
        break;
        
    case 'list':
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $offset = ($page - 1) * $limit;
        
        // Collect filters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });
        
        error_log("Request filters: " . json_encode($filters));
        
        $requests = getHelpRequests($pdo, $limit, $offset, $filters);
        $totalCount = getTotalRequestsCount($pdo, $filters);
        $totalPages = ceil($totalCount / $limit);
        
        if ($requests !== false) {
            echo json_encode([
                'success' => true, 
                'requests' => $requests,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_count' => $totalCount,
                    'limit' => $limit
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch requests']);
        }
        break;
        
    case 'get':
        $requestId = $_GET['request_id'] ?? null;
        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            break;
        }
        
        error_log("Fetching request with ID: " . $requestId);
        
        $request = getHelpRequestById($pdo, $requestId);
        if ($request === false) {
            error_log("Database error when fetching request ID: " . $requestId);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        } elseif ($request === null) {
            error_log("Request not found with ID: " . $requestId);
            echo json_encode(['success' => false, 'message' => 'Request not found']);
        } else {
            error_log("Successfully fetched request: " . json_encode($request));
            echo json_encode(['success' => true, 'request' => $request]);
        }
        break;
        
    case 'update_status':
        $requestId = $_POST['request_id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$requestId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Request ID and status are required']);
            break;
        }
        
        $result = updateRequestStatus($pdo, $requestId, $status);
        echo json_encode($result);
        break;
        
    case 'delete':
        $requestId = $_POST['request_id'] ?? null;
        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            break;
        }
        
        $result = deleteHelpRequest($pdo, $requestId);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>