<x-layouts.app title="Dashboard Analitik Restoran" subtitle="Ringkasan performa penjualan harian, inventaris, dan aktivitas operasional">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Sales Card -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Omzet Hari Ini</span>
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-500 flex items-center justify-center font-bold">
                    💰
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 font-mono">
                Rp {{ number_format($todaySales, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-emerald-600 font-bold mt-1">↑ Dari transaksi selesai hari ini</p>
        </div>

        <!-- Orders Count Card -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Transaksi</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center font-bold">
                    🧾
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 font-mono">
                {{ $todayOrdersCount }} <span class="text-xs text-gray-400 font-sans font-normal">Order</span>
            </p>
            <p class="text-[11px] text-gray-400 font-medium mt-1">Hari ini (Dine-in / Takeaway)</p>
        </div>

        <!-- Active Orders Card -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Pesanan Aktif Dapur</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center font-bold">
                    👨‍🍳
                </div>
            </div>
            <p class="text-2xl font-black text-amber-500 font-mono">
                {{ $activeOrdersCount }} <span class="text-xs text-gray-400 font-sans font-normal">On Progress</span>
            </p>
            <p class="text-[11px] text-amber-600 font-bold mt-1">Sedang diproses di KDS</p>
        </div>

        <!-- Menu & Users Card -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Katalog Menu Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center font-bold">
                    🍽️
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 font-mono">
                {{ $totalMenus }} <span class="text-xs text-gray-400 font-sans font-normal">Menu</span>
            </p>
            <p class="text-[11px] text-gray-400 font-medium mt-1">Tersedia di 9 Kategori</p>
        </div>
    </div>

    <!-- 2-Column Row: Low Stock Alerts & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Orders Table (Col-span 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-black text-gray-900 text-lg">Transaksi Terbaru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Riwayat 6 pesanan paling akhir masuk ke sistem</p>
                </div>
                <a href="{{ route('pos.index') }}" class="text-xs font-bold text-brand-500 hover:text-brand-600">
                    Buka Kasir →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 font-extrabold">Invoice</th>
                            <th class="pb-3 font-extrabold">Pelanggan / Meja</th>
                            <th class="pb-3 font-extrabold">Total</th>
                            <th class="pb-3 font-extrabold">Status</th>
                            <th class="pb-3 font-extrabold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($latestOrders as $order)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-3.5 font-bold font-mono text-gray-900">{{ $order->invoice_number }}</td>
                                <td class="py-3.5 text-gray-700">
                                    <span class="font-bold">{{ $order->customer_name }}</span>
                                    <span class="text-gray-400 ml-1">({{ $order->table ? $order->table->name : 'Takeaway' }})</span>
                                </td>
                                <td class="py-3.5 font-bold font-mono text-brand-500">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($order->status === 'processing' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-gray-400 font-mono">{{ $order->created_at->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada transaksi recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alerts Widget (Col-span 4) -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-gray-900 text-base flex items-center space-x-2">
                    <span>⚠️ Peringatan Stok Bahan</span>
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-rose-50 text-rose-600">
                    Auto-Alert
                </span>
            </div>
            <p class="text-xs text-gray-400 mb-4">Bahan baku yang mendekati atau di bawah batas minimum pemakaian:</p>

            <div class="space-y-3">
                @forelse ($lowStockIngredients as $ing)
                    <div class="p-3 rounded-2xl bg-rose-50/40 border border-rose-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ $ing->name }}</p>
                            <p class="text-[10px] text-gray-500">Min: {{ number_format($ing->minimum_stock, 0) }} {{ $ing->unit }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold font-mono text-rose-600">
                                {{ number_format($ing->current_stock, 0) }} {{ $ing->unit }}
                            </span>
                            <p class="text-[9px] text-rose-500 font-semibold">Stok Kritis</p>
                        </div>
                    </div>
                @empty
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center text-xs text-emerald-700">
                        ✓ Seluruh inventaris bahan baku saat ini dalam batas aman.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
