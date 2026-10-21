<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    private function getReportData(Request $request)
    {
        $startDate = $request->input('start_date') ?: Carbon::today()->startOfMonth()->format('Y-m-d');
        $endDate = $request->input('end_date') ?: Carbon::today()->format('Y-m-d');

        $query = Order::with('items.menu')
            ->whereDate('order_date', '>=', $startDate)
            ->whereDate('order_date', '<=', $endDate);

        $allOrders = (clone $query)->orderByDesc('order_date')->get();

        $totalRevenue = (float) $allOrders->where('status', '!=', 'dibatalkan')->sum('total_amount');
        $totalOrders = $allOrders->count();
        $completedOrders = $allOrders->where('status', 'selesai')->count();
        $processingOrders = $allOrders->where('status', 'diproses')->count();
        $pendingOrders = $allOrders->where('status', 'pending')->count();
        $cancelledOrders = $allOrders->where('status', 'dibatalkan')->count();

        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / ($totalOrders - $cancelledOrders ?: 1)) : 0;

        // Top menu di periode ini
        $topMenus = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.menu_id')
            ->whereDate('orders.order_date', '>=', $startDate)
            ->whereDate('orders.order_date', '<=', $endDate)
            ->where('orders.status', '!=', 'dibatalkan')
            ->select('menu.name', 'menu.category', DB::raw('SUM(order_details.quantity) as total_qty'), DB::raw('SUM(order_details.subtotal) as total_rev'))
            ->groupBy('menu.menu_id', 'menu.name', 'menu.category')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_date_formatted' => Carbon::parse($startDate)->translatedFormat('d F Y'),
            'end_date_formatted' => Carbon::parse($endDate)->translatedFormat('d F Y'),
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'processing_orders' => $processingOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'avg_order_value' => $avgOrderValue,
            'avg_order_value_formatted' => 'Rp ' . number_format($avgOrderValue, 0, ',', '.'),
            'top_menus' => $topMenus,
            'orders' => $allOrders->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tamu'),
                    'total_amount' => (float)$order->total_amount,
                    'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'status' => $order->status,
                    'payment_method' => strtoupper($order->payment_method ?: 'COD'),
                    'order_date' => $order->order_date ? $order->order_date->format('d/m/Y H:i') : '-',
                ];
            }),
        ];
    }

    public function generate(Request $request)
    {
        $data = $this->getReportData($request);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $data['generated_at'] = Carbon::now()->translatedFormat('d F Y, H:i:s');

        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Laporan-Penjualan-Cimohot-' . $data['start_date'] . '-sampai-' . $data['end_date'] . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);

        $fileName = 'Laporan-Penjualan-Cimohot-' . $data['start_date'] . '-sampai-' . $data['end_date'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Judul
            fputcsv($handle, ['LAPORAN PENJUALAN CIMOHOT']);
            fputcsv($handle, ['Periode:', $data['start_date_formatted'] . ' s/d ' . $data['end_date_formatted']]);
            fputcsv($handle, ['Total Pendapatan:', $data['total_revenue_formatted']]);
            fputcsv($handle, ['Total Pesanan:', $data['total_orders']]);
            fputcsv($handle, ['Pesanan Selesai:', $data['completed_orders']]);
            fputcsv($handle, ['Pesanan Dibatalkan:', $data['cancelled_orders']]);
            fputcsv($handle, []);

            // Header Tabel
            fputcsv($handle, [
                'ID Pesanan',
                'Nama Pelanggan',
                'Metode Pembayaran',
                'Total (Rp)',
                'Status',
                'Tanggal Pesanan'
            ]);

            // Isi Data
            foreach ($data['orders'] as $order) {
                fputcsv($handle, [
                    '#' . $order['order_id'],
                    $order['customer_name'],
                    $order['payment_method'],
                    $order['total_amount'],
                    strtoupper($order['status']),
                    $order['order_date']
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
