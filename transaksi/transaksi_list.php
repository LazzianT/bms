<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($filter == 'Menunggu')       $where .= " AND ts.status_servis = 'Menunggu'";
elseif ($filter == 'Dikerjakan') $where .= " AND ts.status_servis = 'Dikerjakan'";
elseif ($filter == 'Lunas')      $where .= " AND ts.status_servis = 'Selesai Lunas'";

if ($search) {
    $where .= " AND (c.nama LIKE '%$search%' OR v.no_polisi LIKE '%$search%' OR ts.keluhan LIKE '%$search%' OR m.nama LIKE '%$search%')";
}

$sql = "SELECT ts.*, c.nama as nama_client, v.no_polisi, v.merk, v.tipe, m.nama as nama_mekanik, tp.registration_id
        FROM transaksi_servis ts
        LEFT JOIN client c ON ts.client_id = c.client_id
        LEFT JOIN vehicle v ON ts.vehicle_id = v.vehicle_id
        LEFT JOIN mekanik m ON ts.mekanik_id = m.mekanik_id
        LEFT JOIN transaksi_pendaftaran tp ON ts.registration_id = tp.registration_id
        $where ORDER BY ts.tanggal DESC";
$result = mysqli_query($conn, $sql);

$tabs = [
    'all'        => ['label' => 'Semua',      'icon' => 'bi-list-ul'],
    'Menunggu'   => ['label' => 'Menunggu',   'icon' => 'bi-hourglass-split'],
    'Dikerjakan' => ['label' => 'Dikerjakan', 'icon' => 'bi-gear'],
    'Lunas'      => ['label' => 'Lunas',      'icon' => 'bi-check-circle'],
];
?>

<div class="page-header">
    <h4><i class="bi bi-wrench me-2"></i>Transaksi Servis</h4>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-3 gap-1">
    <?php foreach ($tabs as $key => $tab): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo ($filter == $key) ? 'active' : ''; ?>" href="?filter=<?php echo $key; ?>&search=<?php echo urlencode($search); ?>">
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
            <?php if ($search): ?><a href="?filter=<?php echo $filter; ?>" class="btn btn-outline-secondary btn-sm">Reset</a><?php endif; ?>
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
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Kendaraan</th>
                        <th>Mekanik</th>
                        <th>Keluhan</th>
                        <th>Total Jasa</th>
                        <th>Total Part</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['trans_id']; ?></td>
                            <td><?php echo formatDateTime($row['tanggal']); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_client']); ?></td>
                            <td>
                                <span class="badge badge-nopol"><?php echo htmlspecialchars($row['no_polisi']); ?></span>
                                <small class="text-muted d-block"><?php echo htmlspecialchars($row['merk'] . ' ' . $row['tipe']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_mekanik'] ?? '-'); ?></td>
                            <td><small><?php echo htmlspecialchars(mb_strimwidth($row['keluhan'] ?? '', 0, 25, '...')); ?></small></td>
                            <td><?php echo formatRupiah($row['total_jasa']); ?></td>
                            <td><?php echo formatRupiah($row['total_sparepart']); ?></td>
                            <td class="fw-bold"><?php echo formatRupiah($row['grand_total']); ?></td>
                            <td><?php echo setStatusBadge($row['status_servis']); ?></td>
                            <td>
                                <?php if ($row['status_servis'] == 'Dikerjakan'): ?>
                                    <a href="<?= BASE_URL ?>/transaksi/tambah_servis.php?edit=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Detail"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-sm btn-success" onclick="openBayarModal(<?php echo $row['trans_id']; ?>, <?php echo $row['grand_total']; ?>)" title="Selesaikan & Bayar"><i class="bi bi-cash-stack"></i></button>
                                <?php elseif ($row['status_servis'] == 'Menunggu'): ?>
                                    <a href="<?= BASE_URL ?>/transaksi/tambah_servis.php?edit=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Detail"><i class="bi bi-pencil"></i></a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/transaksi/detail_servis.php?id=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center py-4 text-muted"><i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada data transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="bayarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header"><h6 class="modal-title fw-bold">Selesaikan & Bayar</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <small class="text-muted">Grand Total</small>
                    <h3 class="text-primary mb-0" id="bayarGrandTotal">Rp 0</h3>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Bayar</label>
                    <select class="form-select" id="bayarMetode">
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Bayar</label>
                    <input type="number" class="form-control" id="bayarJumlah" min="0" oninput="hitungKembali()">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kembalian</label>
                    <input type="text" class="form-control" id="bayarKembali" readonly value="Rp 0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="prosesBayar()"><i class="bi bi-check-lg me-1"></i> Proses Bayar</button>
            </div>
        </div>
    </div>
</div>

<script>
var currentTransId = 0, currentGrand = 0;

function openBayarModal(transId, grand) {
    currentTransId = transId; currentGrand = grand;
    document.getElementById('bayarGrandTotal').textContent = 'Rp ' + Number(grand).toLocaleString('id-ID');
    document.getElementById('bayarJumlah').value = '';
    document.getElementById('bayarKembali').value = 'Rp 0';
    new bootstrap.Modal(document.getElementById('bayarModal')).show();
}

function hitungKembali() {
    var el = document.getElementById('bayarJumlah');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g, '')) || 0;
    document.getElementById('bayarKembali').value = 'Rp ' + Math.max(0, bayar - currentGrand).toLocaleString('id-ID');
}

function prosesBayar() {
    var el = document.getElementById('bayarJumlah');
    var bayar = parseFloat(el.dataset.raw || el.value.replace(/[^\d]/g, '')) || 0;
    if (bayar < currentGrand) { BMS.error('Jumlah bayar kurang dari grand total'); return; }
    var metode = document.getElementById('bayarMetode').value;
    fetch('<?= BASE_URL ?>/api/servis_action.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=bayar&trans_id='+currentTransId+'&bayar='+bayar+'&metode_bayar='+metode
    }).then(r=>r.json()).then(data => {
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('bayarModal')).hide(); BMS.success('Pembayaran berhasil'); setTimeout(function(){ location.reload(); }, 900); }
        else BMS.error(data.message);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
