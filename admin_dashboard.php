<?php
// admin_dashboard.php
session_start();
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        /* ===== Reset & Font ===== */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        /* ===== Navbar ===== */
        .navbar {
            background: #3498db;
            color: white;
            padding: 15px;
            font-size: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            background: #e74c3c;
            border-radius: 6px;
        }

        .navbar a:hover {
            background: #c0392b;
        }

        /* ===== Container ===== */
        .container {
            padding: 20px;
        }

        /* ===== Card Menu ===== */
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            text-align: center;
        }

        .card a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <span>Dashboard Admin</span>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Konten -->
    <div class="container">
        <h2>Selamat datang, <?= $_SESSION['nama']; ?></h2>

        <div class="card">
            <a href="admin_register_petugas.php">Buat Petugas</a>
        </div>

        <div class="card">
            <a href="tambah_buku.php">Pendataan Buku</a>
        </div>

        <div class="card">
            <a href="laporan.php">Laporan Peminjaman</a>
        </div>
    </div>

</body>
</html>
