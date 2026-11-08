<x-layouts.app title="Manajemen Pengguna" subtitle="Kelola hak akses kasir, koki dapur, dan administrator restoran">
    <div class="space-y-6">
        <!-- Top Toolbar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, no HP..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Role Filter -->
                <select name="role" onchange="this.form.submit()"
                    class="py-2.5 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">Semua Peran</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                            Peran: {{ strtoupper($r->name) }}
                        </option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-500 font-bold hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('admin.users.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 py-3 px-5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                <span>+ Tambah Pengguna</span>
            </a>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6 font-extrabold">Pengguna</th>
                            <th class="py-4 px-6 font-extrabold">Kontak</th>
                            <th class="py-4 px-6 font-extrabold">Peran (Role)</th>
                            <th class="py-4 px-6 font-extrabold">Status</th>
                            <th class="py-4 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brand-500 to-amber-400 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm leading-tight">{{ $user->name }}</p>
                                            <p class="text-gray-400 text-[11px] font-mono mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-gray-600 font-mono">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    @foreach ($user->roles as $role)
                                        <span class="inline-block px-3 py-1 rounded-xl text-[11px] font-bold uppercase tracking-wider {{ $role->name === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : ($role->name === 'koki' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-brand-50 text-brand-700 border border-brand-200/60') }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-4 px-6">
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            <span>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="inline-flex px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-brand-50 text-gray-600 hover:text-brand-600 font-bold text-xs border border-gray-200 transition-colors">
                                        Edit
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?')">
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
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    Tidak ada data pengguna ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
