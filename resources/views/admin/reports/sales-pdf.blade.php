<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan {{ $startDate }} - {{ $endDate }}</title>
    <style>
        @page {
            margin: 20px 25px;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #EA580C;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #111827;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #6b7280;
            font-size: 9px;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-card {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .summary-title {
            font-size: 8px;
            text-transform: uppercase;
            color: #6B7280;
            font-weight: bold;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #EA580C;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 9px;
            border: 1px solid #C2410C;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #E5E7EB;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $settings['restaurant_name'] ?? 'AMARTA BOGA RESTO' }}</h1>
        <p>{{ $settings['restaurant_address'] ?? 'Jl. Jenderal Sudirman Kav. 88, Senayan, Jakarta Selatan' }} • Telp: {{ $settings['restaurant_phone'] ?? '0812-3456-7890' }}</p>
        <p style="font-weight: bold; color: #EA580C; font-size: 11px; margin-top: 4px;">
            LAPORAN REKAPITULASI PENJUALAN KASIR
        </p>
        <p>Periode Transaksi: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    <!-- Ringkasan Finansial -->
    <table class="summary-grid">
        <tr>
            <td width="25%" style="padding-right: 8px;">
                <div class="summary-card">
                    <div class="summary-title">Total Pendapatan Akhir</div>
                    <div class="summary-value" style="color: #EA580C;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%" style="padding-right: 8px;">
                <div class="summary-card">
                    <div class="summary-title">Jumlah Transaksi</div>
                    <div class="summary-value">{{ number_format($totalOrders, 0, ',', '.') }} Pesanan</div>
                </div>
            </td>
            <td width="25%" style="padding-right: 8px;">
                <div class="summary-card">
                    <div class="summary-title">Pajak PB1 Terkumpul</div>
                    <div class="summary-value">Rp {{ number_format($totalTax, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-title">Total Potongan Diskon</div>
                    <div class="summary-value" style="color: #059669;">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">No. Invoice</th>
                <th width="13%">Waktu</th>
                <th width="15%">Nama Tamu</th>
                <th width="10%">Tipe / Meja</th>
                <th width="10%">Kasir</th>
                <th width="10%" class="text-right">Subtotal</th>
                <th width="8%" class="text-right">Diskon</th>
                <th width="8%" class="text-right">PB1</th>
                <th width="12%" class="text-right">Total Akhir</th>
                <th width="12%" class="text-center">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="font-mono" style="font-weight: bold;">{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->table ? $order->table->name : strtoupper($order->order_type) }}</td>
                    <td>{{ $order->user?->name ?? 'Kasir' }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #059669;">
                        {{ $order->discount_amount > 0 ? '-Rp ' . number_format($order->discount_amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-mono">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center" style="font-size: 8px;">
                        {{ strtoupper($order->payments->pluck('payment_method')->implode('+') ?: 'CASH') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 15px; color: #9CA3AF;">
                        Tidak ada transaksi ditemukan pada rentang periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($orders->count() > 0)
            <tfoot>
                <tr style="background-color: #FEF3C7; font-weight: bold;">
                    <td colspan="5" class="text-right" style="padding: 6px 8px;">TOTAL KESELURUHAN:</td>
                    <td class="text-right font-mono">Rp {{ number_format($orders->sum('subtotal'), 0, ',', '.') }}</td>
                    <td class="text-right font-mono">-Rp {{ number_format($orders->sum('discount_amount'), 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($orders->sum('tax_amount'), 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #B45309; font-size: 10px;">
                        Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | Amarta Boga Smart POS System v1.0
    </div>
</body>
</html>
