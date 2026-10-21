@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Command Center')
@section('page_id', 'dashboard')

@section('content')
<!-- Top Live Status & Action Bar -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px; padding:14px 20px; background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm);">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; font-weight:700; color:var(--text-primary); background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); padding:6px 14px; border-radius:30px;">
            <span style="width:8px; height:8px; border-radius:50%; background:#22C55E; display:inline-block; box-shadow:0 0 10px #22C55E;"></span>
            <span>LIVE RESTO MONITORING</span>
        </div>
        <span style="color:var(--text-muted); font-size:0.82rem;" id="last-updated-text">Diperbarui: baru saja</span>
    </div>

    <div style="display:flex; align-items:center; gap:10px;">
        <!-- Audio Notification Toggle -->
        <button type="button" id="btn-toggle-sound" class="btn btn-secondary btn-sm" title="Bunyikan nada saat pesanan baru masuk">
            <ion-icon name="volume-high-outline" id="sound-icon" style="color:var(--brand-gold);"></ion-icon>
            <span id="sound-text">Suara: Aktif</span>
        </button>

        <!-- Refresh Button -->
        <button type="button" id="btn-manual-refresh" class="btn btn-secondary btn-sm" title="Perbarui data sekarang">
            <ion-icon name="sync-outline" id="refresh-icon"></ion-icon>
            <span>Refresh</span>
        </button>
    </div>
</div>

<!-- Operational Kitchen Pipeline Bar (3 Interactive Cards) -->
<section style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:18px; margin-bottom:24px;">
    <!-- Pipeline 1: Pending (Action Required) -->
    <div class="content-card pipeline-card" id="pipeline-pending-card" style="border-left:4px solid var(--brand-orange); position:relative; overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="font-size:0.82rem; font-weight:700; color:var(--brand-orange); text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:6px;">
                    <span id="pending-pulse-dot" style="display:none; width:8px; height:8px; border-radius:50%; background:var(--brand-orange); animation: pulse 1s infinite;"></span>
                    <span>Butuh Konfirmasi</span>
                </div>
                <div style="font-size:2.2rem; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary); margin:6px 0;" id="pipeline-pending-count">0</div>
                <div style="font-size:0.78rem; color:var(--text-secondary);">Pesanan baru menunggu diproses dapur</div>
            </div>
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(255,107,53,0.12); color:var(--brand-orange); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                <ion-icon name="alert-circle-outline"></ion-icon>
            </div>
        </div>
        <div style="margin-top:14px; padding-top:12px; border-top:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('admin.orders') }}?status=pending" style="font-size:0.8rem; font-weight:700; color:var(--brand-orange); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <span>Buka Pesanan Pending</span>
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </a>
            <span style="font-size:0.72rem; color:var(--text-muted);" id="pending-status-hint">Semua aman</span>
        </div>
    </div>

    <!-- Pipeline 2: Processing (Kitchen Active) -->
    <div class="content-card pipeline-card" style="border-left:4px solid var(--color-info);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="font-size:0.82rem; font-weight:700; color:var(--color-info); text-transform:uppercase; letter-spacing:0.5px;">
                    Sedang Dimasak
                </div>
                <div style="font-size:2.2rem; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary); margin:6px 0;" id="pipeline-processing-count">0</div>
                <div style="font-size:0.78rem; color:var(--text-secondary);">Antrean pesanan aktif di dapur</div>
            </div>
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(6,182,212,0.12); color:var(--color-info); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                <ion-icon name="flame-outline"></ion-icon>
            </div>
        </div>
        <div style="margin-top:14px; padding-top:12px; border-top:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('admin.orders') }}?status=diproses" style="font-size:0.8rem; font-weight:700; color:var(--color-info); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <span>Pantau Antrean Dapur</span>
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </a>
            <span style="font-size:0.72rem; color:var(--text-muted);">Proses cepat</span>
        </div>
    </div>

    <!-- Pipeline 3: Completed Today -->
    <div class="content-card pipeline-card" style="border-left:4px solid var(--color-success);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="font-size:0.82rem; font-weight:700; color:var(--color-success); text-transform:uppercase; letter-spacing:0.5px;">
                    Selesai Hari Ini
                </div>
                <div style="font-size:2.2rem; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary); margin:6px 0;" id="pipeline-completed-count">0</div>
                <div style="font-size:0.78rem; color:var(--text-secondary);">Total transaksi berhasil diselesaikan</div>
            </div>
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(34,197,94,0.12); color:var(--color-success); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                <ion-icon name="checkmark-done-circle-outline"></ion-icon>
            </div>
        </div>
        <div style="margin-top:14px; padding-top:12px; border-top:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('admin.orders') }}?status=selesai" style="font-size:0.8rem; font-weight:700; color:var(--color-success); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <span>Lihat Riwayat Selesai</span>
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </a>
            <span style="font-size:0.72rem; color:var(--text-muted);">Sukses</span>
        </div>
    </div>
</section>

<!-- KPI Stat Cards -->
<section class="stats-grid">
    <!-- Card 1: Revenue -->
    <div class="stat-card revenue-card">
        <div class="stat-top">
            <div class="stat-icon revenue">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
            <span class="stat-pill positive" id="stat-revenue-diff">+0% vs kemarin</span>
        </div>
        <div class="stat-content">
            <p>Pendapatan Hari Ini</p>
            <h3 id="stat-revenue">Memuat...</h3>
            <div class="stat-sub">Total transaksi berhasil hari ini</div>
        </div>
    </div>

    <!-- Card 2: Orders -->
    <div class="stat-card orders-card">
        <div class="stat-top">
            <div class="stat-icon orders">
                <ion-icon name="cart-outline"></ion-icon>
            </div>
            <span class="stat-pill positive" id="stat-orders-diff">Hari Ini</span>
        </div>
        <div class="stat-content">
            <p>Pesanan Masuk</p>
            <h3 id="stat-orders">0</h3>
            <div class="stat-sub">Pesanan yang tercatat hari ini</div>
        </div>
    </div>

    <!-- Card 3: New Customers -->
    <div class="stat-card customers-card">
        <div class="stat-top">
            <div class="stat-icon customers">
                <ion-icon name="person-add-outline"></ion-icon>
            </div>
            <span class="stat-pill neutral">Aktif</span>
        </div>
        <div class="stat-content">
            <p>Pelanggan Terdaftar</p>
            <h3 id="stat-customers">0</h3>
            <div class="stat-sub">Akun pelanggan aktif di sistem</div>
        </div>
    </div>

    <!-- Card 4: Top Menu -->
    <div class="stat-card product-card">
        <div class="stat-top">
            <div class="stat-icon product">
                <ion-icon name="trophy-outline"></ion-icon>
            </div>
            <span class="stat-pill positive">Paling Laris</span>
        </div>
        <div class="stat-content">
            <p>Menu Terfavorit</p>
            <h3 id="stat-top-product" style="font-size:1.25rem; font-family:'Plus Jakarta Sans',sans-serif; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">Memuat...</h3>
            <div class="stat-sub" id="stat-top-product-sold">0 porsi terjual</div>
        </div>
    </div>
</section>

<!-- Main Visualizations Grid -->
<section class="charts-grid-main">
    <!-- Line Chart: Revenue Trend -->
    <div class="content-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title">
                    <ion-icon name="trending-up-outline" style="color:var(--brand-red);"></ion-icon>
                    Tren Pendapatan Penjualan
                </h3>
                <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Analisis pendapatan harian secara berkala</p>
            </div>
            <div class="chart-filters">
                <button type="button" class="chart-filter-btn active" data-days="7">7 Hari</button>
                <button type="button" class="chart-filter-btn" data-days="14">14 Hari</button>
                <button type="button" class="chart-filter-btn" data-days="30">30 Hari</button>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <!-- Donut Chart: Category Sales -->
    <div class="content-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title">
                    <ion-icon name="pie-chart-outline" style="color:var(--brand-gold);"></ion-icon>
                    Kategori Menu
                </h3>
                <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Distribusi penjualan menu</p>
            </div>
        </div>
        <div class="chart-wrapper" style="height:320px;">
            <canvas id="categoryDonutChart"></canvas>
        </div>
    </div>
</section>

<!-- Operations Split: Low Stock Warning & Cash Flow Breakdown -->
<section style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
    <!-- Low Stock Alert Card -->
    <div class="content-card" style="border-top:3px solid var(--brand-red);">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                    <ion-icon name="warning-outline" style="color:var(--brand-red); font-size:1.3rem;"></ion-icon>
                    <span>Peringatan Stok Menipis</span>
                </h3>
                <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Menu dengan sisa &le; 5 porsi perlu restock</p>
            </div>
            <a href="{{ route('admin.menu') }}" class="btn btn-secondary btn-sm">
                Semua Menu &rarr;
            </a>
        </div>

        <div id="low-stock-container" style="margin-top:14px; min-height:140px; display:flex; flex-direction:column; gap:10px;">
            <div style="text-align:center; padding:28px; color:var(--text-muted);">
                <div class="spinner"></div> Memeriksa stok menu...
            </div>
        </div>
    </div>

    <!-- Cash & Payment Breakdown Card -->
    <div class="content-card" style="border-top:3px solid var(--brand-gold);">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                    <ion-icon name="cash-outline" style="color:var(--brand-gold); font-size:1.3rem;"></ion-icon>
                    <span>Arus Kas & Pembayaran Hari Ini</span>
                </h3>
                <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Rekapitulasi metode bayar tunai vs online</p>
            </div>
            <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">
                Laporan &rarr;
            </a>
        </div>

        <div style="margin-top:16px;">
            <!-- Split Progress Bar -->
            <div style="height:12px; width:100%; border-radius:10px; background:var(--bg-input); overflow:hidden; display:flex; margin-bottom:18px;">
                <div id="bar-pay-transfer" style="width:50%; background:linear-gradient(90deg, #06B6D4, #3B82F6); transition:width 0.6s ease;" title="Transfer / QRIS"></div>
                <div id="bar-pay-cash" style="width:50%; background:linear-gradient(90deg, #F5A623, #FF6B35); transition:width 0.6s ease;" title="Tunai / COD"></div>
            </div>

            <!-- 2-Box Summary -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-md); border:1px solid var(--border-subtle);">
                    <div style="font-size:0.75rem; font-weight:700; color:#38BDF8; text-transform:uppercase; display:flex; align-items:center; gap:6px;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#38BDF8;"></span>
                        <span>Transfer / QRIS</span>
                    </div>
                    <div style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:6px 0;" id="pay-transfer-rev">Rp 0</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);" id="pay-transfer-count">0 transaksi (0%)</div>
                </div>

                <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-md); border:1px solid var(--border-subtle);">
                    <div style="font-size:0.75rem; font-weight:700; color:#FB923C; text-transform:uppercase; display:flex; align-items:center; gap:6px;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#FB923C;"></span>
                        <span>Tunai / COD</span>
                    </div>
                    <div style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:6px 0;" id="pay-cash-rev">Rp 0</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);" id="pay-cash-count">0 transaksi (0%)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lower Grid: Recent Orders & Quick Actions -->
<section style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Recent Orders Table -->
    <div class="content-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title">
                    <ion-icon name="time-outline" style="color:var(--color-info);"></ion-icon>
                    Pesanan Terbaru Masuk
                </h3>
                <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Daftar transaksi yang baru saja diterima</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="btn btn-secondary btn-sm">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Menu / Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody id="recent-orders-body">
                    <tr>
                        <td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">
                            <div class="spinner"></div> Memuat pesanan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Shortcuts Card -->
    <div class="content-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <h3 class="card-title" style="margin-bottom:16px;">
                <ion-icon name="flash-outline" style="color:var(--brand-orange);"></ion-icon>
                Aksi Cepat
            </h3>
            <p style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:20px;">
                Pintasan fitur administrasi yang paling sering digunakan.
            </p>

            <div style="display:flex;flex-direction:column;gap:12px;">
                <a href="{{ route('admin.menu') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:12px 16px;">
                    <ion-icon name="add-circle-outline" style="color:var(--brand-red);font-size:1.2rem;"></ion-icon>
                    <span>Kelola Katalog & Tambah Menu</span>
                </a>
                <a href="{{ route('admin.promotions') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:12px 16px;">
                    <ion-icon name="ticket-outline" style="color:var(--brand-gold);font-size:1.2rem;"></ion-icon>
                    <span>Buat Voucher Promo Baru</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="btn btn-secondary" style="justify-content:flex-start;padding:12px 16px;">
                    <ion-icon name="document-text-outline" style="color:var(--color-success);font-size:1.2rem;"></ion-icon>
                    <span>Export Laporan Penjualan PDF</span>
                </a>
            </div>
        </div>

        <div style="background:var(--bg-input);padding:14px;border-radius:var(--radius-md);border:1px solid var(--border-subtle);margin-top:20px;">
            <div style="font-size:0.8rem;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
                <ion-icon name="shield-checkmark-outline" style="color:var(--color-success);"></ion-icon>
                Sistem Siap Operasional
            </div>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">
                Database terhubung ke cimohot_db & auto-polling 20s aktif.
            </div>
        </div>
    </div>
</section>

<!-- Modal: Quick Order Detail View -->
<div id="quick-order-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:var(--radius-lg); width:100%; max-width:620px; max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-xl); padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
            <div>
                <h3 style="font-size:1.2rem; font-weight:800; color:var(--text-primary); margin:0;" id="modal-order-title">Detail Pesanan #0</h3>
                <span style="font-size:0.78rem; color:var(--text-muted);" id="modal-order-time">-</span>
            </div>
            <button type="button" id="btn-close-order-modal" style="background:none; border:none; color:var(--text-muted); font-size:1.5rem; cursor:pointer;">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <div id="modal-order-content">
            <div style="text-align:center; padding:24px;"><div class="spinner"></div> Memuat rincian pesanan...</div>
        </div>
    </div>
</div>

<!-- Modal: Quick Restock -->
<div id="quick-restock-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:var(--radius-lg); width:100%; max-width:440px; box-shadow:var(--shadow-xl); padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
            <h3 style="font-size:1.15rem; font-weight:800; color:var(--text-primary); margin:0;" id="restock-modal-title">Perbarui Stok Menu</h3>
            <button type="button" id="btn-close-restock-modal" style="background:none; border:none; color:var(--text-muted); font-size:1.5rem; cursor:pointer;">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <form id="quick-restock-form">
            <input type="hidden" id="restock-menu-id">
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">Nama Menu</label>
                <input type="text" id="restock-menu-name" class="form-control" readonly style="opacity:0.8;">
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label" for="restock-stock-val" style="font-size:0.85rem; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">Jumlah Stok Baru (Porsi)</label>
                <input type="number" id="restock-stock-val" class="form-control" min="0" required style="font-size:1.1rem; font-weight:700;">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" id="btn-cancel-restock">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-restock">
                    <ion-icon name="checkmark-outline"></ion-icon>
                    <span>Simpan Stok</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
