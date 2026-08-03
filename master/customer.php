<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);
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
        <div class="d-flex gap-2">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, telepon, email..." autocomplete="off">
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="loadCustomers(1)">Cari</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSearch()">Reset</button>
        </div>
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
                <tbody id="customerTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-hourglass-split"></i> Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="paginationContainer" class="card-footer bg-white d-flex justify-content-between align-items-center" style="display:none;">
        <small class="text-muted">Total <span id="totalDataCount">0</span> data</small>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="paginationList">
            </ul>
        </nav>
    </div>
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
var currentPage = 1;
var currentSearch = '';

document.addEventListener('DOMContentLoaded', function() {
    customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Load data awal
    loadCustomers(1);
    
    // Enter key di search input
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loadCustomers(1);
        }
    });
});

function loadCustomers(page) {
    page = page || 1;
    currentPage = page;
    currentSearch = document.getElementById('searchInput').value;
    
    var url = '<?= BASE_URL ?>/api/customer_action.php?action=list&page=' + page + '&limit=10';
    if (currentSearch) {
        url += '&search=' + encodeURIComponent(currentSearch);
    }
    
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(response) {
            if (response.success) {
                renderTable(response.data, response.pagination);
            } else {
                BMS.error(response.message || 'Gagal memuat data');
            }
        })
        .catch(function(err) {
            BMS.error('Error: ' + err.message);
        });
}

function renderTable(data, pagination) {
    var tbody = document.getElementById('customerTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada data customer</td></tr>';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    var html = '';
    var no = (pagination.page - 1) * pagination.limit + 1;
    
    data.forEach(function(row) {
        var tglDaftar = formatDateInline(row.tanggal_daftar);
        html += '<tr>';
        html += '<td>' + (no++) + '</td>';
        html += '<td class="fw-semibold">' + htmlEscape(row.nama) + '</td>';
        html += '<td>' + htmlEscape(row.telepon) + '</td>';
        html += '<td>' + htmlEscape(row.email) + '</td>';
        html += '<td><small class="text-muted">' + htmlEscape(row.alamat) + '</small></td>';
        html += '<td>' + tglDaftar + '</td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(' + row.client_id + ')"><i class="bi bi-pencil"></i></button>';
        html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(' + row.client_id + ', \'' + htmlEscape(row.nama) + '\')"><i class="bi bi-trash"></i></button>';
        html += '</td>';
        html += '</tr>';
    });
    
    tbody.innerHTML = html;
    
    // Render pagination
    renderPagination(pagination);
}

function renderPagination(pagination) {
    var container = document.getElementById('paginationContainer');
    var paginationList = document.getElementById('paginationList');
    
    if (pagination.total_page <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    document.getElementById('totalDataCount').textContent = pagination.total_data;
    
    var html = '';
    
    // Prev button
    if (pagination.page > 1) {
        html += '<li class="page-item"><a class="page-link" href="javascript:loadCustomers(' + (pagination.page - 1) + ')">Prev</a></li>';
    }
    
    // Page numbers
    var start = Math.max(1, pagination.page - 2);
    var end = Math.min(pagination.total_page, pagination.page + 2);
    
    for (var i = start; i <= end; i++) {
        var active = (i === pagination.page) ? 'active' : '';
        html += '<li class="page-item ' + active + '"><a class="page-link" href="javascript:loadCustomers(' + i + ')">' + i + '</a></li>';
    }
    
    // Next button
    if (pagination.page < pagination.total_page) {
        html += '<li class="page-item"><a class="page-link" href="javascript:loadCustomers(' + (pagination.page + 1) + ')">Next</a></li>';
    }
    
    paginationList.innerHTML = html;
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    loadCustomers(1);
}

function htmlEscape(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDateInline(dateStr) {
    if (!dateStr) return '-';
    var d = new Date(dateStr + 'T00:00:00');
    var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Customer';
    document.getElementById('customerForm').reset();
    document.getElementById('client_id').value = '';
    customerModal.show();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Customer';
    fetch('<?= BASE_URL ?>/api/customer_action.php?action=get&id=' + id)
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
            } else {
                BMS.error(data.message || 'Gagal memuat data');
            }
        });
}

function saveCustomer(e) {
    e.preventDefault();
    var form = document.getElementById('customerForm');
    var formData = new FormData(form);
    formData.append('action', document.getElementById('client_id').value ? 'update' : 'add');

    fetch('<?= BASE_URL ?>/api/customer_action.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            customerModal.hide();
            BMS.success(data.message || 'Customer berhasil disimpan');
            loadCustomers(1);
        } else {
            BMS.error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(function(err) {
        BMS.error('Error: ' + err.message);
    });
    return false;
}

function deleteCustomer(id, name) {
    deleteId = id;
    document.getElementById('deleteName').textContent = name;
    deleteModal.show();
}

function confirmDelete() {
    fetch('<?= BASE_URL ?>/api/customer_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + deleteId
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            deleteModal.hide();
            BMS.success(data.message || 'Customer berhasil dihapus');
            loadCustomers(currentPage);
        } else {
            BMS.error(data.message || 'Gagal menghapus');
        }
    })
    .catch(function(err) {
        BMS.error('Error: ' + err.message);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
