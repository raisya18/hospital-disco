<?php
include 'db_connect.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Selamat Datang, Admin <?php echo $_SESSION['username']; ?></h2>
    <p><a href="logout.php">Logout</a></p>

    <h3>Menu Admin</h3>
    <ul>
        <li><a href="admin_schedules.php">Kelola Jadwal Dokter (Tambah/Edit)</a></li>
        <li><a href="admin_bookings.php">Lihat & Manipulasi Booking User</a></li>
    </ul>

    <hr>
    
    <h3>Ringkasan Booking Hari Ini</h3>
    <?php
    // Contoh sederhana: Hitung jumlah booking hari ini
    $today = date('Y-m-d');
    $sql_summary = "SELECT COUNT(id) AS total FROM bookings WHERE DATE(booking_date) = '$today'";
    $summary_result = $conn->query($sql_summary)->fetch_assoc();
    echo "<p>Total Booking Hari Ini: <strong>{$summary_result['total']}</strong></p>";
    ?>

</body>
</html>