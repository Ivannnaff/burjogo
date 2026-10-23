<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_burjogo";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database SQL gagal: " . mysqli_connect_error());
}
?>