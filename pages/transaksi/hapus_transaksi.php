<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Ambil detail transaksi untuk tahu barang apa saja yang harus dikembalikan stoknya
    $query_detail = "SELECT id_barang, jumlah FROM detail_transaksi WHERE id_transaksi = '$id_transaksi'";
    $result_detail = mysqli_query($koneksi, $query_detail);

    if ($result_detail) {
        // 2. Kembalikan stok untuk setiap barang
        while ($item = mysqli_fetch_assoc($result_detail)) {
            $id_barang = $item['id_barang'];
            $jumlah = $item['jumlah'];
            $query_update_stok = "UPDATE barang SET stok = stok + $jumlah WHERE id_barang = '$id_barang'";
            mysqli_query($koneksi, $query_update_stok);
        }

        // 3. Hapus data dari tabel `transaksi` (data di `detail_transaksi` akan terhapus otomatis karena ON DELETE CASCADE)
        $query_hapus_transaksi = "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'";
        if (mysqli_query($koneksi, $query_hapus_transaksi)) {
            header("Location: daftar_transaksi.php?status=hapus_sukses");
            exit();
        } else {
            die("Gagal menghapus transaksi: " . mysqli_error($koneksi));
        }
    } else {
        die("Gagal mengambil detail transaksi: " . mysqli_error($koneksi));
    }
} else {
    header("Location: daftar_transaksi.php");
    exit();
}
?>