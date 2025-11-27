<?php
// register_peminjam.php
// Registrasi untuk peminjam

include 'koneksi.php';

// Proses registrasi
if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Enkripsi password

    // Simpan ke database
    mysqli_query(
        $koneksi,
        "INSERT INTO users (nama, username, password, role) 
         VALUES ('$nama', '$username', '$password', 'peminjam')"
    );

    echo "<script>
            alert('Registrasi berhasil!');
            window.location='login.php';
          </script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Peminjam</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        .container {
            width: 360px;
            margin: 100px auto;
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background: #2980b9;
        }

        /* Tombol kembali */
        .back-btn {
            display: inline-block;
            margin-bottom: 18px;
            text-decoration: none;
            padding: 8px 12px;
            background: #7f8c8d;
            color: white;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #616669;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Tombol Kembali -->
        <a href="login.php" class="back-btn">← Kembali</a>

        <h2>Registrasi Peminjam</h2>

        <form method="POST">
            <input type="text" name="nama" placeholder="Nama lengkap" required>
            <input type="text" name="username" placeholder="Buat username" required>
            <input type="password" name="password" placeholder="Buat password" required>
            <button name="register">REGISTER</button>
        </form>
    </div>

</body>
</html>
