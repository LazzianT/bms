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

    case 'get_suppliers':
        $result = mysqli_query($conn, "SELECT supplier_id, nama FROM supplier ORDER BY nama");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get':
        $id = (int)$_GET['id'];
        $result = mysqli_query($conn, "SELECT * FROM sparepart WHERE sparepart_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $kode_sparepart  = sanitize($conn, $_POST['kode_sparepart']);
        $nama_sparepart  = sanitize($conn, $_POST['nama_sparepart']);
        $satuan          = sanitize($conn, $_POST['satuan']);
        $stok            = (int)$_POST['stok'];
        $stok_minimum    = (int)$_POST['stok_minimum'];
        $harga_beli      = (double)$_POST['harga_beli'];
        $harga_jual      = (double)$_POST['harga_jual'];
        $supplier_id     = (int)$_POST['supplier_id'];

        if (empty($kode_sparepart) || empty($nama_sparepart) || $supplier_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique kode
        $check = mysqli_query($conn, "SELECT sparepart_id FROM sparepart WHERE kode_sparepart = '$kode_sparepart'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Kode sparepart sudah ada']);
            exit();
        }

        $query = "INSERT INTO sparepart (kode_sparepart, nama_sparepart, satuan, stok, stok_minimum, harga_beli, harga_jual, supplier_id) 
                  VALUES ('$kode_sparepart', '$nama_sparepart', '$satuan', $stok, $stok_minimum, $harga_beli, $harga_jual, $supplier_id)";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Sparepart berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $sparepart_id   = (int)$_POST['sparepart_id'];
        $kode_sparepart = sanitize($conn, $_POST['kode_sparepart']);
        $nama_sparepart = sanitize($conn, $_POST['nama_sparepart']);
        $satuan         = sanitize($conn, $_POST['satuan']);
        $stok           = (int)$_POST['stok'];
        $stok_minimum   = (int)$_POST['stok_minimum'];
        $harga_beli     = (double)$_POST['harga_beli'];
        $harga_jual     = (double)$_POST['harga_jual'];
        $supplier_id    = (int)$_POST['supplier_id'];

        if (empty($kode_sparepart) || empty($nama_sparepart) || $supplier_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique kode (exclude current)
        $check = mysqli_query($conn, "SELECT sparepart_id FROM sparepart WHERE kode_sparepart = '$kode_sparepart' AND sparepart_id != $sparepart_id");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Kode sparepart sudah ada']);
            exit();
        }

        $query = "UPDATE sparepart SET kode_sparepart='$kode_sparepart', nama_sparepart='$nama_sparepart', satuan='$satuan', 
                  stok=$stok, stok_minimum=$stok_minimum, harga_beli=$harga_beli, harga_jual=$harga_jual, supplier_id=$supplier_id 
                  WHERE sparepart_id = $sparepart_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Sparepart berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Check if sparepart has transaction history
        $check = mysqli_query($conn, "SELECT detail_id FROM transaksi_servis_detail WHERE sparepart_id = $id LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Tidak bisa hapus, sparepart sudah memiliki riwayat transaksi']);
            exit();
        }

        $result = mysqli_query($conn, "DELETE FROM sparepart WHERE sparepart_id = $id");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Sparepart berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
