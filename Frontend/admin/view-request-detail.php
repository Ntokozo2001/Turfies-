<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Details - Admin Panel</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .back-btn {
            background: #ffbe19;
            color: #1976d2;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .content-area {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .request-header {
            border-bottom: 2px solid #1976d2;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .request-id {
            color: #1976d2;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .request-subject {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }
        .status-new { background: #ffebee; color: #c62828; }
        .status-viewed { background: #fff8e1; color: #f57f17; }
        .status-assisted { background: #e8f5e8; color: #2e7d32; }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .detail-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #1976d2;
        }
        .detail-section h3 {
            color: #1976d2;
            margin: 0 0 15px 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-item {
            margin-bottom: 12px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 4px;
        }
        .detail-value {
            color: #333;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .message-section {
            background: #f8f9fa;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #ffbe19;
            margin-bottom: 30px;
        }
        .message-section h3 {
            color: #1976d2;
            margin: 0 0 20px 0;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            line-height: 1.6;
            font-size: 1rem;
            color: #333;
            white-space: pre-wrap;
        }
        .actions-section {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-status-update {
            background: #1976d2;
            color: #fff;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-contact {
            background: #28a745;
            color: #fff;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .status-update-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .status-update-section h4 {
            color: #1976d2;
            margin: 0 0 15px 0;
        }
        .status-select {
            padding: 10px 15px;
            border: 2px solid #1976d2;
            border-radius: 8px;
            font-size: 1rem;
            margin-right: 15px;
            background: #fff;
        }
        .loading {
            text-align: center;
            padding: 60px;
            color: #1976d2;
        }
        .error-container {
            text-align: center;
            padding: 60px;
            color: #dc3545;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
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
        .datetime-info {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            .actions-section {
                flex-direction: column;
                align-items: stretch;
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
                <h1><i class="fas fa-envelope-open"></i> Request Details</h1>
                <a href="view-requests.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
            
            <div class="content-area">
                <div id="alertContainer"></div>
                
                <div id="loadingContainer" class="loading">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Loading request details...</p>
                </div>
                
                <div id="errorContainer" class="error-container" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                    <h3>Request Not Found</h3>
                    <p>The requested help request could not be found or may have been deleted.</p>
                    <a href="view-requests.php" class="btn-action btn-status-update">Return to Requests</a>
                </div>
                
                <div id="requestDetailsContainer" style="display: none;">
                    <!-- Request Header -->
                    <div class="request-header">
                        <p class="request-id">Request #<span id="requestId"></span></p>
                        <h2 class="request-subject" id="requestSubject"></h2>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                            <span class="status-badge" id="requestStatus"></span>
                            <div class="datetime-info">
                                <i class="fas fa-calendar"></i>
                                <span id="requestDate"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Status Update -->
                    <div class="status-update-section">
                        <h4><i class="fas fa-edit"></i> Update Status</h4>
                        <select id="statusSelect" class="status-select">
                            <option value="new">New</option>
                            <option value="viewed">Viewed</option>
                            <option value="assisted">Assisted</option>
                        </select>
                        <button class="btn-action btn-status-update" onclick="updateRequestStatus()">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                    
                    <!-- Contact Details Grid -->
                    <div class="details-grid">
                        <div class="detail-section">
                            <h3><i class="fas fa-user"></i> Contact Information</h3>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <div class="detail-value" id="contactName"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <div class="detail-value" id="contactEmail"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">WhatsApp Contact:</span>
                                <div class="detail-value" id="whatsappContact"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Contact Type:</span>
                                <div class="detail-value" id="contactType"></div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-info-circle"></i> Request Information</h3>
                            <div class="detail-item">
                                <span class="detail-label">Request ID:</span>
                                <div class="detail-value" id="requestIdDetail"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">User ID:</span>
                                <div class="detail-value" id="userId"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Current Status:</span>
                                <div class="detail-value" id="currentStatus"></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Submitted:</span>
                                <div class="detail-value" id="submittedDate"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message Content -->
                    <div class="message-section">
                        <h3><i class="fas fa-comment-alt"></i> Message Content</h3>
                        <div class="message-content" id="messageContent"></div>
                    </div>
                    
                    <!-- Actions Section -->
                    <div class="actions-section">
                        <button class="btn-action btn-contact" onclick="initiateContact()">
                            <i class="fas fa-phone"></i> Contact Customer
                        </button>
                        <a href="#" class="btn-action btn-contact" id="emailLink">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                        <button class="btn-action btn-delete" onclick="deleteCurrentRequest()">
                            <i class="fas fa-trash"></i> Delete Request
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentRequest = null;

        // Load request details when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const requestId = urlParams.get('id');
            
            if (requestId) {
                loadRequestDetails(requestId);
            } else {
                showError('No request ID provided');
            }
        });

        // Function to load request details
        function loadRequestDetails(requestId) {
            fetch(`../../backend/admin/managerequests.php?action=get&request_id=${requestId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentRequest = data.request;
                        displayRequestDetails(data.request);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Failed to load request details. Please try again.');
                });
        }

        // Function to display request details
        function displayRequestDetails(request) {
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('requestDetailsContainer').style.display = 'block';

            // Header information
            document.getElementById('requestId').textContent = request.request_id;
            document.getElementById('requestSubject').textContent = request.subject;
            
            // Status badge
            const statusBadge = document.getElementById('requestStatus');
            statusBadge.textContent = request.status.charAt(0).toUpperCase() + request.status.slice(1);
            statusBadge.className = `status-badge status-${request.status}`;
            
            // Date formatting
            const createdDate = new Date(request.created_at);
            document.getElementById('requestDate').textContent = 
                createdDate.toLocaleDateString() + ' at ' + createdDate.toLocaleTimeString();

            // Contact information
            const contactName = request.user_name || request.guest_name || 'Anonymous';
            const contactType = request.user_name ? 'Registered User' : 'Guest User';
            const contactEmail = request.guest_email || request.user_email || 'Not provided';
            
            document.getElementById('contactName').textContent = contactName;
            document.getElementById('contactEmail').textContent = contactEmail;
            document.getElementById('whatsappContact').textContent = request.whatsapp_contact || 'Not provided';
            document.getElementById('contactType').textContent = contactType;

            // Request information
            document.getElementById('requestIdDetail').textContent = '#' + request.request_id;
            document.getElementById('userId').textContent = request.user_id || 'N/A (Guest)';
            document.getElementById('currentStatus').textContent = request.status.charAt(0).toUpperCase() + request.status.slice(1);
            document.getElementById('submittedDate').textContent = 
                createdDate.toLocaleDateString() + ' at ' + createdDate.toLocaleTimeString();

            // Message content
            document.getElementById('messageContent').textContent = request.message;

            // Set status selector
            document.getElementById('statusSelect').value = request.status;

            // Set email link
            if (contactEmail && contactEmail !== 'Not provided') {
                const emailSubject = encodeURIComponent(`Re: ${request.subject} (Request #${request.request_id})`);
                const emailBody = encodeURIComponent(`Dear ${contactName},\n\nThank you for contacting us regarding: ${request.subject}\n\nBest regards,\nTurfies Support Team`);
                document.getElementById('emailLink').href = `mailto:${contactEmail}?subject=${emailSubject}&body=${emailBody}`;
            } else {
                document.getElementById('emailLink').style.display = 'none';
            }

            // Automatically mark as viewed if it's new
            if (request.status === 'new') {
                setTimeout(() => {
                    markAsViewed(request.request_id);
                }, 1000);
            }
        }

        // Function to show error
        function showError(message) {
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('errorContainer').style.display = 'block';
        }

        // Function to update request status
        function updateRequestStatus() {
            if (!currentRequest) return;

            const newStatus = document.getElementById('statusSelect').value;
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('request_id', currentRequest.request_id);
            formData.append('status', newStatus);

            fetch('../../backend/admin/managerequests.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Update the current status display
                    currentRequest.status = newStatus;
                    const statusBadge = document.getElementById('requestStatus');
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    statusBadge.className = `status-badge status-${newStatus}`;
                    document.getElementById('currentStatus').textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                } else {
                    showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error updating status. Please try again.', 'error');
            });
        }

        // Function to automatically mark as viewed
        function markAsViewed(requestId) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('request_id', requestId);
            formData.append('status', 'viewed');

            fetch('../../backend/admin/managerequests.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to reflect viewed status
                    currentRequest.status = 'viewed';
                    const statusBadge = document.getElementById('requestStatus');
                    statusBadge.textContent = 'Viewed';
                    statusBadge.className = 'status-badge status-viewed';
                    document.getElementById('statusSelect').value = 'viewed';
                    document.getElementById('currentStatus').textContent = 'Viewed';
                }
            })
            .catch(error => {
                console.error('Error auto-marking as viewed:', error);
            });
        }

        // Function to initiate contact
        function initiateContact() {
            if (!currentRequest) return;

            const whatsapp = currentRequest.whatsapp_contact;
            if (whatsapp && whatsapp !== 'Not provided') {
                const message = encodeURIComponent(`Hello ${currentRequest.user_name || currentRequest.guest_name}, thank you for contacting Turfies regarding: ${currentRequest.subject}. How can I assist you further?`);
                window.open(`https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${message}`, '_blank');
            } else {
                showAlert('No WhatsApp contact information available', 'error');
            }
        }

        // Function to delete current request
        function deleteCurrentRequest() {
            if (!currentRequest) return;

            if (confirm(`Are you sure you want to delete this request "${currentRequest.subject}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('request_id', currentRequest.request_id);

                fetch('../../backend/admin/managerequests.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'view-requests.php';
                        }, 2000);
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
    </script>
</body>
</html>