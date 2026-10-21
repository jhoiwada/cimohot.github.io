<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Cimohot Command Center</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- External Libs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body data-page="@yield('page_id', 'dashboard')">
    <!-- Backdrop for mobile sidebar -->
    <div id="sidebar-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.65); z-index:95; backdrop-filter:blur(4px);"></div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="brand-link">
                    <img src="/assets/images/logo cimohot.png" alt="Cimohot Logo" class="brand-logo-img" style="width:42px;height:42px;object-fit:contain;border-radius:50%;filter:drop-shadow(0 2px 10px rgba(255,61,46,0.35));flex-shrink:0;">
                    <div>
                        <div class="brand-title" style="font-size:1.3rem;font-weight:800;letter-spacing:-0.5px;">Cimohot<span>.</span></div>
                    </div>
                </a>
                <div class="sidebar-header-actions" style="display:flex;align-items:center;gap:8px;">
                    <span class="brand-tag">v2.0</span>
                    <button class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Tutup Menu">
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="sidebar-body">
                <div class="nav-section-title">Menu Utama</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <ion-icon name="grid-outline"></ion-icon>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                            <ion-icon name="cart-outline"></ion-icon>
                            <span>Pesanan</span>
                            <span class="nav-badge" id="sidebar-orders-badge" style="display:none;"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.menu') }}" class="nav-link {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
                            <ion-icon name="restaurant-outline"></ion-icon>
                            <span>Katalog Menu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.customers') }}" class="nav-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                            <ion-icon name="people-outline"></ion-icon>
                            <span>Pelanggan</span>
                        </a>
                    </li>
                </ul>

                <div class="nav-section-title" style="margin-top:14px;">Analitik & Promosi</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.promotions') }}" class="nav-link {{ request()->routeIs('admin.promotions') ? 'active' : '' }}">
                            <ion-icon name="ticket-outline"></ion-icon>
                            <span>Voucher & Promo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                            <ion-icon name="bar-chart-outline"></ion-icon>
                            <span>Laporan Penjualan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        <div class="sidebar-user-role">Super Admin</div>
                    </div>
                </div>

                <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                    <ion-icon name="globe-outline"></ion-icon> Buka Website
                </a>

                <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:4px;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%; justify-content:center;">
                        <ion-icon name="log-out-outline"></ion-icon> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" id="mobile-menu-toggle" aria-label="Toggle Navigation">
                        <ion-icon name="menu-outline"></ion-icon>
                    </button>
                    <div class="topbar-page-meta">
                        <h1>@yield('header_title', 'Dashboard')</h1>
                        <div class="topbar-breadcrumb">
                            <span>Command Center</span>
                            <ion-icon name="chevron-forward-outline" style="font-size:0.7rem;"></ion-icon>
                            <span>@yield('header_title', 'Dashboard')</span>
                        </div>
                    </div>
                </div>

                <div class="topbar-right">
                    <!-- View Website Link -->
                    <a href="{{ route('home') }}" target="_blank" class="topbar-site-link" title="Preview Website Utama">
                        <ion-icon name="globe-outline"></ion-icon> <span>Preview Site</span>
                    </a>

                    <!-- Theme Toggle -->
                    <button class="topbar-btn" id="theme-toggle-btn" title="Ganti Tema (Dark / Light)">
                        <ion-icon name="moon-outline"></ion-icon>
                    </button>

                    <!-- Real-time Notifications Bell -->
                    <div style="position:relative;">
                        <button class="topbar-btn" id="notification-bell-btn" title="Notifikasi Pesanan">
                            <ion-icon name="notifications-outline"></ion-icon>
                            <span class="pulse-badge" id="notification-badge" style="display:none;"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="notification-dropdown">
                            <div class="notification-header">
                                <h4>Notifikasi</h4>
                                <span class="brand-tag" id="notification-count-text">0 Baru</span>
                            </div>
                            <div class="notification-list" id="notification-list">
                                <p style="color:var(--text-muted);font-size:0.85rem;text-align:center;padding:12px;">Memuat notifikasi...</p>
                            </div>
                            <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-subtle);text-align:center;">
                                <a href="{{ route('admin.orders') }}" style="font-size:0.8rem;color:var(--brand-red);font-weight:700;">
                                    Lihat Semua Pesanan &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Admin User Pill (topbar) -->
                    <div class="topbar-user-pill">
                        <div class="topbar-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="topbar-user-name">{{ explode(' ', Auth::user()->name ?? 'Admin')[0] }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" title="Logout" class="topbar-logout-btn">
                                <ion-icon name="log-out-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content Slot -->
            <main class="admin-content">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: "{{ session('success') }}",
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#11151F',
                                color: '#F8FAFC'
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Peringatan',
                                text: "{{ session('error') }}",
                                background: '#11151F',
                                color: '#F8FAFC'
                            });
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
