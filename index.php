<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

// --- AMBIL DATA UNTUK METRIK HARI INI ---
$query_omset = "SELECT SUM(total_omset) as omset_hari_ini FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
$result_omset = mysqli_query($koneksi, $query_omset);
$data_omset = mysqli_fetch_assoc($result_omset);
$omset_hari_ini = $data_omset['omset_hari_ini'] ?? 0;

$query_laba = "SELECT SUM(total_laba) as laba_hari_ini FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
$result_laba = mysqli_query($koneksi, $query_laba);
$data_laba = mysqli_fetch_assoc($result_laba);
$laba_hari_ini = $data_laba['laba_hari_ini'] ?? 0;

$query_transaksi = "SELECT COUNT(id_transaksi) as jumlah_transaksi FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
$data_transaksi = mysqli_fetch_assoc($result_transaksi);
$jumlah_transaksi_hari_ini = $data_transaksi['jumlah_transaksi'] ?? 0;

// --- AMBIL DATA BARANG YANG STOKNYA MENIPIS ---
$stok_limit = 10;
$query_stok = "SELECT * FROM barang WHERE stok <= $stok_limit ORDER BY stok ASC";
$result_stok = mysqli_query($koneksi, $query_stok);

// Ambil nama toko dari pengaturan
$query_toko = "SELECT nama_toko FROM pengaturan LIMIT 1";
$result_toko = mysqli_query($koneksi, $query_toko);
$data_toko = mysqli_fetch_assoc($result_toko);
$nama_toko = $data_toko['nama_toko'] ?? 'Warung Anda';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($nama_toko); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shop-window me-2"></i>
                <strong><?php echo htmlspecialchars($nama_toko); ?></strong>
            </a>
            <!-- TOMBOL LOGOUT DITAMBAHKAN DI SINI -->
            <a href="logout.php" class="btn btn-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </nav>

    <main class="container mt-4">
        <!-- ... (sisa kode main Anda tidak berubah) ... -->
        <h1 class="h4">Ringkasan Hari Ini</h1>
        <p class="text-muted"><small>Data per tanggal <?php echo date('d F Y'); ?></small></p>

        <div class="row">
            <div class="col-md-4 mb-3"><div class="card card-metric shadow-sm border-0"><div class="card-body"><h6 class="card-subtitle text-muted">Omset</h6><p class="card-title h3 fw-bold">Rp <?php echo number_format($omset_hari_ini, 0, ',', '.'); ?></p></div></div></div>
            <div class="col-md-4 mb-3"><div class="card card-metric shadow-sm border-0"><div class="card-body"><h6 class="card-subtitle text-muted">Laba</h6><p class="card-title h3 fw-bold">Rp <?php echo number_format($laba_hari_ini, 0, ',', '.'); ?></p></div></div></div>
            <div class="col-md-4 mb-3"><div class="card card-metric shadow-sm border-0"><div class="card-body"><h6 class="card-subtitle text-muted">Transaksi</h6><p class="card-title h3 fw-bold"><?php echo $jumlah_transaksi_hari_ini; ?></p></div></div></div>
        </div>

        <hr class="my-4">

        <div class="row text-center g-3">
            <div class="col-4"><a href="pages/transaksi/" class="menu-item bg-success-subtle text-success"><i class="bi bi-cart-plus-fill"></i><span>Kasir</span></a></div>
            <div class="col-4"><a href="pages/barang/" class="menu-item bg-primary-subtle text-primary"><i class="bi bi-box-seam-fill"></i><span>Barang</span></a></div>
            <div class="col-4"><a href="pages/laporan/" class="menu-item bg-secondary-subtle text-secondary"><i class="bi bi-file-earmark-bar-graph-fill"></i><span>Laporan</span></a></div>
            <div class="col-4"><a href="pages/transaksi/daftar_transaksi.php" class="menu-item bg-info-subtle text-info"><i class="bi bi-list-ul"></i><span>Transaksi</span></a></div>
            <div class="col-4"><a href="pages/pengeluaran/" class="menu-item bg-warning-subtle text-warning"><i class="bi bi-wallet-fill"></i><span>Pengeluaran</span></a></div>
            <div class="col-4"><a href="pages/hutang/" class="menu-item bg-danger-subtle text-danger"><i class="bi bi-person-lines-fill"></i><span>Hutang</span></a></div>
        </div>
        
        <hr class="my-4">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-0">
                <h2 class="h5 mb-0"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Stok Menipis</h2>
            </div>
            <div class="card-body pt-0">
                 <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            <?php if (mysqli_num_rows($result_stok) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result_stok)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td class="text-end"><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill"><?php echo $row['stok']; ?> tersisa</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td class="text-center text-muted py-3">Stok aman!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="pages/pengaturan/" class="btn btn-sm btn-outline-secondary"><i class="bi bi-gear-fill"></i> Pengaturan Toko</a>
        </div>
    </main>
    
    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>