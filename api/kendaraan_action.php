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

    case 'get_clients':
        $result = mysqli_query($conn, "SELECT client_id, nama FROM client ORDER BY nama");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get':
        $id = (int)$_GET['id'];
        $result = mysqli_query($conn, "SELECT * FROM vehicle WHERE vehicle_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'add':
        $client_id       = (int)$_POST['client_id'];
        $no_polisi       = sanitize($conn, $_POST['no_polisi']);
        $merk            = sanitize($conn, $_POST['merk']);
        $tipe            = sanitize($conn, $_POST['tipe']);
        $cc              = (int)$_POST['cc'];
        $tipe_kendaraan  = sanitize($conn, $_POST['tipe_kendaraan']);
        $tahun           = (int)$_POST['tahun'];
        $no_rangka       = sanitize($conn, $_POST['no_rangka']);
        $no_mesin        = sanitize($conn, $_POST['no_mesin']);

        if (empty($no_polisi) || empty($merk) || empty($tipe) || $client_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique no_polisi
        $check = mysqli_query($conn, "SELECT vehicle_id FROM vehicle WHERE no_polisi = '$no_polisi'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'No. Polisi sudah terdaftar']);
            exit();
        }

        $query = "INSERT INTO vehicle (client_id, no_polisi, merk, tipe, cc, tipe_kendaraan, tahun, no_rangka, no_mesin) 
                  VALUES ($client_id, '$no_polisi', '$merk', '$tipe', $cc, '$tipe_kendaraan', $tahun, '$no_rangka', '$no_mesin')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Kendaraan berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
        }
        break;

    case 'update':
        $vehicle_id      = (int)$_POST['vehicle_id'];
        $client_id       = (int)$_POST['client_id'];
        $no_polisi       = sanitize($conn, $_POST['no_polisi']);
        $merk            = sanitize($conn, $_POST['merk']);
        $tipe            = sanitize($conn, $_POST['tipe']);
        $cc              = (int)$_POST['cc'];
        $tipe_kendaraan  = sanitize($conn, $_POST['tipe_kendaraan']);
        $tahun           = (int)$_POST['tahun'];
        $no_rangka       = sanitize($conn, $_POST['no_rangka']);
        $no_mesin        = sanitize($conn, $_POST['no_mesin']);

        if (empty($no_polisi) || empty($merk) || empty($tipe) || $client_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique no_polisi (exclude current)
        $check = mysqli_query($conn, "SELECT vehicle_id FROM vehicle WHERE no_polisi = '$no_polisi' AND vehicle_id != $vehicle_id");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'No. Polisi sudah terdaftar']);
            exit();
        }

        $query = "UPDATE vehicle SET client_id=$client_id, no_polisi='$no_polisi', merk='$merk', tipe='$tipe', 
                  cc=$cc, tipe_kendaraan='$tipe_kendaraan', tahun=$tahun, no_rangka='$no_rangka', no_mesin='$no_mesin' 
                  WHERE vehicle_id = $vehicle_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Kendaraan berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Check if vehicle has active service transactions
        $check = mysqli_query($conn, "SELECT registration_id FROM transaksi_pendaftaran WHERE vehicle_id = $id AND status IN ('Registered','InProgress')");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Tidak bisa hapus, kendaraan memiliki transaksi aktif']);
            exit();
        }

        $result = mysqli_query($conn, "DELETE FROM vehicle WHERE vehicle_id = $id");
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Kendaraan berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
