<x-layouts.app title="Resep Bahan Baku Menu" subtitle="Atur komposisi bahan yang otomatis terpotong dari inventaris saat menu dibayar">
    <div class="max-w-4xl mx-auto" x-data="{
        recipes: {{ $menu->recipes->map(fn($r) => ['ingredient_id' => $r->ingredient_id, 'quantity' => (float)$r->quantity_required])->toJson() }},
        addIngredient() {
            this.recipes.push({ ingredient_id: '', quantity: 10 });
        },
        removeIngredient(index) {
            this.recipes.splice(index, 1);
        }
    }">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0">
                        <img src="{{ $menu->image }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 leading-tight">{{ $menu->name }}</h3>
                        <p class="text-xs text-brand-500 font-bold font-mono">Rp {{ number_format($menu->price, 0, ',', '.') }} • {{ $menu->category->name }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.menus.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                    ← Kembali ke Menu
                </a>
            </div>

            <!-- Description Box -->
            <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-100 text-xs text-brand-800 mb-6 flex items-start space-x-3">
                <span class="text-base">💡</span>
                <div>
                    <p class="font-bold">Otomatisasi Pengurangan Stok Dapur (Automated BOM)</p>
                    <p class="text-brand-700 mt-0.5">
                        Setiap kali kasir memproses pesanan menu ini, stok bahan baku di bawah ini akan terpotong secara otomatis dari sistem inventaris.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.menus.recipes.update', $menu) }}" class="space-y-4">
                @csrf

                <div class="flex items-center justify-between pb-2">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-gray-700">Daftar Komposisi Bahan</h4>
                    <button type="button" @click="addIngredient()"
                        class="px-3.5 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-600 font-bold text-xs transition-colors">
                        + Tambah Bahan Baku
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(r, index) in recipes" :key="index">
                        <div class="flex items-center space-x-3 p-3 rounded-2xl bg-gray-50 border border-gray-100">
                            <!-- Ingredient Select -->
                            <div class="flex-1">
                                <select :name="'recipes[' + index + '][ingredient_id]'" x-model="r.ingredient_id" required
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800">
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach ($ingredients as $ing)
                                        <option value="{{ $ing->id }}">
                                            {{ $ing->name }} (Satuan: {{ $ing->unit }} | Stok: {{ number_format($ing->current_stock, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity Input -->
                            <div class="w-44 flex items-center space-x-1.5">
                                <input type="number" step="0.01" min="0.01" :name="'recipes[' + index + '][quantity]'" x-model="r.quantity" required
                                    placeholder="Jumlah"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-mono font-bold text-gray-800 text-right">
                                <span class="text-xs font-semibold text-gray-500">Kebutuhan</span>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" @click="removeIngredient(index)"
                                class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center font-bold text-xs transition-colors">
                                ✕
                            </button>
                        </div>
                    </template>

                    <div x-show="recipes.length === 0" class="p-8 text-center border border-dashed border-gray-200 rounded-2xl text-xs text-gray-400">
                        Belum ada komposisi bahan baku untuk menu ini. Klik tombol di atas untuk menambahkan.
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.menus.index') }}" class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">
                        Simpan Resep Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
