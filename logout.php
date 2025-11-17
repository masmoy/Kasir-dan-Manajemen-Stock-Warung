<?php
session_start(); // Wajib untuk mengakses sesi yang sedang berjalan

// 1. Hapus semua variabel sesi
$_SESSION = array();

// 2. Hancurkan sesi
session_destroy();

// 3. Alihkan pengguna ke halaman login
header("Location: login.php");
exit; // Pastikan tidak ada kode lain yang dieksekusi setelah redirect
?>