<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
require_once '../../config/database.php';

$query = "SELECT * FROM barang ORDER BY id_barang DESC";
$result = mysqli_query($koneksi, $query);

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
    <title>Manajemen Barang - <?php echo htmlspecialchars($nama_toko); ?></title>
    
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
                Manajemen Barang
            </span>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if (isset($_GET['error']) && $_GET['error'] == 'terpakai'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Gagal Menghapus!</strong> Barang ini tidak dapat dihapus karena sudah pernah tercatat dalam transaksi.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Daftar Barang</h1>
            <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Barang</a>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Ketik untuk mencari nama barang...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                             <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barangTable">
                             <?php
                            $nomor = 1;
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $stok = $row['stok'];
                                    $stok_badge_class = $stok <= 10 ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis';
                                    
                                    echo "<tr>";
                                    echo "<td>" . $nomor++ . "</td>";
                                    echo "<td>
                                            <div class='fw-bold'>" . htmlspecialchars($row['nama_barang']) . "</div>
                                            <small class='text-muted'>Kode: " . htmlspecialchars($row['kode_barang']) . "</small>
                                          </td>";
                                    echo "<td>Rp " . number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                                    <?php
                                    echo "<td><span class='badge rounded-pill " . $stok_badge_class . "'>" . $stok . "</span></td>";
                                    echo "<td class='text-end'>
                                            <a href='edit.php?id=" . $row['id_barang'] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil-fill'></i></a>
                                            <a href='hapus.php?id=" . $row['id_barang'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus barang ini?\")'><i class='bi bi-trash-fill'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'>Belum ada data barang.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ... (Script notifikasi dan pencarian)
    </script>
</body>
</html>