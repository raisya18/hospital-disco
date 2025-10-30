<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sistem Rumah Sakit - Beranda</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Gaya spesifik untuk index.php */
        .role-selection {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 50px;
        }
        .role-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 250px;
            transition: transform 0.3s;
        }
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .role-card h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .role-card a {
            display: inline-block;
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .role-card a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px; margin: auto; text-align: center;">
        <h2>🏥 Selamat Datang di Sistem Informasi Rumah Sakit</h2>
        <p>Silakan pilih peran Anda untuk melanjutkan ke sistem.</p>

        <div class="role-selection">
            
            <div class="role-card">
                <h3>Pasien / User</h3>
                <p>Booking jadwal dokter dan cek status antrian.</p>
                <a href="login.php?role=user">Login sebagai User</a>
            </div>

            <div class="role-card">
                <h3>Admin / Petugas RS</h3>
                <p>Kelola data dokter, jadwal, dan booking pasien.</p>
                <a href="login.php?role=admin">Login sebagai Admin</a>
            </div>

        </div>
    </div>
</body>
</html>