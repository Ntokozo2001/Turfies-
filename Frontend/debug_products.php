<?php
// Include the backend product functions
require_once __DIR__ . '/../backend/product.php';

// Fetch all products from database
$products = getAllProducts();

// Debug: Output the products data
echo "Debug - Products fetched: " . count($products) . "<br>";
echo "Debug - Products array: <pre>";
var_dump($products);
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Products - Turfies Exam Care</title>
</head>
<body>
    <h1>Products Debug Page</h1>
    
    <?php if (!empty($products)): ?>
        <h2>Products Found: <?php echo count($products); ?></h2>
        <?php foreach ($products as $product): ?>
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>Price: R<?php echo number_format($product['price'], 2); ?></p>
                <p>Stock: <?php echo $product['stock']; ?></p>
                <p>Image: <?php echo htmlspecialchars($product['image_url']); ?></p>
                <p>Hover Image: <?php echo htmlspecialchars($product['hover_image_url']); ?></p>
                <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h2>No Products Available</h2>
        <p>We're currently updating our product catalog. Please check back soon!</p>
    <?php endif; ?>
</body>
</html>