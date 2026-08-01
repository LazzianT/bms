<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin']);

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = '';
if ($search) {
    $where = "WHERE nama_jasa LIKE '%$search%' OR kategori LIKE '%$search%'";
}

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM master_jasa $where");
$totalData  = mysqli_fetch_assoc($totalQuery)['jml'];
$totalPages = ceil($totalData / $limit);

$query  = "SELECT * FROM master_jasa $where ORDER BY jasa_id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div class="page-header">
    <h4><i class="bi bi-gear me-2"></i>Master Jasa</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Jasa
    </button>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari nama jasa, kategori..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <?php if ($search): ?>
                <a href="<?= BASE_URL ?>/master/jasa.php" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                        <th>Nama Jasa</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_jasa']); ?></td>
                            <td><?php echo htmlspecialchars($row['kategori'] ?? '-'); ?></td>
                            <td><?php echo formatRupiah($row['harga']); ?></td>
                            <td>
                                <?php if ($row['is_aktif']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?php echo $row['jasa_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteJasa(<?php echo $row['jasa_id']; ?>, '<?php echo htmlspecialchars($row['nama_jasa']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data jasa
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

<!-- Modal Add/Edit -->
<div class="modal fade" id="jasaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah Jasa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="jasaForm" onsubmit="return saveJasa(event)">
                <div class="modal-body">
                    <input type="hidden" id="jasa_id" name="jasa_id">
                    <input type="hidden" id="form_action" name="action" value="add">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Jasa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_jasa" name="nama_jasa" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" id="kategori" name="kategori" placeholder="Servis, Mesin, CVT...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga" name="harga" min="0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" checked>
                                <label class="form-check-label" for="is_aktif">Aktif</label>
                            </div>
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
function openAddModal() {
    document.getElementById('jasaForm').reset();
    document.getElementById('jasa_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('modalTitle').textContent = 'Tambah Jasa';
    document.getElementById('is_aktif').checked = true;
    new bootstrap.Modal(document.getElementById('jasaModal')).show();
}

function openEditModal(id) {
    fetch('<?= BASE_URL ?>/api/jasa_action.php?action=get&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                var j = data.data;
                document.getElementById('jasa_id').value = j.jasa_id;
                document.getElementById('nama_jasa').value = j.nama_jasa;
                document.getElementById('kategori').value = j.kategori || '';
                document.getElementById('harga').value = j.harga;
                document.getElementById('deskripsi').value = j.deskripsi || '';
                document.getElementById('is_aktif').checked = j.is_aktif == 1;
                document.getElementById('form_action').value = 'update';
                document.getElementById('modalTitle').textContent = 'Edit Jasa';
                new bootstrap.Modal(document.getElementById('jasaModal')).show();
            }
        });
}

function saveJasa(e) {
    e.preventDefault();
    var formData = new FormData(document.getElementById('jasaForm'));
    if (!document.getElementById('is_aktif').checked) {
        formData.set('is_aktif', '0');
    }
    fetch('<?= BASE_URL ?>/api/jasa_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { BMS.success('Jasa berhasil disimpan'); setTimeout(function(){ location.reload(); }, 800); }
            else { BMS.error(data.message); }
        });
    return false;
}

function deleteJasa(id, nama) {
    BMS.confirm('Yakin hapus jasa "' + nama + '"?').then(function(ok) {
        if (!ok) return;
        var formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        fetch('<?= BASE_URL ?>/api/jasa_action.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) { BMS.success('Jasa berhasil dihapus'); setTimeout(function(){ location.reload(); }, 800); }
                else { BMS.error(data.message); }
            });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
