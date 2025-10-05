<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the backend product functions
require_once __DIR__ . '/../backend/product.php';

// Fetch all products with details from database
$products = getAllProductsWithDetails();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Turfies Exam Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include 'navbar.php'; ?>
    </header>
    <main class="products-section" style="display:flex;gap:36px;justify-content:center;flex-wrap:wrap;padding:40px 0;">
        <h1 style="text-align:center;margin-bottom:40px;color:#a97be0;width:100%;">Our Products</h1>
        
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" style="width:320px;height:500px;position:relative;box-shadow:0 2px 12px rgba(169,123,224,0.08);border-radius:18px;overflow:hidden;background:#fff;" data-product-id="<?php echo $product['product_id']; ?>">
                    <div class="favorite wishlist-btn" 
                         style="position:absolute;top:18px;right:18px;z-index:2;font-size:2rem;color:#a97be0;cursor:pointer;transition:color 0.3s;" 
                         data-product-id="<?php echo $product['product_id']; ?>" 
                         title="Add to Wishlist">&#9825;</div>
                    
                    <div class="product-img-wrap" style="position:relative;width:100%;height:380px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <?php 
                        $mainImage = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/images/placeholder-product.jpg';
                        $hoverImage = getHoverImageUrl($product['hover_image_url'], $product['image_url']);
                        ?>
                        <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:100%;object-fit:cover;display:block;transition:opacity 0.3s;">
                        <img src="<?php echo htmlspecialchars($hoverImage); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> Hover" class="hover-img" style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;position:absolute;top:0;left:0;right:0;bottom:0;margin:auto;opacity:0;transition:opacity 0.3s;background:#fff;">
                    </div>
                    
                    <div class="product-name" style="text-align:center;font-size:1.1rem;font-weight:700;color:#3d2176;margin-top:8px;">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </div>
                    
                    <div class="product-price" style="text-align:center;font-size:1.2rem;font-weight:600;color:#a97be0;margin:10px 0;">
                        <?php echo formatPrice($product['price']); ?>
                    </div>
                    
                    <!-- Show stock status if low or out of stock -->
                    <?php if ($product['stock'] <= 10): ?>
                        <div style="text-align:center;font-size:0.9rem;margin-bottom:10px;color:<?php echo $product['stock'] <= 0 ? '#e74c3c' : '#f39c12'; ?>;">
                            <?php echo getStockStatus($product['stock']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-actions" style="display:flex;justify-content:center;gap:12px;margin-top:18px;">
                        <button class="view-btn" style="background:#a97be0;color:#fff;padding:10px 24px;border:none;border-radius:7px;font-size:1rem;cursor:pointer;">View Product</button>
                        <button class="cart-btn add-to-cart-btn" 
                                style="background:#fff;color:#a97be0;padding:8px 10px;border:1.5px solid #a97be0;border-radius:7px;font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;width:40px;height:40px;transition:all 0.3s;" 
                                data-product-id="<?php echo $product['product_id']; ?>"
                                title="Add to Cart"
                                <?php echo !isInStock($product['stock']) ? 'disabled title="Out of Stock"' : ''; ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#a97be0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center;width:100%;padding:60px 20px;color:#666;">
                <h3>No Products Available</h3>
                <p>We're currently updating our product catalog. Please check back soon!</p>
            </div>
        <?php endif; ?>
    </main>
    <style>
    /* Hover effect for product images */
    .product-card .hover-img {
        opacity: 0;
        pointer-events: none;
        background: #fff;
    }
    .product-card.active .hover-img,
    .product-card:hover .hover-img {
        opacity: 1 !important;
        pointer-events: auto;
    }
    .product-card.active img:not(.hover-img),
    .product-card:hover img:not(.hover-img) {
        opacity: 0.15;
    }
    .product-card .hover-img,
    .product-card img:not(.hover-img) {
        transition: opacity 0.3s;
    }
    /* Modal styles */
    .product-modal-bg {
        position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:1000;display:flex;align-items:center;justify-content:center;
    }
    .product-modal {
        background:#fff;
        border-radius:0;
        max-width:none;
        width:100vw;
        height:100vh;
        padding:40px 0 0 0;
        box-shadow:none;
        position:fixed;
        top:0; left:0;
        z-index:2000;
        animation:fadeIn 0.2s;
        min-height:100vh;
        overflow-y:auto;
        display:flex;
        flex-direction:column;
        align-items:center;
    }
    .product-modal img {
        width:100%;
        max-width:320px;
        max-height:140px;
        object-fit:contain;
        border-radius:10px;
        margin-bottom:14px;
        display:block;
        margin-left:auto;
        margin-right:auto;
    }
    .product-modal .close-modal {
        position:absolute;top:10px;right:16px;background:none;border:none;font-size:2rem;color:#a97be0;cursor:pointer;
    }
    @keyframes fadeIn {
        from { opacity:0; transform:scale(0.96);}
        to { opacity:1; transform:scale(1);}
    }
    /* Button hover effects */
    .cart-btn:hover:not(:disabled) {
        background:#a97be0 !important;
        color:#fff !important;
        transform:scale(1.05);
    }
    .cart-btn:hover:not(:disabled) svg {
        stroke:#fff !important;
    }
    .wishlist-btn:hover {
        transform:scale(1.1);
        color:#e74c3c !important;
    }
    .modal-cart-btn:hover:not(:disabled) {
        background:#8b5fc7 !important;
        transform:translateY(-2px);
    }
    .modal-wishlist-btn:hover:not(:disabled) {
        background:#a97be0 !important;
        color:#fff !important;
        transform:translateY(-2px);
    }
    </style>
    <script>
    // Product data from database with details
    const databaseProducts = <?php echo json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    
    // Function to get database product by ID
    function getProductById(productId) {
        return databaseProducts.find(p => p.product_id == productId);
    }

    // Make hover image stay on click
    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Only activate if click is inside image area, not on buttons
            if (e.target.closest('.product-actions')) return;
            document.querySelectorAll('.product-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
        });
    });
    
    // Remove active state when clicking outside any product card
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-card')) {
            document.querySelectorAll('.product-card').forEach(c => c.classList.remove('active'));
        }
    });

    // Product modal logic - now uses database data completely
    document.querySelectorAll('.view-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Get product info from the card
            const productCard = this.closest('.product-card');
            const productId = productCard.getAttribute('data-product-id');
            
            // Find database product
            const dbProduct = getProductById(productId);
            
            if (!dbProduct) {
                alert('Product not found!');
                return;
            }
            
            // Use all data from database
            const productInfo = {
                name: dbProduct.name,
                img: dbProduct.hover_image_url || dbProduct.image_url || 'assets/images/placeholder-product.jpg',
                price: 'R' + parseFloat(dbProduct.price).toFixed(2),
                desc: dbProduct.description || 'No description available.',
                longDesc: dbProduct.long_description || 'Detailed information coming soon.',
                stock: parseInt(dbProduct.stock) || 0
            };
            
            // Remove any existing modal
            document.querySelectorAll('.product-modal-bg').forEach(el => el.remove());
            
            // Create modal
            const modalBg = document.createElement('div');
            modalBg.className = 'product-modal-bg';
            modalBg.innerHTML = `
                <div class="product-modal">
                    <button class="close-modal" title="Close">&times;</button>
                    <img src="${productInfo.img}" alt="${productInfo.name}">
                    <h2 style="color:#a97be0;margin-bottom:10px;">${productInfo.name}</h2>
                    <div style="font-size:1.1rem;font-weight:600;color:#222;margin-bottom:8px;">Price: <span style="color:#a97be0;">${productInfo.price}</span></div>
                    ${productInfo.stock <= 0 ? '<div style="color:#e74c3c;font-weight:bold;margin-bottom:8px;">Out of Stock</div>' : 
                      productInfo.stock <= 10 ? '<div style="color:#f39c12;margin-bottom:8px;">Only ' + productInfo.stock + ' left in stock!</div>' : 
                      '<div style="color:#27ae60;margin-bottom:8px;">In Stock</div>'}
                    <p style="margin-bottom:10px;">${productInfo.desc}</p>
                    <div style="color:#555;font-size:0.98rem;margin-bottom:18px;line-height:1.5;">${productInfo.longDesc}</div>
                    <div style="display:flex;gap:12px;justify-content:center;margin-bottom:24px;">
                        <button class="modal-cart-btn" data-product-id="${productId}" style="background:#a97be0;color:#fff;padding:10px 28px;border:none;border-radius:7px;font-size:1rem;cursor:pointer;transition:all 0.3s;" ${productInfo.stock <= 0 ? 'disabled' : ''}>
                            ${productInfo.stock <= 0 ? 'Out of Stock' : 'Add to Cart'}
                        </button>
                        <button class="modal-wishlist-btn" data-product-id="${productId}" style="background:#fff;color:#a97be0;border:1.5px solid #a97be0;padding:10px 28px;border-radius:7px;font-size:1rem;cursor:pointer;transition:all 0.3s;">Add to Wishlist</button>
                    </div>
                    <div class="product-reviews" style="max-width:600px;margin:0 auto 32px auto;padding:24px 18px 18px 18px;border:1.5px solid #a97be0;border-radius:16px;background:#faf8fd;">
                        <h3 style="color:#a97be0;margin-bottom:12px;">Customer Reviews</h3>
                        <div style="margin-bottom:14px;">
                            <strong>Lerato M.</strong> <span style="color:#ffbe19;">&#9733;&#9733;&#9733;&#9733;&#9733;</span><br>
                            <span>"Absolutely loved the ${productInfo.name}! It made my finals so much easier."</span>
                        </div>
                        <div style="margin-bottom:14px;">
                            <strong>Sipho K.</strong> <span style="color:#ffbe19;">&#9733;&#9733;&#9733;&#9733;&#9734;</span><br>
                            <span>"Fast delivery and great quality. Highly recommend Turfies!"</span>
                        </div>
                        <div style="margin-bottom:14px;">
                            <strong>Nomsa T.</strong> <span style="color:#ffbe19;">&#9733;&#9733;&#9733;&#9733;&#9733;</span><br>
                            <span>"A thoughtful gift for any student. Will order again!"</span>
                        </div>
                        <div style="margin-bottom:14px;">
                            <strong>Thabo R.</strong> <span style="color:#ffbe19;">&#9733;&#9733;&#9733;&#9733;&#9733;</span><br>
                            <span>"Perfect for exam season! Everything I needed in one package."</span>
                        </div>
                        <div style="margin-bottom:14px;">
                            <strong>Naledi P.</strong> <span style="color:#ffbe19;">&#9733;&#9733;&#9733;&#9733;&#9734;</span><br>
                            <span>"Great value for money. Will definitely order again!"</span>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalBg);
            
            // Close modal logic
            modalBg.querySelector('.close-modal').onclick = () => modalBg.remove();
            modalBg.onclick = (ev) => { if(ev.target === modalBg) modalBg.remove(); };
            
            // Modal Add to Cart functionality
            const modalCartBtn = modalBg.querySelector('.modal-cart-btn');
            if (modalCartBtn && !modalCartBtn.disabled) {
                modalCartBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    // Show loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<div style="display:inline-block;width:16px;height:16px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></div> Adding...';
                    this.disabled = true;
                    
                    // AJAX request to add to cart
                    fetch('../backend/addtocart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('Product added to cart successfully!', 'success');
                            modalBg.remove();
                            setTimeout(() => {
                                window.location.href = 'cart.php';
                            }, 1500);
                        } else {
                            this.innerHTML = originalContent;
                            this.disabled = false;
                            showMessage(data.message || 'Failed to add product to cart', 'error');
                        }
                    })
                    .catch(error => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        console.error('Error:', error);
                        showMessage('An error occurred. Please try again.', 'error');
                    });
                });
            }
            
            // Modal Add to Wishlist functionality
            const modalWishlistBtn = modalBg.querySelector('.modal-wishlist-btn');
            if (modalWishlistBtn) {
                modalWishlistBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    // Show loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<div style="display:inline-block;width:16px;height:16px;border:2px solid #a97be0;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></div> Adding...';
                    this.disabled = true;
                    
                    // AJAX request to add to wishlist
                    fetch('../backend/addtowishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('Product added to wishlist successfully!', 'success');
                            modalBg.remove();
                            setTimeout(() => {
                                window.location.href = 'wishlist.php';
                            }, 1500);
                        } else {
                            this.innerHTML = originalContent;
                            this.disabled = false;
                            showMessage(data.message || 'Failed to add product to wishlist', 'error');
                        }
                    })
                    .catch(error => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        console.error('Error:', error);
                        showMessage('An error occurred. Please try again.', 'error');
                    });
                });
            }
        });
    });

    // Hover effect for product cards
    document.addEventListener('DOMContentLoaded', function() {
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const mainImg = card.querySelector('.product-img-wrap img:first-child');
            const hoverImg = card.querySelector('.hover-img');
            
            if (mainImg && hoverImg) {
                card.addEventListener('mouseenter', function() {
                    mainImg.style.opacity = '0';
                    hoverImg.style.opacity = '1';
                });
                
                card.addEventListener('mouseleave', function() {
                    mainImg.style.opacity = '1';
                    hoverImg.style.opacity = '0';
                });
            }
        });

        // Add to Cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                if (this.disabled) return;
                
                const productId = this.getAttribute('data-product-id');
                const quantity = 1; // Default quantity
                
                // Show loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<div style="width:22px;height:22px;border:2px solid #a97be0;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></div>';
                this.disabled = true;
                
                // AJAX request to add to cart
                fetch('../backend/addtocart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Restore button
                    this.innerHTML = originalContent;
                    this.disabled = false;
                    
                    if (data.success) {
                        // Show success message
                        showMessage('Product added to cart successfully!', 'success');
                        
                        // Redirect to cart page after a short delay
                        setTimeout(() => {
                            window.location.href = 'cart.php';
                        }, 1500);
                    } else {
                        showMessage(data.message || 'Failed to add product to cart', 'error');
                    }
                })
                .catch(error => {
                    // Restore button
                    this.innerHTML = originalContent;
                    this.disabled = false;
                    
                    console.error('Error:', error);
                    showMessage('An error occurred. Please try again.', 'error');
                });
            });
        });

        // Add to Wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                
                // Show loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '⏳';
                this.style.pointerEvents = 'none';
                
                // AJAX request to add to wishlist
                fetch('../backend/addtowishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Restore button
                    this.innerHTML = originalContent;
                    this.style.pointerEvents = 'auto';
                    
                    if (data.success) {
                        // Change heart to filled
                        this.innerHTML = '&#9829;'; // Filled heart
                        this.style.color = '#e74c3c'; // Red color for filled heart
                        
                        // Show success message
                        showMessage('Product added to wishlist successfully!', 'success');
                        
                        // Redirect to wishlist page after a short delay
                        setTimeout(() => {
                            window.location.href = 'wishlist.php';
                        }, 1500);
                    } else {
                        showMessage(data.message || 'Failed to add product to wishlist', 'error');
                    }
                })
                .catch(error => {
                    // Restore button
                    this.innerHTML = originalContent;
                    this.style.pointerEvents = 'auto';
                    
                    console.error('Error:', error);
                    showMessage('An error occurred. Please try again.', 'error');
                });
            });
        });
    });

    // Function to show messages
    function showMessage(message, type) {
        // Remove existing message
        const existingMessage = document.querySelector('.status-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = 'status-message';
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 300px;
            background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
        `;
        messageDiv.textContent = message;
        
        // Add CSS for animation
        if (!document.querySelector('#message-animation-styles')) {
            const style = document.createElement('style');
            style.id = 'message-animation-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(messageDiv);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.style.animation = 'slideIn 0.3s ease reverse';
                setTimeout(() => messageDiv.remove(), 300);
            }
        }, 3000);
    }
    </script>
    <footer class="main-footer">
        <?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
