<x-layouts.app title="Dashboard Koki - Kitchen Display System (KDS)" :withHeader="false">
    <div x-data="{
        soundAlert: true,
        activeStation: 'all',
        showSoldOutModal: false,
        searchTerm: '',
        cancelModalOpen: false,
        activeItemForCancel: null,
        cancelReasonText: '',

        // Ambang batas waktu alert dari settings resto (dalam menit)
        warningMin: {{ $warningMinutes }},
        dangerMin: {{ $dangerMinutes }},

        // Live Orders State (diinisialisasi dari DB)
        orders: {{ json_encode($orders) }},

        // Menus State untuk Sold Out Drawer
        menus: {{ json_encode($menus) }},

        // Inisialisasi Audio & Timer
        init() {
            // Update timer setiap detik
            setInterval(() => {
                this.$dispatch('kds-tick');
            }, 1000);

            // Inisialisasi WebSocket Reverb Listener jika Echo tersedia
            if (window.Echo) {
                window.Echo.private('kitchen')
                    .listen('.OrderCreated', (e) => {
                        this.handleNewOrder(e.order);
                    })
                    .listen('.OrderStatusUpdated', (e) => {
                        this.handleStatusUpdated(e.order, e.new_status);
                    })
                    .listen('.OrderItemCancelled', (e) => {
                        this.handleItemCancelled(e.order_id, e.order_item_id, e.reason);
                    });
            }
        },

        // Web Audio API Synthesizer (Chime dua nada)
        playChime() {
            if (!this.soundAlert) return;
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                
                osc.type = 'sine';
                // Nada 1: 880Hz (A5), Nada 2: 1320Hz (E6)
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.15);
                
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);
                
                osc.start();
                osc.stop(ctx.currentTime + 0.45);
            } catch (err) {
                console.warn('AudioContext belum dapat diputar otomatis sebelum interaksi pengguna.');
            }
        },

        // Handle Reverb Event Datang
        handleNewOrder(newOrder) {
            // Cek apakah order sudah ada
            let exists = this.orders.some(o => o.id === newOrder.id);
            if (!exists) {
                this.orders.push(newOrder);
                this.playChime();
            }
        },

        handleStatusUpdated(updatedOrder, newStatus) {
            if (newStatus === 'completed' || newStatus === 'cancelled') {
                this.orders = this.orders.filter(o => o.id !== updatedOrder.id);
            } else {
                let idx = this.orders.findIndex(o => o.id === updatedOrder.id);
                if (idx !== -1) {
                    this.orders[idx].status = newStatus;
                }
            }
        },

        handleItemCancelled(orderId, itemId, reason) {
            let order = this.orders.find(o => o.id === orderId);
            if (order && order.items) {
                let item = order.items.find(i => i.id === itemId);
                if (item) {
                    item.status = 'cancelled';
                    item.cancel_reason = reason;
                }
            }
        },

        // Helper Hitung Waktu Berlalu (Menit & Detik)
        getElapsedMinutes(createdAt) {
            let start = new Date(createdAt).getTime();
            let now = Date.now();
            return Math.floor((now - start) / 60000);
        },

        getElapsedDisplay(createdAt) {
            let start = new Date(createdAt).getTime();
            let now = Date.now();
            let diffSec = Math.max(0, Math.floor((now - start) / 1000));
            let m = Math.floor(diffSec / 60);
            let s = diffSec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        },

        // Ubah Status Order (Mulai Masak -> Siap Saji -> Selesai)
        async advanceOrderStatus(order) {
            let nextStatus = '';
            if (order.status === 'pending') {
                nextStatus = 'processing';
            } else if (order.status === 'processing') {
                nextStatus = 'ready';
            } else if (order.status === 'ready') {
                nextStatus = 'completed';
            }

            if (!nextStatus) return;

            try {
                let response = await fetch('/kds/orders/' + order.id + '/status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ status: nextStatus })
                });

                let res = await response.json();
                if (res.success) {
                    if (nextStatus === 'completed') {
                        this.orders = this.orders.filter(o => o.id !== order.id);
                    } else {
                        order.status = nextStatus;
                    }
                }
            } catch (err) {
                alert('Gagal memperbarui status dapur: ' + err.message);
            }
        },

        // Centang Item (Item Strike-through checklist)
        async toggleItemCheck(item) {
            let newStatus = item.status === 'ready' ? 'cooking' : 'ready';
            try {
                let response = await fetch('/kds/items/' + item.id + '/status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                let res = await response.json();
                if (res.success) {
                    item.status = newStatus;
                }
            } catch (err) {
                item.status = newStatus; // fallback UI toggle
            }
        },

        // Batalkan Item Khusus
        openCancelItemModal(item) {
            this.activeItemForCancel = item;
            this.cancelReasonText = 'Bahan baku habis mendadak';
            this.cancelModalOpen = true;
        },

        async confirmCancelItem() {
            if (!this.activeItemForCancel || !this.cancelReasonText) return;

            try {
                let response = await fetch('/kds/items/' + this.activeItemForCancel.id + '/cancel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ reason: this.cancelReasonText })
                });

                let res = await response.json();
                if (res.success) {
                    this.activeItemForCancel.status = 'cancelled';
                    this.activeItemForCancel.cancel_reason = this.cancelReasonText;
                    this.cancelModalOpen = false;
                }
            } catch (err) {
                alert('Gagal membatalkan item: ' + err.message);
            }
        },

        // Toggle Sold Out Menu
        async toggleMenuAvailability(menu) {
            try {
                let response = await fetch('/kds/menus/' + menu.id + '/sold-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    }
                });

                let res = await response.json();
                if (res.success) {
                    menu.is_available = res.is_available;
                }
            } catch (err) {
                alert('Gagal mengubah ketersediaan menu: ' + err.message);
            }
        },

        // Filter Station
        shouldShowItemInStation(item) {
            if (this.activeStation === 'all') return true;
            let slug = item.menu?.category?.slug || '';
            if (this.activeStation === 'kitchen') {
                return ['main-course', 'favorit', 'food', 'soup', 'salad'].includes(slug);
            }
            if (this.activeStation === 'bar') {
                return ['drink', 'coffee'].includes(slug);
            }
            if (this.activeStation === 'dessert') {
                return ['dessert', 'ice-cream'].includes(slug);
            }
            return true;
        }
    }" class="h-full w-full flex flex-col overflow-hidden bg-white">

        <!-- ========================================== -->
        <!-- TOP HEADER KDS (matching Gambar 2)         -->
        <!-- ========================================== -->
        <header class="bg-white border-b border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 flex-shrink-0 z-10 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200/60 text-[#FF6B00] flex items-center justify-center font-black text-lg shadow-sm">
                    👨‍🍳
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-gray-900 tracking-tight leading-none">
                        Kitchen Display System (KDS)
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">
                        Antrean dapur real-time FIFO dengan timer otomatis & peringatan visual
                    </p>
                </div>
            </div>

            <!-- Controls: Sound Alert, Reverb status & Sold-out modal button -->
            <div class="flex items-center space-x-3">
                <!-- Reverb Live Indicator -->
                <div class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-700 text-xs font-bold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 -ml-3"></span>
                    <span>Reverb Live</span>
                </div>

                <!-- Sound Alert Toggle Button -->
                <button type="button" @click="soundAlert = !soundAlert; if (soundAlert) playChime();"
                    class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 shadow-xs"
                    :class="soundAlert ? 'bg-orange-50 border-orange-200 text-[#FF6B00]' : 'bg-gray-50 border-gray-200 text-gray-400'">
                    <span x-text="soundAlert ? '🔔 Audio: Aktif' : '🔕 Audio: Bisu'"></span>
                </button>

                <!-- Sold-Out Quick Drawer Button -->
                <button type="button" @click="showSoldOutModal = true"
                    class="px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold shadow-sm transition-colors flex items-center space-x-1.5">
                    <span>🛑 Kelola Sold Out</span>
                </button>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- MAIN WORKSPACE: Active Order Board         -->
        <!-- ========================================== -->
        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Filter Bar & Station Routing -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Station Filter Buttons -->
                <div class="flex items-center space-x-2.5 overflow-x-auto w-full sm:w-auto pb-1 text-xs font-bold">
                    <button type="button" @click="activeStation = 'all'"
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="activeStation === 'all' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/20' : 'bg-white text-gray-600 border border-gray-200/80 hover:bg-gray-50'">
                        Semua Stasiun (<span x-text="orders.length"></span>)
                    </button>
                    <button type="button" @click="activeStation = 'kitchen'"
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="activeStation === 'kitchen' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/20' : 'bg-white text-gray-600 border border-gray-200/80 hover:bg-gray-50'">
                        🍳 Dapur Utama
                    </button>
                    <button type="button" @click="activeStation = 'bar'"
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="activeStation === 'bar' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/20' : 'bg-white text-gray-600 border border-gray-200/80 hover:bg-gray-50'">
                        ☕ Bar Minuman
                    </button>
                    <button type="button" @click="activeStation = 'dessert'"
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="activeStation === 'dessert' ? 'bg-[#FF6B00] text-white shadow-md shadow-orange-500/20' : 'bg-white text-gray-600 border border-gray-200/80 hover:bg-gray-50'">
                        🍨 Dessert & Es
                    </button>
                </div>

                <!-- Info Badges -->
                <div class="flex items-center space-x-3 text-xs">
                    <span class="flex items-center space-x-1.5 text-emerald-600 font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>&lt; <span x-text="warningMin"></span>m Normal</span>
                    </span>
                    <span class="flex items-center space-x-1.5 text-amber-600 font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span><span x-text="warningMin"></span>-<span x-text="dangerMin"></span>m Peringatan</span>
                    </span>
                    <span class="flex items-center space-x-1.5 text-rose-600 font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span>&gt; <span x-text="dangerMin"></span>m Kritis</span>
                    </span>
                </div>
            </div>

            <!-- Responsive Grid of FIFO Ticket Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <template x-for="order in orders" :key="order.id">
                    <div class="bg-white rounded-3xl border shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden"
                        :class="
                            getElapsedMinutes(order.created_at) >= dangerMin
                                ? 'border-rose-400 bg-rose-50/10 ring-2 ring-rose-400/30'
                                : (getElapsedMinutes(order.created_at) >= warningMin
                                    ? 'border-amber-400 bg-amber-50/10 ring-2 ring-amber-400/20'
                                    : 'border-gray-100')
                        ">
                        
                        <!-- Card Header matching Gambar 2 -->
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-mono font-bold text-gray-400" x-text="'#' + order.invoice_number"></span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                        :class="order.table ? 'bg-orange-50 text-[#FF6B00] border border-orange-200' : 'bg-gray-100 text-gray-700'"
                                        x-text="order.table ? order.table.name : 'TAKEAWAY'"></span>
                                </div>
                                <h3 class="font-extrabold text-gray-900 text-base mt-1" x-text="order.customer_name"></h3>
                            </div>

                            <!-- Timer & Progress Status -->
                            <div class="text-right">
                                <div class="flex items-center space-x-1.5 text-xs font-bold"
                                    :class="
                                        order.status === 'ready'
                                            ? 'text-emerald-600'
                                            : (order.status === 'processing' ? 'text-[#FF6B00]' : 'text-amber-600')
                                    ">
                                    <span class="w-2 h-2 rounded-full"
                                        :class="order.status === 'ready' ? 'bg-emerald-500' : 'bg-[#FF6B00] animate-pulse'"></span>
                                    <span x-text="order.status === 'ready' ? 'Siap Saji' : (order.status === 'processing' ? 'Sedang Dimasak' : 'Menunggu')"></span>
                                </div>
                                
                                <!-- Dynamic Stopwatch -->
                                <p class="text-xs font-mono font-black mt-1"
                                    :class="
                                        getElapsedMinutes(order.created_at) >= dangerMin
                                            ? 'text-rose-600 animate-pulse'
                                            : (getElapsedMinutes(order.created_at) >= warningMin ? 'text-amber-600' : 'text-gray-400')
                                    "
                                    x-text="'⏱️ ' + getElapsedDisplay(order.created_at)"></p>
                            </div>
                        </div>

                        <!-- Order General Notes Alert -->
                        <template x-if="order.notes">
                            <div class="px-4 py-2 bg-amber-50 border-b border-amber-100/60 text-amber-800 text-xs font-bold flex items-center space-x-2">
                                <span>⚠️ Catatan Pesanan: <span x-text="order.notes"></span></span>
                            </div>
                        </template>

                        <!-- Items Checklist (Interactive Tap to Plating) -->
                        <div class="p-4 flex-1 space-y-3 divide-y divide-gray-50">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="pt-2.5 first:pt-0 flex items-start space-x-3 group"
                                    x-show="shouldShowItemInStation(item)">
                                    
                                    <!-- Checkbox Plating -->
                                    <button type="button" @click="toggleItemCheck(item)"
                                        class="w-5 h-5 mt-0.5 rounded-lg border flex items-center justify-center transition-all flex-shrink-0 cursor-pointer"
                                        :class="item.status === 'ready' ? 'bg-emerald-500 border-emerald-500 text-white font-black' : 'border-gray-300 hover:border-[#FF6B00]'">
                                        <span x-show="item.status === 'ready'">✓</span>
                                    </button>

                                    <!-- Item Name & Modifiers -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-baseline space-x-2">
                                            <span class="font-black text-xs font-mono text-[#FF6B00]" x-text="item.quantity + 'x'"></span>
                                            <span class="text-xs font-bold text-gray-900 transition-colors"
                                                :class="item.status === 'ready' ? 'line-through text-gray-400' : (item.status === 'cancelled' ? 'line-through text-rose-500' : '')"
                                                x-text="item.menu_name"></span>
                                        </div>

                                        <!-- Modifiers List -->
                                        <template x-if="item.modifiers && item.modifiers.length > 0">
                                            <div class="ml-5 mt-0.5 space-y-0.5">
                                                <template x-for="mod in item.modifiers" :key="mod.id">
                                                    <p class="text-[11px] text-gray-500 font-medium" x-text="'+ ' + mod.name"></p>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Highlighted Cooking Notes -->
                                        <template x-if="item.notes">
                                            <div class="ml-5 mt-1 p-1.5 rounded-lg bg-rose-50 border border-rose-100 text-rose-700 text-[11px] font-bold">
                                                ❗ <span x-text="item.notes"></span>
                                            </div>
                                        </template>

                                        <!-- Cancelled Message -->
                                        <template x-if="item.status === 'cancelled'">
                                            <p class="text-[10px] text-rose-600 font-bold ml-5 mt-0.5" x-text="'❌ Dibatalkan: ' + (item.cancel_reason || 'Bahan habis')"></p>
                                        </template>
                                    </div>

                                    <!-- Quick Cancel Item Trigger -->
                                    <template x-if="item.status !== 'cancelled'">
                                        <button type="button" @click="openCancelItemModal(item)"
                                            class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-rose-500 p-1 text-[11px] transition-opacity"
                                            title="Batalkan Item Ini">
                                            ✕
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Card Action Button: Single Click Progression -->
                        <div class="p-3 bg-gray-50/70 border-t border-gray-100">
                            <button type="button" @click="advanceOrderStatus(order)"
                                class="w-full py-2.5 px-4 rounded-xl font-bold text-xs tracking-wide shadow-md transition-all flex items-center justify-center space-x-2"
                                :class="
                                    order.status === 'pending'
                                        ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20'
                                        : (order.status === 'processing'
                                            ? 'bg-[#FF6B00] hover:bg-orange-600 text-white shadow-orange-500/20'
                                            : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-500/20')
                                ">
                                <span x-text="
                                    order.status === 'pending'
                                        ? 'Mulai Masak (Start)'
                                        : (order.status === 'processing'
                                            ? 'Tandai Siap Saji (Ready)'
                                            : 'Selesai Saji (Done)')
                                "></span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="orders.length === 0"
                    class="col-span-full py-24 text-center space-y-3 bg-white rounded-3xl border border-dashed border-gray-200 p-8">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 text-2xl flex items-center justify-center mx-auto">
                        ✨
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900">Semua Pesanan Dapur Selesai!</h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto">
                        Belum ada pesanan aktif yang menunggu saat ini. Suara bel akan otomatis berbunyi saat ada pesanan baru masuk dari kasir.
                    </p>
                </div>
            </div>
        </main>

        <!-- ======================================================== -->
        <!-- SLIDE-OVER DRAWER: Kendali Menu Habis (Sold Out)         -->
        <!-- ======================================================== -->
        <div x-show="showSoldOutModal" x-transition
            class="fixed inset-0 z-50 overflow-hidden bg-black/60 backdrop-blur-xs flex justify-end" style="display: none;">
            <div class="w-full max-w-md bg-white h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto"
                @click.away="showSoldOutModal = false">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900">Kendali Menu Habis (Sold Out)</h3>
                            <p class="text-xs text-gray-400">Pembaruan langsung disinkronkan ke kasir via Reverb</p>
                        </div>
                        <button type="button" @click="showSoldOutModal = false" class="text-gray-400 hover:text-gray-700 text-lg">
                            ✕
                        </button>
                    </div>

                    <!-- Search Menu -->
                    <div class="py-4">
                        <input type="text" x-model="searchTerm" placeholder="Cari menu untuk diubah ketersediaannya..."
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-[#FF6B00]">
                    </div>

                    <!-- Menu List Toggle -->
                    <div class="space-y-2 max-h-[65vh] overflow-y-auto pr-1 divide-y divide-gray-50">
                        <template x-for="menu in menus" :key="menu.id">
                            <div class="pt-2 first:pt-0 flex items-center justify-between"
                                x-show="' ' + menu.name.toLowerCase().includes(searchTerm.toLowerCase()) || searchTerm === ''">
                                <div>
                                    <p class="text-xs font-bold text-gray-900" x-text="menu.name"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="'Rp ' + Math.round(menu.price).toLocaleString('id-ID')"></p>
                                </div>

                                <button type="button" @click="toggleMenuAvailability(menu)"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="menu.is_available ? 'bg-emerald-50 text-emerald-700 hover:bg-rose-50 hover:text-rose-700' : 'bg-rose-100 text-rose-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                    x-text="menu.is_available ? 'Tersedia' : 'Sold Out'">
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="button" @click="showSoldOutModal = false"
                        class="w-full py-3 rounded-2xl bg-gray-900 text-white font-bold text-xs">
                        Tutup Panel Kendali
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Alasan Pembatalan Item Pesanan                    -->
        <!-- ======================================================== -->
        <div x-show="cancelModalOpen" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.away="cancelModalOpen = false">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Batalkan Item Pesanan</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="activeItemForCancel?.menu_name"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Pembatalan</label>
                    <input type="text" x-model="cancelReasonText"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-rose-500 text-gray-900">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="button" @click="cancelModalOpen = false"
                        class="flex-1 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="button" @click="confirmCancelItem()"
                        class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20">
                        Konfirmasi Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
