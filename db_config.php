<?php
// db_config.php

class Database {
    // Database Configuration
    private const HOST = "localhost";
    private const DBNAME = "chatbot_db";
    private const USERNAME = "root";
    private const PASSWORD = "";
    private const CHARSET = "utf8mb4";

    // Image Paths
    public const PRODUCT_IMAGE_PATH = "/TrialMart/admin/uploads/products/";
    public const DEFAULT_IMAGE = "/TrialMart/img/default-product.jpg";

    private static $pdo = null;
    private static $mysqli = null;

    /**
     * Get PDO Connection
     * @return PDO
     */
    public static function getPDO() {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DBNAME . ";charset=" . self::CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                self::$pdo = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
            } catch (PDOException $e) {
                self::handleError("PDO Connection Error: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /**
     * Get MySQLi Connection
     * @return mysqli
     */
    public static function getMysqli() {
        if (self::$mysqli === null) {
            try {
                self::$mysqli = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DBNAME);
                
                if (self::$mysqli->connect_error) {
                    throw new Exception("MySQLi Connection Error: " . self::$mysqli->connect_error);
                }

                self::$mysqli->set_charset(self::CHARSET);
            } catch (Exception $e) {
                self::handleError($e->getMessage());
            }
        }
        return self::$mysqli;
    }

    /**
     * Handle database errors
     * @param string $error
     */
    private static function handleError($error) {
        error_log($error); // Log error

        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            die($error); // Display error in development mode
        } else {
            die("A database error occurred. Please try again later.");
        }
    }

    /**
     * Close all database connections
     */
    public static function closeConnections() {
        if (self::$pdo !== null) {
            self::$pdo = null;
        }
        if (self::$mysqli !== null) {
            self::$mysqli->close();
            self::$mysqli = null;
        }
    }

    /**
     * Execute a query using PDO
     * @param string $sql
     * @param array $params
     * @return PDOStatement|null
     */
    public static function queryPDO($sql, $params = []) {
        try {
            $stmt = self::getPDO()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            self::handleError("Query Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Transaction Handling
     */
    public static function beginTransaction() { self::getPDO()->beginTransaction(); }
    public static function commit() { self::getPDO()->commit(); }
    public static function rollback() { self::getPDO()->rollBack(); }

    /**
     * Get Product Image URL
     * @param string|null $image_name
     * @return string
     */
    public static function getProductImageUrl($image_name) {
        return empty($image_name) ? self::DEFAULT_IMAGE : self::PRODUCT_IMAGE_PATH . $image_name;
    }
}

// Set environment
define('ENVIRONMENT', 'development'); // Change to 'production' for live site

// Create global connection variables for backward compatibility
$conn = Database::getMysqli();
$pdo = Database::getPDO();

// Register shutdown function to close connections
register_shutdown_function([Database::class, 'closeConnections']);

/**
 * Legacy function for backward compatibility
 * @return PDO
 */
function connectPDO() {
    return Database::getPDO();
}

// Set timezone
date_default_timezone_set('Asia/Manila');

// Set error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
