<?php
require_once __DIR__ . '/config.php';

function filterDbSettings(array $settings): array {
    return array_filter($settings, static fn($value) => $value !== null && $value !== '');
}

$candidates = [];

$envSettings = filterDbSettings([
    'host'     => getenv('DB_HOST') ?: null,
    'port'     => getenv('DB_PORT') ?: null,
    'username' => getenv('DB_USERNAME') ?: null,
    'password' => getenv('DB_PASSWORD') ?: null,
    'database' => getenv('DB_DATABASE') ?: null,
]);
if (!empty($envSettings)) {
    $candidates[] = array_merge([
        'host' => 'localhost',
        'port' => '3306',
        'username' => 'root',
        'password' => '',
        'database' => 'garage_management',
    ], $envSettings);
}

$localConfigFile = __DIR__ . '/local.php';
if (file_exists($localConfigFile)) {
    $localSettings = require $localConfigFile;
    if (is_array($localSettings)) {
        $candidates[] = array_merge([
            'host' => 'localhost',
            'port' => '8889',
            'username' => 'root',
            'password' => 'root',
            'database' => 'garage_management',
        ], filterDbSettings($localSettings));
    }
}

$candidates[] = [
    'host'     => 'localhost',
    'port'     => '8889',
    'username' => 'root',
    'password' => 'root',
    'database' => 'garage_management',
];

$candidates[] = [
    'host'     => 'sql206.infinityfree.com',
    'port'     => '3306',
    'username' => 'if0_42550511',
    'password' => 'Ib1fcmrl1Jk2',
    'database' => 'if0_42550511_bms',
];

$conn = false;
$lastError = 'Konfigurasi database tidak valid';
foreach ($candidates as $settings) {
    $conn = @mysqli_connect(
        $settings['host'],
        $settings['username'],
        $settings['password'],
        $settings['database'],
        (int)$settings['port']
    );
    if ($conn) {
        break;
    }
    $lastError = mysqli_connect_error();
}

if (!$conn) {
    die("Koneksi gagal: " . $lastError);
}
mysqli_set_charset($conn, "utf8mb4");
?>
