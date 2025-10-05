<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the search functions
require_once __DIR__ . '/../backend/search.php';
require_once __DIR__ . '/../backend/product.php';

// Handle search request
$query = $_GET['q'] ?? '';

// Perform search
$search_results = [];
$total_results = 0;
if (!empty($query)) {
    $search_results = searchProducts($query);
    $total_results = countSearchResults($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Turfies Exam Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { overflow-x: hidden; }
        .search-results-container { padding: 40px 0; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        .product-card { background: #fff; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(169,123,224,0.1); transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(169,123,224,0.15); }
        .no-results { text-align: center; padding: 60px 20px; color: #666; }
        .search-info { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>

    <div class="search-results-container">
        <div class="container">
            <?php if (!empty($query)): ?>
                <div class="search-info">
                    <h2 style="color: #a97be0; margin-bottom: 10px;">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
                    <p class="text-muted mb-0"><?php echo $total_results; ?> product<?php echo $total_results !== 1 ? 's' : ''; ?> found</p>
                </div>

                <?php if (!empty($search_results)): ?>
                    <div class="product-grid">
                        <?php foreach ($search_results as $product): ?>
                            <div class="product-card">
                                <div class="product-image" style="text-align: center; margin-bottom: 15px;">
                                    <?php 
                                    $mainImage = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/images/placeholder-product.jpg';
                                    ?>
                                    <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; max-width: 200px; height: 200px; object-fit: cover; border-radius: 10px;">
                                </div>
                                
                                <h5 style="color: #3d2176; text-align: center; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h5>
                                
                                <p class="text-muted" style="font-size: 0.9rem; text-align: center; margin-bottom: 15px;">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?>
                                </p>
                                
                                <div class="product-info" style="text-align: center;">
                                    <div class="price" style="font-size: 1.3rem; font-weight: bold; color: #a97be0; margin-bottom: 8px;">
                                        <?php echo formatPrice($product['price']); ?>
                                    </div>
                                    
                                    <div class="stock-status" style="font-size: 0.9rem; margin-bottom: 15px; color: <?php echo getStockStatusClass($product['stock']) === 'stock-good' ? '#28a745' : (getStockStatusClass($product['stock']) === 'stock-low' ? '#ffc107' : '#dc3545'); ?>;">
                                        <?php echo getStockStatus($product['stock']); ?>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <a href="product-detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary" style="background: #a97be0; border: none; margin-right: 8px; border-radius: 8px;">View Details</a>
                                        <?php if (isInStock($product['stock'])): ?>
                                            <button class="btn btn-outline-primary" style="border-color: #a97be0; color: #a97be0; border-radius: 8px;" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled style="border-radius: 8px;">Out of Stock</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <h4>No products found</h4>
                        <p>Sorry, we couldn't find any products matching "<?php echo htmlspecialchars($query); ?>"</p>
                        <p><a href="product.php" style="color: #a97be0;">Browse all products</a> or try a different search term.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <h4>Please enter a search term</h4>
                    <p><a href="product.php" style="color: #a97be0;">Browse all products</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        // Add to cart function (placeholder)
        function addToCart(productId) {
            alert(`Product ${productId} added to cart! (This is a placeholder function)`);
        }

        // Highlight search term in results
        document.addEventListener('DOMContentLoaded', function() {
            const query = '<?php echo htmlspecialchars($query); ?>';
            if (query) {
                const productNames = document.querySelectorAll('.product-card h5');
                productNames.forEach(name => {
                    const regex = new RegExp(`(${query})`, 'gi');
                    name.innerHTML = name.innerHTML.replace(regex, '<mark style="background: #fff3cd; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                });
            }
        });
    </script>
</body>
</html>