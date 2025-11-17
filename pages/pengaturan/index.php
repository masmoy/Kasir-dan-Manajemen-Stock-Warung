<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/database.php';

// Ambil data pengaturan toko
$query_pengaturan = "SELECT * FROM pengaturan WHERE id_pengaturan = 1";
$result_pengaturan = mysqli_query($koneksi, $query_pengaturan);
$pengaturan = mysqli_fetch_assoc($result_pengaturan);

// Ambil data user admin
$query_user = "SELECT * FROM users WHERE id_user = 1";
$result_user = mysqli_query($koneksi, $query_user);
$user = mysqli_fetch_assoc($result_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko & Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="btn btn-primary" href="../../index.php">
                Dashboard
            </a>
            <span class="navbar-text fw-bold">
                Pengaturan
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert alert-success">Pengaturan berhasil diperbarui!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <div class="alert alert-danger">Error: Password baru tidak cocok.</div>
        <?php endif; ?>

        <form action="proses_update.php" method="POST">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h5>Informasi Toko</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama_toko" class="form-label">Nama Toko</label>
                        <input type="text" class="form-control" id="nama_toko" name="nama_toko" value="<?php echo htmlspecialchars($pengaturan['nama_toko']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat_toko" class="form-label">Alamat Toko</label>
                        <textarea class="form-control" id="alamat_toko" name="alamat_toko" rows="3" required><?php echo htmlspecialchars($pengaturan['alamat_toko']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ucapan_struk" class="form-label">Ucapan di Bawah Struk</label>
                        <textarea class="form-control" id="ucapan_struk" name="ucapan_struk" rows="2" required><?php echo htmlspecialchars($pengaturan['ucapan_struk']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5>Pengaturan Login Admin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_baru" class="form-label">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" class="form-control" id="password_baru" name="password_baru">
                    </div>
                    <div class="mb-3">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                    </div>
                </div>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Simpan Semua Perubahan</button>
            </div>
        </form>
    </main>
</body>
</html>