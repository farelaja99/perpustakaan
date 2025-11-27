<?php
// laporan.php
session_start();
include 'koneksi.php';

// Batasi akses untuk admin, petugas, dan peminjam
if (!in_array($_SESSION['role'], ['admin','petugas','peminjam'])) {
    die("Akses ditolak");
}

// Ambil laporan sesuai role
if ($_SESSION['role'] == 'peminjam') {
    // Laporan peminjam sendiri
    $query = mysqli_query(
        $koneksi,
        "SELECT p.id, b.cover, b.judul, b.pengarang, p.tgl_pinjam 
         FROM peminjaman p 
         JOIN buku b ON p.id_buku=b.id 
         WHERE p.id_user=".$_SESSION['id']
    );
} else {
    // Laporan untuk admin/petugas (semua peminjaman)
    $query = mysqli_query(
        $koneksi,
        "SELECT p.id, u.nama, b.cover, b.judul, b.pengarang, p.tgl_pinjam 
         FROM peminjaman p 
         JOIN buku b ON p.id_buku=b.id 
         JOIN users u ON p.id_user=u.id
         ORDER BY p.tgl_pinjam DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        .container {
            width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #3498db;
            color: white;
        }

        img {
            width: 60px;
            border-radius: 6px;
        }

        .cover {
            width: 160px;
            height: 240px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

    </style>
</head>
<body>
<div class="container">

    <!-- Tombol Kembali -->
    <div style="margin-bottom:15px;">
        <a href="javascript:history.back()" 
        style="text-decoration:none; padding:8px 12px; background:#7f8c8d; color:white; border-radius:6px;">
        ← Kembali
        </a>
    </div>

    <h2>Laporan Peminjaman</h2>

    <table>
        <tr>
            <th>ID</th>
            <?php if ($_SESSION['role'] != 'peminjam'): ?>
                <th>Nama Peminjam</th>
            <?php endif; ?>
            <th>Cover</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Tanggal Pinjam</th>
        </tr>

        <?php while($r = mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $r['id']; ?></td>
            <?php if ($_SESSION['role'] != 'peminjam'): ?>
                <td><?= $r['nama']; ?></td>
            <?php endif; ?>
            <td>
                <?php if ($r['cover'] != ""): ?>
                    <img src="<?= $r['cover']; ?>" class="cover">
                <?php else: ?>
                    <span style="color:#999;">Tidak Ada</span>
                <?php endif; ?>
            </td>
            <td><?= $r['judul']; ?></td>
            <td><?= $r['pengarang']; ?></td>
            <td><?= $r['tgl_pinjam']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
