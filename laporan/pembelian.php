<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$period = isset($_GET['period']) ? $_GET['period'] : '';
list($dari, $sampai) = getPeriodDates($period);
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
if ($search) $where .= " AND (s.nama LIKE '%$search%' OR sp.kode_sparepart LIKE '%$search%' OR sp.nama_sparepart LIKE '%$search%')";

$query = "SELECT p.purchase_id, p.tanggal, s.nama as nama_supplier, sp.kode_sparepart, sp.nama_sparepart,
                 p.qty, p.harga_beli, p.total_harga, p.keterangan
          FROM transaksi_pembelian p
          LEFT JOIN supplier s ON p.supplier_id = s.supplier_id
          LEFT JOIN sparepart sp ON p.sparepart_id = sp.sparepart_id
          $where ORDER BY p.tanggal DESC";
$result = mysqli_query($conn, $query);

$rows = []; $sumQty = 0; $sumTotal = 0;
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; $sumQty += $row['qty']; $sumTotal += $row['total_harga']; }
?>

<div class="page-header"><h4><i class="bi bi-cart-check me-2"></i>Laporan Pembelian Sparepart</h4></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-end">
        <?php echo renderPeriodFilter($period, $_GET['dari'] ?? '', $_GET['sampai'] ?? ''); ?>
        <div><label class="form-label mb-1 small">Cari</label><input type="text" class="form-control form-control-sm" name="search" placeholder="Supplier, kode, nama..." value="<?php echo htmlspecialchars($search); ?>"></div>
        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div></div>

<div class="card mb-3"><div class="card-body py-2"><small class="fw-bold">
    Total <?php echo count($rows); ?> pembelian &nbsp;|&nbsp; Total Qty: <?php echo number_format($sumQty); ?> &nbsp;|&nbsp;
    Total Nilai: <span class="text-danger"><?php echo formatRupiah($sumTotal); ?></span>
</small></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>ID</th><th>Tanggal</th><th>Supplier</th><th>Kode</th><th>Nama Sparepart</th><th>Qty</th><th>Harga Beli</th><th>Total</th><th>Keterangan</th></tr></thead>
        <tbody>
            <?php if (count($rows) > 0): foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['purchase_id']; ?></td>
                <td><?php echo formatDateTime($row['tanggal']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_supplier'] ?? '-'); ?></td>
                <td><code><?php echo htmlspecialchars($row['kode_sparepart']); ?></code></td>
                <td><?php echo htmlspecialchars($row['nama_sparepart']); ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo formatRupiah($row['harga_beli']); ?></td>
                <td class="fw-bold"><?php echo formatRupiah($row['total_harga']); ?></td>
                <td><small class="text-muted"><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></small></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<?php echo renderPeriodScript(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
