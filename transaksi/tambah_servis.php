<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$customers  = mysqli_query($conn, "SELECT client_id, nama, telepon FROM client ORDER BY nama");
$mekaniks   = mysqli_query($conn, "SELECT mekanik_id, nama, spesialis FROM mekanik ORDER BY nama");

// Edit mode?
$editMode = false;
$editData = null;
$editJasa = [];
$editSp = [];
if (isset($_GET['edit'])) {
    $regId = (int)$_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tp.*, ts.trans_id FROM transaksi_pendaftaran tp LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id WHERE tp.registration_id = $regId"));
    if ($editData && in_array($editData['status'], ['Registered', 'InProgress'])) {
        $editMode = true;
        $tsId = $editData['trans_id'];
        if ($tsId) {
            $r = mysqli_query($conn, "SELECT * FROM transaksi_servis_jasa WHERE trans_id = $tsId");
            while ($j = mysqli_fetch_assoc($r)) $editJasa[] = $j;
            $r = mysqli_query($conn, "SELECT d.*, s.kode_sparepart, s.nama_sparepart, s.satuan FROM transaksi_servis_detail d LEFT JOIN sparepart s ON d.sparepart_id = s.sparepart_id WHERE d.trans_id = $tsId");
            while ($s = mysqli_fetch_assoc($r)) $editSp[] = $s;
        }
    }
}
?>

<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2"></i><?php echo $editMode ? 'Edit Detail Servis' : 'Daftar Servis Baru'; ?></h4>
    <a href="<?= BASE_URL ?>/transaksi/servis_list.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<!-- Flow Guide -->
<div class="alert alert-light border mb-4 py-2 text-center small">
    <span class="badge bg-primary me-1" id="stepBadge1">1</span> Customer & Kendaraan
    <i class="bi bi-chevron-right mx-2"></i>
    <span class="badge bg-secondary me-1" id="stepBadge2">2</span> Jasa / Layanan
    <i class="bi bi-chevron-right mx-2"></i>
    <span class="badge bg-secondary me-1" id="stepBadge3">3</span> Sparepart
</div>

<!-- ==================== STEP 1: Customer & Kendaraan ==================== -->
<div id="step1" class="card mb-4">
    <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Step 1: Customer & Kendaraan</h6></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select class="form-select" id="client_id" onchange="loadVehicles()">
                    <option value="">-- Pilih Customer --</option>
                    <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                    <option value="<?php echo $c['client_id']; ?>" <?php echo ($editMode && $editData['client_id'] == $c['client_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nama']); ?> (<?php echo $c['telepon']; ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                <select class="form-select" id="vehicle_id">
                    <option value="">-- Pilih Customer dulu --</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mekanik</label>
                <select class="form-select" id="mekanik_id">
                    <option value="0">-- Opsional --</option>
                    <?php while ($m = mysqli_fetch_assoc($mekaniks)): ?>
                    <option value="<?php echo $m['mekanik_id']; ?>" <?php echo ($editMode && $editData['mekanik_id'] == $m['mekanik_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['nama']); ?> (<?php echo htmlspecialchars($m['spesialis']); ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Catatan</label>
                <input type="text" class="form-control" id="catatan" value="<?php echo $editMode ? htmlspecialchars($editData['catatan']) : ''; ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">Keluhan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="keluhan" rows="2"><?php echo $editMode ? htmlspecialchars($editData['keluhan']) : ''; ?></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary" onclick="goStep(2)">Lanjut <i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- ==================== STEP 2: Jasa ==================== -->
<div id="step2" class="card mb-4 d-none">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Step 2: Jasa / Layanan</h6>
    </div>
    <div class="card-body">
        <div class="row g-2 mb-3 align-items-end">
            <div class="col-md-5"><label class="form-label small">Nama Jasa</label><input type="text" class="form-control form-control-sm" id="jasaNama" placeholder="Contoh: Ganti Oli"></div>
            <div class="col-md-3"><label class="form-label small">Harga</label><input type="number" class="form-control form-control-sm" id="jasaHarga" min="0" placeholder="0"></div>
            <div class="col-md-2"><label class="form-label small">Qty</label><input type="number" class="form-control form-control-sm" id="jasaQty" value="1" min="1"></div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm w-100" onclick="addJasaRow()"><i class="bi bi-plus"></i> Tambah</button></div>
        </div>
        <table class="table table-sm table-hover" id="jasaTable">
            <thead><tr><th>Nama Jasa</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th width="40"></th></tr></thead>
            <tbody></tbody>
            <tfoot><tr><td colspan="3" class="fw-bold text-end">Total Jasa</td><td class="fw-bold text-primary" id="totalJasa">Rp 0</td><td></td></tr></tfoot>
        </table>
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-outline-secondary" onclick="goStep(1)"><i class="bi bi-chevron-left"></i> Kembali</button>
            <button class="btn btn-primary" onclick="goStep(3)">Lanjut <i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- ==================== STEP 3: Sparepart ==================== -->
<div id="step3" class="card mb-4 d-none">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Step 3: Sparepart</h6>
        <button class="btn btn-sm btn-primary" onclick="openSpDialog()"><i class="bi bi-plus"></i> Tambah Sparepart</button>
    </div>
    <div class="card-body">
        <table class="table table-sm table-hover" id="spTable">
            <thead><tr><th>Kode</th><th>Nama</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th width="40"></th></tr></thead>
            <tbody></tbody>
            <tfoot><tr><td colspan="4" class="fw-bold text-end">Total Sparepart</td><td class="fw-bold text-primary" id="totalSp">Rp 0</td><td></td></tr></tfoot>
        </table>

        <!-- Summary -->
        <div class="card bg-light mt-3">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col text-end">
                        <small class="text-muted">Total Jasa: <span id="sumJasa" class="fw-bold">Rp 0</span></small> &nbsp;|&nbsp;
                        <small class="text-muted">Total Part: <span id="sumSp" class="fw-bold">Rp 0</span></small> &nbsp;|&nbsp;
                        <small>Grand Total: <span id="grandTotal" class="fw-bold text-primary fs-5">Rp 0</span></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-outline-secondary" onclick="goStep(2)"><i class="bi bi-chevron-left"></i> Kembali</button>
            <button class="btn btn-success" onclick="simpanServis()"><i class="bi bi-check-lg me-1"></i> Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Pilih Sparepart -->
<div class="modal fade" id="spModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header"><h6 class="modal-title fw-bold">Pilih Sparepart</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="text" class="form-control form-control-sm mb-2" id="spSearch" placeholder="Cari kode/nama..." oninput="searchSp()">
                <div style="max-height:300px;overflow-y:auto;">
                    <table class="table table-sm table-hover"><thead><tr><th>Kode</th><th>Nama</th><th>Stok</th><th>Harga</th><th></th></tr></thead><tbody id="spSearchBody"></tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var jasaItems = <?php echo json_encode($editJasa); ?>;
var spItems = <?php echo json_encode(array_map(function($s) { return ['sparepart_id'=>$s['sparepart_id'],'kode'=>$s['kode_sparepart'],'nama'=>$s['nama_sparepart'],'harga'=>$s['harga'],'qty'=>$s['qty'],'satuan'=>$s['satuan']]; }, $editSp)); ?>;
var editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
var editRegId = <?php echo $editMode ? $editData['registration_id'] : 0; ?>;
var editTransId = <?php echo ($editMode && $editData['trans_id']) ? $editData['trans_id'] : 0; ?>;
var editVehicleId = <?php echo $editMode ? $editData['vehicle_id'] : 0; ?>;

// Init
document.addEventListener('DOMContentLoaded', function() {
    if (editMode) {
        loadVehicles(editVehicleId);
        renderJasaTable();
        renderSpTable();
    }
});

// ==================== STEPS ====================
function goStep(n) {
    if (n === 2) {
        if (!document.getElementById('client_id').value || !document.getElementById('vehicle_id').value || !document.getElementById('keluhan').value.trim()) {
            BMS.warning('Customer, Kendaraan, dan Keluhan wajib diisi'); return;
        }
    }
    document.getElementById('step1').classList.toggle('d-none', n !== 1);
    document.getElementById('step2').classList.toggle('d-none', n !== 2);
    document.getElementById('step3').classList.toggle('d-none', n !== 3);
    for (var i = 1; i <= 3; i++) {
        document.getElementById('stepBadge' + i).className = 'badge me-1 ' + (i <= n ? 'bg-primary' : 'bg-secondary');
    }
}

// ==================== VEHICLES ====================
function loadVehicles(preselect) {
    var cid = document.getElementById('client_id').value;
    var sel = document.getElementById('vehicle_id');
    sel.innerHTML = '<option value="">Memuat...</option>';
    if (!cid) { sel.innerHTML = '<option value="">-- Pilih Customer dulu --</option>'; return; }
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_vehicles&client_id=' + cid).then(r => r.json()).then(data => {
        sel.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
        data.data.forEach(function(v) {
            var opt = document.createElement('option');
            opt.value = v.vehicle_id;
            opt.textContent = v.no_polisi + ' - ' + v.merk + ' ' + v.tipe;
            if (preselect && v.vehicle_id == preselect) opt.selected = true;
            sel.appendChild(opt);
        });
        if (data.data.length === 1) sel.selectedIndex = 1;
    });
}

// ==================== JASA ====================
function addJasaRow() {
    var nama = document.getElementById('jasaNama').value.trim();
    var hargaEl = document.getElementById('jasaHarga');
    var harga = parseFloat(hargaEl.dataset.raw || hargaEl.value.replace(/[^\d]/g, '')) || 0;
    var qty = parseInt(document.getElementById('jasaQty').value) || 1;
    if (!nama || harga <= 0) { BMS.warning('Nama jasa dan harga wajib diisi'); return; }
    jasaItems.push({nama_jasa: nama, harga: harga, qty: qty, subtotal: harga * qty});
    document.getElementById('jasaNama').value = '';
    hargaEl.value = ''; hargaEl.dataset.raw = '';
    document.getElementById('jasaQty').value = '1';
    renderJasaTable();
}
function removeJasa(i) { jasaItems.splice(i, 1); renderJasaTable(); }
function renderJasaTable() {
    var html = '', total = 0;
    jasaItems.forEach(function(j, i) {
        var sub = (j.harga || 0) * (j.qty || 1);
        total += sub;
        html += '<tr><td>' + j.nama_jasa + '</td><td>Rp ' + Number(j.harga).toLocaleString('id-ID') + '</td><td>' + (j.qty||1) + '</td><td class="fw-semibold">Rp ' + Number(sub).toLocaleString('id-ID') + '</td>';
        html += '<td><button class="btn btn-sm btn-outline-danger" onclick="removeJasa(' + i + ')"><i class="bi bi-x"></i></button></td></tr>';
    });
    document.querySelector('#jasaTable tbody').innerHTML = html || '<tr><td colspan="5" class="text-center text-muted py-2">Belum ada jasa</td></tr>';
    document.getElementById('totalJasa').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('sumJasa').textContent = 'Rp ' + total.toLocaleString('id-ID');
    updateGrand();
}

// ==================== SPAREPART ====================
function openSpDialog() {
    document.getElementById('spSearch').value = '';
    new bootstrap.Modal(document.getElementById('spModal')).show();
    searchSp();
}
function searchSp() {
    var q = document.getElementById('spSearch').value;
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_sparepart&q=' + encodeURIComponent(q)).then(r => r.json()).then(data => {
        var html = '';
        data.data.forEach(function(s) {
            html += '<tr><td><code>' + s.kode_sparepart + '</code></td><td>' + s.nama_sparepart + '</td><td>' + s.stok + ' ' + s.satuan + '</td><td>Rp ' + Number(s.harga_jual).toLocaleString('id-ID') + '</td>';
            html += '<td><button class="btn btn-sm btn-primary" onclick="pickSp(' + s.sparepart_id + ',\'' + s.kode_sparepart.replace(/'/g,"\\'") + '\',\'' + s.nama_sparepart.replace(/'/g,"\\'") + '\',' + s.harga_jual + ',' + s.stok + ',\'' + s.satuan + '\')"><i class="bi bi-plus"></i></button></td></tr>';
        });
        document.getElementById('spSearchBody').innerHTML = html || '<tr><td colspan="5" class="text-center text-muted">Tidak ditemukan</td></tr>';
    });
}
function pickSp(id, kode, nama, harga, stok, satuan) {
    for (var i = 0; i < spItems.length; i++) {
        if (spItems[i].sparepart_id == id) { spItems[i].qty++; renderSpTable(); return; }
    }
    spItems.push({sparepart_id: id, kode: kode, nama: nama, harga: harga, qty: 1, stok: stok, satuan: satuan});
    renderSpTable();
}
function removeSp(i) { spItems.splice(i, 1); renderSpTable(); }
function updateSpQty(i, val) { spItems[i].qty = Math.max(1, Math.min(parseInt(val)||1, spItems[i].stok||999)); renderSpTable(); }
function renderSpTable() {
    var html = '', total = 0;
    spItems.forEach(function(s, i) {
        var sub = s.harga * s.qty;
        total += sub;
        html += '<tr><td><code>' + (s.kode||s.kode_sparepart||'') + '</code></td><td>' + (s.nama||s.nama_sparepart||'') + '</td><td>Rp ' + Number(s.harga).toLocaleString('id-ID') + '</td>';
        html += '<td><input type="number" class="form-control form-control-sm" style="width:70px" value="' + s.qty + '" min="1" onchange="updateSpQty(' + i + ',this.value)"></td>';
        html += '<td class="fw-semibold">Rp ' + Number(sub).toLocaleString('id-ID') + '</td>';
        html += '<td><button class="btn btn-sm btn-outline-danger" onclick="removeSp(' + i + ')"><i class="bi bi-x"></i></button></td></tr>';
    });
    document.querySelector('#spTable tbody').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted py-2">Belum ada sparepart</td></tr>';
    document.getElementById('totalSp').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('sumSp').textContent = 'Rp ' + total.toLocaleString('id-ID');
    updateGrand();
}
function updateGrand() {
    var j = 0, s = 0;
    jasaItems.forEach(function(x) { j += (x.harga||0) * (x.qty||1); });
    spItems.forEach(function(x) { s += x.harga * x.qty; });
    document.getElementById('grandTotal').textContent = 'Rp ' + (j + s).toLocaleString('id-ID');
}

// ==================== SIMPAN ====================
function simpanServis() {
    var payload = {
        action: editMode ? 'save_wizard_edit' : 'save_wizard',
        client_id: document.getElementById('client_id').value,
        vehicle_id: document.getElementById('vehicle_id').value,
        mekanik_id: document.getElementById('mekanik_id').value,
        keluhan: document.getElementById('keluhan').value,
        catatan: document.getElementById('catatan').value,
        jasa: JSON.stringify(jasaItems.map(function(j) { return {nama_jasa: j.nama_jasa, harga: j.harga, qty: j.qty || 1}; })),
        sparepart: JSON.stringify(spItems.map(function(s) { return {sparepart_id: s.sparepart_id, qty: s.qty}; }))
    };
    if (editMode) {
        payload.registration_id = editRegId;
        payload.trans_id = editTransId;
    }
    var fd = new FormData();
    for (var k in payload) fd.append(k, payload[k]);
    fetch('<?= BASE_URL ?>/api/servis_action.php', {method: 'POST', body: fd}).then(r => r.json()).then(data => {
        if (data.success) { BMS.success('Data servis berhasil disimpan'); setTimeout(function(){ window.location.href = '<?= BASE_URL ?>/transaksi/servis_list.php'; }, 1000); }
        else { BMS.error(data.message || 'Gagal menyimpan'); }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
