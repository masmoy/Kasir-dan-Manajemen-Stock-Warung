<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}
require_once '../../config/database.php';

// Ambil nama toko dari pengaturan
$query_toko = "SELECT nama_toko FROM pengaturan LIMIT 1";
$result_toko = mysqli_query($koneksi, $query_toko);
$data_toko = mysqli_fetch_assoc($result_toko);
$nama_toko = $data_toko['nama_toko'] ?? 'Warung Anda';

// --- LOGIKA FILTER TANGGAL ---
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'semua';
$judul_laporan = 'Semua Waktu';

$where_clause_transaksi = "WHERE 1=1";
$where_clause_pengeluaran = "WHERE 1=1";

if ($periode == 'harian') {
    $where_clause_transaksi .= " AND DATE(tanggal_transaksi) = CURDATE()";
    $where_clause_pengeluaran .= " AND DATE(tanggal_pengeluaran) = CURDATE()";
    $judul_laporan = 'Hari Ini (' . date('d F Y') . ')';
} elseif ($periode == 'bulanan') {
    $where_clause_transaksi .= " AND MONTH(tanggal_transaksi) = MONTH(CURDATE()) AND YEAR(tanggal_transaksi) = YEAR(CURDATE())";
    $where_clause_pengeluaran .= " AND MONTH(tanggal_pengeluaran) = MONTH(CURDATE()) AND YEAR(tanggal_pengeluaran) = YEAR(CURDATE())";
    $judul_laporan = 'Bulan Ini (' . date('F Y') . ')';
}

// --- PENGAMBILAN DATA ---
// Omset
$query_omset = "SELECT SUM(total_omset) as total_omset FROM transaksi $where_clause_transaksi";
$result_omset = mysqli_query($koneksi, $query_omset);
$data_omset = mysqli_fetch_assoc($result_omset);
$omset = $data_omset['total_omset'] ?? 0;

// Laba Bersih
$query_laba = "SELECT SUM(total_laba) as total_laba FROM transaksi $where_clause_transaksi";
$result_laba = mysqli_query($koneksi, $query_laba);
$data_laba = mysqli_fetch_assoc($result_laba);
$laba_bersih = $data_laba['total_laba'] ?? 0;

// Pengeluaran
$query_pengeluaran = "SELECT SUM(jumlah) as total_pengeluaran FROM pengeluaran $where_clause_pengeluaran";
$result_pengeluaran = mysqli_query($koneksi, $query_pengeluaran);
$data_pengeluaran = mysqli_fetch_assoc($result_pengeluaran);
$total_pengeluaran = $data_pengeluaran['total_pengeluaran'] ?? 0;

// Total Hutang (Piutang)
$query_hutang = "SELECT SUM(total_omset) as total_hutang FROM transaksi WHERE status_hutang = 'belum-lunas'";
$result_hutang = mysqli_query($koneksi, $query_hutang);
$data_hutang = mysqli_fetch_assoc($result_hutang);
$total_hutang = $data_hutang['total_hutang'] ?? 0;

// --- QUERY BARU UNTUK TUNAI & NON-TUNAI ---
// Total Tunai
$query_tunai = "SELECT SUM(total_omset) as total_tunai FROM transaksi $where_clause_transaksi AND metode_pembayaran = 'tunai'";
$result_tunai = mysqli_query($koneksi, $query_tunai);
$data_tunai = mysqli_fetch_assoc($result_tunai);
$total_tunai = $data_tunai['total_tunai'] ?? 0;

// Total Non-Tunai
$query_nontunai = "SELECT SUM(total_omset) as total_nontunai FROM transaksi $where_clause_transaksi AND metode_pembayaran = 'non-tunai'";
$result_nontunai = mysqli_query($koneksi, $query_nontunai);
$data_nontunai = mysqli_fetch_assoc($result_nontunai);
$total_nontunai = $data_nontunai['total_nontunai'] ?? 0;

// Saldo Total (Kas di Tangan)
$query_uang_masuk = "SELECT SUM(total_omset) as total_masuk FROM transaksi $where_clause_transaksi AND (metode_pembayaran IN ('tunai', 'non-tunai') OR status_hutang = 'lunas')";
$result_uang_masuk = mysqli_query($koneksi, $query_uang_masuk);
$data_uang_masuk = mysqli_fetch_assoc($result_uang_masuk);
$uang_masuk = $data_uang_masuk['total_masuk'] ?? 0;
$saldo_total = $uang_masuk - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - <?php echo htmlspecialchars($nama_toko); ?></title>
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
            <a class="btn btn-primary" href="../../index.php">
                <i class="bi bi-arrow-left-circle-fill me-1"></i>
                Dashboard
            </a>
            <span class="navbar-text fw-bold">
                Laporan Keuangan
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="" method="GET" class="row gx-2 gy-3 align-items-center">
                    <div class="col-12 col-sm-auto">
                        <label for="periode" class="visually-hidden">Periode</label>
                        <select name="periode" id="periode" class="form-select">
                            <option value="semua" <?php if ($periode == 'semua') echo 'selected'; ?>>Semua Waktu</option>
                            <option value="harian" <?php if ($periode == 'harian') echo 'selected'; ?>>Hari Ini</option>
                            <option value="bulanan" <?php if ($periode == 'bulanan') echo 'selected'; ?>>Bulan Ini</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>
        
        <h1 class="h4">Ringkasan untuk: <span class="text-primary"><?php echo $judul_laporan; ?></span></h1>
        
        <div class="row mt-3">
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h6 class="card-title">OMSET PENJUALAN</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($omset, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h6 class="card-title">LABA BERSIH</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($laba_bersih, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-danger shadow">
                    <div class="card-body">
                        <h6 class="card-title">TOTAL PENGELUARAN</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card text-dark bg-light shadow">
                    <div class="card-body">
                        <h6 class="card-title">PEMBAYARAN TUNAI</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($total_tunai, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-dark bg-info shadow">
                    <div class="card-body">
                        <h6 class="card-title">PEMBAYARAN NON-TUNAI</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($total_nontunai, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
             <div class="col-md-4 mb-4">
                <div class="card text-white bg-dark shadow">
                    <div class="card-body">
                        <h6 class="card-title">SALDO KAS PERIODE INI</h6>
                        <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($saldo_total, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning mt-3" role="alert">
            <h4 class="alert-heading"><i class="bi bi-info-circle-fill"></i> Info Piutang</h4>
            <p class="mb-0">Total piutang (hutang pelanggan yang belum lunas) dari semua waktu adalah: <strong>Rp <?php echo number_format($total_hutang, 0, ',', '.'); ?></strong></p>
        </div>
    </main>
    
    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>
</body>
</html>