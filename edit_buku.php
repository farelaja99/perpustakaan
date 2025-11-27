<?php
// edit_buku.php
session_start();
include 'koneksi.php';

// Akses hanya admin & petugas
if (!in_array($_SESSION['role'], ['admin', 'petugas'])) {
    die("Akses ditolak");
}

/* Konfigurasi upload sama seperti tambah */
$uploadDir    = "uploads/cover/";
$maxFileSize  = 2 * 1024 * 1024; // 2MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

/* Buat folder jika belum ada */
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* Ambil ID buku dari GET */
if (!isset($_GET['id'])) {
    die("ID buku tidak ditemukan.");
}
$id = (int) $_GET['id'];

/* Ambil data buku */
$res = mysqli_query($koneksi, "SELECT * FROM buku WHERE id = $id");
if (mysqli_num_rows($res) == 0) {
    die("Buku tidak ditemukan.");
}
$buku = mysqli_fetch_assoc($res);
$msg  = "";

/* Proses update */
if (isset($_POST['update'])) {
    $judul     = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $pengarang = mysqli_real_escape_string($koneksi, trim($_POST['pengarang']));
    $tahun     = (int) $_POST['tahun'];

    $coverPath = $buku['cover']; // default tetap cover lama

    // Jika ada file baru di upload
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

        $fileTmp  = $_FILES['cover']['tmp_name'];
        $fileName = basename($_FILES['cover']['name']);
        $fileType = mime_content_type($fileTmp);
        $fileSize = $_FILES['cover']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $msg = "Tipe file tidak diperbolehkan.";
        } elseif ($fileSize > $maxFileSize) {
            $msg = "Ukuran file terlalu besar (maks 2MB).";
        } else {
            // buat nama file baru
            $ext     = pathinfo($fileName, PATHINFO_EXTENSION);
            $newName = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $target  = $uploadDir . $newName;

            if (move_uploaded_file($fileTmp, $target)) {
                // hapus cover lama jika ada dan file exist
                if (!empty($buku['cover']) && file_exists($buku['cover'])) {
                    @unlink($buku['cover']);
                }
                $coverPath = $target;
            } else {
                $msg = "Gagal menyimpan cover baru.";
            }
        }
    }

    // Jika tidak ada error, update DB
    if ($msg == "") {
        $sql = "UPDATE buku SET
                    judul     = '$judul',
                    pengarang = '$pengarang',
                    tahun     = '$tahun',
                    cover     = '$coverPath'
                WHERE id = $id";
        mysqli_query($koneksi, $sql);

        header("Location: tambah_buku.php?updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Buku</title>
<style>
body { margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f2f5; }
.container { width:640px; margin:36px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 8px 18px rgba(0,0,0,0.08); }
h2 { text-align:center; color:#333; margin-bottom:14px; }
input[type="text"], input[type="number"], input[type="file"], button { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; box-sizing:border-box; }
.btn { display:inline-block; padding:8px 12px; text-decoration:none; border-radius:6px; color:#fff; }
.back { background:#7f8c8d; }
.save { background:#3498db; color:white }
.preview { margin-top:8px; }
.msg { color:#b02a37; margin-bottom:8px; }
</style>
</head>
<body>
<div class="container">
    <a class="btn back" href="javascript:history.back()">← Kembali</a>
    <h2>Edit Buku</h2>

    <?php if ($msg != ""): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Judul</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required>

        <label>Pengarang</label>
        <input type="text" name="pengarang" value="<?= htmlspecialchars($buku['pengarang']) ?>" required>

        <label>Tahun</label>
        <input type="number" name="tahun" value="<?= htmlspecialchars($buku['tahun']) ?>" required>

        <label>Cover Saat Ini</label>
        <?php if (!empty($buku['cover']) && file_exists($buku['cover'])): ?>
            <div class="preview">
                <img src="<?= htmlspecialchars($buku['cover']) ?>" width="120" style="border-radius:6px;">
            </div>
        <?php else: ?>
            <div style="color:#999;">(tidak ada cover)</div>
        <?php endif; ?>

        <label>Ganti Cover (opsional)</label>
        <input type="file" name="cover" accept="image/*">

        <button class="save" name="update">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
