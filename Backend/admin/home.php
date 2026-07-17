<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require "../includes/db.php";
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
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default top-navbar">
            <div class="navbar-header">
                <a class="navbar-brand" href="home.php">SEN'Q Admin</a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user"></i> <?php echo $_SESSION['adminUser']; ?>
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <nav class="navbar-default navbar-side">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="active"><a href="home.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="addtable.php"><i class="fa fa-plus-circle"></i> Add Table</a></li>
                    <li><a href="settings.php"><i class="fa fa-table"></i> Table Status</a></li>
                    <li><a href="food_add.php"><i class="fa fa-cutlery"></i> Add Food</a></li>
                    <li><a href="food_list.php"><i class="fa fa-list"></i> Food List</a></li>
                    <li><a href="reservations.php"><i class="fa fa-calendar"></i> Reservations</a></li>
                    <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="messages.php"><i class="fa fa-envelope"></i> Newsletters</a></li>
                    <li><a href="usersettings.php"><i class="fa fa-users"></i> Admin Users</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">Dashboard</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3"><i class="fa fa-table fa-5x"></i></div>
                                    <div class="col-xs-9 text-right"><h3><?php echo $tableCount; ?></h3> Tables</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3"><i class="fa fa-calendar fa-5x"></i></div>
                                    <div class="col-xs-9 text-right"><h3><?php echo $bookingCount; ?></h3> Bookings</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3"><i class="fa fa-cutlery fa-5x"></i></div>
                                    <div class="col-xs-9 text-right"><h3><?php echo $foodCount; ?></h3> Food Items</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3"><i class="fa fa-shopping-cart fa-5x"></i></div>
                                    <div class="col-xs-9 text-right"><h3><?php echo $orderCount; ?></h3> Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Recent Bookings</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
                                                    <span class="label <?php echo $row['status'] == 'Confirm' ? 'label-success' : 'label-warning'; ?>">
                                                        <?php echo $row['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="tablebook.php?rid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
</body>
</html>