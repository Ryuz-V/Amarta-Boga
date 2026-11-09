@props(['title' => 'Dashboard', 'subtitle' => ''])

<header class="h-20 bg-transparent px-6 sm:px-8 flex items-center justify-between flex-shrink-0 z-30">
    <!-- Left: Page Title / Breadcrumb -->
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-none">{{ $title }}</h2>
        @if ($subtitle)
            <p class="text-xs text-gray-400 font-medium mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    <!-- Right: Status Bar, Shift, Clock & WebSocket Indicator -->
    <div class="flex items-center space-x-3 sm:space-x-5" x-data="{
        currentTime: '',
        currentDate: '',
        wsConnected: true,
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
        }
    }" x-init="updateTime(); setInterval(() => updateTime(), 1000)">

        <!-- Digital Clock (WIB) -->
        <div class="hidden md:flex items-center space-x-2 px-3.5 py-1.5 rounded-2xl bg-white border border-gray-200/80 shadow-xs text-gray-700">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-xs font-mono font-bold">
                <span x-text="currentDate" class="text-gray-400 mr-1.5 font-sans font-medium"></span>
                <span x-text="currentTime" class="text-gray-800"></span>
            </div>
        </div>

        <!-- Shift Status Indicator (for Staff/Kasir) -->
        @if (auth()->user()?->hasRole('staff'))
            <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 rounded-2xl bg-amber-50 border border-amber-100 text-amber-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Shift Aktif</span>
            </div>
        @endif
    </div>
</header>
