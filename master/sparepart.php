<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir', 'manager']);
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
        <div class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width:320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari kode, nama sparepart..." autocomplete="off">
            </div>
            <select class="form-select" id="supplierFilter" style="max-width:200px;">
                <option value="0">Semua Supplier</option>
            </select>
            <select class="form-select" id="stockFilter" style="max-width:180px;">
                <option value="">Semua Stok</option>
                <option value="habis">Stok Habis</option>
                <option value="menipis">Stok Menipis</option>
                <option value="aman">Stok Aman</option>
            </select>
            <button type="button" class="btn btn-primary btn-sm" onclick="loadSpareparts(1)">Filter</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">Reset</button>
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
                <tbody id="sparepartTableBody">
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
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
var currentPage = 1;
var currentSearch = '';
var currentSupplierId = 0;
var currentStockFilter = '';

document.addEventListener('DOMContentLoaded', function() {
    sparepartModal = new bootstrap.Modal(document.getElementById('sparepartModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    loadSuppliers();
    loadSpareparts(1);
    
    // Enter key di search input
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loadSpareparts(1);
        }
    });
});

function loadSuppliers() {
    fetch('<?= BASE_URL ?>/api/sparepart_action.php?action=get_suppliers')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var select = document.getElementById('supplierFilter');
                var modalSelect = document.getElementById('supplier_id');
                
                // Clear existing options except first
                while (select.options.length > 1) select.remove(1);
                while (modalSelect.options.length > 1) modalSelect.remove(1);
                
                data.data.forEach(function(s) {
                    var opt1 = document.createElement('option');
                    opt1.value = s.supplier_id;
                    opt1.textContent = s.nama;
                    select.appendChild(opt1);
                    
                    var opt2 = document.createElement('option');
                    opt2.value = s.supplier_id;
                    opt2.textContent = s.nama;
                    modalSelect.appendChild(opt2);
                });
            }
        });
}

function loadSpareparts(page) {
    page = page || 1;
    currentPage = page;
    currentSearch = document.getElementById('searchInput').value;
    currentSupplierId = parseInt(document.getElementById('supplierFilter').value);
    currentStockFilter = document.getElementById('stockFilter').value;
    
    var url = '<?= BASE_URL ?>/api/sparepart_action.php?action=list&page=' + page + '&limit=10';
    if (currentSearch) {
        url += '&search=' + encodeURIComponent(currentSearch);
    }
    if (currentSupplierId > 0) {
        url += '&supplier_id=' + currentSupplierId;
    }
    if (currentStockFilter) {
        url += '&stock_filter=' + encodeURIComponent(currentStockFilter);
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
    var tbody = document.getElementById('sparepartTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted"><i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada data sparepart</td></tr>';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    var html = '';
    var no = (pagination.page - 1) * pagination.limit + 1;
    
    data.forEach(function(row) {
        var stok = row.stok;
        var stokMin = row.stok_minimum || 5;
        var stokBadge, stokDisplay;
        
        if (stok <= 0) {
            stokBadge = 'bg-danger';
            stokDisplay = stok + ' ' + row.satuan;
        } else if (stok <= stokMin) {
            stokBadge = 'bg-warning text-dark';
            stokDisplay = stok + ' ' + row.satuan;
        } else {
            stokBadge = 'bg-success';
            stokDisplay = stok + ' ' + row.satuan;
        }
        
        var margin = parseFloat(row.harga_jual) - parseFloat(row.harga_beli);
        var marginText = margin > 0 ? '<span class="text-success fw-semibold">+' + formatRupiahInline(margin) + '</span>' : 
                         '<span class="text-danger fw-semibold">' + formatRupiahInline(margin) + '</span>';
        
        html += '<tr>';
        html += '<td>' + (no++) + '</td>';
        html += '<td><code class="fw-bold">' + htmlEscape(row.kode_sparepart) + '</code></td>';
        html += '<td class="fw-semibold">' + htmlEscape(row.nama_sparepart) + '</td>';
        html += '<td>' + htmlEscape(row.satuan) + '</td>';
        html += '<td><span class="badge ' + stokBadge + '">' + stokDisplay + '</span></td>';
        html += '<td>' + formatRupiahInline(row.harga_beli) + '</td>';
        html += '<td>' + formatRupiahInline(row.harga_jual) + '</td>';
        html += '<td>' + marginText + '</td>';
        html += '<td><small>' + htmlEscape(row.nama_supplier || '-') + '</small></td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(' + row.sparepart_id + ')"><i class="bi bi-pencil"></i></button>';
        html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteSparepart(' + row.sparepart_id + ', \'' + htmlEscape(row.kode_sparepart) + '\')"><i class="bi bi-trash"></i></button>';
        html += '</td>';
        html += '</tr>';
    });
    
    tbody.innerHTML = html;
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
    
    if (pagination.page > 1) {
        html += '<li class="page-item"><a class="page-link" href="javascript:loadSpareparts(' + (pagination.page - 1) + ')">Prev</a></li>';
    }
    
    var start = Math.max(1, pagination.page - 2);
    var end = Math.min(pagination.total_page, pagination.page + 2);
    
    for (var i = start; i <= end; i++) {
        var active = (i === pagination.page) ? 'active' : '';
        html += '<li class="page-item ' + active + '"><a class="page-link" href="javascript:loadSpareparts(' + i + ')">' + i + '</a></li>';
    }
    
    if (pagination.page < pagination.total_page) {
        html += '<li class="page-item"><a class="page-link" href="javascript:loadSpareparts(' + (pagination.page + 1) + ')">Next</a></li>';
    }
    
    paginationList.innerHTML = html;
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('supplierFilter').value = '0';
    document.getElementById('stockFilter').value = '';
    loadSpareparts(1);
}

function htmlEscape(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatRupiahInline(num) {
    if (!num) return 'Rp 0';
    return 'Rp ' + parseInt(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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
            } else {
                BMS.error(data.message || 'Gagal memuat data');
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
            BMS.success(data.message || 'Sparepart berhasil disimpan');
            loadSpareparts(1);
        } else {
            BMS.error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(function(err) {
        BMS.error('Error: ' + err.message);
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
            BMS.success(data.message || 'Sparepart berhasil dihapus');
            loadSpareparts(currentPage);
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
