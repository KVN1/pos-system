<?php
// database.php
class Database {
    private static $host     = null;
    private static $dbname   = null;
    private static $username = null;
    private static $password = null;
    private static $port     = '3306';
    private static $conn     = null;

    public static function getConnection() {
        if (self::$conn === null) {
            // Read from environment variables (Railway injects these)
            self::$host     = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
            self::$dbname   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'railway';
            self::$username = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
            self::$password = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
            self::$port     = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';

            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname,
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return self::$conn;
    }
}
?>
