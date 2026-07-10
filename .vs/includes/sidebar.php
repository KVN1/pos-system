<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" class="sidebar-brand">
            <img src="/images/logo.png" alt="Logo">
        </a>
    </div>

    <ul class="nav">
        <li><a class="nav-link" href="/add-sales">ADD SALES</a></li>
        <li><a class="nav-link" href="/dashboard">Dashboard</a></li>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li><a class="nav-link" href="http://localhost/sales">Sales</a></li>
        <?php endif; ?>


        <li><a class="nav-link" href="/Products">Products</a></li>
        <li><a class="nav-link" href="/Categories">Categories</a></li>

        <li class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown()">Settings</a>
            <ul class="dropdown-menu" id="dropdownMenu">
                <li><a class="dropdown-item" href="#">General</a></li>
                <li>
                    <form id="logoutForm" action="/user/logout" method="POST">
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
                                
            </ul>
        </li>
    </ul>
</aside>

<!-- Sidebar Styles -->
<style>
/* Sidebar Base */
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

/* Sidebar Header */
.sidebar-header {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar-brand img {
    max-width: 180px;
}

/* Navigation Links */
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
    color: #fff; /* Black text */
    background: #8b5e34; /* Deep warm brown */
    padding: 12px;
    display: block;
    transition: 0.3s;
    border-radius: 5px;
    font-weight: bold;
}

.nav-link:hover {
    background: #a47148; /* Lighter brown for hover */
    color: #000; /* Keeps text black */
}

/* Dropdown */
.dropdown-menu {
    display: none;
    background: #f3e5d7; /* Soft Peach */
    list-style: none;
    padding-left: 0;
    border-radius: 5px;
}

.dropdown-item {
    display: block;
    padding: 8px;
    color: #0000; /* Black text */
    background: #a47148; /* Darker warm brown */
    text-decoration: none;
    text-align: center;
    border-radius: 3px;
}

.dropdown-item:hover {
    background: #a47148; /* Lighter brown */
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

<!-- JavaScript for Dropdown -->
<script>
function toggleDropdown() {
    var menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>
