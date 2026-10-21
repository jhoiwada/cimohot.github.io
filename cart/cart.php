<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode('cart/cart.php');
    header("Location: ../login/login.php?redirect=$redirect&ctx=cart");
    exit;
}
require_once '../db_connect.php';
$user_id = $_SESSION['user_id'];
$cartItems = [];
$query = "SELECT c.menu_id, c.quantity, m.name, m.price, m.category, m.image FROM cart c JOIN menu m ON c.menu_id = m.menu_id WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        'id'        => $row['menu_id'],
        'name'      => htmlspecialchars($row['name']),
        'price'     => 'Rp ' . number_format($row['price'], 0, ',', '.'),
        'price_raw' => (float)$row['price'],
        'category'  => htmlspecialchars($row['category']),
        'image'     => htmlspecialchars($row['image']),
        'quantity'  => (int)$row['quantity'],
    ];
}
$stmt_user = $conn->prepare("SELECT name, phone, address FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$userProfile = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang Belanja - Cimohot</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<link rel="stylesheet" href="cart.css?v=<?php echo time(); ?>">
</head>
<body>
<?php require_once dirname(__DIR__) . '/_admin_bar.php'; ?>
<header class="cart-header">
    <a href="../index.php#food-menu" class="back-btn">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Lanjut Belanja</span>
    </a>
    <a href="../index.php" class="header-logo">
        <div class="logo-mark">Ci</div>
        <div class="logo-text">Cimohot<span>.</span></div>
    </a>
    <span class="secure-badge">
        <ion-icon name="shield-checkmark"></ion-icon> Checkout Aman
    </span>
</header>
<div class="progress-bar-wrap">
    <div class="stepper">
        <div class="step-item active" id="step1">
            <div class="step-circle">1</div>
            <div class="step-label">Keranjang</div>
        </div>
        <div class="step-item" id="step2">
            <div class="step-circle">2</div>
            <div class="step-label">Pengiriman</div>
        </div>
        <div class="step-item" id="step3">
            <div class="step-circle">3</div>
            <div class="step-label">Konfirmasi</div>
        </div>
    </div>
</div>
<div class="page-wrap">
    <div id="cart-app"></div>
</div>
<script>
"use strict";
const CART_ITEMS   = <?php echo json_encode($cartItems); ?>;
const USER_PROFILE = <?php echo json_encode($userProfile); ?>;
</script>
<script src="cart.js?v=<?php echo time(); ?>"></script>
</body>
</html>
