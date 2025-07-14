<?php
// logout.php
session_start(); // Memulai sesi

// Menghapus semua variabel sesi
$_SESSION = array();

// Menghancurkan sesi
session_destroy();

// Mengarahkan kembali ke halaman utama
header("location: index.php");
exit;