<?php
class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=infinite_pos", "root", ""); // Update DB name if needed
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // This method allows other classes to get the PDO connection
    public function getConnection() {
        return $this->pdo;
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    public function insertAndGetId($query, $params = []) {
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $this->db->lastInsertId(); // This returns the last inserted ID
}

}
?>
