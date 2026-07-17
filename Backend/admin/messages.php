<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
}
require "../includes/db.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Newsletters - SEN'Q</title>
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
                    <li><a href="home.php"><i class="fa fa-dashboard"></i> Status</a></li>
                    <li><a class="active-menu" href="messages.php"><i class="fa fa-desktop"></i> News Letters</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">Newsletters</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="jumbotron">
                            <h3>Send Newsletter to Followers</h3>
                            <div class="panel-body">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">Send Newsletter</button>
                                <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Compose Newsletter</h4>
                                            </div>
                                            <form action="" method="post">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <input name="title" class="form-control" placeholder="Enter Title">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Subject</label>
                                                        <input name="subject" class="form-control" placeholder="Enter Subject">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>News</label>
                                                        <textarea name="news" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <input type="submit" name="log" value="Send" class="btn btn-primary">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (isset($_POST["log"])) {
                                $title = $_POST["title"];
                                $subject = $_POST["subject"];
                                $news = $_POST["news"];
                                $conn->query("INSERT INTO newsletterlog(title,subject,news) VALUES('$title','$subject','$news')");
                                echo "<script>alert('Newsletter has been sent')</script>";
                            }
                            ?>
                        </div>
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
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Approval</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $res = $conn->query("SELECT * FROM contact");
                                        while($row = $res->fetch_assoc()) {
                                            $id = $row["id"];
                                            if ($id % 2 == 1) {
                                                echo "<tr class='gradeC'>
                                                        <td>" . $row["fullname"] . "</td>
                                                        <td>" . $row["phoneno"] . "</td>
                                                        <td>" . $row["email"] . "</td>
                                                        <td>" . $row["approval"] . "</td>
                                                        <td><a href='newsletter.php?eid=" . $id . "' class='btn btn-primary'>Permission</a></td>
                                                        <td><a href='newsletterdel.php?eid=" . $id . "' class='btn btn-danger'>Delete</a></td>
                                                    </tr>";
                                            } else {
                                                echo "<tr class='gradeU'>
                                                        <td>" . $row["fullname"] . "</td>
                                                        <td>" . $row["phoneno"] . "</td>
                                                        <td>" . $row["email"] . "</td>
                                                        <td>" . $row["approval"] . "</td>
                                                        <td><a href='newsletter.php?eid=" . $id . "' class='btn btn-primary'>Permission</a></td>
                                                        <td><a href='newsletterdel.php?eid=" . $id . "' class='btn btn-danger'>Delete</a></td>
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
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTables-example').dataTable();
        });
    </script>
    <script src="assets/js/custom-scripts.js"></script>
</body>
</html>