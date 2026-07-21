<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$period = isset($_GET['period']) ? $_GET['period'] : '';
list($dari, $sampai) = getPeriodDates($period);
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE DATE(ts.tanggal) BETWEEN '$dari' AND '$sampai'";
if ($status) $where .= " AND ts.status_servis = '" . mysqli_real_escape_string($conn, $status) . "'";
if ($search) $where .= " AND (c.nama LIKE '%$search%' OR v.no_polisi LIKE '%$search%' OR m.nama LIKE '%$search%' OR ts.keluhan LIKE '%$search%')";

$query = "SELECT ts.trans_id, ts.tanggal, c.nama as nama_client, v.no_polisi, m.nama as nama_mekanik,
                 ts.keluhan, ts.status_servis, ts.total_jasa, ts.total_sparepart, ts.grand_total, ts.metode_bayar
          FROM transaksi_servis ts
          LEFT JOIN client c ON ts.client_id = c.client_id
          LEFT JOIN vehicle v ON ts.vehicle_id = v.vehicle_id
          LEFT JOIN mekanik m ON ts.mekanik_id = m.mekanik_id
          $where ORDER BY ts.tanggal DESC";
$result = mysqli_query($conn, $query);

$rows = []; $sumJasa = 0; $sumSp = 0; $sumGrand = 0;
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; $sumJasa += $row['total_jasa']; $sumSp += $row['total_sparepart']; $sumGrand += $row['grand_total']; }
?>

<div class="page-header">
    <h4><i class="bi bi-clipboard-check me-2"></i>Laporan Transaksi Servis</h4>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
            <?php echo renderPeriodFilter($period, $_GET['dari'] ?? '', $_GET['sampai'] ?? ''); ?>
            <div><label class="form-label mb-1 small">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">Semua</option>
                    <option value="Menunggu" <?php echo $status == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                    <option value="Dikerjakan" <?php echo $status == 'Dikerjakan' ? 'selected' : ''; ?>>Dikerjakan</option>
                    <option value="Selesai Lunas" <?php echo $status == 'Selesai Lunas' ? 'selected' : ''; ?>>Selesai Lunas</option>
                </select>
            </div>
            <div><label class="form-label mb-1 small">Cari</label>
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Nama, plat, mekanik..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<div class="card mb-3"><div class="card-body py-2"><small class="fw-bold">
    Total <?php echo count($rows); ?> transaksi &nbsp;|&nbsp; Jasa: <span class="text-primary"><?php echo formatRupiah($sumJasa); ?></span> &nbsp;|&nbsp; Sparepart: <span class="text-primary"><?php echo formatRupiah($sumSp); ?></span> &nbsp;|&nbsp; Grand Total: <span class="text-success fw-bold"><?php echo formatRupiah($sumGrand); ?></span>
</small></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>No Polisi</th><th>Mekanik</th><th>Keluhan</th><th>Status</th><th>Total Jasa</th><th>Total Part</th><th>Grand Total</th><th>Metode</th></tr></thead>
        <tbody>
            <?php if (count($rows) > 0): foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['trans_id']; ?></td>
                <td><?php echo formatDateTime($row['tanggal']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_client']); ?></td>
                <td><span class="badge badge-nopol"><?php echo htmlspecialchars($row['no_polisi']); ?></span></td>
                <td><?php echo htmlspecialchars($row['nama_mekanik'] ?? '-'); ?></td>
                <td><small><?php echo htmlspecialchars(mb_strimwidth($row['keluhan'] ?? '', 0, 30, '...')); ?></small></td>
                <td><?php echo setStatusBadge($row['status_servis']); ?></td>
                <td><?php echo formatRupiah($row['total_jasa']); ?></td>
                <td><?php echo formatRupiah($row['total_sparepart']); ?></td>
                <td class="fw-bold"><?php echo formatRupiah($row['grand_total']); ?></td>
                <td><small><?php echo $row['metode_bayar'] ?? '-'; ?></small></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="11" class="text-center py-4 text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<?php echo renderPeriodScript(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
