<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT id, password FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    // Directly hash only if the password is NOT already hashed
    if (!password_verify($user['password'], $user['password'])) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->bindValue(":password", $hashedPassword);
        $updateStmt->bindValue(":id", $user['id']);
        $updateStmt->execute();
    }
}

echo "All passwords have been securely hashed!";
?>
