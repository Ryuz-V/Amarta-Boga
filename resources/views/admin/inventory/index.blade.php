<x-layouts.app title="Inventaris Bahan Baku & Stok" subtitle="Pantau stok bahan, catat pembelian masuk, limbah, dan penyesuaian stok opname">
    <div x-data="{
        adjustModal: false,
        addIngModal: false,
        adjustIngId: '',
        adjustIngName: '',
        adjustIngUnit: '',
        adjustType: 'in',
        openAdjust(ing, type = 'in') {
            this.adjustIngId = ing.id;
            this.adjustIngName = ing.name;
            this.adjustIngUnit = ing.unit;
            this.adjustType = type;
            this.adjustModal = true;
        }
    }" class="space-y-6">

        <!-- Top Stat & Actions Bar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari bahan atau SKU..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">Semua Stok</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>⚠️ Stok Kritis (Menipis)</option>
                </select>

                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.inventory.index') }}" class="text-xs text-brand-500 font-bold hover:underline">Reset</a>
                @endif
            </form>

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <a href="{{ route('admin.inventory.movements') }}"
                    class="py-2.5 px-4 rounded-2xl border border-gray-200 hover:border-gray-300 bg-gray-50 text-gray-700 font-bold text-xs transition-colors">
                    📜 Riwayat Mutasi
                </a>
                <button type="button" @click="addIngModal = true"
                    class="py-2.5 px-5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                    + Tambah Bahan Baku
                </button>
            </div>
        </div>

        <!-- Ingredients Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-lg">Daftar Bahan Baku & Stok Fisik</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Total {{ $ingredients->total() }} bahan terdata di dapur</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6 font-extrabold">Bahan Baku</th>
                            <th class="py-4 px-6 font-extrabold">SKU</th>
                            <th class="py-4 px-6 font-extrabold">Stok Saat Ini</th>
                            <th class="py-4 px-6 font-extrabold">Batas Minimal</th>
                            <th class="py-4 px-6 font-extrabold">Estimasi Biaya / Satuan</th>
                            <th class="py-4 px-6 font-extrabold text-right">Mutasi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($ingredients as $ing)
                            @php
                                $isLow = $ing->isLowStock();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $isLow ? 'bg-rose-50/30' : '' }}">
                                <td class="py-4 px-6 font-bold text-gray-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <span>{{ $ing->name }}</span>
                                        @if ($isLow)
                                            <span class="px-2 py-0.5 rounded-lg bg-rose-500 text-white text-[9px] font-black uppercase animate-pulse">
                                                Stok Tipis
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-mono text-gray-400">{{ $ing->sku ?? '-' }}</td>
                                <td class="py-4 px-6 font-mono text-sm font-black {{ $isLow ? 'text-rose-600' : 'text-gray-900' }}">
                                    {{ number_format($ing->current_stock, 0, ',', '.') }} <span class="text-xs font-sans text-gray-400 font-normal">{{ $ing->unit }}</span>
                                </td>
                                <td class="py-4 px-6 font-mono text-gray-500">
                                    {{ number_format($ing->minimum_stock, 0, ',', '.') }} {{ $ing->unit }}
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-brand-500">
                                    Rp {{ number_format($ing->cost_per_unit, 0, ',', '.') }} / {{ $ing->unit }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-1.5">
                                    <!-- Stock In (Beli) -->
                                    <button type="button" @click="openAdjust({{ $ing->toJson() }}, 'in')"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] transition-colors"
                                        title="Stok Masuk / Pembelian">
                                        + Masuk
                                    </button>

                                    <!-- Waste / Rusak -->
                                    <button type="button" @click="openAdjust({{ $ing->toJson() }}, 'waste')"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition-colors"
                                        title="Limbah / Rusak">
                                        - Limbah
                                    </button>

                                    <!-- Opname -->
                                    <button type="button" @click="openAdjust({{ $ing->toJson() }}, 'adjustment')"
                                        class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[11px] transition-colors"
                                        title="Penyesuaian Stok Opname">
                                        ⚖️ Opname
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">Tidak ada bahan baku tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ingredients->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $ingredients->links() }}
                </div>
            @endif
        </div>

        <!-- Adjust Stock Modal (Alpine.js) -->
        <div x-show="adjustModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="adjustModal = false" class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-black text-gray-900 text-lg">Catat Mutasi Stok</h3>
                        <p class="text-xs text-brand-500 font-bold font-mono" x-text="adjustIngName"></p>
                    </div>
                    <button type="button" @click="adjustModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="ingredient_id" :value="adjustIngId">

                    <!-- Type Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Mutasi</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="p-2.5 rounded-xl border border-gray-200 text-center cursor-pointer text-xs font-bold"
                                :class="adjustType === 'in' ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'bg-gray-50 text-gray-600'">
                                <input type="radio" name="type" value="in" x-model="adjustType" class="hidden">
                                📥 Masuk (Beli)
                            </label>
                            <label class="p-2.5 rounded-xl border border-gray-200 text-center cursor-pointer text-xs font-bold"
                                :class="adjustType === 'waste' ? 'bg-rose-50 border-rose-500 text-rose-700' : 'bg-gray-50 text-gray-600'">
                                <input type="radio" name="type" value="waste" x-model="adjustType" class="hidden">
                                🗑️ Limbah (Rusak)
                            </label>
                            <label class="p-2.5 rounded-xl border border-gray-200 text-center cursor-pointer text-xs font-bold"
                                :class="adjustType === 'adjustment' ? 'bg-amber-50 border-amber-500 text-amber-700' : 'bg-gray-50 text-gray-600'">
                                <input type="radio" name="type" value="adjustment" x-model="adjustType" class="hidden">
                                ⚖️ Opname Fisik
                            </label>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            <span x-text="adjustType === 'adjustment' ? 'Jumlah Riil Stok Fisik' : 'Jumlah Perubahan'"></span>
                            (<span x-text="adjustIngUnit"></span>)
                        </label>
                        <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Supplier (only for stock in) -->
                    <div x-show="adjustType === 'in'">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Supplier Pemasok</label>
                        <select name="supplier_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700">
                            <option value="">Pilih Supplier (Opsional)</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cost per Unit -->
                    <div x-show="adjustType === 'in'">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Harga Beli Baru per Satuan (Rp)</label>
                        <input type="number" name="cost_per_unit" placeholder="Opsional"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Keterangan</label>
                        <input type="text" name="notes" placeholder="Contoh: Belanja mingguan pasar / Sayur busuk kena hujan"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="adjustModal = false"
                            class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">
                            Simpan Mutasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Ingredient Modal -->
        <div x-show="addIngModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="addIngModal = false" class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-gray-900 text-lg">Tambah Bahan Baku Baru</h3>
                    <button type="button" @click="addIngModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form action="{{ route('admin.inventory.ingredients.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Bahan Baku</label>
                        <input type="text" name="name" required placeholder="Contoh: Daging Sapi Sirloin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">SKU / Kode</label>
                            <input type="text" name="sku" placeholder="ING-SIRLOIN-01"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Satuan Unit</label>
                            <input type="text" name="unit" required placeholder="gram, ml, pcs"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Stok Awal</label>
                            <input type="number" step="0.01" name="current_stock" value="0" min="0" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Batas Minimum Alert</label>
                            <input type="number" step="0.01" name="minimum_stock" value="10" min="0" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Biaya Modal per Satuan (Rp)</label>
                        <input type="number" name="cost_per_unit" value="0" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="addIngModal = false"
                            class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">
                            Simpan Bahan Baku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
