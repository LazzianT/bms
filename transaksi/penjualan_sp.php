<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$customers = mysqli_query($conn, "SELECT client_id, nama, telepon FROM client ORDER BY nama");
?>

<div class="page-header">
    <h4><i class="bi bi-bag me-2"></i>Penjualan Sparepart</h4>
    <a href="<?= BASE_URL ?>/transaksi/transaksi_list.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Ke Transaksi</a>
</div>

<div class="alert alert-info small py-2 mb-4">
    <i class="bi bi-info-circle me-1"></i> Penjualan sparepart tanpa servis. Customer beli part langsung — otomatis tercatat & dibayar.
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-body p-4">
        <!-- Customer & Vehicle -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select class="form-select" id="client_id" onchange="loadVehicles()">
                    <option value="">-- Pilih Customer --</option>
                    <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                    <option value="<?php echo $c['client_id']; ?>"><?php echo htmlspecialchars($c['nama']); ?> (<?php echo $c['telepon']; ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                <select class="form-select" id="vehicle_id"><option value="">-- Pilih Customer dulu --</option></select>
            </div>
        </div>

        <!-- Sparepart List -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0"><i class="bi bi-box me-1"></i> Sparepart</h6>
            <button class="btn btn-sm btn-primary" onclick="openSpDialog()"><i class="bi bi-plus"></i> Tambah</button>
        </div>
        <table class="table table-sm table-hover mb-3" id="spTable">
            <thead><tr><th>Kode</th><th>Nama</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th width="40"></th></tr></thead>
            <tbody></tbody>
            <tfoot><tr><td colspan="4" class="fw-bold text-end">Grand Total</td><td colspan="2" class="fw-bold text-primary fs-5" id="grandTotal">Rp 0</td></tr></tfoot>
        </table>

        <!-- Payment -->
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Metode Bayar</label>
                <select class="form-select" id="metodeBayar"><option value="Cash">Cash</option><option value="Transfer">Transfer</option><option value="QRIS">QRIS</option></select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bayar</label>
                <input type="number" class="form-control" id="bayar" min="0" oninput="hitungKembali()">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kembalian</label>
                <input type="text" class="form-control" id="kembali" readonly value="Rp 0">
            </div>
        </div>

        <hr class="my-4">
        <div class="d-flex justify-content-end">
            <button class="btn btn-success btn-lg" onclick="simpanPenjualan()"><i class="bi bi-check-lg me-1"></i> Simpan & Bayar</button>
        </div>
    </div>
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
var spItems = [];
var grandVal = 0;

function loadVehicles() {
    var cid = document.getElementById('client_id').value;
    var sel = document.getElementById('vehicle_id');
    if (!cid) { sel.innerHTML = '<option value="">-- Pilih Customer dulu --</option>'; return; }
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_vehicles&client_id='+cid).then(r=>r.json()).then(data => {
        sel.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
        data.data.forEach(v => { sel.innerHTML += '<option value="'+v.vehicle_id+'">'+v.no_polisi+' - '+v.merk+' '+v.tipe+'</option>'; });
        if (data.data.length === 1) sel.selectedIndex = 1;
    });
}

function openSpDialog() { document.getElementById('spSearch').value=''; new bootstrap.Modal(document.getElementById('spModal')).show(); searchSp(); }
function searchSp() {
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_sparepart&q='+encodeURIComponent(document.getElementById('spSearch').value)).then(r=>r.json()).then(data => {
        var html='';
        data.data.forEach(s => {
            html += '<tr><td><code>'+s.kode_sparepart+'</code></td><td>'+s.nama_sparepart+'</td><td>'+s.stok+' '+s.satuan+'</td><td>Rp '+Number(s.harga_jual).toLocaleString('id-ID')+'</td>';
            html += '<td><button class="btn btn-sm btn-primary" onclick="pickSp('+s.sparepart_id+',\''+s.kode_sparepart.replace(/'/g,"\\'")+'\',\''+s.nama_sparepart.replace(/'/g,"\\'")+'\','+s.harga_jual+','+s.stok+')"><i class="bi bi-plus"></i></button></td></tr>';
        });
        document.getElementById('spSearchBody').innerHTML = html || '<tr><td colspan="5" class="text-center text-muted">Tidak ditemukan</td></tr>';
    });
}
function pickSp(id,kode,nama,harga,stok) {
    for (var i=0;i<spItems.length;i++) { if (spItems[i].id==id) { spItems[i].qty++; renderSp(); return; } }
    spItems.push({id:id, kode:kode, nama:nama, harga:harga, qty:1, stok:stok});
    renderSp();
}
function removeSp(i) { spItems.splice(i,1); renderSp(); }
function updateQty(i,v) { spItems[i].qty = Math.max(1, Math.min(parseInt(v)||1, spItems[i].stok)); renderSp(); }
function renderSp() {
    var html='', total=0;
    spItems.forEach(function(s,i) {
        var sub = s.harga * s.qty; total += sub;
        html += '<tr><td><code>'+s.kode+'</code></td><td>'+s.nama+'</td><td>Rp '+Number(s.harga).toLocaleString('id-ID')+'</td>';
        html += '<td><input type="number" class="form-control form-control-sm" style="width:70px" value="'+s.qty+'" min="1" max="'+s.stok+'" onchange="updateQty('+i+',this.value)"></td>';
        html += '<td class="fw-semibold">Rp '+Number(sub).toLocaleString('id-ID')+'</td>';
        html += '<td><button class="btn btn-sm btn-outline-danger" onclick="removeSp('+i+')"><i class="bi bi-x"></i></button></td></tr>';
    });
    document.querySelector('#spTable tbody').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted py-3">Klik Tambah untuk menambah sparepart</td></tr>';
    grandVal = total;
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    hitungKembali();
}

function hitungKembali() {
    var el = document.getElementById('bayar');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g,'')) || 0;
    document.getElementById('kembali').value = 'Rp ' + Math.max(0, bayar - grandVal).toLocaleString('id-ID');
}

function simpanPenjualan() {
    var clientId = document.getElementById('client_id').value;
    var vehicleId = document.getElementById('vehicle_id').value;
    if (!clientId || !vehicleId) { BMS.warning('Pilih customer dan kendaraan'); return; }
    if (spItems.length == 0) { BMS.warning('Tambah minimal 1 sparepart'); return; }
    var el = document.getElementById('bayar');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g,'')) || 0;
    if (bayar < grandVal) { BMS.error('Jumlah bayar kurang dari total'); return; }

    var fd = new FormData();
    fd.append('action', 'save_penjualan_sp');
    fd.append('client_id', clientId);
    fd.append('vehicle_id', vehicleId);
    fd.append('metode_bayar', document.getElementById('metodeBayar').value);
    fd.append('bayar', bayar);
    fd.append('sparepart', JSON.stringify(spItems.map(s => ({sparepart_id: s.id, qty: s.qty}))));

    fetch('<?= BASE_URL ?>/api/servis_action.php', {method:'POST', body:fd}).then(r=>r.json()).then(data => {
        if (data.success) { BMS.success('Penjualan berhasil disimpan!'); setTimeout(function(){ window.location.href='<?= BASE_URL ?>/transaksi/transaksi_list.php'; }, 1000); }
        else BMS.error(data.message);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
