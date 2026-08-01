<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    if (loginUser($conn, $username, $password)) {
        header("Location: " . BASE_URL . "/dashboard.php");
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-split">
        <!-- Left Panel - Branding -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="login-brand-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <h1>BMS</h1>
                <p class="login-tagline">Bengkel Management System</p>
                <div class="login-features">
                    <div class="login-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Kelola data pelanggan & kendaraan</span>
                    </div>
                    <div class="login-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Transaksi servis & sparepart</span>
                    </div>
                    <div class="login-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Laporan & analitik real-time</span>
                    </div>
                </div>
            </div>
            <div class="login-left-footer">
                &copy; <?php echo date('Y'); ?> Bengkel Management System
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="login-right">
            <div class="login-form-wrapper">
                <div class="login-form-header">
                    <h2>Selamat datang 👋</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <?php if ($error): ?>
                    <div class="login-error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="login-form">
                    <div class="login-field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                    <div class="login-field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="login-btn">
                        Masuk
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
