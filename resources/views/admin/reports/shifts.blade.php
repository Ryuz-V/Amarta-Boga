<x-layouts.app title="Audit & Rekap Shift Kasir" subtitle="Laporan pergantian shift kasir, rekonsiliasi laci kasir, dan selisih modal operasional">
    <div class="space-y-6">
        <!-- ======================================================== -->
        <!-- SUMMARY CARDS: Metrik Rekonsiliasi Kas Shift             -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- 1. Total Modal Awal -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Total Modal Awal</span>
                    <span class="text-xs bg-gray-100 text-gray-700 font-bold px-2 py-0.5 rounded-lg">Kas Keluar</span>
                </div>
                <p class="text-2xl font-black font-mono text-gray-900 tracking-tight">
                    Rp {{ number_format($totalStartingCash, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Akumulasi saldo kas awal semua shift
                </p>
            </div>

            <!-- 2. Total Kas Aktual Disetor -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Kas Fisik Disetor</span>
                    <span class="text-xs bg-emerald-50 text-emerald-600 font-bold px-2 py-0.5 rounded-lg">Shift Closed</span>
                </div>
                <p class="text-2xl font-black font-mono text-emerald-600 tracking-tight">
                    Rp {{ number_format($totalActualCash, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Total uang tunai fisik yang dihitung kasir
                </p>
            </div>

            <!-- 3. Total Selisih Kas (Variance) -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Total Selisih (Variance)</span>
                    <span class="text-xs {{ $totalVariance == 0 ? 'bg-emerald-50 text-emerald-600' : ($totalVariance > 0 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} font-bold px-2 py-0.5 rounded-lg">
                        {{ $totalVariance == 0 ? 'Klop / Seimbang' : ($totalVariance > 0 ? 'Surplus' : 'Defisit') }}
                    </span>
                </div>
                <p class="text-2xl font-black font-mono {{ $totalVariance == 0 ? 'text-gray-900' : ($totalVariance > 0 ? 'text-amber-500' : 'text-rose-600') }} tracking-tight">
                    {{ $totalVariance >= 0 ? '+' : '' }}Rp {{ number_format($totalVariance, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    Selisih kas aktual terhadap kas yang diharapkan
                </p>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- FILTER TOOLBAR                                           -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-soft">
            <form action="{{ route('admin.reports.shifts') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filter Kasir -->
                    <div>
                        <select name="user_id" class="px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                            <option value="">Semua Kasir (Staff)</option>
                            @foreach ($cashiers as $c)
                                <option value="{{ $c->id }}" {{ request('user_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <select name="status" class="px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Sedang Aktif (Open)</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Telah Ditutup (Closed)</option>
                        </select>
                    </div>

                    <!-- Submit & Reset -->
                    <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-2xl text-xs font-bold shadow-xs transition-all">
                        Filter
                    </button>

                    @if (request()->hasAny(['user_id', 'status']))
                        <a href="{{ route('admin.reports.shifts') }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="text-xs font-bold text-gray-400">
                    Menampilkan {{ $shifts->total() }} Sesi Shift
                </div>
            </form>
        </div>

        <!-- ======================================================== -->
        <!-- TABLE: Rekap Sesi Shift Kasir                           -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 font-extrabold">ID / Kasir</th>
                            <th class="pb-3 font-extrabold">Waktu Mulai & Tutup</th>
                            <th class="pb-3 font-extrabold text-right">Modal Awal</th>
                            <th class="pb-3 font-extrabold text-right">Kas Diharapkan</th>
                            <th class="pb-3 font-extrabold text-right">Kas Aktual</th>
                            <th class="pb-3 font-extrabold text-center">Selisih (Variance)</th>
                            <th class="pb-3 font-extrabold text-center">Status</th>
                            <th class="pb-3 font-extrabold">Catatan Penutupan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 font-medium">
                        @forelse ($shifts as $shift)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Kasir -->
                                <td class="py-3.5">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-[#FF6B00] font-black flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($shift->user?->name ?? 'K', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $shift->user?->name ?? 'Kasir #' . $shift->user_id }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono">Shift #{{ $shift->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Waktu -->
                                <td class="py-3.5 text-gray-600 font-mono text-[11px]">
                                    <div>
                                        <span class="text-gray-400">Buka:</span> {{ $shift->start_time ? $shift->start_time->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Tutup:</span> {{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : 'Masih Berjalan' }}
                                    </div>
                                </td>

                                <!-- Modal Awal -->
                                <td class="py-3.5 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                                </td>

                                <!-- Kas Diharapkan -->
                                <td class="py-3.5 text-right font-mono font-bold text-gray-600">
                                    @if ($shift->expected_cash !== null)
                                        Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 text-[10px]">-</span>
                                    @endif
                                </td>

                                <!-- Kas Aktual -->
                                <td class="py-3.5 text-right font-mono font-bold text-gray-900">
                                    @if ($shift->actual_cash !== null)
                                        Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 text-[10px] font-sans">Belum dihitung</span>
                                    @endif
                                </td>

                                <!-- Selisih Kas -->
                                <td class="py-3.5 text-center">
                                    @if ($shift->status === 'closed')
                                        @if ($shift->difference == 0)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600">
                                                ✓ Seimbang (Rp 0)
                                            </span>
                                        @elseif ($shift->difference > 0)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600">
                                                +Rp {{ number_format($shift->difference, 0, ',', '.') }} (Surplus)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600">
                                                -Rp {{ number_format(abs($shift->difference), 0, ',', '.') }} (Kurang)
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                            Shift Aktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $shift->status === 'open' ? 'bg-blue-50 text-blue-600 animate-pulse' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $shift->status === 'open' ? 'Aktif' : 'Selesai' }}
                                    </span>
                                </td>

                                <!-- Catatan -->
                                <td class="py-3.5 text-gray-500 max-w-xs truncate" title="{{ $shift->notes }}">
                                    {{ $shift->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">
                                    Belum ada catatan pergantian shift kasir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $shifts->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
