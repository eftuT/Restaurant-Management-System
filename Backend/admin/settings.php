<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Table Status - SEN'Q</title>
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
                <a class="navbar-brand" href="home.php">MAIN MENU</a>
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
                    <li><a class="active-menu" href="settings.php"><i class="fa fa-dashboard"></i>Table Status</a></li>
                    <li><a href="addtable.php"><i class="fa fa-plus-circle"></i>Add Table</a></li>
                    <li><a href="tabledel.php"><i class="fa fa-pencil-square-o"></i> Delete Table</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">Available Tables</h1>
                    </div>
                </div>
                <div class="row">
                    <?php
                    require "../includes/db.php";
                    $query = "SELECT * FROM alltables WHERE status='Available'";
                    $res = $conn->query($query);
                    while($row = $res->fetch_assoc()):
                    ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3"><i class="fa fa-table fa-5x"></i></div>
                                    <div class="col-xs-9 text-right">
                                        <h3><?php echo $row['id']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <span class="pull-left"><?php echo $row['type']; ?></span>
                                <span class="pull-right"><?php echo $row['purpose']; ?></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom-scripts.js"></script>
</body>
</html>