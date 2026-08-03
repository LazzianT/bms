<?php
require_once __DIR__ . '/includes/header.php';
requireRole(['admin', 'kasir', 'manager']);

// ====================== DATA TIM ======================
// Ganti nama, nim, peran, dan foto sesuai anggota kelompokmu.
// Foto: taruh file di folder assets/img lalu isi 'foto' => 'assets/img/nama.jpg'
// Jika 'foto' dikosongkan, otomatis tampil avatar inisial berwarna.
$project_nama = 'BMS - Bengkel Management System';
$project_url  = BASE_URL . '/';

// foto: simpan file ke assets/img/ dengan nama sesuai kolom 'foto'
$team = [
    ['nama' => 'Dela Ramadani',            'nim' => '',  'peran' => 'Anggota', 'foto' => 'assets/img/dela.jpg',      'warna' => '#0891b2', 'project' => 'projects/dela-ramadani/'],
    ['nama' => 'Zinedine Ziddan Fahdlevy', 'nim' => '',  'peran' => 'Anggota',   'foto' => 'assets/img/zinedine.jpg',  'warna' => '#f97316', 'project' => 'projects/zinedine-ziddan-fahdlevy?page=biodata'],
    ['nama' => 'Haura Nahdah',             'nim' => '',  'peran' => 'Anggota', 'foto' => 'assets/img/haura.jpg',     'warna' => '#059669', 'project' => 'projects/haura-nahdah/biodata.html'],
    ['nama' => 'Izra Ilham',               'nim' => '',  'peran' => 'Anggota', 'foto' => 'assets/img/izra.jpg',      'warna' => '#8b5cf6', 'project' => 'projects/izra-ilham/'],
    ['nama' => 'Lazzian Alfalah',          'nim' => '',  'peran' => 'Anggota', 'foto' => 'assets/img/lazzian.jpg',   'warna' => '#ef4444', 'project' => 'projects/lazzian-alfalah/'],
    ['nama' => 'Syanaia Lulailika',        'nim' => '',  'peran' => 'Anggota', 'foto' => 'assets/img/syanaia.jpg',   'warna' => '#0ea5e9', 'project' => 'projects/syanaia-lulailika/home.php'],
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
        <h5 class="mb-1 fw-bold"><i class="bi bi-tools me-2"></i>Project Kelompok</h5>
        <p class="mb-0 opacity-75"><?php echo htmlspecialchars($project_nama); ?></p>
    </div>
    <a href="<?php echo htmlspecialchars($project_url); ?>" class="btn btn-primary btn-lg">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Project
    </a>
</div>

<!-- ====================== MEMBER CARDS ====================== -->
<div class="row g-3">
    <?php foreach ($team as $m): ?>
    <div class="col-md-6 col-lg-4">
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
                <?php if (!empty($m['project'])): ?>
                    <a class="btn btn-sm btn-primary w-100 mt-1" href="<?= BASE_URL ?>/<?php echo htmlspecialchars($m['project']); ?>" target="_blank">
                        <i class="bi bi-folder2-open me-1"></i>Lihat Project
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
