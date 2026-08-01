<?php
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'kasir']);

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($filter == 'Registered')     $where .= " AND tp.status = 'Registered'";
elseif ($filter == 'InProgress') $where .= " AND tp.status = 'InProgress'";
elseif ($filter == 'Completed')  $where .= " AND tp.status = 'Completed'";

if ($search) {
    $where .= " AND (c.nama LIKE '%$search%' OR v.no_polisi LIKE '%$search%' OR tp.keluhan LIKE '%$search%' OR m.nama LIKE '%$search%')";
}

$sql = "SELECT tp.*, c.nama as nama_client, v.no_polisi, v.merk, v.tipe, m.nama as nama_mekanik
        FROM transaksi_pendaftaran tp
        LEFT JOIN client c ON tp.client_id = c.client_id
        LEFT JOIN vehicle v ON tp.vehicle_id = v.vehicle_id
        LEFT JOIN mekanik m ON tp.mekanik_id = m.mekanik_id
        $where ORDER BY tp.tanggal_daftar DESC";
$result = mysqli_query($conn, $sql);

$tabs = [
    'all'        => ['label' => 'Semua',      'icon' => 'bi-list-ul'],
    'Registered' => ['label' => 'Antrean',    'icon' => 'bi-hourglass-split'],
    'InProgress' => ['label' => 'Dikerjakan', 'icon' => 'bi-gear'],
    'Completed'  => ['label' => 'Selesai',    'icon' => 'bi-check-circle'],
];
?>

<div class="page-header">
    <h4><i class="bi bi-clipboard-plus me-2"></i>Pendaftaran Servis</h4>
    <a href="<?= BASE_URL ?>/transaksi/tambah_servis.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Daftar Servis Baru
    </a>
</div>

<!-- Flow Guide -->
<div class="alert alert-light border mb-3 py-2 text-center small">
    <strong>Flow:</strong> Daftar Baru → Mulai Servis → Edit Detail → Selesaikan & Bayar (di menu Transaksi)
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
                <input type="text" class="form-control" name="search" placeholder="Cari customer, plat, keluhan..." value="<?php echo htmlspecialchars($search); ?>">
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
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Kendaraan</th>
                        <th>Keluhan</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo formatDateTime($row['tanggal_daftar']); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_client']); ?></td>
                            <td>
                                <span class="badge badge-nopol"><?php echo htmlspecialchars($row['no_polisi']); ?></span>
                                <small class="text-muted d-block"><?php echo htmlspecialchars($row['merk'] . ' ' . $row['tipe']); ?></small>
                            </td>
                            <td><small><?php echo htmlspecialchars(mb_strimwidth($row['keluhan'], 0, 35, '...')); ?></small></td>
                            <td><?php echo $row['nama_mekanik'] ? htmlspecialchars($row['nama_mekanik']) : '<span class="text-muted">-</span>'; ?></td>
                            <td><?php echo setStatusBadge($row['status']); ?></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($row['catatan'] ?? ''); ?></small></td>
                            <td>
                                <?php if ($row['status'] == 'Registered'): ?>
                                    <button class="btn btn-sm btn-success me-1" onclick="openStartModal(<?php echo $row['registration_id']; ?>)" title="Mulai Servis"><i class="bi bi-play-fill"></i></button>
                                    <a href="<?= BASE_URL ?>/transaksi/tambah_servis.php?edit=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusPendaftaran(<?php echo $row['registration_id']; ?>)" title="Hapus"><i class="bi bi-trash"></i></button>
                                <?php elseif ($row['status'] == 'InProgress'): ?>
                                    <a href="<?= BASE_URL ?>/transaksi/tambah_servis.php?edit=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Detail"><i class="bi bi-pencil"></i></a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/transaksi/detail_servis.php?id=<?php echo $row['registration_id']; ?>" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pilih Mekanik -->
<div class="modal fade" id="mekanikModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header"><h6 class="modal-title fw-bold">Pilih Mekanik</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Pilih mekanik yang <strong>available</strong>:</p>
                <div id="mekanikList" style="max-height:300px;overflow-y:auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnStart" onclick="confirmStart()" disabled><i class="bi bi-play-fill me-1"></i> Mulai Servis</button>
            </div>
        </div>
    </div>
</div>

<script>
var currentRegId = 0, selectedMekanikId = 0;

function openStartModal(regId) {
    currentRegId = regId; selectedMekanikId = 0;
    document.getElementById('btnStart').disabled = true;
    document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Memuat...</div>';
    new bootstrap.Modal(document.getElementById('mekanikModal')).show();
    fetch('<?= BASE_URL ?>/api/servis_action.php?action=get_available_mekanik').then(r=>r.json()).then(data => {
        if (data.data.length == 0) { document.getElementById('mekanikList').innerHTML = '<div class="text-center py-3 text-muted">Tidak ada mekanik available</div>'; return; }
        var html = '';
        data.data.forEach(function(m) {
            html += '<div class="p-3 border rounded mb-2 mk-card" style="cursor:pointer" onclick="pickMk(this,'+m.mekanik_id+')">';
            html += '<div class="fw-semibold">'+m.nama+'</div><small class="text-muted">'+(m.spesialis||'')+'</small></div>';
        });
        document.getElementById('mekanikList').innerHTML = html;
    });
}
function pickMk(el, id) {
    document.querySelectorAll('.mk-card').forEach(c => { c.classList.remove('border-primary'); c.style.background=''; });
    el.classList.add('border-primary'); el.style.background='#eff6ff';
    selectedMekanikId = id; document.getElementById('btnStart').disabled = false;
}
function confirmStart() {
    if (!selectedMekanikId) return;
    fetch('<?= BASE_URL ?>/api/servis_action.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action=start&registration_id='+currentRegId+'&mekanik_id='+selectedMekanikId
    }).then(r=>r.json()).then(data => {
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('mekanikModal')).hide(); BMS.success('Servis dimulai'); setTimeout(function(){ location.reload(); }, 800); }
        else BMS.error(data.message);
    });
}
function hapusPendaftaran(regId) {
    BMS.confirm('Hapus pendaftaran ini?').then(function(ok) {
        if (!ok) return;
        fetch('<?= BASE_URL ?>/api/servis_action.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=cancel_registration&registration_id='+regId
        }).then(r=>r.json()).then(data => { if (data.success) { BMS.success('Pendaftaran dihapus'); setTimeout(function(){ location.reload(); }, 800); } else BMS.error(data.message); });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
