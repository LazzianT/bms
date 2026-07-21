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

    case 'add':
        $supplier_id  = (int)$_POST['supplier_id'];
        $sparepart_id = (int)$_POST['sparepart_id'];
        $qty          = (int)$_POST['qty'];
        $harga_beli   = (double)$_POST['harga_beli'];
        $keterangan   = sanitize($conn, $_POST['keterangan'] ?? '');
        $total_harga  = $qty * $harga_beli;

        if ($supplier_id == 0 || $sparepart_id == 0 || $qty <= 0 || $harga_beli <= 0) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi dengan benar']);
            exit();
        }

        mysqli_begin_transaction($conn);
        try {
            $query = "INSERT INTO transaksi_pembelian (tanggal, supplier_id, sparepart_id, qty, harga_beli, total_harga, keterangan) 
                      VALUES (NOW(), $supplier_id, $sparepart_id, $qty, $harga_beli, $total_harga, '$keterangan')";
            mysqli_query($conn, $query);

            // Update stok sparepart
            mysqli_query($conn, "UPDATE sparepart SET stok = stok + $qty WHERE sparepart_id = $sparepart_id");

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Pembelian berhasil disimpan, stok diperbarui']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];
        $result = mysqli_query($conn, "DELETE FROM transaksi_pembelian WHERE purchase_id = $id");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
