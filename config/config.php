<?php
/**
 * Application Configuration
 */

// App settings
define('APP_NAME', 'Restaurant Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/restaurant-management/public');
define('TIMEZONE', 'UTC');

// Set timezone
date_default_timezone_set(TIMEZONE);

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_WAITER', 'waiter');
define('ROLE_KITCHEN', 'kitchen');
define('ROLE_CUSTOMER', 'customer');

// Order statuses
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PREPARING', 'preparing');
define('ORDER_STATUS_SERVED', 'served');
define('ORDER_STATUS_PAID', 'paid');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Table statuses
define('TABLE_STATUS_AVAILABLE', 'available');
define('TABLE_STATUS_OCCUPIED', 'occupied');
define('TABLE_STATUS_RESERVED', 'reserved');

// Menu item statuses
define('MENU_STATUS_ACTIVE', 'active');
define('MENU_STATUS_INACTIVE', 'inactive');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Password requirements
define('MIN_PASSWORD_LENGTH', 6);

// Pagination
define('ITEMS_PER_PAGE', 20);

// File upload
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Helper function for debugging
 */
function debug($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

/**
 * Helper function to sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Helper function to get current user role
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Helper function to check role
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Helper function to check multiple roles
 */
function hasAnyRole($roles = []) {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], $roles);
}
?>
