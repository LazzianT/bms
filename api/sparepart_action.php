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

function respondJson($payload)
{
    echo json_encode($payload);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {

    case 'list':
        $search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
        $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit  = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $offset = ($page - 1) * $limit;
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
        $stock_filter = isset($_GET['stock_filter']) ? $_GET['stock_filter'] : '';

        $where = [];
        if ($search !== '') {
            $where[] = "(sp.kode_sparepart LIKE '%$search%' OR sp.nama_sparepart LIKE '%$search%')";
        }
        if ($supplier_id > 0) {
            $where[] = "sp.supplier_id = $supplier_id";
        }
        if ($stock_filter === 'habis') {
            $where[] = "sp.stok <= 0";
        } elseif ($stock_filter === 'menipis') {
            $where[] = "sp.stok > 0 AND sp.stok <= sp.stok_minimum";
        } elseif ($stock_filter === 'aman') {
            $where[] = "sp.stok > sp.stok_minimum";
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM sparepart sp $whereClause");
        if (!$totalQuery) {
            respondJson(['success' => false, 'message' => 'Gagal mengambil data: ' . mysqli_error($conn)]);
        }

        $totalData  = (int) mysqli_fetch_assoc($totalQuery)['jml'];
        $totalPages = max(1, (int) ceil($totalData / $limit));

        $query  = "SELECT sp.*, s.nama as nama_supplier FROM sparepart sp 
                   LEFT JOIN supplier s ON sp.supplier_id = s.supplier_id 
                   $whereClause 
                   ORDER BY sp.sparepart_id DESC 
                   LIMIT $limit OFFSET $offset";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            respondJson(['success' => false, 'message' => 'Gagal mengambil data: ' . mysqli_error($conn)]);
        }

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        respondJson([
            'success'    => true,
            'data'       => $rows,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'total_data' => $totalData,
                'total_page' => $totalPages,
            ],
        ]);
        break;

    case 'get_suppliers':
        $result = mysqli_query($conn, "SELECT supplier_id, nama FROM supplier ORDER BY nama");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        respondJson(['success' => true, 'data' => $data]);
        break;

    case 'get':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            respondJson(['success' => false, 'message' => 'ID tidak valid']);
        }

        $result = mysqli_query($conn, "SELECT * FROM sparepart WHERE sparepart_id = $id");
        if (mysqli_num_rows($result) > 0) {
            respondJson(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            respondJson(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $kode_sparepart  = sanitize($conn, isset($_POST['kode_sparepart']) ? $_POST['kode_sparepart'] : '');
        $nama_sparepart  = sanitize($conn, isset($_POST['nama_sparepart']) ? $_POST['nama_sparepart'] : '');
        $satuan          = sanitize($conn, isset($_POST['satuan']) ? $_POST['satuan'] : '');
        $stok            = isset($_POST['stok']) ? (int)$_POST['stok'] : 0;
        $stok_minimum    = isset($_POST['stok_minimum']) ? (int)$_POST['stok_minimum'] : 0;
        $harga_beli      = isset($_POST['harga_beli']) ? (double)$_POST['harga_beli'] : 0;
        $harga_jual      = isset($_POST['harga_jual']) ? (double)$_POST['harga_jual'] : 0;
        $supplier_id     = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;

        if (empty($kode_sparepart) || empty($nama_sparepart) || $supplier_id == 0) {
            respondJson(['success' => false, 'message' => 'Semua field wajib diisi']);
        }

        // Check unique kode
        $check = mysqli_query($conn, "SELECT sparepart_id FROM sparepart WHERE kode_sparepart = '$kode_sparepart'");
        if (mysqli_num_rows($check) > 0) {
            respondJson(['success' => false, 'message' => 'Kode sparepart sudah ada']);
        }

        $query = "INSERT INTO sparepart (kode_sparepart, nama_sparepart, satuan, stok, stok_minimum, harga_beli, harga_jual, supplier_id) 
                  VALUES ('$kode_sparepart', '$nama_sparepart', '$satuan', $stok, $stok_minimum, $harga_beli, $harga_jual, $supplier_id)";
        $result = mysqli_query($conn, $query);

        if ($result) {
            respondJson(['success' => true, 'message' => 'Sparepart berhasil ditambahkan']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $sparepart_id   = isset($_POST['sparepart_id']) ? (int)$_POST['sparepart_id'] : 0;
        $kode_sparepart = sanitize($conn, isset($_POST['kode_sparepart']) ? $_POST['kode_sparepart'] : '');
        $nama_sparepart = sanitize($conn, isset($_POST['nama_sparepart']) ? $_POST['nama_sparepart'] : '');
        $satuan         = sanitize($conn, isset($_POST['satuan']) ? $_POST['satuan'] : '');
        $stok           = isset($_POST['stok']) ? (int)$_POST['stok'] : 0;
        $stok_minimum   = isset($_POST['stok_minimum']) ? (int)$_POST['stok_minimum'] : 0;
        $harga_beli     = isset($_POST['harga_beli']) ? (double)$_POST['harga_beli'] : 0;
        $harga_jual     = isset($_POST['harga_jual']) ? (double)$_POST['harga_jual'] : 0;
        $supplier_id    = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;

        if ($sparepart_id <= 0) {
            respondJson(['success' => false, 'message' => 'ID sparepart tidak valid']);
        }

        if (empty($kode_sparepart) || empty($nama_sparepart) || $supplier_id == 0) {
            respondJson(['success' => false, 'message' => 'Semua field wajib diisi']);
        }

        // Check unique kode (exclude current)
        $check = mysqli_query($conn, "SELECT sparepart_id FROM sparepart WHERE kode_sparepart = '$kode_sparepart' AND sparepart_id != $sparepart_id");
        if (mysqli_num_rows($check) > 0) {
            respondJson(['success' => false, 'message' => 'Kode sparepart sudah ada']);
        }

        $query = "UPDATE sparepart SET kode_sparepart='$kode_sparepart', nama_sparepart='$nama_sparepart', satuan='$satuan', 
                  stok=$stok, stok_minimum=$stok_minimum, harga_beli=$harga_beli, harga_jual=$harga_jual, supplier_id=$supplier_id 
                  WHERE sparepart_id = $sparepart_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            respondJson(['success' => true, 'message' => 'Sparepart berhasil diupdate']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            respondJson(['success' => false, 'message' => 'ID tidak valid']);
        }

        // Check if sparepart has transaction history
        $check = mysqli_query($conn, "SELECT detail_id FROM transaksi_servis_detail WHERE sparepart_id = $id LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            respondJson(['success' => false, 'message' => 'Tidak bisa hapus, sparepart sudah memiliki riwayat transaksi']);
        }

        $result = mysqli_query($conn, "DELETE FROM sparepart WHERE sparepart_id = $id");
        if ($result) {
            respondJson(['success' => true, 'message' => 'Sparepart berhasil dihapus']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        respondJson(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
