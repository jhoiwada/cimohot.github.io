<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cimohot</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
    const API_URL = "../api.php"; 
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header"><h2 class="logo">Cimohot<span>.</span></h2></div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="nav-link active" data-page="dashboard"><ion-icon name="grid-outline"></ion-icon> <span>Dashboard</span></a></li>
                    <li><a href="#" class="nav-link" data-page="orders"><ion-icon name="cart-outline"></ion-icon> <span>Pesanan</span><span class="notification-badge" id="orders-notification"></span></a></li>
                    <li><a href="#" class="nav-link" data-page="menu"><ion-icon name="restaurant-outline"></ion-icon> <span>Menu</span></a></li>
                    <li><a href="#" class="nav-link" data-page="customers"><ion-icon name="people-outline"></ion-icon> <span>Pelanggan</span></a></li>
                    <li><a href="#" class="nav-link" data-page="reports"><ion-icon name="bar-chart-outline"></ion-icon> <span>Laporan</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer"><a href="../logout.php" class="nav-link logout-btn"><ion-icon name="log-out-outline"></ion-icon> <span>Logout</span></a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1 id="page-title">Dashboard</h1>
                <div class="user-profile">
                    <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                </div>
            </header>

            <div id="dashboard-page" class="page-content active"><section class="stats-grid"><div class="stat-card"><div class="stat-icon revenue"><ion-icon name="cash-outline"></ion-icon></div><div class="stat-info"><p>Pendapatan Hari Ini</p><h3 id="stat-revenue">Memuat...</h3></div></div><div class="stat-card"><div class="stat-icon orders"><ion-icon name="cart-outline"></ion-icon></div><div class="stat-info"><p>Pesanan Hari Ini</p><h3 id="stat-orders">Memuat...</h3></div></div><div class="stat-card"><div class="stat-icon customers"><ion-icon name="person-add-outline"></ion-icon></div><div class="stat-info"><p>Pelanggan Baru</p><h3 id="stat-customers">Memuat...</h3></div></div><div class="stat-card"><div class="stat-icon top-product"><ion-icon name="star-outline"></ion-icon></div><div class="stat-info"><p>Menu Terlaris</p><h3 id="stat-top-product">Memuat...</h3></div></div></section><section class="main-data-grid"><div class="content-card chart-container"><h3>Grafik Penjualan (7 Hari Terakhir)</h3><canvas id="salesChart"></canvas></div><div class="content-card"><h3>Pesanan Terbaru</h3><div id="recent-orders-list" class="table-responsive"><p>Memuat pesanan...</p></div></div></section></div>
            <div id="orders-page" class="page-content"><div class="content-card"><div class="card-header"><h3>Semua Pesanan</h3></div><div id="all-orders-table" class="table-responsive"><p>Memuat pesanan...</p></div></div></div>
            <div id="menu-page" class="page-content"><div class="content-card"><div class="card-header"><h3>Manajemen Menu</h3></div>
            <div style="margin-bottom: 16px;">
            <button class="btn btn-primary" id="add-menu-btn">
                <ion-icon name="add-circle-outline"></ion-icon>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
            <div id="menu-grid-container"><p>Memuat menu...</p></div></div></div>
            
            <div id="customers-page" class="page-content">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Manajemen Pelanggan</h3>
                    </div>
                    
                    <div class="search-container">
                        <ion-icon name="search-outline"></ion-icon>
                        <input type="text" id="customer-search-input" placeholder="Cari pelanggan berdasarkan nama atau email...">
                    </div>
                    
                    <div id="all-customers-table" class="table-responsive">
                        <p>Memuat data pelanggan...</p>
                    </div>
                </div>
            </div>

            <div id="reports-page" class="page-content">
                <div class="content-card">
                    <h3>Laporan Penjualan</h3>
                    <div class="report-filter-container">
                        <div class="form-group">
                            <label for="start-date">Tanggal Mulai</label>
                            <input type="date" id="start-date">
                        </div>
                        <div class="form-group">
                            <label for="end-date">Tanggal Akhir</label>
                            <input type="date" id="end-date">
                        </div>
                        <button class="btn btn-primary" id="generate-report-btn">Tampilkan Laporan</button>
                    </div>
                    
                    <div id="report-results" class="report-results-container">
                        <p>Silakan pilih rentang tanggal untuk melihat laporan.</p>
                    </div>
                </div>
            </div>

            <div id="reports-page" class="page-content">
    <div class="content-card">
        <h3>Laporan Penjualan</h3>
        <div class="report-filter-container">
            <div class="form-group">
                <label for="start-date">Tanggal Mulai</label>
                <input type="date" id="start-date">
            </div>
            <div class="form-group">
                <label for="end-date">Tanggal Akhir</label>
                <input type="date" id="end-date">
            </div>
            <button class="btn btn-primary" id="generate-report-btn">Tampilkan Laporan</button>
        </div>
        
        <div id="report-results" class="report-results-container">
            <p>Silakan pilih rentang tanggal untuk melihat laporan.</p>
        </div>
    </div>
</div>
            
        <div id="menu-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header-flex">
            <h3 id="modal-title">Tambah Menu Baru</h3>
            <button id="close-modal-btn" style="background:none; border:none; font-size: 24px; cursor:pointer;">&times;</button>
        </div>
        
        <form id="menu-form" enctype="multipart/form-data">
    <input type="hidden" id="menu-id" name="menu_id">
            
            <div class="form-group">
                <label for="menu-name">Nama Menu</label>
                <input type="text" id="menu-name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="menu-description">Deskripsi</label>
                <textarea id="menu-description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="menu-price">Harga</label>
                <input type="number" id="menu-price" name="price" required>
            </div>

            <div class="form-group">
                <label for="menu-stock">Jumlah Stok</label>
                <input type="number" id="menu-stock" name="stock" required min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="menu-category">Kategori</label>
                <select id="menu-category" name="category" required>
                    <option value="Original">Original</option>
                    <option value="Pedas">Pedas</option>
                    <option value="Minuman">Minuman</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="menu-image">Gambar Menu</label>
                <input type="file" id="menu-image" name="image" accept="image/png, image/jpeg, image/jpg">
                <small id="image-hint" style="color: var(--text-light); margin-top: 4px;">* Kosongkan jika tidak ingin mengubah gambar saat mode edit.</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancel-btn">Batal</button>
                <button type="submit" class="btn btn-primary" id="save-menu-btn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="order-details-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header-flex">
            <h3 id="order-details-title">Detail Pesanan #000</h3>
            <button id="close-order-modal-btn" style="background:none; border:none; font-size: 24px; cursor:pointer;">&times;</button>
        </div>
        
        <div id="order-details-content">
            <p>Memuat detail pesanan...</p>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="cancel-order-modal-btn">Tutup</button>
        </div>
    </div>
</div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>