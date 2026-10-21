<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function getAll(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->get()->map(function ($customer) {
            $orderStats = Order::where('user_id', $customer->user_id)
                ->select(
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('COALESCE(SUM(CASE WHEN status != "dibatalkan" THEN total_amount ELSE 0 END), 0) as total_spent')
                )
                ->first();

            return [
                'user_id' => $customer->user_id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?: '-',
                'address' => $customer->address ?: '-',
                'created_at' => $customer->created_at ? $customer->created_at->format('d M Y') : '-',
                'total_orders' => (int) ($orderStats->total_orders ?? 0),
                'total_spent' => (float) ($orderStats->total_spent ?? 0),
                'total_spent_formatted' => 'Rp ' . number_format($orderStats->total_spent ?? 0, 0, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'customers' => $customers,
                'total' => $customers->count(),
            ]
        ]);
    }

    public function getHistory($id)
    {
        $customer = User::find($id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan.'
            ], 404);
        }

        $orders = Order::where('user_id', $id)
            ->with('items.menu')
            ->orderByDesc('order_date')
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'total_amount' => (float)$order->total_amount,
                    'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'status' => $order->status,
                    'payment_method' => strtoupper($order->payment_method ?: 'COD'),
                    'order_date' => $order->order_date ? $order->order_date->format('d M Y, H:i') : '-',
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->menu ? $item->menu->name : 'Menu',
                            'quantity' => (int)$item->quantity,
                            'price' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                            'subtotal' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'user_id' => $customer->user_id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?: '-',
                ],
                'orders' => $orders
            ]
        ]);
    }
}
