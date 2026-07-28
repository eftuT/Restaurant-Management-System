<?php
// Check if user is logged in
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SEN'Q Admin - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        /* ===== ADMIN THEME - BEIGE/BROWN ===== */
        :root {
            --primary: #b45f2b;
            --primary-dark: #8a471f;
            --bg-cream: #fefcf3;
            --bg-beige: #f7f2e9;
            --text-dark: #2c1f16;
            --text-muted: #7a6a5a;
            --border-light: #e8e0d8;
        }

        /* ===== GLOBAL ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-cream);
            color: var(--text-dark);
        }

        /* ===== TOP NAV ===== */
        .top-nav {
            background: var(--text-dark);
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        /* ===== LOGO WITH BIG S ===== */
        .top-nav .logo {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 24px;
            font-weight: 700;
        }
        .top-nav .logo .logo-icon {
            background: #b45f2b;
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
        }
        .top-nav .logo .logo-text {
            color: #f1c40f;
            letter-spacing: 2px;
            font-size: 24px;
        }

        /* ===== USER DROPDOWN ===== */
        .top-nav .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }
        .top-nav .user-section .dropdown-toggle {
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 6px;
            transition: 0.3s;
        }
        .top-nav .user-section .dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .top-nav .user-section .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            background: #fff;
            color: var(--text-dark);
            padding: 8px 0;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            min-width: 200px;
            display: none;
            z-index: 2000;
            border: 1px solid var(--border-light);
        }
        .top-nav .user-section .dropdown-menu.show { display: block; }
        .top-nav .user-section .dropdown-menu a {
            display: block;
            padding: 10px 22px;
            color: var(--text-dark);
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }
        .top-nav .user-section .dropdown-menu a:hover {
            background: var(--bg-beige);
            color: var(--primary);
        }
        .top-nav .user-section .dropdown-menu a i {
            width: 20px;
            margin-right: 10px;
        }
        .top-nav .user-section .dropdown-menu .divider {
            border-top: 1px solid var(--border-light);
            margin: 5px 0;
        }
        .top-nav .user-section .dropdown-menu .logout { color: #e74c3c; }
        .top-nav .user-section .dropdown-menu .logout:hover {
            background: #fdf0ed;
            color: #c0392b;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            background: var(--text-dark);
            padding: 20px 0;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li a {
            display: block;
            padding: 12px 25px;
            color: #c4b8ac;
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(180, 95, 43, 0.12);
            color: #fff;
            border-left-color: var(--primary);
        }
        .sidebar ul li a i { margin-right: 12px; width: 20px; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        /* ===== CARDS ===== */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(44,31,22,0.08);
            margin-bottom: 30px;
            border: 1px solid var(--border-light);
        }
        .card .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bg-beige);
            color: var(--text-dark);
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(44,31,22,0.08);
            border-left: 4px solid var(--primary);
            border: 1px solid var(--border-light);
        }
        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
        }
        .stat-card .stat-label {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 5px;
        }

        /* ===== TABLES ===== */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: var(--bg-beige);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-light);
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
        }
        .table tr:hover { background: #faf8f5; }

        /* ===== BADGES ===== */
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-primary { background: #cce5ff; color: #004085; }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            font-size: 14px;
            display: inline-block;
            text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180,95,43,0.25);
            color: #fff;
        }
        .btn-success { background: #27ae60; color: #fff; }
        .btn-success:hover { background: #1e8449; color: #fff; }
        .btn-danger { background: #e74c3c; color: #fff; }
        .btn-danger:hover { background: #c0392b; color: #fff; }
        .btn-sm { padding: 5px 14px; font-size: 12px; }
        .btn-secondary { background: #95a5a6; color: #fff; }
        .btn-secondary:hover { background: #7f8c8d; color: #fff; }

        /* ===== FORMS ===== */
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 14px;
            transition: 0.3s;
            background: #fff !important;
            color: var(--text-dark) !important;
            -webkit-appearance: auto !important;
            appearance: auto !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            min-height: 42px;
        }
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(180,95,43,0.1);
            background: #fff !important;
        }
        select.form-control option,
        select option {
            color: var(--text-dark) !important;
            background: #fff !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
            min-height: 30px !important;
        }
        select.form-control option:checked,
        select option:checked {
            background: var(--primary) !important;
            color: #fff !important;
        }
        .form-group {
            overflow: visible !important;
        }
        .panel-body,
        .card-body {
            overflow: visible !important;
        }

        /* ===== MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal.show { display: flex; justify-content: center; align-items: center; }
        .modal-content {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 450px;
            width: 100%;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar ul li a span { display: none; }
            .main-content { margin-left: 60px; }
            .top-nav .logo .logo-icon { width: 40px; height: 40px; font-size: 22px; }
            .top-nav .logo .logo-text { font-size: 20px; }
            select.form-control { font-size: 16px !important; }
        }
    </style>
</head>
<body>

<!-- TOP NAV -->
<div class="top-nav">
    <div class="logo">
        <div class="logo-icon">S</div>
        <span class="logo-text">SEN'Q</span>
         <span style="color:#aaa;font-size:20px;font-weight:600;margin-left:4px;">Admin</span>
    </div>
    <div class="user-section">
        <span><i class="fa fa-user-circle"></i> <?php echo $_SESSION['adminUser'] ?? 'Admin'; ?></span>
        <div class="dropdown-toggle" onclick="toggleDropdown()">
            <i class="fa fa-chevron-down"></i>
        </div>
        <div class="dropdown-menu" id="userDropdown">
            <a href="usersettings.php"><i class="fa fa-user"></i> Profile</a>
            <div class="divider"></div>
            <a href="logout.php" class="logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <ul>
        <li><a href="home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a></li>
        <li><a href="addtable.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'addtable.php' ? 'active' : ''; ?>">
            <i class="fa fa-plus-circle"></i> <span>Add Table</span>
        </a></li>
        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <i class="fa fa-table"></i> <span>Table Status</span>
        </a></li>
        <li><a href="tabledel.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tabledel.php' ? 'active' : ''; ?>">
            <i class="fa fa-trash"></i> <span>Delete Table</span>
        </a></li>
        <li><a href="food_add.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_add.php' ? 'active' : ''; ?>">
            <i class="fa fa-cutlery"></i> <span>Add Food</span>
        </a></li>
        <li><a href="food_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_list.php' ? 'active' : ''; ?>">
            <i class="fa fa-list"></i> <span>Food List</span>
        </a></li>
        <li><a href="reservations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : ''; ?>">
            <i class="fa fa-calendar"></i> <span>Reservations</span>
        </a></li>
        <li><a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            <i class="fa fa-shopping-cart"></i> <span>Orders</span>
        </a></li>
        <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
            <i class="fa fa-envelope"></i> <span>Newsletters</span>
        </a></li>
        <li><a href="usersettings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usersettings.php' ? 'active' : ''; ?>">
            <i class="fa fa-users"></i> <span>Admin Users</span>
        </a></li>
        <li><a href="logout.php" style="color:#e74c3c;">
            <i class="fa fa-sign-out"></i> <span>Logout</span>
        </a></li>
    </ul>
</div>

<script>
// Toggle user dropdown
function toggleDropdown() {
    var dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    var dropdown = document.getElementById('userDropdown');
    var toggle = document.querySelector('.dropdown-toggle');
    if (dropdown && !dropdown.contains(event.target) && toggle && !toggle.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>