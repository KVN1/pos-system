<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? 'cashier'; // default to cashier if role not set
$current_page = basename($_SERVER['REQUEST_URI']);

function checkNotifications() {
    $nearExpiryCount = 3;
    $lowStockCount = 2;
    return ($nearExpiryCount > 0 || $lowStockCount > 0);
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
        <li><a class="nav-link <?= (strpos($current_page, 'dashboard') !== false) ? 'active' : '' ?>" href="/dashboard">Dashboard</a></li>
        <li><a class="nav-link <?= (strpos($current_page, 'ADD-SALES-PAGE') !== false) ? 'active' : '' ?>" href="/ADD-SALES-PAGE">Add Sales</a></li>

        <?php if ($role === 'admin'): ?>
            <li><a class="nav-link <?= (strpos($current_page, 'sales') !== false) ? 'active' : '' ?>" href="/sales">Sales Report</a></li>
        <?php endif; ?>

        <li><a class="nav-link <?= (strpos($current_page, 'products') !== false) ? 'active' : '' ?>" href="/products">Products Inventory</a></li>
        <li><a class="nav-link <?= (strpos($current_page, 'Categories') !== false) ? 'active' : '' ?>" href="/Categories">Categories</a></li>

        <li>
            <a class="nav-link <?= (strpos($current_page, 'notifications') !== false) ? 'active' : '' ?>" href="/notifications">
                Notifications
                <?php if (checkNotifications()): ?>
                    <span class="notification-badge">!</span>
                <?php endif; ?>
            </a>
        </li>
        <li><a class="nav-link <?= (strpos($current_page, 'settings') !== false) ? 'active' : '' ?>" href="/settings">Settings</a></li>
    </ul>
</aside>

<style>
/* Notification Badge */
.notification-badge {
    color: #fff;
    background-color: #ff4d4d;
    border-radius: 50%;
    padding: 3px 8px;
    font-size: 14px;
    margin-left: 8px;
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

/* Sidebar Base */
.sidebar { position: fixed; left: 0; top: 0; width: 260px; height: 100%; background-color: #b9c79f; color: #f4f4f4; padding: 20px 10px; display: flex; flex-direction: column; align-items: center; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease; overflow-y: auto; border-radius: 8px; }
.sidebar-header { text-align: center; margin-bottom: 20px; }
.sidebar-brand img { max-width: 180px; border-radius: 8px; }

/* Navigation */
.nav { list-style: none; padding: 0; width: 100%; }
.nav li { width: 100%; text-align: center; margin: 5px 0; }
.nav-link { text-decoration: none; color: #f4f4f4; background: #6f7c54; padding: 12px; display: block; border-radius: 4px; font-weight: bold; transition: all 0.3s ease; margin: 5px 0; }
.nav-link:hover { background: #8a9764; color: #fff; transform: translateY(-2px); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); }

.dropdown-menu, .dropdown-submenu { display: none; list-style: none; background: #6f7c54; padding: 10px 0; border-radius: 8px; margin-top: 5px; transition: max-height 0.5s ease-in-out; }
.dropdown-item { display: block; padding: 10px; color: #f4f4f4; text-decoration: none; background: #8a9764; margin: 4px auto; width: 85%; border-radius: 6px; transition: all 0.3s ease; }
.dropdown-item:hover { background: #a3b479; transform: scale(1.05); }
.dropdown-menu.show, .dropdown-submenu.show { display: block; animation: fadeIn 0.4s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 768px) {
    .sidebar { width: 220px; }
    .sidebar-brand img { max-width: 140px; }
}

.nav-link.active { background: #344F1F; color: #ffffff; box-shadow: inset 0 0 5px rgba(0,0,0,0.3); transform: none; }
</style>

<script>
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    menu.classList.toggle('show');
}
</script>
