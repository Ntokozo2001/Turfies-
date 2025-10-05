<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
        .settings-form {
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #1976d2;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn-save {
            background: #1976d2;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-save:hover {
            background: #0d47a1;
        }
        .btn-backup {
            background: #ff9800;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-left: 10px;
        }
        .btn-backup:hover {
            background: #f57c00;
        }
        .btn-reset {
            background: #f44336;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-left: 10px;
        }
        .btn-reset:hover {
            background: #d32f2f;
        }
        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #1976d2;
        }
        .settings-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .settings-section h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
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
                <h1>Settings</h1>
            </div>
            
            <div class="content-area">
                <div id="loadingContainer" style="display: none; text-align: center; padding: 40px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading settings...</p>
                </div>

                <div id="alertContainer"></div>

                <form class="settings-form" id="settingsForm">
                    <div class="settings-section">
                        <h3>General Settings</h3>
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" value="">
                        </div>
                        <div class="form-group">
                            <label for="site_description">Site Description</label>
                            <textarea id="site_description" name="site_description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" id="admin_email" name="admin_email" value="">
                        </div>
                        <div class="form-group">
                            <label for="maintenance_mode">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                Maintenance Mode
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="user_registration">
                                <input type="checkbox" id="user_registration" name="user_registration">
                                Allow User Registration
                            </label>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Payment Settings</h3>
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency">
                                <option value="ZAR">South African Rand (ZAR)</option>
                                <option value="USD">US Dollar (USD)</option>
                                <option value="EUR">Euro (EUR)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tax_rate">Tax Rate (%)</label>
                            <input type="number" id="tax_rate" name="tax_rate" value="" step="0.01" min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Notification Settings</h3>
                        <div class="form-group">
                            <label for="email_notifications">
                                <input type="checkbox" id="email_notifications" name="email_notifications">
                                Enable Email Notifications
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="sms_notifications">
                                <input type="checkbox" id="sms_notifications" name="sms_notifications">
                                Enable SMS Notifications
                            </label>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Security Settings</h3>
                        <div class="form-group">
                            <label for="session_timeout">Session Timeout (minutes)</label>
                            <input type="number" id="session_timeout" name="session_timeout" value="" min="1" max="1440">
                        </div>
                        <div class="form-group">
                            <label for="max_login_attempts">Max Login Attempts</label>
                            <input type="number" id="max_login_attempts" name="max_login_attempts" value="" min="1" max="20">
                        </div>
                        <div class="form-group">
                            <label for="default_user_role">Default User Role</label>
                            <select id="default_user_role" name="default_user_role">
                                <option value="user">User</option>
                                <option value="subscriber">Subscriber</option>
                                <option value="contributor">Contributor</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <span id="saveText">Save Settings</span>
                            <span id="saveSpinner" style="display: none;">Saving...</span>
                        </button>
                        <button type="button" class="btn-backup" onclick="backupSettings()">
                            Backup Settings
                        </button>
                        <button type="button" class="btn-reset" onclick="resetToDefaults()">
                            Reset to Defaults
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <footer class="main-footer">
        <?php include '../footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAllSettings();
        });

        // Load all settings from backend
        async function loadAllSettings() {
            showLoading(true);
            try {
                const response = await fetch('../../backend/admin/settings.php?action=get_all');
                const data = await response.json();
                
                if (data.success) {
                    populateForm(data.settings);
                } else {
                    showAlert('error', data.message || 'Failed to load settings');
                }
            } catch (error) {
                console.error('Error loading settings:', error);
                showAlert('error', 'Error loading settings. Please try again.');
            } finally {
                showLoading(false);
            }
        }

        // Populate form with settings data
        function populateForm(settings) {
            Object.keys(settings).forEach(key => {
                const element = document.getElementById(key);
                if (element) {
                    const value = settings[key];
                    
                    if (element.type === 'checkbox') {
                        element.checked = Boolean(value);
                    } else {
                        element.value = value;
                    }
                }
            });
        }

        // Handle form submission
        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSettings();
        });

        // Save settings
        async function saveSettings() {
            const saveBtn = document.querySelector('.btn-save');
            const saveText = document.getElementById('saveText');
            const saveSpinner = document.getElementById('saveSpinner');
            
            saveBtn.disabled = true;
            saveText.style.display = 'none';
            saveSpinner.style.display = 'inline';

            try {
                const formData = new FormData(document.getElementById('settingsForm'));
                const settings = {};
                
                // Convert form data to settings object with types
                for (let [key, value] of formData.entries()) {
                    const element = document.getElementById(key);
                    let type = 'string';
                    let processedValue = value;
                    
                    if (element) {
                        if (element.type === 'checkbox') {
                            type = 'boolean';
                            processedValue = element.checked;
                        } else if (element.type === 'number') {
                            type = 'number';
                            processedValue = parseFloat(value) || 0;
                        }
                    }
                    
                    settings[key] = {
                        value: processedValue,
                        type: type
                    };
                }
                
                // Handle checkboxes that aren't in FormData when unchecked
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    if (!settings[checkbox.name]) {
                        settings[checkbox.name] = {
                            value: checkbox.checked,
                            type: 'boolean'
                        };
                    }
                });

                console.log('Sending settings:', settings);

                const response = await fetch('../../backend/admin/settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update',
                        ...settings
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', 'Settings saved successfully!');
                } else {
                    showAlert('error', data.message || 'Failed to save settings');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                showAlert('error', 'Error saving settings. Please try again.');
            } finally {
                saveBtn.disabled = false;
                saveText.style.display = 'inline';
                saveSpinner.style.display = 'none';
            }
        }

        // Backup settings
        async function backupSettings() {
            try {
                const response = await fetch('../../backend/admin/settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=backup'
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', `Settings backed up successfully! File: ${data.filename}`);
                } else {
                    showAlert('error', data.message || 'Failed to backup settings');
                }
            } catch (error) {
                console.error('Error backing up settings:', error);
                showAlert('error', 'Error creating backup. Please try again.');
            }
        }

        // Reset to defaults
        async function resetToDefaults() {
            if (!confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) {
                return;
            }

            try {
                // First backup current settings
                await backupSettings();
                
                // Then reload the page to get fresh defaults
                showAlert('info', 'Resetting to defaults...');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                
            } catch (error) {
                console.error('Error resetting settings:', error);
                showAlert('error', 'Error resetting settings. Please try again.');
            }
        }

        // Show/hide loading
        function showLoading(show) {
            const loadingContainer = document.getElementById('loadingContainer');
            const form = document.getElementById('settingsForm');
            
            if (show) {
                loadingContainer.style.display = 'block';
                form.style.display = 'none';
            } else {
                loadingContainer.style.display = 'none';
                form.style.display = 'block';
            }
        }

        // Show alert messages
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-error' : 'alert-info';
            
            const alertHTML = `
                <div class="alert ${alertClass}" role="alert">
                    ${message}
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                </div>
            `;
            
            alertContainer.innerHTML = alertHTML;
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) alert.remove();
                }, 5000);
            }
        }

        // Auto-save functionality (optional)
        let autoSaveTimeout;
        function scheduleAutoSave() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                saveSettings();
            }, 2000); // Auto-save after 2 seconds of inactivity
        }

        // Add auto-save listeners to form elements
        document.querySelectorAll('#settingsForm input, #settingsForm select, #settingsForm textarea').forEach(element => {
            element.addEventListener('change', scheduleAutoSave);
        });
    </script>
</body>
</html>