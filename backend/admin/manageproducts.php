<?php
session_start();
require_once __DIR__ . '/../db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Test database connection
try {
    $testStmt = $pdo->query("SELECT 1");
    error_log("Database connection successful");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Function to get all products from the database
function getAllProducts($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM product ORDER BY created_at DESC');
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($products)) {
            error_log("Product table columns: " . implode(', ', array_keys($products[0])));
        }
        
        return $products;
    } catch (PDOException $e) {
        error_log('Error fetching products: ' . $e->getMessage());
        return false;
    }
}

// Function to get a single product for editing
function getProductById($pdo, $productId) {
    try {
        // First, let's check what columns actually exist
        $stmt = $pdo->prepare('SELECT * FROM product WHERE product_id = ? LIMIT 1');
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false) {
            // No product found
            error_log("No product found with ID: " . $productId);
            return null;
        }
        
        error_log("Product columns available: " . implode(', ', array_keys($result)));
        return $result;
    } catch (PDOException $e) {
        error_log('Error fetching product: ' . $e->getMessage());
        return false;
    }
}

// Function to create a new product
function createProduct($pdo, $name, $description, $price, $stock, $imageUrl = '', $hoverImageUrl = '') {
    try {
        // Check if product name already exists
        $stmt = $pdo->prepare('SELECT product_id FROM product WHERE name = ?');
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Product with this name already exists'];
        }

        $stmt = $pdo->prepare('INSERT INTO product (name, description, price, stock, image_url, hover_image_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $now = date('Y-m-d H:i:s');
        $result = $stmt->execute([$name, $description, $price, $stock, $imageUrl, $hoverImageUrl, $now, $now]);
        
        return ['success' => $result, 'message' => $result ? 'Product created successfully' : 'Failed to create product', 'product_id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        error_log('Error creating product: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Function to update product information
function updateProduct($pdo, $productId, $name, $description, $price, $stock, $imageUrl = '', $hoverImageUrl = '') {
    try {
        // Check if another product has the same name
        $stmt = $pdo->prepare('SELECT product_id FROM product WHERE name = ? AND product_id != ?');
        $stmt->execute([$name, $productId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Another product with this name already exists'];
        }
        
        $stmt = $pdo->prepare('UPDATE product SET name = ?, description = ?, price = ?, stock = ?, image_url = ?, hover_image_url = ?, updated_at = ? WHERE product_id = ?');
        $result = $stmt->execute([$name, $description, $price, $stock, $imageUrl, $hoverImageUrl, date('Y-m-d H:i:s'), $productId]);
        
        return ['success' => $result, 'message' => $result ? 'Product updated successfully' : 'Failed to update product'];
    } catch (PDOException $e) {
        error_log('Error updating product: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Function to soft delete a product
function deleteProduct($pdo, $productId) {
    try {
        $pdo->beginTransaction();
        
        // Remove from cart and wishlist first
        $stmt = $pdo->prepare('DELETE FROM cart WHERE product_id = ?');
        $stmt->execute([$productId]);
        
        $stmt = $pdo->prepare('DELETE FROM wishlist WHERE product_id = ?');
        $stmt->execute([$productId]);
        
        // Delete the product
        $stmt = $pdo->prepare('DELETE FROM product WHERE product_id = ?');
        $stmt->execute([$productId]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Product deleted successfully'];
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log('Error deleting product: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to delete product'];
    }
}

// Function to handle file upload for product images
function handleImageUpload($file, $uploadDir = '../../Frontend/assets/images/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed'];
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB'];
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Return relative path for database storage
        return ['success' => true, 'filename' => 'assets/images/' . $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

// Function to get product categories (for dropdown)
function getProductCategories($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != "" AND deleted_at IS NULL ORDER BY category');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log('Error fetching categories: ' . $e->getMessage());
        return ['Exam Package', 'Study Material', 'Tutorial', 'Other']; // Default categories
    }
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'list':
        $products = getAllProducts($pdo);
        if ($products !== false) {
            echo json_encode(['success' => true, 'products' => $products]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch products']);
        }
        break;
        
    case 'get':
        $productId = $_GET['product_id'] ?? null;
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            break;
        }
        
        // Debug logging
        error_log("Fetching product with ID: " . $productId);
        
        $product = getProductById($pdo, $productId);
        if ($product === false) {
            error_log("Database error when fetching product ID: " . $productId);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        } elseif ($product === null) {
            error_log("Product not found with ID: " . $productId);
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        } else {
            error_log("Successfully fetched product: " . json_encode($product));
            echo json_encode(['success' => true, 'product' => $product]);
        }
        break;
        
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        
        if (!$name || !$description || $price <= 0 || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid']);
            break;
        }
        
        // Handle image uploads
        $imageUrl = '';
        $hoverImageUrl = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUpload = handleImageUpload($_FILES['image']);
            if ($imageUpload['success']) {
                $imageUrl = $imageUpload['filename'];
            } else {
                echo json_encode(['success' => false, 'message' => 'Image upload error: ' . $imageUpload['message']]);
                break;
            }
        }
        
        if (isset($_FILES['hover_image']) && $_FILES['hover_image']['error'] === UPLOAD_ERR_OK) {
            $hoverImageUpload = handleImageUpload($_FILES['hover_image']);
            if ($hoverImageUpload['success']) {
                $hoverImageUrl = $hoverImageUpload['filename'];
            }
        }
        
        $result = createProduct($pdo, $name, $description, $price, $stock, $imageUrl, $hoverImageUrl);
        echo json_encode($result);
        break;
        
    case 'update':
        $productId = $_POST['product_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        
        if (!$productId || !$name || !$description || $price <= 0 || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid']);
            break;
        }
        
        // Get current product data
        $currentProduct = getProductById($pdo, $productId);
        if ($currentProduct === false) {
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            break;
        } elseif ($currentProduct === null) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            break;
        }
        
        // Use existing images as default
        $imageUrl = $currentProduct['image_url'];
        $hoverImageUrl = $currentProduct['hover_image_url'];
        
        // Handle new image uploads
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUpload = handleImageUpload($_FILES['image']);
            if ($imageUpload['success']) {
                $imageUrl = $imageUpload['filename'];
            } else {
                echo json_encode(['success' => false, 'message' => 'Image upload error: ' . $imageUpload['message']]);
                break;
            }
        }
        
        if (isset($_FILES['hover_image']) && $_FILES['hover_image']['error'] === UPLOAD_ERR_OK) {
            $hoverImageUpload = handleImageUpload($_FILES['hover_image']);
            if ($hoverImageUpload['success']) {
                $hoverImageUrl = $hoverImageUpload['filename'];
            }
        }
        
        $result = updateProduct($pdo, $productId, $name, $description, $price, $stock, $imageUrl, $hoverImageUrl);
        echo json_encode($result);
        break;
        
    case 'delete':
        $productId = $_POST['product_id'] ?? null;
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            break;
        }
        
        $result = deleteProduct($pdo, $productId);
        echo json_encode($result);
        break;
        
    case 'categories':
        $categories = getProductCategories($pdo);
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
