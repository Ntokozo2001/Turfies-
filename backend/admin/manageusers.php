<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

// Function to get all users from the database
function getAllUsers($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT user_id, full_name, email, school, created_at FROM users ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching users: ' . $e->getMessage());
        return false;
    }
}

// Function to delete a user
function deleteUser($pdo, $userId) {
    try {
        $pdo->beginTransaction();
        
        // Delete related records first (cart, wishlist, orders if they exist)
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$userId]);
        
        $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ?');
        $stmt->execute([$userId]);
        
        // Delete the user
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log('Error deleting user: ' . $e->getMessage());
        return false;
    }
}

// Function to get a single user for editing
function getUserById($pdo, $userId) {
    try {
        $stmt = $pdo->prepare('SELECT user_id, full_name, email, school, whatsapp_number, created_at FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching user: ' . $e->getMessage());
        return false;
    }
}

// Function to update user information
function updateUser($pdo, $userId, $fullName, $email, $school, $whatsapp) {
    try {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ?');
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email is already taken by another user'];
        }
        
        // Check if whatsapp is already taken by another user
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE whatsapp_number = ? AND user_id != ?');
        $stmt->execute([$whatsapp, $userId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'WhatsApp number is already taken by another user'];
        }
        
        $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, school = ?, whatsapp_number = ?, updated_at = ? WHERE user_id = ?');
        $result = $stmt->execute([$fullName, $email, $school, $whatsapp, date('Y-m-d H:i:s'), $userId]);
        
        return ['success' => $result, 'message' => $result ? 'User updated successfully' : 'Failed to update user'];
    } catch (PDOException $e) {
        error_log('Error updating user: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'list':
        $users = getAllUsers($pdo);
        if ($users !== false) {
            echo json_encode(['success' => true, 'users' => $users]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch users']);
        }
        break;
        
    case 'delete':
        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            break;
        }
        
        $result = deleteUser($pdo, $userId);
        echo json_encode(['success' => $result, 'message' => $result ? 'User deleted successfully' : 'Failed to delete user']);
        break;
        
    case 'get':
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            break;
        }
        
        $user = getUserById($pdo, $userId);
        if ($user !== false) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        break;
        
    case 'update':
        $userId = $_POST['user_id'] ?? null;
        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $school = trim($_POST['school'] ?? '');
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        
        if (!$userId || !$fullName || !$email || !$school || !$whatsapp) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            break;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            break;
        }
        
        // Validate WhatsApp number
        if (!preg_match('/^[0-9]{7,15}$/', $whatsapp)) {
            echo json_encode(['success' => false, 'message' => 'Invalid WhatsApp number format']);
            break;
        }
        
        $result = updateUser($pdo, $userId, $fullName, $email, $school, $whatsapp);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
