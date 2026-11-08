<x-layouts.app title="Edit Menu" subtitle="Perbarui hidangan, varian, add-on, dan foto menu">
    <div class="max-w-4xl mx-auto" x-data="{
        variants: {{ $menu->variants->map(fn($v) => ['group' => $v->group_name, 'name' => $v->name, 'price' => (float)$v->price_adjustment])->toJson() }},
        addons: {{ $menu->addons->map(fn($a) => ['name' => $a->name, 'price' => (float)$a->price])->toJson() }},
        addVariant() {
            this.variants.push({ group: 'Ukuran', name: '', price: 0 });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        },
        addAddon() {
            this.addons.push({ name: '', price: 0 });
        },
        removeAddon(index) {
            this.addons.splice(index, 1);
        }
    }">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Perbarui Menu: {{ $menu->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Slug: {{ $menu->slug }}</p>
                </div>
                <a href="{{ route('admin.menus.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                    ← Kembali ke Katalog
                </a>
            </div>

            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Menu</label>
                        <input type="text" name="name" value="{{ old('name', $menu->name) }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                        @error('name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori</label>
                        <select name="category_id" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi Hidangan</label>
                    <textarea name="description" rows="2"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">{{ old('description', $menu->description) }}</textarea>
                </div>

                <!-- Pricing Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Harga Jual (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $menu->price) }}" required min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-mono font-bold text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">HPP / Biaya Modal (Rp)</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $menu->cost_price) }}" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-mono text-gray-900">
                    </div>
                </div>

                <!-- Image Preview & Upload -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Foto Saat Ini & Unggah Baru</label>
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0">
                                <img src="{{ $menu->image }}" class="w-full h-full object-cover">
                            </div>
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-brand-50 file:text-brand-600">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Atau Ganti URL Foto Menu</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $menu->image) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    </div>
                </div>

                <!-- Dynamic Variants Builder -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-gray-900">Varian Menu</h4>
                            <p class="text-[11px] text-gray-400">Pilihan ukuran, tingkat kepedasan, atau penyajian</p>
                        </div>
                        <button type="button" @click="addVariant()"
                            class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-brand-50 text-gray-700 hover:text-brand-600 font-bold text-xs transition-colors">
                            + Tambah Varian
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(v, index) in variants" :key="index">
                            <div class="flex items-center space-x-2 p-2 rounded-2xl bg-gray-50 border border-gray-100">
                                <input type="text" :name="'variants[' + index + '][group]'" x-model="v.group" placeholder="Grup (Ukuran/Pedas)"
                                    class="w-32 px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs">
                                <input type="text" :name="'variants[' + index + '][name]'" x-model="v.name" placeholder="Nama Opsi"
                                    class="flex-1 px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs">
                                <input type="number" :name="'variants[' + index + '][price]'" x-model="v.price" placeholder="+Harga (Rp)"
                                    class="w-28 px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-mono">
                                <button type="button" @click="removeVariant(index)" class="text-rose-500 hover:text-rose-700 px-2 font-bold">✕</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Dynamic Addons Builder -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-gray-900">Tambahan / Add-ons</h4>
                            <p class="text-[11px] text-gray-400">Topping ekstra atau pelengkap pesanan</p>
                        </div>
                        <button type="button" @click="addAddon()"
                            class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-brand-50 text-gray-700 hover:text-brand-600 font-bold text-xs transition-colors">
                            + Tambah Add-on
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(a, index) in addons" :key="index">
                            <div class="flex items-center space-x-2 p-2 rounded-2xl bg-gray-50 border border-gray-100">
                                <input type="text" :name="'addons[' + index + '][name]'" x-model="a.name" placeholder="Nama Topping"
                                    class="flex-1 px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs">
                                <input type="number" :name="'addons[' + index + '][price]'" x-model="a.price" placeholder="Harga Tambahan (Rp)"
                                    class="w-36 px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-mono">
                                <button type="button" @click="removeAddon(index)" class="text-rose-500 hover:text-rose-700 px-2 font-bold">✕</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Flags -->
                <div class="pt-4 border-t border-gray-100 flex items-center space-x-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-500">
                        <span class="text-xs font-bold text-gray-700">Aktif di Sistem</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" {{ $menu->is_available ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-500">
                        <span class="text-xs font-bold text-gray-700">Tersedia (Ready to Order)</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_favorite" value="1" {{ $menu->is_favorite ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-500">
                        <span class="text-xs font-bold text-amber-600">★ Menu Favorit</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.menus.index') }}" class="px-5 py-3 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">
                        Perbarui Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
