<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator | Cimohot Command Center</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

    <style>
        .login-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 20%, rgba(255, 61, 46, 0.12) 0%, rgba(7, 9, 14, 1) 70%);
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .login-glow-circle {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 61, 46, 0.18) 0%, rgba(255, 107, 53, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            filter: blur(40px);
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(17, 21, 31, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 40px 36px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 30px rgba(255, 61, 46, 0.15);
            position: relative;
            z-index: 10;
        }

        .login-brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo-badge {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--brand-red) 0%, var(--brand-orange) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: 0 8px 24px var(--brand-red-glow);
            margin-bottom: 16px;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 6px;
        }

        .login-alert {
            background: var(--color-danger-bg);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: var(--color-danger);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-alert ion-icon {
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .login-footer-text {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border-subtle);
            font-size: 0.8rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-glow-circle"></div>

        <div class="login-card">
            <div class="login-brand-header">
                <img src="{{ asset('assets/images/logo cimohot.png') }}" alt="Cimohot" style="width:52px;height:52px;object-fit:contain;border-radius:12px;margin-bottom:4px;">
                <h1 class="login-title">Cimohot<span>.</span></h1>
                <p class="login-subtitle">Command Center &amp; Management Dashboard</p>
            </div>


            @if(session('error'))
                <div class="login-alert">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="login-alert" style="background:var(--color-info-bg);border-color:rgba(6,182,212,0.3);color:var(--color-info);">
                    <ion-icon name="information-circle-outline"></ion-icon>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email_or_phone">Email atau No. Telepon</label>
                    <div class="search-box-wrapper" style="max-width:100%;">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" id="email_or_phone" name="email_or_phone" class="form-control" 
                               value="{{ old('email_or_phone', 'admin@cimohot.com') }}" 
                               placeholder="Contoh: admin@cimohot.com" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="search-box-wrapper" style="max-width:100%;">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Masukkan password admin" required>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--text-secondary); cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:var(--brand-red);"> Ingat Saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; font-size:0.95rem;">
                    <ion-icon name="log-in-outline"></ion-icon> Masuk ke Dashboard
                </button>
            </form>

            <div class="login-footer-text">
                &copy; {{ date('Y') }} Cimohot Street Kitchen. All rights reserved.
                <div style="margin-top:10px;">
                    <a href="http://cimohot.test" style="color:var(--brand-red);font-weight:700;font-size:0.8rem;text-decoration:none;" target="_blank">
                        &larr; Kembali ke Website Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
