<?php
// admin_register_petugas.php
// Hanya admin yang bisa membuat akun petugas

session_start();
include 'koneksi.php';

// Batasi akses hanya admin
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak");
}

// Proses buat akun petugas
if (isset($_POST['buat'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    mysqli_query(
        $koneksi,
        "INSERT INTO users (nama, username, password, role) 
         VALUES ('$nama', '$username', '$password', 'petugas')"
    );

    echo "<script>
            alert('Petugas berhasil dibuat');
          </script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Petugas</title>
    <style>
        /* ===== Reset & Font ===== */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        /* ===== Container ===== */
        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* ===== Judul ===== */
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        /* ===== Input ===== */
        input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #f39c12;
            outline: none;
            box-shadow: 0 0 5px rgba(243,156,18,0.3);
        }

        /* ===== Tombol ===== */
        button {
            width: 100%;
            padding: 12px;
            background: #f39c12;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background: #d68910;
        }
    </style>
</head>
<body>
    <div class="container">
                <!-- Tombol Kembali -->
        <div style="margin-bottom:15px;">
            <a href="admin_dashboard.php" 
            style="text-decoration:none; padding:8px 12px; background:#3498db; color:white; border-radius:6px;">
            ← Kembali
            </a>
        </div>
        <h2>Buat Akun Petugas</h2>
        <form method="POST">
            <input type="text" name="nama" placeholder="Nama petugas" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="buat">BUAT PETUGAS</button>
        </form>
    </div>
</body>
</html>
