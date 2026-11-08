<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->invoice_number }} - Amarta Boga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Courier Prime', monospace;
            background-color: #F3F4F6;
        }

        /* Paper Width Rules */
        .thermal-receipt {
            width: {{ $paperWidth === '58mm' ? '58mm' : '80mm' }};
            max-width: 100%;
            background: #ffffff;
            margin: 20px auto;
            padding: 15px 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            color: #000000;
            font-size: {{ $paperWidth === '58mm' ? '11px' : '12px' }};
            line-height: 1.35;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .thermal-receipt {
                width: 100% !important;
                margin: 0 !important;
                padding: 4px 6px !important;
                box-shadow: none !important;
            }
            @page {
                margin: 0;
                size: {{ $paperWidth === '58mm' ? '58mm' : '80mm' }} auto;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8">
    <!-- Top Action Bar (Screen only, hidden on print) -->
    <div class="no-print max-w-md mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('pos.index') }}"
            class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 flex items-center space-x-1.5 shadow-sm">
            <span>← Kembali ke POS</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('pos.receipt.pdf', $order) }}"
                class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 flex items-center space-x-1.5 shadow-sm">
                <span>📥 Unduh PDF</span>
            </a>

            <button type="button" onclick="window.print()"
                class="px-5 py-2 rounded-xl bg-[#FF6B00] hover:bg-orange-600 text-white text-xs font-bold flex items-center space-x-1.5 shadow-md shadow-orange-500/25">
                <span>🖨️ Cetak Struk (Print)</span>
            </button>
        </div>
    </div>

    <!-- Thermal Receipt Paper Structure -->
    <div class="thermal-receipt">
        <!-- 1. Header Restoran -->
        <div class="text-center pb-3 border-b border-dashed border-gray-400">
            <h1 class="font-bold text-sm sm:text-base uppercase tracking-wider">
                {{ strtoupper($settings['restaurant_name'] ?? 'AMARTA BOGA RESTO') }}
            </h1>
            <p class="text-[10px] text-gray-600 mt-0.5">
                {{ $settings['restaurant_tagline'] ?? 'Cita Rasa Nusantara & Fusion Jepang' }}
            </p>
            <p class="text-[10px] text-gray-700 mt-1">
                {{ $settings['restaurant_address'] ?? 'Jl. Jenderal Sudirman Kav. 88, Senayan, Jakarta Selatan' }}
            </p>
            <p class="text-[10px] text-gray-700">
                Telp: {{ $settings['restaurant_phone'] ?? '0812-3456-7890' }}
            </p>
        </div>

        <!-- 2. Metadata Transaksi -->
        <div class="py-2.5 text-[11px] space-y-1 border-b border-dashed border-gray-400">
            <div class="flex justify-between">
                <span>No: {{ $order->invoice_number }}</span>
                <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir: {{ $order->user?->name ?? 'Kasir Utama' }}</span>
                <span class="font-bold uppercase">
                    {{ $order->order_type === 'dine_in' ? 'DINE IN' : 'TAKEAWAY' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Tamu : {{ $order->customer_name }}</span>
                @if ($order->table)
                    <span class="font-bold">{{ $order->table->name }}</span>
                @endif
            </div>
        </div>

        <!-- 3. Daftar Item Pesanan -->
        <div class="py-2.5 space-y-2 border-b border-dashed border-gray-400">
            @foreach ($order->items as $item)
                <div>
                    <div class="flex justify-between font-bold">
                        <span class="flex-1 pr-2 truncate">{{ $item->menu_name }}</span>
                        <span class="whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-600 pl-2">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price + $item->modifiers_price, 0, ',', '.') }}</span>
                    </div>
                    @if ($item->modifiers->count() > 0)
                        <div class="pl-3 text-[9px] text-gray-500">
                            @foreach ($item->modifiers as $mod)
                                <span>+ {{ $mod->name }} (Rp {{ number_format($mod->price, 0, ',', '.') }})</span><br>
                            @endforeach
                        </div>
                    @endif
                    @if ($item->notes)
                        <div class="pl-3 text-[9px] text-gray-500 italic">
                            * {{ $item->notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- 4. Rincian Kalkulasi & Pembayaran -->
        <div class="py-2.5 space-y-1 text-[11px] border-b border-dashed border-gray-400">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>

            @if ($order->discount_amount > 0)
                <div class="flex justify-between">
                    <span>Diskon ({{ $order->discount?->code ?? 'Promo' }})</span>
                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span>PB1 ({{ (float)$order->tax_rate }}%)</span>
                    <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($order->service_charge > 0)
                <div class="flex justify-between">
                    <span>Servis ({{ (float)$order->service_charge_rate }}%)</span>
                    <span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex justify-between font-bold text-xs pt-1.5 border-t border-dotted border-gray-300">
                <span>TOTAL AKHIR</span>
                <span class="text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between pt-1">
                <span class="uppercase">Metode: {{ $order->payments->pluck('payment_method')->implode(' + ') ?: 'Tunai' }}</span>
                <span>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
            </div>

            @if ($order->change_amount > 0)
                <div class="flex justify-between font-bold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- 5. Footer Struk -->
        <div class="pt-3 text-center space-y-1 text-[10px] text-gray-600">
            <p class="font-bold uppercase tracking-wider">
                Terima Kasih Atas Kunjungan Anda!
            </p>
            <p>
                Kritik & Saran WA: {{ $settings['restaurant_phone'] ?? '0812-3456-7890' }}
            </p>
            <p class="text-[9px] text-gray-400 pt-1">
                WiFi: AmartaBoga-Guest | Pass: selamatmenikmati
            </p>
            <div class="pt-2 text-[8px] font-mono text-gray-400">
                --- Amarta Boga POS v1.0 Powered by Laravel ---
            </div>
        </div>
    </div>
</body>
</html>
