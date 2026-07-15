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
        $result = mysqli_query($conn, "SELECT * FROM supplier WHERE supplier_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $nama    = sanitize($conn, $_POST['nama']);
        $telepon = sanitize($conn, $_POST['telepon']);
        $email   = sanitize($conn, $_POST['email']);
        $alamat  = sanitize($conn, $_POST['alamat']);

        if (empty($nama) || empty($telepon)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan telepon wajib diisi']);
            exit();
        }

        $query = "INSERT INTO supplier (nama, telepon, email, alamat) VALUES ('$nama', '$telepon', '$email', '$alamat')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Supplier berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $supplier_id = (int)$_POST['supplier_id'];
        $nama        = sanitize($conn, $_POST['nama']);
        $telepon     = sanitize($conn, $_POST['telepon']);
        $email       = sanitize($conn, $_POST['email']);
        $alamat      = sanitize($conn, $_POST['alamat']);

        if (empty($nama) || empty($telepon)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan telepon wajib diisi']);
            exit();
        }

        $query = "UPDATE supplier SET nama='$nama', telepon='$telepon', email='$email', alamat='$alamat' WHERE supplier_id = $supplier_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Supplier berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Set FK sparepart.supplier_id to NULL before deleting
        mysqli_query($conn, "UPDATE sparepart SET supplier_id = NULL WHERE supplier_id = $id");

        $result = mysqli_query($conn, "DELETE FROM supplier WHERE supplier_id = $id");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Supplier berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
