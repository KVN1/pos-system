<?php
// Run this ONCE to import your SQL file to Railway MySQL
// Delete this file after running!

$host     = getenv('MYSQLHOST')     ?: 'localhost';
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';
$username = getenv('MYSQLUSER')     ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$port     = getenv('MYSQLPORT')     ?: '3306';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname",
        $username, $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__ . '/infinite_pos.sql');
    $pdo->exec($sql);

    echo "<h2 style='color:green'>Database imported successfully!</h2>";
    echo "<p>Delete this file (import_db.php) now for security.</p>";
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
?>
