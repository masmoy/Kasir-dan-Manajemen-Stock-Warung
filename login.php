<?php
session_start();
// Jika sudah ada sesi login, alihkan ke dashboard utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config/database.php';
// Ambil nama toko dari pengaturan
$query_toko = "SELECT nama_toko FROM pengaturan LIMIT 1";
$result_toko = mysqli_query($koneksi, $query_toko);
$data_toko = mysqli_fetch_assoc($result_toko);
$nama_toko = $data_toko['nama_toko'] ?? 'Aplikasi Kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars($nama_toko); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            animation: fadeInUp 0.8s ease-in-out;
        }
        .card {
            border-radius: 20px;
            overflow: hidden;
        }
        .card-body {
            position: relative;
            z-index: 2;
        }
        h3 {
            font-weight: bold;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
        }
        .btn-primary {
            border-radius: 12px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(90deg, #007bff, #00c6ff);
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(90deg, #0056b3, #0096c7);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #fff;
            font-size: 0.9rem;
        }
        .footer a {
            color: #ffc107;
            text-decoration: none;
            font-weight: bold;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4 p-sm-5">
                <div class="text-center mb-4">
                    <i class="bi bi-shop-window text-primary" style="font-size:3rem;"></i>
                    <h3 class="mt-2"><?php echo htmlspecialchars($nama_toko); ?></h3>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="bi bi-exclamation-circle"></i> Username atau password salah!
                    </div>
                <?php endif; ?>

                <form action="proses_login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label"><i class="bi bi-person-circle"></i> Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right"></i> LOGIN</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Aplikasi POS Gratis - Dibuat oleh 
            <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>
        </div>
    </div>
</body>
</html>
