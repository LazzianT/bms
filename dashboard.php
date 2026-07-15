<?php
require_once __DIR__ . '/includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

// Dashboard stats
$totalCustomer    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM client"))['jml'];
$totalKendaraan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM vehicle"))['jml'];
$transaksiHariIni = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE DATE(tanggal) = CURDATE()"))['jml'];
$omsetHariIni     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(bayar),0) as total FROM transaksi_servis WHERE DATE(tanggal) = CURDATE() AND status_servis = 'Selesai Lunas'"))['total'];
$stokMenipis      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM sparepart WHERE stok <= stok_minimum"))['jml'];
$servisAktif      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE status_servis = 'Dikerjakan'"))['jml'];

// Pendapatan 6 bulan terakhir
$queryBulan = "SELECT DATE_FORMAT(tanggal, '%Y-%m') as bulan, SUM(bayar) as total 
               FROM transaksi_servis 
               WHERE status_servis = 'Selesai Lunas' 
                 AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
               GROUP BY bulan ORDER BY bulan";
$resultBulan = mysqli_query($conn, $queryBulan);
$dataBulan = [];
while ($row = mysqli_fetch_assoc($resultBulan)) {
    $dataBulan[] = $row;
}

// Top 5 sparepart
$queryTop = "SELECT sp.nama_sparepart, SUM(d.qty) as total_qty
             FROM transaksi_servis_detail d
             JOIN sparepart sp ON d.sparepart_id = sp.sparepart_id
             JOIN transaksi_servis t ON d.trans_id = t.trans_id
             WHERE t.status_servis = 'Selesai Lunas'
             GROUP BY sp.nama_sparepart
             ORDER BY total_qty DESC LIMIT 5";
$resultTop = mysqli_query($conn, $queryTop);
$dataTop = [];
while ($row = mysqli_fetch_assoc($resultTop)) {
    $dataTop[] = $row;
}
?>

<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
    <span class="text-muted"><?php echo date('d F Y'); ?></span>
</div>

<!-- Stats Cards -->
<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="stat-card stat-card-blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Customer</h6>
                    <h3><?php echo $totalCustomer; ?></h3>
                </div>
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Kendaraan</h6>
                    <h3><?php echo $totalKendaraan; ?></h3>
                </div>
                <i class="bi bi-bicycle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-teal">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Transaksi Hari Ini</h6>
                    <h3><?php echo $transaksiHariIni; ?></h3>
                </div>
                <i class="bi bi-receipt"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-orange">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Omset Hari Ini</h6>
                    <h3><?php echo formatRupiah($omsetHariIni); ?></h3>
                </div>
                <i class="bi bi-cash"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="info-box box-danger">
            <h6><i class="bi bi-exclamation-triangle me-1"></i> Stok Menipis</h6>
            <h2><?php echo $stokMenipis; ?> item</h2>
            <small class="text-muted">Sparepart di bawah stok minimum</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box box-info">
            <h6><i class="bi bi-wrench me-1"></i> Servis Aktif</h6>
            <h2><?php echo $servisAktif; ?></h2>
            <small class="text-muted">Sedang dikerjakan</small>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3">
    <div class="col-md-8">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">Pendapatan 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="chartPendapatan" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">Top 5 Sparepart Terlaris</h6>
            </div>
            <div class="card-body">
                <canvas id="chartSparepart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
new Chart(ctxPendapatan, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_map(function($d) { return date('M Y', strtotime($d['bulan'] . '-01')); }, $dataBulan)); ?>,
        datasets: [{
            label: 'Pendapatan',
            data: <?php echo json_encode(array_map(function($d) { return $d['total']; }, $dataBulan)); ?>,
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#f97316',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
});

var ctxSparepart = document.getElementById('chartSparepart').getContext('2d');
new Chart(ctxSparepart, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_map(function($d) { return $d['nama_sparepart']; }, $dataTop)); ?>,
        datasets: [{
            label: 'Qty Terjual',
            data: <?php echo json_encode(array_map(function($d) { return $d['total_qty']; }, $dataTop)); ?>,
            backgroundColor: ['#0a1628', '#1e3a5f', '#f97316', '#fb923c', '#0891b2'],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
