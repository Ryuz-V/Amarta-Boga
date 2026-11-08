<x-layouts.app title="Promo & Diskon Restoran" subtitle="Kelola potongan harga, kode voucher diskon, batas pemakaian, dan minimal belanja">
    <div class="space-y-6">
        <!-- Top Action Bar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 text-lg">Daftar Promo & Voucher</h3>
                <p class="text-xs text-gray-400 mt-0.5">Program diskon aktif untuk meningkatkan penjualan resto</p>
            </div>

            <a href="{{ route('admin.discounts.create') }}"
                class="py-2.5 px-5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                + Tambah Promo Baru
            </a>
        </div>

        <!-- Discounts Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6 font-extrabold">Nama Promo</th>
                            <th class="py-4 px-6 font-extrabold">Kode Voucher</th>
                            <th class="py-4 px-6 font-extrabold">Nilai Potongan</th>
                            <th class="py-4 px-6 font-extrabold">Min. Belanja</th>
                            <th class="py-4 px-6 font-extrabold">Pemakaian</th>
                            <th class="py-4 px-6 font-extrabold">Status</th>
                            <th class="py-4 px-6 font-extrabold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($discounts as $d)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6">
                                    <p class="font-bold text-gray-900 text-sm">{{ $d->name }}</p>
                                    <p class="text-gray-400 text-[10px]">
                                        @if($d->start_date && $d->end_date)
                                            Berlaku: {{ $d->start_date->format('d/m/y') }} s/d {{ $d->end_date->format('d/m/y') }}
                                        @else
                                            Berlaku tanpa batas waktu
                                        @endif
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    @if ($d->code)
                                        <span class="px-2.5 py-1 rounded-xl bg-purple-50 text-purple-700 font-mono font-bold border border-purple-100">
                                            {{ $d->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Diskon Otomatis</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono font-black text-brand-500 text-sm">
                                    @if ($d->type === 'percentage')
                                        {{ number_format($d->value, 0) }}%
                                        @if ($d->max_discount)
                                            <span class="text-[10px] text-gray-400 font-sans font-normal block">Maks. Rp {{ number_format($d->max_discount, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        Rp {{ number_format($d->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono text-gray-600">
                                    Rp {{ number_format($d->min_spend, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 font-mono text-xs">
                                    <span class="font-bold text-gray-800">{{ $d->usage_count }}</span>
                                    <span class="text-gray-400">/ {{ $d->usage_limit ?? '∞' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <form method="POST" action="{{ route('admin.discounts.toggle', $d) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $d->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $d->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            <span>{{ $d->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('admin.discounts.edit', $d) }}"
                                        class="inline-flex px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-brand-50 text-gray-600 hover:text-brand-600 font-bold text-xs border border-gray-200 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.discounts.destroy', $d) }}" class="inline" onsubmit="return confirm('Hapus diskon {{ $d->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-rose-50 text-gray-600 hover:text-rose-600 font-bold text-xs border border-gray-200 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">Belum ada promo atau voucher dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($discounts->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $discounts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
