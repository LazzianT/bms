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

        $where = '';
        if ($search !== '') {
            $where = "WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%' OR email LIKE '%$search%'";
        }

        $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as jml FROM client $where");
        if (!$totalQuery) {
            respondJson(['success' => false, 'message' => 'Gagal mengambil data: ' . mysqli_error($conn)]);
        }

        $totalData  = (int) mysqli_fetch_assoc($totalQuery)['jml'];
        $totalPages = max(1, (int) ceil($totalData / $limit));

        $query  = "SELECT * FROM client $where ORDER BY client_id DESC LIMIT $limit OFFSET $offset";
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

    case 'get':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            respondJson(['success' => false, 'message' => 'ID tidak valid']);
        }

        $result = mysqli_query($conn, "SELECT * FROM client WHERE client_id = $id");
        if (mysqli_num_rows($result) > 0) {
            respondJson(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            respondJson(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $nama     = sanitize($conn, isset($_POST['nama']) ? $_POST['nama'] : '');
        $telepon  = sanitize($conn, isset($_POST['telepon']) ? $_POST['telepon'] : '');
        $email    = sanitize($conn, isset($_POST['email']) ? $_POST['email'] : '');
        $alamat   = sanitize($conn, isset($_POST['alamat']) ? $_POST['alamat'] : '');
        $tanggal  = date('Y-m-d');

        if (empty($nama) || empty($telepon)) {
            respondJson(['success' => false, 'message' => 'Nama dan Telepon wajib diisi']);
        }

        $query = "INSERT INTO client (nama, alamat, telepon, email, tanggal_daftar) 
                  VALUES ('$nama', '$alamat', '$telepon', '$email', '$tanggal')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            respondJson(['success' => true, 'message' => 'Customer berhasil ditambahkan']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $id      = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $nama    = sanitize($conn, isset($_POST['nama']) ? $_POST['nama'] : '');
        $telepon = sanitize($conn, isset($_POST['telepon']) ? $_POST['telepon'] : '');
        $email   = sanitize($conn, isset($_POST['email']) ? $_POST['email'] : '');
        $alamat  = sanitize($conn, isset($_POST['alamat']) ? $_POST['alamat'] : '');

        if ($id <= 0) {
            respondJson(['success' => false, 'message' => 'ID customer tidak valid']);
        }

        if (empty($nama) || empty($telepon)) {
            respondJson(['success' => false, 'message' => 'Nama dan Telepon wajib diisi']);
        }

        $query = "UPDATE client SET nama='$nama', telepon='$telepon', email='$email', alamat='$alamat' 
                  WHERE client_id = $id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            respondJson(['success' => true, 'message' => 'Customer berhasil diupdate']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            respondJson(['success' => false, 'message' => 'ID tidak valid']);
        }

        $result = mysqli_query($conn, "DELETE FROM client WHERE client_id = $id");

        if ($result) {
            respondJson(['success' => true, 'message' => 'Customer berhasil dihapus']);
        } else {
            respondJson(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        respondJson(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
