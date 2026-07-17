<?php
require 'credentials.php';
$conn = mysqli_connect($host, $user, $password);
mysqli_select_db($conn, $database);
$id = $_GET["eid"];
$query = "DELETE FROM login WHERE id = $id";
if(mysqli_query($conn, $query)) {
    echo '<script>alert("Deleted")</script>';
}
echo '<meta http-equiv="refresh" content="1; URL=usersettings.php" />';
?>