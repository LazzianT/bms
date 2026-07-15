<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMS - Bengkel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/bms/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header p-3">
            <div class="d-flex align-items-center gap-2">
                <div class="login-logo" style="width:40px;height:40px;border-radius:10px;font-size:1rem;margin-bottom:0;">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h5 class="mb-0">BMS</h5>
                    <small style="color:rgba(255,255,255,0.4);font-size:0.65rem;letter-spacing:1px;">BENGKEL MANAGEMENT</small>
                </div>
            </div>
        </div>
        <hr class="my-0">
        <ul class="nav flex-column p-2">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="/bms/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <?php if (in_array($_SESSION['role'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'master') ? 'active' : ''; ?>" href="#masterSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-database"></i> Master Data <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'master') ? 'show' : ''; ?>" id="masterSubmenu">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'customer.php') ? 'active' : ''; ?>" href="/bms/master/customer.php"><i class="bi bi-person"></i> Customer</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'kendaraan.php') ? 'active' : ''; ?>" href="/bms/master/kendaraan.php"><i class="bi bi-bicycle"></i> Kendaraan</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'sparepart.php') ? 'active' : ''; ?>" href="/bms/master/sparepart.php"><i class="bi bi-box"></i> Sparepart</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'jasa.php') ? 'active' : ''; ?>" href="/bms/master/jasa.php"><i class="bi bi-gear"></i> Jasa</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'mekanik.php') ? 'active' : ''; ?>" href="/bms/master/mekanik.php"><i class="bi bi-person-workspace"></i> Mekanik</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'supplier.php') ? 'active' : ''; ?>" href="/bms/master/supplier.php"><i class="bi bi-truck"></i> Supplier</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'transaksi') ? 'active' : ''; ?>" href="#transaksiSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-receipt"></i> Transaksi <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'transaksi') ? 'show' : ''; ?>" id="transaksiSubmenu">
                    <ul class="nav flex-column ms-2">
                        <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'servis_list.php') ? 'active' : ''; ?>" href="/bms/transaksi/servis_list.php"><i class="bi bi-wrench"></i> Service</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'pembelian_list.php') ? 'active' : ''; ?>" href="/bms/transaksi/pembelian_list.php"><i class="bi bi-cart"></i> Pembelian Part</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'po_list.php') ? 'active' : ''; ?>" href="/bms/transaksi/po_list.php"><i class="bi bi-file-earmark-text"></i> Purchase Order</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'mrs_list.php') ? 'active' : ''; ?>" href="/bms/transaksi/mrs_list.php"><i class="bi bi-inbox"></i> Penerimaan (MRS)</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentDir == 'laporan') ? 'active' : ''; ?>" href="#laporanSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-graph-up"></i> Laporan <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse <?php echo ($currentDir == 'laporan') ? 'show' : ''; ?>" id="laporanSubmenu">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'performa_mekanik.php') ? 'active' : ''; ?>" href="/bms/laporan/performa_mekanik.php"><i class="bi bi-person-check"></i> Performa Mekanik</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'penjualan_sparepart.php') ? 'active' : ''; ?>" href="/bms/laporan/penjualan_sparepart.php"><i class="bi bi-box-seam"></i> Penjualan Sparepart</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'servis.php') ? 'active' : ''; ?>" href="/bms/laporan/servis.php"><i class="bi bi-clipboard-check"></i> Servis</a></li>
                        <li class="nav-item"><a class="nav-link py-1 <?php echo ($currentPage == 'omset_pembelian.php') ? 'active' : ''; ?>" href="/bms/laporan/omset_pembelian.php"><i class="bi bi-cash-stack"></i> Omset vs Pembelian</a></li>
                    </ul>
                </div>
            </li>

            <?php if (in_array($_SESSION['role'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'user_management.php') ? 'active' : ''; ?>" href="/bms/master/user_management.php">
                    <i class="bi bi-people"></i> User Management
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="content" class="flex-grow-1">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="navbar-text"><?php echo getRoleName($_SESSION['role']); ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="user-name"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
                <a href="/bms/auth/logout.php" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>
        <div class="container-fluid p-4 flex-grow-1">
