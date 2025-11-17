<?php
// Ini adalah file struk_ulang.php
session_start();
require_once '../../config/database.php';

// Ambil ID dari URL, bukan dari session
if (!isset($_GET['id'])) {
    die("ID Transaksi tidak ditemukan.");
}

$id_transaksi = $_GET['id'];
// ... sisa kodenya sama persis dengan struk.php ...
// (Anda bisa copy-paste seluruh isi file struk.php ke sini,
// lalu pastikan baris di atas menggantikan pengecekan session)

// 1. Ambil data pengaturan toko
$query_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($query_pengaturan);

// 2. Ambil data transaksi
$query_transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'");
$transaksi = mysqli_fetch_assoc($query_transaksi);

// 3. Ambil data detail transaksi
$query_detail = mysqli_query($koneksi, "SELECT dt.*, b.nama_barang 
                                        FROM detail_transaksi dt 
                                        JOIN barang b ON dt.id_barang = b.id_barang 
                                        WHERE dt.id_transaksi = '$id_transaksi'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi - <?php echo htmlspecialchars($id_transaksi); ?></title>
    <style>
        body { font-family: 'Courier New', monospace; width: 58mm; font-size: 10pt; margin: 0; padding: 5px; }
        .header, .footer { text-align: center; }
        .content table { width: 100%; border-collapse: collapse; }
        .content th, .content td { padding: 2px 0; }
        .separator { border-top: 1px dashed black; margin: 5px 0; }
        .total-section { text-align: right; }
        .no-print { text-align: center; margin-top: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header"><strong><?php echo htmlspecialchars($pengaturan['nama_toko']); ?></strong><br><?php echo htmlspecialchars($pengaturan['alamat_toko']); ?><br></div>
        <div class="separator"></div>
        <div>No: <?php echo htmlspecialchars($transaksi['id_transaksi']); ?><br>Tgl: <?php echo date('d/m/Y H:i', strtotime($transaksi['tanggal_transaksi'])); ?><br></div>
        <div class="separator"></div>
        <div class="content">
            <table>
                <?php while($item = mysqli_fetch_assoc($query_detail)): ?>
                <tr><td colspan="3"><?php echo htmlspecialchars($item['nama_barang']); ?></td></tr>
                <tr><td><?php echo $item['jumlah']; ?> x</td><td style="text-align: right;"><?php echo number_format($item['harga_jual_saat_transaksi'], 0, ',', '.'); ?></td><td style="text-align: right;"><?php echo number_format($item['subtotal_omset'], 0, ',', '.'); ?></td></tr>
                <?php endwhile; ?>
            </table>
        </div>
        <div class="separator"></div>
        <div class="total-section"><strong>Total: <?php echo number_format($transaksi['total_omset'], 0, ',', '.'); ?></strong><br></div>
        <div class="separator"></div>
        <div class="footer"><?php echo htmlspecialchars($pengaturan['ucapan_struk']); ?><br></div>
    </div>
    <div class="no-print"><button onclick="window.print()">Cetak Ulang</button><br><a href="daftar_transaksi.php">Kembali ke Daftar Transaksi</a></div>
</body>
</html>