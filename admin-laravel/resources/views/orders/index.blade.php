@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')
@section('header_title', 'Manajemen Pesanan')
@section('page_id', 'orders')

@section('content')
<div class="content-card">
    <div class="card-header-flex">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab-btn orders-filter-tab active" data-status="all">Semua</button>
            <button class="filter-tab-btn orders-filter-tab" data-status="pending">Pending</button>
            <button class="filter-tab-btn orders-filter-tab" data-status="diproses">Diproses</button>
            <button class="filter-tab-btn orders-filter-tab" data-status="selesai">Selesai</button>
            <button class="filter-tab-btn orders-filter-tab" data-status="dibatalkan">Dibatalkan</button>
        </div>

        <!-- Search Box -->
        <div class="search-box-wrapper">
            <ion-icon name="search-outline"></ion-icon>
            <input type="text" id="orders-search-input" class="form-control" placeholder="Cari ID, nama pelanggan, atau no telepon...">
        </div>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Tanggal Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="orders-table-body">
                <tr>
                    <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                        Memuat data pesanan...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="orders-pagination"></div>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="order-detail-modal">
    <div class="modal-box" style="max-width:680px;">
        <div class="modal-box-header">
            <h3>Rincian Lengkap Pesanan</h3>
            <button class="modal-close-btn modal-close-trigger">&times;</button>
        </div>

        <div id="order-detail-content">
            <div style="text-align:center;padding:30px;color:var(--text-muted);">
                Memuat rincian pesanan...
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-trigger">Tutup</button>
        </div>
    </div>
</div>
@endsection
