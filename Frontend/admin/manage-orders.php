<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.pending { background: linear-gradient(135deg, #ff9800, #ffb74d); }
        .stat-card.processing { background: linear-gradient(135deg, #2196f3, #64b5f6); }
        .stat-card.completed { background: linear-gradient(135deg, #4caf50, #81c784); }
        .stat-card.cancelled { background: linear-gradient(135deg, #f44336, #e57373); }
        .stat-card h3 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: bold;
        }
        .stat-card p {
            margin: 8px 0 0 0;
            opacity: 0.9;
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .filters-section {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group label {
            font-weight: 600;
            color: #1976d2;
        }
        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .filter-group select:focus, .filter-group input:focus {
            border-color: #1976d2;
            outline: none;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .orders-table th {
            background: #1976d2;
            color: #fff;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        .orders-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #cce7ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-refunded { background: #e2e3e5; color: #383d41; }
        .btn-action {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-view {
            background: #1976d2;
            color: #fff;
        }
        .btn-edit {
            background: #ffbe19;
            color: #1976d2;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #1976d2;
        }
        .no-orders {
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
        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
        }
        .pagination-info {
            color: #666;
            font-size: 0.9rem;
        }
        .pagination-btn {
            padding: 8px 16px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .pagination-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .order-id {
            font-weight: bold;
            color: #1976d2;
        }
        .customer-info {
            font-size: 0.9rem;
        }
        .product-info {
            max-width: 200px;
            font-size: 0.9rem;
        }
        .amount-cell {
            font-weight: bold;
            color: #2e7d32;
        }
        .date-cell {
            font-size: 0.85rem;
            color: #666;
        }
        .status-select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.8rem;
            background: #fff;
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
            margin: 5% auto;
            padding: 20px;
            border: none;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-height: 80vh;
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
        .order-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .detail-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1976d2;
        }
        .detail-section h4 {
            margin: 0 0 15px 0;
            color: #1976d2;
        }
        .detail-item {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .search-container {
            position: relative;
            margin-right: 15px;
        }
        .search-input {
            padding: 8px 40px 8px 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            width: 200px;
        }
        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr;
            }
            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }
            .order-detail-grid {
                grid-template-columns: 1fr;
            }
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
                <h1><i class="fas fa-shopping-cart"></i> Manage Orders</h1>
            </div>
            
            <div class="content-area">
                <h3>Order Management System</h3>
                <p>Monitor, track, and manage all customer orders and transactions.</p>
                
                <div id="alertContainer"></div>
                
                <!-- Statistics Cards -->
                <div class="stats-row" id="statsContainer">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                        <h3 id="totalOrders">0</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-card pending">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <h3 id="pendingOrders">0</h3>
                        <p>Pending Orders</p>
                    </div>
                    <div class="stat-card processing">
                        <div class="stat-icon"><i class="fas fa-cogs"></i></div>
                        <h3 id="processingOrders">0</h3>
                        <p>Processing Orders</p>
                    </div>
                    <div class="stat-card completed">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <h3 id="completedOrders">0</h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
                
                <!-- Filters Section -->
                <div class="filters-section">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="search-input" placeholder="Search orders...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="filter-group">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="dateFilter">Date Range:</label>
                        <input type="date" id="dateFrom">
                        <span style="margin: 0 5px;">to</span>
                        <input type="date" id="dateTo">
                    </div>
                    <button class="btn-action btn-view" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn-action btn-edit" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                    <button class="btn-action btn-view" onclick="refreshOrders()">
                        <i class="fas fa-refresh"></i> Refresh
                    </button>
                </div>
                
                <div id="loadingContainer" class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading orders...</p>
                </div>
                
                <div id="ordersTableContainer" style="display: none;">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <!-- Orders will be loaded here dynamically -->
                        </tbody>
                    </table>
                    
                    <!-- Pagination Controls -->
                    <div class="pagination-controls">
                        <button class="pagination-btn" id="prevPageBtn" onclick="changePage(-1)">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <div class="pagination-info" id="paginationInfo">
                            Page 1 of 1 (0 total)
                        </div>
                        <button class="pagination-btn" id="nextPageBtn" onclick="changePage(1)">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div id="noOrdersContainer" class="no-orders" style="display: none;">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                    <p>No orders found matching the current filters.</p>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <span class="close" onclick="closeOrderModal()">&times;</span>
            </div>
            <div id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let currentFilters = {};

        // Load orders and stats when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadOrders();
        });

        // Function to load order statistics
        function loadStats() {
            fetch('../../backend/admin/manageorders.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalOrders').textContent = data.stats.total || 0;
                        document.getElementById('pendingOrders').textContent = data.stats.pending || 0;
                        document.getElementById('processingOrders').textContent = data.stats.processing || 0;
                        document.getElementById('completedOrders').textContent = (data.stats.delivered || 0) + (data.stats.completed || 0);
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Function to load all orders
        function loadOrders(page = 1) {
            currentPage = page;
            
            let url = `../../backend/admin/manageorders.php?action=list&page=${page}&limit=20`;
            
            // Add filters to URL
            if (currentFilters.status) url += `&status=${currentFilters.status}`;
            if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
            if (currentFilters.dateFrom) url += `&date_from=${currentFilters.dateFrom}`;
            if (currentFilters.dateTo) url += `&date_to=${currentFilters.dateTo}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrders(data.orders);
                        updatePagination(data.pagination);
                    } else {
                        showAlert('Error loading orders: ' + data.message, 'error');
                        document.getElementById('loadingContainer').style.display = 'none';
                        document.getElementById('noOrdersContainer').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading orders. Please try again.', 'error');
                    document.getElementById('loadingContainer').style.display = 'none';
                });
        }

        // Function to display orders in the table
        function displayOrders(orders) {
            const tableBody = document.getElementById('ordersTableBody');
            const loadingContainer = document.getElementById('loadingContainer');
            const ordersTableContainer = document.getElementById('ordersTableContainer');
            const noOrdersContainer = document.getElementById('noOrdersContainer');

            loadingContainer.style.display = 'none';

            if (orders.length === 0) {
                noOrdersContainer.style.display = 'block';
                ordersTableContainer.style.display = 'none';
                return;
            }

            tableBody.innerHTML = '';
            orders.forEach(order => {
                const row = document.createElement('tr');
                
                // Format date
                const orderDate = new Date(order.created_at || order.order_date);
                const formattedDate = orderDate.toLocaleDateString() + '<br><small>' + 
                                    orderDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + '</small>';
                
                // Format amount
                const amount = parseFloat(order.total_amount || order.amount || 0);
                
                row.innerHTML = `
                    <td class="order-id">#${order.order_id}</td>
                    <td class="customer-info">
                        <strong>${order.customer_name || order.user_name || 'N/A'}</strong><br>
                        <small>${order.customer_email || order.email || 'N/A'}</small>
                    </td>
                    <td class="product-info">
                        <strong>${order.product_name || 'Product'}</strong><br>
                        <small>${order.product_description || ''}</small>
                    </td>
                    <td>${order.quantity || 1}</td>
                    <td class="amount-cell">R${amount.toFixed(2)}</td>
                    <td>
                        <select class="status-select" onchange="updateOrderStatus(${order.order_id}, this.value)">
                            <option value="pending" ${(order.order_status || order.status) === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="processing" ${(order.order_status || order.status) === 'processing' ? 'selected' : ''}>Processing</option>
                            <option value="shipped" ${(order.order_status || order.status) === 'shipped' ? 'selected' : ''}>Shipped</option>
                            <option value="delivered" ${(order.order_status || order.status) === 'delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="cancelled" ${(order.order_status || order.status) === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            <option value="refunded" ${(order.order_status || order.status) === 'refunded' ? 'selected' : ''}>Refunded</option>
                        </select>
                    </td>
                    <td class="date-cell">${formattedDate}</td>
                    <td>
                        <button class="btn-action btn-view" onclick="viewOrderDetails(${order.order_id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit" onclick="editOrder(${order.order_id})" title="Edit Order">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteOrder(${order.order_id}, '${order.order_id}')" title="Delete Order">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            noOrdersContainer.style.display = 'none';
            ordersTableContainer.style.display = 'block';
        }

        // Function to update pagination controls
        function updatePagination(pagination) {
            totalPages = pagination.total_pages;
            currentPage = pagination.current_page;

            document.getElementById('paginationInfo').innerHTML = 
                `Page ${pagination.current_page} of ${pagination.total_pages} (${pagination.total_count} total)`;
            
            document.getElementById('prevPageBtn').disabled = pagination.current_page <= 1;
            document.getElementById('nextPageBtn').disabled = pagination.current_page >= pagination.total_pages;
        }

        // Function to change page
        function changePage(direction) {
            const newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                loadOrders(newPage);
            }
        }

        // Function to apply filters
        function applyFilters() {
            currentFilters = {
                status: document.getElementById('statusFilter').value,
                search: document.getElementById('searchInput').value,
                dateFrom: document.getElementById('dateFrom').value,
                dateTo: document.getElementById('dateTo').value
            };
            loadOrders(1);
        }

        // Function to clear filters
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            currentFilters = {};
            loadOrders(1);
        }

        // Function to refresh orders
        function refreshOrders() {
            loadStats();
            loadOrders(currentPage);
        }

        // Function to update order status
        function updateOrderStatus(orderId, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('order_id', orderId);
            formData.append('status', status);

            fetch('../../backend/admin/manageorders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    loadStats(); // Refresh stats
                } else {
                    showAlert('Error: ' + data.message, 'error');
                    loadOrders(currentPage); // Reload to reset status
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error updating order status. Please try again.', 'error');
                loadOrders(currentPage); // Reload to reset status
            });
        }

        // Function to view order details
        function viewOrderDetails(orderId) {
            fetch(`../../backend/admin/manageorders.php?action=get&order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showOrderDetailsModal(data.order);
                    } else {
                        showAlert('Error loading order details: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading order details. Please try again.', 'error');
                });
        }

        // Function to show order details modal
        function showOrderDetailsModal(order) {
            const modal = document.getElementById('orderDetailsModal');
            const content = document.getElementById('orderDetailsContent');
            
            const orderDate = new Date(order.created_at || order.order_date);
            const amount = parseFloat(order.total_amount || order.amount || 0);
            
            content.innerHTML = `
                <div class="order-detail-grid">
                    <div class="detail-section">
                        <h4><i class="fas fa-shopping-cart"></i> Order Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Order ID:</span>
                            <span class="detail-value">#${order.order_id}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="status-badge status-${order.order_status || order.status}">
                                    ${(order.order_status || order.status).charAt(0).toUpperCase() + (order.order_status || order.status).slice(1)}
                                </span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Order Date:</span>
                            <span class="detail-value">${orderDate.toLocaleDateString()} ${orderDate.toLocaleTimeString()}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Amount:</span>
                            <span class="detail-value amount-cell">R${amount.toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-user"></i> Customer Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">${order.customer_name || order.user_name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">${order.customer_email || order.email || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">${order.customer_phone || order.phone || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">User ID:</span>
                            <span class="detail-value">${order.user_id || 'Guest'}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4><i class="fas fa-box"></i> Product Details</h4>
                    <div class="detail-item">
                        <span class="detail-label">Product:</span>
                        <span class="detail-value">${order.product_name || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Quantity:</span>
                        <span class="detail-value">${order.quantity || 1}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Unit Price:</span>
                        <span class="detail-value">R${(amount / (order.quantity || 1)).toFixed(2)}</span>
                    </div>
                    ${order.product_description ? `
                    <div class="detail-item">
                        <span class="detail-label">Description:</span>
                        <span class="detail-value">${order.product_description}</span>
                    </div>
                    ` : ''}
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <button class="btn-action btn-edit" onclick="editOrder(${order.order_id})">
                        <i class="fas fa-edit"></i> Edit Order
                    </button>
                    <button class="btn-action btn-view" onclick="printOrder(${order.order_id})">
                        <i class="fas fa-print"></i> Print Invoice
                    </button>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        // Function to close order modal
        function closeOrderModal() {
            document.getElementById('orderDetailsModal').style.display = 'none';
        }

        // Function to edit order (placeholder)
        function editOrder(orderId) {
            showAlert('Edit order functionality will be implemented soon.', 'success');
        }

        // Function to delete order
        function deleteOrder(orderId, orderNumber) {
            if (confirm(`Are you sure you want to delete order #${orderNumber}? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('order_id', orderId);

                fetch('../../backend/admin/manageorders.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        loadStats();
                        loadOrders(currentPage);
                    } else {
                        showAlert('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting order. Please try again.', 'error');
                });
            }
        }

        // Function to print order (placeholder)
        function printOrder(orderId) {
            showAlert('Print functionality will be implemented soon.', 'success');
        }

        // Function to show alerts
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
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

        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailsModal');
            if (event.target === modal) {
                closeOrderModal();
            }
        }
    </script>
</body>
</html>
