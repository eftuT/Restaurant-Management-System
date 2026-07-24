<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

$tableCount = $conn->query("SELECT COUNT(*) as count FROM alltables")->fetch_assoc()['count'];
$bookingCount = $conn->query("SELECT COUNT(*) as count FROM tablebook WHERE status='NOT CONFIRM'")->fetch_assoc()['count'];
$foodCount = $conn->query("SELECT COUNT(*) as count FROM food")->fetch_assoc()['count'];
$orderCount = $conn->query("SELECT COUNT(*) as count FROM basket")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - SEN'Q</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f0f2f5; }
        .top-nav {
            background: #1a1a2e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .top-nav .logo { font-size: 22px; font-weight: 700; }
        .top-nav .logo span { color: #f1c40f; }
        .top-nav .user-section { display: flex; align-items: center; gap: 20px; }
        .top-nav .user-section .dropdown { position: relative; cursor: pointer; }
        .top-nav .user-section .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: #fff;
            color: #333;
            padding: 10px 0;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            min-width: 180px;
            display: none;
        }
        .top-nav .user-section .dropdown:hover .dropdown-menu { display: block; }
        .top-nav .user-section .dropdown-menu a {
            display: block;
            padding: 8px 20px;
            color: #333;
            text-decoration: none;
            transition: 0.3s;
        }
        .top-nav .user-section .dropdown-menu a:hover { background: #f5f5f5; color: #b45f2b; }
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            background: #16213e;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li a {
            display: block;
            padding: 12px 25px;
            color: #a0aec0;
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border-left-color: #f1c40f;
        }
        .sidebar ul li a i { margin-right: 12px; width: 20px; }
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-left: 4px solid;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-card .stat-number { font-size: 32px; font-weight: 700; }
        .stat-card .stat-label { color: #888; font-size: 14px; margin-top: 5px; }
        .stat-card.primary { border-color: #4a90d9; }
        .stat-card.primary .stat-number { color: #4a90d9; }
        .stat-card.success { border-color: #27ae60; }
        .stat-card.success .stat-number { color: #27ae60; }
        .stat-card.warning { border-color: #f39c12; }
        .stat-card.warning .stat-number { color: #f39c12; }
        .stat-card.danger { border-color: #e74c3c; }
        .stat-card.danger .stat-number { color: #e74c3c; }
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .card .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
        .table tr:hover { background: #f8f9fa; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .btn { padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-primary { background: #4a90d9; color: #fff; }
        .btn-primary:hover { background: #357abd; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar ul li a span { display: none; }
            .main-content { margin-left: 60px; }
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<div class="top-nav">
    <div class="logo">🍽️ <span>SEN'Q</span> Admin</div>
    <div class="user-section">
        <span><i class="fa fa-user-circle"></i> <?php echo $_SESSION['adminUser']; ?></span>
        <div class="dropdown">
            <i class="fa fa-chevron-down"></i>
            <div class="dropdown-menu">
                <a href="usersettings.php"><i class="fa fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
                <hr style="margin:5px 0;">
                <a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="home.php" class="active"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li><a href="addtable.php"><i class="fa fa-plus-circle"></i> <span>Add Table</span></a></li>
        <li><a href="settings.php"><i class="fa fa-table"></i> <span>Table Status</span></a></li>
        <li><a href="tabledel.php"><i class="fa fa-trash"></i> <span>Delete Table</span></a></li>
        <li><a href="food_add.php"><i class="fa fa-cutlery"></i> <span>Add Food</span></a></li>
        <li><a href="food_list.php"><i class="fa fa-list"></i> <span>Food List</span></a></li>
        <li><a href="reservations.php"><i class="fa fa-calendar"></i> <span>Reservations</span></a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="messages.php"><i class="fa fa-envelope"></i> <span>Newsletters</span></a></li>
        <li><a href="usersettings.php"><i class="fa fa-users"></i> <span>Admin Users</span></a></li>
        <li><a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 style="margin-bottom:25px;">Dashboard Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-number"><?php echo $tableCount; ?></div>
            <div class="stat-label"><i class="fa fa-table"></i> Total Tables</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?php echo $bookingCount; ?></div>
            <div class="stat-label"><i class="fa fa-calendar"></i> Pending Bookings</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?php echo $foodCount; ?></div>
            <div class="stat-label"><i class="fa fa-cutlery"></i> Food Items</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-number"><?php echo $orderCount; ?></div>
            <div class="stat-label"><i class="fa fa-shopping-cart"></i> Total Orders</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-clock-o"></i> Recent Bookings</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Table</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $bookings = $conn->query("SELECT * FROM tablebook ORDER BY id DESC LIMIT 5");
                while($row = $bookings->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['FName'] . ' ' . $row['LName']; ?></td>
                        <td><?php echo $row['Tbltyp']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['status'] == 'Confirm' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="tablebook.php?rid=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>