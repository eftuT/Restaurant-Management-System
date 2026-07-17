<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location:../includes/logout.php");
}
require "../includes/db.php";
if (isset($_POST["ad"])) {
    $unam = $_POST["newun"];
    $passd = $_POST["newps"];
    $conn->query("INSERT INTO login(uname,pass) VALUES('$unam','$passd')");
    echo "<script>alert('User Added')</script>";
    echo '<meta http-equiv="refresh" content="1; URL=usersettings.php" />';
}
if (isset($_POST["upd"])) {
    $una = $_POST["uname"];
    $pasw = $_POST["pass"];
    $conn->query("UPDATE login SET uname = '$una', pass = '$pasw' WHERE id = '$id'");
    echo "<script>alert('User Updated')</script>";
    echo '<meta http-equiv="refresh" content="1; URL=usersettings.php" />';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Settings - SEN'Q</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
                    <li><a class="active-menu" href="settings.php"><i class="fa fa-dashboard"></i>User Dashboard</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">ADMINISTRATOR</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>User ID</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>Update</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $querys = "SELECT * FROM login";
                                        $result = $conn->query($querys);
                                        while($row = $result->fetch_assoc()) {
                                            $id = $row["id"];
                                            $us = $row["uname"];
                                            $ps = $row["pass"];
                                            if ($id % 2 == 0) {
                                                echo "<tr class='gradeC'>
                                                        <td>" . $id . "</td>
                                                        <td>" . $us . "</td>
                                                        <td>" . $ps . "</td>
                                                        <td><button class='btn btn-primary' data-toggle='modal' data-target='#myModal'>Update</button></td>
                                                        <td><a href='usersettingsdel.php?eid=" . $id . "' class='btn btn-danger'>Delete</a></td>
                                                    </tr>";
                                            } else {
                                                echo "<tr class='gradeU'>
                                                        <td>" . $id . "</td>
                                                        <td>" . $us . "</td>
                                                        <td>" . $ps . "</td>
                                                        <td><button class='btn btn-primary' data-toggle='modal' data-target='#myModal'>Update</button></td>
                                                        <td><a href='usersettingsdel.php?eid=" . $id . "' class='btn btn-danger'>Delete</a></td>
                                                    </tr>";
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#myModalins">Add New Admin</button>
                            <div class="modal fade" id="myModalins" tabindex="-1" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Add Admin</h4>
                                        </div>
                                        <form action="" method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <input name="newun" class="form-control" placeholder="Enter Username">
                                                </div>
                                                <div class="form-group">
                                                    <label>Password</label>
                                                    <input name="newps" class="form-control" placeholder="Enter Password">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <input type="submit" name="ad" value="Add" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Update Admin</h4>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Change Username</label>
                                                    <input name="uname" value="<?php echo $us; ?>" class="form-control" placeholder="Enter Username">
                                                </div>
                                                <div class="form-group">
                                                    <label>Change Password</label>
                                                    <input name="pass" value="<?php echo $ps; ?>" class="form-control" placeholder="Enter Password">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <input type="submit" name="upd" value="Update" class="btn btn-primary">
                                            </div>
                                        </form>
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