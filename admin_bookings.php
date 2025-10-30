<?php
include 'db_connect.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';

// --- LOGIKA MANIPULASI STATUS BOOKING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id']) && isset($_POST['new_status'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    // Pastikan status yang dikirim valid
    $valid_statuses = ['pending', 'confirmed', 'done', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $sql_update = "UPDATE bookings SET status = '$new_status' WHERE id = '$booking_id'";
        
        if ($conn->query($sql_update) === TRUE) {
            $message = "Status Booking ID #{$booking_id} berhasil diubah menjadi **{$new_status}**.";
        } else {
            $message = "Error saat update status: " . $conn->error;
        }
    } else {
        $message = "Status tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin - Kelola Booking</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container" style="max-width: 1100px; margin: auto;">
        <h2>Kelola Booking (Antrian) Pasien</h2>
        <p><a href="admin_dashboard.php">← Kembali ke Dashboard Admin</a> | <a href="logout.php">Logout</a></p>
        
        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <h3>Daftar Semua Booking</h3>
        
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Antrian No.</th>
                <th>Pasien (User)</th>
                <th>Dokter Tujuan</th>
                <th>Jadwal</th>
                <th>Waktu Booking</th>
                <th>Status Saat Ini</th>
                <th>Aksi (Ubah Status)</th>
            </tr>
            <?php
            // Query untuk mendapatkan semua data booking dengan detail
            $sql_bookings = "SELECT b.id AS booking_id, b.booking_number, b.status, b.booking_date, 
                             u.username AS pasien_username, d.name AS doctor_name, 
                             s.day, s.start_time
                             FROM bookings b 
                             JOIN users u ON b.user_id = u.id
                             JOIN schedules s ON b.schedule_id = s.id
                             JOIN doctors d ON s.doctor_id = d.id
                             ORDER BY s.day, s.start_time, b.booking_number ASC";
                             
            $result_bookings = $conn->query($sql_bookings);

            if ($result_bookings->num_rows > 0) {
                while($row = $result_bookings->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['booking_id']}</td>";
                    echo "<td><strong>{$row['booking_number']}</strong></td>";
                    echo "<td>{$row['pasien_username']}</td>";
                    echo "<td>{$row['doctor_name']}</td>";
                    echo "<td>{$row['day']}, Pukul {$row['start_time']}</td>";
                    echo "<td>" . date('Y-m-d H:i', strtotime($row['booking_date'])) . "</td>";
                    echo "<td><span class='status-{$row['status']}'>{$row['status']}</span></td>";
                    
                    // Form Aksi untuk mengubah Status
                    echo "<td>";
                    echo "<form method='post' action='admin_bookings.php' style='margin: 0;'>";
                    echo "<input type='hidden' name='booking_id' value='{$row['booking_id']}'>";
                    echo "<select name='new_status' required>";
                    echo "<option value='confirmed'" . ($row['status'] == 'confirmed' ? ' selected' : '') . ">Confirmed</option>";
                    echo "<option value='pending'" . ($row['status'] == 'pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='done'" . ($row['status'] == 'done' ? ' selected' : '') . ">Done</option>";
                    echo "<option value='cancelled'" . ($row['status'] == 'cancelled' ? ' selected' : '') . ">Cancelled</option>";
                    echo "</select>";
                    echo "<input type='submit' value='Ubah' style='padding: 5px; margin-left: 5px;'>";
                    echo "</form>";
                    echo "</td>";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Belum ada booking yang tercatat.</td></tr>";
            }
            ?>
        </table>

    </div>
</body>
</html>