<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
// Aktifkan pelaporan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Sertakan file koneksi
require_once '../../config/database.php';

// 2. Cek apakah form disubmit dan koneksi berhasil
if ($_SERVER["REQUEST_METHOD"] == "POST" && $koneksi) {

    // 3. Ambil data dari form dan lakukan sanitasi
    $id_barang   = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $harga_beli  = mysqli_real_escape_string($koneksi, $_POST['harga_beli']);
    $harga_jual  = mysqli_real_escape_string($koneksi, $_POST['harga_jual']);
    $stok        = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    // 4. Buat query UPDATE
    $query = "UPDATE barang SET 
                kode_barang = '$kode_barang', 
                nama_barang = '$nama_barang', 
                harga_beli = '$harga_beli', 
                harga_jual = '$harga_jual', 
                stok = '$stok', 
                satuan = '$satuan' 
              WHERE id_barang = '$id_barang'";

    // 5. Eksekusi query dan cek hasilnya
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, redirect ke halaman utama dengan status sukses
        header("Location: index.php?status=edit_sukses");
        exit();
    } else {
        // Jika query gagal, tampilkan pesan error dari MySQL
        echo "Query Gagal Dijalankan: " . mysqli_error($koneksi);
    }

    // Tutup koneksi
    mysqli_close($koneksi);

} else {
    // Tangani jika koneksi gagal atau file diakses langsung
    if (!$koneksi) {
        echo "Koneksi database gagal. Periksa file config/database.php";
    } else {
        header("Location: index.php");
        exit();
    }
}
?>