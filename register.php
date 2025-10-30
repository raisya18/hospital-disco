<?php
include 'db_connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Default role adalah 'user'
    $role = 'user'; 

    // Query untuk memasukkan data user
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";

    if ($conn->query($sql) === TRUE) {
        $message = "Registrasi berhasil! Silakan <a href='login.php'>Login</a>.";
    } else {
        // Cek jika username sudah ada (UNIQUE constraint)
        if ($conn->errno == 1062) {
             $message = "Error: Username sudah digunakan.";
        } else {
             $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Registrasi</title>
</head>
<body>
    <h2>Registrasi Akun User</h2>
    <p><?php echo $message; ?></p>
    <form method="post" action="register.php">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Daftar">
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
</body>
</html>