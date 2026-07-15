<?php
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    return date('d/m/Y H:i', strtotime($datetime));
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
