<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/addtocart.php';
require_once __DIR__ . '/addtowishlist.php';

/**
 * Migrate all anonymous user data (cart and wishlist) to logged-in user
 * Call this function when a user successfully logs in
 * @param int $user_id User ID of the logged-in user
 * @return array Migration results
 */
function migrateAnonymousUserData($user_id) {
    $userInfo = getUserIdentifier();
    $session_id = $userInfo['session_id'];
    
    // Migrate cart
    $cartResult = migrateAnonymousCart($session_id, $user_id);
    
    // Migrate wishlist
    $wishlistResult = migrateAnonymousWishlist($session_id, $user_id);
    
    return [
        'success' => true,
        'cart' => $cartResult,
        'wishlist' => $wishlistResult,
        'message' => 'Anonymous user data migration completed'
    ];
}

// Handle AJAX request for migration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'migrate_user_data') {
    header('Content-Type: application/json');
    
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode([
            'success' => false,
            'message' => 'User must be logged in to migrate data'
        ]);
        exit;
    }
    
    echo json_encode(migrateAnonymousUserData($user_id));
    exit;
}
?>