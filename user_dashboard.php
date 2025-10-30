<?php
include 'db_connect.php';

// Cek apakah user sudah login dan role-nya adalah 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard User</title>
</head>
<body>
    <h2>Selamat Datang, User <?php echo $_SESSION['username']; ?></h2>
    <p><a href="logout.php">Logout</a></p>

    <h3>Pilih Dokter & Jadwal</h3>
    <form method="post" action="process_booking.php">
        <label>Pilih Jadwal:</label><br>
        <select name="schedule_id" required>
            <option value="">-- Pilih --</option>
            <?php
            $sql_schedules = "SELECT s.id, d.name, d.specialty, s.day, s.start_time, s.max_slots FROM schedules s JOIN doctors d ON s.doctor_id = d.id";
            $result_schedules = $conn->query($sql_schedules);
            
            if ($result_schedules->num_rows > 0) {
                while($row = $result_schedules->fetch_assoc()) {
                    // Cek ketersediaan slot (ini sangat sederhana, harus ditingkatkan)
                    $sql_booked = "SELECT COUNT(id) AS total_booked FROM bookings WHERE schedule_id = " . $row['id'];
                    $booked_result = $conn->query($sql_booked)->fetch_assoc();
                    $available_slots = $row['max_slots'] - $booked_result['total_booked'];

                    if ($available_slots > 0) {
                        echo "<option value='{$row['id']}'>";
                        echo "{$row['name']} ({$row['specialty']}) - {$row['day']}, {$row['start_time']} (Sisa: {$available_slots})";
                        echo "</option>";
                    }
                }
            } else {
                echo "<option disabled>Tidak ada jadwal tersedia.</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" value="Booking Sekarang">
    </form>
    
    <hr>
    
    <h3>Status Booking Anda</h3>
    <?php
    $sql_bookings = "SELECT b.booking_number, b.status, s.day, s.start_time, d.name FROM bookings b 
                     JOIN schedules s ON b.schedule_id = s.id 
                     JOIN doctors d ON s.doctor_id = d.id 
                     WHERE b.user_id = $user_id ORDER BY b.booking_date DESC";
    $result_bookings = $conn->query($sql_bookings);

    if ($result_bookings->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Dokter</th><th>Jadwal</th><th>Nomor Antrian</th><th>Status</th></tr>";
        while($row = $result_bookings->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['day']}, {$row['start_time']}</td>";
            echo "<td>{$row['booking_number']}</td>";
            echo "<td><strong>{$row['status']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Anda belum memiliki booking.</p>";
    }
    ?>

</body>
</html>