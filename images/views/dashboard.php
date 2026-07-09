<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - POS</title>
    <link rel="stylesheet" href="/styles/stylee.css">
</head>
<body>
    <div class="dashboard">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
        <main class="main-content">
            <header class="header">
                <h1>Dashboard</h1>
                <p>Welcome, 
                    <strong>
                        <?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "Guest"; ?>
                    </strong>!
                </p>
            </header>

            <section class="content">
                <div class="card">
                    <h3>Total Sales This Month</h3>
                    <p>$12,340</p>
                </div>
                <div class="card">
                    <h3>Items in Stock</h3>
                    <p>256</p>
                </div>
                <div class="card">
                    <h3>Items Low in Stock</h3>
                    <p>5</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

