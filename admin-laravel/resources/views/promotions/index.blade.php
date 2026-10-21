@extends('layouts.admin')

@section('title', 'Voucher & Promo')
@section('header_title', 'Voucher & Promo')
@section('page_id', 'promotions')

@section('content')
<div class="content-card">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title">
                <ion-icon name="ticket-outline" style="color:var(--brand-gold);"></ion-icon>
                Manajemen Voucher & Kode Diskon
            </h3>
            <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">
                Buat dan kelola kode promo diskon untuk meningkatkan penjualan Cimohot.
            </p>
        </div>

        <button class="btn btn-primary" id="add-promo-btn">
            <ion-icon name="add-circle-outline"></ion-icon>
            <span>Buat Promo Baru</span>
        </button>
    </div>

    <!-- Promotions Cards Grid -->
    <div class="promo-grid" id="promotions-grid">
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">
            Memuat daftar promo...
        </div>
    </div>
</div>

<!-- Modal Form Promo -->
<div class="modal-overlay" id="promo-modal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3 id="promo-modal-title">Buat Voucher Promo Baru</h3>
            <button class="modal-close-btn promo-modal-close">&times;</button>
        </div>

        <form id="promo-form">
            <input type="hidden" id="promo-id" name="promo_id">

            <div class="form-group">
                <label class="form-label" for="promo-code">Kode Voucher (Huruf Besar)</label>
                <input type="text" id="promo-code" name="code" class="form-control mono" placeholder="CONTOH: CIMOHOT20" style="text-transform:uppercase;font-weight:700;" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label" for="promo-discount-type">Tipe Diskon</label>
                    <select id="promo-discount-type" name="discount_type" class="form-control" required>
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="promo-discount-value">Nilai Diskon</label>
                    <input type="number" id="promo-discount-value" name="discount_value" class="form-control" placeholder="20 atau 5000" min="1" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label" for="promo-min-order">Minimal Belanja (Rp)</label>
                    <input type="number" id="promo-min-order" name="min_order" class="form-control" placeholder="0" min="0">
                </div>

                <div class="form-group">
                    <label class="form-label" for="promo-expires-at">Tanggal Kedaluwarsa</label>
                    <input type="date" id="promo-expires-at" name="expires_at" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="promo-description">Deskripsi Singkat</label>
                <textarea id="promo-description" name="description" class="form-control" rows="2" placeholder="Contoh: Diskon 20% khusus pelanggan setia..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary promo-modal-close">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save-promo">Simpan Voucher</button>
            </div>
        </form>
    </div>
</div>
@endsection
