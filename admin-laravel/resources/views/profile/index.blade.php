<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya | Cimohot</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png?v=2">
    <link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-dark: #0A0A0C;
            --surface-dark: #121217;
            --surface-card: #181820;
            --surface-card-hover: #1F1F2A;
            --surface-border: rgba(255, 255, 255, 0.08);
            --primary-red: #FF3D2E;
            --primary-red-hover: #E02B1D;
            --warm-gold: #F5A623;
            --spicy-orange: #FF6B35;
            --text-primary: #FAFAFA;
            --text-secondary: #9CA3AF;
            --text-muted: #6B7280;
            --input-bg: #111116;
            --radius-md: 12px;
            --radius-lg: 18px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
            line-height: 1.6;
        }

        /* Ambient Glow */
        .ambient-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            filter: blur(100px);
            opacity: 0.15;
        }
        .glow-top-left { top: -100px; left: -100px; background: var(--primary-red); }
        .glow-bottom-right { bottom: -100px; right: -100px; background: var(--warm-gold); }

        /* Navigation Header */
        .header-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10, 10, 12, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--surface-border);
            padding: 16px 24px;
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FFF;
        }

        .profile-nav-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 50%;
            filter: drop-shadow(0 2px 10px rgba(255, 61, 46, 0.4));
            flex-shrink: 0;
            display: block;
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .logo-text span { color: var(--primary-red); }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-header-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--surface-border);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-header-link:hover {
            color: #FFF;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-logout {
            color: #FF5A5A;
            border-color: rgba(255, 90, 90, 0.2);
            background: rgba(255, 90, 90, 0.06);
        }

        .btn-logout:hover {
            background: rgba(255, 90, 90, 0.15);
            border-color: rgba(255, 90, 90, 0.4);
            color: #FF7777;
        }

        /* Container & Layout */
        .app-container {
            max-width: 1200px;
            margin: 32px auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* Left Card: Profile & Stats */
        .profile-card {
            background: var(--surface-card);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            padding: 28px;
            position: sticky;
            top: 90px;
        }

        .profile-header {
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--surface-border);
            margin-bottom: 24px;
        }

        .user-avatar-wrap {
            position: relative;
            width: 88px;
            height: 88px;
            margin: 0 auto 16px;
        }

        .user-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--spicy-orange) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFF;
            box-shadow: 0 10px 25px rgba(255, 61, 46, 0.35);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        .badge-verified {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 26px;
            height: 26px;
            background: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFF;
            font-size: 0.9rem;
            border: 2px solid var(--surface-card);
        }

        .profile-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #FFF;
            margin-bottom: 4px;
        }

        .profile-email {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 12px;
            word-break: break-all;
        }

        .member-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 166, 35, 0.12);
            border: 1px solid rgba(245, 166, 35, 0.3);
            color: var(--warm-gold);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Profile Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-item {
            background: var(--input-bg);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 14px;
            text-align: center;
        }

        .stat-val {
            font-size: 1.25rem;
            font-weight: 800;
            color: #FFF;
            margin-bottom: 2px;
        }

        .stat-val.money {
            font-size: 1rem;
            color: var(--warm-gold);
        }

        .stat-lbl {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Sidebar Navigation Menu */
        .account-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            border-radius: var(--radius-md);
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-secondary);
            font-size: 0.92rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            width: 100%;
            text-align: left;
        }

        .nav-btn ion-icon {
            font-size: 1.25rem;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #FFF;
        }

        .nav-btn.active {
            background: rgba(255, 61, 46, 0.12);
            color: #FFF;
            border-color: rgba(255, 61, 46, 0.3);
        }

        .nav-btn.active ion-icon {
            color: var(--primary-red);
        }

        /* Right Side: Tab Contents */
        .main-card {
            background: var(--surface-card);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-lg);
            padding: 36px;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease forwards;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pane-header {
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--surface-border);
        }

        .pane-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #FFF;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .pane-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Profile Form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box ion-icon {
            position: absolute;
            left: 16px;
            font-size: 1.2rem;
            color: var(--text-muted);
            pointer-events: none;
            transition: var(--transition);
        }

        .form-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius-md);
            padding: 13px 16px 13px 46px;
            color: #FFF;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .form-input:focus {
            border-color: var(--primary-red);
            background: #1B1B24;
            box-shadow: 0 0 0 3px rgba(255, 61, 46, 0.15);
        }

        .form-input:focus ~ ion-icon {
            color: var(--primary-red);
        }

        .form-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #15151B;
        }

        textarea.form-input {
            padding-left: 16px;
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary-red) 0%, #D82213 100%);
            border: none;
            border-radius: var(--radius-md);
            color: #FFF;
            padding: 13px 32px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(255, 61, 46, 0.35);
            transition: var(--transition);
        }

        .btn-save:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 61, 46, 0.45);
        }

        .btn-save:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        /* Orders Table & Cards */
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-orders ion-icon {
            font-size: 3.5rem;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .empty-orders h3 {
            font-size: 1.2rem;
            color: #FFF;
            margin-bottom: 8px;
        }

        .empty-orders p {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .orders-table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--surface-border);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }

        .orders-table th {
            background: #111116;
            color: var(--text-secondary);
            font-weight: 600;
            text-align: left;
            padding: 14px 18px;
            border-bottom: 1px solid var(--surface-border);
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }

        .orders-table td {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #E2E8F0;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .order-id-badge {
            font-family: monospace;
            font-weight: 700;
            color: var(--spicy-orange);
            background: rgba(255, 107, 53, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .order-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(245, 166, 35, 0.12);
            color: #F5A623;
            border: 1px solid rgba(245, 166, 35, 0.3);
        }

        .status-diproses {
            background: rgba(56, 189, 248, 0.12);
            color: #38BDF8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .status-selesai {
            background: rgba(34, 197, 94, 0.12);
            color: #22C55E;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-dibatalkan {
            background: rgba(239, 68, 68, 0.12);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .order-amount {
            font-weight: 700;
            color: #FFF;
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .app-container {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 20px;
            }

            .profile-card {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .main-card {
                padding: 24px 20px;
            }
        }

        @media (max-width: 600px) {
            .header-bar {
                padding: 12px 0;
            }

            .header-inner {
                padding: 0 14px;
            }

            .main-card {
                padding: 20px 14px;
                border-radius: var(--radius-md);
            }

            .profile-card {
                padding: 24px 16px;
                border-radius: var(--radius-md);
            }

            .pane-title {
                font-size: 1.35rem;
            }

            .orders-table {
                min-width: 560px;
            }

            .orders-table th, .orders-table td {
                padding: 12px 14px;
            }

            .stat-item {
                padding: 10px 8px;
            }

            .stat-val {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

    @include('partials.admin_bar')

    <div class="ambient-glow glow-top-left"></div>
    <div class="ambient-glow glow-bottom-right"></div>

    <!-- Header -->
    <header class="header-bar">
        <div class="header-inner">
            <a href="/" class="logo-wrap">
                <img src="/assets/images/logo-cimohot.png" alt="Cimohot Logo" class="profile-nav-logo">
                <div class="logo-text">Cimohot<span>.</span></div>
            </a>

            <div class="header-actions">
                <a href="/logout" class="btn-header-link btn-logout">
                    <ion-icon name="log-out-outline"></ion-icon>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="app-container">
        <!-- Sidebar Profile Card -->
        <aside class="profile-card">
            <div class="profile-header">
                <div class="user-avatar-wrap">
                    <div class="user-avatar" id="avatarLetter">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="badge-verified">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </div>
                </div>
                <h2 class="profile-name" id="sidebarUserName">{{ $user->name }}</h2>
                <p class="profile-email">{{ $user->email }}</p>
                <div class="member-badge">
                    <ion-icon name="shield-checkmark"></ion-icon>
                    <span>Member Sejak {{ $join_date }}</span>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-val">{{ $total_orders }}</div>
                    <div class="stat-lbl">Total Pesanan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val money">Rp {{ number_format($total_spent, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Total Belanja</div>
                </div>
            </div>

            <nav class="account-nav">
                <button type="button" class="nav-btn active" data-tab="profil">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>Profil & Alamat</span>
                </button>
                <button type="button" class="nav-btn" data-tab="riwayat">
                    <ion-icon name="receipt-outline"></ion-icon>
                    <span>Riwayat Pesanan</span>
                </button>
                <a href="index.php#menu-section" class="nav-btn">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>Pesan Lagi</span>
                </a>
            </nav>
        </aside>

        <!-- Right Content Card -->
        <main class="main-card">
            <!-- Tab Profil -->
            <div id="tab-profil" class="tab-pane active">
                <div class="pane-header">
                    <h1 class="pane-title">Pengaturan Profil</h1>
                    <p class="pane-subtitle">Perbarui informasi kontak dan alamat pengiriman Anda</p>
                </div>

                <form id="profileForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <div class="input-box">
                                <input type="text" name="name" id="name" class="form-input" value="{{ $user->name }}" required>
                                <ion-icon name="person-outline"></ion-icon>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Nomor WhatsApp / HP</label>
                            <div class="input-box">
                                <input type="tel" name="phone" id="phone" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('phone', $user->phone ?? '') }}">
                                <ion-icon name="call-outline"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email (Akun)</label>
                        <div class="input-box">
                            <input type="email" id="email" class="form-input" value="{{ $user->email }}" disabled>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                        <small style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px; display: block;">Email digunakan sebagai identifikasi login dan tidak dapat diubah.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Pengiriman Utama</label>
                        <div class="input-box">
                            <textarea name="address" id="address" class="form-input" rows="4" placeholder="Tuliskan alamat lengkap beserta patokan rumah untuk kurir...">{{ old('address', $user->address ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save" id="saveProfileBtn">
                            <ion-icon name="save-outline"></ion-icon>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Riwayat -->
            <div id="tab-riwayat" class="tab-pane">
                <div class="pane-header">
                    <h1 class="pane-title">Riwayat Pesanan</h1>
                    <p class="pane-subtitle">Daftar semua transaksi yang pernah Anda buat di Cimohot</p>
                </div>

                @if($orders->isEmpty())
                    <div class="empty-orders">
                        <ion-icon name="receipt-outline"></ion-icon>
                        <h3>Belum Ada Pesanan</h3>
                        <p>Anda belum pernah melakukan pemesanan cireng montok di Cimohot.</p>
                        <a href="/#food-menu" class="btn-save" style="text-decoration:none;">
                            <span>Pilih Menu Sekarang</span>
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                @else
                    <div class="orders-table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID Order</th>
                                    <th>Waktu Pesan</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $status_raw = strtolower(trim($order->status));
                                        $status_class = 'status-pending';
                                        if (str_contains($status_raw, 'selesai')) {
                                            $status_class = 'status-selesai';
                                        } elseif (str_contains($status_raw, 'proses')) {
                                            $status_class = 'status-diproses';
                                        } elseif (str_contains($status_raw, 'batal')) {
                                            $status_class = 'status-dibatalkan';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="order-id-badge">#{{ $order->order_id }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500; color: #FFF;">{{ date('d M Y', strtotime($order->order_date)) }}</div>
                                            <small style="color: var(--text-muted); font-size: 0.78rem;">{{ date('H:i', strtotime($order->order_date)) }} WIB</small>
                                        </td>
                                        <td>
                                            <span class="order-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="order-status-badge {{ $status_class }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navButtons = document.querySelectorAll('.account-nav .nav-btn[data-tab]');
            const tabPanes = document.querySelectorAll('.tab-pane');
            const profileForm = document.getElementById('profileForm');
            const saveBtn = document.getElementById('saveProfileBtn');
            const sidebarUserName = document.getElementById('sidebarUserName');
            const avatarLetter = document.getElementById('avatarLetter');

            // Tab switching logic
            const switchTab = (tabName) => {
                navButtons.forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.tab === tabName);
                });

                tabPanes.forEach(pane => {
                    pane.classList.toggle('active', pane.id === `tab-${tabName}`);
                });
            };

            navButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabName = btn.dataset.tab;
                    switchTab(tabName);
                    history.replaceState(null, '', `#${tabName}`);
                });
            });

            // Read hash on page load
            const hash = window.location.hash.replace('#', '');
            if (hash === 'riwayat') {
                switchTab('riwayat');
            }

            // AJAX Save Profile
            profileForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const originalBtnContent = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML = `
                    <span style="display:inline-block; animation: spin 1s linear infinite; margin-right: 6px;">⏳</span>
                    <span>Menyimpan...</span>
                `;

                const formData = new FormData(profileForm);
                formData.append('action', 'update_user_profile');

                try {
                    const response = await fetch('/api.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Profil Diperbarui!',
                            text: data.message || 'Perubahan data berhasil disimpan.',
                            background: '#141418',
                            color: '#FAFAFA',
                            iconColor: '#22C55E',
                            confirmButtonColor: '#FF3D2E'
                        });

                        const newName = document.getElementById('name').value.trim();
                        sidebarUserName.textContent = newName;
                        if (newName.length > 0) {
                            avatarLetter.textContent = newName.charAt(0).toUpperCase();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: data.message || 'Terjadi kesalahan saat menyimpan profil.',
                            background: '#141418',
                            color: '#FAFAFA',
                            confirmButtonColor: '#FF3D2E'
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Sistem',
                        text: 'Gagal menghubungi server.',
                        background: '#141418',
                        color: '#FAFAFA',
                        confirmButtonColor: '#FF3D2E'
                    });
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalBtnContent;
                }
            });
        });
    </script>
</body>
</html>