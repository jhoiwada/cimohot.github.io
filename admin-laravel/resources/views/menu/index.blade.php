@extends('layouts.admin')

@section('title', 'Katalog Menu')
@section('header_title', 'Katalog & Stok Menu')
@section('page_id', 'menu')

@section('content')
<div class="content-card">
    <div class="card-header-flex">
        <!-- Category Tabs -->
        <div class="filter-tabs" id="menu-category-tabs">
            <button class="filter-tab-btn menu-cat-all active">Semua</button>
        </div>

        <div style="display:flex;gap:12px;align-items:center;">
            <!-- Search -->
            <div class="search-box-wrapper" style="max-width:280px;">
                <ion-icon name="search-outline"></ion-icon>
                <input type="text" id="menu-search-input" class="form-control" placeholder="Cari menu...">
            </div>

            <!-- Add Button -->
            <button class="btn btn-primary" id="add-menu-btn">
                <ion-icon name="add-circle-outline"></ion-icon>
                <span>Tambah Menu</span>
            </button>
        </div>
    </div>

    <!-- Menu Cards Grid -->
    <div class="menu-grid" id="menu-grid">
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">
            Memuat daftar menu...
        </div>
    </div>
</div>

<!-- Modal Form Add/Edit Menu -->
<div class="modal-overlay" id="menu-form-modal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3 id="menu-modal-title">Tambah Menu Baru</h3>
            <button class="modal-close-btn menu-modal-close">&times;</button>
        </div>

        <form id="menu-form" enctype="multipart/form-data">
            <input type="hidden" id="menu-id" name="menu_id">

            <div class="form-group">
                <label class="form-label" for="menu-name">Nama Menu</label>
                <input type="text" id="menu-name" name="name" class="form-control" placeholder="Contoh: Cimol Bojot Keju Mozzarella" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label" for="menu-price">Harga (Rp)</label>
                    <input type="number" id="menu-price" name="price" class="form-control" placeholder="15000" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="menu-stock">Jumlah Stok</label>
                    <input type="number" id="menu-stock" name="stock" class="form-control" placeholder="50" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="menu-category">Kategori Menu</label>
                <select id="menu-category" name="category" class="form-control" required>
                    <option value="Original">Original</option>
                    <option value="Pedas">Pedas</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Camilan">Camilan</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="menu-description">Deskripsi</label>
                <textarea id="menu-description" name="description" class="form-control" rows="3" placeholder="Deskripsikan cita rasa atau komposisi menu..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="menu-image">Foto Menu</label>
                <input type="file" id="menu-image" name="image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                <small id="menu-image-hint" style="color:var(--text-muted);font-size:0.75rem;margin-top:4px;">* Upload foto menu beresolusi jelas (maksimal 3MB).</small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary menu-modal-close">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save-menu">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>
@endsection
