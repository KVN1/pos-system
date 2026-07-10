<?php
// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {  // Changed 'id' to 'user_id' here
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Include the Database class
require_once __DIR__ . '/../database.php'; // Adjust the path if necessary

// Create a new instance of the Database class
$database = new Database();
$pdo = $database->getConnection();  // Get the PDO connection

// Assuming user_id is stored in session, retrieve the user details from the database
$user_id = $_SESSION['user_id'];  // Changed 'id' to 'user_id'

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";  // Changed 'id' to 'user_id' in query
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);  // Bound parameter is also 'user_id'
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Handle case if user not found
    echo "User not found!";
    exit;
}

// User details to display
$username = $user['username'];
$user_role = $user['role']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/styles/stylee.css"> 
    <link rel="stylesheet" href="/styles/userstyle.css"> <!-- Correct CSS link -->

</head>
<body>
    <!-- Sidebar or Navigation -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="main-content">
        <header class="header">
            <h1>User Profile</h1>
        </header>

        <section class="content">
            <div class="user-details-container">
                <h2>Welcome, <?= htmlspecialchars($username); ?>!</h2>
                <p><strong>Role:</strong> <?= htmlspecialchars($user_role); ?></p>

                <!-- You can add more user-specific details here -->
                <p><strong>User ID:</strong> <?= htmlspecialchars($user_id); ?></p>  <!-- Changed 'id' to 'user_id' here -->
                <p><a href="logout.php">Logout</a></p> <!-- Logout link -->
            </div>
        </section>
    </div>
</body>
</html>
