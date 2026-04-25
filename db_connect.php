<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calenda";

date_default_timezone_set('Asia/Bangkok');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET time_zone = '+07:00'");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>