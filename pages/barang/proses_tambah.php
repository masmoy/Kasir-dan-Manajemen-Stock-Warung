<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
// 1. Sertakan file koneksi database
require_once '../../config/database.php';

// 2. Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Ambil data dari form dan lakukan sanitasi dasar
    // mysqli_real_escape_string membantu mencegah SQL Injection
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $harga_beli  = mysqli_real_escape_string($koneksi, $_POST['harga_beli']);
    $harga_jual  = mysqli_real_escape_string($koneksi, $_POST['harga_jual']);
    $stok        = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    // 4. Buat query INSERT untuk menambahkan data ke tabel barang
    $query = "INSERT INTO barang (kode_barang, nama_barang, harga_beli, harga_jual, stok, satuan) 
              VALUES ('$kode_barang', '$nama_barang', '$harga_beli', '$harga_jual', '$stok', '$satuan')";

    // 5. Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, redirect (alihkan) ke halaman utama manajemen barang
        header("Location: index.php?status=tambah_sukses");
        exit(); // Hentikan skrip setelah redirect
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }

    // Tutup koneksi
    mysqli_close($koneksi);

} else {
    // Jika file diakses langsung tanpa submit form, redirect ke halaman tambah
    header("Location: tambah.php");
    exit();
}
?>