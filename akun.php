<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Ambil data profil pengguna terbaru
$user_stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

// Ambil riwayat pesanan pengguna
$orders_stmt = $conn->prepare("SELECT order_id, total_amount, status, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$orders_stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Cimohot</title>

    <script>
        const API_URL = "api.php";
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-orange: #ff6b35;
            --dark-orange: #e55a2b;
            --light-gray: #f0f2f5;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --ff-heading: 'Rubik', sans-serif;
            --ff-body: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--ff-body);
            background-color: var(--light-gray);
            color: var(--text-dark);
            margin: 0;
        }

        .dashboard-container {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            gap: 30px;
            padding: 0 20px;
            align-items: flex-start;
        }

        .sidebar {
            flex: 0 0 280px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 25px;
        }

        .main-content {
            flex: 1;
        }

        .user-profile {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: var(--primary-orange);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 15px;
            font-weight: 700;
        }

        .user-profile h3 {
            font-family: var(--ff-heading);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .user-profile p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
            margin-top: 5px;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: var(--primary-orange);
            color: var(--white);
        }

        .sidebar-nav a ion-icon {
            font-size: 1.3rem;
        }

        .content-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 30px 40px;
        }

        .content-tab {
            display: none;
        }

        .content-tab.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            font-family: var(--ff-heading);
            font-size: 2rem;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .order-history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-history-table th,
        .order-history-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #ffffff;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #ffc107;
        }

        .status-diproses {
            background-color: #17a2b8;
        }

        .status-selesai {
            background-color: #28a745;
        }

        .status-dibatalkan {
            background-color: #dc3545;
        }

        .profile-form .form-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 25px;
        }

        .profile-form label {
            flex-basis: 150px;
            font-weight: 500;
            font-size: 1rem;
            color: var(--text-light);
            padding-right: 20px;
        }

        .profile-form .input-wrapper {
            flex: 1;
        }

        .profile-form input,
        .profile-form textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }

        .profile-form input:disabled {
            background-color: #e9ecef;
        }

        .form-actions {
            padding-top: 20px;
            margin-top: 10px;
            text-align: right;
            border-top: 1px solid var(--border-color);
        }

        .btn-save {
            background-color: var(--primary-orange);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn-save:disabled {
            background-color: #ccc;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .profile-form .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-form label {
                margin-bottom: 8px;
                flex-basis: auto;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <h3 id="sidebarName"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Beranda</a>
                <a href="#profil" data-tab="profil" class="nav-link"><ion-icon name="person-outline"></ion-icon> Profil Saya</a>
                <a href="#riwayat" data-tab="riwayat" class="nav-link"><ion-icon name="receipt-outline"></ion-icon> Riwayat Pesanan</a>
                <a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div id="profil" class="content-tab">
                <div class="content-card">
                    <h2>Edit Profil Saya</h2>
                    <form id="profileForm" class="profile-form">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <div class="input-wrapper">
                                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <div class="input-wrapper">
                                <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrapper">
                                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <div class="input-wrapper">
                                <textarea name="address" id="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save" id="saveProfileBtn">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="riwayat" class="content-tab">
                <div class="content-card">
                    <h2>Riwayat Pesanan</h2>
                    <?php if (empty($orders)): ?>
                        <p>Anda belum memiliki riwayat pesanan.</p>
                    <?php else: ?>
                        <table class="order-history-table">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></td>
                                        <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="status status-<?php echo strtolower(str_replace(' ', '_', $order['status'])); ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            const contentTabs = document.querySelectorAll('.content-tab');
            const profileForm = document.getElementById('profileForm');

            function switchTab(tabId) {
                contentTabs.forEach(tab => tab.classList.remove('active'));
                navLinks.forEach(link => link.classList.remove('active'));

                const targetTab = document.getElementById(tabId);
                const targetLink = document.querySelector(`.nav-link[data-tab="${tabId}"]`);

                if (targetTab) targetTab.classList.add('active');
                if (targetLink) targetLink.classList.add('active');
            }

            navLinks.forEach(link => {
                const tabId = link.dataset.tab;
                if (tabId) {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        switchTab(tabId);
                        window.location.hash = tabId;
                    });
                }
            });

            const currentHash = window.location.hash.substring(1);
            if (currentHash === 'riwayat') {
                switchTab('riwayat');
            } else {
                switchTab('profil');
            }

            profileForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const saveBtn = document.getElementById('saveProfileBtn');
                saveBtn.disabled = true;
                saveBtn.textContent = 'Menyimpan...';

                const formData = new FormData(this);
                formData.append('action', 'update_user_profile');

                fetch(API_URL, {
                    method: 'POST',
                    body: new URLSearchParams(formData),
                    credentials: 'include'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success');
                            document.getElementById('sidebarName').textContent = document.getElementById('name').value;
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    })
                    .finally(() => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Simpan Perubahan';
                    });
            });
        });
    </script>
</body>
</html>
