@extends('layouts.admin')

@section('title', 'Data Pelanggan')
@section('header_title', 'Data Pelanggan')
@section('page_id', 'customers')

@section('content')
<div class="content-card">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title">
                <ion-icon name="people-outline" style="color:var(--color-info);"></ion-icon>
                Daftar Pelanggan Terdaftar
            </h3>
            <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">
                Semua akun pelanggan yang terdaftar di Cimohot beserta ringkasan aktivitas belanja.
            </p>
        </div>

        <div class="search-box-wrapper">
            <ion-icon name="search-outline"></ion-icon>
            <input type="text" id="customer-search-input" class="form-control" placeholder="Cari nama, email, atau no telepon...">
        </div>
    </div>

    <!-- Customers Table -->
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat Pengiriman</th>
                    <th style="text-align:center;">Total Pesanan</th>
                    <th>Total Belanja</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="customers-table-body">
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">
                        Memuat data pelanggan...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Customer Order History -->
<div class="modal-overlay" id="customer-history-modal">
    <div class="modal-box" style="max-width:680px;">
        <div class="modal-box-header">
            <h3 id="customer-history-modal-title">Riwayat Pesanan Pelanggan</h3>
            <button class="modal-close-btn customer-modal-close">&times;</button>
        </div>

        <div id="customer-history-body">
            <div style="text-align:center;padding:24px;color:var(--text-muted);">
                Memuat riwayat transaksi...
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary customer-modal-close">Tutup</button>
        </div>
    </div>
</div>
@endsection
