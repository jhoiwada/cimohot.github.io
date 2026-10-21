<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    die('Akses langsung tidak diizinkan.');
}
$dbHost = 'localhost';
$dbName = 'cimohot_db';
$dbUser = 'root';
$dbPass = '';

$envFile = __DIR__ . '/admin-laravel/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '#')) continue;
        if (str_contains($trimmed, '=')) {
            [$k, $v] = explode('=', $trimmed, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if ($k === 'DB_HOST') $dbHost = $v;
            if ($k === 'DB_DATABASE') $dbName = $v;
            if ($k === 'DB_USERNAME') $dbUser = $v;
            if ($k === 'DB_PASSWORD') $dbPass = $v;
        }
    }
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false, 
        'message' => 'Koneksi ke database gagal. Periksa kembali detail di file db_connect.php. Error: ' . $e->getMessage()
    ]);
    exit;
}
?>