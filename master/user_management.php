<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin']);

$result = mysqli_query($conn, "SELECT user_id, username, role, nama_lengkap FROM users ORDER BY user_id");
?>

<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>User Management</h4>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah User
    </button>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td>
                            <?php
                            $roleBadge = ['admin' => 'bg-danger', 'kasir' => 'bg-primary', 'manager' => 'bg-success'];
                            $badge = $roleBadge[$row['role']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $badge; ?>"><?php echo ucfirst($row['role']); ?></span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(<?php echo $row['user_id']; ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="resetPassword(<?php echo $row['user_id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">
                                <i class="bi bi-key"></i>
                            </button>
                            <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $row['user_id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" onsubmit="return saveUser(event)">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">
                    <input type="hidden" id="form_action" name="action" value="add">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-12" id="passwordField">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" minlength="4">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                                <option value="manager">Manager</option>
                            </select>
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
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('modalTitle').textContent = 'Tambah User';
    document.getElementById('passwordField').style.display = '';
    document.getElementById('password').required = true;
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function openEditModal(id) {
    fetch('/bms/api/user_action.php?action=get&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                var u = data.data;
                document.getElementById('user_id').value = u.user_id;
                document.getElementById('username').value = u.username;
                document.getElementById('nama_lengkap').value = u.nama_lengkap;
                document.getElementById('role').value = u.role;
                document.getElementById('form_action').value = 'update';
                document.getElementById('modalTitle').textContent = 'Edit User';
                document.getElementById('passwordField').style.display = 'none';
                document.getElementById('password').required = false;
                new bootstrap.Modal(document.getElementById('userModal')).show();
            }
        });
}

function saveUser(e) {
    e.preventDefault();
    var formData = new FormData(document.getElementById('userForm'));
    fetch('/bms/api/user_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message); }
        });
    return false;
}

function resetPassword(id, username) {
    var newPass = prompt('Masukkan password baru untuk "' + username + '":');
    if (!newPass) return;
    var formData = new FormData();
    formData.append('action', 'reset_password');
    formData.append('user_id', id);
    formData.append('new_password', newPass);
    fetch('/bms/api/user_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { alert(data.message); });
}

function deleteUser(id, username) {
    if (!confirm('Yakin hapus user "' + username + '"?')) return;
    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    fetch('/bms/api/user_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message); }
        });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
