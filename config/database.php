<?php
// File: config/database.php

// 1. Definisikan parameter koneksi database
$host     = "localhost";
$user     = "root";
$password = ""; // Biasanya kosong jika menggunakan XAMPP default
$database = "db_warung";

// 2. Buat koneksi ke database menggunakan mysqli
$koneksi = mysqli_connect($host, $user, $password, $database);

// 3. Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    // Jika gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Jika koneksi berhasil, tidak akan ada pesan apa-apa dan skrip akan lanjut.
?>