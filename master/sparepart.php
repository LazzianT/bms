<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$search     = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$supplierId = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
$stockFilter = isset($_GET['stock']) ? $_GET['stock'] : '';
$page       = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit      = 10;
$offset     = ($page - 1) * $limit;

$where = [];
if ($search) {
    $where[] = "(sp.kode_sparepart LIKE '%$search%' OR sp.nama_sparepart LIKE '%$search%')";
}
if ($supplierId) {
    $where[] = "sp.supplier_id = $supplierId";
}
if ($stockFilter == 'habis') {
    $where[] = "sp.stok <= 0";
} elseif ($stockFilter == 'menipis') {
    $where[] = "sp.stok > 0 AND sp.stok <= sp.stok_minimum";
} elseif ($stockFilter == 'aman') {
    $where[] = "sp.stok > sp.stok_minimum";
}

$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM sparepart sp $whereClause");
$totalData  = mysqli_fetch_assoc($totalQuery)['jml'];
$totalPages = ceil($totalData / $limit);

$query = "SELECT sp.*, s.nama as nama_supplier 
          FROM sparepart sp 
          LEFT JOIN supplier s ON sp.supplier_id = s.supplier_id 
          $whereClause 
          ORDER BY sp.sparepart_id DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$suppliers = mysqli_query($conn, "SELECT supplier_id, nama FROM supplier ORDER BY nama");
?>

<div class="page-header">
    <h4><i class="bi bi-box me-2"></i>Master Sparepart</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Sparepart
    </button>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width:320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari kode, nama sparepart..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <select class="form-select" name="supplier_id" style="max-width:200px;">
                <option value="0">Semua Supplier</option>
                <?php while ($s = mysqli_fetch_assoc($suppliers)): ?>
                    <option value="<?php echo $s['supplier_id']; ?>" <?php echo ($supplierId == $s['supplier_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select class="form-select" name="stock" style="max-width:180px;">
                <option value="">Semua Stok</option>
                <option value="habis" <?php echo ($stockFilter == 'habis') ? 'selected' : ''; ?>>Stok Habis</option>
                <option value="menipis" <?php echo ($stockFilter == 'menipis') ? 'selected' : ''; ?>>Stok Menipis</option>
                <option value="aman" <?php echo ($stockFilter == 'aman') ? 'selected' : ''; ?>>Stok Aman</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($search || $supplierId || $stockFilter): ?>
                <a href="<?= BASE_URL ?>/master/sparepart.php" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                        <th>Kode</th>
                        <th>Nama Sparepart</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Margin</th>
                        <th>Supplier</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $stok = $row['stok'];
                            $stokMin = $row['stok_minimum'] ?? 5;
                            if ($stok <= 0) {
                                $stokBadge = 'bg-danger';
                                $stokLabel = 'Habis';
                            } elseif ($stok <= $stokMin) {
                                $stokBadge = 'bg-warning text-dark';
                                $stokLabel = 'Menipis';
                            } else {
                                $stokBadge = 'bg-success';
                                $stokLabel = 'Aman';
                            }
                            $margin = $row['harga_jual'] - $row['harga_beli'];
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><code class="fw-bold"><?php echo htmlspecialchars($row['kode_sparepart']); ?></code></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_sparepart']); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan']); ?></td>
                            <td>
                                <span class="badge <?php echo $stokBadge; ?>"><?php echo $stok; ?> <?php echo $row['satuan']; ?></span>
                            </td>
                            <td><?php echo formatRupiah($row['harga_beli']); ?></td>
                            <td><?php echo formatRupiah($row['harga_jual']); ?></td>
                            <td>
                                <?php if ($margin > 0): ?>
                                    <span class="text-success fw-semibold">+<?php echo formatRupiah($margin); ?></span>
                                <?php else: ?>
                                    <span class="text-danger fw-semibold"><?php echo formatRupiah($margin); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo htmlspecialchars($row['nama_supplier'] ?? '-'); ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?php echo $row['sparepart_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSparepart(<?php echo $row['sparepart_id']; ?>, '<?php echo htmlspecialchars($row['kode_sparepart']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data sparepart
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
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&supplier_id=<?php echo $supplierId; ?>&stock=<?php echo urlencode($stockFilter); ?>">Prev</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&supplier_id=<?php echo $supplierId; ?>&stock=<?php echo urlencode($stockFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&supplier_id=<?php echo $supplierId; ?>&stock=<?php echo urlencode($stockFilter); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="sparepartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah Sparepart</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sparepartForm" onsubmit="return saveSparepart(event)">
                <div class="modal-body">
                    <input type="hidden" id="sparepart_id" name="sparepart_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Sparepart <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_sparepart" name="kode_sparepart" required placeholder="OLI-001">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nama Sparepart <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_sparepart" name="nama_sparepart" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select class="form-select" id="satuan" name="satuan" required>
                                <option value="Pcs">Pcs</option>
                                <option value="Botol">Botol</option>
                                <option value="Set">Set</option>
                                <option value="Liter">Liter</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok Minimum</label>
                            <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" value="5" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Pilih Supplier --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-body text-center py-4">
                <div style="width:56px;height:56px;background:#fef2f2;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="bi bi-exclamation-triangle" style="font-size:1.4rem;color:#dc2626;"></i>
                </div>
                <h6 class="fw-bold mb-1">Hapus Sparepart?</h6>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Kode <strong id="deleteName"></strong> akan dihapus permanen.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm px-3" onclick="confirmDelete()">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var sparepartModal, deleteModal;
var deleteId = null;

document.addEventListener('DOMContentLoaded', function() {
    sparepartModal = new bootstrap.Modal(document.getElementById('sparepartModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    loadSuppliers();
});

function loadSuppliers() {
    fetch('<?= BASE_URL ?>/api/sparepart_action.php?action=get_suppliers')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var select = document.getElementById('supplier_id');
                data.data.forEach(function(s) {
                    var opt = document.createElement('option');
                    opt.value = s.supplier_id;
                    opt.textContent = s.nama;
                    select.appendChild(opt);
                });
            }
        });
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Sparepart';
    document.getElementById('sparepartForm').reset();
    document.getElementById('sparepart_id').value = '';
    document.getElementById('stok_minimum').value = '5';
    sparepartModal.show();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Sparepart';
    fetch('<?= BASE_URL ?>/api/sparepart_action.php?action=get&id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var d = data.data;
                document.getElementById('sparepart_id').value = d.sparepart_id;
                document.getElementById('kode_sparepart').value = d.kode_sparepart;
                document.getElementById('nama_sparepart').value = d.nama_sparepart;
                document.getElementById('satuan').value = d.satuan;
                document.getElementById('stok').value = d.stok;
                document.getElementById('stok_minimum').value = d.stok_minimum || 5;
                document.getElementById('harga_beli').value = d.harga_beli;
                document.getElementById('harga_jual').value = d.harga_jual;
                document.getElementById('supplier_id').value = d.supplier_id;
                sparepartModal.show();
            }
        });
}

function saveSparepart(e) {
    e.preventDefault();
    var form = document.getElementById('sparepartForm');
    var formData = new FormData(form);
    formData.append('action', document.getElementById('sparepart_id').value ? 'update' : 'add');

    fetch('<?= BASE_URL ?>/api/sparepart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            sparepartModal.hide();
            BMS.success('Sparepart berhasil disimpan');
            setTimeout(function(){ window.location.reload(); }, 800);
        } else {
            BMS.error(data.message || 'Terjadi kesalahan');
        }
    });
    return false;
}

function deleteSparepart(id, name) {
    deleteId = id;
    document.getElementById('deleteName').textContent = name;
    deleteModal.show();
}

function confirmDelete() {
    fetch('<?= BASE_URL ?>/api/sparepart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + deleteId
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            deleteModal.hide();
            BMS.success('Sparepart berhasil dihapus');
            setTimeout(function(){ window.location.reload(); }, 800);
        } else {
            BMS.error(data.message || 'Gagal menghapus');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
