<?php
require_once 'database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Initialize Database class
    }

public function checkCredentials($username, $password) {
    require_once 'database.php';
    $db = new Database();
    $conn = $db->getConnection();

    echo "Database connection established.<br>";

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo "Query executed successfully.<br>";
    } else {
        echo "Query execution failed!<br>";
        print_r($stmt->errorInfo());
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "User not found.<br>";
        return false;
    }

    echo "User found in database.<br>";
    echo "Stored Password Hash: " . htmlspecialchars($user['password']) . "<br>";

    if (password_verify($password, $user['password'])) {
        echo "Password verification successful.<br>";
        return $user;
    } else {
        echo "Password verification failed.<br>";
        return false;
    }
}


}
?>
