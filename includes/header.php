<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

$userInitial = strtoupper(substr(trim($_SESSION['nama_lengkap'] ?? 'U'), 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMS - Bengkel Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header d-flex align-items-center gap-3">
            <div class="login-logo"><i class="bi bi-tools"></i></div>
            <div>
                <h5>BMS</h5>
                <small>Bengkel Management</small>
            </div>
        </div>

        <div class="sidebar-label">Menu</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/dashboard.php">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>

            <?php if (in_array($_SESSION['role'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'master') ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#masterSubmenu" role="button" aria-expanded="<?php echo ($currentDir == 'master') ? 'true' : 'false'; ?>">
                    <i class="bi bi-database"></i> Master Data <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'master') ? 'show' : ''; ?>" id="masterSubmenu">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'customer.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/customer.php"><i class="bi bi-person"></i> Customer</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'kendaraan.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/kendaraan.php"><i class="bi bi-bicycle"></i> Kendaraan</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'sparepart.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/sparepart.php"><i class="bi bi-box"></i> Sparepart</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'mekanik.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/mekanik.php"><i class="bi bi-person-workspace"></i> Mekanik</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'supplier.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/supplier.php"><i class="bi bi-truck"></i> Supplier</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'transaksi') ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="<?php echo ($currentDir == 'transaksi') ? 'true' : 'false'; ?>">
                    <i class="bi bi-receipt"></i> Transaksi <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'transaksi') ? 'show' : ''; ?>" id="transaksiSubmenu">
                    <ul class="nav flex-column ms-2">
                        <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'servis_list.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/transaksi/servis_list.php"><i class="bi bi-clipboard-plus"></i> Pendaftaran Servis</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'transaksi_list.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/transaksi/transaksi_list.php"><i class="bi bi-wrench"></i> Transaksi Servis</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'pembelian_list.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/transaksi/pembelian_list.php"><i class="bi bi-cart"></i> Pembelian Sparepart</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'penjualan_sp.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/transaksi/penjualan_sp.php"><i class="bi bi-bag"></i> Penjualan Sparepart</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'laporan') ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#laporanSubmenu" role="button" aria-expanded="<?php echo ($currentDir == 'laporan') ? 'true' : 'false'; ?>">
                    <i class="bi bi-graph-up"></i> Laporan <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'laporan') ? 'show' : ''; ?>" id="laporanSubmenu">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'performa_mekanik.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/laporan/performa_mekanik.php"><i class="bi bi-person-check"></i> Performa Mekanik</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'penjualan_sparepart.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/laporan/penjualan_sparepart.php"><i class="bi bi-box-seam"></i> Penjualan Sparepart</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'servis.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/laporan/servis.php"><i class="bi bi-clipboard-check"></i> Transaksi Servis</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'pembelian.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/laporan/pembelian.php"><i class="bi bi-cart-check"></i> Pembelian</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'omset_pembelian.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/laporan/omset_pembelian.php"><i class="bi bi-cash-stack"></i> Omset & Pendapatan</a></li>
                    </ul>
                </div>
            </li>

            <?php if (in_array($_SESSION['role'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'user_management.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/master/user_management.php">
                    <i class="bi bi-shield-lock"></i> User Management
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'team.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/team.php">
                    <i class="bi bi-people-fill"></i> Our Team
                </a>
            </li>
        </ul>

        <div class="sidebar-user d-flex align-items-center gap-3">
            <div class="avatar"><?php echo $userInitial; ?></div>
            <div>
                <div class="username"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? ''); ?></div>
                <div class="role"><?php echo getRoleName($_SESSION['role'] ?? ''); ?></div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="content" class="flex-grow-1">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="navbar-text d-none d-md-inline"><i class="bi bi-chevron-right me-1 opacity-50"></i><?php echo getRoleName($_SESSION['role']); ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="user-name d-none d-sm-inline"><i class="bi bi-person-circle me-1 text-muted"></i> <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        </nav>
        <div class="container-fluid p-4 flex-grow-1">
