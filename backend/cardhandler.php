<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Get user identifier
 * @return array Array with user information
 */
function getUserIdentifier() {
    $user_id = $_SESSION['user_id'] ?? null;
    return [
        'user_id' => $user_id,
        'is_logged_in' => $user_id !== null
    ];
}

/**
 * Encrypt sensitive card data
 * @param string $data Data to encrypt
 * @return string Encrypted data
 */
function encryptCardData($data) {
    $encryption_key = getenv('CARD_ENCRYPTION_KEY') ?: 'your-secret-encryption-key-change-this';
    $cipher = 'AES-256-CBC';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

/**
 * Decrypt sensitive card data
 * @param string $data Encrypted data
 * @return string Decrypted data
 */
function decryptCardData($data) {
    $encryption_key = getenv('CARD_ENCRYPTION_KEY') ?: 'your-secret-encryption-key-change-this';
    $cipher = 'AES-256-CBC';
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
}

/**
 * Validate credit card number using Luhn algorithm
 * @param string $card_number Card number
 * @return bool True if valid
 */
function validateCardNumber($card_number) {
    $card_number = preg_replace('/\D/', '', $card_number);
    
    if (strlen($card_number) < 13 || strlen($card_number) > 19) {
        return false;
    }
    
    // Luhn algorithm
    $sum = 0;
    $is_even = false;
    
    for ($i = strlen($card_number) - 1; $i >= 0; $i--) {
        $digit = intval($card_number[$i]);
        
        if ($is_even) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        
        $sum += $digit;
        $is_even = !$is_even;
    }
    
    return ($sum % 10) === 0;
}

/**
 * Get card type from card number
 * @param string $card_number Card number
 * @return string Card type
 */
function getCardType($card_number) {
    $card_number = preg_replace('/\D/', '', $card_number);
    
    // Visa
    if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $card_number)) {
        return 'Visa';
    }
    // Mastercard
    if (preg_match('/^5[1-5][0-9]{14}$/', $card_number)) {
        return 'Mastercard';
    }
    // American Express
    if (preg_match('/^3[47][0-9]{13}$/', $card_number)) {
        return 'American Express';
    }
    // Discover
    if (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $card_number)) {
        return 'Discover';
    }
    
    return 'Unknown';
}

/**
 * Validate expiry date
 * @param string $month Expiry month
 * @param string $year Expiry year
 * @return bool True if valid
 */
function validateExpiryDate($month, $year) {
    $month = intval($month);
    $year = intval($year);
    
    if ($month < 1 || $month > 12) {
        return false;
    }
    
    // Convert 2-digit year to 4-digit
    if ($year < 100) {
        $year += 2000;
    }
    
    $current_year = date('Y');
    $current_month = date('n');
    
    if ($year < $current_year || ($year == $current_year && $month < $current_month)) {
        return false;
    }
    
    return true;
}

/**
 * Mask card number for display
 * @param string $card_number Card number
 * @return string Masked card number
 */
function maskCardNumber($card_number) {
    $card_number = preg_replace('/\D/', '', $card_number);
    $length = strlen($card_number);
    
    if ($length < 4) {
        return str_repeat('*', $length);
    }
    
    return str_repeat('*', $length - 4) . substr($card_number, -4);
}

/**
 * Save card details to database using existing cards table
 * @param int $user_id User ID
 * @param array $card_data Card information
 * @return array Response with success status
 */
function saveCardDetails($user_id, $card_data) {
    global $pdo;
    
    try {
        // Validate required fields
        $required_fields = ['card_number', 'expiry_month', 'expiry_year', 'cvv'];
        foreach ($required_fields as $field) {
            if (empty($card_data[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: $field"
                ];
            }
        }
        
        // Clean and validate card number
        $card_number = preg_replace('/\D/', '', $card_data['card_number']);
        if (!validateCardNumber($card_number)) {
            return [
                'success' => false,
                'message' => 'Invalid card number'
            ];
        }
        
        // Validate expiry date
        if (!validateExpiryDate($card_data['expiry_month'], $card_data['expiry_year'])) {
            return [
                'success' => false,
                'message' => 'Invalid or expired date'
            ];
        }
        
        // Validate CVV
        if (!preg_match('/^\d{3,4}$/', $card_data['cvv'])) {
            return [
                'success' => false,
                'message' => 'Invalid CVV'
            ];
        }
        
        $card_brand = getCardType($card_number);
        $last4 = substr($card_number, -4);
        $admin_id = $card_data['admin_id'] ?? null;
        
        // Check if card already exists for this user (using last4 and expiry)
        $checkStmt = $pdo->prepare('SELECT card_id FROM cards WHERE user_id = ? AND last4 = ? AND expiry_month = ? AND expiry_year = ?');
        $checkStmt->execute([$user_id, $last4, $card_data['expiry_month'], $card_data['expiry_year']]);
        
        if ($checkStmt->fetch()) {
            return [
                'success' => false,
                'message' => 'This card is already saved'
            ];
        }
        
        // Generate card token (encrypted card number for security)
        $card_token = encryptCardData($card_number);
        
        // Save card details using existing table structure
        $stmt = $pdo->prepare('
            INSERT INTO cards (
                user_id, admin_id, card_token, last4, card_brand,
                expiry_month, expiry_year, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ');
        
        $stmt->execute([
            $user_id,
            $admin_id,
            $card_token,
            $last4,
            $card_brand,
            $card_data['expiry_month'],
            $card_data['expiry_year']
        ]);
        
        return [
            'success' => true,
            'message' => 'Card saved successfully',
            'card_id' => $pdo->lastInsertId(),
            'card_info' => [
                'last4' => $last4,
                'card_brand' => $card_brand,
                'expiry_month' => $card_data['expiry_month'],
                'expiry_year' => $card_data['expiry_year']
            ]
        ];
        
    } catch (PDOException $e) {
        error_log('Error saving card details: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to save card details'
        ];
    }
}

/**
 * Get user's saved cards using existing cards table
 * @param int $user_id User ID
 * @return array Array of saved cards (without sensitive data)
 */
function getSavedCards($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT card_id, user_id, admin_id, last4, card_brand,
                   expiry_month, expiry_year, created_at
            FROM cards 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$user_id]);
        
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format cards for display
        foreach ($cards as &$card) {
            $card['masked_number'] = '**** **** **** ' . $card['last4'];
            $card['display_name'] = $card['card_brand'] . ' ending in ' . $card['last4'];
        }
        
        return $cards;
        
    } catch (PDOException $e) {
        error_log('Error fetching saved cards: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get card details for payment processing using existing cards table
 * @param int $user_id User ID
 * @param int $card_id Card ID
 * @return array|null Card details with decrypted sensitive data or null if not found
 */
function getCardForPayment($user_id, $card_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT card_id, user_id, admin_id, card_token, last4, card_brand,
                   expiry_month, expiry_year, created_at
            FROM cards 
            WHERE user_id = ? AND card_id = ?
        ');
        $stmt->execute([$user_id, $card_id]);
        
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$card) {
            return null;
        }
        
        // Decrypt card number from token for payment processing
        $card['card_number'] = decryptCardData($card['card_token']);
        $card['card_type'] = $card['card_brand']; // Use card_brand as card_type for compatibility
        
        // Note: CVV is not stored in your existing table for security reasons
        // You'll need to collect CVV from user at payment time
        $card['cvv'] = null; // Will need to be provided during payment
        
        return $card;
        
    } catch (PDOException $e) {
        error_log('Error fetching card for payment: ' . $e->getMessage());
        return null;
    }
}

/**
 * Delete saved card from existing cards table
 * @param int $user_id User ID
 * @param int $card_id Card ID
 * @return array Response with success status
 */
function deleteSavedCard($user_id, $card_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('DELETE FROM cards WHERE user_id = ? AND card_id = ?');
        $stmt->execute([$user_id, $card_id]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Card deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Card not found'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error deleting card: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to delete card'
        ];
    }
}

/**
 * Set default card (using user preference or session)
 * Note: Since the existing cards table doesn't have is_default column,
 * we'll store the default card preference in session or a separate table
 * @param int $user_id User ID
 * @param int $card_id Card ID
 * @return array Response with success status
 */
function setDefaultCard($user_id, $card_id) {
    global $pdo;
    
    try {
        // Verify card belongs to user
        $checkStmt = $pdo->prepare('SELECT card_id FROM cards WHERE user_id = ? AND card_id = ?');
        $checkStmt->execute([$user_id, $card_id]);
        
        if ($checkStmt->fetch()) {
            // Store default card in session for now
            $_SESSION['default_card_id'] = $card_id;
            
            return [
                'success' => true,
                'message' => 'Default card updated successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Card not found'
            ];
        }
        
    } catch (PDOException $e) {
        error_log('Error setting default card: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to update default card'
        ];
    }
}

/**
 * Process payment using saved card or new card details
 * @param int $user_id User ID
 * @param array $payment_data Payment information
 * @return array Payment response
 */
function processPayment($user_id, $payment_data) {
    global $pdo;
    
    try {
        $card_details = null;
        
        if (isset($payment_data['card_id']) && $payment_data['card_id'] > 0) {
            // Using saved card
            $card_details = getCardForPayment($user_id, $payment_data['card_id']);
            if (!$card_details) {
                return [
                    'success' => false,
                    'message' => 'Saved card not found'
                ];
            }
        } else {
            // Using new card details
            $required_fields = ['card_number', 'expiry_month', 'expiry_year', 'cvv', 'cardholder_name'];
            foreach ($required_fields as $field) {
                if (empty($payment_data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ];
                }
            }
            
            // Validate new card
            $card_number = preg_replace('/\D/', '', $payment_data['card_number']);
            if (!validateCardNumber($card_number)) {
                return [
                    'success' => false,
                    'message' => 'Invalid card number'
                ];
            }
            
            if (!validateExpiryDate($payment_data['expiry_month'], $payment_data['expiry_year'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired date'
                ];
            }
            
            $card_details = [
                'card_number' => $card_number,
                'expiry_month' => $payment_data['expiry_month'],
                'expiry_year' => $payment_data['expiry_year'],
                'cvv' => $payment_data['cvv'],
                'cardholder_name' => $payment_data['cardholder_name'],
                'card_type' => getCardType($card_number)
            ];
        }
        
        // Validate amount
        $amount = floatval($payment_data['amount']);
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid payment amount'
            ];
        }
        
        // Create transaction record
        $transaction_id = 'TXN_' . time() . '_' . rand(1000, 9999);
        
        $stmt = $pdo->prepare('
            INSERT INTO transactions (
                user_id, transaction_id, amount, currency, card_type,
                card_last_four, cardholder_name, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ');
        
        $stmt->execute([
            $user_id,
            $transaction_id,
            $amount,
            $payment_data['currency'] ?? 'USD',
            $card_details['card_type'],
            substr($card_details['card_number'], -4),
            $card_details['cardholder_name'],
            'pending'
        ]);
        
        $db_transaction_id = $pdo->lastInsertId();
        
        // Process payment with payment gateway
        $gateway_response = processWithPaymentGateway($card_details, $amount, $transaction_id, $payment_data);
        
        // Update transaction status
        $updateStmt = $pdo->prepare('
            UPDATE transactions 
            SET status = ?, gateway_response = ?, gateway_transaction_id = ?, updated_at = NOW()
            WHERE id = ?
        ');
        
        $updateStmt->execute([
            $gateway_response['success'] ? 'completed' : 'failed',
            json_encode($gateway_response),
            $gateway_response['gateway_transaction_id'] ?? null,
            $db_transaction_id
        ]);
        
        // Save card if requested and payment successful
        if ($gateway_response['success'] && isset($payment_data['save_card']) && $payment_data['save_card'] && !isset($payment_data['card_id'])) {
            saveCardDetails($user_id, [
                'card_number' => $card_details['card_number'],
                'expiry_month' => $card_details['expiry_month'],
                'expiry_year' => $card_details['expiry_year'],
                'cvv' => $card_details['cvv'],
                'cardholder_name' => $card_details['cardholder_name'],
                'billing_address' => $payment_data['billing_address'] ?? null,
                'billing_city' => $payment_data['billing_city'] ?? null,
                'billing_state' => $payment_data['billing_state'] ?? null,
                'billing_zip' => $payment_data['billing_zip'] ?? null,
                'billing_country' => $payment_data['billing_country'] ?? null
            ]);
        }
        
        return [
            'success' => $gateway_response['success'],
            'message' => $gateway_response['message'],
            'transaction_id' => $transaction_id,
            'gateway_transaction_id' => $gateway_response['gateway_transaction_id'] ?? null,
            'amount' => $amount,
            'currency' => $payment_data['currency'] ?? 'USD'
        ];
        
    } catch (Exception $e) {
        error_log('Error processing payment: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Payment processing failed'
        ];
    }
}

/**
 * Process payment with external payment gateway (Stripe, PayPal, etc.)
 * @param array $card_details Card information
 * @param float $amount Payment amount
 * @param string $transaction_id Transaction ID
 * @param array $payment_data Additional payment data
 * @return array Gateway response
 */
function processWithPaymentGateway($card_details, $amount, $transaction_id, $payment_data) {
    // This is a mock implementation - replace with actual payment gateway integration
    
    try {
        // Simulate payment gateway processing
        sleep(1); // Simulate network delay
        
        // Mock validation - in real implementation, send to actual gateway
        $success_rate = 0.95; // 95% success rate for simulation
        $is_successful = (rand(1, 100) / 100) <= $success_rate;
        
        if ($is_successful) {
            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'gateway_transaction_id' => 'GTW_' . time() . '_' . rand(10000, 99999),
                'authorization_code' => 'AUTH_' . rand(100000, 999999)
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Payment declined by gateway',
                'error_code' => 'DECLINED',
                'gateway_transaction_id' => null
            ];
        }
        
        // Real implementation examples:
        
        /*
        // Stripe integration example
        \Stripe\Stripe::setApiKey('your-stripe-secret-key');
        
        $charge = \Stripe\Charge::create([
            'amount' => $amount * 100, // Amount in cents
            'currency' => $payment_data['currency'] ?? 'usd',
            'source' => [
                'object' => 'card',
                'number' => $card_details['card_number'],
                'exp_month' => $card_details['expiry_month'],
                'exp_year' => $card_details['expiry_year'],
                'cvc' => $card_details['cvv']
            ],
            'description' => 'Order payment - ' . $transaction_id
        ]);
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'gateway_transaction_id' => $charge->id,
            'authorization_code' => $charge->outcome['network_status']
        ];
        */
        
        /*
        // PayPal integration example
        $paypal = new \PayPal\Api\Payment();
        // Configure PayPal payment object
        // Process payment
        // Return response
        */
        
    } catch (Exception $e) {
        error_log('Payment gateway error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Gateway communication error',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Get transaction history for user
 * @param int $user_id User ID
 * @param int $limit Number of transactions to return
 * @return array Array of transactions
 */
function getTransactionHistory($user_id, $limit = 50) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT id, transaction_id, amount, currency, card_type, card_last_four,
                   cardholder_name, status, gateway_transaction_id, created_at
            FROM transactions 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ');
        $stmt->execute([$user_id, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Error fetching transaction history: ' . $e->getMessage());
        return [];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $userInfo = getUserIdentifier();
    $user_id = $userInfo['user_id'];
    
    // Most actions require login
    if (!$user_id && !in_array($action, ['validate_card'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Please log in to access card management'
        ]);
        exit;
    }
    
    switch ($action) {
        case 'save_card':
            echo json_encode(saveCardDetails($user_id, $_POST));
            break;
            
        case 'get_saved_cards':
            echo json_encode([
                'success' => true,
                'cards' => getSavedCards($user_id)
            ]);
            break;
            
        case 'delete_card':
            $card_id = intval($_POST['card_id'] ?? 0);
            if ($card_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid card ID'
                ]);
                break;
            }
            echo json_encode(deleteSavedCard($user_id, $card_id));
            break;
            
        case 'set_default_card':
            $card_id = intval($_POST['card_id'] ?? 0);
            if ($card_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid card ID'
                ]);
                break;
            }
            echo json_encode(setDefaultCard($user_id, $card_id));
            break;
            
        case 'process_payment':
            echo json_encode(processPayment($user_id, $_POST));
            break;
            
        case 'validate_card':
            $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
            echo json_encode([
                'success' => true,
                'valid' => validateCardNumber($card_number),
                'card_type' => getCardType($card_number),
                'masked_number' => maskCardNumber($card_number)
            ]);
            break;
            
        case 'get_transactions':
            $limit = intval($_GET['limit'] ?? 50);
            echo json_encode([
                'success' => true,
                'transactions' => getTransactionHistory($user_id, $limit)
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action specified'
            ]);
    }
    
    exit;
}
?>
