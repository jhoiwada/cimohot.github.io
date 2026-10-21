<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Cimohot</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            border-bottom: 2px solid #FF3D2E;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
        }

        .brand-title {
            font-size: 22px;
            font-weight: bold;
            color: #FF3D2E;
            margin: 0;
        }

        .report-subtitle {
            font-size: 11px;
            color: #666666;
            margin-top: 4px;
        }

        .meta-info {
            text-align: right;
            font-size: 10px;
            color: #666666;
        }

        .summary-box {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            text-align: center;
        }

        .summary-table td {
            padding: 4px;
        }

        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #888888;
            font-weight: bold;
        }

        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #111111;
            margin-top: 2px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #222222;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th, .data-table td {
            border: 1px solid #E2E8F0;
            padding: 6px 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #F1F5F9;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-selesai { background-color: #D1FAE5; color: #065F46; }
        .badge-pending { background-color: #FEF3C7; color: #92400E; }
        .badge-diproses { background-color: #CFFAFE; color: #155E75; }
        .badge-dibatalkan { background-color: #FEE2E2; color: #991B1B; }

        .text-right { text-align: right; }
        .mono { font-family: monospace; }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="brand-title">Cimohot.</h1>
                    <div class="report-subtitle">Laporan Penjualan & Rekapitulasi Transaksi</div>
                </td>
                <td class="meta-info">
                    <div><strong>Periode:</strong> {{ $start_date_formatted }} s.d. {{ $end_date_formatted }}</div>
                    <div><strong>Dicetak:</strong> {{ $generated_at }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Eksekutif -->
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Omset</div>
                    <div class="summary-value" style="color:#FF3D2E;">{{ $total_revenue_formatted }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Pesanan</div>
                    <div class="summary-value">{{ $total_orders }}</div>
                </td>
                <td>
                    <div class="summary-label">Pesanan Selesai</div>
                    <div class="summary-value" style="color:#10B981;">{{ $completed_orders }}</div>
                </td>
                <td>
                    <div class="summary-label">Pesanan Batal</div>
                    <div class="summary-value" style="color:#EF4444;">{{ $cancelled_orders }}</div>
                </td>
                <td>
                    <div class="summary-label">Rata-Rata Transaksi</div>
                    <div class="summary-value">{{ $avg_order_value_formatted }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Menu Terlaris -->
    @if(count($top_menus) > 0)
    <div class="section-title">5 Menu Paling Banyak Terjual</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th class="text-right">Qty Terjual</th>
                <th class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($top_menus as $index => $menu)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $menu->name }}</strong></td>
                <td>{{ $menu->category }}</td>
                <td class="text-right mono">{{ $menu->total_qty }} porsi</td>
                <td class="text-right mono">Rp {{ number_format($menu->total_rev, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Daftar Transaksi -->
    <div class="section-title">Rincian Transaksi Transaksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Metode</th>
                <th class="text-right">Total Transaksi</th>
                <th>Status</th>
                <th>Waktu Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $o)
            <tr>
                <td class="mono">#{{ $o['order_id'] }}</td>
                <td>{{ $o['customer_name'] }}</td>
                <td>{{ $o['payment_method'] }}</td>
                <td class="text-right mono">{{ $o['total_formatted'] }}</td>
                <td>
                    <span class="badge badge-{{ $o['status'] }}">{{ $o['status'] }}</span>
                </td>
                <td>{{ $o['order_date'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:15px;color:#888;">Tidak ada data pesanan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Cimohot Command Center &bull; {{ config('app.url') }}
    </div>
</body>
</html>
