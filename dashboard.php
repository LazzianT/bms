<?php
require_once __DIR__ . '/includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

// ==================== STAT CARDS ====================
$transaksiHariIni = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE DATE(tanggal) = CURDATE()"))['jml'];
$omsetHariIni     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total),0) as total FROM transaksi_servis WHERE DATE(tanggal) = CURDATE()"))['total'];
$antrian          = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE DATE(tanggal) = CURDATE() AND status_servis = 'Menunggu'"))['jml'];
$pengerjaan       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE DATE(tanggal) = CURDATE() AND status_servis = 'Dikerjakan'"))['jml'];
$selesai          = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_servis WHERE DATE(tanggal) = CURDATE() AND status_servis = 'Selesai Lunas'"))['jml'];
$stokKritis       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM sparepart WHERE stok <= 5"))['jml'];

// ==================== FINANCE ====================
$omsetMinggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total),0) as total FROM transaksi_servis WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)"))['total'];
$omsetBulan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total),0) as total FROM transaksi_servis WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())"))['total'];
$omsetTahun  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total),0) as total FROM transaksi_servis WHERE YEAR(tanggal) = YEAR(CURDATE())"))['total'];

// Mekanik terajin bulan ini
$mekanikTerajin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT m.nama, COUNT(t.trans_id) as total_servis FROM transaksi_servis t JOIN mekanik m ON t.mekanik_id = m.mekanik_id WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE()) GROUP BY m.mekanik_id, m.nama ORDER BY total_servis DESC LIMIT 1"));

// ==================== ANTRIAN HARI INI ====================
$queryAntrian = mysqli_query($conn, "SELECT v.no_polisi, t.status_servis FROM transaksi_servis t JOIN vehicle v ON t.vehicle_id = v.vehicle_id WHERE DATE(t.tanggal) = CURDATE() AND t.status_servis IN ('Menunggu', 'Dikerjakan') ORDER BY t.tanggal ASC LIMIT 8");

// ==================== SPAREPART TERLARIS ====================
$querySp = mysqli_query($conn, "SELECT s.nama_sparepart, SUM(td.qty) as total_qty FROM transaksi_servis_detail td JOIN sparepart s ON td.sparepart_id = s.sparepart_id JOIN transaksi_servis t ON td.trans_id = t.trans_id WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE()) GROUP BY s.sparepart_id, s.nama_sparepart ORDER BY total_qty DESC LIMIT 5");
$dataTopSp = [];
while ($row = mysqli_fetch_assoc($querySp)) { $dataTopSp[] = $row; }

// ==================== KINERJA MEKANIK ====================
$queryKinerja = mysqli_query($conn, "SELECT m.nama, COUNT(t.trans_id) as total_servis FROM transaksi_servis t JOIN mekanik m ON t.mekanik_id = m.mekanik_id WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE()) GROUP BY m.mekanik_id, m.nama ORDER BY total_servis DESC LIMIT 6");
$dataKinerja = [];
$maxServis = 1;
while ($row = mysqli_fetch_assoc($queryKinerja)) { $dataKinerja[] = $row; $maxServis = max($maxServis, $row['total_servis']); }

// ==================== OMZET BULANAN (Chart) ====================
$queryOmzetBulanan = mysqli_query($conn, "SELECT MONTH(tanggal) as bulan, SUM(grand_total) as total FROM transaksi_servis WHERE YEAR(tanggal) = YEAR(CURDATE()) GROUP BY MONTH(tanggal) ORDER BY bulan ASC");
$dataOmzetBulanan = [];
while ($row = mysqli_fetch_assoc($queryOmzetBulanan)) { $dataOmzetBulanan[] = $row; }

// ==================== TIPE KENDARAAN (Chart) ====================
$queryTipe = mysqli_query($conn, "SELECT COALESCE(v.tipe_kendaraan, 'Lainnya') as tipe, COUNT(t.trans_id) as total FROM transaksi_servis t JOIN vehicle v ON t.vehicle_id = v.vehicle_id GROUP BY v.tipe_kendaraan");
$dataTipe = [];
while ($row = mysqli_fetch_assoc($queryTipe)) { $dataTipe[] = $row; }
?>

<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
    <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
</div>

<!-- ==================== STAT CARDS ==================== -->
<div class="row mb-4 g-3">
    <div class="col-md-2">
        <div class="stat-card stat-card-blue">
            <div><h6>Transaksi</h6><h3><?php echo $transaksiHariIni; ?></h3></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-card-green">
            <div><h6>Omset Hari Ini</h6><h3><?php echo formatRupiah($omsetHariIni); ?></h3></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-card-teal">
            <div><h6>Antrian</h6><h3><?php echo $antrian; ?></h3></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-card-orange">
            <div><h6>Pengerjaan</h6><h3><?php echo $pengerjaan; ?></h3></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:#fff;border-radius:12px;padding:16px;">
            <div><h6 style="color:rgba(255,255,255,.8)">Selesai</h6><h3><?php echo $selesai; ?></h3></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:12px;padding:16px;">
            <div><h6 style="color:rgba(255,255,255,.8)">Stok Kritis</h6><h3><?php echo $stokKritis; ?></h3></div>
        </div>
    </div>
</div>

<!-- ==================== CHARTS ROW ==================== -->
<div class="row mb-4 g-3">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Omzet Bulanan <?php echo date('Y'); ?></h6></div>
            <div class="card-body"><canvas id="chartOmzet" height="160"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Tipe Kendaraan Servis</h6></div>
            <div class="card-body"><canvas id="chartTipe" height="160"></canvas></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0">Top 5 Sparepart</h6></div>
            <div class="card-body"><canvas id="chartSparepart" height="160"></canvas></div>
        </div>
    </div>
</div>

<!-- ==================== BOTTOM ROW ==================== -->
<div class="row g-3">
    <!-- Keuangan & Performa -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-wallet2 me-1"></i> Keuangan & Performa</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Omset Minggu Ini</small>
                    <div class="fw-bold text-primary"><?php echo formatRupiah($omsetMinggu); ?></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Omset Bulan Ini</small>
                    <div class="fw-bold text-success"><?php echo formatRupiah($omsetBulan); ?></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Omset Tahun Ini</small>
                    <div class="fw-bold"><?php echo formatRupiah($omsetTahun); ?></div>
                </div>
                <hr>
                <div>
                    <small class="text-muted">🏆 Mekanik Terajin</small>
                    <div class="fw-bold">
                        <?php echo $mekanikTerajin ? htmlspecialchars($mekanikTerajin['nama']) . ' (' . $mekanikTerajin['total_servis'] . ' servis)' : 'Belum ada data'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kinerja Mekanik -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person-workspace me-1"></i> Kinerja Mekanik</h6></div>
            <div class="card-body">
                <?php if (count($dataKinerja) > 0): ?>
                    <?php $colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#f97316','#ef4444']; ?>
                    <?php foreach ($dataKinerja as $i => $mk): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span><?php echo htmlspecialchars($mk['nama']); ?></span>
                            <span class="fw-bold"><?php echo $mk['total_servis']; ?></span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar" style="width:<?php echo ($mk['total_servis'] / $maxServis * 100); ?>%;background:<?php echo $colors[$i % 6]; ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center small">Belum ada data</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Antrian Hari Ini -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Antrian Hari Ini</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th class="small">No Polisi</th><th class="small">Status</th></tr></thead>
                    <tbody>
                        <?php $hasAntrian = false; while ($a = mysqli_fetch_assoc($queryAntrian)): $hasAntrian = true; ?>
                        <tr>
                            <td><small class="fw-semibold"><?php echo htmlspecialchars($a['no_polisi']); ?></small></td>
                            <td><?php echo setStatusBadge($a['status_servis']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (!$hasAntrian): ?>
                        <tr><td colspan="2" class="text-center text-muted small py-3">Tidak ada antrian</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sparepart Terlaris -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-fire me-1"></i> Sparepart Terlaris</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th class="small">Sparepart</th><th class="small">Qty</th></tr></thead>
                    <tbody>
                        <?php if (count($dataTopSp) > 0): ?>
                            <?php foreach ($dataTopSp as $sp): ?>
                            <tr>
                                <td><small><?php echo htmlspecialchars($sp['nama_sparepart']); ?></small></td>
                                <td><span class="badge bg-primary"><?php echo $sp['total_qty']; ?> pcs</span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted small py-3">Belum ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Omzet Bulanan Bar Chart
var bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
var omzetData = <?php echo json_encode($dataOmzetBulanan); ?>;
new Chart(document.getElementById('chartOmzet'), {
    type: 'bar',
    data: {
        labels: omzetData.map(d => bulanNama[d.bulan]),
        datasets: [{
            label: 'Omzet',
            data: omzetData.map(d => d.total),
            backgroundColor: '#3b82f6',
            borderRadius: 6
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});

// Tipe Kendaraan Pie Chart
var tipeData = <?php echo json_encode($dataTipe); ?>;
new Chart(document.getElementById('chartTipe'), {
    type: 'doughnut',
    data: {
        labels: tipeData.map(d => d.tipe || 'Lainnya'),
        datasets: [{
            data: tipeData.map(d => d.total),
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

// Top 5 Sparepart Horizontal Bar
var spData = <?php echo json_encode($dataTopSp); ?>;
new Chart(document.getElementById('chartSparepart'), {
    type: 'bar',
    data: {
        labels: spData.map(d => d.nama_sparepart.length > 12 ? d.nama_sparepart.substring(0,12)+'...' : d.nama_sparepart),
        datasets: [{
            label: 'Qty',
            data: spData.map(d => d.total_qty),
            backgroundColor: ['#0a1628','#1e3a5f','#f97316','#fb923c','#0891b2'],
            borderRadius: 4
        }]
    },
    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' } }, y: { grid: { display: false } } } }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
