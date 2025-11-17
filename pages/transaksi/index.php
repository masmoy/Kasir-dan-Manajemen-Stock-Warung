<?php
session_start();
// Cek jika pengguna belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); // Path ini untuk file di dalam folder /pages/
    exit();
}

require_once '../../config/database.php';

// Ambil nama toko
$query_toko = "SELECT nama_toko FROM pengaturan LIMIT 1";
$result_toko = mysqli_query($koneksi, $query_toko);
$data_toko = mysqli_fetch_assoc($result_toko);
$nama_toko = $data_toko['nama_toko'] ?? 'Warung Anda';

// Ambil semua data barang
$query_barang = "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC";
$result_barang = mysqli_query($koneksi, $query_barang);

// Hitung total belanja awal
$grand_total = 0;
if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id => $item) {
        $grand_total += $item['harga_jual'] * $item['jumlah'];
    }
}

// PERUBAHAN: Ambil kata kunci pencarian dari URL jika ada
$search_query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kasir - <?php echo htmlspecialchars($nama_toko); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        <?php if (empty($search_query)): ?>
        #barangTable tr {
            display: none;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="btn btn-primary" href="../../index.php">
                <i class="bi bi-arrow-left-circle-fill me-1"></i>
                Dashboard
            </a>
            <span class="navbar-text fw-bold">
                Halaman Kasir
            </span>
        </div>
    </nav>

    <main class="container-fluid mt-4">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h4 class="card-title">Pilih Barang</h4>
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Ketik nama barang untuk menampilkan..." value="<?php echo $search_query; ?>" autofocus>
                        <div class="table-responsive" style="max-height: 60vh;">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Stok</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="barangTable">
                                    <?php while ($barang = mysqli_fetch_assoc($result_barang)) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                            <td class="text-end">Rp <?php echo number_format($barang['harga_jual'], 0, ',', '.'); ?></td>
                                            <td class="text-end"><?php echo $barang['stok']; ?></td>
                                            <td class="text-center">
                                                <a href="keranjang_aksi.php?aksi=tambah&id=<?php echo $barang['id_barang']; ?>" class="btn btn-success btn-sm link-tambah"><i class="bi bi-plus-lg"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Keranjang</h4>
                            <a href="keranjang_aksi.php?aksi=kosongkan" class="btn btn-danger btn-sm"><i class="bi bi-x-circle me-1"></i> Kosongkan</a>
                        </div>
                        
                        <form id="form-keranjang" action="keranjang_aksi.php?aksi=update" method="POST" class="flex-grow-1">
                            <div class="table-responsive" style="max-height: 35vh;">
                                <table class="table">
                                    <tbody>
                                        <?php if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) : ?>
                                            <?php foreach ($_SESSION['keranjang'] as $id_barang => $item) : ?>
                                                <tr class="item-keranjang">
                                                    <td><?php echo htmlspecialchars($item['nama_barang']); ?><br><small class="text-muted">Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></small></td>
                                                    <td style="width: 90px;">
                                                        <input type="number" name="jumlah[<?php echo $id_barang; ?>]" class="form-control form-control-sm qty-input" value="<?php echo $item['jumlah']; ?>" min="1" data-harga="<?php echo $item['harga_jual']; ?>">
                                                    </td>
                                                    <td class="text-end fw-bold">Rp <span class="subtotal"><?php echo number_format($item['harga_jual'] * $item['jumlah'], 0, ',', '.'); ?></span></td>
                                                    <td class="text-center"><a href="keranjang_aksi.php?aksi=hapus&id=<?php echo $id_barang; ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash-fill"></i></a></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr><td class="text-center text-muted py-5">Keranjang masih kosong</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (!empty($_SESSION['keranjang'])): ?>
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 mt-2">Update Keranjang</button>
                            <?php endif; ?>
                        </form>
                        
                        <div class="mt-auto pt-3">
                             <div class="d-flex justify-content-between h4">
                                <span>Total:</span>
                                <span class="fw-bold">Rp <span id="grand-total"><?php echo number_format($grand_total, 0, ',', '.'); ?></span></span>
                            </div>
                            <hr>
                            <form action="proses_checkout.php" method="POST">
                                <div class="mb-2"><select class="form-select" name="metode_pembayaran" required><option value="tunai">Tunai</option><option value="non-tunai">Non-Tunai</option><option value="hutang">Hutang</option></select></div>
                                <div class="mb-3"><input type="text" class="form-control" name="nama_pelanggan" placeholder="Nama Pelanggan (jika hutang)"></div>
                                <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg" <?php echo empty($_SESSION['keranjang']) ? 'disabled' : ''; ?>>PROSES PEMBAYARAN</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
        <p><small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nama_toko); ?></small></p>Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
    </footer>
    
    <script>
        // ... (kode untuk formatRupiah dan hitungUlangTotal tetap sama) ...
        function formatRupiah(angka) { /* ... */ }
        function hitungUlangTotal() { /* ... */ }
        document.querySelectorAll('.qty-input').forEach(function(input) { input.addEventListener('input', hitungUlangTotal); });

        // Fungsi untuk menjalankan filter pencarian
        function filterBarang() {
            let filter = document.getElementById('searchInput').value.toUpperCase();
            let tableBody = document.getElementById('barangTable');
            let rows = tableBody.getElementsByTagName('tr');

            if (filter.length > 0) {
                for (let i = 0; i < rows.length; i++) {
                    let cell = rows[i].getElementsByTagName('td')[0];
                    if (cell) {
                        let textValue = cell.textContent || cell.innerText;
                        if (textValue.toUpperCase().indexOf(filter) > -1) {
                            rows[i].style.display = "table-row";
                        } else {
                            rows[i].style.display = "none";
                        }
                    }
                }
            } else {
                for (let i = 0; i < rows.length; i++) {
                    rows[i].style.display = "none";
                }
            }
        }

        // Jalankan filter saat mengetik
        document.getElementById('searchInput').addEventListener('keyup', filterBarang);

        // PERUBAHAN UTAMA: Saat link "Tambah" diklik, tambahkan kata kunci pencarian ke URL
        document.querySelectorAll('.link-tambah').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Hentikan link agar tidak langsung pindah halaman
                let currentSearch = document.getElementById('searchInput').value;
                let originalUrl = this.href;
                // Arahkan ke URL baru yang sudah berisi kata kunci pencarian
                window.location.href = originalUrl + '&search=' + encodeURIComponent(currentSearch);
            });
        });

        // PERUBAHAN UTAMA: Jalankan filter saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', function() {
            filterBarang();
        });
    </script>

</body>
</html>