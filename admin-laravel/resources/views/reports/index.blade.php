@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('header_title', 'Laporan & Rekap Transaksi')
@section('page_id', 'reports')

@section('content')
<div class="content-card" style="margin-bottom:24px;">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title">
                <ion-icon name="filter-outline" style="color:var(--brand-red);"></ion-icon>
                Filter Periode Laporan
            </h3>
            <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">
                Tentukan rentang tanggal untuk menganalisis performa bisnis Cimohot
            </p>
        </div>

        <div style="display:flex;gap:8px;">
            <button class="btn btn-secondary btn-sm report-preset-btn" data-preset="today">Hari Ini</button>
            <button class="btn btn-secondary btn-sm report-preset-btn" data-preset="7days">7 Hari Terakhir</button>
            <button class="btn btn-secondary btn-sm report-preset-btn" data-preset="month">Bulan Ini</button>
        </div>
    </div>

    <!-- Filter Form Controls -->
    <div style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px;">
            <label class="form-label" for="report-start-date">Tanggal Mulai</label>
            <input type="date" id="report-start-date" class="form-control">
        </div>

        <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px;">
            <label class="form-label" for="report-end-date">Tanggal Akhir</label>
            <input type="date" id="report-end-date" class="form-control">
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button class="btn btn-primary" id="btn-generate-report">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Tampilkan Laporan</span>
            </button>
            <button class="btn btn-secondary" id="btn-export-pdf" title="Download dokumen PDF siap cetak">
                <ion-icon name="document-text-outline" style="color:var(--brand-red);"></ion-icon>
                <span>Export PDF</span>
            </button>
            <button class="btn btn-secondary" id="btn-export-excel" title="Download spreadsheet CSV/Excel">
                <ion-icon name="download-outline" style="color:var(--color-success);"></ion-icon>
                <span>Export Excel</span>
            </button>
        </div>
    </div>
</div>

<!-- Report Results Container -->
<div id="report-results-wrapper" style="display:none;">
    <!-- Metric Cards Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-content">
                <p>Total Pendapatan</p>
                <h3 id="rep-revenue" style="color:var(--brand-gold);">Rp 0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <p>Total Pesanan</p>
                <h3 id="rep-orders">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <p>Pesanan Selesai</p>
                <h3 id="rep-completed" style="color:var(--color-success);">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <p>Pesanan Dibatalkan</p>
                <h3 id="rep-cancelled" style="color:var(--color-danger);">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <p>Rata-rata Order</p>
                <h3 id="rep-avg" style="color:var(--color-info);">Rp 0</h3>
            </div>
        </div>
    </div>

    <!-- Top Selling Menu Table in Period -->
    <div class="content-card" style="margin-bottom:24px;">
        <h3 class="card-title" style="margin-bottom:14px;">
            <ion-icon name="star-outline" style="color:var(--brand-gold);"></ion-icon>
            Top 5 Menu Paling Banyak Dipesan
        </h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Nilai Penjualan</th>
                    </tr>
                </thead>
                <tbody id="rep-top-menu-body">
                    <tr><td colspan="4" style="text-align:center;padding:16px;color:var(--text-muted);">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complete Orders List in Period -->
    <div class="content-card">
        <h3 class="card-title" style="margin-bottom:14px;">
            <ion-icon name="receipt-outline" style="color:var(--text-primary);"></ion-icon>
            Daftar Seluruh Transaksi Periode Ini
        </h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody id="rep-orders-body">
                    <tr><td colspan="6" style="text-align:center;padding:16px;color:var(--text-muted);">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
