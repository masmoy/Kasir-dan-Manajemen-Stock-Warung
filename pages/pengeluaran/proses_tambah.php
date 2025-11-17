<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis_pengeluaran']);
    $jumlah = mysqli_real_escape_string($koneksi, $_POST['jumlah']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $query = "INSERT INTO pengeluaran (jenis_pengeluaran, jumlah, deskripsi) VALUES ('$jenis', '$jumlah', '$deskripsi')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>