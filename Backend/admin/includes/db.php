<?php
$host = "localhost";
$user = "root";
$password = "12345678";
$database = "restaurant";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>