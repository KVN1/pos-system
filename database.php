<?php
class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $host     = getenv('MYSQLHOST')     ?: 'localhost';
            $dbname   = getenv('MYSQLDATABASE') ?: 'railway';
            $username = getenv('MYSQLUSER')     ?: 'root';
            $password = getenv('MYSQLPASSWORD') ?: '';
            $port     = getenv('MYSQLPORT')     ?: '3306';

            try {
                // Force TCP connection with host= and port= explicitly
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                self::$conn = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ]);
            } catch (PDOException $e) {
                die("Connection error: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
