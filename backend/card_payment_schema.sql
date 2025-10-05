-- SQL schema for card handling and payment processing
-- Run this script on your database to create the necessary tables

-- Create saved_cards table
CREATE TABLE saved_cards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_number_encrypted TEXT NOT NULL,
    card_number_masked VARCHAR(20) NOT NULL,
    card_number_hash VARCHAR(64) NOT NULL,
    expiry_month TINYINT NOT NULL,
    expiry_year SMALLINT NOT NULL,
    cvv_encrypted TEXT NOT NULL,
    cardholder_name VARCHAR(100) NOT NULL,
    card_type VARCHAR(50) NOT NULL,
    billing_address TEXT NULL,
    billing_city VARCHAR(100) NULL,
    billing_state VARCHAR(100) NULL,
    billing_zip VARCHAR(20) NULL,
    billing_country VARCHAR(100) NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_card_hash (card_number_hash),
    INDEX idx_default_card (user_id, is_default)
);

-- Create transactions table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_id VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    card_type VARCHAR(50) NULL,
    card_last_four VARCHAR(4) NULL,
    cardholder_name VARCHAR(100) NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    gateway_response TEXT NULL,
    gateway_transaction_id VARCHAR(100) NULL,
    order_id VARCHAR(50) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_gateway_transaction_id (gateway_transaction_id),
    INDEX idx_order_id (order_id)
);

-- Create payment_methods table (for different payment options)
CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type ENUM('card', 'bank', 'wallet', 'crypto') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    config JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default payment methods
INSERT INTO payment_methods (name, type) VALUES 
('Credit Card', 'card'),
('Debit Card', 'card'),
('PayPal', 'wallet'),
('Bank Transfer', 'bank');

-- Create orders table (if not exists) for linking transactions to orders
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    shipping_address TEXT NULL,
    billing_address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
);

-- Create order_items table for order line items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Add foreign key constraints (if users table exists)
-- ALTER TABLE saved_cards ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
-- ALTER TABLE transactions ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
-- ALTER TABLE orders ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

-- Add transaction to order relationship
ALTER TABLE transactions ADD COLUMN order_id INT NULL;
ALTER TABLE transactions ADD INDEX idx_order_transaction (order_id);
-- ALTER TABLE transactions ADD FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE SET NULL;