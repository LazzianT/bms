<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$reg_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$reg_id) {
    header('Location: ' . BASE_URL . '/transaksi/servis_list.php');
    exit();
}

$reg = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT tp.*, c.nama as nama_client, c.telepon as telp_client, v.no_polisi, v.merk, v.tipe, v.cc,
           m.nama as nama_mekanik, m.spesialis,
           ts.trans_id, ts.status_servis, ts.total_jasa, ts.total_sparepart, ts.grand_total,
           ts.bayar, ts.kembali, ts.metode_bayar, ts.user_kasir
    FROM transaksi_pendaftaran tp
    LEFT JOIN client c ON tp.client_id = c.client_id
    LEFT JOIN vehicle v ON tp.vehicle_id = v.vehicle_id
    LEFT JOIN mekanik m ON tp.mekanik_id = m.mekanik_id
    LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id
    WHERE tp.registration_id = $reg_id
"));
if (!$reg) {
    header('Location: ' . BASE_URL . '/transaksi/servis_list.php');
    exit();
}

$ts_id = $reg['trans_id'];

$details_sp = [];
$details_jasa = [];
if ($ts_id) {
    $details_sp = mysqli_query($conn, "SELECT d.*, s.kode_sparepart, s.nama_sparepart, s.satuan
        FROM transaksi_servis_detail d
        LEFT JOIN sparepart s ON d.sparepart_id = s.sparepart_id
        WHERE d.trans_id = $ts_id ORDER BY d.detail_id");
    $details_jasa = mysqli_query($conn, "SELECT * FROM transaksi_servis_jasa WHERE trans_id = $ts_id ORDER BY detail_id");
}

$canAddPart = in_array($reg['status'], ['Registered', 'InProgress']) && $ts_id;
$canStart   = $reg['status'] == 'Registered';
$canSelesai = $reg['status'] == 'InProgress' && $reg['status_servis'] == 'Dikerjakan';
$canBayar   = $reg['status_servis'] == 'Selesai';
$isLunas    = $reg['status_servis'] == 'Selesai Lunas';
?>

<div class="page-header">
    <h4><i class="bi bi-wrench me-2"></i>Detail Servis</h4>
    <div class="d-flex gap-2">
        <?php if ($canStart): ?>
            <button class="btn btn-success" onclick="openStartModal()">
                <i class="bi bi-play-fill me-1"></i> Mulai Kerjakan
            </button>
        <?php endif; ?>
        <?php if ($canSelesai): ?>
            <button class="btn btn-success" onclick="setSelesai()">
                <i class="bi bi-check-lg me-1"></i> Selesai
            </button>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/transaksi/servis_list.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Panel -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Informasi Pendaftaran</h6></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted" style="width:120px">No. Pendaftaran</td><td class="fw-semibold">REG-<?php echo str_pad($reg_id, 5, '0', STR_PAD_LEFT); ?></td></tr>
                    <tr><td class="text-muted">Tanggal</td><td><?php echo formatDateTime($reg['tanggal_daftar']); ?></td></tr>
                    <tr><td class="text-muted">Status</td><td><?php echo setStatusBadge($isLunas ? 'Lunas' : $reg['status']); ?></td></tr>
                    <?php if ($reg['status_servis']): ?>
                    <tr><td class="text-muted">Status Servis</td><td><?php echo setStatusBadge($reg['status_servis']); ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Customer & Kendaraan</h6></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted" style="width:120px">Customer</td><td class="fw-semibold"><?php echo htmlspecialchars($reg['nama_client']); ?></td></tr>
                    <tr><td class="text-muted">Telepon</td><td><?php echo htmlspecialchars($reg['telp_client']); ?></td></tr>
                    <tr><td class="text-muted">Kendaraan</td><td><span class="badge badge-nopol"><?php echo htmlspecialchars($reg['no_polisi']); ?></span></td></tr>
                    <tr><td class="text-muted">Tipe</td><td><?php echo htmlspecialchars($reg['merk'] . ' ' . $reg['tipe'] . ' ' . $reg['cc'] . 'cc'); ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Mekanik</h6></div>
            <div class="card-body">
                <?php if ($reg['nama_mekanik']): ?>
                    <span class="fw-semibold"><?php echo htmlspecialchars($reg['nama_mekanik']); ?></span>
                    <small class="text-muted d-block"><?php echo htmlspecialchars($reg['spesialis']); ?></small>
                <?php else: ?>
                    <span class="text-muted">Belum ditentukan</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Keluhan</h6></div>
            <div class="card-body">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($reg['keluhan'])); ?></p>
                <?php if ($reg['catatan']): ?>
                    <hr class="my-2">
                    <small class="text-muted"><strong>Catatan:</strong> <?php echo htmlspecialchars($reg['catatan']); ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Sparepart -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-box me-1"></i> Sparepart</h6>
                <?php if ($canAddPart): ?>
                <button class="btn btn-sm btn-primary" onclick="openTambahPart()">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Sparepart</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <?php if ($canAddPart): ?><th width="50"></th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ts_id && mysqli_num_rows($details_sp) > 0): ?>
                            <?php while ($d = mysqli_fetch_assoc($details_sp)): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($d['kode_sparepart']); ?></code></td>
                                <td><?php echo htmlspecialchars($d['nama_sparepart']); ?></td>
                                <td><?php echo formatRupiah($d['harga']); ?></td>
                                <td><?php echo $d['qty']; ?> <?php echo htmlspecialchars($d['satuan']); ?></td>
                                <td class="fw-semibold"><?php echo formatRupiah($d['subtotal']); ?></td>
                                <?php if ($canAddPart): ?>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusSparepart(<?php echo $d['detail_id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="<?php echo $canAddPart ? 6 : 5; ?>" class="text-center text-muted py-3">Belum ada sparepart</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jasa -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-1"></i> Jasa</h6>
                <?php if ($canAddPart): ?>
                <button class="btn btn-sm btn-primary" onclick="openAddJasa()">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Jasa</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <?php if ($canAddPart): ?><th width="50"></th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ts_id && mysqli_num_rows($details_jasa) > 0): ?>
                            <?php while ($j = mysqli_fetch_assoc($details_jasa)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($j['nama_jasa']); ?></td>
                                <td><?php echo formatRupiah($j['harga']); ?></td>
                                <td><?php echo $j['qty']; ?></td>
                                <td class="fw-semibold"><?php echo formatRupiah($j['subtotal']); ?></td>
                                <?php if ($canAddPart): ?>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusJasa(<?php echo $j['detail_id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="<?php echo $canAddPart ? 5 : 4; ?>" class="text-center text-muted py-3">Belum ada jasa</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ringkasan & Pembayaran -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Ringkasan</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted">Total Jasa</td><td class="text-end"><?php echo formatRupiah($reg['total_jasa']); ?></td></tr>
                            <tr><td class="text-muted">Total Sparepart</td><td class="text-end"><?php echo formatRupiah($reg['total_sparepart']); ?></td></tr>
                            <tr style="border-top:2px solid #e2e8f0;">
                                <td class="fw-bold fs-6">Grand Total</td>
                                <td class="text-end fw-bold fs-6 text-primary"><?php echo formatRupiah($reg['grand_total']); ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php if ($canBayar): ?>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Pembayaran</h6>
                        <form onsubmit="return prosesBayar(event)">
                            <div class="mb-2">
                                <label class="form-label small">Metode Bayar</label>
                                <select class="form-select form-select-sm" id="metodeBayar" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer">Transfer</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Bayar</label>
                                <input type="number" class="form-control form-control-sm" id="inputBayar" min="<?php echo $reg['grand_total']; ?>" required oninput="hitungKembali()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Kembalian</label>
                                <input type="text" class="form-control form-control-sm" id="kembali" readonly value="Rp 0">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cash-stack me-1"></i> Proses Pembayaran
                            </button>
                        </form>
                    </div>
                    <?php elseif ($isLunas): ?>
                    <div class="col-md-6">
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-1"></i> <strong>Sudah Lunas</strong><br>
                            <small>Dibayar: <?php echo formatRupiah($reg['bayar']); ?> | Kembalian: <?php echo formatRupiah($reg['kembali']); ?></small><br>
                            <small>Metode: <?php echo $reg['metode_bayar']; ?> | Oleh: <?php echo $reg['user_kasir']; ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Sparepart -->
<div class="modal fade" id="sparepartModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold">Tambah Sparepart</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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

<!-- Modal Tambah Jasa -->
<div class="modal fade" id="jasaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold">Tambah Jasa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Jasa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="jasaNamaInput" placeholder="Contoh: Ganti Oli, Tune Up, dll">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jasaHargaInput" min="0" placeholder="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" id="jasaQty" value="1" min="1">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="addJasa()"><i class="bi bi-check-lg me-1"></i> Tambah</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Mekanik -->
<div class="modal fade" id="mekanikModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;">
                <h6 class="modal-title fw-bold">Pilih Mekanik</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Pilih mekanik yang <strong>available</strong>:</p>
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
var transId = <?php echo $ts_id ?: 0; ?>;
var grandTotalVal = <?php echo $reg['grand_total'] ?: 0; ?>;
var selectedMekanikId = 0;
var selectedSpItems = [];
var allSpData = [];

function reloadPage() { window.location.reload(); }

// ==================== SPAREPART ====================

function openTambahPart() {
    selectedSpItems = [];
    document.getElementById('cariSp').value = '';
    document.getElementById('spSelectedList').innerHTML = '<p class="text-muted small text-center py-3">Klik sparepart di tabel kiri untuk menambahkan</p>';
    document.getElementById('spSubtotal').textContent = 'Rp 0';
    document.getElementById('btnSimpanSp').disabled = true;
    new bootstrap.Modal(document.getElementById('sparepartModal')).show();
    loadAllSparepart();
}

function loadAllSparepart() {
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_sparepart&q=')
        .then(r => r.json())
        .then(data => {
            allSpData = data.data;
            renderSpTable(allSpData);
        });
}

function renderSpTable(items) {
    var selectedIds = selectedSpItems.map(function(i) { return i.id; });
    var filtered = items.filter(function(s) { return selectedIds.indexOf(parseInt(s.sparepart_id)) === -1; });

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
        html += '<td class="small text-end">Rp ' + Number(s.harga_jual).toLocaleString('id-ID') + '</td>';
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
    for (var i = 0; i < selectedSpItems.length; i++) {
        if (selectedSpItems[i].id == id) {
            selectedSpItems[i].qty++;
            renderSelectedList();
            return;
        }
    }
    selectedSpItems.push({ id: id, kode: kode, nama: nama, harga: harga, stok: stok, satuan: satuan, qty: 1 });
    renderSelectedList();
}

function renderSelectedList() {
    var html = '';
    if (selectedSpItems.length == 0) {
        html = '<p class="text-muted small text-center py-3">Klik sparepart di tabel kiri untuk menambahkan</p>';
    } else {
        selectedSpItems.forEach(function(item, idx) {
            var subtotal = item.qty * item.harga;
            html += '<div class="d-flex align-items-center gap-2 p-2 border rounded mb-2">';
            html += '  <div class="flex-grow-1">';
            html += '    <div class="small fw-semibold">' + item.nama + '</div>';
            html += '    <div class="text-muted" style="font-size:0.75rem;">' + item.kode + ' | Rp ' + Number(item.harga).toLocaleString('id-ID') + ' / ' + item.satuan + '</div>';
            html += '  </div>';
            html += '  <div style="width:70px;">';
            html += '    <input type="number" class="form-control form-control-sm text-center" value="' + item.qty + '" min="1" max="' + item.stok + '" onchange="updateQty(' + idx + ', this.value)" oninput="updateQty(' + idx + ', this.value)">';
            html += '  </div>';
            html += '  <div class="text-end" style="width:100px;">';
            html += '    <div class="small fw-bold">Rp ' + Number(subtotal).toLocaleString('id-ID') + '</div>';
            if (item.qty >= item.stok) html += '    <div class="text-danger" style="font-size:0.65rem;">Stok max</div>';
            html += '  </div>';
            html += '  <button class="btn btn-sm btn-outline-danger" onclick="removeSelected(' + idx + ')" style="padding:2px 6px;"><i class="bi bi-x"></i></button>';
            html += '</div>';
        });
    }
    document.getElementById('spSelectedList').innerHTML = html;
    var total = 0;
    selectedSpItems.forEach(function(item) { total += item.qty * item.harga; });
    document.getElementById('spSubtotal').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
    document.getElementById('btnSimpanSp').disabled = selectedSpItems.length == 0;

    // Refresh search table
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
    if (selectedSpItems.length == 0) { BMS.warning('Pilih minimal 1 sparepart'); return; }
    var items = selectedSpItems.map(function(item) { return { sparepart_id: item.id, qty: item.qty }; });
    var formData = new FormData();
    formData.append('action', 'add_sparepart_batch');
    formData.append('trans_id', transId);
    formData.append('items', JSON.stringify(items));
    fetch('<?= BASE_URL ?>/api/servis_action.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { if (data.success) { BMS.success('Berhasil disimpan'); setTimeout(reloadPage, 800); } else BMS.error(data.message); });
}

function hapusSparepart(detailId) {
    BMS.confirm('Hapus sparepart ini?').then(function(ok) {
        if (!ok) return;
        fetch('<?= BASE_URL ?>/api/servis_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=delete_sparepart&detail_id=' + detailId + '&trans_id=' + transId
        }).then(r => r.json()).then(data => { if (data.success) { BMS.success('Sparepart dihapus'); setTimeout(reloadPage, 800); } else BMS.error(data.message); });
    });
}

// ==================== JASA ====================

var selectedJasa = null;
function openAddJasa() {
    document.getElementById('jasaNamaInput').value = '';
    document.getElementById('jasaHargaInput').value = '';
    document.getElementById('jasaQty').value = 1;
    new bootstrap.Modal(document.getElementById('jasaModal')).show();
}
function addJasa() {
    var nama = document.getElementById('jasaNamaInput').value.trim();
    var hargaEl = document.getElementById('jasaHargaInput');
    var harga = parseFloat(hargaEl.dataset.raw || hargaEl.value.replace(/[^\d]/g, '')) || 0;
    var qty = document.getElementById('jasaQty').value;
    if (!nama || harga <= 0) { BMS.warning('Nama jasa dan harga wajib diisi'); return; }
    fetch('<?= BASE_URL ?>/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add_jasa&trans_id=' + transId + '&nama_jasa=' + encodeURIComponent(nama) + '&harga=' + harga + '&qty=' + qty
    }).then(r => r.json()).then(data => { if (data.success) reloadPage(); else BMS.error(data.message); });
}
function hapusJasa(detailId) {
    BMS.confirm('Hapus jasa ini?').then(function(ok) {
        if (!ok) return;
        fetch('<?= BASE_URL ?>/api/servis_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=delete_jasa&detail_id=' + detailId + '&trans_id=' + transId
        }).then(r => r.json()).then(data => { if (data.success) { BMS.success('Jasa dihapus'); setTimeout(reloadPage, 800); } else BMS.error(data.message); });
    });
}

// ==================== MEKANIK & START ====================

function openStartModal() {
    selectedMekanikId = 0;
    document.getElementById('btnStart').disabled = true;
    document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Memuat...</div>';
    new bootstrap.Modal(document.getElementById('mekanikModal')).show();
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_available_mekanik')
        .then(r => r.json())
        .then(data => {
            if (data.data.length == 0) {
                document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Tidak ada mekanik available</div>';
                return;
            }
            var html = '';
            data.data.forEach(function(m) {
                html += '<div class="p-3 border rounded mb-2 mk-card" style="cursor:pointer" onclick="pickMekanik(this,' + m.mekanik_id + ')">';
                html += '<div class="fw-semibold">' + m.nama + '</div>';
                html += '<small class="text-muted">' + m.spesialis + '</small></div>';
            });
            document.getElementById('mekanikList').innerHTML = html;
        });
}
function pickMekanik(el, id) {
    document.querySelectorAll('.mk-card').forEach(function(c) { c.classList.remove('border-primary'); c.style.background = ''; });
    el.classList.add('border-primary');
    el.style.background = '#eff6ff';
    selectedMekanikId = id;
    document.getElementById('btnStart').disabled = false;
}
function confirmStart() {
    if (!selectedMekanikId) return;
    fetch('<?= BASE_URL ?>/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=start&registration_id=<?php echo $reg_id; ?>&mekanik_id=' + selectedMekanikId
    }).then(r => r.json()).then(data => {
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('mekanikModal')).hide(); BMS.success('Servis dimulai'); setTimeout(reloadPage, 800); }
        else BMS.error(data.message);
    });
}

function setSelesai() {
    BMS.confirm('Tandai servis ini sebagai Selesai?').then(function(ok) {
        if (!ok) return;
        fetch('<?= BASE_URL ?>/api/servis_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=set_selesai&trans_id=' + transId
        }).then(r => r.json()).then(data => { if (data.success) { BMS.success('Servis selesai'); setTimeout(reloadPage, 800); } else BMS.error(data.message); });
    });
}

// ==================== PEMBAYARAN ====================

function hitungKembali() {
    var el = document.getElementById('inputBayar');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g, '')) || 0;
    var kembali = bayar - grandTotalVal;
    document.getElementById('kembali').value = 'Rp ' + Number(Math.max(0, kembali)).toLocaleString('id-ID');
}
function prosesBayar(e) {
    e.preventDefault();
    var el = document.getElementById('inputBayar');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g, '')) || 0;
    var metode = document.getElementById('metodeBayar').value;
    if (parseFloat(bayar) < grandTotalVal) { BMS.error('Jumlah bayar kurang dari grand total'); return false; }
    fetch('<?= BASE_URL ?>/api/servis_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=bayar&trans_id=' + transId + '&bayar=' + bayar + '&metode_bayar=' + metode
    }).then(r => r.json()).then(data => {
        if (data.success) { BMS.success('Pembayaran berhasil! Kembalian: Rp ' + Number(data.kembali).toLocaleString('id-ID')); setTimeout(reloadPage, 1200); }
        else BMS.error(data.message);
    });
    return false;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
