<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = '';
if ($search) {
    $where = "WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%' OR email LIKE '%$search%'";
}

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM client $where");
$totalData  = mysqli_fetch_assoc($totalQuery)['jml'];
$totalPages = ceil($totalData / $limit);

$query  = "SELECT * FROM client $where ORDER BY client_id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>Master Customer</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Customer
    </button>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari nama, telepon, email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <?php if ($search): ?>
                <a href="/bms/master/customer.php" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Tanggal Daftar</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['telepon']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($row['alamat']); ?></small></td>
                            <td><?php echo formatDate($row['tanggal_daftar']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?php echo $row['client_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(<?php echo $row['client_id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data customer
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
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah Customer</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customerForm" onsubmit="return saveCustomer(event)">
                <div class="modal-body">
                    <input type="hidden" id="client_id" name="client_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="telepon" name="telepon" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
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
                <h6 class="fw-bold mb-1">Hapus Customer?</h6>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Data kendaraan milik <strong id="deleteName"></strong> juga akan terhapus.
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
var customerModal, deleteModal;
var deleteId = null;

document.addEventListener('DOMContentLoaded', function() {
    customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Customer';
    document.getElementById('customerForm').reset();
    document.getElementById('client_id').value = '';
    customerModal.show();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Customer';
    fetch('/bms/api/customer_action.php?action=get&id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var d = data.data;
                document.getElementById('client_id').value = d.client_id;
                document.getElementById('nama').value = d.nama;
                document.getElementById('telepon').value = d.telepon;
                document.getElementById('email').value = d.email;
                document.getElementById('alamat').value = d.alamat;
                customerModal.show();
            }
        });
}

function saveCustomer(e) {
    e.preventDefault();
    var form = document.getElementById('customerForm');
    var formData = new FormData(form);
    formData.append('action', document.getElementById('client_id').value ? 'update' : 'add');

    fetch('/bms/api/customer_action.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            customerModal.hide();
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    });
    return false;
}

function deleteCustomer(id, name) {
    deleteId = id;
    document.getElementById('deleteName').textContent = name;
    deleteModal.show();
}

function confirmDelete() {
    fetch('/bms/api/customer_action.php', {
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
