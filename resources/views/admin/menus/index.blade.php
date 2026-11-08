<x-layouts.app title="Manajemen Menu Restoran" subtitle="Kelola katalog makanan, minuman, varian, dan toggle status ketersediaan (Sold Out)">
    <div class="space-y-6">
        <!-- Top Toolbar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.menus.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Category Filter -->
                <select name="category_id" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Habis (Sold Out)</option>
                </select>

                @if(request()->anyFilled(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.menus.index') }}" class="text-xs text-brand-500 font-bold hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('admin.menus.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 py-3 px-5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                <span>+ Tambah Menu Baru</span>
            </a>
        </div>

        <!-- Menus Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($menus as $menu)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-soft hover:shadow-soft-lg transition-all duration-300 p-4 flex flex-col justify-between group">
                    <div>
                        <!-- Image Container -->
                        <div class="h-44 rounded-2xl bg-gray-100 overflow-hidden relative mb-3">
                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Category Badge -->
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-black/60 text-white backdrop-blur-sm">
                                {{ $menu->category->name }}
                            </span>

                            <!-- Favorite badge -->
                            @if ($menu->is_favorite)
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded-xl text-[10px] font-black uppercase bg-amber-500 text-white shadow-sm">
                                    ★ Favorit
                                </span>
                            @endif

                            <!-- Sold out overlay -->
                            @if (!$menu->is_available)
                                <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-xs flex items-center justify-center">
                                    <span class="px-3 py-1.5 rounded-xl bg-rose-600 text-white text-xs font-black uppercase tracking-wider shadow-lg">
                                        Habis (Sold Out)
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Menu Info -->
                        <h4 class="font-bold text-gray-900 text-sm line-clamp-1 group-hover:text-brand-600 transition-colors">
                            {{ $menu->name }}
                        </h4>
                        <p class="text-xs text-gray-400 line-clamp-2 mt-1 leading-relaxed">
                            {{ $menu->description }}
                        </p>
                    </div>

                    <!-- Pricing & Quick Toggle -->
                    <div class="mt-4 pt-3 border-t border-gray-100 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Harga Jual</p>
                                <p class="font-black text-brand-500 font-mono text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">HPP (Modal)</p>
                                <p class="font-bold text-gray-700 font-mono">Rp {{ number_format($menu->cost_price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between gap-2 pt-1">
                            <!-- Toggle Sold Out Button -->
                            <form method="POST" action="{{ route('admin.menus.toggle', $menu) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full py-2 px-2.5 rounded-xl text-[11px] font-bold transition-all {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}">
                                    {{ $menu->is_available ? '✓ Tersedia' : '✕ Sold Out' }}
                                </button>
                            </form>

                            <!-- Recipes Button -->
                            <a href="{{ route('admin.menus.recipes', $menu) }}"
                                class="p-2 rounded-xl bg-gray-50 hover:bg-amber-50 text-gray-600 hover:text-amber-600 border border-gray-200 text-xs font-bold transition-colors"
                                title="Atur Resep Bahan Baku">
                                🍳
                            </a>

                            <!-- Edit Button -->
                            <a href="{{ route('admin.menus.edit', $menu) }}"
                                class="p-2 rounded-xl bg-gray-50 hover:bg-brand-50 text-gray-600 hover:text-brand-600 border border-gray-200 text-xs font-bold transition-colors"
                                title="Edit Menu">
                                ✏️
                            </a>

                            <!-- Delete Button -->
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 rounded-xl bg-gray-50 hover:bg-rose-50 text-gray-600 hover:text-rose-600 border border-gray-200 text-xs font-bold transition-colors"
                                    title="Hapus Menu">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm">Tidak ada menu yang sesuai kriteria pencarian.</p>
                </div>
            @endforelse
        </div>

        @if ($menus->hasPages())
            <div class="p-4 bg-white rounded-3xl border border-gray-100 shadow-soft">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
