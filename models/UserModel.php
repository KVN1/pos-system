<?php
require_once __DIR__ . '/../database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* ============================
       AUTHENTICATION METHODS
    ============================ */

    public function checkCredentials($username, $password) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE username = :username
        ");
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;

        return $user;
    }

    public function usernameExists($username) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE username = :username
        ");
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    /* ============================
       USER REGISTRATION & INFO
    ============================ */

    public function registerUser($firstName, $lastName, $username, $hashedPassword, $role) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO users (first_name, last_name, username, password, role)
            VALUES (:first_name, :last_name, :username, :password, :role)
        ");
        $stmt->bindValue(":first_name", $firstName, PDO::PARAM_STR);
        $stmt->bindValue(":last_name", $lastName, PDO::PARAM_STR);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(":role", $role, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getUserByUsername($username) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE username = :username
        ");
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($username, $newPassword) {
        $conn = $this->db->getConnection();

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET password = :password 
            WHERE username = :username
        ");
        $stmt->bindValue(":password", $hashed, PDO::PARAM_STR);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /* ============================
       STATUS MANAGEMENT
    ============================ */

    public function getUsersByStatus($status) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE status = :status
        ");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserStatus($userId, $status) {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            UPDATE users 
            SET status = :status 
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /* ============================
   LOGIN ATTEMPTS & LOCKING
============================ */

// Update failed attempts count
public function updateFailedAttempts($userId, $attempts) {
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("UPDATE users SET failed_attempts = :attempts WHERE user_id = :user_id");
    $stmt->bindValue(":attempts", $attempts, PDO::PARAM_INT);
    $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Lock account until a specific time
public function lockAccount($userId, $attempts, $lockUntil) {
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("
        UPDATE users 
        SET failed_attempts = :attempts, lock_until = :lock_until 
        WHERE user_id = :user_id
    ");
    $stmt->bindValue(":attempts", $attempts, PDO::PARAM_INT);
    $stmt->bindValue(":lock_until", $lockUntil, PDO::PARAM_STR);
    $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Reset failed attempts and unlock account
public function resetLoginAttempts($userId) {
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE user_id = :user_id");
    $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
    return $stmt->execute();
}


}
?>
