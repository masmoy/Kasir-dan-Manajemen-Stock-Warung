<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

// Cek apakah ada aksi yang dikirim
if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];
    
    // Aksi untuk menambah barang ke keranjang
    if ($aksi == 'tambah' && isset($_GET['id'])) {
        $id_barang = $_GET['id'];
        
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }
        
        if (isset($_SESSION['keranjang'][$id_barang])) {
            $_SESSION['keranjang'][$id_barang]['jumlah']++;
        } else {
            $query = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
            $barang = mysqli_fetch_assoc($query);
            
            if ($barang) {
                $_SESSION['keranjang'][$id_barang] = [
                    "nama_barang" => $barang['nama_barang'],
                    "harga_jual"  => $barang['harga_jual'],
                    "jumlah"      => 1
                ];
            }
        }
    }
    
    // Aksi untuk menghapus barang dari keranjang
    if ($aksi == 'hapus' && isset($_GET['id'])) {
        $id_barang = $_GET['id'];
        if (isset($_SESSION['keranjang'][$id_barang])) {
            unset($_SESSION['keranjang'][$id_barang]);
        }
    }
    
    // Aksi untuk mengosongkan keranjang
    if ($aksi == 'kosongkan') {
        $_SESSION['keranjang'] = [];
    }

    // Aksi untuk update jumlah
    if ($aksi == 'update') {
        if (isset($_POST['jumlah'])) {
            foreach ($_POST['jumlah'] as $id_barang => $jumlah) {
                $jumlah = (int)$jumlah;
                if ($jumlah > 0) {
                    if (isset($_SESSION['keranjang'][$id_barang])) {
                        $_SESSION['keranjang'][$id_barang]['jumlah'] = $jumlah;
                    }
                } else {
                    unset($_SESSION['keranjang'][$id_barang]);
                }
            }
        }
    }
}

// --- PERUBAHAN KRUSIAL DI SINI ---
// Siapkan URL untuk redirect kembali
$redirect_url = "index.php";

// Cek apakah ada parameter 'search' dari halaman kasir
if (isset($_GET['search'])) {
    // Jika ada, tambahkan ke URL redirect
    $redirect_url .= "?search=" . urlencode($_GET['search']);
}

// Setelah memproses, kembalikan ke halaman kasir dengan atau tanpa kata kunci pencarian
header("Location: " . $redirect_url);
exit();
?>