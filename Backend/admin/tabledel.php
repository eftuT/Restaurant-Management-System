<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
}
require "../includes/db.php";
if (isset($_POST["del"])) {
    $del = $_POST["id"];
    $conn->query("DELETE FROM alltables WHERE id = '$del'");
    echo "<script>alert('Table Deleted')</script>";
    echo '<meta http-equiv="refresh" content="1; URL=tabledel.php" />';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delete Table - SEN'Q</title>
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
                    <li><a href="settings.php"><i class="fa fa-dashboard"></i>Table Status</a></li>
                    <li><a href="addtable.php"><i class="fa fa-plus-circle"></i>Add Table</a></li>
                    <li><a class="active-menu" href="tabledel.php"><i class="fa fa-pencil-square-o"></i> Delete Table</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">DELETE Table</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Delete Table</div>
                            <div class="panel-body">
                                <form name="form" method="post">
                                    <div class="form-group">
                                        <label>Select the Table ID *</label>
                                        <select name="id" class="form-control" required>
                                            <option value selected></option>
                                            <?php
                                            $query = "SELECT * from alltables";
                                            $res = $conn->query($query);
                                            while ($row = $res->fetch_assoc()) {
                                                echo '<option value="'. $row["id"] . '">' . $row["id"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <input type="submit" name="del" value="Delete Table" class="btn btn-primary">
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
    <script src="assets/js/custom-scripts.js"></script>
</body>
</html>