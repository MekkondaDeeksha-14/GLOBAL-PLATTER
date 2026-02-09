<?php
$host = "localhost";
$dbname = "global_platter";
$user = "root";  // default XAMPP user
$pass = "";      // default XAMPP password is empty

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
