-- Restaurant Management System - Database Schema
-- MySQL Database

-- Drop existing database (if needed)
-- DROP DATABASE IF EXISTS restaurant_management;

-- Create database
CREATE DATABASE IF NOT EXISTS restaurant_management;
USE restaurant_management;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'waiter', 'kitchen', 'customer') NOT NULL DEFAULT 'customer',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- ============================================
-- RESTAURANT TABLES
-- ============================================
CREATE TABLE IF NOT EXISTS tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL DEFAULT 4,
    status ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_table_number (table_number)
);

-- ============================================
-- MENU ITEMS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(255),
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    preparation_time INT COMMENT 'Time in minutes',
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_available (is_available)
);

-- ============================================
-- ORDERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT NOT NULL,
    customer_id INT,
    waiter_id INT,
    order_number VARCHAR(50) UNIQUE,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    payment_method VARCHAR(50),
    status ENUM('pending', 'preparing', 'served', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT,
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    served_time TIMESTAMP NULL,
    completed_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (waiter_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_table_id (table_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_time (order_time),
    INDEX idx_order_number (order_number)
);

-- ============================================
-- ORDER ITEMS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'preparing', 'ready', 'served') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
);

-- ============================================
-- INVENTORY TABLE (For Ingredients)
-- ============================================
CREATE TABLE IF NOT EXISTS inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    ingredient_name VARCHAR(100) NOT NULL,
    quantity_used DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50) NOT NULL COMMENT 'e.g., grams, ml, pieces',
    minimum_stock DECIMAL(10, 2),
    reorder_level DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_item_ingredient (menu_item_id, ingredient_name)
);

-- ============================================
-- INVOICES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL UNIQUE,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_payment_status (payment_status)
);

-- ============================================
-- TABLE RESERVATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT NOT NULL,
    customer_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(15) NOT NULL,
    customer_email VARCHAR(100),
    reservation_date DATETIME NOT NULL,
    duration INT COMMENT 'Duration in minutes',
    number_of_guests INT NOT NULL,
    status ENUM('confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'confirmed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reservation_date (reservation_date),
    INDEX idx_status (status)
);

-- ============================================
-- ACTIVITY LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    details JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Admin user
INSERT INTO users (name, email, password_hash, role, status) VALUES 
('Admin User', 'admin@restaurant.local', '$2y$10$8kvH15fqIK319KUsej6gQOCW.9UB.pdYxbNhpJjEEolRR5kHY5GPi', 'admin', 'active'),
('Waiter Staff', 'waiter@restaurant.local', '$2y$10$8kvH15fqIK319KUsej6gQOCW.9UB.pdYxbNhpJjEEolRR5kHY5GPi', 'waiter', 'active'),
('Kitchen Staff', 'kitchen@restaurant.local', '$2y$10$8kvH15fqIK319KUsej6gQOCW.9UB.pdYxbNhpJjEEolRR5kHY5GPi', 'kitchen', 'active'),
('John Customer', 'john@customer.local', '$2y$10$8kvH15fqIK319KUsej6gQOCW.9UB.pdYxbNhpJjEEolRR5kHY5GPi', 'customer', 'active');

-- Tables
INSERT INTO tables (table_number, capacity, status) VALUES 
(1, 2, 'available'),
(2, 2, 'available'),
(3, 4, 'available'),
(4, 4, 'available'),
(5, 6, 'available'),
(6, 6, 'available'),
(7, 8, 'available'),
(8, 8, 'available');

-- Menu Items
INSERT INTO menu_items (name, description, price, category, status, preparation_time) VALUES 
-- Appetizers
('Bruschetta', 'Toasted bread with tomatoes and garlic', 5.99, 'appetizers', 'active', 5),
('Spring Rolls', 'Vietnamese style spring rolls with dipping sauce', 4.99, 'appetizers', 'active', 8),

-- Main Courses
('Grilled Chicken', 'Herb marinated grilled chicken with vegetables', 12.99, 'mains', 'active', 15),
('Pasta Carbonara', 'Classic Italian pasta with cream sauce', 11.99, 'mains', 'active', 12),
('Beef Steak', 'Premium beef steak served with sides', 18.99, 'mains', 'active', 18),
('Salmon Fillet', 'Grilled salmon with lemon butter sauce', 16.99, 'mains', 'active', 15),

-- Vegetarian
('Vegetable Biryani', 'Aromatic vegetarian biryani rice', 9.99, 'vegetarian', 'active', 14),
('Paneer Butter Masala', 'Indian cottage cheese in tomato gravy', 10.99, 'vegetarian', 'active', 12),

-- Desserts
('Cheesecake', 'New York style cheesecake', 6.99, 'desserts', 'active', 2),
('Chocolate Brownie', 'Rich chocolate brownie with ice cream', 5.99, 'desserts', 'active', 3),

-- Beverages
('Soft Drink', 'Various soft drinks', 2.99, 'beverages', 'active', 1),
('Coffee', 'Hot coffee', 2.49, 'beverages', 'active', 3),
('Fresh Juice', 'Freshly squeezed juice', 3.99, 'beverages', 'active', 2);

-- ============================================
-- SAMPLE QUERIES FOR REPORTS
-- ============================================

-- Revenue by date
-- SELECT DATE(order_time) as date, SUM(total_amount) as daily_revenue FROM orders WHERE status = 'paid' GROUP BY DATE(order_time);

-- Top selling items
-- SELECT mi.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id GROUP BY mi.id ORDER BY total_sold DESC;

-- Table occupancy
-- SELECT t.table_number, t.capacity, t.status FROM tables t;

-- Pending orders
-- SELECT o.id, o.order_number, t.table_number, o.order_time FROM orders o JOIN tables t ON o.table_id = t.id WHERE o.status IN ('pending', 'preparing') ORDER BY o.order_time ASC;
