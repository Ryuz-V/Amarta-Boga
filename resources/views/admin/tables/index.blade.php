<x-layouts.app title="Denah & Meja Restoran" subtitle="Kelola nomor meja, kapasitas duduk, lokasi area, dan pantau status keterisian secara real-time">
    <div x-data="{
        addModal: false,
        editModal: false,
        editId: '',
        editNumber: '',
        editName: '',
        editCapacity: 4,
        editLocation: 'Lantai 1 Indoor',
        editStatus: 'available',
        openEdit(t) {
            this.editId = t.id;
            this.editNumber = t.number;
            this.editName = t.name;
            this.editCapacity = t.capacity;
            this.editLocation = t.location;
            this.editStatus = t.status;
            this.editModal = true;
        }
    }" class="space-y-6">

        <!-- Top Header & Action -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div>
                    <h3 class="font-black text-gray-900 text-lg">Denah Meja Restoran</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Total {{ $tables->count() }} meja di seluruh area restoran</p>
                </div>

                <!-- Status Legend -->
                <div class="hidden lg:flex items-center space-x-4 text-xs font-semibold">
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-gray-600">Kosong</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span><span class="text-gray-600">Terisi (Makan)</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span><span class="text-gray-600">Dipesan</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span><span class="text-gray-600">Dibersihkan</span></span>
                </div>
            </div>

            <button type="button" @click="addModal = true"
                class="py-2.5 px-5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                + Tambah Meja Baru
            </button>
        </div>

        <!-- Tables Grouped by Location -->
        @foreach ($tables->groupBy('location') as $location => $groupTables)
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h4 class="font-black text-gray-900 text-base flex items-center space-x-2">
                        <span>📍 {{ $location }}</span>
                        <span class="text-xs font-bold font-mono px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600">
                            {{ $groupTables->count() }} Meja
                        </span>
                    </h4>
                </div>

                <!-- Table Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($groupTables as $t)
                        <div class="rounded-2xl border p-4 transition-all duration-200 group flex flex-col justify-between {{ $t->status === 'occupied' ? 'bg-brand-50/40 border-brand-200' : ($t->status === 'available' ? 'bg-white border-gray-100 hover:border-emerald-300 shadow-soft' : ($t->status === 'reserved' ? 'bg-indigo-50/40 border-indigo-200' : 'bg-gray-50 border-gray-200')) }}">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono font-black text-lg {{ $t->status === 'occupied' ? 'text-brand-600' : ($t->status === 'available' ? 'text-emerald-600' : 'text-gray-700') }}">
                                        {{ $t->number }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase {{ $t->status === 'occupied' ? 'bg-brand-500 text-white' : ($t->status === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700') }}">
                                        {{ $t->status }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-gray-800">{{ $t->name }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">👥 {{ $t->capacity }} Kursi</p>

                                @if ($t->activeOrder)
                                    <div class="mt-2 p-1.5 rounded-lg bg-white/80 border border-brand-100 text-[10px] font-mono text-brand-700">
                                        {{ $t->activeOrder->customer_name }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between">
                                <button type="button" @click="openEdit({{ $t->toJson() }})"
                                    class="text-[11px] font-bold text-brand-500 hover:text-brand-700">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.tables.destroy', $t) }}" onsubmit="return confirm('Hapus meja {{ $t->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-rose-400 hover:text-rose-600">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Add Table Modal -->
        <div x-show="addModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="addModal = false" class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-gray-100">
                <h3 class="font-black text-gray-900 text-lg mb-1">Tambah Meja Baru</h3>
                <p class="text-xs text-gray-400 mb-6">Tentukan nomor meja, kapasitas, dan lokasinya</p>

                <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor / Kode</label>
                            <input type="text" name="number" required placeholder="01 / VIP-3"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kapasitas Kursi</label>
                            <input type="number" name="capacity" value="4" min="1" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Meja</label>
                        <input type="text" name="name" required placeholder="Meja 01"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lokasi / Area</label>
                        <input type="text" name="location" required placeholder="Lantai 1 Indoor, Outdoor, VIP"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Awal</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                            <option value="available">Kosong (Available)</option>
                            <option value="occupied">Terisi (Occupied)</option>
                            <option value="reserved">Dipesan (Reserved)</option>
                            <option value="cleaning">Dibersihkan (Cleaning)</option>
                        </select>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="addModal = false" class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">Simpan Meja</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Table Modal -->
        <div x-show="editModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editModal = false" class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-gray-100">
                <h3 class="font-black text-gray-900 text-lg mb-1">Edit Meja</h3>
                <p class="text-xs text-gray-400 mb-6">Perbarui konfigurasi meja restoran</p>

                <form :action="'{{ url('admin/tables') }}/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor / Kode</label>
                            <input type="text" name="number" x-model="editNumber" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kapasitas Kursi</label>
                            <input type="number" name="capacity" x-model="editCapacity" min="1" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Meja</label>
                        <input type="text" name="name" x-model="editName" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lokasi / Area</label>
                        <input type="text" name="location" x-model="editLocation" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Meja</label>
                        <select name="status" x-model="editStatus" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                            <option value="available">Kosong (Available)</option>
                            <option value="occupied">Terisi (Occupied)</option>
                            <option value="reserved">Dipesan (Reserved)</option>
                            <option value="cleaning">Dibersihkan (Cleaning)</option>
                        </select>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
