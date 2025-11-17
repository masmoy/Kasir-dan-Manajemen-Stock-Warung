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

// --- LOGIKA PAGINATION ---
$batas_per_halaman = 10;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$posisi_awal = ($halaman_aktif - 1) * $batas_per_halaman;

// --- MENGAMBIL DATA TRANSAKSI ---
$query_transaksi = "SELECT * FROM transaksi ORDER BY tanggal_transaksi DESC LIMIT $posisi_awal, $batas_per_halaman";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);

// --- MENGHITUNG TOTAL DATA ---
$query_total = "SELECT COUNT(*) as total FROM transaksi";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total'];
$jumlah_halaman = ceil($total_data / $batas_per_halaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - <?php echo htmlspecialchars($nama_toko); ?></title>
    
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
                Riwayat Transaksi
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Semua Transaksi</h1>
            <a href="index.php" class="btn btn-success"><i class="bi bi-cart-plus-fill me-2"></i>Buat Transaksi Baru</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari ID transaksi atau nama pelanggan...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Detail Transaksi</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transaksiTable">
                            <?php if (mysqli_num_rows($result_transaksi) > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($result_transaksi)) : ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['id_transaksi']); ?></div>
                                            <small class="text-muted">
                                                <?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?>
                                                <?php if(!empty($row['nama_pelanggan'])): ?>
                                                    | <?php echo htmlspecialchars($row['nama_pelanggan']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td class="fw-bold">Rp <?php echo number_format($row['total_omset'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php
                                                $metode = $row['metode_pembayaran'];
                                                $status = $row['status_hutang'];
                                                $badge_class = 'bg-secondary';
                                                if ($metode == 'tunai') $badge_class = 'bg-success-subtle text-success-emphasis';
                                                if ($metode == 'non-tunai') $badge_class = 'bg-info-subtle text-info-emphasis';
                                                if ($metode == 'hutang') {
                                                    $badge_class = ($status == 'lunas') ? 'bg-primary-subtle text-primary-emphasis' : 'bg-warning-subtle text-warning-emphasis';
                                                    $metode = "Hutang (" . ucfirst($status) . ")";
                                                }
                                            ?>
                                            <span class="badge rounded-pill <?php echo $badge_class; ?>"><?php echo ucfirst($metode); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="struk_ulang.php?id=<?php echo $row['id_transaksi']; ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="bi bi-printer-fill"></i></a>
                                            <a href="hapus_transaksi.php?id=<?php echo $row['id_transaksi']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat transaksi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($jumlah_halaman > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $jumlah_halaman; $i++): ?>
                            <li class="page-item <?php if($i == $halaman_aktif) echo 'active'; ?>">
                                <a class="page-link" href="?halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>
    
    <script>
        // Script notifikasi (dari kode Anda sebelumnya)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'hapus_sukses') {
            alert('Transaksi berhasil dihapus dan stok telah dikembalikan.');
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({path: newUrl}, '', newUrl);
        }

        // Script untuk pencarian/filter tabel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let tableBody = document.getElementById('transaksiTable');
            let rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let cell = rows[i].getElementsByTagName('td')[0]; // Kolom pertama (Detail Transaksi)
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