<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
// Sertakan file koneksi
require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Buat query DELETE
    $query = "DELETE FROM barang WHERE id_barang = '$id_barang'";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?status=hapus_sukses");
        exit();
    } else {
        // Jika gagal, cek jenis errornya
        // Error nomor 1451 adalah error foreign key constraint (data terpakai)
        if (mysqli_errno($koneksi) == 1451) {
            // Redirect dengan pesan error spesifik
            header("Location: index.php?error=terpakai");
            exit();
        } else {
            // Tampilkan error umum jika masalahnya lain
            echo "Error deleting record: " . mysqli_error($koneksi);
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>