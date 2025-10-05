-- SQL script to add session_id columns to support anonymous users
-- Run this script on your database to enable anonymous user support

-- Add session_id column to wishlist table
ALTER TABLE wishlist ADD COLUMN session_id VARCHAR(128) NULL AFTER user_id;

-- Add session_id column to cart table  
ALTER TABLE cart ADD COLUMN session_id VARCHAR(128) NULL AFTER user_id;

-- Add indexes for better performance
CREATE INDEX idx_wishlist_session_id ON wishlist(session_id);
CREATE INDEX idx_cart_session_id ON cart(session_id);

-- Add indexes for anonymous user queries
CREATE INDEX idx_wishlist_session_user ON wishlist(session_id, user_id);
CREATE INDEX idx_cart_session_user ON cart(session_id, user_id);