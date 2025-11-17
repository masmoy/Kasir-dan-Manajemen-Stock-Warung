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

// Ambil semua data pengeluaran
$query = "SELECT * FROM pengeluaran ORDER BY tanggal_pengeluaran DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total pengeluaran bulan ini
$query_total = "SELECT SUM(jumlah) as total_bulan_ini FROM pengeluaran WHERE MONTH(tanggal_pengeluaran) = MONTH(CURDATE()) AND YEAR(tanggal_pengeluaran) = YEAR(CURDATE())";
$result_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_bulan_ini = $data_total['total_bulan_ini'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengeluaran - <?php echo htmlspecialchars($nama_toko); ?></title>
    
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
                Riwayat Pengeluaran
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="card card-metric shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Total Pengeluaran Bulan Ini</h6>
                <p class="card-title h3 fw-bold">Rp <?php echo number_format($total_bulan_ini, 0, ',', '.'); ?></p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Semua Pengeluaran</h1>
            <a href="tambah.php" class="btn btn-danger"><i class="bi bi-dash-circle-fill me-2"></i>Tambah Pengeluaran</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Ketik untuk mencari deskripsi...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Detail Pengeluaran</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="pengeluaranTable">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>
                                            <?php
                                                $jenis = $row['jenis_pengeluaran'];
                                                $badge_class = ($jenis == 'belanja') ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis';
                                            ?>
                                            <span class="badge rounded-pill <?php echo $badge_class; ?> mb-1">
                                                <?php echo str_replace('_', ' ', ucfirst($jenis)); ?>
                                            </span>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
                                            <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($row['tanggal_pengeluaran'])); ?></small>
                                        </td>
                                        <td class="text-end fw-bold">Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted py-4">Belum ada data pengeluaran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>
    
    <script>
        // Script notifikasi
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'sukses') {
            alert('Pengeluaran berhasil ditambahkan.');
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({path: newUrl}, '', newUrl);
        }

        // Script untuk pencarian/filter tabel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let tableBody = document.getElementById('pengeluaranTable');
            let rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let cell = rows[i].getElementsByTagName('td')[0]; // Kolom pertama (Detail)
                if (cell) {
                    let textValue = cell.textContent || cell.innerText;
                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    </script>
</body>
</html>