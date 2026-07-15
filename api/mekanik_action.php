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
        $result = mysqli_query($conn, "SELECT * FROM mekanik WHERE mekanik_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $nama      = sanitize($conn, $_POST['nama']);
        $telepon   = sanitize($conn, $_POST['telepon']);
        $spesialis = sanitize($conn, $_POST['spesialis']);

        if (empty($nama) || empty($telepon) || empty($spesialis)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        $query = "INSERT INTO mekanik (nama, telepon, spesialis) VALUES ('$nama', '$telepon', '$spesialis')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Mekanik berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $mekanik_id = (int)$_POST['mekanik_id'];
        $nama       = sanitize($conn, $_POST['nama']);
        $telepon    = sanitize($conn, $_POST['telepon']);
        $spesialis  = sanitize($conn, $_POST['spesialis']);

        if (empty($nama) || empty($telepon) || empty($spesialis)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        $query = "UPDATE mekanik SET nama='$nama', telepon='$telepon', spesialis='$spesialis' WHERE mekanik_id = $mekanik_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Mekanik berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Check if mekanik has active service transactions
        $check = mysqli_query($conn, "SELECT registration_id FROM transaksi_pendaftaran WHERE mekanik_id = $id AND status IN ('Registered','InProgress')");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Tidak bisa hapus, mekanik memiliki transaksi aktif']);
            exit();
        }

        $result = mysqli_query($conn, "DELETE FROM mekanik WHERE mekanik_id = $id");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Mekanik berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
