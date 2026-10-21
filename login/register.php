<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Cimohot</title>
    <meta name="description" content="Daftar akun Cimohot untuk mulai memesan cireng isi montok dengan aneka varian rasa dan level kepedasan favoritmu.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

    <style>
        :root {
            --bg-dark: #0A0A0C;
            --surface-card: #141418;
            --surface-border: rgba(255, 255, 255, 0.08);
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

        body::before {
            content: '';
            position: fixed;
            top: -15%;
            right: -10%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(255, 61, 46, 0.15) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(80px);
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -15%;
            left: -10%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(245, 166, 35, 0.12) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(80px);
        }

        .auth-container {
            width: 100%;
            max-width: 1060px;
            background: rgba(20, 20, 24, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.03);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        /* Form Column */
        .form-side {
            padding: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--surface-card);
        }

        .form-header {
            margin-bottom: 24px;
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

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 7px;
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
            padding: 13px 16px 13px 48px;
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

        /* Password strength meter */
        .strength-meter {
            height: 4px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: var(--transition);
        }

        .strength-text {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 5px;
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
            margin-top: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(255, 61, 46, 0.5);
            background: linear-gradient(135deg, #FF5042 0%, #E62A1B 100%);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .auth-switch {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
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

        /* Right / Brand Column */
        .brand-side {
            background: linear-gradient(145deg, #180D0B 0%, #101015 100%);
            padding: 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-left: 1px solid var(--surface-border);
            overflow: hidden;
        }

        .brand-side::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: radial-gradient(circle at 80% 20%, rgba(245, 166, 35, 0.18) 0%, transparent 60%),
                        radial-gradient(circle at 20% 80%, rgba(255, 61, 46, 0.2) 0%, transparent 55%);
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
            margin-bottom: 28px;
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
            margin-bottom: 20px;
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
            margin-bottom: 14px;
        }

        .brand-headline {
            font-size: 2rem;
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
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .perk-cards {
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .perk-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .perk-icon {
            font-size: 1.4rem;
            color: var(--warm-gold);
        }

        .perk-info h4 {
            font-size: 0.88rem;
            font-weight: 700;
            color: #FFF;
            margin-bottom: 2px;
        }

        .perk-info p {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .brand-bottom {
            position: relative;
            z-index: 2;
            padding-top: 20px;
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

        @media (max-width: 860px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .brand-side {
                display: none;
            }

            .form-side {
                padding: 36px 24px;
            }

            .mobile-brand-header {
                display: flex !important;
                align-items: center;
                gap: 12px;
                margin-bottom: 20px;
            }
        }

        .mobile-brand-header {
            display: none;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Left Side: Registration Form -->
        <div class="form-side">
            <div class="mobile-brand-header">
                <a href="../index.php" class="back-link" style="margin-bottom: 0;">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    <span>Beranda</span>
                </a>
                <div class="brand-title" style="font-size: 1.4rem;">Cimohot<span>.</span></div>
            </div>

            <div class="form-header">
                <h1 class="form-title">Buat Akun Baru</h1>
                <p class="form-subtitle">Daftar sekarang dan nikmati cireng montok favoritmu</p>
            </div>

            <form id="registerForm" novalidate autocomplete="on">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" class="form-input" placeholder="Nama Anda" required autofocus>
                        <ion-icon name="person-outline" class="input-icon"></ion-icon>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" required>
                        <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter" required minlength="6">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <button type="button" class="toggle-password-btn" data-target="password" aria-label="Lihat password">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                    <div class="strength-meter">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text">
                        <span id="strengthLabel">Kekuatan password</span>
                        <span>Min. 6 karakter</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Konfirmasi Kata Sandi</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Ulangi kata sandi" required>
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <button type="button" class="toggle-password-btn" data-target="confirm_password" aria-label="Lihat konfirmasi password">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="registerBtn">
                    <span>Daftar Akun</span>
                    <ion-icon name="person-add-outline"></ion-icon>
                </button>
            </form>

            <div class="auth-switch">
                Sudah punya akun? <a href="login.php">Masuk di Sini</a>
            </div>
        </div>

        <!-- Right Side: Perks & Identity -->
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

                <div class="brand-badge"><ion-icon name="star" style="color:var(--warm-gold);font-size:1.05rem;"></ion-icon> Gabung Sekarang</div>
                <h2 class="brand-headline">Kelezatan Cireng <em>Montok</em> Menunggumu!</h2>
                <p class="brand-desc">Nikmati kemudahan checkout instan, simpan riwayat pesanan, dan dapatkan akses promo khusus pelanggan setia.</p>

                <div class="perk-cards">
                    <div class="perk-card">
                        <div class="perk-icon"><ion-icon name="flash-outline"></ion-icon></div>
                        <div class="perk-info">
                            <h4>Checkout Instan</h4>
                            <p>Data pengiriman tersimpan aman, tinggal sekali klik</p>
                        </div>
                    </div>
                    <div class="perk-card">
                        <div class="perk-icon"><ion-icon name="gift-outline"></ion-icon></div>
                        <div class="perk-info">
                            <h4>Reward & Diskon Spesial</h4>
                            <p>Penawaran khusus untuk setiap pesanan berkala</p>
                        </div>
                    </div>
                    <div class="perk-card">
                        <div class="perk-icon"><ion-icon name="receipt-outline"></ion-icon></div>
                        <div class="perk-info">
                            <h4>Tracking Status Pesanan</h4>
                            <p>Pantau pesanan dari dapur hingga diantar ke tanganmu</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="brand-bottom">
                <div class="brand-tagline">#GakMontokGakEnak</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">
                    © <?php echo date('Y'); ?> Cimohot Kitchen
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('registerBtn');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const strengthBar = document.getElementById('strengthBar');
            const strengthLabel = document.getElementById('strengthLabel');

            // Password toggles
            document.querySelectorAll('.toggle-password-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.target;
                    const input = document.getElementById(targetId);
                    const icon = btn.querySelector('ion-icon');
                    const isPass = input.type === 'password';
                    input.type = isPass ? 'text' : 'password';
                    icon.name = isPass ? 'eye-off-outline' : 'eye-outline';
                });
            });

            // Password strength meter
            passwordInput.addEventListener('input', () => {
                const val = passwordInput.value;
                let score = 0;
                if (val.length >= 6) score++;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val) || /[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                if (val.length === 0) {
                    strengthBar.style.width = '0%';
                    strengthBar.style.backgroundColor = 'transparent';
                    strengthLabel.textContent = 'Kekuatan password';
                    strengthLabel.style.color = 'var(--text-muted)';
                } else if (score <= 1) {
                    strengthBar.style.width = '25%';
                    strengthBar.style.backgroundColor = '#EF4444';
                    strengthLabel.textContent = 'Lemah (tambahkan huruf/angka)';
                    strengthLabel.style.color = '#EF4444';
                } else if (score === 2) {
                    strengthBar.style.width = '55%';
                    strengthBar.style.backgroundColor = '#F59E0B';
                    strengthLabel.textContent = 'Sedang';
                    strengthLabel.style.color = '#F59E0B';
                } else {
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#10B981';
                    strengthLabel.textContent = 'Kuat & Aman';
                    strengthLabel.style.color = '#10B981';
                }
            });

            // Form submit handler
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = passwordInput.value;
                const confirmPassword = confirmInput.value;

                if (!name || !email || !password) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Silakan isi semua field yang tersedia.',
                        background: '#141418',
                        color: '#FAFAFA',
                        confirmButtonColor: '#FF3D2E'
                    });
                    return;
                }

                if (password.length < 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kata Sandi Terlalu Pendek',
                        text: 'Kata sandi minimal harus 6 karakter.',
                        background: '#141418',
                        color: '#FAFAFA',
                        confirmButtonColor: '#FF3D2E'
                    });
                    return;
                }

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kata Sandi Tidak Sama',
                        text: 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dimasukkan.',
                        background: '#141418',
                        color: '#FAFAFA',
                        confirmButtonColor: '#FF3D2E'
                    });
                    return;
                }

                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <ion-icon name="sync-outline" style="display:inline-block; animation: spin 0.8s linear infinite; margin-right: 8px; font-size:1.15rem; vertical-align:middle;"></ion-icon>
                    <span>Mendaftarkan...</span>
                `;

                const formData = new FormData(form);
                formData.append('action', 'register');

                try {
                    const response = await fetch('../api.php', {
                        method: 'POST',
                        body: new URLSearchParams(formData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Pendaftaran Berhasil!',
                            html: '<div style="font-size:0.95rem; color:#D1D5DB; line-height:1.5;">Akun Anda telah aktif. Silakan masuk untuk mulai memesan cireng montok favoritmu.</div>',
                            background: '#141418',
                            color: '#FAFAFA',
                            iconColor: '#22C55E',
                            confirmButtonColor: '#FF3D2E',
                            confirmButtonText: 'Masuk Sekarang'
                        });
                        window.location.href = 'login.php';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pendaftaran Gagal',
                            html: `<div style="font-size:0.95rem; color:#D1D5DB; line-height:1.55;">${result.message || 'Terjadi kesalahan saat mendaftar. Silakan periksa kembali data Anda.'}</div>`,
                            customClass: {
                                popup: 'swal-error-shake'
                            },
                            background: '#141418',
                            color: '#FAFAFA',
                            confirmButtonColor: '#FF3D2E',
                            confirmButtonText: 'Coba Lagi'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Sistem',
                        text: 'Gagal menghubungi server.',
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