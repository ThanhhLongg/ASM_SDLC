<?php
$severname = "localhost";
$username = "root";
$password = "";
$dbname = "book databasee";

$conn = new mysqli($severname, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
