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

    <!-- Modul Utama Administrator (Fitur Role Admin) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Pusat Kendali Fitur Administrator</h2>
            <span class="text-[11px] text-gray-400 font-medium">Akses cepat menu pengelolaan data restoran</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4">
            <!-- 1. Laporan Penjualan -->
            <a href="{{ route('admin.reports.sales') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-orange-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-orange-50 group-hover:bg-[#FF6B00] text-[#FF6B00] group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-[#FF6B00] leading-tight">Laporan Keuangan</span>
            </a>

            <!-- 2. Rekap Shift -->
            <a href="{{ route('admin.reports.shifts') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-sky-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-sky-50 group-hover:bg-sky-500 text-sky-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-sky-600 leading-tight">Rekap Shift Kasir</span>
            </a>

            <!-- 3. Menu & Resep -->
            <a href="{{ route('admin.menus.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-purple-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-purple-50 group-hover:bg-purple-500 text-purple-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-purple-600 leading-tight">Menu & Resep</span>
            </a>

            <!-- 4. Bahan & Stok -->
            <a href="{{ route('admin.inventory.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-emerald-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 group-hover:bg-emerald-500 text-emerald-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-emerald-600 leading-tight">Bahan & Stok</span>
            </a>

            <!-- 5. Denah Meja -->
            <a href="{{ route('admin.tables.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-amber-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-amber-50 group-hover:bg-amber-500 text-amber-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-amber-600 leading-tight">Denah Meja</span>
            </a>

            <!-- 6. Diskon & Promo -->
            <a href="{{ route('admin.discounts.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-rose-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-rose-50 group-hover:bg-rose-500 text-rose-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-rose-600 leading-tight">Diskon Promo</span>
            </a>

            <!-- 7. Kelola Pengguna -->
            <a href="{{ route('admin.users.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-indigo-300 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 group-hover:bg-indigo-500 text-indigo-600 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-indigo-600 leading-tight">Karyawan</span>
            </a>

            <!-- 8. Pengaturan Sistem -->
            <a href="{{ route('admin.settings.index') }}"
                class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-gray-400 hover:shadow-md transition-all flex flex-col items-center text-center group">
                <div class="w-11 h-11 rounded-xl bg-gray-100 group-hover:bg-gray-800 text-gray-700 group-hover:text-white flex items-center justify-center transition-colors mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-800 group-hover:text-gray-900 leading-tight">Pengaturan</span>
            </a>
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
