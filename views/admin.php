<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - POS</title>
    <link rel="stylesheet" href="/styles/stylee.css">
</head>
<body>
    <div class="dashboard">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
                <h1>ADMIN</h1>
            </header>

            <section class="content">
                <div class="card">
                    <h3>Total Sales</h3>
                    <p>$620,000</p>
                </div>
                <div class="card">
                    <h3>Total Items in Storage</h3>
                    <p>1300</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
