<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
    }

    public function getAll(Request $request)
    {
        $query = Order::with(['user', 'items.menu'])
            ->orderByDesc('order_id');

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter pencarian (ID, nama, atau no telp)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $perPage = (int) $request->input('per_page', 10);
        $orders = $query->paginate($perPage);

        $transformed = $orders->getCollection()->map(function ($order) {
            return [
                'order_id' => $order->order_id,
                'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tamu'),
                'customer_phone' => $order->customer_phone ?: ($order->user ? $order->user->phone : '-'),
                'total_amount' => (float) $order->total_amount,
                'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                'status' => $order->status,
                'payment_method' => strtoupper($order->payment_method ?: 'COD'),
                'delivery_address' => $order->delivery_address ?: '-',
                'notes' => $order->notes ?: '-',
                'order_date' => $order->order_date ? $order->order_date->format('d M Y, H:i') : '-',
                'items_count' => $order->items->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $transformed,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.menu'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        $items = $order->items->map(function ($item) {
            return [
                'detail_id' => $item->order_detail_id,
                'menu_id' => $item->menu_id,
                'menu_name' => $item->menu ? $item->menu->name : 'Menu telah dihapus',
                'category' => $item->menu ? $item->menu->category : '-',
                'image' => $item->menu ? $item->menu->image_url : null,
                'price' => (float)$item->price,
                'price_formatted' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                'quantity' => (int)$item->quantity,
                'subtotal' => (float)$item->subtotal,
                'subtotal_formatted' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->order_id,
                'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tamu'),
                'customer_email' => $order->user ? $order->user->email : '-',
                'customer_phone' => $order->customer_phone ?: ($order->user ? $order->user->phone : '-'),
                'delivery_address' => $order->delivery_address ?: 'Tidak ada alamat khusus',
                'notes' => $order->notes ?: 'Tidak ada catatan pesanan',
                'status' => $order->status,
                'payment_method' => strtoupper($order->payment_method ?: 'COD'),
                'total_amount' => (float)$order->total_amount,
                'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                'order_date' => $order->order_date ? $order->order_date->format('d F Y, H:i:s') : '-',
                'items' => $items,
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,dibatalkan'
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->order_id} berhasil diubah menjadi '{$order->status}'.",
            'data' => [
                'order_id' => $order->order_id,
                'status' => $order->status
            ]
        ]);
    }
}
