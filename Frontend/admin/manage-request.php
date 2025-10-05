<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Request - Admin Panel</title>
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
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-input {
            padding: 8px 12px 8px 40px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            width: 250px;
        }
        .search-input:focus {
            border-color: #1976d2;
            outline: none;
        }
        .search-icon {
            position: absolute;
            left: 12px;
            color: #666;
            pointer-events: none;
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
        .stat-card.new { background: linear-gradient(135deg, #ff9800, #ffb74d); }
        .stat-card.viewed { background: linear-gradient(135deg, #2196f3, #64b5f6); }
        .stat-card.assisted { background: linear-gradient(135deg, #4caf50, #81c784); }
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
        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .request-table th, .request-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .request-table th {
            background: #1976d2;
            color: #fff;
        }
        .btn-action {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-view {
            background: #1976d2;
            color: #fff;
        }
        .btn-edit {
            background: #ffbe19;
            color: #1976d2;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #1976d2;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }
        .status-new { background: #fff3cd; color: #856404; }
        .status-viewed { background: #d1ecf1; color: #0c5460; }
        .status-assisted { background: #d4edda; color: #155724; }
        .pagination-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: #fff;
            color: #1976d2;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .pagination-btn:hover {
            background: #1976d2;
            color: #fff;
        }
        .pagination-btn.active {
            background: #1976d2;
            color: #fff;
        }
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-approve {
            background: #28a745;
            color: #fff;
        }
        .btn-reject {
            background: #dc3545;
            color: #fff;
        }
        .btn-view {
            background: #ffbe19;
            color: #1976d2;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
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
                <h1>Manage Request</h1>
            </div>
            
            <div class="content-area">
                <h3>Request Management</h3>
                <p>Review and manage all help requests and support tickets.</p>
                
                <!-- Statistics Cards -->
                <div class="stats-row" id="statsContainer">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 id="totalRequests">0</h3>
                        <p>Total Requests</p>
                    </div>
                    <div class="stat-card new">
                        <div class="stat-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3 id="newRequests">0</h3>
                        <p>New Requests</p>
                    </div>
                    <div class="stat-card viewed">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 id="viewedRequests">0</h3>
                        <p>Viewed Requests</p>
                    </div>
                    <div class="stat-card assisted">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 id="assistedRequests">0</h3>
                        <p>Assisted Requests</p>
                    </div>
                </div>
                
                <!-- Filters Section -->
                <div class="filters-section">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="search-input" placeholder="Search requests...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="filter-group">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="new">New</option>
                            <option value="viewed">Viewed</option>
                            <option value="assisted">Assisted</option>
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
                    <button class="btn-action btn-success" onclick="refreshRequests()">
                        <i class="fas fa-refresh"></i> Refresh
                    </button>
                </div>
                
                <div id="loadingContainer" class="loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading requests...</p>
                </div>
                
                <div id="requestsTableContainer">
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>User/Guest</th>
                            <th>Subject</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTableBody">
                        <!-- Requests will be loaded here dynamically -->
                    </tbody>
                </table>
                
                <div id="paginationContainer" style="margin-top: 20px; text-align: center;">
                    <!-- Pagination will be added here -->
                </div>
                </div>
                
                <div id="noRequestsMessage" style="display: none; text-align: center; padding: 60px; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>No requests found</h3>
                    <p>There are no help requests matching your current filters.</p>
                </div>
            </div>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let currentFilters = {};

        // Load requests on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadRequests();
        });

        // Load request statistics
        async function loadStats() {
            try {
                const response = await fetch('../../backend/admin/managerequests.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.stats;
                    document.getElementById('totalRequests').textContent = stats.total || 0;
                    document.getElementById('newRequests').textContent = stats.new || 0;
                    document.getElementById('viewedRequests').textContent = stats.viewed || 0;
                    document.getElementById('assistedRequests').textContent = stats.assisted || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load requests with pagination and filters
        async function loadRequests(page = 1, filters = {}) {
            showLoading(true);
            try {
                const params = new URLSearchParams({
                    action: 'list',
                    page: page,
                    limit: 20,
                    ...filters
                });

                const response = await fetch(`../../backend/admin/managerequests.php?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    displayRequests(data.requests);
                    displayPagination(data.pagination);
                } else {
                    showError(data.message || 'Failed to load requests');
                }
            } catch (error) {
                console.error('Error loading requests:', error);
                showError('Error loading requests. Please try again.');
            } finally {
                showLoading(false);
            }
        }

        // Display requests in table
        function displayRequests(requests) {
            const tbody = document.getElementById('requestsTableBody');
            const noRequestsMsg = document.getElementById('noRequestsMessage');
            const tableContainer = document.getElementById('requestsTableContainer');

            if (!requests || requests.length === 0) {
                tableContainer.style.display = 'none';
                noRequestsMsg.style.display = 'block';
                return;
            }

            tableContainer.style.display = 'block';
            noRequestsMsg.style.display = 'none';

            tbody.innerHTML = requests.map(request => `
                <tr>
                    <td>#${request.request_id}</td>
                    <td>
                        ${request.user_name || request.guest_name || 'Guest'}
                        <br><small class="text-muted">${request.guest_email || ''}</small>
                    </td>
                    <td>
                        <strong>${request.subject}</strong>
                        <br><small class="text-muted">${request.message.substring(0, 50)}${request.message.length > 50 ? '...' : ''}</small>
                    </td>
                    <td>${request.whatsapp_contact || 'N/A'}</td>
                    <td>${new Date(request.created_at).toLocaleDateString()}</td>
                    <td><span class="status-badge status-${request.status}">${request.status}</span></td>
                    <td>
                        <button class="btn-action btn-view" onclick="viewRequest(${request.request_id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        ${request.status === 'new' ? `
                            <button class="btn-action btn-edit" onclick="markAsViewed(${request.request_id})">
                                <i class="fas fa-check"></i> Mark Viewed
                            </button>
                        ` : ''}
                        ${request.status !== 'assisted' ? `
                            <button class="btn-action btn-success" onclick="markAsAssisted(${request.request_id})">
                                <i class="fas fa-check-circle"></i> Assist
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // Display pagination
        function displayPagination(pagination) {
            const container = document.getElementById('paginationContainer');
            
            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }

            let paginationHTML = '<div class="pagination-controls">';
            
            // Previous button
            if (pagination.current_page > 1) {
                paginationHTML += `<button class="pagination-btn" onclick="changePage(${pagination.current_page - 1})">Previous</button>`;
            }

            // Page numbers
            const startPage = Math.max(1, pagination.current_page - 2);
            const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === pagination.current_page ? 'active' : '';
                paginationHTML += `<button class="pagination-btn ${activeClass}" onclick="changePage(${i})">${i}</button>`;
            }

            // Next button
            if (pagination.current_page < pagination.total_pages) {
                paginationHTML += `<button class="pagination-btn" onclick="changePage(${pagination.current_page + 1})">Next</button>`;
            }

            paginationHTML += '</div>';
            container.innerHTML = paginationHTML;
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            loadRequests(page, currentFilters);
        }

        // Apply filters
        function applyFilters() {
            const filters = {
                status: document.getElementById('statusFilter').value,
                search: document.getElementById('searchInput').value,
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            };

            // Remove empty filters
            Object.keys(filters).forEach(key => {
                if (!filters[key]) delete filters[key];
            });

            currentFilters = filters;
            currentPage = 1;
            loadRequests(1, filters);
        }

        // Clear filters
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            
            currentFilters = {};
            currentPage = 1;
            loadRequests();
        }

        // Refresh requests
        function refreshRequests() {
            loadStats();
            loadRequests(currentPage, currentFilters);
        }

        // View request details
        function viewRequest(requestId) {
            window.location.href = `view-request-detail.php?id=${requestId}`;
        }

        // Mark request as viewed
        async function markAsViewed(requestId) {
            try {
                const response = await fetch('../../backend/admin/managerequests.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&request_id=${requestId}&status=viewed`
                });

                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Request marked as viewed');
                    loadStats();
                    loadRequests(currentPage, currentFilters);
                } else {
                    showError(data.message || 'Failed to update request');
                }
            } catch (error) {
                console.error('Error updating request:', error);
                showError('Error updating request. Please try again.');
            }
        }

        // Mark request as assisted
        async function markAsAssisted(requestId) {
            try {
                const response = await fetch('../../backend/admin/managerequests.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&request_id=${requestId}&status=assisted`
                });

                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Request marked as assisted');
                    loadStats();
                    loadRequests(currentPage, currentFilters);
                } else {
                    showError(data.message || 'Failed to update request');
                }
            } catch (error) {
                console.error('Error updating request:', error);
                showError('Error updating request. Please try again.');
            }
        }

        // Show/hide loading
        function showLoading(show) {
            const loadingContainer = document.getElementById('loadingContainer');
            const tableContainer = document.getElementById('requestsTableContainer');
            
            if (show) {
                loadingContainer.style.display = 'block';
                tableContainer.style.display = 'none';
            } else {
                loadingContainer.style.display = 'none';
            }
        }

        // Show success message
        function showSuccess(message) {
            // You can implement a toast or alert here
            alert('Success: ' + message);
        }

        // Show error message
        function showError(message) {
            // You can implement a toast or alert here
            alert('Error: ' + message);
        }

        // Real-time search
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(applyFilters, 500);
        });

        // Auto-apply filters on status change
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
    </script>
</body>
</html>