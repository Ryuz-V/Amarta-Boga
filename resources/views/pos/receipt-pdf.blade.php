<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk-{{ $order->invoice_number }}</title>
    <style>
        @page {
            margin: 4px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            padding: 4px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .dashed { border-bottom: 1px dashed #000; margin: 6px 0; }
        .dotted { border-bottom: 1px dotted #888; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 1px 0; }
        .small { font-size: 9px; color: #555; }
    </style>
</head>
<body>
    <!-- Header Restoran -->
    <div class="text-center">
        <div class="font-bold" style="font-size: 13px;">{{ strtoupper($settings['restaurant_name'] ?? 'AMARTA BOGA RESTO') }}</div>
        <div class="small">{{ $settings['restaurant_tagline'] ?? 'Cita Rasa Nusantara & Fusion Jepang' }}</div>
        <div class="small">{{ $settings['restaurant_address'] ?? 'Jl. Jenderal Sudirman Kav. 88, Jakarta Selatan' }}</div>
        <div class="small">Telp: {{ $settings['restaurant_phone'] ?? '0812-3456-7890' }}</div>
    </div>

    <div class="dashed"></div>

    <!-- Metadata Transaksi -->
    <table>
        <tr>
            <td>No: {{ $order->invoice_number }}</td>
            <td class="text-right">{{ $order->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $order->user?->name ?? 'Kasir Utama' }}</td>
            <td class="text-right font-bold">{{ strtoupper($order->order_type === 'dine_in' ? 'DINE IN' : 'TAKEAWAY') }}</td>
        </tr>
        <tr>
            <td>Tamu : {{ $order->customer_name }}</td>
            <td class="text-right font-bold">{{ $order->table ? $order->table->name : '-' }}</td>
        </tr>
    </table>

    <div class="dashed"></div>

    <!-- Daftar Item -->
    <table>
        @foreach ($order->items as $item)
            <tr>
                <td colspan="2" class="font-bold">{{ $item->menu_name }}</td>
            </tr>
            <tr>
                <td class="small" style="padding-left: 6px;">
                    {{ $item->quantity }} x Rp {{ number_format($item->unit_price + $item->modifiers_price, 0, ',', '.') }}
                    @if ($item->modifiers->count() > 0)
                        <br>+ {{ $item->modifiers->pluck('name')->implode(', ') }}
                    @endif
                    @if ($item->notes)
                        <br>* {{ $item->notes }}
                    @endif
                </td>
                <td class="text-right font-bold">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            <tr><td colspan="2" style="height: 3px;"></td></tr>
        @endforeach
    </table>

    <div class="dashed"></div>

    <!-- Rincian Total -->
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($order->discount_amount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if ($order->tax_amount > 0)
            <tr>
                <td>PB1 ({{ (float)$order->tax_rate }}%)</td>
                <td class="text-right">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if ($order->service_charge > 0)
            <tr>
                <td>Servis ({{ (float)$order->service_charge_rate }}%)</td>
                <td class="text-right">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td class="font-bold" style="font-size: 12px; padding-top: 4px;">TOTAL</td>
            <td class="text-right font-bold" style="font-size: 12px; padding-top: 4px;">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Bayar ({{ $order->payments->pluck('payment_method')->implode('+') ?: 'Tunai' }})</td>
            <td class="text-right">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</td>
        </tr>
        @if ($order->change_amount > 0)
            <tr>
                <td class="font-bold">Kembalian</td>
                <td class="text-right font-bold">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="dashed"></div>

    <!-- Footer -->
    <div class="text-center small">
        <div class="font-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</div>
        <div>Kritik & Saran WA: {{ $settings['restaurant_phone'] ?? '0812-3456-7890' }}</div>
        <div>WiFi: AmartaBoga-Guest | Pass: nikmat</div>
        <div style="font-size: 8px; margin-top: 6px;">--- Amarta Boga POS ---</div>
    </div>
</body>
</html>
