<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

if (isset($_POST["ad"])) {
    $unam = $_POST["newun"];
    $passd = $_POST["newps"];
    $conn->query("INSERT INTO login(uname,pass) VALUES('$unam','$passd')");
    $success = "New admin user added!";
}
if (isset($_POST["upd"])) {
    $una = $_POST["uname"];
    $pasw = $_POST["pass"];
    $conn->query("UPDATE login SET uname = '$una', pass = '$pasw' WHERE id = '$id'");
    $success = "Admin user updated!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Users - SEN'Q</title>
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
        .btn-success {
            background: #27ae60;
            color: #fff;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-success:hover { background: #1e8449; }
        .btn-danger-sm {
            background: #e74c3c;
            color: #fff;
            padding: 5px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 12px;
        }
        .btn-danger-sm:hover { background: #c0392b; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
        .table tr:hover { background: #f8f9fa; }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal.show { display: flex; justify-content: center; align-items: center; }
        .modal-content {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 450px;
            width: 100%;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-content h3 { margin-bottom: 20px; color: #1a1a2e; }
        .modal-content .btn-close {
            background: #e74c3c;
            color: #fff;
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 10px;
        }
        .modal-content .btn-close:hover { background: #c0392b; }
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar ul li a span { display: none; }
            .main-content { margin-left: 60px; }
        }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="logo">🍽️ <span>SEN'Q</span> Admin</div>
    <div>
        <span><i class="fa fa-user-circle"></i> <?php echo $_SESSION['adminUser']; ?></span>
        <a href="logout.php" style="color:#e74c3c;margin-left:20px;text-decoration:none;">
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </div>
</div>

<div class="sidebar">
    <ul>
        <li><a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li><a href="addtable.php"><i class="fa fa-plus-circle"></i> <span>Add Table</span></a></li>
        <li><a href="settings.php"><i class="fa fa-table"></i> <span>Table Status</span></a></li>
        <li><a href="tabledel.php"><i class="fa fa-trash"></i> <span>Delete Table</span></a></li>
        <li><a href="food_add.php"><i class="fa fa-cutlery"></i> <span>Add Food</span></a></li>
        <li><a href="food_list.php"><i class="fa fa-list"></i> <span>Food List</span></a></li>
        <li><a href="reservations.php"><i class="fa fa-calendar"></i> <span>Reservations</span></a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="messages.php"><i class="fa fa-envelope"></i> <span>Newsletters</span></a></li>
        <li><a href="usersettings.php" class="active"><i class="fa fa-users"></i> <span>Admin Users</span></a></li>
        <li><a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <h2 style="margin-bottom:25px;"><i class="fa fa-users"></i> Admin User Management</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-user-plus"></i> Add New Admin
            <button onclick="document.getElementById('addModal').classList.add('show')" style="float:right;background:#27ae60;color:#fff;padding:5px 15px;border:none;border-radius:8px;cursor:pointer;">
                <i class="fa fa-plus"></i> Add Admin
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-list"></i> Admin Users List</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM login");
                while($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['uname']; ?></strong></td>
                        <td>
                            <button onclick="openUpdateModal('<?php echo $row['id']; ?>', '<?php echo $row['uname']; ?>')" class="btn-primary" style="padding:5px 15px;border:none;border-radius:8px;cursor:pointer;color:#fff;background:#4a90d9;">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <a href="usersettingsdel.php?eid=<?php echo $row['id']; ?>" class="btn-danger-sm" onclick="return confirm('Delete this admin user?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa fa-user-plus"></i> Add New Admin</h3>
        <form method="post">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Username</label>
                <input name="newun" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Password</label>
                <input name="newps" type="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" name="ad" class="btn-success"><i class="fa fa-save"></i> Add Admin</button>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('show')" class="btn-close">Cancel</button>
        </form>
    </div>
</div>

<!-- Update Admin Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa fa-edit"></i> Update Admin</h3>
        <form method="post" id="updateForm">
            <input type="hidden" name="update_id" id="update_id">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Username</label>
                <input name="uname" id="update_uname" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Password</label>
                <input name="pass" type="password" class="form-control" placeholder="Enter new password" required>
            </div>
            <button type="submit" name="upd" class="btn-primary"><i class="fa fa-save"></i> Update Admin</button>
            <button type="button" onclick="document.getElementById('updateModal').classList.remove('show')" class="btn-close">Cancel</button>
        </form>
    </div>
</div>

<script>
function openUpdateModal(id, username) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_uname').value = username;
    document.getElementById('updateModal').classList.add('show');
}
</script>

</body>
</html>