<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {

    case 'get':
        $id = (int)$_GET['id'];
        $result = mysqli_query($conn, "SELECT user_id, username, role, nama_lengkap FROM users WHERE user_id = $id");
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => true, 'data' => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        }
        break;

    case 'add':
        $username     = sanitize($conn, $_POST['username']);
        $password     = $_POST['password'];
        $nama_lengkap = sanitize($conn, $_POST['nama_lengkap']);
        $role         = sanitize($conn, $_POST['role']);

        if (empty($username) || empty($password) || empty($nama_lengkap)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique username
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit();
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, password_hash, role, nama_lengkap) VALUES ('$username', '$hashed', '$role', '$nama_lengkap')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'User berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan']);
        }
        break;

    case 'update':
        $user_id      = (int)$_POST['user_id'];
        $username     = sanitize($conn, $_POST['username']);
        $nama_lengkap = sanitize($conn, $_POST['nama_lengkap']);
        $role         = sanitize($conn, $_POST['role']);

        if (empty($username) || empty($nama_lengkap)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit();
        }

        // Check unique username (exclude current)
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE username = '$username' AND user_id != $user_id");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit();
        }

        $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE user_id = $user_id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'User berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update']);
        }
        break;

    case 'reset_password':
        $user_id      = (int)$_POST['user_id'];
        $new_password = $_POST['new_password'];

        if (empty($new_password) || strlen($new_password) < 4) {
            echo json_encode(['success' => false, 'message' => 'Password minimal 4 karakter']);
            exit();
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password_hash = '$hashed' WHERE user_id = $user_id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Password berhasil direset']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal reset password']);
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Cannot delete yourself
        if ($id == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri']);
            exit();
        }

        if (mysqli_query($conn, "DELETE FROM users WHERE user_id = $id")) {
            echo json_encode(['success' => true, 'message' => 'User berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        break;
}
?>
