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

// --- LOGIKA FILTER ---
$bulan_filter = $_GET['bulan'] ?? '';
$tahun_filter = $_GET['tahun'] ?? '';
$nama_filter = $_GET['nama'] ?? '';

$where_clause = "WHERE status_hutang = 'belum-lunas'";
$judul_filter = "Semua Waktu";
$filter_params = []; 

if (!empty($bulan_filter) && !empty($tahun_filter)) {
    $where_clause .= " AND MONTH(tanggal_transaksi) = '$bulan_filter' AND YEAR(tanggal_transaksi) = '$tahun_filter'";
    $nama_bulan = date('F', mktime(0, 0, 0, $bulan_filter, 10));
    $judul_filter = "$nama_bulan $tahun_filter";
    $filter_params['bulan'] = $bulan_filter;
    $filter_params['tahun'] = $tahun_filter;
}

if (!empty($nama_filter)) {
    $nama_filter_sanitized = mysqli_real_escape_string($koneksi, $nama_filter);
    $where_clause .= " AND nama_pelanggan LIKE '%$nama_filter_sanitized%'";
    $judul_filter = "Pencarian: '$nama_filter'";
    $filter_params['nama'] = $nama_filter;
}

$query = "SELECT * FROM transaksi $where_clause ORDER BY tanggal_transaksi ASC";
$result = mysqli_query($koneksi, $query);

$query_total = "SELECT SUM(total_omset) as total_hutang FROM transaksi $where_clause";
$result_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_hutang = $data_total['total_hutang'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Hutang - <?php echo htmlspecialchars($nama_toko); ?></title>
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
                Manajemen Hutang
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="nama" class="form-label">Cari Nama Pelanggan</label>
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Ketik nama..." value="<?php echo htmlspecialchars($nama_filter); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="bulan" class="form-label">Filter Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            <option value="">-- Semua Bulan --</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo sprintf('%02d', $i); ?>" <?php if ($i == $bulan_filter) echo 'selected'; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Filter Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            <option value="">-- Semua Tahun --</option>
                             <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == $tahun_filter) echo 'selected'; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card bg-warning-subtle text-warning-emphasis border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Total Piutang (Filter: <?php echo $judul_filter; ?>)</h5>
                <p class="card-text fs-4 fw-bold">Rp <?php echo number_format($total_hutang, 0, ',', '.'); ?></p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                 <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pelanggan</th>
                                <th>Detail Transaksi</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                        <td>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($row['id_transaksi']); ?></small>
                                            <small class="text-muted d-block"><?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></small>
                                        </td>
                                        <td class="text-end fw-bold">Rp <?php echo number_format($row['total_omset'], 0, ',', '.'); ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#lunasiModal" data-id="<?php echo $row['id_transaksi']; ?>" data-nama="<?php echo htmlspecialchars($row['nama_pelanggan']); ?>">
                                                <i class="bi bi-check-circle-fill"></i> Lunas
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data hutang yang cocok.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="lunasiModal" tabindex="-1" aria-labelledby="lunasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="lunasiModalLabel">Konfirmasi Pelunasan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="lunasi.php" method="POST">
                    <div class="modal-body">
                        <p>Anda akan menandai lunas untuk hutang atas nama <strong id="namaPelanggan"></strong>.</p>
                        <input type="hidden" name="id_transaksi" id="idTransaksi">
                        <div class="mb-3">
                            <label for="metode_pelunasan" class="form-label">Pilih Metode Pelunasan:</label>
                            <select name="metode_pelunasan" id="metode_pelunasan" class="form-select" required>
                                <option value="tunai">Tunai</option>
                                <option value="non-tunai">Non-Tunai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Konfirmasi Lunas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'lunas_sukses') {
            alert('Hutang berhasil ditandai lunas!');
        }
        
        const lunasiModal = document.getElementById('lunasiModal');
        if(lunasiModal) {
            lunasiModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');

                const modalBodyNama = lunasiModal.querySelector('#namaPelanggan');
                const modalInputId = lunasiModal.querySelector('#idTransaksi');
                
                modalBodyNama.textContent = nama;
                modalInputId.value = id;
            });
        }
    </script>
</body>
</html>