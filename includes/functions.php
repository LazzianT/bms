<?php
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function formatDate($date) {
    if (empty($date)) return '-';
    $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[date('n', $ts) - 1] . ' ' . date('y', $ts);
}

function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    $bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $ts = strtotime($datetime);
    return date('d', $ts) . ' ' . $bulan[date('n', $ts) - 1] . ' ' . date('y', $ts) . ', ' . date('H:i', $ts);
}

function getPeriodDates($period) {
    switch ($period) {
        case 'hari_ini':
            return [date('Y-m-d'), date('Y-m-d')];
        case 'kemarin':
            $d = date('Y-m-d', strtotime('-1 day'));
            return [$d, $d];
        case 'minggu_ini':
            return [date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week'))];
        case 'minggu_lalu':
            return [date('Y-m-d', strtotime('monday last week')), date('Y-m-d', strtotime('sunday last week'))];
        case 'bulan_ini':
            return [date('Y-m-01'), date('Y-m-t')];
        case 'bulan_lalu':
            return [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))];
        case 'tahun_ini':
            return [date('Y-01-01'), date('Y-12-31')];
        case 'custom':
            $dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
            $sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');
            return [$dari, $sampai];
        default: // semua
            return ['2000-01-01', '2099-12-31'];
    }
}

function renderPeriodFilter($period, $dari = '', $sampai = '', $extraParams = '') {
    $options = [
        ''            => 'Semua',
        'hari_ini'    => 'Hari Ini',
        'kemarin'     => 'Kemarin',
        'minggu_ini'  => 'Minggu Ini',
        'minggu_lalu' => 'Minggu Lalu',
        'bulan_ini'   => 'Bulan Ini',
        'bulan_lalu'  => 'Bulan Lalu',
        'tahun_ini'   => 'Tahun Ini',
        'custom'      => 'Custom',
    ];
    $html = '<div><label class="form-label mb-1 small">Periode</label><select class="form-select form-select-sm" name="period" onchange="toggleCustomDate(this)">';
    foreach ($options as $val => $label) {
        $sel = ($period === $val) ? 'selected' : '';
        $html .= "<option value=\"$val\" $sel>$label</option>";
    }
    $html .= '</select></div>';
    $show = ($period === 'custom') ? '' : 'style="display:none"';
    $html .= "<div id=\"customDateRange\" class=\"d-flex gap-2\" $show>";
    $html .= '<div><label class="form-label mb-1 small">Dari</label><input type="date" class="form-control form-control-sm" name="dari" value="' . htmlspecialchars($dari) . '"></div>';
    $html .= '<div><label class="form-label mb-1 small">Sampai</label><input type="date" class="form-control form-control-sm" name="sampai" value="' . htmlspecialchars($sampai) . '"></div>';
    $html .= '</div>';
    return $html;
}

function renderPeriodScript() {
    return '<script>function toggleCustomDate(el){document.getElementById("customDateRange").style.display=el.value==="custom"?"flex":"none";}</script>';
}

function generateNomor($prefix, $conn, $table, $column) {
    $tanggal = date('Ymd');
    $query = "SELECT COUNT(*) as jumlah FROM $table WHERE DATE($column) = CURDATE()";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $nomor = $row['jumlah'] + 1;
    return $prefix . $tanggal . '-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
}

function setStatusBadge($status) {
    $classes = [
        'Registered'        => 'bg-info',
        'Antrean'           => 'bg-info',
        'InProgress'        => 'bg-warning text-dark',
        'Dikerjakan'        => 'bg-warning text-dark',
        'Completed'         => 'bg-success',
        'Selesai'           => 'bg-success',
        'Selesai Lunas'     => 'bg-primary',
        'Lunas'             => 'bg-primary',
        'Menunggu'          => 'bg-secondary',
        'Draft'             => 'bg-secondary',
        'Sent'              => 'bg-info',
        'Partial Received'  => 'bg-warning text-dark',
        'Fully Received'    => 'bg-success',
        'Received'          => 'bg-info',
        'Verified'          => 'bg-success',
        'Cancelled'         => 'bg-danger',
    ];
    $class = isset($classes[$status]) ? $classes[$status] : 'bg-secondary';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($status) . '</span>';
}

function alertSuccess($msg) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $msg . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

function alertError($msg) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $msg . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}
?>
