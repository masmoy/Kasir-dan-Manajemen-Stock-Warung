<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengeluaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Tambah Pengeluaran Baru</h1>
        <form action="proses_tambah.php" method="POST">
            <div class="mb-3">
                <label for="jenis_pengeluaran" class="form-label">Jenis Pengeluaran</label>
                <select name="jenis_pengeluaran" id="jenis_pengeluaran" class="form-select" required>
                    <option value="belanja">Belanja Stok / Operasional</option>
                    <option value="ambil_keuntungan">Ambil Keuntungan (Prive)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi (Cth: Beli Gula 5kg)</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>