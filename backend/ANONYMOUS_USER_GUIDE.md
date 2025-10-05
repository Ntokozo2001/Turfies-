# Anonymous User Support for Cart and Wishlist

This update enables anonymous (non-logged-in) users to add items to their cart and wishlist. When they later log in, their items will be automatically migrated to their user account.

## Database Changes Required

Run the SQL script `add_session_support.sql` on your database to add the necessary columns:

```sql
-- Add session_id column to wishlist table
ALTER TABLE wishlist ADD COLUMN session_id VARCHAR(128) NULL AFTER user_id;

-- Add session_id column to cart table  
ALTER TABLE cart ADD COLUMN session_id VARCHAR(128) NULL AFTER user_id;

-- Add indexes for better performance
CREATE INDEX idx_wishlist_session_id ON wishlist(session_id);
CREATE INDEX idx_cart_session_id ON cart(session_id);
CREATE INDEX idx_wishlist_session_user ON wishlist(session_id, user_id);
CREATE INDEX idx_cart_session_user ON cart(session_id, user_id);
```

## How It Works

1. **Anonymous Users**: Items are stored using the PHP session ID
2. **Logged-in Users**: Items are stored using the user ID
3. **Migration**: When anonymous users log in, their items are automatically migrated

## Updated Backend Functions

### Cart Functions (`addtocart.php`)

All cart functions now support both logged-in and anonymous users:

- `addToCart($user_id, $session_id, $product_id, $quantity, $admin_id)`
- `getUserCart($user_id, $session_id)`
- `getCartCount($user_id, $session_id)`
- `removeFromCart($user_id, $session_id, $product_id)`
- `clearCart($user_id, $session_id)`

### Wishlist Functions (`addtowishlist.php`)

All wishlist functions now support both logged-in and anonymous users:

- `addToWishlist($user_id, $session_id, $product_id, $admin_id)`
- `getUserWishlist($user_id, $session_id)`
- `getWishlistCount($user_id, $session_id)`
- `removeFromWishlist($user_id, $session_id, $product_id)`
- `clearWishlist($user_id, $session_id)`

### Migration Functions

- `migrateAnonymousCart($session_id, $user_id)` - Migrates cart items
- `migrateAnonymousWishlist($session_id, $user_id)` - Migrates wishlist items
- `migrateAnonymousUserData($user_id)` - Migrates both cart and wishlist

## Frontend Usage

### Adding Items (Works for Both Anonymous and Logged-in Users)

```javascript
// Add to cart
fetch('backend/addtocart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=add&product_id=1&quantity=2'
});

// Add to wishlist
fetch('backend/addtowishlist.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=add&product_id=1'
});
```

### Getting Items

```javascript
// Get cart items
fetch('backend/addtocart.php?action=get_cart')
    .then(response => response.json())
    .then(data => console.log(data));

// Get wishlist items
fetch('backend/addtowishlist.php?action=get_wishlist')
    .then(response => response.json())
    .then(data => console.log(data));
```

### Migration After Login

Add this to your login success handler:

```javascript
// After successful login, migrate anonymous data
fetch('backend/migrate_anonymous_data.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=migrate_user_data'
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Data migrated:', data);
        // Refresh cart and wishlist displays
        updateCartDisplay();
        updateWishlistDisplay();
    }
});
```

### Automatic Migration

You can also add automatic migration to your login PHP script:

```php
// After successful login validation
if ($login_successful) {
    $_SESSION['user_id'] = $user_id;
    
    // Migrate anonymous data
    require_once 'backend/migrate_anonymous_data.php';
    $migration_result = migrateAnonymousUserData($user_id);
    
    // Return login success with migration info
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'migration' => $migration_result
    ]);
}
```

## Key Features

1. **Seamless Experience**: Anonymous users can shop without creating an account
2. **Data Persistence**: Items are preserved when users log in
3. **Duplicate Handling**: Prevents duplicate items when migrating
4. **Stock Validation**: Maintains stock validation for all users
5. **Session Security**: Uses PHP session management for anonymous users
6. **Automatic Cleanup**: Removes anonymous data after migration

## Benefits

- **Improved User Experience**: Users don't lose items when they decide to create an account
- **Higher Conversion Rates**: Reduces friction in the shopping process
- **Better Analytics**: Track shopping behavior across anonymous and registered users
- **Flexible Shopping**: Users can shop first, register later

## AJAX Endpoints

Both `addtocart.php` and `addtowishlist.php` support these actions:

- `add` - Add item
- `remove` - Remove item
- `get_cart` / `get_wishlist` - Get all items
- `get_count` - Get item count
- `check_status` - Check if item exists
- `clear` - Clear all items
- `migrate` - Migrate anonymous data (requires login)

All endpoints automatically handle both anonymous and logged-in users transparently.