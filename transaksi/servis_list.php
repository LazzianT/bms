<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
if ($filter == 'Registered')      $where .= " AND tp.status = 'Registered'";
elseif ($filter == 'InProgress')  $where .= " AND tp.status = 'InProgress'";
elseif ($filter == 'Completed')   $where .= " AND tp.status = 'Completed'";
elseif ($filter == 'Lunas')       $where .= " AND ts.status_servis = 'Selesai Lunas'";

if ($search) {
    $where .= " AND (c.nama LIKE '%$search%' OR v.no_polisi LIKE '%$search%' 
                  OR tp.keluhan LIKE '%$search%' OR m.nama LIKE '%$search%')";
}

$countSql = "FROM transaksi_pendaftaran tp
    LEFT JOIN client c ON tp.client_id = c.client_id
    LEFT JOIN vehicle v ON tp.vehicle_id = v.vehicle_id
    LEFT JOIN mekanik m ON tp.mekanik_id = m.mekanik_id
    LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id
    $where";
$countResult = mysqli_query($conn, "SELECT COUNT(*) as jml $countSql");
$totalData = mysqli_fetch_assoc($countResult)['jml'];
$totalPages = ceil($totalData / $limit);

$sql = "SELECT tp.*, c.nama as nama_client, v.no_polisi, v.merk, v.tipe, m.nama as nama_mekanik,
        ts.trans_id, ts.grand_total, ts.total_sparepart, ts.total_jasa, ts.bayar, ts.status_servis, ts.metode_bayar
    FROM transaksi_pendaftaran tp
    LEFT JOIN client c ON tp.client_id = c.client_id
    LEFT JOIN vehicle v ON tp.vehicle_id = v.vehicle_id
    LEFT JOIN mekanik m ON tp.mekanik_id = m.mekanik_id
    LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id
    $where
    ORDER BY tp.tanggal_daftar DESC
    LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

$tabs = [
    'all'        => ['label' => 'Semua',        'icon' => 'bi-list-ul'],
    'Registered' => ['label' => 'Antrean',      'icon' => 'bi-hourglass-split'],
    'InProgress' => ['label' => 'Dikerjakan',   'icon' => 'bi-gear'],
    'Completed'  => ['label' => 'Selesai',      'icon' => 'bi-check-circle'],
    'Lunas'      => ['label' => 'Lunas',        'icon' => 'bi-cash-stack'],
];
?>

<div class="page-header">
    <h4><i class="bi bi-wrench me-2"></i>Transaksi Service</h4>
    <a href="/bms/transaksi/tambah_servis.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Daftar Servis Baru
    </a>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-3 gap-1">
    <?php foreach ($tabs as $key => $tab): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo ($filter == $key) ? 'active' : ''; ?>" 
           href="?filter=<?php echo $key; ?>&search=<?php echo urlencode($search); ?>">
            <i class="<?php echo $tab['icon']; ?> me-1"></i><?php echo $tab['label']; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="Cari customer, plat, keluhan, mekanik..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <?php if ($search): ?>
                <a href="/bms/transaksi/servis_list.php?filter=<?php echo $filter; ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                        <th>No. Pendaftaran</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Kendaraan</th>
                        <th>Keluhan</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><code class="fw-bold">REG-<?php echo str_pad($row['registration_id'], 5, '0', STR_PAD_LEFT); ?></code></td>
                            <td><?php echo formatDateTime($row['tanggal_daftar']); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_client']); ?></td>
                            <td>
                                <span class="badge badge-nopol"><?php echo htmlspecialchars($row['no_polisi']); ?></span>
                                <small class="text-muted d-block"><?php echo htmlspecialchars($row['merk'] . ' ' . $row['tipe']); ?></small>
                            </td>
                            <td><small><?php echo htmlspecialchars(mb_strimwidth($row['keluhan'], 0, 40, '...')); ?></small></td>
                            <td><?php echo $row['nama_mekanik'] ? htmlspecialchars($row['nama_mekanik']) : '<span class="text-muted">-</span>'; ?></td>
                            <td>
                                <?php
                                if ($row['status_servis'] == 'Selesai Lunas') {
                                    echo setStatusBadge('Lunas');
                                } else {
                                    echo setStatusBadge($row['status']);
                                }
                                ?>
                            </td>
                            <td class="fw-semibold">
                                <?php if ($row['grand_total'] > 0): ?>
                                    <?php echo formatRupiah($row['grand_total']); ?>
                                <?php elseif ($row['total_sparepart'] > 0): ?>
                                    <small class="text-muted"><?php echo formatRupiah($row['total_sparepart']); ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Registered'): ?>
                                    <button class="btn btn-sm btn-outline-warning me-1" onclick="openTambahPart(<?php echo $row['registration_id']; ?>, <?php echo $row['trans_id']; ?>, 'Registered')" title="Tambah Part">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success me-1" onclick="openStartModal(<?php echo $row['registration_id']; ?>)" title="Mulai Kerjakan">
                                        <i class="bi bi-play-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="batalPendaftaran(<?php echo $row['registration_id']; ?>)" title="Batalkan">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                <?php elseif ($row['status'] == 'InProgress'): ?>
                                    <button class="btn btn-sm btn-outline-warning me-1" onclick="openTambahPart(<?php echo $row['registration_id']; ?>, <?php echo $row['trans_id']; ?>, 'InProgress')" title="Tambah Part">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                    <a href="/bms/transaksi/detail_servis.php?id=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="setSelesai(<?php echo $row['trans_id']; ?>)" title="Selesai">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="/bms/transaksi/detail_servis.php?id=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data transaksi servis
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
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Prev</a></li>
                <?php endif; ?>
                <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ==================== MODAL TAMBAH PART (MULTI) ==================== -->
<div class="modal fade" id="sparepartModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold" id="spModalTitle">Tambah Sparepart</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Existing sparepart (for InProgress) -->
                <div id="existingSpSection" class="d-none mb-3">
                    <h6 class="fw-bold text-muted small mb-2"><i class="bi bi-box me-1"></i> Sparepart Sudah Ditambahkan</h6>
                    <div id="existingSpList"></div>
                    <hr>
                </div>

                <div class="row g-3">
                    <!-- Kiri: Tabel Cari Sparepart -->
                    <div class="col-md-7">
                        <h6 class="fw-bold small mb-2"><i class="bi bi-search me-1"></i> Cari Sparepart</h6>
                        <input type="text" class="form-control form-control-sm mb-2" id="cariSp" placeholder="Ketik nama atau kode sparepart..." oninput="searchSpTable()">
                        <div style="max-height:280px;overflow-y:auto;">
                            <table class="table table-sm table-hover mb-0" id="spSearchTable">
                                <thead class="table-light" style="position:sticky;top:0;">
                                    <tr>
                                        <th class="small">Kode</th>
                                        <th class="small">Nama</th>
                                        <th class="small text-center">Stok</th>
                                        <th class="small text-end">Harga</th>
                                        <th class="small" width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="spSearchBody">
                                    <tr><td colspan="5" class="text-center text-muted py-3 small">Ketik untuk mencari sparepart...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Kanan: List yang akan ditambahkan -->
                    <div class="col-md-5">
                        <h6 class="fw-bold small mb-2"><i class="bi bi-cart-plus me-1"></i> Akan Ditambahkan</h6>
                        <div id="spSelectedList" style="max-height:320px;overflow-y:auto;">
                            <p class="text-muted small text-center py-3" id="spEmptyMsg">Klik sparepart di tabel kiri untuk menambahkan</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                <div class="me-auto">
                    <strong>Subtotal: </strong> <span id="spSubtotal" class="text-primary fw-bold">Rp 0</span>
                </div>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanSparepartBatch()" id="btnSimpanSp" disabled>
                    <i class="bi bi-check-lg me-1"></i> Simpan Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL PILIH MEKANIK ==================== -->
<div class="modal fade" id="mekanikModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold">Pilih Mekanik</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Pilih mekanik yang sedang <strong>available</strong> (tidak sedang mengerjakan servis lain):</p>
                <div id="mekanikList" style="max-height:300px;overflow-y:auto;"></div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnStart" onclick="confirmStart()" disabled>
                    <i class="bi bi-play-fill me-1"></i> Mulai Kerjakan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var currentRegId = 0;
var currentTransId = 0;
var currentStatus = '';
var selectedMekanikId = 0;
var selectedSpItems = [];

// ==================== MULTIPLE SPAREPART ====================

function openTambahPart(regId, transId, status) {
    currentRegId = regId;
    currentTransId = transId;
    currentStatus = status;
    selectedSpItems = [];

    document.getElementById('cariSp').value = '';
    document.getElementById('spSelectedList').innerHTML = '<p class="text-muted small text-center py-3" id="spEmptyMsg">Klik sparepart di tabel kiri untuk menambahkan</p>';
    document.getElementById('spSubtotal').textContent = 'Rp 0';
    document.getElementById('btnSimpanSp').disabled = true;
    document.getElementById('spModalTitle').textContent = 'Tambah Sparepart - REG-' + String(regId).padStart(5, '0');

    // Show existing sparepart if InProgress
    var existingSection = document.getElementById('existingSpSection');
    if (status == 'InProgress' && transId) {
        existingSection.classList.remove('d-none');
        loadExistingSparepart(transId);
    } else {
        existingSection.classList.add('d-none');
    }

    new bootstrap.Modal(document.getElementById('sparepartModal')).show();
    loadAllSparepart();
}

function loadExistingSparepart(transId) {
    fetch('/bms/api/servis_action.php?action=get_sparepart_detail&trans_id=' + transId)
        .then(r => r.json())
        .then(data => {
            var html = '';
            if (data.data.length == 0) {
                html = '<p class="text-muted small mb-0">Belum ada sparepart</p>';
            } else {
                html = '<table class="table table-sm table-borderless mb-0">';
                html += '<thead><tr><th class="small">Kode</th><th class="small">Nama</th><th class="small text-center">Qty</th><th class="small text-end">Subtotal</th></tr></thead><tbody>';
                var total = 0;
                data.data.forEach(function(d) {
                    total += parseFloat(d.subtotal);
                    html += '<tr>';
                    html += '<td class="small"><code>' + d.kode_sparepart + '</code></td>';
                    html += '<td class="small">' + d.nama_sparepart + '</td>';
                    html += '<td class="small text-center">' + d.qty + ' ' + d.satuan + '</td>';
                    html += '<td class="small text-end fw-semibold">' + formatRupiahJS(d.subtotal) + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                html += '<div class="text-end fw-bold small">Total: ' + formatRupiahJS(total) + '</div>';
            }
            document.getElementById('existingSpList').innerHTML = html;
        });
}

function loadAllSparepart() {
    fetch('/bms/api/servis_action.php?action=get_sparepart&q=')
        .then(r => r.json())
        .then(data => {
            allSpData = data.data;
            renderSpTable(allSpData);
        });
}

var allSpData = [];

function renderSpTable(items) {
    // Filter out items already selected
    var selectedIds = selectedSpItems.map(function(i) { return i.id; });
    var filtered = items.filter(function(s) { return selectedIds.indexOf(s.id) === -1; });

    if (filtered.length == 0) {
        var msg = selectedSpItems.length > 0 ? 'Semua sparepart sudah ditambahkan' : 'Tidak ada sparepart tersedia';
        document.getElementById('spSearchBody').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3 small">' + msg + '</td></tr>';
        return;
    }
    var html = '';
    filtered.forEach(function(s) {
        var stokClass = s.stok <= 5 ? 'text-danger fw-bold' : (s.stok <= 10 ? 'text-warning fw-bold' : '');
        html += '<tr style="cursor:pointer;" onclick="addToSelected(' + s.sparepart_id + ',\'' + s.kode_sparepart.replace(/'/g,"\\'") + '\',\'' + s.nama_sparepart.replace(/'/g,"\\'") + '\',' + s.harga_jual + ',' + s.stok + ',\'' + s.satuan + '\')">';
        html += '<td class="small"><code>' + s.kode_sparepart + '</code></td>';
        html += '<td class="small fw-semibold">' + s.nama_sparepart + '</td>';
        html += '<td class="small text-center ' + stokClass + '">' + s.stok + ' ' + s.satuan + '</td>';
        html += '<td class="small text-end">' + formatRupiahJS(s.harga_jual) + '</td>';
        html += '<td class="small text-center"><i class="bi bi-plus-circle text-primary"></i></td>';
        html += '</tr>';
    });
    document.getElementById('spSearchBody').innerHTML = html;
}

function searchSpTable() {
    var q = document.getElementById('cariSp').value.toLowerCase();
    if (q.length < 1) {
        renderSpTable(allSpData);
        return;
    }
    var filtered = allSpData.filter(function(s) {
        return s.nama_sparepart.toLowerCase().includes(q) || s.kode_sparepart.toLowerCase().includes(q);
    });
    renderSpTable(filtered);
}

function addToSelected(id, kode, nama, harga, stok, satuan) {
    // Check if already added
    for (var i = 0; i < selectedSpItems.length; i++) {
        if (selectedSpItems[i].id == id) {
            // Increase qty by 1
            selectedSpItems[i].qty++;
            renderSelectedList();
            return;
        }
    }
    // Add new
    selectedSpItems.push({ id: id, kode: kode, nama: nama, harga: harga, stok: stok, satuan: satuan, qty: 1 });
    renderSelectedList();
}

function renderSelectedList() {
    var html = '';
    if (selectedSpItems.length == 0) {
        html = '<p class="text-muted small text-center py-3">Klik sparepart di tabel kiri untuk menambahkan</p>';
    } else {
        selectedSpItems.forEach(function(item, idx) {
            var maxQty = item.stok;
            var subtotal = item.qty * item.harga;
            html += '<div class="d-flex align-items-center gap-2 p-2 border rounded mb-2">';
            html += '  <div class="flex-grow-1">';
            html += '    <div class="small fw-semibold">' + item.nama + '</div>';
            html += '    <div class="text-muted" style="font-size:0.75rem;">' + item.kode + ' | ' + formatRupiahJS(item.harga) + ' / ' + item.satuan + '</div>';
            html += '  </div>';
            html += '  <div style="width:70px;">';
            html += '    <input type="number" class="form-control form-control-sm text-center" value="' + item.qty + '" min="1" max="' + maxQty + '" onchange="updateQty(' + idx + ', this.value)" oninput="updateQty(' + idx + ', this.value)">';
            html += '  </div>';
            html += '  <div class="text-end" style="width:100px;">';
            html += '    <div class="small fw-bold">' + formatRupiahJS(subtotal) + '</div>';
            if (item.qty >= item.stok) {
                html += '    <div class="text-danger" style="font-size:0.65rem;">Stok max</div>';
            }
            html += '  </div>';
            html += '  <button class="btn btn-sm btn-outline-danger" onclick="removeSelected(' + idx + ')" style="padding:2px 6px;"><i class="bi bi-x"></i></button>';
            html += '</div>';
        });
    }
    document.getElementById('spSelectedList').innerHTML = html;

    // Recalc total
    var total = 0;
    selectedSpItems.forEach(function(item) { total += item.qty * item.harga; });
    document.getElementById('spSubtotal').textContent = formatRupiahJS(total);
    document.getElementById('btnSimpanSp').disabled = selectedSpItems.length == 0;

    // Refresh search table to hide/show items
    var q = document.getElementById('cariSp').value.toLowerCase();
    if (q.length > 0) {
        var filtered = allSpData.filter(function(s) {
            return s.nama_sparepart.toLowerCase().includes(q) || s.kode_sparepart.toLowerCase().includes(q);
        });
        renderSpTable(filtered);
    } else {
        renderSpTable(allSpData);
    }
}

function updateQty(idx, val) {
    var qty = parseInt(val) || 1;
    if (qty < 1) qty = 1;
    if (qty > selectedSpItems[idx].stok) qty = selectedSpItems[idx].stok;
    selectedSpItems[idx].qty = qty;
    renderSelectedList();
}

function removeSelected(idx) {
    selectedSpItems.splice(idx, 1);
    renderSelectedList();
}

function simpanSparepartBatch() {
    if (selectedSpItems.length == 0) {
        alert('Pilih minimal 1 sparepart');
        return;
    }

    var items = selectedSpItems.map(function(item) {
        return { sparepart_id: item.id, qty: item.qty };
    });

    var formData = new FormData();
    formData.append('action', 'add_sparepart_batch');
    formData.append('trans_id', currentTransId);
    formData.append('items', JSON.stringify(items));

    fetch('/bms/api/servis_action.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('sparepartModal')).hide();
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}

function formatRupiahJS(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

// ==================== PILIH MEKANIK & START ====================

function openStartModal(regId) {
    currentRegId = regId;
    selectedMekanikId = 0;
    document.getElementById('btnStart').disabled = true;
    document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Memuat...</div>';
    new bootstrap.Modal(document.getElementById('mekanikModal')).show();

    fetch('/bms/api/servis_action.php?action=get_available_mekanik')
        .then(r => r.json())
        .then(data => {
            if (data.data.length == 0) {
                document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Tidak ada mekanik available</div>';
                return;
            }
            var html = '';
            data.data.forEach(function(m) {
                html += '<div class="p-3 border rounded mb-2 mekanik-card" style="cursor:pointer" onclick="selectMekanik(this, ' + m.mekanik_id + ')">';
                html += '<div class="fw-semibold">' + m.nama + '</div>';
                html += '<small class="text-muted">' + m.spesialis + '</small>';
                html += '</div>';
            });
            document.getElementById('mekanikList').innerHTML = html;
        });
}

function selectMekanik(el, id) {
    document.querySelectorAll('.mekanik-card').forEach(function(c) {
        c.classList.remove('border-primary');
        c.style.background = '';
    });
    el.classList.add('border-primary');
    el.style.background = '#eff6ff';
    selectedMekanikId = id;
    document.getElementById('btnStart').disabled = false;
}

function confirmStart() {
    if (!selectedMekanikId) return;

    fetch('/bms/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=start&registration_id=' + currentRegId + '&mekanik_id=' + selectedMekanikId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('mekanikModal')).hide();
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}

// ==================== LAINNYA ====================

function batalPendaftaran(regId) {
    if (!confirm('Batalkan pendaftaran ini? Semua sparepart yang ditambahkan juga akan dihapus.')) return;
    fetch('/bms/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=cancel_registration&registration_id=' + regId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}

function setSelesai(transId) {
    if (!confirm('Tandai servis ini sebagai Selesai?')) return;
    fetch('/bms/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=set_selesai&trans_id=' + transId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
