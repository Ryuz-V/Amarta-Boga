<x-layouts.app title="Tambah Promo / Voucher" subtitle="Buat program diskon kasir dengan persentase atau potongan nominal tetap">
    <div class="max-w-3xl mx-auto" x-data="{
        type: 'percentage'
    }">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Form Diskon & Voucher Baru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tentukan nilai potongan dan syarat minimal transaksi</p>
                </div>
                <a href="{{ route('admin.discounts.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.discounts.store') }}" class="space-y-6">
                @csrf

                <!-- Name & Code -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Program Diskon</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Diskon Jam Makan Siang"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        @error('name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kode Voucher (Opsional)</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="HEMAT10 / PROMOJUMAT"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono uppercase text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>
                </div>

                <!-- Type Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Potongan</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="p-4 rounded-2xl border cursor-pointer flex items-center space-x-3 transition-all"
                            :class="type === 'percentage' ? 'bg-brand-50/50 border-brand-500' : 'bg-gray-50 border-gray-200'">
                            <input type="radio" name="type" value="percentage" x-model="type" class="text-brand-500">
                            <div>
                                <p class="text-xs font-bold text-gray-900">Persentase (%)</p>
                                <p class="text-[11px] text-gray-400">Potongan persen dari subtotal</p>
                            </div>
                        </label>
                        <label class="p-4 rounded-2xl border cursor-pointer flex items-center space-x-3 transition-all"
                            :class="type === 'fixed' ? 'bg-brand-50/50 border-brand-500' : 'bg-gray-50 border-gray-200'">
                            <input type="radio" name="type" value="fixed" x-model="type" class="text-brand-500">
                            <div>
                                <p class="text-xs font-bold text-gray-900">Nominal Tetap (Rp)</p>
                                <p class="text-[11px] text-gray-400">Potongan langsung nilai rupiah</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Value & Max Discount Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nilai Potongan (<span x-text="type === 'percentage' ? '%' : 'Rp'"></span>)
                        </label>
                        <input type="number" step="0.01" name="value" value="{{ old('value', 10) }}" required min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900">
                    </div>
                    <div x-show="type === 'percentage'">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Maksimal Diskon (Rp, Opsional)</label>
                        <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0" placeholder="Contoh: 25000"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                    </div>
                </div>

                <!-- Min Spend & Usage Limit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Minimal Belanja (Rp)</label>
                        <input type="number" name="min_spend" value="{{ old('min_spend', 0) }}" min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Batas Total Pemakaian (Opsional)</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Kosongkan jika tak terbatas"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                    </div>
                </div>

                <!-- Dates Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Mulai (Opsional)</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Berakhir (Opsional)</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700">
                    </div>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center space-x-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded text-brand-500">
                    <label for="is_active" class="text-xs font-bold text-gray-700 cursor-pointer">Promo Langsung Aktif</label>
                </div>

                <!-- Action Button -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.discounts.index') }}" class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">
                        Simpan Diskon
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
