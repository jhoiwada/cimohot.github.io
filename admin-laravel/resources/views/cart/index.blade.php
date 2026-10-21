<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang Belanja - Cimohot</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="/assets/cart/cart.css?v={{ time() }}">
</head>
<body>
@include('partials.admin_bar')
<header class="cart-header">
    <a href="/#food-menu" class="back-btn">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Lanjut Belanja</span>
    </a>
    <a href="/" class="header-logo">
        <img src="/assets/images/logo cimohot.png" alt="Cimohot" style="width:34px;height:34px;object-fit:contain;border-radius:8px;">
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
const CART_ITEMS   = {!! json_encode($cartItems) !!};
const USER_PROFILE = {!! json_encode($userProfile) !!};
</script>
<script src="/assets/cart/cart.js?v={{ time() }}"></script>
</body>
</html>
