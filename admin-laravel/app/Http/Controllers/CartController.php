<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $userId = session('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        if (!$userId) {
            return redirect()->route('login')->with('redirect_after', 'cart');
        }

        $role = session('role') ?: (\Illuminate\Support\Facades\Auth::user()?->role ?? 'user');
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Akun Admin hanya bertugas mengelola pesanan & sistem, tidak dapat melakukan checkout pemesanan.');
        }

        $cartRows = DB::table('cart as c')
            ->join('menu as m', 'c.menu_id', '=', 'm.menu_id')
            ->where('c.user_id', $userId)
            ->select('c.menu_id', 'c.quantity', 'm.name', 'm.price', 'm.category', 'm.image')
            ->get();

        $cartItems = $cartRows->map(function ($r) {
            return [
                'id'        => (int)$r->menu_id,
                'name'      => $r->name,
                'price'     => 'Rp ' . number_format($r->price, 0, ',', '.'),
                'price_raw' => (float)$r->price,
                'category'  => $r->category,
                'image'     => $r->image,
                'quantity'  => (int)$r->quantity,
            ];
        })->toArray();

        $userProfile = DB::table('users')
            ->where('user_id', $userId)
            ->select('name', 'phone', 'address')
            ->first();

        return view('cart.index', [
            'cartItems'   => $cartItems,
            'userProfile' => $userProfile ? (array)$userProfile : [],
        ]);
    }

    public function confirmation(Request $request)
    {
        $userId = session('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        $orderId = $request->query('order_id') ?? session('last_order_id');

        if (!$orderId && $userId) {
            $latest = DB::table('orders')->where('user_id', $userId)->orderByDesc('order_id')->first();
            if ($latest) {
                $orderId = $latest->order_id;
            }
        }

        if (!$orderId) {
            return redirect()->route('home');
        }

        $query = DB::table('orders')->where('order_id', $orderId);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $order = $query->first();

        if (!$order) {
            return redirect()->route('home');
        }

        $order = (array)$order;

        $orderItems = DB::table('order_details as od')
            ->join('menu as m', 'od.menu_id', '=', 'm.menu_id')
            ->where('od.order_id', $orderId)
            ->select('od.quantity', 'od.price', 'od.subtotal', 'm.name as menu_name', 'm.image')
            ->get()
            ->map(fn($item) => (array)$item)
            ->toArray();

        session()->forget('last_order_id');

        $isPickup = ($order['delivery_type'] ?? 'delivery') === 'pickup';
        $isCOD    = in_array($order['payment_method'], ['cod', 'pickup']);
        $isTransfer = $order['payment_method'] === 'transfer';
        $subtotalRaw = array_sum(array_column($orderItems, 'subtotal'));

        // WhatsApp message builder
        $waLines = "Halo Cimohot! Saya mau konfirmasi pesanan:\n\n";
        $waLines .= "No Pesanan: #" . $order['order_id'] . "\n";
        foreach ($orderItems as $itm) {
            $waLines .= "- " . $itm['menu_name'] . " x" . $itm['quantity'] . " = Rp " . number_format($itm['subtotal'], 0, ',', '.') . "\n";
        }
        if (($order['discount_amount'] ?? 0) > 0) {
            $waLines .= "Diskon: -Rp " . number_format($order['discount_amount'], 0, ',', '.') . "\n";
        }
        $waLines .= "Total: Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n";
        $waLines .= "Metode: " . strtoupper($order['payment_method']) . "\n";
        if ($isPickup) {
            $waLines .= "Pickup: Ambil Sendiri\n";
        } else {
            $waLines .= "Alamat: " . ($order['delivery_address'] ?? '-') . "\n";
        }
        if (!empty($order['notes'])) {
            $waLines .= "Catatan: " . $order['notes'] . "\n";
        }
        if ($isTransfer) {
            $waLines .= "\n*Bukti transfer saya lampirkan di sini.*";
        }
        $waURL = "https://wa.me/6285930194086?text=" . rawurlencode($waLines);

        return view('cart.confirmation', compact(
            'order', 'orderItems', 'isPickup', 'isCOD', 'isTransfer', 'subtotalRaw', 'waURL'
        ));
    }
}
