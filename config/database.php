<?php
/**
 * Database Configuration & Connection
 * Using PDO for secure database operations
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurant_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

/**
 * Helper function to execute queries with prepared statements
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        throw new Exception("Query Error: " . $e->getMessage());
    }
}

/**
 * Helper function to fetch data
 */
function fetchAll($sql, $params = []) {
    return executeQuery($sql, $params)->fetchAll();
}

/**
 * Helper function to fetch one row
 */
function fetchOne($sql, $params = []) {
    return executeQuery($sql, $params)->fetch();
}

/**
 * Helper function to get last insert ID
 */
function getLastInsertId() {
    global $pdo;
    return $pdo->lastInsertId();
}

/**
 * Helper function to check if record exists
 */
function recordExists($table, $column, $value) {
    $result = fetchOne("SELECT 1 FROM $table WHERE $column = ?", [$value]);
    return !empty($result);
}
?>
