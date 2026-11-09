@php
    $user = auth()->user();
    $role = $user?->getRoleNames()->first() ?? 'staff';
@endphp

<!-- Sleek Slim Navigation Rail matching Gambar 2 (~72px width) -->
<aside class="w-[72px] sm:w-20 bg-[#111215] flex-shrink-0 flex flex-col justify-between items-center py-5 transition-all duration-300 z-50 select-none">
    <!-- Top Brand Mark: Orange Hexagon / Honeycomb (Gambar 2) -->
    <div class="flex flex-col items-center space-y-4 w-full">
        <a href="{{ route('dashboard') }}" class="group relative flex items-center justify-center" title="Amarta Boga POS">
            <!-- Orange Hexagonal Brand Icon matching Gambar 2 -->
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#FF6B00] to-[#FFA149] flex items-center justify-center text-white shadow-lg shadow-orange-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="7" r="2.2"/>
                    <circle cx="7" cy="10" r="2.2"/>
                    <circle cx="17" cy="10" r="2.2"/>
                    <circle cx="7" cy="15" r="2.2"/>
                    <circle cx="17" cy="15" r="2.2"/>
                    <circle cx="12" cy="18" r="2.2"/>
                </svg>
            </div>
            
            <!-- Hover Tooltip -->
            <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                Amarta Boga POS
            </span>
        </a>

        <!-- Subtle Rail Divider -->
        <div class="w-8 h-[1px] bg-[#22252E]"></div>

        <!-- ========================================== -->
        <!-- 1. KHUSUS ROLE KOKI (DAPUR / KDS)          -->
        <!-- ========================================== -->
        @if ($user->hasRole('koki'))
            <nav class="flex flex-col items-center space-y-3 w-full px-2">
                <!-- Dashboard Koki / KDS Screen -->
                <a href="{{ route('kds.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('kds.*') || request()->routeIs('koki.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Dashboard Koki (KDS)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Dashboard Dapur (KDS)
                    </span>
                </a>
            </nav>

        <!-- ========================================== -->
        <!-- 2. KHUSUS ROLE STAFF (KASIR)               -->
        <!-- ========================================== -->
        @elseif ($user->hasRole('staff'))
            <nav class="flex flex-col items-center space-y-3 w-full px-2">
                <!-- POS / Katalog Menu & Kasir (Gambar 2) -->
                <a href="{{ route('pos.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('pos.index') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Katalog & Kasir POS">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Kasir (POS)
                    </span>
                </a>

                <!-- Denah Meja Kasir -->
                <a href="{{ route('admin.tables.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.tables.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Denah Meja">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Denah Meja
                    </span>
                </a>

                <!-- Riwayat Transaksi Kasir -->
                <a href="{{ route('pos.orders') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('pos.orders') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Riwayat Transaksi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Riwayat Transaksi
                    </span>
                </a>
            </nav>

        <!-- ========================================== -->
        <!-- 3. KHUSUS ROLE ADMIN (DASHBOARD & KENDALI) -->
        <!-- ========================================== -->
        @elseif ($user->hasRole('admin'))
            <nav class="flex flex-col items-center space-y-2.5 w-full px-2 overflow-y-auto max-h-[calc(100vh-220px)] scrollbar-none">
                <!-- 1. Dashboard Utama Admin -->
                <a href="{{ route('admin.dashboard') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Dashboard Admin">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Dashboard Admin
                    </span>
                </a>

                <!-- 2. Laporan Penjualan & Keuangan -->
                <a href="{{ route('admin.reports.sales') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.reports.sales*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Laporan Penjualan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Laporan Keuangan
                    </span>
                </a>

                <!-- 3. Rekap Shift Kasir -->
                <a href="{{ route('admin.reports.shifts') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.reports.shifts*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Rekap Shift Kasir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Rekap Shift
                    </span>
                </a>

                <!-- 4. Menu & Resep -->
                <a href="{{ route('admin.menus.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.menus.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Menu & Resep">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Menu & Resep
                    </span>
                </a>

                <!-- 5. Bahan Baku & Stok -->
                <a href="{{ route('admin.inventory.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.inventory.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Bahan Baku & Stok">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Bahan & Stok
                    </span>
                </a>

                <!-- 6. Denah Meja -->
                <a href="{{ route('admin.tables.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.tables.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Denah Meja">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Denah Meja
                    </span>
                </a>

                <!-- 7. Diskon & Promo -->
                <a href="{{ route('admin.discounts.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.discounts.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Diskon Promo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Diskon & Promo
                    </span>
                </a>

                <!-- 8. Kelola Pengguna / Karyawan -->
                <a href="{{ route('admin.users.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                    title="Kelola Pengguna">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Karyawan
                    </span>
                </a>

                <div class="w-6 h-[1px] bg-[#22252E] my-1"></div>

                <!-- Admin Quick Switch to POS & KDS -->
                <a href="{{ route('pos.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center text-gray-500 hover:text-orange-400 hover:bg-[#1E2028] transition-all duration-200"
                    title="Buka Kasir (POS)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Buka Kasir (POS)
                    </span>
                </a>

                <a href="{{ route('kds.index') }}"
                    class="group relative w-12 h-12 rounded-2xl flex items-center justify-center text-gray-500 hover:text-emerald-400 hover:bg-[#1E2028] transition-all duration-200"
                    title="Buka Dapur (KDS)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                        Buka Dapur (KDS)
                    </span>
                </a>
            </nav>
        @endif
    </div>

    <!-- Bottom Actions: Settings & Logout (Gambar 2) -->
    <div class="flex flex-col items-center space-y-3 w-full px-2 pt-4 border-t border-[#22252E]">
        @if ($user->hasRole('admin'))
            <a href="{{ route('admin.settings.index') }}"
                class="group relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-[#FF6B00] text-white shadow-lg shadow-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-[#1E2028]' }}"
                title="Pengaturan Resto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-gray-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                    Pengaturan Toko
                </span>
            </a>
        @endif

        <!-- Logout Action -->
        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="w-full flex justify-center">
            @csrf
            <button type="submit"
                class="group relative w-12 h-12 rounded-2xl flex items-center justify-center text-gray-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-200"
                title="Keluar dari Sistem">
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-rose-900 text-white text-[11px] font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity shadow-lg z-50">
                    Keluar ({{ $user->name }})
                </span>
            </button>
        </form>
    </div>
</aside>
