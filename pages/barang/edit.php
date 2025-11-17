<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
// Sertakan file koneksi
require_once '../../config/database.php';

// Ambil nama toko dari pengaturan
$query_toko = "SELECT nama_toko FROM pengaturan LIMIT 1";
$result_toko = mysqli_query($koneksi, $query_toko);
$data_toko = mysqli_fetch_assoc($result_toko);
$nama_toko = $data_toko['nama_toko'] ?? 'Warung Anda';

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id_barang = $_GET['id'];

    // Query untuk mengambil data barang berdasarkan ID
    $query = "SELECT * FROM barang WHERE id_barang = '$id_barang'";
    $result = mysqli_query($koneksi, $query);
    
    // Cek apakah data ditemukan
    if (mysqli_num_rows($result) == 1) {
        $barang = mysqli_fetch_assoc($result);
    } else {
        // Jika data tidak ditemukan, tampilkan pesan
        die("Data barang tidak ditemukan.");
    }
} else {
    // Jika tidak ada ID, redirect ke halaman utama
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - <?php echo htmlspecialchars($nama_toko); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="btn btn-secondary" href="index.php">
                <i class="bi bi-arrow-left-circle-fill me-1"></i>
                Batal
            </a>
            <span class="navbar-text fw-bold">
                Edit Barang
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p class="text-muted">Anda sedang mengubah data untuk: <strong><?php echo htmlspecialchars($barang['nama_barang']); ?></strong></p>
                <form action="proses_edit.php" method="POST">
                    <input type="hidden" name="id_barang" value="<?php echo htmlspecialchars($barang['id_barang']); ?>">

                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang / Barcode</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                            <input type="text" inputmode="decimal" class="form-control" id="harga_beli" name="harga_beli" value="<?php echo htmlspecialchars($barang['harga_beli']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                            <input type="text" inputmode="decimal" class="form-control" id="harga_jual" name="harga_jual" value="<?php echo htmlspecialchars($barang['harga_jual']); ?>" required>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" value="<?php echo htmlspecialchars($barang['stok']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan" value="<?php echo htmlspecialchars($barang['satuan']); ?>" placeholder="Contoh: Pcs, Kg, Pack">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>

</body>
</html>