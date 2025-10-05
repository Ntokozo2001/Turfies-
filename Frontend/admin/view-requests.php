<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests - Admin Panel</title>
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
        }
        .stat-card.new { background: linear-gradient(135deg, #f44336, #ff7961); }
        .stat-card.viewed { background: linear-gradient(135deg, #ff9800, #ffb74d); }
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
        .filter-group select {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .requests-table th,
        .requests-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .requests-table th {
            background: #1976d2;
            color: #fff;
            font-weight: 600;
        }
        .requests-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status-new { background: #ffebee; color: #c62828; }
        .status-viewed { background: #fff8e1; color: #f57f17; }
        .status-assisted { background: #e8f5e8; color: #2e7d32; }
        .btn-action {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view {
            background: #1976d2;
            color: #fff;
        }
        .btn-status {
            background: #ffbe19;
            color: #1976d2;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #1976d2;
        }
        .no-requests {
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
        .subject-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .message-preview {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #666;
            font-size: 0.9rem;
        }
        .date-cell {
            font-size: 0.85rem;
            color: #666;
        }
        .contact-info {
            font-size: 0.9rem;
            color: #444;
        }
        .status-select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.8rem;
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
                <h1><i class="fas fa-envelope"></i> View Requests</h1>
            </div>
            
            <div class="content-area">
                <h3>Help Request Management</h3>
                <p>Monitor and manage all customer support requests and inquiries.</p>
                
                <div id="alertContainer"></div>
                
                <!-- Statistics Cards -->
                <div class="stats-row" id="statsContainer">
                    <div class="stat-card">
                        <h3 id="totalRequests">0</h3>
                        <p>Total Requests</p>
                    </div>
                    <div class="stat-card new">
                        <h3 id="newRequests">0</h3>
                        <p>New Requests</p>
                    </div>
                    <div class="stat-card viewed">
                        <h3 id="viewedRequests">0</h3>
                        <p>Viewed Requests</p>
                    </div>
                    <div class="stat-card assisted">
                        <h3 id="assistedRequests">0</h3>
                        <p>Assisted Requests</p>
                    </div>
                </div>
                
                <!-- Filters Section -->
                <div class="filters-section">
                    <div class="filter-group">
                        <label for="statusFilter">Filter by Status:</label>
                        <select id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="new">New</option>
                            <option value="viewed">Viewed</option>
                            <option value="assisted">Assisted</option>
                        </select>
                    </div>
                    <button class="btn-action btn-view" onclick="refreshRequests()">
                        <i class="fas fa-refresh"></i> Refresh
                    </button>
                </div>
                
                <div id="loadingContainer" class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading requests...</p>
                </div>
                
                <div id="requestsTableContainer" style="display: none;">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>From</th>
                                <th>Contact Info</th>
                                <th>Subject</th>
                                <th>Message Preview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <!-- Requests will be loaded here dynamically -->
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
                
                <div id="noRequestsContainer" class="no-requests" style="display: none;">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                    <p>No requests found in the system.</p>
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
        let currentStatus = '';
        let totalPages = 1;

        // Load requests and stats when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadRequests();
        });

        // Function to load request statistics
        function loadStats() {
            fetch('../../backend/admin/managerequests.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalRequests').textContent = data.stats.total;
                        document.getElementById('newRequests').textContent = data.stats.new;
                        document.getElementById('viewedRequests').textContent = data.stats.viewed;
                        document.getElementById('assistedRequests').textContent = data.stats.assisted;
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Function to load all requests
        function loadRequests(page = 1) {
            currentPage = page;
            currentStatus = document.getElementById('statusFilter').value;
            
            let url = `../../backend/admin/managerequests.php?action=list&page=${page}&limit=20`;
            if (currentStatus) {
                url += `&status=${currentStatus}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRequests(data.requests);
                        updatePagination(data.pagination);
                    } else {
                        showAlert('Error loading requests: ' + data.message, 'error');
                        document.getElementById('loadingContainer').style.display = 'none';
                        document.getElementById('noRequestsContainer').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading requests. Please try again.', 'error');
                    document.getElementById('loadingContainer').style.display = 'none';
                });
        }

        // Function to display requests in the table
        function displayRequests(requests) {
            const tableBody = document.getElementById('requestsTableBody');
            const loadingContainer = document.getElementById('loadingContainer');
            const requestsTableContainer = document.getElementById('requestsTableContainer');
            const noRequestsContainer = document.getElementById('noRequestsContainer');

            loadingContainer.style.display = 'none';

            if (requests.length === 0) {
                noRequestsContainer.style.display = 'block';
                requestsTableContainer.style.display = 'none';
                return;
            }

            tableBody.innerHTML = '';
            requests.forEach(request => {
                const row = document.createElement('tr');
                
                // Format date
                const createdDate = new Date(request.created_at);
                const formattedDate = createdDate.toLocaleDateString() + '<br>' + 
                                    createdDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Determine contact source
                const contactFrom = request.user_name || request.guest_name || 'Anonymous';
                const contactInfo = request.user_name 
                    ? `<strong>User:</strong> ${request.user_name}<br><small>Email: ${request.guest_email || 'N/A'}</small>`
                    : `<strong>Guest:</strong> ${request.guest_name}<br><small>Email: ${request.guest_email}</small>`;
                
                row.innerHTML = `
                    <td><strong>#${request.request_id}</strong></td>
                    <td class="contact-info">${contactFrom}</td>
                    <td class="contact-info">${contactInfo}<br><small>WhatsApp: ${request.whatsapp_contact || 'N/A'}</small></td>
                    <td class="subject-preview" title="${request.subject}">${request.subject}</td>
                    <td class="message-preview" title="${request.message}">${request.message}</td>
                    <td>
                        <select class="status-select" onchange="updateStatus(${request.request_id}, this.value)">
                            <option value="new" ${request.status === 'new' ? 'selected' : ''}>New</option>
                            <option value="viewed" ${request.status === 'viewed' ? 'selected' : ''}>Viewed</option>
                            <option value="assisted" ${request.status === 'assisted' ? 'selected' : ''}>Assisted</option>
                        </select>
                    </td>
                    <td class="date-cell">${formattedDate}</td>
                    <td>
                        <a href="view-request-detail.php?id=${request.request_id}" class="btn-action btn-view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn-action btn-delete" onclick="deleteRequest(${request.request_id}, '${request.subject}')" title="Delete Request">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            noRequestsContainer.style.display = 'none';
            requestsTableContainer.style.display = 'block';
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
                loadRequests(newPage);
            }
        }

        // Function to refresh requests
        function refreshRequests() {
            loadStats();
            loadRequests(1);
        }

        // Function to update request status
        function updateStatus(requestId, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('request_id', requestId);
            formData.append('status', status);

            fetch('../../backend/admin/managerequests.php', {
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
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error updating status. Please try again.', 'error');
            });
        }

        // Function to delete request
        function deleteRequest(requestId, subject) {
            if (confirm(`Are you sure you want to delete the request "${subject}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('request_id', requestId);

                fetch('../../backend/admin/managerequests.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        loadStats();
                        loadRequests(currentPage);
                    } else {
                        showAlert('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting request. Please try again.', 'error');
                });
            }
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

        // Filter change event
        document.getElementById('statusFilter').addEventListener('change', function() {
            loadRequests(1);
        });
    </script>
</body>
</html>