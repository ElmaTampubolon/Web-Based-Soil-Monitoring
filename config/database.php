<?php
// config/database.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_iot";

// Coba lakukan koneksi
$conn = mysqli_connect($servername, $username, $password, $database);

// Periksa Koneksi
if (!$conn) {
    // Catat error detail ke log server
    error_log("Koneksi Database Gagal (config/database.php): [DB: $database] " . mysqli_connect_error());
} else {
    // Atur character set ke utf8mb4 (Praktik Terbaik)
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        error_log("Error saat mengatur character set utf8mb4: " . mysqli_error($conn));
    }
}
?>