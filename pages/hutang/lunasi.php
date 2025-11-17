<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

// Cek apakah data dikirim melalui POST dari modal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_transaksi']) && isset($_POST['metode_pelunasan'])) {
    
    $id_transaksi = mysqli_real_escape_string($koneksi, $_POST['id_transaksi']);
    $metode_pelunasan = mysqli_real_escape_string($koneksi, $_POST['metode_pelunasan']);

    // Buat query UPDATE untuk mengubah status dan metode pelunasan
    $query = "UPDATE transaksi SET 
                status_hutang = 'lunas',
                metode_pelunasan = '$metode_pelunasan' 
              WHERE id_transaksi = '$id_transaksi'";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, redirect kembali ke halaman daftar hutang
        header("Location: index.php?status=lunas_sukses");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: Gagal mengupdate status hutang. " . mysqli_error($koneksi);
    }
} else {
    // Jika diakses secara tidak benar, redirect kembali
    header("Location: index.php");
    exit();
}
?>