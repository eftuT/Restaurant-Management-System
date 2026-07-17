<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
}
require "../includes/db.php";
if (isset($_POST["add"])) {
    $typ = $_POST["tbltyp"];
    $purp = $_POST["purpose"];
    $status = "Available";
    $check = $conn->query("SELECT * FROM alltables WHERE type = '$typ' AND purpose = '$purp'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Table Already Exists')</script>";
    } else {
        $conn->query("INSERT INTO alltables(type,purpose,status) VALUES ('$typ','$purp','$status')");
        echo "<script>alert('New Table Added')</script>";
        echo '<meta http-equiv="refresh" content="1; URL=addtable.php" />';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Table - SEN'Q</title>
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
                    <li><a class="active-menu" href="addtable.php"><i class="fa fa-plus-circle"></i>Add Table</a></li>
                    <li><a href="tabledel.php"><i class="fa fa-desktop"></i> Delete Table</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">NEW Table</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 col-sm-5">
                        <div class="panel panel-primary">
                            <div class="panel-heading">ADD NEW Table</div>
                            <div class="panel-body">
                                <form name="form" method="post">
                                    <div class="form-group">
                                        <label>Type Of Table *</label>
                                        <select name="tbltyp" class="form-control" required>
                                            <option value selected></option>
                                            <option value="Table for 2">Table for 2</option>
                                            <option value="Table for 3">Table for 3</option>
                                            <option value="Table for 4">Table for 4</option>
                                            <option value="Table for 5">Table for 5</option>
                                            <option value="Table for 6">Table for 6</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Purpose</label>
                                        <select name="purpose" class="form-control" required>
                                            <option value selected></option>
                                            <option value="meeting">Meeting</option>
                                            <option value="casual">Casual</option>
                                            <option value="celebration">Celebration</option>
                                        </select>
                                    </div>
                                    <input type="submit" name="add" value="Add New" class="btn btn-primary">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Tables INFORMATION</div>
                                <div class="panel-body">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Tables ID</th>
                                                            <th>Tables Type</th>
                                                            <th>Purpose</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $query = "SELECT * FROM alltables limit 0,10";
                                                    $res = $conn->query($query);
                                                    while($row = $res->fetch_assoc()) {
                                                        $id = $row["id"];
                                                        if ($id % 2 == 0) {
                                                            echo "<tr class='odd gradeX'>
                                                                    <td>" . $row["id"] . "</td>
                                                                    <td>" . $row["type"] . "</td>
                                                                    <th>" . $row["purpose"] . "</th>
                                                                </tr>";
                                                        } else {
                                                            echo "<tr class='even gradeC'>
                                                                    <td>" . $row["id"] . "</td>
                                                                    <td>" . $row["type"] . "</td>
                                                                    <th>" . $row["purpose"] . "</th>
                                                                </tr>";
                                                        }
                                                    }
                                                    ?>
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
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom-scripts.js"></script>
</body>
</html>