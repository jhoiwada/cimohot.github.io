<?php 
session_start(); 
if (isset($_SESSION['user_id'])) { 
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: ../admin/admin-dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit; 
} 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun | Cimohot</title>
    <meta name="description" content="Masuk ke akun Cimohot untuk menikmati sensasi cireng isi montok pedas nampol dan tracking pesanan Anda.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16x16.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/apple-touch-icon.png?v=2">
    <link rel="shortcut icon" href="../favicon.ico?v=2" type="image/x-icon">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

    <style>
        :root {
            --bg-dark: #0A0A0C;
            --surface-card: #141418;
            --surface-card-hover: #1A1A22;
            --surface-border: rgba(255, 255, 255, 0.08);
            --surface-border-focus: rgba(255, 61, 46, 0.5);
            --primary-red: #FF3D2E;
            --primary-red-hover: #E02B1D;
            --warm-gold: #F5A623;
            --spicy-orange: #FF6B35;
            --text-primary: #FAFAFA;
            --text-secondary: #9CA3AF;
            --text-muted: #6B7280;
            --input-bg: #1B1B22;
            --radius-md: 12px;
            --radius-lg: 18px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes cimohotModalPop {
            0% { opacity: 0; transform: scale(0.88) translateY(18px); }
            65% { opacity: 1; transform: scale(1.02) translateY(-2px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes cimohotModalHide {
            0% { opacity: 1; transform: scale(1) translateY(0); }
            100% { opacity: 0; transform: scale(0.92) translateY(14px); }
        }

        @keyframes cimohotShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        /* SweetAlert2 Theme for Cimohot Auth */
        .swal2-popup:not(.swal2-toast) {
            background: rgba(20, 20, 26, 0.96) !important;
            backdrop-filter: blur(28px) !important;
            -webkit-backdrop-filter: blur(28px) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 24px !important;
            box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.85), 0 0 45px rgba(255, 61, 46, 0.18) !important;
            padding: 32px 28px !important;
            color: #FAFAFA !important;
        }

        .swal2-popup:not(.swal2-toast).swal2-show {
            animation: cimohotModalPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }

        .swal2-popup:not(.swal2-toast).swal2-hide {
            animation: cimohotModalHide 0.2s cubic-bezier(0.4, 0, 1, 1) forwards !important;
        }

        .swal2-popup.swal-error-shake {
            animation: cimohotShake 0.45s ease-in-out !important;
            border-color: rgba(255, 61, 46, 0.45) !important;
            box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.85), 0 0 50px rgba(255, 61, 46, 0.28) !important;
        }

        .swal2-popup .swal2-title {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 1.35rem !important;
            font-weight: 800 !important;
            color: #FFFFFF !important;
            margin: 12px 0 6px 0 !important;
            letter-spacing: -0.3px !important;
        }

        .swal2-popup .swal2-confirm {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            border-radius: 14px !important;
            padding: 12px 26px !important;
            background: linear-gradient(135deg, #FF3D2E, #E02B1D) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 8px 24px rgba(255, 61, 46, 0.35) !important;
            transition: all 0.25s ease !important;
        }

        .swal2-popup .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 30px rgba(255, 61, 46, 0.5) !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 24px 16px;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 61, 46, 0.15) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(80px);
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245, 166, 35, 0.12) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(80px);
        }

        /* Auth Container */
        .auth-container {
            width: 100%;
            max-width: 1060px;
            background: rgba(20, 20, 24, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.03);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        /* Left Side: Brand Experience */
        .brand-side {
            background: linear-gradient(145deg, #1A0D0B 0%, #111116 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid var(--surface-border);
            overflow: hidden;
        }

        .brand-side::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: radial-gradient(circle at 80% 20%, rgba(255, 61, 46, 0.22) 0%, transparent 55%),
                        radial-gradient(circle at 20% 80%, rgba(245, 166, 35, 0.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .brand-top {
            position: relative;
            z-index: 2;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 8px 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--surface-border);
            border-radius: 30px;
            transition: var(--transition);
            margin-bottom: 32px;
        }

        .back-link:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateX(-3px);
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .brand-logo-img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(255, 61, 46, 0.4);
            box-shadow: 0 0 20px rgba(255, 61, 46, 0.3);
        }

        .brand-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #FFF;
        }

        .brand-title span {
            color: var(--primary-red);
        }

        .brand-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--warm-gold);
            background: rgba(245, 166, 35, 0.12);
            border: 1px solid rgba(245, 166, 35, 0.3);
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 16px;
        }

        .brand-headline {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
            color: #FFFFFF;
        }

        .brand-headline em {
            font-style: normal;
            background: linear-gradient(135deg, #FF6B35 0%, #FF3D2E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-desc {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .brand-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            color: #E2E8F0;
        }

        .feature-icon-box {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 61, 46, 0.15);
            color: var(--primary-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            border: 1px solid rgba(255, 61, 46, 0.25);
        }

        .brand-bottom {
            position: relative;
            z-index: 2;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-tagline {
            font-family: 'Caveat', cursive;
            font-size: 1.3rem;
            color: var(--warm-gold);
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: #34D399;
            background: rgba(52, 211, 153, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #34D399;
            border-radius: 50%;
            box-shadow: 0 0 8px #34D399;
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        /* Right Side: Form Area */
        .form-side {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--surface-card);
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #FFF;
            margin-bottom: 6px;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 0.92rem;
        }

        /* Role Selector Switcher */
        .role-selector {
            background: #111115;
            border: 1px solid var(--surface-border);
            border-radius: 10px;
            padding: 4px;
            display: flex;
            margin-bottom: 24px;
            gap: 4px;
        }

        .role-tab {
            flex: 1;
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 0.88rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
        }

        .role-tab.active {
            background: var(--input-bg);
            color: #FFF;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .role-tab.active ion-icon {
            color: var(--primary-red);
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            font-size: 1.25rem;
            color: var(--text-muted);
            transition: var(--transition);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 14px 16px 14px 48px;
            color: #FFF;
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-red);
            background: #202028;
            box-shadow: 0 0 0 4px rgba(255, 61, 46, 0.15);
        }

        .form-input:focus ~ .input-icon {
            color: var(--primary-red);
        }

        .toggle-password-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            transition: var(--transition);
        }

        .toggle-password-btn:hover {
            color: var(--text-primary);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-red) 0%, #D82213 100%);
            border: none;
            border-radius: var(--radius-md);
            color: #FFF;
            padding: 15px;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(255, 61, 46, 0.35);
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(255, 61, 46, 0.5);
            background: linear-gradient(135deg, #FF5042 0%, #E62A1B 100%);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        .auth-switch {
            text-align: center;
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid var(--surface-border);
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .auth-switch a {
            color: var(--spicy-orange);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            margin-left: 4px;
        }

        .auth-switch a:hover {
            color: var(--primary-red);
            text-decoration: underline;
        }

        /* Mobile Responsive */
        @media (max-width: 860px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .brand-side {
                padding: 32px 24px;
                display: none; /* Keep clean on small devices */
            }

            .form-side {
                padding: 36px 24px;
            }

            .mobile-brand-header {
                display: flex !important;
                align-items: center;
                gap: 12px;
                margin-bottom: 24px;
            }
        }

        .mobile-brand-header {
            display: none;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Left Side: Brand Story -->
        <div class="brand-side">
            <div class="brand-top">
                <a href="../index.php" class="back-link">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    <span>Kembali ke Beranda</span>
                </a>

                <div class="brand-header">
                    <img src="../assets/images/logo%20cimohot.png" alt="Cimohot" class="brand-logo-img" onerror="this.style.display='none'">
                    <div class="brand-title">Cimohot<span>.</span></div>
                </div>

                <div class="brand-badge"><ion-icon name="flame" style="color:var(--primary-red);font-size:1.05rem;"></ion-icon> Street Kitchen Premium</div>
                <h2 class="brand-headline">Sensasi Pedas <em>Nampol</em> di Setiap Gigitan.</h2>
                <p class="brand-desc">Masuk untuk memesan menu favoritmu, pantau status pesanan secara real-time, dan nikmati berbagai promo eksklusif.</p>

                <ul class="brand-features">
                    <li>
                        <div class="feature-icon-box"><ion-icon name="flame"></ion-icon></div>
                        <span>Level kepedasan 1 - 5 bikin nagih</span>
                    </li>
                    <li>
                        <div class="feature-icon-box"><ion-icon name="restaurant"></ion-icon></div>
                        <span>Isian ayam suwir & keju melimpah</span>
                    </li>
                    <li>
                        <div class="feature-icon-box"><ion-icon name="shield-checkmark"></ion-icon></div>
                        <span>Bahan baku segar & 100% higienis</span>
                    </li>
                </ul>
            </div>

            <div class="brand-bottom">
                <div class="brand-tagline">#GakMontokGakEnak</div>
                <div class="status-pill">
                    <span class="status-dot"></span>
                    <span>Dapur Buka</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="form-side">
            <div class="mobile-brand-header">
                <a href="../index.php" class="back-link" style="margin-bottom: 0;">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    <span>Beranda</span>
                </a>
                <div class="brand-title" style="font-size: 1.4rem;">Cimohot<span>.</span></div>
            </div>

            <div class="form-header">
                <?php
                $ctx = $_GET['ctx'] ?? '';
                $redirect = $_GET['redirect'] ?? '';
                if ($ctx === 'cart' || strpos($redirect, 'cart') !== false):
                ?>
                <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:rgba(245,166,35,0.08);border:1px solid rgba(245,166,35,0.22);border-radius:12px;margin-bottom:16px;font-size:0.85rem;color:#F5A623;font-weight:600;">
                    <ion-icon name="cart-outline" style="font-size:1.2rem;flex-shrink:0;"></ion-icon>
                    <span>Login dulu untuk melanjutkan pesananmu ya!</span>
                </div>
                <h1 class="form-title">Masuk ke Akun</h1>
                <p class="form-subtitle">Setelah login, pesananmu langsung diproses</p>
                <?php else: ?>
                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan pesanan</p>
                <?php endif; ?>
            </div>


            <!-- Role Selector Switcher -->
            <div class="role-selector" id="roleSelector">
                <button type="button" class="role-tab active" data-role="user">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>Pelanggan</span>
                </button>
                <button type="button" class="role-tab" data-role="admin">
                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                    <span>Admin</span>
                </button>
            </div>

            <form id="loginForm" autocomplete="on">
                <input type="hidden" name="role" id="roleInput" value="user">

                <div class="form-group">
                    <label class="form-label" for="email_or_phone">Email atau No. WhatsApp</label>
                    <div class="input-wrapper">
                        <input type="text" id="email_or_phone" name="email_or_phone" class="form-input" placeholder="contoh@gmail.com / 0812..." required autofocus>
                        <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan kata sandi" required>
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <button type="button" class="toggle-password-btn" id="togglePasswordBtn" aria-label="Lihat password">
                            <ion-icon name="eye-outline" id="togglePasswordIcon"></ion-icon>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Masuk Sekarang</span>
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
            </form>

            <div class="auth-switch">
                Belum punya akun? <a href="register.php">Daftar Akun Baru</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelector = document.getElementById('roleSelector');
            const roleInput = document.getElementById('roleInput');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            // Role switcher tabs
            roleSelector.querySelectorAll('.role-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    roleSelector.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    const selectedRole = tab.dataset.role;
                    roleInput.value = selectedRole;
                });
            });

            // Password visibility toggle
            togglePasswordBtn.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                togglePasswordIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
            });

            // Form Submit via AJAX to api.php
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <ion-icon name="sync-outline" style="display:inline-block; animation: spin 0.8s linear infinite; margin-right: 8px; font-size:1.15rem; vertical-align:middle;"></ion-icon>
                    <span>Memverifikasi...</span>
                `;

                const formData = new FormData(loginForm);
                formData.append('action', 'login');

                try {
                    const response = await fetch('../api.php', {
                        method: 'POST',
                        body: new URLSearchParams(formData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Selamat Datang!',
                            html: `<div style="font-size:0.95rem; color:#D1D5DB; line-height:1.5;">${result.message || 'Login berhasil, mengalihkan...'}</div>`,
                            background: '#141418',
                            color: '#FAFAFA',
                            iconColor: '#22C55E',
                            timer: 1400,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });

                        const isAdmin = result.data?.user?.role === 'admin';
                        window.location.href = isAdmin ? '../admin/admin-dashboard.php' : '../index.php';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Masuk',
                            html: `
                                <div style="font-size:0.95rem; color:#D1D5DB; line-height:1.55;">
                                    ${result.message || 'Email atau kata sandi yang Anda masukkan tidak sesuai.'}
                                    <div style="margin-top:10px; font-size:0.84rem; color:#9CA3AF;">
                                        Silakan periksa kembali email dan kata sandi Anda.
                                    </div>
                                </div>
                            `,
                            customClass: {
                                popup: 'swal-error-shake'
                            },
                            background: '#141418',
                            color: '#FAFAFA',
                            iconColor: '#FF3D2E',
                            confirmButtonColor: '#FF3D2E',
                            confirmButtonText: 'Coba Lagi'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Sistem',
                        text: 'Terjadi kesalahan saat menghubungi server.',
                        customClass: {
                            popup: 'swal-error-shake'
                        },
                        background: '#141418',
                        color: '#FAFAFA',
                        confirmButtonColor: '#FF3D2E',
                        confirmButtonText: 'Tutup'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            });
        });
    </script>
</body>
</html>
