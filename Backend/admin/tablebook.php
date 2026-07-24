<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

if (!isset($_GET["rid"])) {
    header("location: index.php");
    exit;
}
$curdate = date("Y/m/d");
$id = $_GET["rid"];
$result = $conn->query("SELECT * FROM tablebook WHERE id = '$id'");
$row = $result->fetch_assoc();
$title = $row["Title"];
$fname = $row["FName"];
$lname = $row["LName"];
$email = $row["Email"];
$nation = $row["National"];
$country = $row["Country"];
$phone = $row["Phone"];
$tble = $row["Tbltyp"];
$purpose = $row["Purpose"];
$meal = $row["Meal"];
$time = $row["time"];
$date = $row["date"];
$status = $row["status"];

if (isset($_POST["confirm"])) {
    $status = $_POST["con"];
    if ($status == "Confirm") {
        $conn->query("UPDATE tablebook SET status='$status' WHERE id = '$id'");
        $notavail = "Booked";
        $conn->query("UPDATE alltables SET `status`='$notavail',`cid`='$id' WHERE purpose='$purpose' AND type='$tble'");
        echo "<script>alert('Booking Confirmed!'); window.location.href='tablebook.php?rid=$id';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirm Booking - SEN'Q</title>
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
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f0f2f5; }
        .detail-row .label { font-weight: 600; width: 40%; color: #555; }
        .detail-row .value { width: 60%; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-not-confirm { background: #fff3cd; color: #856404; }
        .badge-confirm { background: #d4edda; color: #155724; }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
        }
        .btn-success {
            background: #27ae60;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-success:hover { background: #1e8449; transform: scale(1.05); }
        .btn-back {
            background: #95a5a6;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back:hover { background: #7f8c8d; }
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
        <li><a href="usersettings.php"><i class="fa fa-users"></i> <span>Admin Users</span></a></li>
        <li><a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <h2 style="margin-bottom:25px;"><i class="fa fa-calendar-check-o"></i> Booking Confirmation</h2>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-user"></i> Booking #<?php echo $id; ?>
            <a href="home.php" class="btn-back" style="float:right;"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        
        <div class="detail-row">
            <div class="label">Full Name</div>
            <div class="value"><strong><?php echo $title . $fname . ' ' . $lname; ?></strong></div>
        </div>
        <div class="detail-row">
            <div class="label">Email</div>
            <div class="value"><?php echo $email; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Phone</div>
            <div class="value"><?php echo $phone; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Nationality</div>
            <div class="value"><?php echo $nation; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Country</div>
            <div class="value"><?php echo $country; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Table Type</div>
            <div class="value"><?php echo $tble; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Purpose</div>
            <div class="value"><?php echo $purpose; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Meal Plan</div>
            <div class="value"><?php echo $meal; ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Date</div>
            <div class="value"><?php echo date('l, F d, Y', strtotime($date)); ?></div>
        </div>
        <div class="detail-row">
            <div class="label">Time</div>
            <div class="value"><?php echo date('h:i A', strtotime($time)); ?></div>
        </div>
        <div class="detail-row" style="border-bottom:none;">
            <div class="label">Status</div>
            <div class="value">
                <span class="badge <?php echo $status == 'Confirm' ? 'badge-confirm' : 'badge-not-confirm'; ?>">
                    <?php echo $status; ?>
                </span>
            </div>
        </div>
    </div>

    <?php if($status != 'Confirm'): ?>
    <div class="card">
        <div class="card-header"><i class="fa fa-check-circle" style="color:#27ae60;"></i> Confirm Booking</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Confirmation Action</label>
                <select name="con" class="form-control" required>
                    <option value="">Select Action</option>
                    <option value="Confirm">Confirm Booking</option>
                </select>
            </div>
            <button type="submit" name="confirm" class="btn-success" onclick="return confirm('Confirm this booking? This will mark the table as BOOKED.')">
                <i class="fa fa-check"></i> Confirm Booking
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>