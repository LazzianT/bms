<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$period = isset($_GET['period']) ? $_GET['period'] : '';
list($dari, $sampai) = getPeriodDates($period);
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$having = $search ? "HAVING m.nama LIKE '%$search%' OR m.spesialis LIKE '%$search%'" : '';

$query = "SELECT m.mekanik_id, m.nama, m.spesialis,
                 COUNT(ts.trans_id) as total_servis,
                 SUM(CASE WHEN ts.status_servis = 'Selesai Lunas' THEN 1 ELSE 0 END) as selesai,
                 SUM(CASE WHEN ts.status_servis = 'Dikerjakan' THEN 1 ELSE 0 END) as dalam_proses,
                 COALESCE(SUM(ts.total_jasa), 0) as total_jasa,
                 COALESCE(SUM(ts.grand_total), 0) as total_revenue
          FROM mekanik m
          LEFT JOIN transaksi_servis ts ON m.mekanik_id = ts.mekanik_id AND DATE(ts.tanggal) BETWEEN '$dari' AND '$sampai'
          GROUP BY m.mekanik_id, m.nama, m.spesialis $having
          ORDER BY total_servis DESC";
$result = mysqli_query($conn, $query);

$rows = []; $sumServis = 0; $sumSelesai = 0; $sumJasa = 0; $sumRevenue = 0;
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; $sumServis += $row['total_servis']; $sumSelesai += $row['selesai']; $sumJasa += $row['total_jasa']; $sumRevenue += $row['total_revenue']; }
?>

<div class="page-header"><h4><i class="bi bi-person-check me-2"></i>Laporan Kinerja Mekanik</h4></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
        <?php echo renderPeriodFilter($period, $_GET['dari'] ?? '', $_GET['sampai'] ?? ''); ?>
        <div><label class="form-label mb-1 small">Cari</label><input type="text" class="form-control form-control-sm" name="search" placeholder="Nama mekanik..." value="<?php echo htmlspecialchars($search); ?>"></div>
        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div></div>

<div class="card mb-3"><div class="card-body py-2"><small class="fw-bold">
    <?php echo count($rows); ?> mekanik &nbsp;|&nbsp; Total Servis: <?php echo $sumServis; ?> &nbsp;|&nbsp; Selesai: <?php echo $sumSelesai; ?> &nbsp;|&nbsp;
    Total Jasa: <span class="text-primary"><?php echo formatRupiah($sumJasa); ?></span> &nbsp;|&nbsp; Revenue: <span class="text-success"><?php echo formatRupiah($sumRevenue); ?></span>
</small></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>ID</th><th>Nama Mekanik</th><th>Spesialis</th><th>Total Servis</th><th>Selesai</th><th>Dalam Proses</th><th>Total Jasa</th><th>Total Revenue</th></tr></thead>
        <tbody>
            <?php if (count($rows) > 0): foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['mekanik_id']; ?></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['spesialis'] ?? '-'); ?></td>
                <td><span class="badge bg-primary"><?php echo $row['total_servis']; ?></span></td>
                <td><span class="badge bg-success"><?php echo $row['selesai']; ?></span></td>
                <td><span class="badge bg-warning text-dark"><?php echo $row['dalam_proses']; ?></span></td>
                <td><?php echo formatRupiah($row['total_jasa']); ?></td>
                <td class="fw-bold"><?php echo formatRupiah($row['total_revenue']); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<?php echo renderPeriodScript(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
