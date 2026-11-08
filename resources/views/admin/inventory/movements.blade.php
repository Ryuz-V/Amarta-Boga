<x-layouts.app title="Riwayat Mutasi Stok Bahan" subtitle="Audit trail lengkap seluruh pergerakan stok bahan baku dapur restoran">
    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.inventory.movements') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select name="ingredient_id" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700">
                    <option value="">Semua Bahan Baku</option>
                    @foreach ($ingredients as $ing)
                        <option value="{{ $ing->id }}" {{ request('ingredient_id') == $ing->id ? 'selected' : '' }}>
                            {{ $ing->name }}
                        </option>
                    @endforeach
                </select>

                <select name="type" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700">
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk (Pembelian)</option>
                    <option value="waste" {{ request('type') === 'waste' ? 'selected' : '' }}>Limbah / Rusak</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Stok Opname</option>
                    <option value="order_deduction" {{ request('type') === 'order_deduction' ? 'selected' : '' }}>Pengurangan Order POS</option>
                </select>

                @if(request()->anyFilled(['ingredient_id', 'type']))
                    <a href="{{ route('admin.inventory.movements') }}" class="text-xs text-brand-500 font-bold hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('admin.inventory.index') }}"
                class="text-xs font-bold text-gray-500 hover:text-gray-800">
                ← Kembali ke Inventaris
            </a>
        </div>

        <!-- Movements Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6 font-extrabold">Waktu</th>
                            <th class="py-4 px-6 font-extrabold">Bahan Baku</th>
                            <th class="py-4 px-6 font-extrabold">Tipe Mutasi</th>
                            <th class="py-4 px-6 font-extrabold">Stok Awal</th>
                            <th class="py-4 px-6 font-extrabold">Perubahan</th>
                            <th class="py-4 px-6 font-extrabold">Stok Akhir</th>
                            <th class="py-4 px-6 font-extrabold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movements as $m)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 font-mono text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $m->ingredient->name }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $m->type === 'in' ? 'bg-emerald-50 text-emerald-700' : ($m->type === 'waste' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ $m->type }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-mono text-gray-500">{{ number_format($m->before_stock, 0) }} {{ $m->ingredient->unit }}</td>
                                <td class="py-4 px-6 font-mono font-bold {{ $m->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $m->type === 'in' ? '+' : '-' }}{{ number_format($m->quantity, 0) }} {{ $m->ingredient->unit }}
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ number_format($m->after_stock, 0) }} {{ $m->ingredient->unit }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $m->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">Belum ada riwayat mutasi stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
