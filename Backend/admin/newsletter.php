<?php
require 'credentials.php';
$eid = $_GET["eid"];
$approval = "Allowed";
$napproval = "Not Allowed";
$conn = mysqli_connect($host, $user, $password);
mysqli_select_db($conn, $database);
$query = "SELECT * FROM contact where id = '$eid'";
$res = mysqli_query($conn, $query);
$row = mysqli_fetch_array($res);
$id = $row["approval"];
if($id == "Not Allowed") {
    $queryupd = "UPDATE contact SET `approval`= '$approval' WHERE id = '$eid'";
    if(mysqli_query($conn, $queryupd)) {
        echo '<script>alert("Subscriber status changed to Allowed")</script>';
        echo '<meta http-equiv="refresh" content="1; URL=messages.php" />';
    }
} else {
    $queryup = "UPDATE contact SET `approval`= '$napproval' WHERE id = '$eid'";
    if(mysqli_query($conn, $queryup)) {
        echo '<script>alert("Subscriber status changed to Not Allowed")</script>';
        echo '<meta http-equiv="refresh" content="1; URL=messages.php" />';
    }
}
?>