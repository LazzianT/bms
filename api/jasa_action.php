<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {

    case 'get':
        $id = (int)$_GET['id'];
        $result = mysqli_query($conn, "SELECT * FROM master_jasa WHERE jasa_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $nama_jasa = sanitize($conn, $_POST['nama_jasa']);
        $kategori  = sanitize($conn, $_POST['kategori'] ?? '');
        $harga     = (double)$_POST['harga'];
        $deskripsi = sanitize($conn, $_POST['deskripsi'] ?? '');
        $is_aktif  = isset($_POST['is_aktif']) ? (int)$_POST['is_aktif'] : 0;

        if (empty($nama_jasa)) {
            echo json_encode(['success' => false, 'message' => 'Nama jasa wajib diisi']);
            exit();
        }

        $query = "INSERT INTO master_jasa (nama_jasa, kategori, harga, deskripsi, is_aktif) 
                  VALUES ('$nama_jasa', '$kategori', $harga, '$deskripsi', $is_aktif)";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Jasa berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $jasa_id   = (int)$_POST['jasa_id'];
        $nama_jasa = sanitize($conn, $_POST['nama_jasa']);
        $kategori  = sanitize($conn, $_POST['kategori'] ?? '');
        $harga     = (double)$_POST['harga'];
        $deskripsi = sanitize($conn, $_POST['deskripsi'] ?? '');
        $is_aktif  = isset($_POST['is_aktif']) ? (int)$_POST['is_aktif'] : 0;

        if (empty($nama_jasa)) {
            echo json_encode(['success' => false, 'message' => 'Nama jasa wajib diisi']);
            exit();
        }

        $query = "UPDATE master_jasa SET nama_jasa='$nama_jasa', kategori='$kategori', harga=$harga, deskripsi='$deskripsi', is_aktif=$is_aktif WHERE jasa_id = $jasa_id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Jasa berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];
        if (mysqli_query($conn, "DELETE FROM master_jasa WHERE jasa_id = $id")) {
            echo json_encode(['success' => true, 'message' => 'Jasa berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
