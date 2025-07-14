<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once '../db_connect.php';
$user_id = $_SESSION['user_id'];
$cartItems = [];

$query = "SELECT c.menu_id, c.quantity, m.name, m.price, m.category, m.image 
          FROM cart c 
          JOIN menu m ON c.menu_id = m.menu_id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        'id' => $row['menu_id'],
        'name' => htmlspecialchars($row['name']),
        'price' => 'Rp ' . number_format($row['price'], 0, ',', '.'),
        'price_raw' => (float)$row['price'],
        'category' => htmlspecialchars($row['category']),
        'image' => htmlspecialchars($row['image']),
        'quantity' => (int)$row['quantity']
    ];
}

$stmt_user = $conn->prepare("SELECT name, phone, address FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$userProfile = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Cimohot</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --primary-orange: #ff6b35;
            --dark-orange: #e55a2e;
            --light-orange: #ffebe5;
            --accent-orange: #ff8f6b;
            --light-gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --ff-heading: 'Rubik', sans-serif;
            --ff-body: 'Inter', sans-serif;
            --radius: 16px;
            --radius-sm: 8px;
            --radius-lg: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--ff-body);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            border-radius: var(--radius-lg);
            color: var(--white);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .cart-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }

        .cart-header .logo-img {
            height: 70px;
            width: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            position: relative;
            z-index: 1;
        }

        .cart-header h1 {
            font-family: var(--ff-heading);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .cart-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        #cart-app {
            min-height: 400px;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 30px;
            align-items: flex-start;
        }

        .cart-items-list {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .cart-items-header {
            padding: 25px 30px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%);
            color: var(--white);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            position: relative;
        }

        .cart-items-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(180deg, rgba(255, 107, 53, 0.1) 0%, transparent 100%);
        }

        .cart-items-header ion-icon {
            font-size: 1.5rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 25px 30px;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
        }

        .cart-item:hover {
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 90px;
            height: 90px;
            border-radius: var(--radius);
            object-fit: cover;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .cart-item:hover .item-image {
            transform: scale(1.05);
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            font-family: var(--ff-heading);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-dark);
        }

        .item-details p {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-orange);
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            background: var(--light-gray);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .quantity-control:hover {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .quantity-btn {
            background: var(--white);
            border: none;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: var(--primary-orange);
        }

        .quantity-btn:hover {
            background: var(--primary-orange);
            color: var(--white);
        }

        .quantity-input {
            width: 50px;
            height: 40px;
            text-align: center;
            border: none;
            background: var(--white);
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-dark);
        }

        .remove-btn {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #fca5a5;
            color: #dc2626;
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            font-size: 1.3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: var(--white);
            transform: scale(1.1);
        }

        .cart-summary {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            position: sticky;
            top: 30px;
            overflow: hidden;
        }

        .summary-header {
            padding: 25px 30px;
            background: linear-gradient(135deg, var(--text-dark) 0%, #374151 100%);
            color: var(--white);
            border-bottom: 1px solid var(--border-color);
        }

        .summary-title {
            font-family: var(--ff-heading);
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-content {
            padding: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 8px 0;
            font-size: 1rem;
        }

        .summary-row.total {
            font-weight: 700;
            font-size: 1.3rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
            color: var(--primary-orange);
        }

        .summary-actions {
            padding: 0 30px 30px;
        }

        .btn {
            width: 100%;
            padding: 16px 24px;
            border-radius: var(--radius);
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
            color: #6b7280;
        }

        .btn:disabled::before {
            display: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--white);
            border: 2px solid var(--primary-orange);
            color: var(--primary-orange);
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background: var(--light-orange);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .empty-cart-container {
            text-align: center;
            padding: 80px 40px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .empty-cart-container ion-icon {
            font-size: 5rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-cart-container h3 {
            font-family: var(--ff-heading);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .empty-cart-container p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 30px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-orange);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 60px auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .badge {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--white);
            padding: 4px 12px;
            border-radius: var(--radius-lg);
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .payment-method-container {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        .payment-method-container h4 {
            font-family: var(--ff-heading);
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: var(--text-dark);
        }
        .payment-option {
            display: flex;
            align-items: center;
            background: var(--light-gray);
            padding: 15px;
            border-radius: var(--radius-sm);
            margin-bottom: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-option:hover {
            border-color: var(--accent-orange);
        }
        .payment-option input[type="radio"] {
            margin-right: 12px;
            accent-color: var(--primary-orange);
            width: 18px;
            height: 18px;
        }
        .payment-option label {
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
        }
        .payment-option input[type="radio"]:checked + label {
            color: var(--primary-orange);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .cart-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .cart-summary {
                position: static;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .cart-header h1 {
                font-size: 2rem;
            }
            
            .cart-item {
                padding: 20px;
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .item-image {
                width: 120px;
                height: 120px;
            }
            
            .item-controls {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header class="cart-header">
            <a href="../index.php"><img src="../assets/images/logo cimohot.png" alt="Cimohot Logo" class="logo-img"/></a>
            <h1>Keranjang Belanja</h1>
            <p>Review dan selesaikan pesanan Anda di sini</p>
        </header>
        <main id="cart-app"><div class="loading-spinner"></div></main>
    </div>
    
    <script>
        const initialCartItems = <?php echo json_encode($cartItems); ?>;
        const currentUserProfile = <?php echo json_encode($userProfile); ?>;
    </script>
    
    <script>
    "use strict";
    class CartPage {
        constructor(items) { this.items = items; this.app = document.getElementById("cart-app"); this.render(); }
        render() {
            if (!this.app) { console.error("Elemen #cart-app tidak ditemukan!"); return; }
            if (!this.items || this.items.length === 0) { this.renderEmptyCart(); } 
            else { this.renderCart(); }
        }

        renderCart() {
            const total = this.calculateTotal();
            const itemCount = this.items.reduce((sum, item) => sum + item.quantity, 0);
            this.app.innerHTML = `
                <div class="cart-layout">
                    <section class="cart-items-list">
                        <div class="cart-items-header">
                            <ion-icon name="bag-handle-outline"></ion-icon>
                            <span>${itemCount} Item di Keranjang</span>
                            <span class="badge">${itemCount}</span>
                        </div>
                        <div>${this.items.map(item => this.renderCartItem(item)).join('')}</div>
                    </section>
                    <aside class="cart-summary">
                        <div class="summary-header">
                            <h3 class="summary-title">
                                <ion-icon name="receipt-outline"></ion-icon>
                                Ringkasan Pesanan
                            </h3>
                        </div>
                        <div class="summary-content">
                            <div class="summary-row">
                                <span>Subtotal (${itemCount} item)</span>
                                <span class="summary-value">Rp ${total.toLocaleString("id-ID")}</span>
                            </div>
                            <div class="summary-row">
                                <span>Biaya Layanan</span>
                                <span class="summary-value">Gratis</span>
                            </div>
                            <div class="payment-method-container">
                                <h4>Pilih Metode Pembayaran</h4>
                                <div class="payment-option">
                                    <input type="radio" id="payment-cod" name="payment_method" value="cod" checked>
                                    <label for="payment-cod">Bayar di Tempat (COD)</label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="payment-transfer" name="payment_method" value="transfer">
                                    <label for="payment-transfer">Transfer Bank</label>
                                </div>
                            </div>
                            <div class="summary-row total">
                                <span>Total Pembayaran</span>
                                <span class="summary-value">Rp ${total.toLocaleString("id-ID")}</span>
                            </div>
                        </div>
                        <div class="summary-actions">
                            <button class="btn btn-primary checkout-btn">
                                <ion-icon name="card-outline"></ion-icon>
                                <span>Lanjut ke Checkout</span>
                            </button>
                            <button class="btn btn-secondary continue-shopping-btn">
                                <ion-icon name="arrow-back-outline"></ion-icon>
                                <span>Lanjut Belanja</span>
                            </button>
                        </div>
                    </aside>
                </div>`;
            this.attachEventListeners();
        }
        
        renderCartItem(item) {
            const imagePath = `../${item.image}`;
            return `
                <article class="cart-item" data-id="${item.id}">
                    <img src="${imagePath}" alt="${item.name}" class="item-image">
                    <div class="item-details">
                        <h4>${item.name}</h4>
                        <p>${item.price}</p>
                    </div>
                    <div class="item-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn" data-action="decrease">−</button>
                            <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                            <button class="quantity-btn" data-action="increase">+</button>
                        </div>
                        <button class="remove-btn" title="Hapus dari keranjang">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </div>
                </article>`;
        }
        renderEmptyCart() { 
            this.app.innerHTML = `
                <div class="empty-cart-container">
                    <ion-icon name="cart-outline"></ion-icon>
                    <h3>Keranjang Anda Kosong</h3>
                    <p>Belum ada item dalam keranjang. Ayo pilih menu cireng favorit Anda!</p>
                    <button class="btn btn-primary continue-shopping-btn">
                        <ion-icon name="restaurant-outline"></ion-icon>
                        <span>Lihat Menu</span>
                    </button>
                </div>`; 
            this.attachEventListeners(); 
        }
        attachEventListeners() { 
            this.app.querySelector('.continue-shopping-btn')?.addEventListener('click', this.continueShopping); 
            this.app.querySelector('.checkout-btn')?.addEventListener('click', () => this.proceedToCheckout()); 
            this.app.querySelectorAll('.cart-item').forEach(el => { 
                const id = el.dataset.id; 
                el.querySelector('[data-action="increase"]')?.addEventListener('click', () => this.updateQuantity(id, 1)); 
                el.querySelector('[data-action="decrease"]')?.addEventListener('click', () => this.updateQuantity(id, -1)); 
                el.querySelector('.remove-btn')?.addEventListener('click', () => this.removeItem(id)); 
            }); 
        }
        async updateQuantity(id, change) { 
            const item = this.items.find(i => i.id == id); 
            if (!item) return; 
            let newQty = item.quantity + change; 
            if (newQty < 1) { 
                this.removeItem(id); 
                return; 
            } 
            const params = new URLSearchParams({ action: 'update_cart', menu_id: id, quantity: newQty }); 
            const response = await fetch('../api.php', { method: 'POST', body: params }); 
            const result = await response.json(); 
            if (result.success) { 
                item.quantity = newQty; 
                this.render(); 
            } else { 
                Swal.fire('Error', result.message, 'error'); 
            } 
        }
        async removeItem(id) { 
            const confirmed = await Swal.fire({ 
                title: 'Hapus Item dari Keranjang?', 
                text: "Item akan dihapus dari keranjang belanja Anda", 
                icon: 'warning', 
                showCancelButton: true, 
                confirmButtonColor: '#d33', 
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }); 
            if (confirmed.isConfirmed) { 
                const params = new URLSearchParams({ action: 'remove_from_cart', menu_id: id }); 
                const response = await fetch('../api.php', { method: 'POST', body: params }); 
                const result = await response.json(); 
                if (result.success) { 
                    this.items = this.items.filter(i => i.id != id); 
                    this.render(); 
                    Swal.fire('Berhasil!', 'Item telah dihapus dari keranjang.', 'success'); 
                } else { 
                    Swal.fire('Error', result.message, 'error'); 
                } 
            } 
        }
        async proceedToCheckout() {
            if (!currentUserProfile || !currentUserProfile.phone || !currentUserProfile.address) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Profil Belum Lengkap',
                    html: 'Untuk melanjutkan pesanan, Anda wajib melengkapi <strong>Alamat</strong> dan <strong>Nomor Telepon</strong> di profil Anda.',
                    confirmButtonText: 'Lengkapi Profil Sekarang',
                    confirmButtonColor: '#ff6b35',
                    showCancelButton: true,
                    cancelButtonText: 'Nanti Saja'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '../akun.php?redirect=cart';
                    }
                });
                return;
            } 
            const btn = this.app.querySelector('.checkout-btn');
            if (!btn) return;

            btn.disabled = true;
            btn.innerHTML = '<div class="loading-spinner" style="width:20px;height:20px;border-width:2px;margin:0;"></div> Memproses...';
            
            // Pastikan variabel ini dideklarasikan dengan benar (camelCase)
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            try {
                const params = new URLSearchParams();
                params.append('action', 'checkout');
                // Kita tetap mengirim data item sebagai string JSON
                params.append('items', JSON.stringify(this.items));
                params.append('payment_method', paymentMethod);

                const response = await fetch('../api.php', {
                    method: 'POST',
                    body: params // Menggunakan URLSearchParams, bukan JSON.stringify
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    window.location.href = 'order_confirmation.php';
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire('Checkout Gagal', `Terjadi kesalahan: ${error.message}`, 'error');
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="card-outline"></ion-icon><span>Lanjut ke Pembayaran</span>';
            }
        }

         continueShopping() { 
        window.location.href = '../index.php#food-menu'; 
    }
        calculateTotal() { 
        return this.items.reduce((total, item) => total + (item.price_raw * item.quantity), 0); 
    }
}
    document.addEventListener("DOMContentLoaded", () => { 
        new CartPage(initialCartItems); 
    });
    </script>
</body>
</html>