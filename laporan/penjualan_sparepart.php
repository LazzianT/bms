<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$period = isset($_GET['period']) ? $_GET['period'] : '';
list($dari, $sampai) = getPeriodDates($period);
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$having = $search ? "HAVING sp.kode_sparepart LIKE '%$search%' OR sp.nama_sparepart LIKE '%$search%'" : '';

$query = "SELECT sp.kode_sparepart, sp.nama_sparepart, sp.satuan,
                 SUM(d.qty) as total_terjual, SUM(d.subtotal) as total_nilai, COUNT(DISTINCT d.trans_id) as jml_transaksi
          FROM transaksi_servis_detail d
          JOIN sparepart sp ON d.sparepart_id = sp.sparepart_id
          JOIN transaksi_servis ts ON d.trans_id = ts.trans_id
          WHERE DATE(ts.tanggal) BETWEEN '$dari' AND '$sampai'
          GROUP BY sp.sparepart_id, sp.kode_sparepart, sp.nama_sparepart, sp.satuan $having
          ORDER BY total_terjual DESC";
$result = mysqli_query($conn, $query);

$rows = []; $sumQty = 0; $sumNilai = 0; $sumTrx = 0;
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; $sumQty += $row['total_terjual']; $sumNilai += $row['total_nilai']; $sumTrx += $row['jml_transaksi']; }
?>

<div class="page-header"><h4><i class="bi bi-box-seam me-2"></i>Laporan Sparepart Terlaris</h4></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
        <?php echo renderPeriodFilter($period, $_GET['dari'] ?? '', $_GET['sampai'] ?? ''); ?>
        <div><label class="form-label mb-1 small">Cari</label><input type="text" class="form-control form-control-sm" name="search" placeholder="Kode/nama part..." value="<?php echo htmlspecialchars($search); ?>"></div>
        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div></div>

<div class="card mb-3"><div class="card-body py-2"><small class="fw-bold">
    <?php echo count($rows); ?> jenis sparepart &nbsp;|&nbsp; Total Qty: <?php echo number_format($sumQty); ?> &nbsp;|&nbsp;
    Total Nilai: <span class="text-success"><?php echo formatRupiah($sumNilai); ?></span> &nbsp;|&nbsp; Dari <?php echo $sumTrx; ?> transaksi
</small></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>Kode</th><th>Nama Sparepart</th><th>Satuan</th><th>Total Qty</th><th>Total Nilai</th><th>Jml Transaksi</th></tr></thead>
        <tbody>
            <?php if (count($rows) > 0): foreach ($rows as $row): ?>
            <tr>
                <td><code><?php echo htmlspecialchars($row['kode_sparepart']); ?></code></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_sparepart']); ?></td>
                <td><?php echo htmlspecialchars($row['satuan']); ?></td>
                <td><span class="badge bg-info"><?php echo $row['total_terjual']; ?> <?php echo $row['satuan']; ?></span></td>
                <td class="fw-bold text-success"><?php echo formatRupiah($row['total_nilai']); ?></td>
                <td><?php echo $row['jml_transaksi']; ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<?php echo renderPeriodScript(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
