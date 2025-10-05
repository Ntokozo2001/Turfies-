<?php
session_start();
require_once __DIR__ . '/../db.php';

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
try {
    $testStmt = $pdo->query("SELECT 1");
    error_log("Database connection successful for settings management");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Create settings table if it doesn't exist
function createSettingsTable($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS site_settings (
            setting_id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
            category VARCHAR(50) DEFAULT 'general',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        error_log("Settings table created or already exists");
        
        // Insert default settings if table is empty
        $checkStmt = $pdo->query("SELECT COUNT(*) as count FROM site_settings");
        $count = $checkStmt->fetch()['count'];
        
        if ($count == 0) {
            insertDefaultSettings($pdo);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error creating settings table: " . $e->getMessage());
        return false;
    }
}

// Insert default settings
function insertDefaultSettings($pdo) {
    try {
        $defaultSettings = [
            // General Settings
            ['site_name', 'Turfies Exam Care', 'string', 'general', 'Website name'],
            ['site_description', 'Your trusted partner for exam preparation and success.', 'string', 'general', 'Website description'],
            ['admin_email', 'admin@turfies.com', 'string', 'general', 'Administrator email address'],
            
            // Payment Settings
            ['currency', 'ZAR', 'string', 'payment', 'Default currency'],
            ['tax_rate', '15.00', 'number', 'payment', 'Tax rate percentage'],
            
            // Notification Settings
            ['email_notifications', '1', 'boolean', 'notifications', 'Enable email notifications'],
            ['sms_notifications', '0', 'boolean', 'notifications', 'Enable SMS notifications'],
            
            // Security Settings
            ['session_timeout', '30', 'number', 'security', 'Session timeout in minutes'],
            ['max_login_attempts', '5', 'number', 'security', 'Maximum login attempts before lockout'],
            
            // Additional Settings
            ['maintenance_mode', '0', 'boolean', 'general', 'Enable maintenance mode'],
            ['user_registration', '1', 'boolean', 'general', 'Allow user registration'],
            ['default_user_role', 'user', 'string', 'security', 'Default role for new users']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, category, description) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        
        error_log("Default settings inserted successfully");
        return true;
    } catch (PDOException $e) {
        error_log("Error inserting default settings: " . $e->getMessage());
        return false;
    }
}

// Get all settings
function getAllSettings($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT setting_key, setting_value, setting_type, category FROM site_settings ORDER BY category, setting_key");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];
            
            // Convert value based on type
            switch ($row['setting_type']) {
                case 'boolean':
                    $value = (bool)$value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (float)$value : 0;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
                default:
                    // string - keep as is
                    break;
            }
            
            $settings[$row['setting_key']] = $value;
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Error getting all settings: " . $e->getMessage());
        return false;
    }
}

// Get settings by category
function getSettingsByCategory($pdo, $category) {
    try {
        $stmt = $pdo->prepare("SELECT setting_key, setting_value, setting_type FROM site_settings WHERE category = ? ORDER BY setting_key");
        $stmt->execute([$category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];
            
            // Convert value based on type
            switch ($row['setting_type']) {
                case 'boolean':
                    $value = (bool)$value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (float)$value : 0;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            $settings[$row['setting_key']] = $value;
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Error getting settings by category: " . $e->getMessage());
        return false;
    }
}

// Get single setting
function getSetting($pdo, $key) {
    try {
        $stmt = $pdo->prepare("SELECT setting_value, setting_type FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        $value = $result['setting_value'];
        
        // Convert value based on type
        switch ($result['setting_type']) {
            case 'boolean':
                return (bool)$value;
            case 'number':
                return is_numeric($value) ? (float)$value : 0;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    } catch (PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return null;
    }
}

// Update single setting
function updateSetting($pdo, $key, $value, $type = 'string') {
    try {
        // Convert value to string for storage
        $storageValue = $value;
        if ($type === 'boolean') {
            $storageValue = $value ? '1' : '0';
        } elseif ($type === 'json') {
            $storageValue = json_encode($value);
        }
        
        // Check if setting exists
        $checkStmt = $pdo->prepare("SELECT setting_id FROM site_settings WHERE setting_key = ?");
        $checkStmt->execute([$key]);
        
        if ($checkStmt->fetch()) {
            // Update existing setting
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ?, setting_type = ? WHERE setting_key = ?");
            $result = $stmt->execute([$storageValue, $type, $key]);
        } else {
            // Insert new setting
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)");
            $result = $stmt->execute([$key, $storageValue, $type]);
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

// Update multiple settings
function updateMultipleSettings($pdo, $settings) {
    try {
        $pdo->beginTransaction();
        
        $success = true;
        $errors = [];
        
        foreach ($settings as $key => $data) {
            $value = $data['value'] ?? $data;
            $type = $data['type'] ?? 'string';
            
            if (!updateSetting($pdo, $key, $value, $type)) {
                $success = false;
                $errors[] = "Failed to update setting: $key";
            }
        }
        
        if ($success) {
            $pdo->commit();
            return ['success' => true, 'message' => 'Settings updated successfully'];
        } else {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Failed to update some settings', 'errors' => $errors];
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating multiple settings: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Delete setting
function deleteSetting($pdo, $key) {
    try {
        $stmt = $pdo->prepare("DELETE FROM site_settings WHERE setting_key = ?");
        $result = $stmt->execute([$key]);
        
        return $result && $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error deleting setting: " . $e->getMessage());
        return false;
    }
}

// Backup settings
function backupSettings($pdo) {
    try {
        $settings = getAllSettings($pdo);
        if ($settings === false) {
            return false;
        }
        
        $backup = [
            'timestamp' => date('Y-m-d H:i:s'),
            'settings' => $settings
        ];
        
        $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = __DIR__ . '/../../backups/' . $filename;
        
        // Create backups directory if it doesn't exist
        $backupDir = dirname($filepath);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $result = file_put_contents($filepath, json_encode($backup, JSON_PRETTY_PRINT));
        
        if ($result !== false) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        } else {
            return ['success' => false, 'message' => 'Failed to write backup file'];
        }
        
    } catch (Exception $e) {
        error_log("Error backing up settings: " . $e->getMessage());
        return ['success' => false, 'message' => 'Backup failed'];
    }
}

// Restore settings from backup
function restoreSettings($pdo, $backupFile) {
    try {
        if (!file_exists($backupFile)) {
            return ['success' => false, 'message' => 'Backup file not found'];
        }
        
        $backup = json_decode(file_get_contents($backupFile), true);
        if (!$backup || !isset($backup['settings'])) {
            return ['success' => false, 'message' => 'Invalid backup file format'];
        }
        
        $pdo->beginTransaction();
        
        // Clear existing settings
        $pdo->exec("DELETE FROM site_settings");
        
        // Restore settings
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)");
        
        foreach ($backup['settings'] as $key => $value) {
            $type = 'string';
            $storageValue = $value;
            
            if (is_bool($value)) {
                $type = 'boolean';
                $storageValue = $value ? '1' : '0';
            } elseif (is_numeric($value)) {
                $type = 'number';
            } elseif (is_array($value)) {
                $type = 'json';
                $storageValue = json_encode($value);
            }
            
            $stmt->execute([$key, $storageValue, $type]);
        }
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Settings restored successfully'];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error restoring settings: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to restore settings'];
    }
}

// Initialize settings table
createSettingsTable($pdo);

// Handle requests
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    error_log("Settings management request - Method: $method, Action: $action");

    switch ($action) {
        case 'get_all':
            if ($method === 'GET') {
                $settings = getAllSettings($pdo);
                if ($settings !== false) {
                    echo json_encode(['success' => true, 'settings' => $settings]);
                } else {
                    throw new Exception("Failed to retrieve settings");
                }
            } else {
                throw new Exception("Invalid request method for getting all settings");
            }
            break;
            
        case 'get_category':
            if ($method === 'GET') {
                $category = $_GET['category'] ?? '';
                if (empty($category)) {
                    throw new Exception("Category is required");
                }
                
                $settings = getSettingsByCategory($pdo, $category);
                if ($settings !== false) {
                    echo json_encode(['success' => true, 'settings' => $settings]);
                } else {
                    throw new Exception("Failed to retrieve settings for category: $category");
                }
            } else {
                throw new Exception("Invalid request method for getting category settings");
            }
            break;
            
        case 'get_single':
            if ($method === 'GET') {
                $key = $_GET['key'] ?? '';
                if (empty($key)) {
                    throw new Exception("Setting key is required");
                }
                
                $value = getSetting($pdo, $key);
                if ($value !== null) {
                    echo json_encode(['success' => true, 'value' => $value]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Setting not found']);
                }
            } else {
                throw new Exception("Invalid request method for getting single setting");
            }
            break;
            
        case 'update':
            if ($method === 'POST') {
                $settingsData = json_decode(file_get_contents('php://input'), true);
                
                if (empty($settingsData)) {
                    // Try to get from POST data
                    $settingsData = [];
                    foreach ($_POST as $key => $value) {
                        if ($key !== 'action') {
                            $settingsData[$key] = ['value' => $value, 'type' => 'string'];
                        }
                    }
                }
                
                if (empty($settingsData)) {
                    throw new Exception("No settings data provided");
                }
                
                error_log("Updating settings: " . json_encode($settingsData));
                
                $result = updateMultipleSettings($pdo, $settingsData);
                echo json_encode($result);
            } else {
                throw new Exception("Invalid request method for updating settings");
            }
            break;
            
        case 'update_single':
            if ($method === 'POST') {
                $key = $_POST['key'] ?? '';
                $value = $_POST['value'] ?? '';
                $type = $_POST['type'] ?? 'string';
                
                if (empty($key)) {
                    throw new Exception("Setting key is required");
                }
                
                if (updateSetting($pdo, $key, $value, $type)) {
                    echo json_encode(['success' => true, 'message' => 'Setting updated successfully']);
                } else {
                    throw new Exception("Failed to update setting");
                }
            } else {
                throw new Exception("Invalid request method for updating single setting");
            }
            break;
            
        case 'delete':
            if ($method === 'POST') {
                $key = $_POST['key'] ?? '';
                if (empty($key)) {
                    throw new Exception("Setting key is required");
                }
                
                if (deleteSetting($pdo, $key)) {
                    echo json_encode(['success' => true, 'message' => 'Setting deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Setting not found or could not be deleted']);
                }
            } else {
                throw new Exception("Invalid request method for deleting setting");
            }
            break;
            
        case 'backup':
            if ($method === 'POST') {
                $result = backupSettings($pdo);
                echo json_encode($result);
            } else {
                throw new Exception("Invalid request method for backup");
            }
            break;
            
        case 'restore':
            if ($method === 'POST') {
                $backupFile = $_POST['backup_file'] ?? '';
                if (empty($backupFile)) {
                    throw new Exception("Backup file path is required");
                }
                
                $result = restoreSettings($pdo, $backupFile);
                echo json_encode($result);
            } else {
                throw new Exception("Invalid request method for restore");
            }
            break;
            
        case 'test':
            echo json_encode(['success' => true, 'message' => 'Settings backend is working', 'method' => $method]);
            break;
            
        default:
            throw new Exception("Invalid or missing action parameter");
    }

} catch (Exception $e) {
    error_log("Settings management error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
