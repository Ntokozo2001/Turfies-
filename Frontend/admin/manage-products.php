<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f6f6;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: #1976d2;
            color: #fff;
            width: 220px;
            padding: 24px 0;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .admin-sidebar h2 {
            color: #ffbe19;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 0 24px 24px 24px;
            margin: 0;
            border-bottom: 2px solid #ffbe19;
        }
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .admin-sidebar li {
            margin: 8px 0;
        }
        .admin-sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px 24px;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background: #ffbe19;
            color: #1976d2;
        }
        .admin-main {
            flex: 1;
            background: #ffbe19;
            padding: 32px;
        }
        .page-header {
            background: #1976d2;
            color: #ffbe19;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .content-area {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            background: #f9f9f9;
        }
        .product-card h4 {
            color: #1976d2;
            margin: 0 0 8px 0;
        }
        .btn-action {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-edit {
            background: #ffbe19;
            color: #1976d2;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-add {
            background: #28a745;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 20px;
            border: none;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1976d2;
        }
        .modal-header h2 {
            margin: 0;
            color: #1976d2;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #1976d2;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #1976d2;
            outline: none;
        }
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        .btn-save {
            background: #1976d2;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-cancel {
            background: #6c757d;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #1976d2;
        }
        .no-products {
            text-align: center;
            padding: 60px;
            color: #666;
        }
        .alert {
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            font-weight: bold;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #ddd;
        }
        .stock-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .stock-good { background: #d4edda; color: #155724; }
        .stock-low { background: #fff3cd; color: #856404; }
        .stock-out { background: #f8d7da; color: #721c24; }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type=file] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-input-display {
            display: block;
            padding: 10px;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .file-input-display:hover {
            border-color: #1976d2;
        }
        .current-image {
            max-width: 100px;
            max-height: 100px;
            margin: 10px 0;
            border-radius: 6px;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php include '../navbar.php'; ?>
    </header>
    
    <div class="admin-container">
        <?php include 'sidebar-admin.php'; ?>
        
        <main class="admin-main">
            <div class="page-header">
                <h1>Manage Products</h1>
            </div>
            
            <div class="content-area">
                <h3>Product Management</h3>
                <p>Add, edit, and manage all products in your store.</p>
                
                <div id="alertContainer"></div>
                
                <button class="btn-action btn-add" onclick="openAddModal()">Add New Product</button>
                
                <div id="loadingContainer" class="loading">
                    <p>Loading products...</p>
                </div>
                
                <div id="productGridContainer" style="display: none;">
                    <div class="product-grid" id="productGrid">
                        <!-- Products will be loaded here dynamically -->
                    </div>
                </div>
                
                <div id="noProductsContainer" class="no-products" style="display: none;">
                    <p>No products found in the system.</p>
                    <button class="btn-action btn-add" onclick="openAddModal()">Add Your First Product</button>
                </div>
            </div>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>
    
    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Product</h2>
                <span class="close" onclick="closeProductModal()">&times;</span>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="product_id">
                <input type="hidden" id="formAction" name="action" value="create">
                
                <div class="form-group">
                    <label for="productName">Product Name: *</label>
                    <input type="text" id="productName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="productDescription">Description: *</label>
                    <textarea id="productDescription" name="description" required placeholder="Enter product description..."></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="productPrice">Price (R): *</label>
                        <input type="number" id="productPrice" name="price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="productStock">Stock Quantity: *</label>
                        <input type="number" id="productStock" name="stock" min="0" required>
                    </div>
                </div>
                
                <!-- Category field removed as it doesn't exist in database -->
                
                <div class="form-group">
                    <label for="productImage">Main Product Image:</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="productImage" name="image" accept="image/*">
                        <div class="file-input-display" onclick="document.getElementById('productImage').click()">
                            Click to select main image (JPG, PNG, GIF - Max 5MB)
                        </div>
                    </div>
                    <img id="currentMainImage" class="current-image" style="display: none;">
                </div>
                
                <div class="form-group">
                    <label for="productHoverImage">Hover Image (Optional):</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="productHoverImage" name="hover_image" accept="image/*">
                        <div class="file-input-display" onclick="document.getElementById('productHoverImage').click()">
                            Click to select hover image (JPG, PNG, GIF - Max 5MB)
                        </div>
                    </div>
                    <img id="currentHoverImage" class="current-image" style="display: none;">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-save" id="saveButton">Save Product</button>
                    <button type="button" class="btn-cancel" onclick="closeProductModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentProducts = [];

        // Load products when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            loadCategories();
        });

        // Function to load all products
        function loadProducts() {
            fetch('../../backend/admin/manageproducts.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentProducts = data.products;
                        displayProducts(data.products);
                    } else {
                        showAlert('Error loading products: ' + data.message, 'error');
                        document.getElementById('loadingContainer').style.display = 'none';
                        document.getElementById('noProductsContainer').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading products. Please try again.', 'error');
                    document.getElementById('loadingContainer').style.display = 'none';
                });
        }

        // Function to load categories
        function loadCategories() {
            fetch('../../backend/admin/manageproducts.php?action=categories')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.categories.length > 0) {
                        const categorySelect = document.getElementById('productCategory');
                        // Clear existing options except the first one
                        while (categorySelect.children.length > 1) {
                            categorySelect.removeChild(categorySelect.lastChild);
                        }
                        // Add categories from database
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category;
                            option.textContent = category;
                            categorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
        }

        // Function to display products in the grid
        function displayProducts(products) {
            const productGrid = document.getElementById('productGrid');
            const loadingContainer = document.getElementById('loadingContainer');
            const productGridContainer = document.getElementById('productGridContainer');
            const noProductsContainer = document.getElementById('noProductsContainer');

            loadingContainer.style.display = 'none';

            if (products.length === 0) {
                noProductsContainer.style.display = 'block';
                return;
            }

            productGrid.innerHTML = '';
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                
                const price = parseFloat(product.price || 0);
                const stock = parseInt(product.stock || 0);
                const stockStatus = getStockStatus(stock);
                const stockClass = getStockStatusClass(stock);
                
                productCard.innerHTML = `
                    ${product.image_url ? `<img src="../${product.image_url}" alt="${product.name}" class="product-image" style="width: 100%; height: 120px; object-fit: cover; margin-bottom: 10px;">` : ''}
                    <h4>${product.name}</h4>
                    <p><strong>Price:</strong> R${price.toFixed(2)}</p>
                    <p><strong>Stock:</strong> <span class="stock-status ${stockClass}">${stockStatus}</span></p>
                    <p style="font-size: 0.9rem; color: #666; margin: 10px 0;">${(product.description || '').substring(0, 100)}${product.description && product.description.length > 100 ? '...' : ''}</p>
                    <div>
                        <button class="btn-action btn-edit" onclick="editProduct(${product.product_id})">Edit</button>
                        <button class="btn-action btn-delete" onclick="deleteProduct(${product.product_id}, '${product.name}')">Delete</button>
                    </div>
                `;
                productGrid.appendChild(productCard);
            });

            productGridContainer.style.display = 'block';
        }

        // Function to get stock status text
        function getStockStatus(stock) {
            if (stock > 10) return `${stock} units`;
            else if (stock > 0) return `${stock} left (Low)`;
            else return 'Out of Stock';
        }

        // Function to get stock status class
        function getStockStatusClass(stock) {
            if (stock > 10) return 'stock-good';
            else if (stock > 0) return 'stock-low';
            else return 'stock-out';
        }

        // Function to open add product modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'create';
            document.getElementById('saveButton').textContent = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('currentMainImage').style.display = 'none';
            document.getElementById('currentHoverImage').style.display = 'none';
            document.getElementById('productModal').style.display = 'block';
        }

        // Function to edit product
        function editProduct(productId) {
            fetch(`../../backend/admin/manageproducts.php?action=get&product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        document.getElementById('modalTitle').textContent = 'Edit Product';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('saveButton').textContent = 'Update Product';
                        
                        document.getElementById('productId').value = product.product_id;
                        document.getElementById('productName').value = product.name;
                        document.getElementById('productDescription').value = product.description;
                        document.getElementById('productPrice').value = product.price;
                        document.getElementById('productStock').value = product.stock;
                        
                        // Show current images
                        if (product.image_url) {
                            document.getElementById('currentMainImage').src = '../' + product.image_url;
                            document.getElementById('currentMainImage').style.display = 'block';
                        } else {
                            document.getElementById('currentMainImage').style.display = 'none';
                        }
                        
                        if (product.hover_image_url) {
                            document.getElementById('currentHoverImage').src = '../' + product.hover_image_url;
                            document.getElementById('currentHoverImage').style.display = 'block';
                        } else {
                            document.getElementById('currentHoverImage').style.display = 'none';
                        }
                        
                        document.getElementById('productModal').style.display = 'block';
                    } else {
                        showAlert('Error loading product data: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading product data. Please try again.', 'error');
                });
        }

        // Function to close product modal
        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        // Handle product form submission
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = document.getElementById('formAction').value;
            
            // Show loading state
            const saveButton = document.getElementById('saveButton');
            const originalText = saveButton.textContent;
            saveButton.textContent = 'Saving...';
            saveButton.disabled = true;

            fetch('../../backend/admin/manageproducts.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeProductModal();
                    loadProducts(); // Reload the product list
                } else {
                    showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error saving product. Please try again.', 'error');
            })
            .finally(() => {
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
        });

        // Function to delete product
        function deleteProduct(productId, productName) {
            if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('product_id', productId);

                fetch('../../backend/admin/manageproducts.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        loadProducts(); // Reload the product list
                    } else {
                        showAlert('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting product. Please try again.', 'error');
                });
            }
        }

        // Function to show alerts
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;
            
            // Auto-hide alert after 5 seconds
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
            
            // Scroll to top to show alert
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Update file input displays when files are selected
        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const display = e.target.nextElementSibling;
            if (file) {
                display.textContent = `Selected: ${file.name}`;
            } else {
                display.textContent = 'Click to select main image (JPG, PNG, GIF - Max 5MB)';
            }
        });

        document.getElementById('productHoverImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const display = e.target.nextElementSibling;
            if (file) {
                display.textContent = `Selected: ${file.name}`;
            } else {
                display.textContent = 'Click to select hover image (JPG, PNG, GIF - Max 5MB)';
            }
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                closeProductModal();
            }
        }
    </script>
</body>
</html>