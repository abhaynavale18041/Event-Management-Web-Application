<?php
/**
 * Database connection using PDO
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $connection = null;
    
    /**
     * Get database connection instance
     * @return PDO
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}

/**
 * Helper function to get database connection
 * @return PDO
 */
function getDB() {
    return Database::getConnection();
}

