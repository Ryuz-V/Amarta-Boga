<x-layouts.app title="Laporan & Analitik Penjualan" subtitle="Ringkasan omzet, HPP, margin laba kotor, dan tren transaksi restoran">
    <div class="space-y-6">
        <!-- ======================================================== -->
        <!-- TOP CONTROLS: Filter Rentang Tanggal & Tombol Export     -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <!-- Preset Date Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.reports.sales', ['preset' => 'today']) }}"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all {{ $preset === 'today' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'yesterday']) }}"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all {{ $preset === 'yesterday' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Kemarin
                </a>
                <a href="{{ route('admin.reports.sales', ['preset' => '7days']) }}"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all {{ $preset === '7days' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    7 Hari Terakhir
                </a>
                <a href="{{ route('admin.reports.sales', ['preset' => '30days']) }}"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all {{ $preset === '30days' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    30 Hari Terakhir
                </a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'this_month']) }}"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all {{ $preset === 'this_month' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Bulan Ini
                </a>
            </div>

            <!-- Custom Date Range Form & Export Actions -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-end">
                <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex items-center space-x-2">
                    <input type="hidden" name="preset" value="custom">
                    <input type="date" name="start_date" value="{{ $startDate->toDateString() }}"
                        class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                    <span class="text-xs text-gray-400 font-bold">-</span>
                    <input type="date" name="end_date" value="{{ $endDate->toDateString() }}"
                        class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                    <button type="submit"
                        class="px-3.5 py-1.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-bold shadow-xs transition-all">
                        Terapkan
                    </button>
                </form>

                <div class="h-6 w-[1px] bg-gray-200 hidden sm:block"></div>

                <!-- Export Buttons -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.reports.sales.excel', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                        class="px-3.5 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center space-x-1.5 shadow-sm transition-all"
                        title="Ekspor data penjualan ke format Microsoft Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Unduh Excel</span>
                    </a>

                    <a href="{{ route('admin.reports.sales.pdf', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                        class="px-3.5 py-2 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold flex items-center space-x-1.5 shadow-sm transition-all"
                        title="Unduh laporan ringkasan dalam format PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Unduh PDF</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KPI METRICS: 8 Indikator Keuangan & Operasional           -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- 1. Penjualan Bersih (Net Sales) -->
            <div class="bg-gradient-to-br from-[#111215] to-[#1E2028] text-white rounded-3xl p-5 border border-gray-800 shadow-xl relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-orange-400">Penjualan Bersih (Net)</span>
                    <span class="text-xs bg-orange-500/20 text-[#FFA149] px-2 py-0.5 rounded-lg font-bold">Total Omzet</span>
                </div>
                <p class="text-2xl font-black font-mono text-white tracking-tight">
                    Rp {{ number_format($netSales, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1 font-medium">
                    Periode: {{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}
                </p>
            </div>

            <!-- 2. Laba Kotor (Gross Profit) & Margin -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Laba Kotor Resto</span>
                    <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-lg font-bold">
                        {{ number_format($profitMargin, 1) }}% Margin
                    </span>
                </div>
                <p class="text-2xl font-black font-mono text-emerald-600 tracking-tight">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Omzet bersih dikurangi HPP (COGS)
                </p>
            </div>

            <!-- 3. Total HPP (Cost of Goods Sold) -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Bahan Baku (HPP)</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg font-bold">Resep & COGS</span>
                </div>
                <p class="text-2xl font-black font-mono text-gray-900 tracking-tight">
                    Rp {{ number_format($totalCogs, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Estimasi modal bahan baku menu
                </p>
            </div>

            <!-- 4. Rata-Rata Nilai Transaksi (AOV) -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Rata-rata Order (AOV)</span>
                    <span class="text-xs bg-sky-50 text-sky-600 px-2 py-0.5 rounded-lg font-bold">{{ $totalOrders }} Order</span>
                </div>
                <p class="text-2xl font-black font-mono text-sky-600 tracking-tight">
                    Rp {{ number_format($aov, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Rata-rata pembelanjaan per tamu
                </p>
            </div>

            <!-- 5. Penjualan Kotor (Gross Sales) -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Penjualan Kotor</span>
                    <span class="text-xs text-gray-400">Subtotal Item</span>
                </div>
                <p class="text-xl font-black font-mono text-gray-900">
                    Rp {{ number_format($grossSales, 0, ',', '.') }}
                </p>
            </div>

            <!-- 6. Total Diskon Diberikan -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Total Potongan Diskon</span>
                    <span class="text-xs text-amber-500 font-bold">Promo / Voucher</span>
                </div>
                <p class="text-xl font-black font-mono text-amber-600">
                    Rp {{ number_format($totalDiscount, 0, ',', '.') }}
                </p>
            </div>

            <!-- 7. Pajak Restoran (PB1 11%) -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Pajak Restoran (PB1)</span>
                    <span class="text-xs text-purple-600 font-bold">11% PB1</span>
                </div>
                <p class="text-xl font-black font-mono text-purple-600">
                    Rp {{ number_format($totalTax, 0, ',', '.') }}
                </p>
            </div>

            <!-- 8. Service Charge -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Service Charge</span>
                    <span class="text-xs text-indigo-600 font-bold">Layanan</span>
                </div>
                <p class="text-xl font-black font-mono text-indigo-600">
                    Rp {{ number_format($totalService, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- CHARTS: Tren Penjualan & Komposisi Metode Pembayaran     -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Daily Sales Trend (Col-span 8) -->
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base">Tren Penjualan Harian</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Grafik pergerakan omzet bersih harian</p>
                    </div>
                    <span class="px-3 py-1 rounded-xl bg-orange-50 text-[#FF6B00] text-xs font-bold">
                        Rp {{ number_format($netSales, 0, ',', '.') }}
                    </span>
                </div>
                <div class="h-72 w-full relative">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

            <!-- Payment Methods Breakdown (Col-span 4) -->
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col justify-between">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base mb-1">Metode Pembayaran</h3>
                    <p class="text-xs text-gray-400 mb-4">Distribusi nominal transaksi per channel</p>
                    <div class="h-56 w-full relative flex items-center justify-center">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs">
                    @foreach ($paymentLabels as $idx => $label)
                        <div class="p-2 rounded-xl bg-gray-50 flex flex-col">
                            <span class="text-[10px] text-gray-400 font-bold">{{ $label }}</span>
                            <span class="font-bold text-gray-900 font-mono">Rp {{ number_format($paymentValues[$idx] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- ROW: Top 8 Menu Terlaris & Penjualan per Kategori         -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Top Menus Bar Chart (Col-span 6) -->
            <div class="lg:col-span-6 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base">Top 8 Menu Terlaris</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Berdasarkan kuantitas porsi yang dipesan</p>
                    </div>
                    <span class="text-xs font-bold text-gray-500">Porsi Terjual</span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="topMenusChart"></canvas>
                </div>
            </div>

            <!-- Category Sales Breakdown Table (Col-span 6) -->
            <div class="lg:col-span-6 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base">Penjualan per Kategori</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Kontribusi pendapatan per klasifikasi menu</p>
                    </div>
                    <span class="text-xs text-gray-400 font-bold">{{ $categoryBreakdown->count() }} Kategori</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                                <th class="pb-3 font-extrabold">Kategori</th>
                                <th class="pb-3 font-extrabold text-center">Porsi Terjual</th>
                                <th class="pb-3 font-extrabold text-right">Total Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 font-medium">
                            @forelse ($categoryBreakdown as $cat)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3 font-bold text-gray-900">{{ $cat->category_name }}</td>
                                    <td class="py-3 text-center font-mono font-bold text-gray-600">{{ number_format($cat->total_qty) }}</td>
                                    <td class="py-3 text-right font-mono font-bold text-[#FF6B00]">
                                        Rp {{ number_format($cat->total_revenue, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400">Belum ada transaksi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- RECENT TRANSACTIONS TABLE: Audit Pesanan & Status         -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">Daftar Transaksi Periode Terpilih</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Menampilkan pesanan yang diproses pada rentang tanggal aktif</p>
                </div>
                <span class="text-xs font-bold text-gray-500">Total: {{ $recentOrders->total() }} Pesanan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 font-extrabold">Invoice</th>
                            <th class="pb-3 font-extrabold">Waktu</th>
                            <th class="pb-3 font-extrabold">Pelanggan</th>
                            <th class="pb-3 font-extrabold">Meja / Tipe</th>
                            <th class="pb-3 font-extrabold">Kasir</th>
                            <th class="pb-3 font-extrabold">Metode Bayar</th>
                            <th class="pb-3 font-extrabold text-right">Total Transaksi</th>
                            <th class="pb-3 font-extrabold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-3.5 font-bold font-mono text-gray-900">{{ $order->invoice_number }}</td>
                                <td class="py-3.5 text-gray-500 font-mono">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 font-bold text-gray-800">{{ $order->customer_name }}</td>
                                <td class="py-3.5 text-gray-600">
                                    @if ($order->order_type === 'dine_in')
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">
                                            Meja {{ $order->table ? $order->table->number : '-' }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-bold text-[10px]">
                                            {{ strtoupper($order->order_type) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-gray-600">{{ $order->user?->name ?? 'Staff' }}</td>
                                <td class="py-3.5 font-bold uppercase text-[11px] text-gray-700">
                                    {{ $order->payments->pluck('payment_method')->unique()->implode(', ') ?: 'Cash' }}
                                </td>
                                <td class="py-3.5 text-right font-mono font-bold text-[#FF6B00]">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($order->status === 'processing' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">
                                    Tidak ada data transaksi yang ditemukan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $recentOrders->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Sales Trend Line Chart
            const trendCtx = document.getElementById('salesTrendChart')?.getContext('2d');
            if (trendCtx) {
                const trendLabels = @json($trendLabels);
                const trendValues = @json($trendValues);

                const gradient = trendCtx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(255, 107, 0, 0.35)');
                gradient.addColorStop(1, 'rgba(255, 107, 0, 0.0)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendLabels.length > 0 ? trendLabels : ['Belum Ada'],
                        datasets: [{
                            label: 'Omzet Harian (Rp)',
                            data: trendValues.length > 0 ? trendValues : [0],
                            borderColor: '#FF6B00',
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#FF6B00',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw || 0;
                                        return ' Omzet: Rp ' + val.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.04)' },
                                ticks: {
                                    font: { family: 'Inter', size: 10 },
                                    callback: function(value) {
                                        return 'Rp ' + (value >= 1000000 ? (value / 1000000) + 'jt' : (value / 1000) + 'k');
                                    }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 10 } }
                            }
                        }
                    }
                });
            }

            // 2. Payment Methods Doughnut Chart
            const paymentCtx = document.getElementById('paymentChart')?.getContext('2d');
            if (paymentCtx) {
                const paymentLabels = @json($paymentLabels);
                const paymentValues = @json($paymentValues);

                new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: paymentLabels.length > 0 ? paymentLabels : ['Belum Ada'],
                        datasets: [{
                            data: paymentValues.length > 0 ? paymentValues : [1],
                            backgroundColor: [
                                '#10B981', // Emerald (Cash)
                                '#FF6B00', // Orange (QRIS)
                                '#3B82F6', // Blue (Card)
                                '#8B5CF6', // Purple (Transfer)
                                '#EC4899', // Pink (Ewallet)
                            ],
                            borderWidth: 2,
                            borderColor: '#FFFFFF',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { family: 'Inter', size: 11, weight: 'bold' } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw || 0;
                                        return ' ' + context.label + ': Rp ' + val.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 3. Top Menus Bar Chart
            const topMenusCtx = document.getElementById('topMenusChart')?.getContext('2d');
            if (topMenusCtx) {
                const menuLabels = @json($topMenuLabels);
                const menuValues = @json($topMenuValues);

                new Chart(topMenusCtx, {
                    type: 'bar',
                    data: {
                        labels: menuLabels.length > 0 ? menuLabels : ['Belum Ada'],
                        datasets: [{
                            label: 'Porsi Terjual',
                            data: menuValues.length > 0 ? menuValues : [0],
                            backgroundColor: '#FF6B00',
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.04)' },
                                ticks: { font: { family: 'Inter', size: 10 } }
                            },
                            y: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 11, weight: 'bold' } }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
