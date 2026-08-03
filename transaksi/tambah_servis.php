<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$customers  = mysqli_query($conn, "SELECT client_id, nama, telepon FROM client ORDER BY nama");
$mekaniks   = mysqli_query($conn, "SELECT mekanik_id, nama, spesialis FROM mekanik ORDER BY nama");

// Edit mode?
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $regId = (int)$_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tp.*, ts.trans_id FROM transaksi_pendaftaran tp LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id WHERE tp.registration_id = $regId"));
    if ($editData && in_array($editData['status'], ['Registered', 'InProgress'])) {
        $editMode = true;
    }
}
?>

<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2"></i><?php echo $editMode ? 'Edit Pendaftaran Servis' : 'Daftar Servis Baru'; ?></h4>
    <a href="<?= BASE_URL ?>/transaksi/servis_list.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="alert alert-light border mb-4 py-2 text-center small">
    <strong>Catatan:</strong> halaman ini hanya untuk pencatatan servis masuk. Jasa, sparepart, grand total, dan pembayaran dikelola di menu Transaksi Servis.
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Form Pendaftaran Servis</h6></div>
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
        <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-success" onclick="simpanPendaftaran()"><i class="bi bi-check-lg me-1"></i> <?php echo $editMode ? 'Update Pendaftaran' : 'Simpan Pendaftaran'; ?></button>
        </div>
    </div>
</div>

<script>
var editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
var editRegId = <?php echo $editMode ? $editData['registration_id'] : 0; ?>;
var editVehicleId = <?php echo $editMode ? $editData['vehicle_id'] : 0; ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (editMode) {
        loadVehicles(editVehicleId);
    }
});

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

function simpanPendaftaran() {
    if (!document.getElementById('client_id').value || !document.getElementById('vehicle_id').value || !document.getElementById('keluhan').value.trim()) {
        BMS.warning('Customer, Kendaraan, dan Keluhan wajib diisi');
        return;
    }

    var payload = {
        action: editMode ? 'save_wizard_edit' : 'save_wizard',
        client_id: document.getElementById('client_id').value,
        vehicle_id: document.getElementById('vehicle_id').value,
        mekanik_id: document.getElementById('mekanik_id').value,
        keluhan: document.getElementById('keluhan').value,
        catatan: document.getElementById('catatan').value
    };
    if (editMode) {
        payload.registration_id = editRegId;
    }
    var fd = new FormData();
    for (var k in payload) fd.append(k, payload[k]);
    fetch('<?= BASE_URL ?>/api/servis_action.php', {method: 'POST', body: fd}).then(r => r.json()).then(data => {
        if (data.success) { BMS.success(editMode ? 'Pendaftaran berhasil diupdate' : 'Pendaftaran berhasil disimpan'); setTimeout(function(){ window.location.href = '<?= BASE_URL ?>/transaksi/servis_list.php'; }, 1000); }
        else { BMS.error(data.message || 'Gagal menyimpan'); }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
