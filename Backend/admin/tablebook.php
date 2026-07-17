<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
}
require "../includes/db.php";
if (!isset($_GET["rid"])) {
    echo '<meta http-equiv="refresh" content="1; URL=index.php" />';
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
        echo "<script>alert('Booking Confirmed')</script>";
        echo '<meta http-equiv="refresh" content="1; URL=tablebook.php?rid='.$id.'" />';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Table Booking - SEN'Q</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default top-navbar">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">SEN'Q Admin</a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="usersettings.php"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                        <li><a href="settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <nav class="navbar-default navbar-side">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li><a href="home.php"><i class="fa fa-dashboard"></i> Status</a></li>
                    <li><a href="messages.php"><i class="fa fa-desktop"></i> News Letters</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">Table Booking <small><?php echo $curdate; ?></small></h1>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="panel panel-info">
                            <div class="panel-heading">Booking Confirmation</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr><th>DESCRIPTION</th><th>INFORMATION</th></tr>
                                        <tr><th>Name</th><th><?php echo $title . $fname . $lname; ?></th></tr>
                                        <tr><th>Email</th><th><?php echo $email; ?></th></tr>
                                        <tr><th>Nationality</th><th><?php echo $nation; ?></th></tr>
                                        <tr><th>Country</th><th><?php echo $country; ?></th></tr>
                                        <tr><th>Phone No</th><th><?php echo $phone; ?></th></tr>
                                        <tr><th>Type Of Table</th><th><?php echo $tble; ?></th></tr>
                                        <tr><th>Purpose</th><th><?php echo $purpose; ?></th></tr>
                                        <tr><th>Meal Plan</th><th><?php echo $meal; ?></th></tr>
                                        <tr><th>Time</th><th><?php echo $time; ?></th></tr>
                                        <tr><th>Date</th><th><?php echo $date; ?></th></tr>
                                        <tr><th>Status Level</th><th><?php echo $status; ?></th></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label>Select the Confirmation</label>
                                        <select name="con" class="form-control">
                                            <option value selected></option>
                                            <option value="Confirm">Confirm</option>
                                        </select>
                                    </div>
                                    <input type="submit" name="confirm" value="Confirm" class="btn btn-success">
                                </form>
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