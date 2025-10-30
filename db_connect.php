<?php
// Mulai session di awal setiap file PHP yang memerlukan session
session_start();

$servername = "localhost";
$username = "root"; // Sesuaikan jika berbeda
$password = "";     // Sesuaikan jika berbeda
$dbname = "hospital_db";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>