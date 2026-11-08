<x-layouts.app title="Riwayat Transaksi Kasir" subtitle="Daftar seluruh pesanan, cetak ulang struk, dan status pembayaran">
    <div class="space-y-6">
        <!-- Top Filters & Search -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <a href="{{ route('pos.index') }}"
                    class="px-4 py-2.5 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold border border-gray-200 flex items-center space-x-2 transition-all">
                    <span>← Kembali ke Kasir</span>
                </a>

                <h3 class="text-base font-extrabold text-gray-900">
                    Semua Transaksi
                </h3>
            </div>

            <form method="GET" action="{{ route('pos.orders') }}" class="flex items-center space-x-3 w-full sm:w-auto">
                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()"
                    class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#FF6B00] text-gray-700">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses (Processing)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>

                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="No invoice atau nama..."
                        class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#FF6B00] text-gray-900 placeholder-gray-400">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/75 text-[11px] font-extrabold uppercase tracking-wider text-gray-400">
                            <th class="py-4 px-6">Invoice</th>
                            <th class="py-4 px-6">Waktu</th>
                            <th class="py-4 px-6">Pelanggan & Meja</th>
                            <th class="py-4 px-6">Item Pesanan</th>
                            <th class="py-4 px-6">Metode</th>
                            <th class="py-4 px-6 text-right">Total Transaksi</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Aksi Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">
                                    {{ $order->invoice_number }}
                                </td>
                                <td class="py-4 px-6 text-gray-500 font-mono text-[11px]">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-gray-900">{{ $order->customer_name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">
                                        {{ $order->table ? $order->table->name : 'Takeaway' }} • Kasir: {{ $order->user?->name }}
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-bold text-[11px]">
                                        {{ $order->items->count() }} Menu ({{ $order->items->sum('quantity') }} porsi)
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700">
                                        {{ $order->payments->pluck('payment_method')->implode(', ') ?: 'Tunai' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-gray-900 text-sm">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($order->status === 'processing' ? 'bg-orange-50 text-orange-600' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('pos.receipt', $order) }}" target="_blank"
                                            class="p-2 rounded-xl bg-orange-50 hover:bg-[#FF6B00] text-[#FF6B00] hover:text-white transition-colors"
                                            title="Cetak Ulang Struk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>

                                        <a href="{{ route('pos.receipt.pdf', $order) }}"
                                            class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors"
                                            title="Unduh PDF Struk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400 text-xs">
                                    Belum ada transaksi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
