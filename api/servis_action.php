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

    // ==================== DATA LOOKUP ====================

    case 'get_registration':
        $id = (int)$_GET['id'];
        $result = mysqli_query($conn, "
            SELECT tp.*, c.nama as nama_client, v.no_polisi, v.merk, v.tipe, v.cc,
                   m.nama as nama_mekanik, m.spesialis,
                   ts.trans_id, ts.status_servis, ts.total_jasa, ts.total_sparepart, ts.grand_total,
                   ts.bayar, ts.kembali, ts.metode_bayar, ts.user_kasir
            FROM transaksi_pendaftaran tp
            LEFT JOIN client c ON tp.client_id = c.client_id
            LEFT JOIN vehicle v ON tp.vehicle_id = v.vehicle_id
            LEFT JOIN mekanik m ON tp.mekanik_id = m.mekanik_id
            LEFT JOIN transaksi_servis ts ON tp.registration_id = ts.registration_id
            WHERE tp.registration_id = $id
        ");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;

    case 'get_vehicles':
        $client_id = (int)$_GET['client_id'];
        $result = mysqli_query($conn, "SELECT vehicle_id, no_polisi, merk, tipe FROM vehicle WHERE client_id = $client_id ORDER BY no_polisi");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get_sparepart':
        $q = isset($_GET['q']) ? sanitize($conn, $_GET['q']) : '';
        $where = $q ? "WHERE (kode_sparepart LIKE '%$q%' OR nama_sparepart LIKE '%$q%') AND stok > 0" : "WHERE stok > 0";
        $result = mysqli_query($conn, "SELECT sparepart_id, kode_sparepart, nama_sparepart, satuan, harga_jual, stok FROM sparepart $where ORDER BY nama_sparepart LIMIT 20");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get_available_mekanik':
        $result = mysqli_query($conn, "
            SELECT m.mekanik_id, m.nama, m.spesialis
            FROM mekanik m
            WHERE m.mekanik_id NOT IN (
                SELECT ts.mekanik_id FROM transaksi_servis ts
                WHERE ts.status_servis = 'Dikerjakan'
                AND ts.mekanik_id IS NOT NULL
            )
            ORDER BY m.nama
        ");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    // ==================== PENJUALAN SPAREPART (tanpa servis) ====================

    case 'save_penjualan_sp':
        $client_id   = (int)$_POST['client_id'];
        $vehicle_id  = (int)$_POST['vehicle_id'];
        $metode      = sanitize($conn, $_POST['metode_bayar']);
        $bayar       = (double)$_POST['bayar'];
        $spList      = json_decode($_POST['sparepart'], true) ?: [];

        if ($client_id == 0 || $vehicle_id == 0 || empty($spList)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit();
        }

        $now = date('Y-m-d H:i:s');
        mysqli_begin_transaction($conn);
        try {
            // Calculate totals
            $totalSp = 0;
            foreach ($spList as $s) {
                $sp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_jual FROM sparepart WHERE sparepart_id = " . (int)$s['sparepart_id']));
                $totalSp += ($sp['harga_jual'] * $s['qty']);
            }
            $kembali = $bayar - $totalSp;
            $user = $_SESSION['username'];

            // Registration (directly Completed)
            mysqli_query($conn, "INSERT INTO transaksi_pendaftaran (client_id, vehicle_id, keluhan, status, tanggal_daftar, catatan) VALUES ($client_id, $vehicle_id, 'Pembelian Sparepart', 'Completed', '$now', 'Penjualan sparepart langsung')");
            $reg_id = mysqli_insert_id($conn);

            // Transaction (directly Selesai Lunas)
            mysqli_query($conn, "INSERT INTO transaksi_servis (tanggal, client_id, vehicle_id, registration_id, keluhan, status_servis, total_jasa, total_sparepart, grand_total, bayar, kembali, metode_bayar, user_kasir) VALUES ('$now', $client_id, $vehicle_id, $reg_id, 'Pembelian Sparepart', 'Selesai Lunas', 0, $totalSp, $totalSp, $bayar, $kembali, '$metode', '$user')");
            $trans_id = mysqli_insert_id($conn);

            // Sparepart details + kurangi stok
            foreach ($spList as $s) {
                $spId = (int)$s['sparepart_id'];
                $qty = (int)$s['qty'];
                $sp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_jual FROM sparepart WHERE sparepart_id = $spId"));
                $harga = $sp['harga_jual'];
                $sub = $harga * $qty;
                mysqli_query($conn, "INSERT INTO transaksi_servis_detail (trans_id, sparepart_id, qty, harga, subtotal) VALUES ($trans_id, $spId, $qty, $harga, $sub)");
                mysqli_query($conn, "UPDATE sparepart SET stok = stok - $qty WHERE sparepart_id = $spId");
            }

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Penjualan sparepart berhasil']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
        break;

    // ==================== REGISTRATION ====================

    case 'save_wizard':
        $client_id  = (int)$_POST['client_id'];
        $vehicle_id = (int)$_POST['vehicle_id'];
        $mekanik_id = (int)$_POST['mekanik_id'];
        $keluhan    = sanitize($conn, $_POST['keluhan']);
        $catatan    = sanitize($conn, $_POST['catatan']);

        if ($client_id == 0 || $vehicle_id == 0 || empty($keluhan)) {
            echo json_encode(['success' => false, 'message' => 'Customer, kendaraan, dan keluhan wajib diisi']);
            exit();
        }

        $now = date('Y-m-d H:i:s');
        mysqli_begin_transaction($conn);
        try {
            $mekVal = $mekanik_id > 0 ? $mekanik_id : 'NULL';
            mysqli_query($conn, "INSERT INTO transaksi_pendaftaran (client_id, vehicle_id, keluhan, mekanik_id, status, tanggal_daftar, catatan) VALUES ($client_id, $vehicle_id, '$keluhan', $mekVal, 'Registered', '$now', '$catatan')");
            $reg_id = mysqli_insert_id($conn);

            mysqli_query($conn, "INSERT INTO transaksi_servis (tanggal, client_id, vehicle_id, mekanik_id, registration_id, keluhan, status_servis, total_jasa, total_sparepart, grand_total, bayar, kembali) VALUES ('$now', $client_id, $vehicle_id, $mekVal, $reg_id, '$keluhan', 'Menunggu', 0, 0, 0, 0, 0)");

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Pendaftaran servis berhasil disimpan']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
        break;

    case 'save_wizard_edit':
        $reg_id     = (int)$_POST['registration_id'];
        $client_id  = (int)$_POST['client_id'];
        $vehicle_id = (int)$_POST['vehicle_id'];
        $mekanik_id = (int)$_POST['mekanik_id'];
        $keluhan    = sanitize($conn, $_POST['keluhan']);
        $catatan    = sanitize($conn, $_POST['catatan']);

        mysqli_begin_transaction($conn);
        try {
            $mekVal = $mekanik_id > 0 ? $mekanik_id : 'NULL';
            mysqli_query($conn, "UPDATE transaksi_pendaftaran SET client_id=$client_id, vehicle_id=$vehicle_id, keluhan='$keluhan', mekanik_id=$mekVal, catatan='$catatan' WHERE registration_id=$reg_id");

            mysqli_query($conn, "UPDATE transaksi_servis SET client_id=$client_id, vehicle_id=$vehicle_id, mekanik_id=$mekVal, keluhan='$keluhan' WHERE registration_id=$reg_id");

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Pendaftaran servis berhasil diupdate']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
        break;

    case 'start':
        $reg_id     = (int)$_POST['registration_id'];
        $mekanik_id = (int)$_POST['mekanik_id'];

        if ($mekanik_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Pilih mekanik terlebih dahulu']);
            exit();
        }

        $reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi_pendaftaran WHERE registration_id = $reg_id"));
        if (!$reg) {
            echo json_encode(['success' => false, 'message' => 'Pendaftaran tidak ditemukan']);
            exit();
        }
        if ($reg['status'] != 'Registered') {
            echo json_encode(['success' => false, 'message' => 'Pendaftaran sudah diproses']);
            exit();
        }

        $now = date('Y-m-d H:i:s');

        // Update pendaftaran
        mysqli_query($conn, "UPDATE transaksi_pendaftaran SET status = 'InProgress', mekanik_id = $mekanik_id, tanggal_mulai = '$now' WHERE registration_id = $reg_id");

        // Update transaksi_servis
        mysqli_query($conn, "UPDATE transaksi_servis SET mekanik_id = $mekanik_id, status_servis = 'Dikerjakan' WHERE registration_id = $reg_id");

        echo json_encode(['success' => true, 'message' => 'Berhasil dimulai']);
        break;

    case 'cancel_registration':
        $reg_id = (int)$_POST['registration_id'];

        $reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM transaksi_pendaftaran WHERE registration_id = $reg_id"));
        if ($reg['status'] != 'Registered') {
            echo json_encode(['success' => false, 'message' => 'Hanya pendaftaran Antrean yang bisa dibatalkan']);
            exit();
        }

        // Hapus transaksi_servis dan detailnya
        $ts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT trans_id FROM transaksi_servis WHERE registration_id = $reg_id"));
        if ($ts) {
            mysqli_query($conn, "DELETE FROM transaksi_servis_jasa WHERE trans_id = {$ts['trans_id']}");
            mysqli_query($conn, "DELETE FROM transaksi_servis_detail WHERE trans_id = {$ts['trans_id']}");
            mysqli_query($conn, "DELETE FROM transaksi_servis WHERE trans_id = {$ts['trans_id']}");
        }
        mysqli_query($conn, "DELETE FROM transaksi_pendaftaran WHERE registration_id = $reg_id");

        echo json_encode(['success' => true, 'message' => 'Pendaftaran dibatalkan']);
        break;

    case 'add_sparepart_batch':
        $trans_id = (int)$_POST['trans_id'];
        $items = json_decode($_POST['items'], true);

        if (empty($items) || !is_array($items)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada sparepart yang dipilih']);
            exit();
        }

        $errors = [];
        $added = 0;

        foreach ($items as $item) {
            $sparepart_id = (int)$item['sparepart_id'];
            $qty = (int)$item['qty'];

            if ($qty <= 0) continue;

            $sp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sparepart WHERE sparepart_id = $sparepart_id"));
            if (!$sp) {
                $errors[] = "Sparepart ID $sparepart_id tidak ditemukan";
                continue;
            }
            if ($sp['stok'] < $qty) {
                $errors[] = "{$sp['nama_sparepart']} stok tidak cukup (stok: {$sp['stok']})";
                continue;
            }

            $harga = $sp['harga_jual'];
            $subtotal = $qty * $harga;

            mysqli_query($conn, "INSERT INTO transaksi_servis_detail (trans_id, sparepart_id, qty, harga, subtotal)
                VALUES ($trans_id, $sparepart_id, $qty, $harga, $subtotal)");
            $added++;
        }

        if ($added > 0) {
            recalcTotal($conn, $trans_id);
        }

        $msg = "$added sparepart berhasil ditambahkan";
        if (!empty($errors)) {
            $msg .= ". Peringatan: " . implode('; ', $errors);
        }
        echo json_encode(['success' => $added > 0, 'message' => $msg, 'added' => $added, 'errors' => $errors]);
        break;

    case 'delete_sparepart':
        $detail_id = (int)$_POST['detail_id'];
        $trans_id = (int)$_POST['trans_id'];

        mysqli_query($conn, "DELETE FROM transaksi_servis_detail WHERE detail_id = $detail_id");
        recalcTotal($conn, $trans_id);

        echo json_encode(['success' => true, 'message' => 'Sparepart dihapus']);
        break;

    // ==================== JASA ====================

    case 'add_jasa':
        $trans_id  = (int)$_POST['trans_id'];
        $nama_jasa = sanitize($conn, $_POST['nama_jasa']);
        $harga     = (double)$_POST['harga'];
        $qty       = max(1, (int)$_POST['qty']);

        if (empty($nama_jasa) || $harga <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nama jasa dan harga wajib diisi']);
            exit();
        }

        $subtotal = $qty * $harga;

        $ins = "INSERT INTO transaksi_servis_jasa (trans_id, nama_jasa, harga, qty, subtotal)
                VALUES ($trans_id, '$nama_jasa', $harga, $qty, $subtotal)";
        $result = mysqli_query($conn, $ins);

        if ($result) {
            recalcTotal($conn, $trans_id);
            echo json_encode(['success' => true, 'message' => 'Jasa berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
        }
        break;

    case 'delete_jasa':
        $detail_id = (int)$_POST['detail_id'];
        $trans_id = (int)$_POST['trans_id'];

        mysqli_query($conn, "DELETE FROM transaksi_servis_jasa WHERE detail_id = $detail_id");
        recalcTotal($conn, $trans_id);

        echo json_encode(['success' => true, 'message' => 'Jasa dihapus']);
        break;

    // ==================== STATUS ====================

    case 'set_selesai':
        $trans_id = (int)$_POST['trans_id'];

        mysqli_query($conn, "UPDATE transaksi_servis SET status_servis = 'Selesai' WHERE trans_id = $trans_id");

        // Update pendaftaran
        $ts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT registration_id FROM transaksi_servis WHERE trans_id = $trans_id"));
        if ($ts) {
            mysqli_query($conn, "UPDATE transaksi_pendaftaran SET status = 'Completed' WHERE registration_id = {$ts['registration_id']}");
        }

        echo json_encode(['success' => true, 'message' => 'Status diubah ke Selesai']);
        break;

    case 'bayar':
        $trans_id    = (int)$_POST['trans_id'];
        $bayar       = (double)$_POST['bayar'];
        $metode      = sanitize($conn, $_POST['metode_bayar']);

        $ts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT grand_total FROM transaksi_servis WHERE trans_id = $trans_id"));
        if ($bayar < $ts['grand_total']) {
            echo json_encode(['success' => false, 'message' => 'Jumlah bayar kurang dari grand total']);
            exit();
        }

        $kembali = $bayar - $ts['grand_total'];
        $user = $_SESSION['username'];

        mysqli_query($conn, "UPDATE transaksi_servis SET bayar = $bayar, kembali = $kembali, metode_bayar = '$metode', user_kasir = '$user', status_servis = 'Selesai Lunas' WHERE trans_id = $trans_id");

        // Update pendaftaran
        $reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT registration_id FROM transaksi_servis WHERE trans_id = $trans_id"));
        if ($reg) {
            mysqli_query($conn, "UPDATE transaksi_pendaftaran SET status = 'Completed' WHERE registration_id = {$reg['registration_id']}");
        }

        // Kurangi stok sparepart
        $details = mysqli_query($conn, "SELECT sparepart_id, qty FROM transaksi_servis_detail WHERE trans_id = $trans_id");
        while ($d = mysqli_fetch_assoc($details)) {
            mysqli_query($conn, "UPDATE sparepart SET stok = stok - {$d['qty']} WHERE sparepart_id = {$d['sparepart_id']}");
        }

        echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil', 'kembali' => $kembali]);
        break;

    // ==================== HELPER ====================

    case 'get_sparepart_detail':
        $trans_id = (int)$_GET['trans_id'];
        $result = mysqli_query($conn, "
            SELECT d.*, s.kode_sparepart, s.nama_sparepart, s.satuan
            FROM transaksi_servis_detail d
            LEFT JOIN sparepart s ON d.sparepart_id = s.sparepart_id
            WHERE d.trans_id = $trans_id
            ORDER BY d.detail_id
        ");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}

// Static helper
function recalcTotal($conn, $trans_id) {
    $sp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(subtotal),0) as total FROM transaksi_servis_detail WHERE trans_id = $trans_id"));
    $jasa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(subtotal),0) as total FROM transaksi_servis_jasa WHERE trans_id = $trans_id"));
    $totalSp = $sp['total'];
    $totalJasa = $jasa['total'];
    $grand = $totalSp + $totalJasa;
    mysqli_query($conn, "UPDATE transaksi_servis SET total_sparepart = $totalSp, total_jasa = $totalJasa, grand_total = $grand WHERE trans_id = $trans_id");
}
?>
