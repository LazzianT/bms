<?php
require_once __DIR__ . '/includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

// ====================== DATA TIM ======================
// Ganti nama, nim, peran, dan foto sesuai anggota kelompokmu.
// Foto: taruh file di folder assets/img lalu isi 'foto' => 'assets/img/nama.jpg'
// Jika 'foto' dikosongkan, otomatis tampil avatar inisial berwarna.
$project_nama = 'BMS - Bengkel Management System';
$project_url  = 'https://';

$team = [
    ['nama' => 'Anggota 1', 'nim' => '12345', 'peran' => 'Ketua',   'foto' => '', 'warna' => '#f97316'],
    ['nama' => 'Anggota 2', 'nim' => '12345', 'peran' => 'Anggota', 'foto' => '', 'warna' => '#0891b2'],
    ['nama' => 'Anggota 3', 'nim' => '12345', 'peran' => 'Anggota', 'foto' => '', 'warna' => '#059669'],
    ['nama' => 'Anggota 4', 'nim' => '12345', 'peran' => 'Anggota', 'foto' => '', 'warna' => '#8b5cf6'],
    ['nama' => 'Anggota 5', 'nim' => '12345', 'peran' => 'Anggota', 'foto' => '', 'warna' => '#0ea5e9'],
];

function teamInitials($nama) {
    $parts = array_values(array_filter(explode(' ', trim($nama))));
    $ini = '';
    foreach ($parts as $p) { $ini .= strtoupper(substr($p, 0, 1)); }
    return substr($ini, 0, 2);
}
?>

<div class="page-header">
    <h4><i class="bi bi-people-fill me-2"></i>Our Team</h4>
    <span class="text-muted"><?php echo count($team); ?> anggota</span>
</div>

<!-- ====================== PROJECT BANNER ====================== -->
<div class="team-hero mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-bold"><i class="bi bi-link-45deg me-2"></i>Project Kelompok</h5>
        <p class="mb-0 opacity-75"><?php echo htmlspecialchars($project_nama); ?></p>
    </div>
    <a href="<?php echo htmlspecialchars($project_url); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Project
    </a>
</div>

<!-- ====================== MEMBER CARDS ====================== -->
<div class="row g-3">
    <?php foreach ($team as $m): ?>
    <div class="col-md-6 col-lg-3">
        <div class="card team-card h-100">
            <div class="team-cover"></div>

            <?php if ($m['foto'] !== '' && file_exists(__DIR__ . '/' . $m['foto'])): ?>
                <div class="team-avatar" style="background-image:url('<?php echo htmlspecialchars(BASE_URL . '/' . $m['foto']); ?>'); background-size:cover; background-position:center;"></div>
            <?php else: ?>
                <div class="team-avatar" style="background:linear-gradient(135deg, <?php echo $m['warna']; ?>, <?php echo $m['warna']; ?>cc);"><?php echo teamInitials($m['nama']); ?></div>
            <?php endif; ?>

            <div class="card-body text-center">
                <h6 class="team-name"><?php echo htmlspecialchars($m['nama']); ?></h6>
                <span class="team-role" style="color:<?php echo $m['warna']; ?>;"><?php echo htmlspecialchars($m['peran']); ?></span>
                <?php if (!empty($m['nim'])): ?>
                    <div class="team-nim"><?php echo htmlspecialchars($m['nim']); ?></div>
                <?php endif; ?>
                <div class="team-divider"></div>
                <a class="team-mail" href="mailto:"><i class="bi bi-envelope me-1"></i>Hubungi</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
