<?php
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $schedule_id = $_POST['schedule_id'];
    
    // 1. Cek kuota dan hitung nomor antrian berikutnya (Atomic operation sangat penting di sini, tapi kita gunakan cara sederhana dulu)
    
    // Ambil max_slots
    $sql_schedule = "SELECT max_slots FROM schedules WHERE id = '$schedule_id'";
    $schedule_info = $conn->query($sql_schedule)->fetch_assoc();
    $max_slots = $schedule_info['max_slots'];

    // Hitung jumlah booking saat ini untuk jadwal ini
    $sql_current_bookings = "SELECT COUNT(id) AS current_count, MAX(booking_number) AS last_number FROM bookings WHERE schedule_id = '$schedule_id'";
    $current_info = $conn->query($sql_current_bookings)->fetch_assoc();
    $current_count = $current_info['current_count'];
    $last_number = $current_info['last_number'] ?? 0;
    
    if ($current_count >= $max_slots) {
        // Kuota penuh
        $_SESSION['message'] = "Booking gagal: Kuota untuk jadwal ini sudah penuh.";
        header("Location: user_dashboard.php");
        exit();
    }
    
    // Cek apakah user sudah booking di jadwal ini (untuk mencegah double booking)
    $sql_check_user = "SELECT id FROM bookings WHERE user_id = '$user_id' AND schedule_id = '$schedule_id' AND status IN ('pending', 'confirmed')";
    if ($conn->query($sql_check_user)->num_rows > 0) {
        $_SESSION['message'] = "Booking gagal: Anda sudah memiliki booking aktif untuk jadwal ini.";
        header("Location: user_dashboard.php");
        exit();
    }
    
    // Nomor antrian baru = Nomor antrian terakhir + 1
    $new_booking_number = $last_number + 1;
    
    // 2. Masukkan booking baru
    $sql_insert = "INSERT INTO bookings (user_id, schedule_id, booking_number, status) 
                   VALUES ('$user_id', '$schedule_id', '$new_booking_number', 'pending')";
                   
    if ($conn->query($sql_insert) === TRUE) {
        $_SESSION['message'] = "Booking berhasil! Nomor antrian Anda: **$new_booking_number**.";
    } else {
        $_SESSION['message'] = "Booking gagal: " . $conn->error;
    }
    
    header("Location: user_dashboard.php");
    exit();
} else {
    header("Location: user_dashboard.php");
    exit();
}
?>