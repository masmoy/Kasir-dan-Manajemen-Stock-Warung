<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

// Cek apakah form disubmit dan keranjang tidak kosong
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {

    // 1. Ambil data dari form
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $status_hutang = ($metode_pembayaran == 'hutang') ? 'belum-lunas' : 'lunas';

    // 2. Buat ID Transaksi Unik (LOGIKA DIPERBAIKI)
    $prefix = '';
    if ($metode_pembayaran == 'tunai') $prefix = 'TN';
    if ($metode_pembayaran == 'non-tunai') $prefix = 'NT';
    if ($metode_pembayaran == 'hutang') $prefix = 'HT';
    
    $tanggal = date('dmy'); // Format tanggal ddmmyy
    
    // --- PERBAIKAN LOGIKA DISINI ---
    // Cari nomor urut terakhir untuk hari ini dengan prefix yang sesuai
    $query_no_urut = "SELECT MAX(CAST(SUBSTRING(id_transaksi, 10) AS UNSIGNED)) as no_terakhir 
                      FROM transaksi 
                      WHERE SUBSTRING(id_transaksi, 1, 2) = '$prefix' AND SUBSTRING(id_transaksi, 3, 6) = '$tanggal'";
    
    $result_no_urut = mysqli_query($koneksi, $query_no_urut);
    $row_no_urut = mysqli_fetch_assoc($result_no_urut);
    
    // Jika belum ada transaksi hari ini, no_terakhir akan NULL, kita set ke 0
    $no_urut_terakhir = $row_no_urut['no_terakhir'] ?? 0;
    
    // Tambah 1 untuk nomor urut baru
    $no_urut_baru = $no_urut_terakhir + 1;
    
    $id_transaksi = $prefix . $tanggal . '-' . sprintf('%03d', $no_urut_baru);
    // --- AKHIR PERBAIKAN ---

    // 3. Hitung Total Omset dan Laba
    $total_omset = 0;
    $total_laba = 0;
    foreach ($_SESSION['keranjang'] as $id_barang => $item) {
        $query_barang = mysqli_query($koneksi, "SELECT harga_beli FROM barang WHERE id_barang = '$id_barang'");
        $barang = mysqli_fetch_assoc($query_barang);
        $harga_beli = $barang['harga_beli'];
        
        $subtotal_omset = $item['harga_jual'] * $item['jumlah'];
        $subtotal_laba = ($item['harga_jual'] - $harga_beli) * $item['jumlah'];
        
        $total_omset += $subtotal_omset;
        $total_laba += $subtotal_laba;
    }
    
    // 4. Simpan ke tabel `transaksi`
    $tanggal_sekarang = date('Y-m-d H:i:s'); // Buat timestamp yang benar dari PHP
    $query_transaksi = "INSERT INTO transaksi (id_transaksi, total_omset, total_laba, metode_pembayaran, status_hutang, nama_pelanggan) 
                        VALUES ('$id_transaksi', '$total_omset', '$total_laba', '$metode_pembayaran', '$status_hutang', '$nama_pelanggan')";
    mysqli_query($koneksi, $query_transaksi);
    
    // 5. Simpan ke tabel `detail_transaksi` dan Kurangi Stok
    foreach ($_SESSION['keranjang'] as $id_barang => $item) {
        $jumlah = $item['jumlah'];
        $harga_jual_saat_transaksi = $item['harga_jual'];
        
        $query_barang_detail = mysqli_query($koneksi, "SELECT harga_beli FROM barang WHERE id_barang = '$id_barang'");
        $barang_detail = mysqli_fetch_assoc($query_barang_detail);
        $harga_beli_detail = $barang_detail['harga_beli'];

        $subtotal_omset_detail = $harga_jual_saat_transaksi * $jumlah;
        $subtotal_laba_detail = ($harga_jual_saat_transaksi - $harga_beli_detail) * $jumlah;
        
        $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, harga_jual_saat_transaksi, subtotal_omset, subtotal_laba)
                         VALUES ('$id_transaksi', '$id_barang', '$jumlah', '$harga_jual_saat_transaksi', '$subtotal_omset_detail', '$subtotal_laba_detail')";
        mysqli_query($koneksi, $query_detail);
        
        $query_stok = "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'";
        mysqli_query($koneksi, $query_stok);
    }
    
    // 6. Kosongkan keranjang dan simpan ID untuk struk
    $_SESSION['id_transaksi_terakhir'] = $id_transaksi;
    unset($_SESSION['keranjang']);
    
    // 7. Arahkan ke halaman cetak struk
    header("Location: struk.php");
    exit();

} else {
    echo "Keranjang kosong atau terjadi kesalahan.";
    // header("Location: index.php");
    // exit();
}
?>