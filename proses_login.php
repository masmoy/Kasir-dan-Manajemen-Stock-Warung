<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cari user berdasarkan username (kita asumsikan hanya ada 1 user)
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi plaintext (tidak aman untuk produksi)
        if ($password === $user['password']) {
            // Jika password cocok, buat session
            $_SESSION['user_id'] = $user['id_user'];
            
            // Redirect ke dashboard utama
            header("Location: index.php");
            exit();
        }
    }
    
    // Jika username tidak ditemukan atau password salah, redirect kembali ke login dengan pesan error
    header("Location: login.php?error=1");
    exit();
}
?>
