<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User - Admin Panel</title>
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
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .user-table th {
            background: #1976d2;
            color: #fff;
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
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
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
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group input:focus {
            border-color: #1976d2;
            outline: none;
        }
        .btn-save {
            background: #1976d2;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-cancel {
            background: #6c757d;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #1976d2;
        }
        .no-users {
            text-align: center;
            padding: 40px;
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
                <h1>Manage User</h1>
            </div>
            
            <div class="content-area">
                <h3>User Management</h3>
                <p>View, edit, and manage all registered users.</p>
                
                <div id="alertContainer"></div>
                
                <div id="loadingContainer" class="loading">
                    <p>Loading users...</p>
                </div>
                
                <div id="userTableContainer" style="display: none;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>School</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Users will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
                
                <div id="noUsersContainer" class="no-users" style="display: none;">
                    <p>No users found in the system.</p>
                </div>
            </div>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>
    
    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="form-group">
                    <label for="editFullName">Full Name:</label>
                    <input type="text" id="editFullName" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="editSchool">School:</label>
                    <input type="text" id="editSchool" name="school" required>
                </div>
                
                <div class="form-group">
                    <label for="editWhatsapp">WhatsApp Number:</label>
                    <input type="tel" id="editWhatsapp" name="whatsapp_number" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load users when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Function to load all users
        function loadUsers() {
            fetch('../../backend/admin/manageusers.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUsers(data.users);
                    } else {
                        showAlert('Error loading users: ' + data.message, 'error');
                        document.getElementById('loadingContainer').style.display = 'none';
                        document.getElementById('noUsersContainer').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading users. Please try again.', 'error');
                    document.getElementById('loadingContainer').style.display = 'none';
                });
        }

        // Function to display users in the table
        function displayUsers(users) {
            const tableBody = document.getElementById('userTableBody');
            const loadingContainer = document.getElementById('loadingContainer');
            const userTableContainer = document.getElementById('userTableContainer');
            const noUsersContainer = document.getElementById('noUsersContainer');

            loadingContainer.style.display = 'none';

            if (users.length === 0) {
                noUsersContainer.style.display = 'block';
                return;
            }

            tableBody.innerHTML = '';
            users.forEach(user => {
                const row = document.createElement('tr');
                const registrationDate = new Date(user.created_at).toLocaleDateString();
                
                row.innerHTML = `
                    <td>${user.user_id}</td>
                    <td>${user.full_name}</td>
                    <td>${user.email}</td>
                    <td>${user.school}</td>
                    <td>${registrationDate}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editUser(${user.user_id})">Edit</button>
                        <button class="btn-action btn-delete" onclick="deleteUser(${user.user_id}, '${user.full_name}')">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            userTableContainer.style.display = 'block';
        }

        // Function to edit user
        function editUser(userId) {
            fetch(`../../backend/admin/manageusers.php?action=get&user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.user;
                        document.getElementById('editUserId').value = user.user_id;
                        document.getElementById('editFullName').value = user.full_name;
                        document.getElementById('editEmail').value = user.email;
                        document.getElementById('editSchool').value = user.school;
                        document.getElementById('editWhatsapp').value = user.whatsapp_number || '';
                        document.getElementById('editUserModal').style.display = 'block';
                    } else {
                        showAlert('Error loading user data: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading user data. Please try again.', 'error');
                });
        }

        // Function to close edit modal
        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        // Handle edit form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update');

            fetch('../../backend/admin/manageusers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeEditModal();
                    loadUsers(); // Reload the user list
                } else {
                    showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error updating user. Please try again.', 'error');
            });
        });

        // Function to delete user
        function deleteUser(userId, userName) {
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('user_id', userId);

                fetch('../../backend/admin/manageusers.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        loadUsers(); // Reload the user list
                    } else {
                        showAlert('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting user. Please try again.', 'error');
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
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editUserModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>