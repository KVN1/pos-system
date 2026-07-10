<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/HauldUpBro/home" class="sidebar-brand">
            <img src="/images/logo.png" alt="Logo">
        </a>
    </div>

    <ul class="nav">
        <!-- Dashboard is accessible to all roles -->
        <li><a class="nav-link" href="/dashboard">Dashboard</a></li>

        <!-- Add-Sales is only for managers -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin','cashier'): ?>
            <li><a class="nav-link" href="/add-sales-page">Add-Sales</a></li>
        <?php endif; ?>

        <!-- Sales is accessible to both admin and manager -->
        

        <!-- Products and Categories are only for managers -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'cashier', 'admin'): ?>
            <li><a class="nav-link" href="/products">Products</a></li>
            <li><a class="nav-link" href="/categories">Categories</a></li>
        <?php endif; ?>

        <!-- Admin panel is only visible to admin -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a class="nav-link" href="/admin">Admin</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a class="nav-link" href="/sales">Sales</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'cashier'): ?>
            <li><a class="nav-link" href="/admin">Manager</a></li>
        <?php endif; ?>

        <!-- Settings Dropdown - Available for all roles -->
        <li class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown()">Settings</a>
            <ul class="dropdown-menu" id="dropdownMenu">
                <li><a class="dropdown-item" href="#">General</a></li>
                <li><a class="dropdown-item" href="#">Security</a></li>
            </ul>
        </li>
    </ul>
</aside>

<style>
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100%;
    background-color: #f8f1e4; 
    color: #000; 
    padding: 20px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar-brand img {
    max-width: 180px;
}

.nav {
    list-style: none;
    padding: 0;
    width: 100%;
}

.nav li {
    width: 100%;
    text-align: center;
}

.nav-link {
    text-decoration: none;
    color: #fff; 
    background: #8b5e34; 
    padding: 12px;
    display: block;
    transition: 0.3s;
    border-radius: 5px;
    font-weight: bold;
}

.nav-link:hover {
    background: #a47148; 
    color: #000; 
}

.dropdown-menu {
    display: none;
    background: #f3e5d7;
    list-style: none;
    padding-left: 0;
    border-radius: 5px;
}

.dropdown-item {
    display: block;
    padding: 8px;
    color: #000; 
    background: #724927; 
    text-decoration: none;
    text-align: center;
    border-radius: 3px;
}

.dropdown-item:hover {
    background: #a47148; 
    color: #000;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 220px;
    }
    
    .sidebar-brand img {
        max-width: 150px;
    }
}
</style>

<script>
function toggleDropdown() {
    var menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>