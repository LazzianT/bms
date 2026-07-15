<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$customers = mysqli_query($conn, "SELECT client_id, nama FROM client ORDER BY nama");
?>

<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2"></i>Daftar Servis Baru</h4>
    <a href="/bms/transaksi/servis_list.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form id="formServis" onsubmit="return submitForm(event)">
                    <div class="row g-3">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id" name="client_id" required onchange="loadVehicles()">
                                <option value="">-- Pilih Customer --</option>
                                <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                                    <option value="<?php echo $c['client_id']; ?>"><?php echo htmlspecialchars($c['nama']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Kendaraan -->
                        <div class="col-md-6">
                            <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                <option value="">-- Pilih Customer dulu --</option>
                            </select>
                        </div>

                        <!-- Keluhan -->
                        <div class="col-md-12">
                            <label class="form-label">Keluhan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3" placeholder="Deskripsikan keluhan kendaraan..." required></textarea>
                        </div>

                        <!-- Catatan -->
                        <div class="col-md-12">
                            <label class="form-label">Catatan (Opsional)</label>
                            <input type="text" class="form-control" id="catatan" name="catatan" placeholder="Catatan tambahan...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="/bms/transaksi/servis_list.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Daftarkan Servis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadVehicles() {
    var clientId = document.getElementById('client_id').value;
    var vehicleSelect = document.getElementById('vehicle_id');
    vehicleSelect.innerHTML = '<option value="">Memuat...</option>';

    if (!clientId) {
        vehicleSelect.innerHTML = '<option value="">-- Pilih Customer dulu --</option>';
        return;
    }

    fetch('/bms/api/servis_action.php?action=get_vehicles&client_id=' + clientId)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                vehicleSelect.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
                data.data.forEach(function(v) {
                    var opt = document.createElement('option');
                    opt.value = v.vehicle_id;
                    opt.textContent = v.no_polisi + ' - ' + v.merk + ' ' + v.tipe;
                    vehicleSelect.appendChild(opt);
                });
            } else {
                vehicleSelect.innerHTML = '<option value="">Tidak ada kendaraan</option>';
            }
        });
}

function submitForm(e) {
    e.preventDefault();
    var form = document.getElementById('formServis');
    var formData = new FormData(form);
    formData.append('action', 'add_registration');

    fetch('/bms/api/servis_action.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/bms/transaksi/servis_list.php';
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    });
    return false;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
