<?php
// Menggunakan koneksi database yang sudah include session_start()
include 'db_connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    // Gunakan real_escape_string untuk sanitasi dasar (walaupun prepared statements lebih baik)
    $username = $conn->real_escape_string($_POST['username']); 
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan username
    $sql = "SELECT id, password, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Verifikasi password yang di-hash
        if (password_verify($password, $row['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $username;

            // Redirect berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: user_dashboard.php");
                exit();
            }
        } else {
            $message = "Password salah. Silakan coba lagi.";
        }
    } else {
        $message = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Sistem Rumah Sakit</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 50px auto;"> 
        <h2>Login Sistem Rumah Sakit</h2>
        
        <?php if ($message): ?>
            <p style="color:red; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            
            <input type="submit" value="Login">
        </form>
        
        <p>Belum punya akun user? <a href="register.php">Daftar di sini</a>.</p>
        
        <p><a href="index.php">← Kembali ke Beranda</a></p>
    </div>
</body>
</html>