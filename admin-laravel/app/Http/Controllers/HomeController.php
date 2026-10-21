<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /** Customer homepage */
    public function index(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$request->session()->has('user_id')) {
                $request->session()->put('user_id', $user->user_id);
                $request->session()->put('name',    $user->name);
                $request->session()->put('role',    $user->role);
            }
        } elseif ($request->session()->has('user_id')) {
            \Illuminate\Support\Facades\Auth::loginUsingId($request->session()->get('user_id'));
        }

        $menuItems = DB::table('menu')->orderByRaw("FIELD(category,'original','pedas','paket hemat','minuman'), name ASC")->get();
        $menuByCategory = $menuItems->groupBy('category');
        return view('home.index', compact('menuItems', 'menuByCategory'));
    }

    /** Unified API endpoint — handles all legacy /api.php AJAX calls */
    public function api(Request $request)
    {
        $action = $request->input('action', '');
        if (empty($action)) {
            return response()->json(['success' => false, 'message' => 'Aksi tidak ditentukan.'], 400);
        }

        // Bi-directional synchronization between Laravel Auth and Session
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $userId = $user->user_id;
            $role   = $user->role;
            if (!$request->session()->has('user_id')) {
                $request->session()->put('user_id', $user->user_id);
                $request->session()->put('name',    $user->name);
                $request->session()->put('role',    $user->role);
            }
        } elseif ($request->session()->has('user_id')) {
            $userId = $request->session()->get('user_id');
            $role   = $request->session()->get('role', 'user');
            \Illuminate\Support\Facades\Auth::loginUsingId($userId);
        } else {
            $userId = null;
            $role   = 'guest';
        }

        $userActions  = ['update_user_profile','add_to_cart','update_cart','remove_from_cart','checkout','validate_promo','get_cart'];
        $adminActions = ['get_dashboard_stats','get_all_orders','get_all_menus_admin','update_order_status',
                         'get_order_details','generate_sales_report','get_all_customers','add_menu','update_menu','delete_menu'];

        if (in_array($action, $userActions) && !$userId) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Silakan login.'], 401);
        }
        $orderingActions = ['add_to_cart', 'update_cart', 'remove_from_cart', 'checkout', 'validate_promo'];
        if (in_array($action, $orderingActions) && $role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Admin hanya bertugas mengelola restoran dan tidak dapat melakukan pemesanan makanan.'
            ], 403);
        }
        if (in_array($action, $adminActions) && $role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Admin only.'], 403);
        }

        try {
            return match ($action) {
                'get_menu'             => $this->getMenu($request),
                'login'                => $this->loginApi($request),
                'register'             => $this->registerApi($request),
                'check_session'        => $this->checkSession($request),
                'logout'               => $this->logoutApi($request),
                'update_user_profile'  => $this->updateProfile($request),
                'add_to_cart'          => $this->addToCart($request),
                'update_cart'          => $this->updateCart($request),
                'remove_from_cart'     => $this->removeFromCart($request),
                'get_cart'             => $this->getCartAction($request),
                'checkout'             => $this->checkout($request),
                'validate_promo'       => $this->validatePromo($request),
                'get_recent_orders'    => $this->getRecentOrders($request),
                'get_order_details'    => $this->getOrderDetails($request),
                'get_dashboard_stats'  => $this->getDashboardStats(),
                'get_all_orders'       => $this->getAllOrders($request),
                'get_all_menus_admin'  => $this->getAllMenusAdmin(),
                'update_order_status'  => $this->updateOrderStatus($request),
                'get_all_customers'    => $this->getAllCustomers($request),
                'generate_sales_report'=> $this->generateSalesReport($request),
                'add_menu'             => $this->addMenu($request),
                'update_menu'          => $this->updateMenuAction($request),
                'delete_menu'          => $this->deleteMenuAction($request),
                default                => response()->json(['success' => false, 'message' => 'Aksi tidak valid: ' . $action], 404),
            };
        } catch (\Exception $e) {
            \Log::error('API Error ['.$action.']: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Kesalahan internal server.'], 500);
        }
    }

    // ── Public Actions ─────────────────────────────────────────────────────────

    private function getMenu(Request $request)
    {
        $category = $request->query('category', 'all');
        $query = DB::table('menu');
        if ($category !== 'all') {
            $query->where('category', $category);
        } else {
            $query->orderByRaw("FIELD(category,'original','pedas','paket hemat','minuman')");
        }
        $menu = $query->orderBy('name')->get()->map(fn($r) => [
            'id'              => (int)$r->menu_id,
            'name'            => htmlspecialchars($r->name),
            'price'           => (float)$r->price,
            'price_formatted' => 'Rp ' . number_format($r->price, 0, ',', '.'),
            'category'        => $r->category,
            'image'           => htmlspecialchars($r->image),
            'rating'          => (float)($r->rating ?? 0),
            'description'     => htmlspecialchars($r->description ?? ''),
            'stock'           => (int)$r->stock,
        ]);
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['menu' => $menu]]);
    }

    private function loginApi(Request $request)
    {
        $id   = trim($request->post('email_or_phone', ''));
        $pass = $request->post('password', '');
        $role = trim($request->post('role', 'user'));
        if (!$id || !$pass) {
            return response()->json(['success' => false, 'message' => 'Data wajib diisi.']);
        }
        $user = DB::table('users')->where(function($q) use ($id) {
            $q->where('email', $id)->orWhere('phone', $id);
        })->where('role', $role)->first();
        if ($user && Hash::check($pass, $user->password)) {
            $request->session()->put('user_id', $user->user_id);
            $request->session()->put('name',    $user->name);
            $request->session()->put('role',    $user->role);
            \Illuminate\Support\Facades\Auth::loginUsingId($user->user_id);
            $request->session()->save();
            return response()->json(['success' => true, 'message' => 'Login berhasil', 'data' => [
                'user' => ['id' => $user->user_id, 'name' => $user->name, 'role' => $user->role]
            ]]);
        }
        return response()->json(['success' => false, 'message' => 'Email/password atau peran salah.']);
    }

    private function registerApi(Request $request)
    {
        $name  = trim($request->post('name', ''));
        $email = trim($request->post('email', ''));
        $pass  = $request->post('password', '');
        if (!$name || !$email || !$pass) return response()->json(['success' => false, 'message' => 'Data wajib diisi.']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return response()->json(['success' => false, 'message' => 'Format email tidak valid.']);
        if (DB::table('users')->where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'Email sudah terdaftar.']);
        }
        $newId = DB::table('users')->insertGetId(['name' => $name, 'email' => $email, 'password' => Hash::make($pass), 'role' => 'user', 'created_at' => now()]);
        $request->session()->put('user_id', $newId);
        $request->session()->put('name',    $name);
        $request->session()->put('role',    'user');
        \Illuminate\Support\Facades\Auth::loginUsingId($newId);
        $request->session()->save();
        return response()->json(['success' => true, 'message' => 'Pendaftaran berhasil!']);
    }

    private function checkSession(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $userId = $user->user_id;
            $name   = $user->name;
            $role   = $user->role;
            if (!$request->session()->has('user_id')) {
                $request->session()->put('user_id', $userId);
                $request->session()->put('name',    $name);
                $request->session()->put('role',    $role);
            }
        } else {
            $userId = $request->session()->get('user_id');
            $name   = $request->session()->get('name');
            $role   = $request->session()->get('role', 'user');
            if ($userId) {
                \Illuminate\Support\Facades\Auth::loginUsingId($userId);
            }
        }

        if ($userId) {
            $cartData = ($role === 'admin')
                ? ['count' => 0, 'subtotal' => 0, 'subtotal_formatted' => 'Rp 0', 'items' => []]
                : $this->getCartData($userId);

            return response()->json(['success' => true, 'message' => 'Logged in.', 'data' => [
                'loggedIn'               => true,
                'user'                   => ['id' => $userId, 'name' => $name, 'role' => $role],
                'cart_count'             => $cartData['count'],
                'cart_subtotal'          => $cartData['subtotal'],
                'cart_subtotal_formatted'=> $cartData['subtotal_formatted'],
                'cart_items'             => $cartData['items'],
            ]]);
        }
        return response()->json(['success' => true, 'message' => 'Not logged in.', 'data' => ['loggedIn' => false]]);
    }

    private function logoutApi(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }

    // ── User Actions ────────────────────────────────────────────────────────────

    private function updateProfile(Request $request)
    {
        $userId  = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $name    = trim($request->post('name', ''));
        $phone   = trim($request->post('phone', ''));
        $address = trim($request->post('address', ''));
        if (!$name) return response()->json(['success' => false, 'message' => 'Nama tidak boleh kosong.'], 400);
        DB::table('users')->where('user_id', $userId)->update(['name' => $name, 'phone' => $phone, 'address' => $address]);
        $request->session()->put('name', $name);
        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    }

    private function getCartData($userId)
    {
        if (!$userId) {
            return ['count' => 0, 'subtotal' => 0, 'subtotal_formatted' => 'Rp 0', 'items' => []];
        }

        $rows = DB::table('cart as c')
            ->join('menu as m', 'c.menu_id', '=', 'm.menu_id')
            ->where('c.user_id', $userId)
            ->select('c.menu_id', 'c.quantity', 'm.name', 'm.price', 'm.category', 'm.image', 'm.stock')
            ->orderBy('c.cart_id', 'desc')
            ->get();

        $subtotal = 0;
        $count = 0;
        $items = [];

        foreach ($rows as $r) {
            $qty = (int)$r->quantity;
            $price = (float)$r->price;
            $itemSubtotal = $price * $qty;
            $subtotal += $itemSubtotal;
            $count += $qty;

            $items[] = [
                'id'                 => (int)$r->menu_id,
                'menu_id'            => (int)$r->menu_id,
                'name'               => $r->name,
                'price'              => $price,
                'price_formatted'    => 'Rp ' . number_format($price, 0, ',', '.'),
                'subtotal'           => $itemSubtotal,
                'subtotal_formatted' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
                'quantity'           => $qty,
                'category'           => $r->category,
                'image'              => $r->image,
                'stock'              => (int)$r->stock,
            ];
        }

        return [
            'count'              => $count,
            'subtotal'           => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'items'              => $items,
        ];
    }

    private function getCartAction(Request $request)
    {
        $userId = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $cartData = $this->getCartData($userId);
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $cartData]);
    }

    private function addToCart(Request $request)
    {
        $userId = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $menuId = (int)$request->post('menu_id', 0);
        if ($menuId <= 0) return response()->json(['success' => false, 'message' => 'Input tidak valid.'], 400);
        $menu = DB::table('menu')->where('menu_id', $menuId)->first();
        if (!$menu) return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan.'], 404);
        $stock = (int)$menu->stock;
        if ($stock <= 0) return response()->json(['success' => false, 'message' => 'Stok habis.'], 400);
        $cartItem = DB::table('cart')->where('user_id', $userId)->where('menu_id', $menuId)->first();
        $inCart   = $cartItem ? (int)$cartItem->quantity : 0;
        if ($inCart + 1 > $stock) {
            return response()->json(['success' => false, 'message' => "Stok tersisa {$stock} buah."], 400);
        }
        if ($cartItem) {
            DB::table('cart')->where('user_id', $userId)->where('menu_id', $menuId)->update(['quantity' => $inCart + 1]);
        } else {
            DB::table('cart')->insert(['user_id' => $userId, 'menu_id' => $menuId, 'quantity' => 1]);
        }
        $cartData = $this->getCartData($userId);
        return response()->json([
            'success' => true,
            'message' => 'Item ditambahkan ke keranjang.',
            'data'    => [
                'new_cart_count'          => $cartData['count'],
                'cart_count'              => $cartData['count'],
                'cart_subtotal'           => $cartData['subtotal'],
                'cart_subtotal_formatted' => $cartData['subtotal_formatted'],
                'cart_items'              => $cartData['items'],
            ]
        ]);
    }

    private function updateCart(Request $request)
    {
        $userId   = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $menuId   = (int)$request->post('menu_id', 0);
        $newQty   = (int)$request->post('quantity', 0);
        if ($menuId <= 0 || $newQty <= 0) return response()->json(['success' => false, 'message' => 'Input tidak valid.'], 400);
        $menu = DB::table('menu')->where('menu_id', $menuId)->first();
        if (!$menu) return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan.'], 404);
        if ($newQty > (int)$menu->stock) {
            return response()->json(['success' => false, 'message' => "Stok tidak mencukupi. Maks {$menu->stock}."], 400);
        }
        DB::table('cart')->where('user_id', $userId)->where('menu_id', $menuId)->update(['quantity' => $newQty]);
        $cartData = $this->getCartData($userId);
        return response()->json([
            'success' => true,
            'message' => 'Keranjang diperbarui.',
            'data'    => [
                'cart_count'              => $cartData['count'],
                'cart_subtotal'           => $cartData['subtotal'],
                'cart_subtotal_formatted' => $cartData['subtotal_formatted'],
                'cart_items'              => $cartData['items'],
            ]
        ]);
    }

    private function removeFromCart(Request $request)
    {
        $userId = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $menuId = (int)$request->post('menu_id', 0);
        if ($menuId <= 0) return response()->json(['success' => false, 'message' => 'Input tidak valid.'], 400);
        $deleted = DB::table('cart')->where('user_id', $userId)->where('menu_id', $menuId)->delete();
        $cartData = $this->getCartData($userId);
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang.',
                'data'    => [
                    'cart_count'              => $cartData['count'],
                    'cart_subtotal'           => $cartData['subtotal'],
                    'cart_subtotal_formatted' => $cartData['subtotal_formatted'],
                    'cart_items'              => $cartData['items'],
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
    }

    private function checkout(Request $request)
    {
        $userId = $request->session()->get('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $items  = json_decode($request->post('items', '[]'), true) ?: [];
        $paymentMethod = in_array($request->post('payment_method'), ['cod','transfer','pickup']) ? $request->post('payment_method') : 'cod';
        $deliveryType  = in_array($request->post('delivery_type'), ['delivery','pickup']) ? $request->post('delivery_type') : 'delivery';
        $notes         = htmlspecialchars(trim($request->post('notes', '')));
        $promoCode     = strtoupper(trim($request->post('promo_code', '')));
        if (empty($items)) return response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 400);

        $userProfile = DB::table('users')->where('user_id', $userId)->first();
        if (!$userProfile) return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        if ($deliveryType === 'delivery' && (empty($userProfile->phone) || empty($userProfile->address))) {
            return response()->json(['success' => false, 'message' => 'Harap isi alamat dan nomor telepon dulu.'], 400);
        }

        DB::beginTransaction();
        try {
            $menuIds  = array_column($items, 'id');
            $menus    = DB::table('menu')->whereIn('menu_id', $menuIds)->get()->keyBy('menu_id');
            $totalAmt = 0;
            foreach ($items as &$item) {
                $m = $menus[$item['id']] ?? null;
                $item['price_raw'] = $m ? (float)$m->price : 0;
                $item['name']      = $m ? $m->name : $item['name'];
                $totalAmt += $item['price_raw'] * $item['quantity'];
            }
            unset($item);

            $discount = 0;
            $appliedPromo = null;
            if ($promoCode) {
                $promo = DB::table('promotions')->where('code', $promoCode)->where('is_active', 1)
                    ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()); })->first();
                if ($promo && $totalAmt >= (float)$promo->min_order) {
                    $discount = $promo->discount_type === 'percentage'
                        ? (int)round($totalAmt * ($promo->discount_value / 100))
                        : (int)$promo->discount_value;
                    $discount = min($discount, $totalAmt);
                    $appliedPromo = $promoCode;
                }
            }
            $finalAmt = $totalAmt - $discount;
            $deliveryAddress = $deliveryType === 'pickup' ? 'PICKUP - Ambil Sendiri' : ($userProfile->address ?? '');

            $orderId = DB::table('orders')->insertGetId([
                'user_id'          => $userId,
                'total_amount'     => $finalAmt,
                'status'           => 'pending',
                'payment_method'   => $paymentMethod,
                'customer_name'    => $userProfile->name,
                'customer_phone'   => $userProfile->phone ?? '',
                'delivery_address' => $deliveryAddress,
                'notes'            => $notes,
                'delivery_type'    => $deliveryType,
                'discount_amount'  => $discount,
                'promo_code'       => $appliedPromo,
                'order_date'       => now(),
            ]);
            $request->session()->put('last_order_id', $orderId);

            foreach ($items as $item) {
                $subtotal = $item['price_raw'] * $item['quantity'];
                DB::table('order_details')->insert(['order_id' => $orderId, 'menu_id' => $item['id'], 'quantity' => $item['quantity'], 'price' => $item['price_raw'], 'subtotal' => $subtotal]);
                DB::table('menu')->where('menu_id', $item['id'])->decrement('stock', $item['quantity']);
            }
            DB::table('cart')->where('user_id', $userId)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order berhasil dibuat.', 'data' => ['order_id' => $orderId]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function validatePromo(Request $request)
    {
        $code  = strtoupper(trim($request->input('code', '')));
        $total = (float)$request->input('total', 0);
        if (!$code) return response()->json(['success' => false, 'message' => 'Kode promo wajib diisi.'], 400);
        $promo = DB::table('promotions')->where('code', $code)->where('is_active', 1)
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()); })->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Kode promo tidak valid.'], 404);
        if ($total > 0 && $total < (float)$promo->min_order) {
            return response()->json(['success' => false, 'message' => 'Minimum order Rp ' . number_format($promo->min_order, 0, ',', '.') . '.'], 400);
        }
        return response()->json(['success' => true, 'message' => 'Promo valid!', 'data' => [
            'code'           => $promo->code,
            'discount_type'  => $promo->discount_type,
            'discount_value' => (float)$promo->discount_value,
            'min_order'      => (float)$promo->min_order,
            'description'    => $promo->description,
        ]]);
    }

    private function getRecentOrders(Request $request)
    {
        $limit = min((int)$request->query('limit', 5), 10);
        $rows = DB::select("
            SELECT o.order_id, o.customer_name, o.order_date,
                (SELECT m.name FROM order_details od JOIN menu m ON od.menu_id = m.menu_id WHERE od.order_id = o.order_id ORDER BY od.order_detail_id ASC LIMIT 1) as first_item,
                (SELECT COUNT(*) FROM order_details od2 WHERE od2.order_id = o.order_id) as item_count
            FROM orders o ORDER BY o.order_id DESC LIMIT ?", [$limit]);
        $orders = array_map(fn($r) => [
            'customer_name' => explode(' ', $r->customer_name)[0],
            'item_summary'  => ($r->first_item ?? 'menu Cimohot') . ($r->item_count > 1 ? ' + ' . ($r->item_count - 1) . ' item lain' : ''),
        ], $rows);
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['orders' => $orders]]);
    }

    // ── Admin Actions ───────────────────────────────────────────────────────────

    private function getOrderDetails(Request $request)
    {
        $orderId = (int)$request->query('order_id', 0);
        if ($orderId <= 0) return response()->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        $order = DB::table('orders')->where('order_id', $orderId)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        $items = DB::table('order_details as od')->join('menu as m', 'od.menu_id', '=', 'm.menu_id')
            ->where('od.order_id', $orderId)->select('od.quantity','od.price','od.subtotal','m.name as menu_name')->get();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['order_info' => $order, 'items' => $items]]);
    }

    private function getDashboardStats()
    {
        $today = DB::table('orders')->whereDate('order_date', today())->selectRaw('SUM(total_amount) as revenue, COUNT(order_id) as total_orders')->first();
        $newCust = DB::table('users')->whereDate('created_at', today())->count();
        $topProduct = DB::table('order_details as od')->join('menu as m','od.menu_id','m.menu_id')->selectRaw('m.name, COUNT(od.menu_id) as cnt')->groupBy('m.menu_id','m.name')->orderByDesc('cnt')->value('name');
        $pending = DB::table('orders')->whereIn('status',['pending','diproses'])->count();
        $chartData = DB::table('orders')->where('order_date', '>=', now()->subDays(6))->selectRaw('DATE(order_date) as a_date, SUM(total_amount) as daily_revenue')->groupByRaw('DATE(order_date)')->orderBy('a_date')->pluck('daily_revenue', 'a_date')->toArray();
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $key = now()->subDays($i)->format('Y-m-d');
            $salesData[$key] = $chartData[$key] ?? 0;
        }
        $recent = DB::table('orders as o')->join('users as u','o.user_id','u.user_id')->orderByDesc('o.order_id')->limit(5)->select('o.order_id','u.name as customer_name','o.total_amount','o.status')->get();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => [
            'revenue'       => (float)($today->revenue ?? 0),
            'orders_today'  => (int)($today->total_orders ?? 0),
            'new_customers' => $newCust,
            'top_product'   => $topProduct ?? 'N/A',
            'sales_chart'   => ['labels' => array_map(fn($d) => date('d M', strtotime($d)), array_keys($salesData)), 'values' => array_values($salesData)],
            'recent_orders' => $recent,
            'pending_orders'=> $pending,
        ]]);
    }

    private function getAllOrders(Request $request)
    {
        $q = DB::table('orders as o')->join('users as u','o.user_id','u.user_id')->select('o.order_id','u.name as customer_name','o.order_date','o.total_amount','o.status');
        if ($cid = $request->query('customer_id')) $q->where('o.user_id', (int)$cid);
        $orders = $q->orderByDesc('o.order_id')->get();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['orders' => $orders]]);
    }

    private function getAllMenusAdmin()
    {
        $menu = DB::table('menu')->select('menu_id as id','name','price','stock','category','image','description')->orderBy('name')->get();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['menu' => $menu]]);
    }

    private function updateOrderStatus(Request $request)
    {
        $orderId  = (int)$request->post('order_id', 0);
        $newStatus = trim($request->post('new_status', ''));
        if ($orderId <= 0 || !in_array($newStatus, ['pending','diproses','selesai','dibatalkan'])) {
            return response()->json(['success' => false, 'message' => 'Input tidak valid.'], 400);
        }
        if (!DB::table('orders')->where('order_id', $orderId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }
        DB::table('orders')->where('order_id', $orderId)->update(['status' => $newStatus]);
        return response()->json(['success' => true, 'message' => "Status #$orderId → $newStatus."]);
    }

    private function getAllCustomers(Request $request)
    {
        $search = trim($request->query('search', ''));
        $q = DB::table('users')->where('role','user')->select('user_id','name','email','phone','created_at');
        if ($search) $q->where(fn($w) => $w->where('name','like',"%$search%")->orWhere('email','like',"%$search%"));
        $customers = $q->orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['customers' => $customers]]);
    }

    private function generateSalesReport(Request $request)
    {
        $start = $request->post('start_date','');
        $end   = $request->post('end_date','');
        if (!$start || !$end) return response()->json(['success' => false, 'message' => 'Tanggal wajib diisi.'], 400);
        $summary = DB::table('orders')->where('status','selesai')->whereBetween(DB::raw('DATE(order_date)'), [$start, $end])
            ->selectRaw('SUM(total_amount) as total_revenue, COUNT(order_id) as total_orders, AVG(total_amount) as average_transaction')->first();
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $summary]);
    }

    private function addMenu(Request $request)
    {
        if (!$request->hasFile('image') || $request->file('image')->getError() !== UPLOAD_ERR_OK) {
            return response()->json(['success' => false, 'message' => 'Gambar wajib diisi.'], 400);
        }
        $img  = $request->file('image');
        $path = 'assets/uploads/' . uniqid() . '-' . $img->getClientOriginalName();
        $img->move(public_path('assets/uploads'), basename($path));
        DB::table('menu')->insert([
            'name'        => $request->post('name'),
            'description' => $request->post('description',''),
            'price'       => (float)$request->post('price',0),
            'stock'       => (int)$request->post('stock',0),
            'category'    => $request->post('category',''),
            'image'       => $path,
        ]);
        return response()->json(['success' => true, 'message' => 'Menu baru ditambahkan.']);
    }

    private function updateMenuAction(Request $request)
    {
        $menuId = (int)$request->post('menu_id', 0);
        if ($menuId <= 0) return response()->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        $data = [
            'name'        => $request->post('name'),
            'description' => $request->post('description',''),
            'price'       => (float)$request->post('price',0),
            'stock'       => (int)$request->post('stock',0),
            'category'    => $request->post('category'),
        ];
        if ($request->hasFile('image') && $request->file('image')->getError() === UPLOAD_ERR_OK) {
            $old = DB::table('menu')->where('menu_id', $menuId)->value('image');
            if ($old && file_exists(public_path($old))) @unlink(public_path($old));
            $img  = $request->file('image');
            $path = 'assets/uploads/' . uniqid() . '-' . $img->getClientOriginalName();
            $img->move(public_path('assets/uploads'), basename($path));
            $data['image'] = $path;
        }
        DB::table('menu')->where('menu_id', $menuId)->update($data);
        return response()->json(['success' => true, 'message' => 'Menu diperbarui.']);
    }

    private function deleteMenuAction(Request $request)
    {
        $menuId = (int)$request->post('menu_id', 0);
        if ($menuId <= 0) return response()->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        $img = DB::table('menu')->where('menu_id', $menuId)->value('image');
        DB::table('menu')->where('menu_id', $menuId)->delete();
        if ($img && file_exists(public_path($img))) @unlink(public_path($img));
        return response()->json(['success' => true, 'message' => 'Menu dihapus.']);
    }
}