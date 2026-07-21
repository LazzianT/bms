<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$period  = isset($_GET['period']) ? $_GET['period'] : '';
list($dari, $sampai) = getPeriodDates($period);
$groupBy = isset($_GET['group_by']) ? $_GET['group_by'] : 'DAY';
$status  = isset($_GET['status']) ? $_GET['status'] : '';
$metode  = isset($_GET['metode']) ? $_GET['metode'] : '';

$groupExpr = "DATE(ts.tanggal)";
$labelExpr = "DATE(ts.tanggal)";
if ($groupBy == 'WEEK') { $groupExpr = "YEARWEEK(ts.tanggal, 1)"; $labelExpr = "CONCAT('Minggu ', WEEK(MIN(ts.tanggal), 1), ' (', YEAR(MIN(ts.tanggal)), ')')"; }
elseif ($groupBy == 'MONTH') { $groupExpr = "DATE_FORMAT(ts.tanggal, '%Y-%m')"; $labelExpr = "DATE_FORMAT(MIN(ts.tanggal), '%M %Y')"; }

$where = "WHERE DATE(ts.tanggal) BETWEEN '$dari' AND '$sampai'";
if ($status) $where .= " AND ts.status_servis = '" . mysqli_real_escape_string($conn, $status) . "'";
if ($metode) $where .= " AND ts.metode_bayar = '" . mysqli_real_escape_string($conn, $metode) . "'";

$query = "SELECT $labelExpr as periode, COUNT(*) as jml_transaksi, COALESCE(SUM(ts.total_jasa),0) as pendapatan_jasa,
                 COALESCE(SUM(ts.total_sparepart),0) as pendapatan_sparepart, COALESCE(SUM(ts.grand_total),0) as total_omset, COALESCE(SUM(ts.bayar),0) as total_dibayar
          FROM transaksi_servis ts $where GROUP BY $groupExpr ORDER BY MIN(ts.tanggal) DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    $error = mysqli_error($conn);
}

$rows = []; $sumTrx = 0; $sumJasa = 0; $sumSp = 0; $sumOmset = 0; $sumBayar = 0;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; $sumTrx += $row['jml_transaksi']; $sumJasa += $row['pendapatan_jasa']; $sumSp += $row['pendapatan_sparepart']; $sumOmset += $row['total_omset']; $sumBayar += $row['total_dibayar']; }
}

$pembelianResult = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_harga),0) as total FROM transaksi_pembelian WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'"));
$totalPembelian = $pembelianResult['total'];
$labaKotor = $sumOmset - $totalPembelian;
$margin = $sumOmset > 0 ? ($labaKotor / $sumOmset * 100) : 0;
?>

<div class="page-header">
    <h4><i class="bi bi-cash-stack me-2"></i>Laporan Omset & Pendapatan</h4>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
        <?php echo renderPeriodFilter($period, $_GET['dari'] ?? '', $_GET['sampai'] ?? ''); ?>
        <div><label class="form-label mb-1 small">Group By</label>
            <select class="form-select form-select-sm" name="group_by">
                <option value="DAY" <?php echo $groupBy == 'DAY' ? 'selected' : ''; ?>>Harian</option>
                <option value="WEEK" <?php echo $groupBy == 'WEEK' ? 'selected' : ''; ?>>Mingguan</option>
                <option value="MONTH" <?php echo $groupBy == 'MONTH' ? 'selected' : ''; ?>>Bulanan</option>
            </select>
        </div>
        <div><label class="form-label mb-1 small">Status</label>
            <select class="form-select form-select-sm" name="status">
                <option value="">Semua</option>
                <option value="Selesai Lunas" <?php echo $status == 'Selesai Lunas' ? 'selected' : ''; ?>>Selesai Lunas</option>
                <option value="Dikerjakan" <?php echo $status == 'Dikerjakan' ? 'selected' : ''; ?>>Dikerjakan</option>
                <option value="Menunggu" <?php echo $status == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
            </select>
        </div>
        <div><label class="form-label mb-1 small">Metode</label>
            <select class="form-select form-select-sm" name="metode">
                <option value="">Semua</option>
                <option value="Cash" <?php echo $metode == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                <option value="Transfer" <?php echo $metode == 'Transfer' ? 'selected' : ''; ?>>Transfer</option>
                <option value="QRIS" <?php echo $metode == 'QRIS' ? 'selected' : ''; ?>>QRIS</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div></div>

<?php if (isset($error)): ?>
<div class="alert alert-danger">Query error: <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card mb-3"><div class="card-body py-2">
    <div class="mb-1"><small class="fw-bold">
        <?php echo count($rows); ?> periode &nbsp;|&nbsp; <?php echo $sumTrx; ?> transaksi &nbsp;|&nbsp;
        Jasa: <?php echo formatRupiah($sumJasa); ?> &nbsp;|&nbsp; Sparepart: <?php echo formatRupiah($sumSp); ?> &nbsp;|&nbsp;
        Omzet: <span class="text-primary"><?php echo formatRupiah($sumOmset); ?></span> &nbsp;|&nbsp; Dibayar: <?php echo formatRupiah($sumBayar); ?>
    </small></div>
    <small class="fw-bold text-success">
        Pembelian Stok: <?php echo formatRupiah($totalPembelian); ?> &nbsp;|&nbsp;
        Laba Kotor: <?php echo formatRupiah($labaKotor); ?> &nbsp;|&nbsp;
        Margin: <?php echo number_format($margin, 1); ?>%
    </small>
</div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>Periode</th><th>Jml Transaksi</th><th>Pendapatan Jasa</th><th>Pendapatan Part</th><th>Total Omzet</th><th>Total Dibayar</th></tr></thead>
        <tbody>
            <?php if (count($rows) > 0): foreach ($rows as $row): ?>
            <tr>
                <td class="fw-semibold"><?php echo $groupBy == 'DAY' ? formatDate($row['periode']) : htmlspecialchars($row['periode']); ?></td>
                <td><?php echo $row['jml_transaksi']; ?></td>
                <td><?php echo formatRupiah($row['pendapatan_jasa']); ?></td>
                <td><?php echo formatRupiah($row['pendapatan_sparepart']); ?></td>
                <td class="fw-bold text-primary"><?php echo formatRupiah($row['total_omset']); ?></td>
                <td><?php echo formatRupiah($row['total_dibayar']); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<?php echo renderPeriodScript(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
