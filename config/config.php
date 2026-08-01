<?php
define('BASE_PATH', dirname(__DIR__));

$root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$base = rtrim(str_replace('\\', '/', BASE_PATH), '/');

$baseUrl = '';
if ($root !== '' && strpos($base, $root) === 0) {
    $baseUrl = substr($base, strlen($root));
}
define('BASE_URL', rtrim($baseUrl, '/'));
?>
