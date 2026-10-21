<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function getStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Pendapatan hari ini & kemarin
        $revenueToday = (float) Order::whereDate('order_date', $today)
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_amount');

        $revenueYesterday = (float) Order::whereDate('order_date', $yesterday)
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_amount');

        $revenueDiff = $revenueYesterday > 0
            ? round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100, 1)
            : ($revenueToday > 0 ? 100 : 0);

        // 2. Pesanan hari ini & kemarin
        $ordersToday = Order::whereDate('order_date', $today)->count();
        $ordersYesterday = Order::whereDate('order_date', $yesterday)->count();
        $ordersDiff = $ordersYesterday > 0
            ? round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 1)
            : ($ordersToday > 0 ? 100 : 0);

        // 3. Pelanggan baru hari ini & total pelanggan
        $customersToday = User::where('role', '!=', 'admin')
            ->whereDate('created_at', $today)
            ->count();
        $totalCustomers = User::where('role', '!=', 'admin')->count();

        // 4. Menu Terlaris
        $topProductRow = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.menu_id')
            ->select('menu.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('menu.menu_id', 'menu.name')
            ->orderByDesc('total_sold')
            ->first();

        $topProductName = $topProductRow ? $topProductRow->name : 'Belum Ada';
        $topProductSold = $topProductRow ? (int)$topProductRow->total_sold : 0;

        // 5. Pesanan Pending, Diproses, Selesai
        $pendingCount = Order::where('status', 'pending')->count();
        $processingCount = Order::where('status', 'diproses')->count();
        $completedCount = Order::where('status', 'selesai')->count();

        // 6. Active pending order IDs (for client chime detection)
        $activePendingIds = Order::where('status', 'pending')
            ->orderByDesc('order_id')
            ->pluck('order_id')
            ->toArray();

        // 7. Recent Orders (6 data dengan rincian item)
        $recentOrders = Order::with(['user', 'items.menu'])
            ->orderByDesc('order_id')
            ->limit(6)
            ->get()
            ->map(function ($order) {
                $itemNames = $order->items->map(fn($it) => ($it->menu ? $it->menu->name : 'Menu') . ' (' . $it->quantity . 'x)')->implode(', ');
                return [
                    'order_id' => $order->order_id,
                    'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tamu'),
                    'customer_phone' => $order->customer_phone ?: ($order->user ? $order->user->phone : '-'),
                    'total_amount' => (float)$order->total_amount,
                    'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'status' => $order->status,
                    'payment_method' => strtoupper($order->payment_method ?: 'COD'),
                    'order_date' => $order->order_date ? $order->order_date->format('d M Y H:i') : '-',
                    'items_summary' => $itemNames ?: 'Tidak ada item',
                    'items_count' => $order->items->count(),
                ];
            });

        // 8. Low Stock Menus (Stok <= 5 porsi)
        $lowStockMenus = Menu::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(6)
            ->get()
            ->map(function ($menu) {
                return [
                    'menu_id' => $menu->menu_id,
                    'name' => $menu->name,
                    'category' => $menu->category,
                    'stock' => (int)$menu->stock,
                    'image_url' => $menu->image_url,
                    'price_formatted' => $menu->formatted_price,
                ];
            });

        // 9. Payment Breakdown (Hari ini: Transfer/QRIS vs Tunai/COD)
        $paymentStats = Order::whereDate('order_date', $today)
            ->where('status', '!=', 'dibatalkan')
            ->select('payment_method', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_rev'))
            ->groupBy('payment_method')
            ->get();

        $transferRev = 0; $transferCount = 0;
        $cashRev = 0; $cashCount = 0;

        foreach ($paymentStats as $ps) {
            $method = strtolower($ps->payment_method ?? '');
            if (in_array($method, ['transfer', 'qris', 'bank', 'online'])) {
                $transferRev += (float)$ps->total_rev;
                $transferCount += (int)$ps->total_orders;
            } else {
                $cashRev += (float)$ps->total_rev;
                $cashCount += (int)$ps->total_orders;
            }
        }

        $totalPayOrders = ($transferCount + $cashCount) ?: 1;
        $transferPct = round(($transferCount / $totalPayOrders) * 100);
        $cashPct = 100 - $transferPct;

        return response()->json([
            'success' => true,
            'data' => [
                'revenue_today' => $revenueToday,
                'revenue_formatted' => 'Rp ' . number_format($revenueToday, 0, ',', '.'),
                'revenue_diff' => $revenueDiff,
                'orders_today' => $ordersToday,
                'orders_diff' => $ordersDiff,
                'customers_today' => $customersToday,
                'total_customers' => $totalCustomers,
                'top_product' => $topProductName,
                'top_product_sold' => $topProductSold,
                'pending_count' => $pendingCount,
                'processing_count' => $processingCount,
                'completed_count' => $completedCount,
                'active_pending_ids' => $activePendingIds,
                'recent_orders' => $recentOrders,
                'low_stock_menus' => $lowStockMenus,
                'payment_breakdown' => [
                    'transfer_rev' => $transferRev,
                    'transfer_rev_formatted' => 'Rp ' . number_format($transferRev, 0, ',', '.'),
                    'transfer_count' => $transferCount,
                    'transfer_pct' => $transferPct,
                    'cash_rev' => $cashRev,
                    'cash_rev_formatted' => 'Rp ' . number_format($cashRev, 0, ',', '.'),
                    'cash_count' => $cashCount,
                    'cash_pct' => $cashPct,
                ]
            ]
        ]);
    }

    public function quickRestock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan.'], 404);
        }

        $menu->stock = (int)$request->stock;
        $menu->save();

        return response()->json([
            'success' => true,
            'message' => "Stok {$menu->name} berhasil diperbarui menjadi {$menu->stock} porsi.",
            'data' => [
                'menu_id' => $menu->menu_id,
                'stock' => $menu->stock,
            ]
        ]);
    }

    public function getSalesChart(Request $request)
    {
        $days = (int) $request->input('days', 7);
        if (!in_array($days, [7, 14, 30])) {
            $days = 7;
        }

        // 1. Line Chart: Penjualan harian
        $labels = [];
        $revenueData = [];
        $ordersCountData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->translatedFormat('d M');

            $dayRevenue = Order::whereDate('order_date', $dateStr)
                ->where('status', '!=', 'dibatalkan')
                ->sum('total_amount');

            $dayOrders = Order::whereDate('order_date', $dateStr)->count();

            $revenueData[] = (float) $dayRevenue;
            $ordersCountData[] = $dayOrders;
        }

        // 2. Donut Chart: Penjualan per Kategori Menu
        $categorySales = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.menu_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->where('orders.status', '!=', 'dibatalkan')
            ->select('menu.category', DB::raw('SUM(order_details.quantity) as total_qty'), DB::raw('SUM(order_details.subtotal) as total_revenue'))
            ->groupBy('menu.category')
            ->get();

        $catLabels = [];
        $catData = [];
        foreach ($categorySales as $cat) {
            $catLabels[] = $cat->category ?: 'Lainnya';
            $catData[] = (int) $cat->total_qty;
        }

        // 3. Status Distribution
        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'selesai' => Order::where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('status', 'dibatalkan')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'line_chart' => [
                    'labels' => $labels,
                    'revenue' => $revenueData,
                    'orders' => $ordersCountData,
                ],
                'category_chart' => [
                    'labels' => $catLabels,
                    'data' => $catData,
                ],
                'status_distribution' => $statusCounts,
            ]
        ]);
    }

    public function getNotifications()
    {
        $pendingOrders = Order::where('status', 'pending')
            ->orderByDesc('order_id')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'customer' => $order->customer_name ?: 'Pelanggan',
                    'total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'time_ago' => $order->order_date ? $order->order_date->diffForHumans() : 'baru saja',
                ];
            });

        $pendingCount = Order::where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_count' => $pendingCount,
                'orders' => $pendingOrders,
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }
}
