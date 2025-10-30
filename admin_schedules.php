<?php
include 'db_connect.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';

// --- LOGIKA TAMBAH DOKTER (Jika belum ada admin_doctors.php) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_doctor') {
    $name = $conn->real_escape_string($_POST['doctor_name']);
    $specialty = $conn->real_escape_string($_POST['specialty']);
    
    $sql = "INSERT INTO doctors (name, specialty) VALUES ('$name', '$specialty')";
    if ($conn->query($sql) === TRUE) {
        $message = "Dokter baru berhasil ditambahkan!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// --- LOGIKA TAMBAH JADWAL ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_schedule') {
    $doctor_id = $_POST['doctor_id'];
    $day = $conn->real_escape_string($_POST['day']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_slots = $_POST['max_slots'];

    $sql = "INSERT INTO schedules (doctor_id, day, start_time, end_time, max_slots) 
            VALUES ('$doctor_id', '$day', '$start_time', '$end_time', '$max_slots')";
            
    if ($conn->query($sql) === TRUE) {
        $message = "Jadwal baru berhasil ditambahkan!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin - Kelola Jadwal</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container" style="max-width: 900px; margin: auto;">
        <h2>Kelola Jadwal Dokter</h2>
        <p><a href="admin_dashboard.php">← Kembali ke Dashboard Admin</a> | <a href="logout.php">Logout</a></p>
        
        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <h3>Tambah Dokter Baru</h3>
        <form method="post" action="admin_schedules.php">
            <input type="hidden" name="action" value="add_doctor">
            <label>Nama Dokter:</label>
            <input type="text" name="doctor_name" required>
            <label>Spesialisasi:</label>
            <input type="text" name="specialty" required>
            <input type="submit" value="Simpan Dokter">
        </form>
        <hr>

        <h3>Tambah Jadwal Praktik</h3>
        <form method="post" action="admin_schedules.php">
            <input type="hidden" name="action" value="add_schedule">
            <label>Pilih Dokter:</label>
            <select name="doctor_id" required>
                <option value="">-- Pilih Dokter --</option>
                <?php
                $sql_doctors = "SELECT id, name, specialty FROM doctors ORDER BY name";
                $result_doctors = $conn->query($sql_doctors);
                if ($result_doctors->num_rows > 0) {
                    while($row = $result_doctors->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']} ({$row['specialty']})</option>";
                    }
                }
                ?>
            </select><br>

            <label>Hari:</label>
            <select name="day" required>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select><br>

            <label>Jam Mulai:</label>
            <input type="time" name="start_time" required><br>
            
            <label>Jam Selesai:</label>
            <input type="time" name="end_time" required><br>
            
            <label>Kuota Maksimal (Slots):</label>
            <input type="number" name="max_slots" min="1" required><br>

            <input type="submit" value="Simpan Jadwal">
        </form>

        <hr>

        <h3>Daftar Jadwal Dokter Saat Ini</h3>
        <table border="1">
            <tr>
                <th>Dokter</th>
                <th>Spesialisasi</th>
                <th>Hari</th>
                <th>Waktu</th>
                <th>Kuota</th>
            </tr>
            <?php
            $sql_view = "SELECT d.name, d.specialty, s.day, s.start_time, s.end_time, s.max_slots 
                         FROM schedules s JOIN doctors d ON s.doctor_id = d.id 
                         ORDER BY d.name, s.day";
            $result_view = $conn->query($sql_view);

            if ($result_view->num_rows > 0) {
                while($row = $result_view->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['specialty']}</td>";
                    echo "<td>{$row['day']}</td>";
                    echo "<td>{$row['start_time']} - {$row['end_time']}</td>";
                    echo "<td>{$row['max_slots']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Belum ada jadwal yang tersedia.</td></tr>";
            }
            ?>
        </table>

    </div>
</body>
</html>