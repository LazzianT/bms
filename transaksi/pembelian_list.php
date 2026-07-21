<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = '';
if ($search) {
    $where = "WHERE (sp.nama_sparepart LIKE '%$search%' OR s.nama LIKE '%$search%' OR sp.kode_sparepart LIKE '%$search%')";
}

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi_pembelian p LEFT JOIN sparepart sp ON p.sparepart_id = sp.sparepart_id LEFT JOIN supplier s ON p.supplier_id = s.supplier_id $where");
$totalData  = mysqli_fetch_assoc($totalQuery)['jml'];
$totalPages = ceil($totalData / $limit);

$query = "SELECT p.*, sp.kode_sparepart, sp.nama_sparepart, s.nama as nama_supplier 
          FROM transaksi_pembelian p 
          LEFT JOIN sparepart sp ON p.sparepart_id = sp.sparepart_id 
          LEFT JOIN supplier s ON p.supplier_id = s.supplier_id 
          $where 
          ORDER BY p.tanggal DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$suppliers  = mysqli_query($conn, "SELECT supplier_id, nama FROM supplier ORDER BY nama");
$spareparts = mysqli_query($conn, "SELECT sparepart_id, kode_sparepart, nama_sparepart, harga_beli, supplier_id FROM sparepart ORDER BY nama_sparepart");
?>

<div class="page-header">
    <h4><i class="bi bi-cart me-2"></i>Pembelian Sparepart</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pembelian
    </button>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari sparepart, supplier..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <?php if ($search): ?>
                <a href="/bms/transaksi/pembelian_list.php" class="btn btn-outline-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Tanggal</th>
                        <th>Sparepart</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Harga Beli</th>
                        <th>Total</th>
                        <th>Keterangan</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo formatDateTime($row['tanggal']); ?></td>
                            <td>
                                <code><?php echo htmlspecialchars($row['kode_sparepart']); ?></code><br>
                                <small><?php echo htmlspecialchars($row['nama_sparepart']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_supplier'] ?? '-'); ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td><?php echo formatRupiah($row['harga_beli']); ?></td>
                            <td class="fw-semibold"><?php echo formatRupiah($row['total_harga']); ?></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePembelian(<?php echo $row['purchase_id']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data pembelian
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Total <?php echo $totalData; ?> data</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">Prev</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Pembelian -->
<div class="modal fade" id="pembelianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold">Tambah Pembelian Sparepart</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pembelianForm" onsubmit="return savePembelian(event)">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required onchange="filterSparepart()">
                                <option value="">-- Pilih Supplier --</option>
                                <?php while ($s = mysqli_fetch_assoc($suppliers)): ?>
                                    <option value="<?php echo $s['supplier_id']; ?>"><?php echo htmlspecialchars($s['nama']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Sparepart <span class="text-danger">*</span></label>
                            <select class="form-select" id="sparepart_id" name="sparepart_id" required onchange="updateHarga()">
                                <option value="">-- Pilih Supplier dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="qty" name="qty" min="1" value="1" required onchange="hitungTotal()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Beli/pcs <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0" required onchange="hitungTotal()">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Total Harga</label>
                            <input type="text" class="form-control" id="total_display" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Opsional...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var sparepartData = <?php
    mysqli_data_seek($spareparts, 0);
    $spArr = [];
    while ($sp = mysqli_fetch_assoc($spareparts)) { $spArr[] = $sp; }
    echo json_encode($spArr);
?>;

function openAddModal() {
    document.getElementById('pembelianForm').reset();
    document.getElementById('total_display').value = '';
    new bootstrap.Modal(document.getElementById('pembelianModal')).show();
}

function filterSparepart() {
    var suppId = document.getElementById('supplier_id').value;
    var sel = document.getElementById('sparepart_id');
    sel.innerHTML = '<option value="">-- Pilih Sparepart --</option>';
    sparepartData.filter(function(s) { return s.supplier_id == suppId; }).forEach(function(s) {
        sel.innerHTML += '<option value="'+s.sparepart_id+'" data-harga="'+s.harga_beli+'">'+s.kode_sparepart+' - '+s.nama_sparepart+'</option>';
    });
}

function updateHarga() {
    var sel = document.getElementById('sparepart_id');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.harga) {
        var el = document.getElementById('harga_beli');
        el.dataset.raw = opt.dataset.harga;
        el.value = formatCurrency ? formatCurrency(opt.dataset.harga) : opt.dataset.harga;
        hitungTotal();
    }
}

function hitungTotal() {
    var qty = parseInt(document.getElementById('qty').value) || 0;
    var hargaEl = document.getElementById('harga_beli');
    var harga = parseFloat(hargaEl.dataset.raw || hargaEl.value.replace(/[^\d]/g, '')) || 0;
    document.getElementById('total_display').value = 'Rp ' + (qty * harga).toLocaleString('id-ID');
}

function savePembelian(e) {
    e.preventDefault();
    var formData = new FormData(document.getElementById('pembelianForm'));
    formData.append('action', 'add');
    fetch('/bms/api/pembelian_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message || 'Gagal menyimpan'); }
        });
    return false;
}

function deletePembelian(id) {
    if (!confirm('Yakin hapus data pembelian ini?')) return;
    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    fetch('/bms/api/pembelian_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message || 'Gagal menghapus'); }
        });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
