<?php
// tambah_buku.php
session_start();
include 'koneksi.php';

// Akses hanya admin & petugas
if (!in_array($_SESSION['role'], ['admin', 'petugas'])) {
    die("Akses ditolak");
}

/* -----------------------
   Konfigurasi upload
   ----------------------- */
$uploadDir    = "uploads/cover/";
$maxFileSize  = 2 * 1024 * 1024; // 2MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

/* Buat folder upload jika belum ada */
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* -----------------------
   Proses Tambah Buku
   ----------------------- */
if (isset($_POST['tambah'])) {

    // Sanitasi input
    $judul     = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $pengarang = mysqli_real_escape_string($koneksi, trim($_POST['pengarang']));
    $tahun     = (int) $_POST['tahun'];

    // Validasi sederhana
    if ($judul == "" || $pengarang == "" || $tahun <= 0) {
        $msg = "Lengkapi semua field dengan benar.";
    } else {
        // Periksa file upload
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

            $fileTmp  = $_FILES['cover']['tmp_name'];
            $fileName = basename($_FILES['cover']['name']);
            $fileType = mime_content_type($fileTmp);
            $fileSize = $_FILES['cover']['size'];

            // Validasi tipe & ukuran
            if (!in_array($fileType, $allowedTypes)) {
                $msg = "Tipe file tidak diperbolehkan. Gunakan JPG/PNG/GIF.";
            } elseif ($fileSize > $maxFileSize) {
                $msg = "Ukuran file terlalu besar (maks 2MB).";
            } else {
                // Buat nama file unik
                $ext       = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName   = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
                $targetLoc = $uploadDir . $newName;

                // Pindah file
                if (move_uploaded_file($fileTmp, $targetLoc)) {
                    // Simpan ke DB (kolom cover sudah ada)
                    $sql = "INSERT INTO buku (judul, pengarang, tahun, cover)
                            VALUES ('$judul', '$pengarang', '$tahun', '$targetLoc')";
                    mysqli_query($koneksi, $sql);

                    // Redirect agar form tidak double submit
                    header("Location: tambah_buku.php?success=1");
                    exit;
                } else {
                    $msg = "Gagal menyimpan file cover.";
                }
            }

        } else {
            $msg = "Silakan pilih file cover (JPG/PNG/GIF, max 2MB).";
        }
    }
}

/* Ambil daftar buku (terbaru) */
$buku = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pendataan Buku</title>
<style>
/* =========================
   GLOBAL
========================= */
body {
    margin: 0;
    background: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* =========================
   CONTAINER
========================= */
.container {
    width: 720px;
    margin: 32px auto;
    padding: 22px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
}

h2 {
    text-align: center;
    margin-bottom: 18px;
    color: #333;
}

/* =========================
   BUTTONS & LINKS
========================= */
.back {
    display: inline-block;
    margin-bottom: 12px;
    padding: 8px 12px;
    background: #7f8c8d;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
}

button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 6px;
    background: #3498db;
    color: #fff;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: #246c9c;
}

/* =========================
   FORM
========================= */
.form-row {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
}

.form-row .col {
    flex: 1;
}

input[type="text"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

/* =========================
   MESSAGE FEEDBACK
========================= */
.msg {
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 6px;
}

.msg.success {
    background: #ecf8f1;
    color: #1b6f38;
}

.msg.error {
    background: #fdecea;
    color: #b02a37;
}

/* =========================
   TABLE
========================= */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

.table th,
.table td {
    padding: 10px;
    border: 1px solid #e1e1e1;
    text-align: left;
    vertical-align: middle;
}

.table th {
    background: #3498db;
    color: #fff;
}

/* =========================
   COVER IMAGE
========================= */
.cover {
    width: 70px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* =========================
   TABLE ACTION BUTTONS
========================= */
.actions a {
    display: inline-block;
    margin-right: 6px;
    padding: 6px 10px;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
}

.actions .edit {
    background: #f39c12;
}

.actions .delete {
    background: #e74c3c;
}


</style>
</head>
<body>

<div class="container">

    <a class="back" href="admin_dashboard.php">← Kembali</a>

    <h2>Pendataan Buku</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="msg success">Buku berhasil ditambahkan.</div>
    <?php endif; ?>

    <?php if (!empty($msg)): ?>
        <div class="msg error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Form Tambah Buku (satu halaman dengan daftar) -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <input type="text" name="judul" placeholder="Judul Buku" required>
            </div>
            <div class="col">
                <input type="text" name="pengarang" placeholder="Pengarang" required>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <input type="number" name="tahun" placeholder="Tahun Terbit" required>
            </div>
            <div class="col">
                <input type="file" name="cover" accept="image/*" required>
            </div>
        </div>

        <button name="tambah">Tambah Buku</button>
    </form>

    <!-- Tabel daftar buku -->
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Tahun</th>
            <th>Aksi</th>
        </tr>

        <?php while ($r = mysqli_fetch_assoc($buku)) : ?>
            <tr>
                <td><?= $r['id']; ?></td>
                <td>
                    <?php if (!empty($r['cover']) && file_exists($r['cover'])): ?>
                        <img class="thumb" src="<?= htmlspecialchars($r['cover']); ?>" alt="cover" class="cover">
                    <?php else: ?>
                        <span style="color:#999;font-size:13px;">(tidak ada)</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['judul']); ?></td>
                <td><?= htmlspecialchars($r['pengarang']); ?></td>
                <td><?= htmlspecialchars($r['tahun']); ?></td>
                <td class="actions">
                    <a class="edit" href="edit_buku.php?id=<?= $r['id']; ?>">Edit</a>
                    <a class="delete" href="hapus_buku.php?id=<?= $r['id']; ?>"
                       onclick="return confirm('Hapus buku ini beserta covernya?');">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>
</div>

</body>
</html>
