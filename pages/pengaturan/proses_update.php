<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Proses update informasi toko (DENGAN UCAPAN STRUK)
    $nama_toko = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $alamat_toko = mysqli_real_escape_string($koneksi, $_POST['alamat_toko']);
    $ucapan_struk = mysqli_real_escape_string($koneksi, $_POST['ucapan_struk']); // BARIS TAMBAHAN
    
    $query_toko = "UPDATE pengaturan SET 
                    nama_toko = '$nama_toko', 
                    alamat_toko = '$alamat_toko',
                    ucapan_struk = '$ucapan_struk' /* BARIS TAMBAHAN */
                   WHERE id_pengaturan = 1";
    mysqli_query($koneksi, $query_toko);

    // 2. Proses update data login admin
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Cek apakah password baru diisi
    if (!empty($password_baru)) {
        // Cek apakah password baru dan konfirmasinya cocok
        if ($password_baru === $konfirmasi_password) {
            // Simpan password baru sebagai plaintext (tidak aman untuk produksi)
            $query_user = "UPDATE users SET 
                            username = '$username', 
                            password = '$password_baru' 
                           WHERE id_user = 1";
        } else {
            // Jika tidak cocok, redirect dengan pesan error
            header("Location: index.php?status=error");
            exit();
        }
    } else {
        // Jika password tidak diubah, hanya update username
        $query_user = "UPDATE users SET 
                        username = '$username' 
                       WHERE id_user = 1";
    }

    // Eksekusi query user
    if (mysqli_query($koneksi, $query_user)) {
        header("Location: index.php?status=sukses");
        exit();
    } else {
        echo "Error: Gagal memperbarui pengaturan login. " . mysqli_error($koneksi);
    }
}
?>
