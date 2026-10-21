<?php
/**
 * Cimohot Admin Router & SSO Bridge
 * Seamlessly transitions admin from the native frontend to the new Laravel Command Center
 */
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$timestamp = time();
$secret = 'CIMOHOT_SECURE_ADMIN_SSO_KEY_2026';
$sig = hash_hmac('sha256', $userId . '|' . $timestamp, $secret);

// Check if running on Laragon virtual host (cimohot.test) or direct localhost/IP
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (str_contains($host, 'cimohot.test')) {
    $targetUrl = "http://cimohot-admin.test/sso-login?uid={$userId}&ts={$timestamp}&sig={$sig}";
} elseif (str_contains($host, ':8000')) {
    $targetUrl = "http://127.0.0.1:8000/sso-login?uid={$userId}&ts={$timestamp}&sig={$sig}";
} else {
    $targetUrl = "../admin-laravel/public/sso-login?uid={$userId}&ts={$timestamp}&sig={$sig}";
}

header("Location: {$targetUrl}");
exit;