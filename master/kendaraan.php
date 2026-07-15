<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$search   = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit    = 10;
$offset   = ($page - 1) * $limit;

$where = '';
if ($search && $clientId) {
    $where = "WHERE (v.no_polisi LIKE '%$search%' OR v.merk LIKE '%$search%' OR v.tipe LIKE '%$search%') AND v.client_id = $clientId";
} elseif ($search) {
    $where = "WHERE v.no_polisi LIKE '%$search%' OR v.merk LIKE '%$search%' OR v.tipe LIKE '%$search%'";
} elseif ($clientId) {
    $where = "WHERE v.client_id = $clientId";
}

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM vehicle v $where");
$totalData  = mysqli_fetch_assoc($totalQuery)['jml'];
$totalPages = ceil($totalData / $limit);

$query = "SELECT v.*, c.nama as nama_client 
          FROM vehicle v 
          LEFT JOIN client c ON v.client_id = c.client_id 
          $where 
          ORDER BY v.vehicle_id DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$clients = mysqli_query($conn, "SELECT client_id, nama FROM client ORDER BY nama");
?>

<div class="page-header">
    <h4><i class="bi bi-bicycle me-2"></i>Master Kendaraan</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kendaraan
    </button>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width:350px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari no. polisi, merk, tipe..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <select class="form-select" name="client_id" style="max-width:220px;">
                <option value="0">Semua Pemilik</option>
                <?php while ($c = mysqli_fetch_assoc($clients)): ?>
                    <option value="<?php echo $c['client_id']; ?>" <?php echo ($clientId == $c['client_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($search || $clientId): ?>
                <a href="/bms/master/kendaraan.php" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                        <th>No. Polisi</th>
                        <th>Pemilik</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>CC</th>
                        <th>Tipe Kendaraan</th>
                        <th>Tahun</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><span class="badge badge-nopol"><?php echo htmlspecialchars($row['no_polisi']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['nama_client']); ?></td>
                            <td><?php echo htmlspecialchars($row['merk']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipe']); ?></td>
                            <td><?php echo $row['cc']; ?></td>
                            <td>
                                <?php if ($row['tipe_kendaraan'] == 'Roda 2'): ?>
                                    <span class="badge bg-success">Roda 2</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Lebih dari 2</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['tahun']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?php echo $row['vehicle_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteKendaraan(<?php echo $row['vehicle_id']; ?>, '<?php echo htmlspecialchars($row['no_polisi']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data kendaraan
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
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&client_id=<?php echo $clientId; ?>">Prev</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&client_id=<?php echo $clientId; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&client_id=<?php echo $clientId; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="kendaraanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah Kendaraan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="kendaraanForm" onsubmit="return saveKendaraan(event)">
                <div class="modal-body">
                    <input type="hidden" id="vehicle_id" name="vehicle_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pemilik (Client) <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">-- Pilih Pemilik --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Polisi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_polisi" name="no_polisi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="merk" name="merk" required placeholder="Honda, Yamaha, dll">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tipe" name="tipe" required placeholder="Vario, NMAX, dll">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kapasitas Mesin (CC) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cc" name="cc" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipe Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipe_kendaraan" name="tipe_kendaraan" required>
                                <option value="Roda 2">Roda 2</option>
                                <option value="Lebih dari Roda 2">Lebih dari Roda 2</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tahun" name="tahun" required min="1990" max="<?php echo date('Y')+1; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Rangka</label>
                            <input type="text" class="form-control" id="no_rangka" name="no_rangka">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Mesin</label>
                            <input type="text" class="form-control" id="no_mesin" name="no_mesin">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">
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
                <h6 class="fw-bold mb-1">Hapus Kendaraan?</h6>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Kendaraan dengan no. polisi <strong id="deleteName"></strong> akan dihapus.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="btnConfirmDelete" onclick="confirmDelete()">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var kendaraanModal, deleteModal;
var deleteId = null;

document.addEventListener('DOMContentLoaded', function() {
    kendaraanModal = new bootstrap.Modal(document.getElementById('kendaraanModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    loadClients();
});

function loadClients() {
    fetch('/bms/api/kendaraan_action.php?action=get_clients')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var select = document.getElementById('client_id');
                data.data.forEach(function(c) {
                    var opt = document.createElement('option');
                    opt.value = c.client_id;
                    opt.textContent = c.nama;
                    select.appendChild(opt);
                });
            }
        });
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Kendaraan';
    document.getElementById('kendaraanForm').reset();
    document.getElementById('vehicle_id').value = '';
    kendaraanModal.show();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Kendaraan';
    fetch('/bms/api/kendaraan_action.php?action=get&id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var d = data.data;
                document.getElementById('vehicle_id').value = d.vehicle_id;
                document.getElementById('client_id').value = d.client_id;
                document.getElementById('no_polisi').value = d.no_polisi;
                document.getElementById('merk').value = d.merk;
                document.getElementById('tipe').value = d.tipe;
                document.getElementById('cc').value = d.cc;
                document.getElementById('tipe_kendaraan').value = d.tipe_kendaraan;
                document.getElementById('tahun').value = d.tahun;
                document.getElementById('no_rangka').value = d.no_rangka;
                document.getElementById('no_mesin').value = d.no_mesin;
                kendaraanModal.show();
            }
        });
}

function saveKendaraan(e) {
    e.preventDefault();
    var form = document.getElementById('kendaraanForm');
    var formData = new FormData(form);
    formData.append('action', document.getElementById('vehicle_id').value ? 'update' : 'add');

    fetch('/bms/api/kendaraan_action.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            kendaraanModal.hide();
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    });
    return false;
}

function deleteKendaraan(id, name) {
    deleteId = id;
    document.getElementById('deleteName').textContent = name;
    deleteModal.show();
}

function confirmDelete() {
    fetch('/bms/api/kendaraan_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + deleteId
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            deleteModal.hide();
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menghapus');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
