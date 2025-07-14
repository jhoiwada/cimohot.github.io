<?php

// Konfigurasi dasar dan error handling
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once 'db_connect.php';
define('PROJECT_ROOT', __DIR__ . '/');

// Fungsi untuk mengirim response JSON
function sendResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    if (ob_get_level() > 0) { ob_clean(); }
    echo json_encode($response);
    exit;
}

$action = $_REQUEST['action'] ?? '';
if (empty($action)) {
    sendResponse(false, 'Aksi tidak ditentukan.', null, 400);
}

try {
    // Pengecekan Izin Akses Terpusat
    $user_actions = ['update_user_profile', 'add_to_cart', 'update_cart', 'remove_from_cart', 'checkout'];
    $admin_actions = ['get_dashboard_stats', 'get_all_orders', 'get_all_menus_admin', 'update_order_status'];

    if (in_array($action, $user_actions) || in_array($action, $admin_actions)) {
        if (!isset($_SESSION['user_id'])) {
            sendResponse(false, 'Akses ditolak. Silakan login terlebih dahulu.', null, 401);
        }
    }
    if (in_array($action, $admin_actions)) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            sendResponse(false, 'Akses ditolak. Peran admin diperlukan.', null, 403);
        }
    }

    switch ($action) {

        // ===================================
        // ===== AKSI PUBLIK =====
        // ===================================
        case 'get_menu':
            $category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
            $sql = $category === 'all' ? "SELECT * FROM menu ORDER BY category, name" : "SELECT * FROM menu WHERE category = ? ORDER BY name";
            $stmt = $conn->prepare($sql);
            if ($category !== 'all') { $stmt->bind_param("s", $category); }
            $stmt->execute();
            $result = $stmt->get_result();
            $menu = [];
            while ($row = $result->fetch_assoc()) {
                $menu[] = [
                    'id' => (int)$row['menu_id'], 'name' => htmlspecialchars($row['name']),
                    'price' => (float)$row['price'], 'price_formatted' => 'Rp ' . number_format($row['price'], 0, ',', '.'),
                    'category' => $row['category'], 'image' => htmlspecialchars($row['image']),
                    'rating' => (float)$row['rating'], 'description' => htmlspecialchars($row['description']),
                    'stock' => (int)$row['stock']
                ];
            }
            sendResponse(true, 'Menu berhasil diambil', ['menu' => $menu]);
            break;

        // ===================================
        // ===== AKSI OTENTIKASI =====
        // ===================================
        case 'login':
            if (empty($_POST['email_or_phone']) || empty($_POST['password'])) { 
                sendResponse(false, 'Data wajib diisi.', null, 400); 
            }
            $email_or_phone = trim($_POST['email_or_phone']);
            $password = $_POST['password'];
            $role = trim($_POST['role']);
            $stmt = $conn->prepare("SELECT user_id, password, name, role FROM users WHERE (email = ? OR phone = ?) AND role = ?");
            $stmt->bind_param('sss', $email_or_phone, $email_or_phone, $role);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id']; 
                $_SESSION['name'] = $user['name']; 
                $_SESSION['role'] = $user['role'];
                sendResponse(true, 'Login berhasil', ['user' => ['id' => $user['user_id'], 'name' => $user['name'], 'role' => $user['role']]]);
            } else { 
                sendResponse(false, 'Kombinasi email/password atau peran salah.', null, 401); 
            }
            break;

        case 'register':
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) { 
                sendResponse(false, 'Data wajib diisi.', null, 400); 
            }
            $name = trim($_POST['name']); 
            $email = trim($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
                sendResponse(false, 'Format email tidak valid.', null, 400); 
            }
            $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt_check->bind_param("s", $email); 
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) { 
                sendResponse(false, 'Email ini sudah terdaftar.', null, 409); 
            }
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt_insert->execute()) { 
                sendResponse(true, 'Pendaftaran berhasil!'); 
            } else { 
                sendResponse(false, 'Gagal mendaftarkan pengguna.', null, 500); 
            }
            break;

        case 'check_session':
            if (isset($_SESSION['user_id'])) {
                // -- BLOK BARU: MENGHITUNG ITEM DI KERANJANG --
                $user_id = $_SESSION['user_id'];
                $stmt_cart = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
                $stmt_cart->bind_param("i", $user_id);
                $stmt_cart->execute();
                $cart_data = $stmt_cart->get_result()->fetch_assoc();
                $cart_count = (int)($cart_data['total_items'] ?? 0);
                // -- AKHIR BLOK BARU --

                sendResponse(true, 'User is logged in.', [
                    'loggedIn' => true, 
                    'user' => [
                        'id' => $_SESSION['user_id'], 
                        'name' => $_SESSION['name'] ?? '', 
                        'role' => $_SESSION['role'] ?? 'user'
                    ],
                    'cart_count' => $cart_count // <-- TAMBAHKAN DATA INI
                ]);
            } else { 
                sendResponse(true, 'User is not logged in.', ['loggedIn' => false]); 
            }
            break;



        case 'delete_menu':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { sendResponse(false, 'Akses ditolak.', null, 403); }

            $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
            if ($menu_id <= 0) { sendResponse(false, 'ID menu tidak valid.', null, 400); }

            $stmt_img = $conn->prepare("SELECT image FROM menu WHERE menu_id = ?");
            $stmt_img->bind_param("i", $menu_id);
            $stmt_img->execute();
            $image_to_delete = $stmt_img->get_result()->fetch_assoc()['image'] ?? '';

            $stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ?");
            $stmt->bind_param("i", $menu_id);
            
            if ($stmt->execute()) {
            if (!empty($image_to_delete) && file_exists($image_to_delete)) {
                unlink($image_to_delete);
            }
                sendResponse(true, 'Menu berhasil dihapus.');
            } else {
                sendResponse(false, 'Gagal menghapus menu.', null, 500);
            }
            break;


        case 'add_menu':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { sendResponse(false, 'Akses ditolak.', null, 403); }
            if (empty($_POST['name']) || !isset($_POST['price'])) { sendResponse(false, 'Nama dan harga wajib diisi.', null, 400); }

            $image_path_for_db = ''; // Path yang akan disimpan ke DB (relatif)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir_relative = 'assets/uploads/';
                $upload_dir_absolute = PROJECT_ROOT . $upload_dir_relative;
                
                $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
                $target_file_absolute = $upload_dir_absolute . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file_absolute)) {
                    $image_path_for_db = $upload_dir_relative . $image_name;
                } else {
                    sendResponse(false, 'Gagal mengupload gambar.', null, 500);
                }
                } else {
                    sendResponse(false, 'Gambar wajib diisi untuk menu baru.', null, 400);
                }

            $stmt = $conn->prepare("INSERT INTO menu (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");// api.php - baris 209 (Perbaikan)
            $stmt->bind_param("ssdsiss", $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['category'], $image_path_for_db, $menu_id); $stmt->bind_param("ssdiss", $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['category'], $image_path_for_db);

            if ($stmt->execute()) { sendResponse(true, 'Menu baru berhasil ditambahkan.'); } 
            else { sendResponse(false, 'Gagal menyimpan menu ke database.', null, 500); }
            break;


        case 'update_menu':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                sendResponse(false, 'Akses ditolak.', null, 403);
            }

            $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
            if ($menu_id <= 0) {
                sendResponse(false, 'ID menu tidak valid.', null, 400);
            }

            // Ambil data dari POST
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $category = $_POST['category'] ?? '';

            // Siapkan query dasar dan parameter
            $sql_parts = [
                "name = ?",
                "description = ?",
                "price = ?",
                "stock = ?",
                "category = ?"
            ];
            $params = [$name, $description, $price, $stock, $category];
            $types = "ssdss"; // Tipe untuk 5 parameter dasar

            // Cek jika ada file gambar baru yang diunggah
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                
                // Ambil path gambar lama untuk dihapus nanti
                $stmt_old_img = $conn->prepare("SELECT image FROM menu WHERE menu_id = ?");
                $stmt_old_img->bind_param("i", $menu_id);
                $stmt_old_img->execute();
                $old_image_path = $stmt_old_img->get_result()->fetch_assoc()['image'] ?? null;

                // Proses upload gambar baru
                $upload_dir_relative = 'assets/uploads/';
                $upload_dir_absolute = PROJECT_ROOT . $upload_dir_relative;
                $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
                $target_file_absolute = $upload_dir_absolute . $image_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file_absolute)) {
                    $new_image_path = $upload_dir_relative . $image_name;

                    // Tambahkan pembaruan gambar ke query dan parameter
                    $sql_parts[] = "image = ?";
                    $params[] = $new_image_path;
                    $types .= "s";

                    // Hapus gambar lama jika ada dan valid
                    if ($old_image_path && file_exists(PROJECT_ROOT . $old_image_path)) {
                        unlink(PROJECT_ROOT . $old_image_path);
                    }
                } else {
                    sendResponse(false, 'Gagal memindahkan file gambar baru.', null, 500);
                }
            }

            $params[] = $menu_id;
            $types .= "i";

            $sql = "UPDATE menu SET " . implode(", ", $sql_parts) . " WHERE menu_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                sendResponse(true, 'Menu berhasil diperbarui.');
            } else {
                sendResponse(false, 'Gagal memperbarui menu di database.', null, 500);
            }
            break;


         case 'delete_menu':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { sendResponse(false, 'Akses ditolak.', null, 403); }

            $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
            if ($menu_id <= 0) { sendResponse(false, 'ID menu tidak valid.', null, 400); }

            $stmt_img = $conn->prepare("SELECT image FROM menu WHERE menu_id = ?");
            $stmt_img->bind_param("i", $menu_id);
            $stmt_img->execute();
            $image_to_delete_relative = $stmt_img->get_result()->fetch_assoc()['image'] ?? '';

            $stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ?");
            $stmt->bind_param("i", $menu_id);
            
            if ($stmt->execute()) {
                $image_to_delete_absolute = PROJECT_ROOT . $image_to_delete_relative;
                if (!empty($image_to_delete_relative) && file_exists($image_to_delete_absolute)) {
                    unlink($image_to_delete_absolute);
                }
                sendResponse(true, 'Menu berhasil dihapus.');
            } else {
                sendResponse(false, 'Gagal menghapus menu.', null, 500);
            }
            break;


        case 'update_user_profile':
            $user_id = (int)$_SESSION['user_id'];
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';

            if (empty($name)) {
                sendResponse(false, 'Nama tidak boleh kosong.', null, 400);
            }

            $stmt_update = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE user_id = ?");
            $stmt_update->bind_param("sssi", $name, $phone, $address, $user_id);

            if ($stmt_update->execute()) {
                $_SESSION['name'] = $name;
                sendResponse(true, 'Profil berhasil diperbarui.');
            } else {
                sendResponse(false, 'Gagal memperbarui profil.', null, 500);
            }
            break;

        // ===================================
        // ===== AKSI PENGGUNA =====
        // ===================================

        case 'add_to_cart':
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        sendResponse(false, 'Aksi ditolak. Akun admin tidak dapat melakukan pemesanan.', null, 403);
        exit;
    }
    $user_id = (int)$_SESSION['user_id'];
    $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
    $quantity_to_add = 1; 

    if ($menu_id <= 0) {
        sendResponse(false, 'Input tidak valid', null, 400);
    }

    $stmt_stock = $conn->prepare("SELECT stock FROM menu WHERE menu_id = ?");
    $stmt_stock->bind_param("i", $menu_id);
    $stmt_stock->execute();
    $menu_data = $stmt_stock->get_result()->fetch_assoc();

    if (!$menu_data) {
        sendResponse(false, 'Menu tidak ditemukan.', null, 404);
    }
    $available_stock = (int)$menu_data['stock'];

    if ($available_stock <= 0) {
        sendResponse(false, 'Maaf, stok untuk item ini sudah habis.', null, 400);
    }

    $stmt_cart = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND menu_id = ?");
    $stmt_cart->bind_param("ii", $user_id, $menu_id);
    $stmt_cart->execute();
    $cart_item = $stmt_cart->get_result()->fetch_assoc();
    $quantity_in_cart = $cart_item ? (int)$cart_item['quantity'] : 0;

    if (($quantity_in_cart + $quantity_to_add) > $available_stock) {
        sendResponse(false, "Stok tidak mencukupi. Hanya tersisa {$available_stock} buah di stok kami.", null, 400);
    }

    if ($cart_item) {
        $new_quantity = $quantity_in_cart + $quantity_to_add;
        $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND menu_id = ?");
        $stmt_update->bind_param("iii", $new_quantity, $user_id, $menu_id);
        $stmt_update->execute();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iii", $user_id, $menu_id, $quantity_to_add);
        $stmt_insert->execute();
    }
    $stmt_count = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $new_cart_count = (int)($stmt_count->get_result()->fetch_assoc()['total_items'] ?? 0);

    sendResponse(true, 'Item berhasil ditambahkan ke keranjang.', ['new_cart_count' => $new_cart_count]);
        break;

        case 'update_cart':
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        sendResponse(false, 'Aksi ditolak. Akun admin tidak dapat melakukan pemesanan.', null, 403);
        exit;
    }
    $user_id = (int)$_SESSION['user_id'];
    $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
    $new_quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($menu_id <= 0 || $new_quantity <= 0) {
        sendResponse(false, 'Input tidak valid.', null, 400);
    }

    // --- VALIDASI STOK ---
    $stmt_stock = $conn->prepare("SELECT stock FROM menu WHERE menu_id = ?");
    $stmt_stock->bind_param("i", $menu_id);
    $stmt_stock->execute();
    $menu_data = $stmt_stock->get_result()->fetch_assoc();
    if (!$menu_data) {
        sendResponse(false, 'Menu tidak ditemukan.', null, 404);
    }
    $available_stock = (int)$menu_data['stock'];

    if ($new_quantity > $available_stock) {
        sendResponse(false, "Stok tidak mencukupi. Maksimal pembelian {$available_stock} buah.", null, 400);
    }
    // --- AKHIR VALIDASI STOK ---

    // Jika valid, lanjutkan update
    $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND menu_id = ?");
    $stmt_update->bind_param("iii", $new_quantity, $user_id, $menu_id);

    if ($stmt_update->execute()) {
        sendResponse(true, 'Keranjang berhasil diperbarui.');
    } else {
        sendResponse(false, 'Gagal memperbarui keranjang.', null, 500);
    }
    break;

        case 'remove_from_cart':
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        sendResponse(false, 'Aksi ditolak. Akun admin tidak dapat melakukan pemesanan.', null, 403);
        exit;
    }
            $user_id = (int)$_SESSION['user_id'];
            $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
            
            if ($menu_id <= 0) {
                sendResponse(false, 'Menu ID tidak valid.', null, 400);
            }
            
            // Cek apakah item ada di keranjang user
            $stmt_check = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ? AND menu_id = ?");
            $stmt_check->bind_param("ii", $user_id, $menu_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            
            if ($result->num_rows === 0) {
                sendResponse(false, 'Item tidak ditemukan di keranjang.', null, 404);
            }
            
            // Hapus item dari keranjang
            $stmt_delete = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND menu_id = ?");
            $stmt_delete->bind_param("ii", $user_id, $menu_id);
            
            if ($stmt_delete->execute()) {
                sendResponse(true, 'Item berhasil dihapus dari keranjang.');
            } else {
                sendResponse(false, 'Gagal menghapus item dari keranjang.', null, 500);
            }
            break;

       case 'checkout':
     if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
         sendResponse(false, 'Aksi ditolak. Akun admin tidak dapat melakukan pemesanan.', null, 403);
         exit;
     }
     $user_id = (int)$_SESSION['user_id'];

     // PERUBAHAN UTAMA: Membaca dari $_POST
     $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
     $payment_method = isset($_POST['payment_method']) && in_array($_POST['payment_method'], ['cod', 'transfer']) ? $_POST['payment_method'] : 'cod';
     
            if (empty($items)) {
                sendResponse(false, 'Keranjang Anda kosong.', null, 400);
            }

            // data profil pengguna untuk detail pesanan
            $stmt_user = $conn->prepare("SELECT name, phone, address FROM users WHERE user_id = ?");
            $stmt_user->bind_param("i", $user_id);
            $stmt_user->execute();
            $user_profile = $stmt_user->get_result()->fetch_assoc();
            if (!$user_profile) {
                sendResponse(false, 'Profil pengguna tidak ditemukan.', null, 404);
            }
            if (empty($user_profile['phone']) || empty($user_profile['address'])) {
                sendResponse(false, 'Profil tidak lengkap. Harap isi alamat dan nomor telepon sebelum checkout.', null, 400);
            }

            $conn->begin_transaction();

            try {
                // Hitung total harga berdasarkan harga dari database
                $total_amount = 0;
                $menu_ids = array_column($items, 'id');
                $placeholders = str_repeat('?,', count($menu_ids) - 1) . '?';
                $types = str_repeat('i', count($menu_ids));
                $stmt_prices = $conn->prepare("SELECT menu_id, price FROM menu WHERE menu_id IN ($placeholders)");
                $stmt_prices->bind_param($types, ...$menu_ids);
                $stmt_prices->execute();
                $correct_prices_from_db = array_column($stmt_prices->get_result()->fetch_all(MYSQLI_ASSOC), 'price', 'menu_id');

                foreach ($items as &$item) {
                    $item['price_raw'] = $correct_prices_from_db[$item['id']] ?? 0;
                    $total_amount += $item['price_raw'] * $item['quantity'];
                }
                unset($item);

                // detail pesanan dari profil pengguna
                $customer_name = $user_profile['name'];
                $customer_phone = $user_profile['phone'] ?? '';
                $delivery_address = $user_profile['address'] ?? '';

                // Query INSERT 
                $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, customer_name, customer_phone, delivery_address) VALUES (?, ?, 'pending', ?, ?, ?, ?)");
                $stmt_order->bind_param("idssss", $user_id, $total_amount, $payment_method, $customer_name, $customer_phone, $delivery_address);
                if (!$stmt_order->execute()) { throw new Exception('Gagal membuat pesanan utama.'); }

                $order_id = $conn->insert_id;
                
                // Simpan ID pesanan ke session untuk halaman konfirmasi
                $_SESSION['last_order_id'] = $order_id;

                //  detail item pesanan
                $stmt_details = $conn->prepare("INSERT INTO order_details (order_id, menu_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt_update_stock = $conn->prepare("UPDATE menu SET stock = stock - ? WHERE menu_id = ?");

                foreach ($items as $item) {
                    $subtotal = $item['price_raw'] * $item['quantity'];
                    $stmt_details->bind_param("iiidd", $order_id, $item['id'], $item['quantity'], $item['price_raw'], $subtotal);
                    if (!$stmt_details->execute()) { throw new Exception('Gagal menyimpan detail item pesanan.'); }
                    
                    $stmt_update_stock->bind_param("ii", $item['quantity'], $item['id']);
                    if (!$stmt_update_stock->execute()) { 
                        throw new Exception('Gagal memperbarui stok menu.'); 
                    }
                }

                // Kosongkan keranjang
                $stmt_delete = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt_delete->bind_param("i", $user_id);
                $stmt_delete->execute();

                $conn->commit();
                sendResponse(true, 'Order berhasil dibuat.');

            } catch (Exception $e) {
                $conn->rollback();
                sendResponse(false, 'Terjadi kesalahan internal pada server: ' . $e->getMessage(), null, 500);
            }
            break;

        // ===================================
        // ===== AKSI ADMIN =====
        // ===================================

     
        case 'get_order_details':
           
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                sendResponse(false, 'Akses ditolak.', null, 403);
            }
            
            $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
            if ($order_id <= 0) {
                sendResponse(false, 'ID Pesanan tidak valid.', null, 400);
            }

            
            $stmt_order = $conn->prepare("SELECT customer_name, customer_phone, delivery_address, order_date, total_amount, status FROM orders WHERE order_id = ?");
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $order_data = $stmt_order->get_result()->fetch_assoc();

            if (!$order_data) {
                sendResponse(false, 'Pesanan tidak ditemukan.', null, 404);
            }

      
            $stmt_items = $conn->prepare(
                "SELECT od.quantity, od.price, od.subtotal, m.name as menu_name 
                 FROM order_details od 
                 JOIN menu m ON od.menu_id = m.menu_id 
                 WHERE od.order_id = ?"
            );
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $items_data = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

           
            $response_data = [
                'order_info' => $order_data,
                'items' => $items_data
            ];

            sendResponse(true, 'Detail pesanan berhasil diambil.', $response_data);
            break;

         case 'generate_sales_report':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                sendResponse(false, 'Akses ditolak.', null, 403);
            }

            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';

            if (empty($start_date) || empty($end_date)) {
                sendResponse(false, 'Tanggal mulai dan akhir wajib diisi.', null, 400);
            }

            // Query untuk mendapatkan ringkasan
            $stmt = $conn->prepare(
                "SELECT 
                    SUM(total_amount) as total_revenue, 
                    COUNT(order_id) as total_orders,
                    AVG(total_amount) as average_transaction
                 FROM orders 
                 WHERE status IN ('selesai') AND DATE(order_date) BETWEEN ? AND ?"
            );
            $stmt->bind_param("ss", $start_date, $end_date);
            $stmt->execute();
            $summary = $stmt->get_result()->fetch_assoc();

            sendResponse(true, 'Laporan berhasil dibuat.', $summary);
            break;

                case 'get_all_customers':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                sendResponse(false, 'Akses ditolak.', null, 403);
            }

            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
            $sql = "SELECT user_id, name, email, phone, created_at FROM users WHERE role = 'user'";
            $params = [];
            $types = '';

            if (!empty($search_term)) {
                $sql .= " AND (name LIKE ? OR email LIKE ?)";
                $like_term = "%" . $search_term . "%";
                $params[] = $like_term;
                $params[] = $like_term;
                $types .= 'ss';
            }
            $sql .= " ORDER BY created_at DESC";

            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Sanitasi output
            foreach ($customers as &$customer) {
                $customer['name'] = htmlspecialchars($customer['name']);
                $customer['email'] = htmlspecialchars($customer['email']);
                $customer['phone'] = htmlspecialchars($customer['phone'] ?? '-');
            }

            sendResponse(true, 'Data pelanggan berhasil diambil.', ['customers' => $customers]);
            break;


        case 'get_dashboard_stats':
            $sql_today = "SELECT SUM(total_amount) as total_revenue, COUNT(order_id) as total_orders FROM orders WHERE DATE(order_date) = CURDATE()";
            $today_stats = $conn->query($sql_today)->fetch_assoc();
            $new_customers = (int)($conn->query("SELECT COUNT(user_id) as c FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['c'] ?? 0);
            $top_product_row = $conn->query("SELECT m.name FROM order_details od JOIN menu m ON od.menu_id = m.menu_id GROUP BY m.menu_id ORDER BY COUNT(od.menu_id) DESC LIMIT 1")->fetch_assoc();
            $pending_orders = (int)($conn->query("SELECT COUNT(order_id) as c FROM orders WHERE status = 'pending' OR status = 'processing'")->fetch_assoc()['c'] ?? 0);
            $sql_chart = "SELECT DATE(order_date) as a_date, SUM(total_amount) as daily_revenue FROM orders WHERE order_date >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(order_date) ORDER BY a_date ASC";
            $chart_result = $conn->query($sql_chart)->fetch_all(MYSQLI_ASSOC);
            $sales_data = [];
            for ($i = 6; $i >= 0; $i--) { 
                $date_key = date('Y-m-d', strtotime("-$i days")); 
                $sales_data[$date_key] = 0; 
            }
            foreach ($chart_result as $row) { 
                $sales_data[$row['a_date']] = (float)$row['daily_revenue']; 
            }
            $sql_recent = "SELECT o.order_id, u.name as customer_name, o.total_amount, o.status FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5";
            $recent_orders_raw = $conn->query($sql_recent)->fetch_all(MYSQLI_ASSOC);
            $recent_orders_clean = array_map(function($order) { 
                $order['customer_name'] = htmlspecialchars($order['customer_name']); 
                return $order; 
            }, $recent_orders_raw);
            
            sendResponse(true, 'Data dashboard berhasil diambil.', [
                'revenue' => (float)($today_stats['total_revenue'] ?? 0),
                'orders_today' => (int)($today_stats['total_orders'] ?? 0),
                'new_customers' => $new_customers,
                'top_product' => $top_product_row ? htmlspecialchars($top_product_row['name']) : 'N/A',
                'sales_chart' => [
                    'labels' => array_map(fn($d) => date('d M', strtotime($d)), array_keys($sales_data)), 
                    'values' => array_values($sales_data)
                ],
                'recent_orders' => $recent_orders_clean,
                'pending_orders' => $pending_orders
            ]);
            break;

       case 'get_all_orders':
    // Query SQL dasar
    $sql = "SELECT o.order_id, u.name as customer_name, o.order_date, o.total_amount, o.status 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id";

    // Cek apakah ada parameter customer_id dari JavaScript
    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        // Jika ADA, tambahkan filter WHERE untuk pelanggan spesifik
        $sql .= " WHERE o.user_id = ?"; // '?' adalah placeholder untuk keamanan
        $sql .= " ORDER BY o.order_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_GET['customer_id']); // "i" berarti tipe integer
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);

    } else {
        // Jika TIDAK ADA, ambil semua pesanan seperti biasa (untuk menu Pesanan)
        $sql .= " ORDER BY o.order_date DESC";
        $orders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Sanitasi data untuk keamanan (tetap diperlukan)
    foreach ($orders as &$order) {
        $order['customer_name'] = htmlspecialchars($order['customer_name']);
        $order['total_amount'] = (float)$order['total_amount'];
        $order['order_id'] = (int)$order['order_id'];
    }
    
    sendResponse(true, 'Semua pesanan berhasil diambil.', ['orders' => $orders]);
    break;

        case 'get_all_menus_admin':
            $sql = "SELECT menu_id as id, name, price, stock, category, image, description FROM menu ORDER BY name ASC";
            $menu = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            
            foreach ($menu as &$item) {
                $item['name'] = htmlspecialchars($item['name']);
                $item['category'] = htmlspecialchars($item['category']);
                $item['image'] = htmlspecialchars($item['image']);
                $item['description'] = htmlspecialchars($item['description']);
                $item['price'] = (float)$item['price'];
                $item['id'] = (int)$item['id'];
                $item['stock'] = (int)$item['stock'];
            }
            
            sendResponse(true, 'Semua menu berhasil diambil.', ['menu' => $menu]);
            break;

        case 'update_order_status':
            //  data yang dikirim dari JavaScript
            $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $new_status = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';

            // Validasi input
            $valid_statuses = ['pending', 'diproses', 'selesai', 'dibatalkan'];
            if ($order_id <= 0 || !in_array($new_status, $valid_statuses)) {
                sendResponse(false, 'Data untuk update status tidak valid.', null, 400);
            }

            $stmt_check = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
            $stmt_check->bind_param("i", $order_id);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows === 0) {
                sendResponse(false, 'Pesanan tidak ditemukan.', null, 404);
            }

            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $new_status, $order_id);

            if ($stmt->execute()) {
                sendResponse(true, "Status pesanan #${order_id} berhasil diubah menjadi ${new_status}.");
            } else {
                sendResponse(false, "Gagal mengupdate status pesanan.", null, 500);
            }
            $stmt->close();
            break;

        default:
            sendResponse(false, 'Aksi tidak valid atau tidak ditemukan.', null, 404);
            break;
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->in_transaction) { 
        $conn->rollback(); 
    }
    error_log("API Error [action: {$action}]: " . $e->getMessage());
    sendResponse(false, 'Terjadi kesalahan internal pada server.', null, 500);
}
?>