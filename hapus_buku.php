<?php
// hapus_buku.php
session_start();
include 'koneksi.php';

// Akses hanya admin & petugas
if (!in_array($_SESSION['role'], ['admin', 'petugas'])) {
    die("Akses ditolak");
}

if (!isset($_GET['id'])) {
    die("ID buku tidak ditemukan.");
}

$id = (int) $_GET['id'];

/* Ambil info buku untuk mendapatkan path cover */
$res = mysqli_query($koneksi, "SELECT cover FROM buku WHERE id = $id");
if (mysqli_num_rows($res) == 0) {
    die("Buku tidak ditemukan.");
}
$row = mysqli_fetch_assoc($res);

/* Hapus file cover jika ada */
if (!empty($row['cover']) && file_exists($row['cover'])) {
    @unlink($row['cover']);
}

/* Hapus record buku */
mysqli_query($koneksi, "DELETE FROM buku WHERE id = $id");

/* Redirect kembali ke halaman tambah_buku.php */
header("Location: tambah_buku.php?deleted=1");
exit;
?>
