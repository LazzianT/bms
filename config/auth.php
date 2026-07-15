<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /bms/auth/login.php");
        exit();
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION['role'], (array)$roles)) {
        header("Location: /bms/dashboard.php?error=akses_ditolak");
        exit();
    }
}

function loginUser($conn, $username, $password) {
    $username = mysqli_real_escape_string($conn, $username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($password === $user['password_hash']) {
            $_SESSION['user_id']     = $user['user_id'];
            $_SESSION['username']    = $user['username'];
            $_SESSION['role']        = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            return true;
        }
    }
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: /bms/auth/login.php");
    exit();
}

function getRoleName($role) {
    $roles = [
        'admin'   => 'Administrator',
        'kasir'   => 'Kasir',
        'manager' => 'Manager'
    ];
    return isset($roles[$role]) ? $roles[$role] : $role;
}
?>
