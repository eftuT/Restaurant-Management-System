<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

if (isset($_POST["add"])) {
    $typ = $_POST["tbltyp"];
    $purp = $_POST["purpose"];
    $status = "Available";
    $check = $conn->query("SELECT * FROM alltables WHERE type = '$typ' AND purpose = '$purp'");
    if ($check->num_rows > 0) {
        $error = "Table Already Exists!";
    } else {
        $conn->query("INSERT INTO alltables(type,purpose,status) VALUES ('$typ','$purp','$status')");
        $success = "New Table Added Successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Table - SEN'Q</title>
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
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #b45f2b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(180, 95, 43, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #b45f2b, #8a471f);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(180, 95, 43, 0.3);
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
        .table tr:hover { background: #f8f9fa; }
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
    <div>
        <span><i class="fa fa-user-circle"></i> <?php echo $_SESSION['adminUser']; ?></span>
        <a href="logout.php" style="color:#e74c3c;margin-left:20px;text-decoration:none;">
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li><a href="addtable.php" class="active"><i class="fa fa-plus-circle"></i> <span>Add Table</span></a></li>
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
    <h2 style="margin-bottom:25px;"><i class="fa fa-plus-circle"></i> Add New Table</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-info-circle"></i> Table Information</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Type of Table *</label>
                <select name="tbltyp" class="form-control" required>
                    <option value="">Select Table Type</option>
                    <option value="Table for 2">Table for 2</option>
                    <option value="Table for 3">Table for 3</option>
                    <option value="Table for 4">Table for 4</option>
                    <option value="Table for 5">Table for 5</option>
                    <option value="Table for 6">Table for 6</option>
                    <option value="Table for 8">Table for 8</option>
                    <option value="Table for 10">Table for 10</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Purpose *</label>
                <select name="purpose" class="form-control" required>
                    <option value="">Select Purpose</option>
                    <option value="meeting">Meeting</option>
                    <option value="casual">Casual Dining</option>
                    <option value="celebration">Celebration</option>
                    <option value="business">Business</option>
                    <option value="family">Family</option>
                </select>
            </div>
            <button type="submit" name="add" class="btn-primary"><i class="fa fa-save"></i> Add Table</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-list"></i> Existing Tables</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM alltables LIMIT 10";
                $res = $conn->query($query);
                while($row = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo ucfirst($row['purpose']); ?></td>
                        <td>
                            <span class="badge <?php echo $row['status'] == 'Available' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>