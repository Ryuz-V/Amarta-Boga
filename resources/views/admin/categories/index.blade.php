<x-layouts.app title="Kategori Menu" subtitle="Kelola kategori makanan, minuman, dan menu pencuci mulut">
    <div x-data="{
        editModal: false,
        editId: null,
        editName: '',
        editIcon: '',
        editSort: 0,
        editActive: true,
        openEdit(cat) {
            this.editId = cat.id;
            this.editName = cat.name;
            this.editIcon = cat.icon;
            this.editSort = cat.sort_order;
            this.editActive = cat.is_active;
            this.editModal = true;
        }
    }" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Tambah Kategori (Col-span 4) -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-100 shadow-soft sticky top-24">
            <h3 class="font-black text-gray-900 text-base mb-1">Tambah Kategori Baru</h3>
            <p class="text-xs text-gray-400 mb-6">Kelompokkan menu agar rapi di katalog kasir</p>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Ramen & Mie"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Ikon</label>
                        <input type="text" name="icon" value="utensils" placeholder="utensils, coffee"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="1" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active_new" value="1" checked
                        class="w-4 h-4 rounded text-brand-500 border-gray-300 focus:ring-brand-500 cursor-pointer">
                    <label for="is_active_new" class="text-xs font-bold text-gray-700 cursor-pointer">Kategori Langsung Aktif</label>
                </div>

                <button type="submit"
                    class="w-full mt-2 py-3 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                    Simpan Kategori
                </button>
            </form>
        </div>

        <!-- Daftar Kategori Table (Col-span 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-lg">Daftar Kategori Resto</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Total {{ $categories->count() }} kategori terdaftar di sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6 font-extrabold">Urutan</th>
                            <th class="py-4 px-6 font-extrabold">Nama Kategori</th>
                            <th class="py-4 px-6 font-extrabold">Slug URL</th>
                            <th class="py-4 px-6 font-extrabold">Total Menu</th>
                            <th class="py-4 px-6 font-extrabold">Status</th>
                            <th class="py-4 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $cat)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 font-mono font-bold text-gray-400">#{{ $cat->sort_order }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900 text-sm">
                                    {{ $cat->name }}
                                </td>
                                <td class="py-4 px-6 font-mono text-gray-400">{{ $cat->slug }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-brand-50 text-brand-600">
                                        {{ $cat->menus_count }} Menu
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button type="button" @click="openEdit({{ $cat->toJson() }})"
                                        class="inline-flex px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-brand-50 text-gray-600 hover:text-brand-600 font-bold text-xs border border-gray-200 transition-colors">
                                        Edit
                                    </button>
                                    @if ($cat->menus_count == 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-rose-50 text-gray-600 hover:text-rose-600 font-bold text-xs border border-gray-200 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">Belum ada kategori menu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Category Modal (Alpine.js) -->
        <div x-show="editModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editModal = false" class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-gray-100">
                <h3 class="font-black text-gray-900 text-lg mb-1">Edit Kategori</h3>
                <p class="text-xs text-gray-400 mb-6">Perbarui nama dan urutan tampilan</p>

                <form :action="'{{ url('admin/categories') }}/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Kategori</label>
                        <input type="text" name="name" x-model="editName" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Ikon</label>
                            <input type="text" name="icon" x-model="editIcon"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Urutan Tampil</label>
                            <input type="number" name="sort_order" x-model="editSort" min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_active" value="1" :checked="editActive"
                            class="w-4 h-4 rounded text-brand-500 border-gray-300 focus:ring-brand-500 cursor-pointer">
                        <label for="edit_active" class="text-xs font-bold text-gray-700 cursor-pointer">Status Aktif</label>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="editModal = false"
                            class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
