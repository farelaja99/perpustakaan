<?php
// ============================ //
//         peminjaman.php       //
// ============================ //

session_start();
include 'koneksi.php';

// Batasi hanya untuk peminjam
if ($_SESSION['role'] != 'peminjam') {
    die("Akses ditolak");
}

// ----------------------- //
//  Proses Peminjaman      //
// ----------------------- //
if (isset($_POST['pinjam'])) {
    $id_buku    = $_POST['id_buku'];
    $id_user    = $_SESSION['id'];
    $tgl_pinjam = date('Y-m-d');

    mysqli_query(
        $koneksi,
        "INSERT INTO peminjaman (id_user, id_buku, tgl_pinjam)
         VALUES ('$id_user', '$id_buku', '$tgl_pinjam')"
    );

    echo "<script>alert('Buku berhasil dipinjam');window.location='peminjaman.php';</script>";
}

// ----------------------- //
//  Proses Pengembalian    //
// ----------------------- //
if (isset($_GET['kembalikan'])) {
    $id = $_GET['kembalikan'];
    mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id='$id'");
    echo "<script>alert('Buku berhasil dikembalikan');window.location='peminjaman.php';</script>";
}

// ----------------------- //
//  Ambil Data Buku        //
// ----------------------- //
$buku = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY judul ASC");

// ----------------------- //
//  Ambil Data Pinjaman    //
// ----------------------- //
$peminjaman = mysqli_query(
    $koneksi,
    "SELECT p.id, b.judul, b.pengarang, b.cover, p.tgl_pinjam
     FROM peminjaman p
     JOIN buku b ON p.id_buku = b.id
     WHERE p.id_user = " . $_SESSION['id']
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman Buku</title>

    <style>
        body {
            margin: 0;
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            width: 850px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 22px;
            color: #333;
        }
        select, button {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin: 6px 0;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: .3s;
        }
        button:hover { background: #246c9c; }
        table {
            width: 100%;
            margin-top: 22px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #3498db;
            color: white;
        }
        img {
            width: 55px;
            height: 75px;
            object-fit: cover;
            border-radius: 4px;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #3498db;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-back:hover { background: #246c9c; }
        .btn-return {
            padding: 6px 10px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .btn-return:hover { background: #c0392b; }

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

    <!-- Tombol kembali -->
    <a class="btn-back" href="peminjam_dashboard.php">← Kembali</a>

    <h2>Peminjaman Buku</h2>

    <!-- Form Pinjam -->
    <form method="POST">
        <select name="id_buku" required>
            <option value="">Pilih Buku</option>
            <?php while ($b = mysqli_fetch_assoc($buku)) : ?>
                <option value="<?= $b['id']; ?>">
                    <?= $b['judul']; ?> - <?= $b['pengarang']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button name="pinjam">Pinjam Buku</button>
    </form>

    <!-- Tabel daftar peminjaman -->
    <table>
    <tr>
        <th>ID</th>
        <th>Cover</th>
        <th>Judul Buku</th>
        <th>Pengarang</th>
        <th>Tanggal Pinjam</th>
        <th>Aksi</th>
    </tr>

    <?php while ($p = mysqli_fetch_assoc($peminjaman)) : ?>
    <tr>
        <td><?= $p['id']; ?></td>
        <td>
       <img src="<?= $p['cover']; ?>" class="cover">
        </td>
        <td><?= $p['judul']; ?></td>
        <td><?= $p['pengarang']; ?></td>
        <td><?= $p['tgl_pinjam']; ?></td>
        <td>
            <a class="btn-return"
               onclick="return confirm('Yakin ingin mengembalikan buku?')"
               href="?kembalikan=<?= $p['id']; ?>">
               Kembalikan
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</div>
</body>
</html>
