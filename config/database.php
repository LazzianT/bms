<?php
require_once __DIR__ . '/config.php';

$host     = "sql206.infinityfree.com";
$username = "if0_42550511";
$password = "Ib1fcmrl1Jk2";
$database = "if0_42550511_bms";

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
