<x-layouts.app title="Tambah Pengguna Baru" subtitle="Buat akun untuk kasir, koki dapur, atau staf admin restoran">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft">
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Form Pengguna Baru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Isi seluruh informasi kredensial dengan lengkap</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                    ← Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Rian Hidayat"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    @error('name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Email & Phone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Login</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@amartaboga.com"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                        @error('email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor Telepon / WA</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                        @error('phone') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Peran (Role Akses)</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($roles as $r)
                            <label class="p-4 rounded-2xl border border-gray-200 hover:border-brand-500 bg-gray-50 hover:bg-brand-50/20 cursor-pointer flex items-center space-x-3 transition-all">
                                <input type="radio" name="role" value="{{ $r->name }}" {{ old('role', 'staff') === $r->name ? 'checked' : '' }}
                                    class="text-brand-500 focus:ring-brand-500">
                                <div>
                                    <p class="text-xs font-bold uppercase text-gray-800">{{ $r->name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $r->name === 'admin' ? 'Akses Penuh' : ($r->name === 'koki' ? 'Layar Dapur' : 'Kasir & POS') }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kata Sandi Awal</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900">
                    @error('password') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center space-x-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-brand-500 border-gray-300 focus:ring-brand-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-bold text-gray-700 cursor-pointer">Akun Langsung Aktif</label>
                </div>

                <!-- Action Button -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
