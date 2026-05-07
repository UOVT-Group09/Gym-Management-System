<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gym_management";

$conn = new mysqli($host, $user, $pass, $db, 3308);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
