<x-layouts.app title="Kasir & Katalog Menu" :withHeader="false">
    <!-- POS Tablet & Desktop Full-Height Layout matching Gambar 2 -->
    <div x-data="{
        search: '',
        activeCategory: 'all',
        showUpgradeBanner: true,
        recipient: 'Hilal Achmad',
        selectedTableId: '{{ $tables->first()?->id }}',
        selectedTableNumber: '{{ $tables->first()?->number ?? '19' }}',
        orderType: 'dine_in',
        orderNotes: '',
        
        // Diskon & Voucher
        discountCode: '',
        discountAmount: 0,
        discountMessage: '',
        
        // Pajak & Servis dari Pengaturan Resto
        taxEnabled: {{ ($settings['tax_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
        taxPercentage: {{ (float)($settings['tax_percentage'] ?? 11) }},
        serviceEnabled: {{ ($settings['service_charge_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
        servicePercentage: {{ (float)($settings['service_charge_percentage'] ?? 5) }},

        // Modals
        showModifierModal: false,
        activeMenuForModifier: null,
        selectedVariantId: null,
        selectedVariantAdjustment: 0,
        selectedAddonIds: [],
        selectedAddonsTotal: 0,
        itemCustomNotes: '',

        showPaymentModal: false,
        paymentMethod: 'cash',
        cashTendered: 0,
        cardLast4: '',
        cardBank: 'BCA',
        qrisRef: 'QRIS-' + Math.floor(100000 + Math.random() * 900000),
        
        // Split Bill State
        splitCount: 2,
        splitPayments: [
            { method: 'cash', amount: 0, reference: '' },
            { method: 'qris', amount: 0, reference: '' }
        ],

        // Processing & Success State
        isSubmitting: false,
        lastSuccessOrder: null,
        showSuccessModal: false,

        // Shift Management
        hasActiveShift: false,
        shiftData: null,
        showShiftModal: false,
        startingCashInput: 100000,
        actualCashInput: 0,
        shiftNotesInput: '',
        isShiftSubmitting: false,

        // Held Orders (Parkir Pesanan)
        heldOrders: [],
        showHeldOrdersModal: false,
        isHoldingOrder: false,

        // Cart Items State (Initial sample matching Gambar 2)
        cart: [
            {
                id: 1,
                menu_id: 1,
                name: 'Sashimi',
                variant_id: null,
                variant_name: 'With Chicken Egg',
                addon_ids: [],
                notes: '',
                price: 45600,
                qty: 1,
                image: 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=150&auto=format&fit=crop&q=80'
            },
            {
                id: 2,
                menu_id: 2,
                name: 'Okonomiyaki',
                variant_id: null,
                variant_name: 'With Spicy Sauce',
                addon_ids: [],
                notes: '',
                price: 52900,
                qty: 1,
                image: 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=150&auto=format&fit=crop&q=80'
            },
            {
                id: 3,
                menu_id: 3,
                name: 'Tamagoyaki',
                variant_id: null,
                variant_name: 'With Soy Sauce',
                addon_ids: [],
                notes: '',
                price: 24000,
                qty: 2,
                image: 'https://images.unsplash.com/photo-1611143669185-af224c5e3252?w=150&auto=format&fit=crop&q=80'
            },
            {
                id: 4,
                menu_id: 4,
                name: 'Noodles Ramen',
                variant_id: null,
                variant_name: 'With Sausages',
                addon_ids: [],
                notes: '',
                price: 45000,
                qty: 1,
                image: 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=150&auto=format&fit=crop&q=80'
            }
        ],

        // Cart Actions
        openModifierOrAdd(menu) {
            if (!menu.is_available) {
                alert('Menu ini saat ini sedang habis (sold out).');
                return;
            }

            // Jika menu punya varian atau addons, buka modal kustomisasi
            if ((menu.variants && menu.variants.length > 0) || (menu.addons && menu.addons.length > 0)) {
                this.activeMenuForModifier = menu;
                this.selectedVariantId = menu.variants.length > 0 ? menu.variants[0].id : null;
                this.selectedVariantAdjustment = menu.variants.length > 0 ? parseFloat(menu.variants[0].price_adjustment) : 0;
                this.selectedAddonIds = [];
                this.selectedAddonsTotal = 0;
                this.itemCustomNotes = '';
                this.showModifierModal = true;
            } else {
                this.addItemDirect(menu);
            }
        },

        addItemDirect(menu) {
            let existing = this.cart.find(i => i.menu_id === menu.id && !i.variant_id && i.addon_ids.length === 0);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    id: Date.now() + Math.random(),
                    menu_id: menu.id,
                    name: menu.name,
                    variant_id: null,
                    variant_name: menu.description ? menu.description.substring(0, 24) : 'Standar',
                    addon_ids: [],
                    notes: '',
                    price: parseFloat(menu.price),
                    qty: 1,
                    image: menu.image || 'https://images.unsplash.com/photo-1544025162-d76694265947?w=150&auto=format&fit=crop&q=80'
                });
            }
        },

        confirmModifierAdd() {
            if (!this.activeMenuForModifier) return;

            let menu = this.activeMenuForModifier;
            let finalPrice = parseFloat(menu.price) + this.selectedVariantAdjustment + this.selectedAddonsTotal;
            
            let variantName = '';
            if (this.selectedVariantId) {
                let v = menu.variants.find(vr => vr.id === this.selectedVariantId);
                if (v) variantName = v.name;
            }

            this.cart.push({
                id: Date.now() + Math.random(),
                menu_id: menu.id,
                name: menu.name,
                variant_id: this.selectedVariantId,
                variant_name: variantName || 'Pilihan Kustom',
                addon_ids: [...this.selectedAddonIds],
                notes: this.itemCustomNotes,
                price: finalPrice,
                qty: 1,
                image: menu.image || 'https://images.unsplash.com/photo-1544025162-d76694265947?w=150&auto=format&fit=crop&q=80'
            });

            this.showModifierModal = false;
        },

        updateQty(index, delta) {
            this.cart[index].qty += delta;
            if (this.cart[index].qty <= 0) {
                this.cart.splice(index, 1);
            }
        },

        clearCart() {
            if (confirm('Kosongkan semua item dalam keranjang?')) {
                this.cart = [];
                this.discountAmount = 0;
                this.discountCode = '';
                try { localStorage.removeItem('amarta_cart'); } catch(e) {}
            }
        },

        // Kalkulasi Rincian Transaksi
        get itemCount() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get tax() {
            if (!this.taxEnabled) return 0;
            let taxable = Math.max(0, this.subtotal - this.discountAmount);
            return taxable * (this.taxPercentage / 100);
        },

        get serviceCharge() {
            if (!this.serviceEnabled) return 0;
            let serviceable = Math.max(0, this.subtotal - this.discountAmount);
            return serviceable * (this.servicePercentage / 100);
        },

        get grandTotal() {
            return Math.max(0, Math.round(this.subtotal - this.discountAmount + this.tax + this.serviceCharge));
        },

        get changeAmount() {
            return Math.max(0, this.cashTendered - this.grandTotal);
        },

        // Quick Cash Helper
        setQuickCash(amount) {
            this.cashTendered = amount;
        },

        setExactCash() {
            this.cashTendered = this.grandTotal;
        },

        // Pembagian Split Bill
        initSplitPayments() {
            let part = Math.ceil(this.grandTotal / this.splitCount);
            this.splitPayments = [];
            for (let i = 0; i < this.splitCount; i++) {
                this.splitPayments.push({
                    method: i === 0 ? 'cash' : 'qris',
                    amount: part,
                    reference: ''
                });
            }
        },

        // Buka Modal Pembayaran
        openPaymentModal() {
            if (this.cart.length === 0) {
                alert('Keranjang pesanan masih kosong! Silakan pilih menu terlebih dahulu.');
                return;
            }
            this.cashTendered = this.grandTotal;
            this.initSplitPayments();
            this.showPaymentModal = true;
        },

        // Eksekusi Checkout via AJAX
        async processCheckout() {
            if (this.isSubmitting) return;

            if (this.paymentMethod === 'cash' && this.cashTendered < this.grandTotal) {
                alert('Nominal uang tunai kurang dari total tagihan!');
                return;
            }

            this.isSubmitting = true;

            let payload = {
                customer_name: this.recipient,
                order_type: this.orderType,
                table_id: this.orderType === 'dine_in' ? this.selectedTableId : null,
                discount_code: this.discountCode || null,
                notes: this.orderNotes || null,
                payment_method: this.paymentMethod,
                cash_tendered: this.cashTendered,
                reference_number: this.paymentMethod === 'qris' ? this.qrisRef : (this.paymentMethod === 'debit' || this.paymentMethod === 'credit' ? (this.cardBank + '-' + this.cardLast4) : null),
                split_payments: this.paymentMethod === 'split' ? this.splitPayments : null,
                items: this.cart.map(i => ({
                    menu_id: i.menu_id,
                    quantity: i.qty,
                    variant_id: i.variant_id,
                    addon_ids: i.addon_ids,
                    notes: i.notes
                }))
            };

            try {
                let response = await fetch('{{ route('pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                let result = await response.json();

                if (response.ok && result.success) {
                    this.lastSuccessOrder = result;
                    this.showPaymentModal = false;
                    this.showSuccessModal = true;
                    this.cart = []; // Kosongkan keranjang
                    try { localStorage.removeItem('amarta_cart'); } catch(e) {}
                } else {
                    alert('Gagal memproses transaksi: ' + (result.message || 'Terjadi kesalahan sistem.'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan: ' + err.message);
            } finally {
                this.isSubmitting = false;
            }
        },

        formatRupiah(val) {
            return 'Rp ' + Math.round(val || 0).toLocaleString('id-ID');
        },

        init() {
            // Restore cart from localStorage jika koneksi terputus/refresh (Resilience)
            try {
                let saved = localStorage.getItem('amarta_cart');
                if (saved) {
                    let parsed = JSON.parse(saved);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.cart = parsed;
                    }
                }
            } catch (e) {
                console.warn('Could not restore cart:', e);
            }

            // Watch perubahan keranjang & simpan ke localStorage
            this.$watch('cart', (val) => {
                try {
                    localStorage.setItem('amarta_cart', JSON.stringify(val));
                } catch (e) {}
            });

            this.checkCurrentShift();
            this.fetchHeldOrders();
        },

        async checkCurrentShift() {
            try {
                let res = await fetch('{{ route('pos.shift.current') }}');
                let data = await res.json();
                this.hasActiveShift = data.has_active_shift;
                this.shiftData = data.shift;
                if (this.shiftData) {
                    this.actualCashInput = this.shiftData.expected_cash;
                }
            } catch (err) {
                console.error('Error fetching shift:', err);
            }
        },

        async openShift() {
            if (this.startingCashInput < 0) {
                alert('Modal awal tidak boleh kurang dari Rp 0.');
                return;
            }
            this.isShiftSubmitting = true;
            try {
                let res = await fetch('{{ route('pos.shift.open') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ starting_cash: this.startingCashInput })
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    alert('Shift kasir berhasil dibuka!');
                    await this.checkCurrentShift();
                    this.showShiftModal = false;
                } else {
                    alert(data.message || 'Gagal membuka shift.');
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            } finally {
                this.isShiftSubmitting = false;
            }
        },

        async closeShift() {
            if (this.actualCashInput < 0) {
                alert('Kas fisik tidak boleh bernilai negatif.');
                return;
            }
            if (!confirm('Apakah Anda yakin ingin menutup dan merekonsiliasi shift ini?')) return;

            this.isShiftSubmitting = true;
            try {
                let res = await fetch('{{ route('pos.shift.close') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({
                        actual_cash: this.actualCashInput,
                        notes: this.shiftNotesInput
                    })
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    let diff = data.summary.difference;
                    let diffMsg = diff === 0 ? 'Kas seimbang (Rp 0)!' : (diff > 0 ? 'Surplus: +Rp ' + diff.toLocaleString('id-ID') : 'Selisih kurang: -Rp ' + Math.abs(diff).toLocaleString('id-ID'));
                    alert('Shift berhasil ditutup!\n' + diffMsg);
                    await this.checkCurrentShift();
                    this.showShiftModal = false;
                } else {
                    alert(data.message || 'Gagal menutup shift.');
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            } finally {
                this.isShiftSubmitting = false;
            }
        },

        async fetchHeldOrders() {
            try {
                let res = await fetch('{{ route('pos.held-orders') }}');
                let data = await res.json();
                if (data.success) {
                    this.heldOrders = data.orders || [];
                }
            } catch (err) {
                console.error('Error fetching held orders:', err);
            }
        },

        async holdCurrentOrder() {
            if (this.cart.length === 0) {
                alert('Keranjang belanja kosong. Masukkan minimal 1 menu untuk ditahan.');
                return;
            }
            this.isHoldingOrder = true;
            try {
                let payload = {
                    customer_name: this.recipient || 'Tamu Resto',
                    table_id: this.orderType === 'dine_in' ? this.selectedTableId : null,
                    order_type: this.orderType,
                    subtotal: this.subtotal,
                    total_amount: this.grandTotal,
                    notes: this.orderNotes,
                    items: this.cart
                };
                let res = await fetch('{{ route('pos.hold') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    alert('Pesanan tamu ' + payload.customer_name + ' berhasil ditahan (parkir).');
                    this.cart = [];
                    this.discountAmount = 0;
                    this.discountCode = '';
                    try { localStorage.removeItem('amarta_cart'); } catch(e) {}
                    await this.fetchHeldOrders();
                } else {
                    alert(data.message || 'Gagal menahan pesanan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            } finally {
                this.isHoldingOrder = false;
            }
        },

        async resumeHeldOrder(orderId) {
            try {
                let res = await fetch('/pos/orders/' + orderId + '/resume', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    }
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    let ord = data.order;
                    this.recipient = ord.customer_name;
                    if (ord.table_id) {
                        this.selectedTableId = ord.table_id;
                        this.orderType = 'dine_in';
                    } else {
                        this.orderType = ord.order_type;
                    }
                    this.cart = ord.items.map(item => ({
                        id: Date.now() + Math.random(),
                        menu_id: item.menu_id,
                        name: item.menu_name,
                        variant_id: null,
                        variant_name: 'Pilihan Semula',
                        addon_ids: [],
                        notes: item.notes || '',
                        price: parseFloat(item.unit_price),
                        qty: parseInt(item.quantity),
                        image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=150&auto=format&fit=crop&q=80'
                    }));
                    await this.fetchHeldOrders();
                    this.showHeldOrdersModal = false;
                    alert('Pesanan ' + ord.customer_name + ' berhasil dipanggil kembali ke keranjang!');
                } else {
                    alert(data.message || 'Gagal memanggil pesanan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            }
        },

        async voidHeldOrder(orderId) {
            let reason = prompt('Masukkan alasan pembatalan / void pesanan:');
            if (!reason) return;

            try {
                let res = await fetch('/pos/orders/' + orderId + '/void', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ cancel_reason: reason })
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    alert('Pesanan berhasil dibatalkan (void).');
                    await this.fetchHeldOrders();
                } else {
                    alert(data.message || 'Gagal membatalkan pesanan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            }
        }
    }"
    @keydown.f1.prevent.window="$refs.posSearchInput?.focus()"
    @keydown.f2.prevent.window="showShiftModal = true"
    @keydown.f4.prevent.window="if (cart.length > 0) holdCurrentOrder()"
    @keydown.f9.prevent.window="if (cart.length > 0) openPaymentModal()"
    @keydown.escape.window="showModifierModal = false; showPaymentModal = false; showShiftModal = false; showHeldOrdersModal = false; showSuccessModal = false;"
    class="h-full w-full flex flex-col xl:flex-row overflow-hidden bg-white">

        <!-- ========================================== -->
        <!-- CENTER WORKSPACE (Menu & Order Line)       -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col h-full overflow-y-auto p-5 sm:p-6 lg:p-7 space-y-5 bg-white scrollbar-thin">
            
            <!-- 1. Section: Order Line & Quick Shift/Parkir Status (Gambar 2) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                            Order Line
                        </h2>

                        <!-- Shift Status Pill (Gambar 2 aligned) -->
                        <button type="button" @click="showShiftModal = true"
                            class="px-3 py-1 rounded-xl text-xs font-bold flex items-center space-x-1.5 transition-all shadow-xs"
                            :class="hasActiveShift ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 animate-pulse'">
                            <span class="w-2 h-2 rounded-full" :class="hasActiveShift ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="hasActiveShift ? 'Shift Aktif' : 'Buka Shift (F2)'"></span>
                        </button>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Held Orders Button -->
                        <button type="button" @click="showHeldOrdersModal = true; fetchHeldOrders()"
                            class="px-3 py-1 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-700 flex items-center space-x-1.5 transition-all shadow-xs"
                            title="Lihat pesanan yang sedang diparkir">
                            <span>⏸️ Parkir</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-[#FF6B00] text-white text-[10px] font-mono font-bold"
                                x-text="heldOrders.length"></span>
                        </button>

                        <a href="{{ route('pos.orders') }}" class="text-xs font-semibold text-gray-400 hover:text-[#FF6B00] transition-colors ml-2 hidden sm:inline">
                            Riwayat →
                        </a>
                    </div>
                </div>

                <!-- Notice / Upgrade Banner (Exact match to Gambar 2) -->
                <div x-show="showUpgradeBanner" x-transition
                    class="bg-white rounded-2xl p-4 border border-[#FF6B00]/40 flex items-center justify-between shadow-xs relative">
                    <div class="flex items-center space-x-3.5 relative z-10">
                        <div class="w-11 h-11 rounded-xl bg-[#16171B] flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-[#FFA149]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/>
                                <rect x="9" y="9" width="6" height="6" stroke-width="2"/>
                                <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-gray-900 leading-tight">
                                Don't miss the opportunity to upgrade your system to the latest version
                            </h4>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Update to the latest version soon to discover the many exciting features we've added!
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 relative z-10 flex-shrink-0">
                        <!-- Mini Phone graphic mockup matching Gambar 2 -->
                        <div class="hidden sm:flex items-center justify-center w-8 h-10 bg-amber-50 rounded-lg border border-amber-200/60 p-0.5 -rotate-6 shadow-xs">
                            <div class="w-full h-full bg-white rounded-md flex flex-col items-center justify-center p-0.5">
                                <div class="w-3 h-0.5 bg-gray-200 rounded-full mb-0.5"></div>
                                <div class="w-full h-4 bg-orange-100 rounded-xs"></div>
                            </div>
                        </div>

                        <button type="button" @click="alert('Sistem Amarta Boga POS sudah versi 1.0 terbaru!')"
                            class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-orange-300 text-gray-800 hover:text-[#FF6B00] font-bold text-xs transition-colors shadow-xs flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Get Started</span>
                        </button>

                        <button type="button" @click="showUpgradeBanner = false"
                            class="w-5 h-5 rounded-full bg-gray-900 text-white flex items-center justify-center text-[10px] hover:bg-black transition-colors"
                            title="Tutup banner">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Horizontal Scrollable Queue Cards matching Gambar 2 -->
                <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-none">
                    @forelse ($activeOrders as $order)
                        <div class="flex-shrink-0 w-72 p-4 rounded-2xl bg-white border border-gray-100 shadow-xs hover:shadow-md hover:border-orange-200 transition-all cursor-pointer">
                            <div class="flex items-center justify-between text-xs text-gray-400 font-medium mb-1.5">
                                <span class="font-mono text-orange-500 font-bold">#{{ $order->invoice_number }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-gray-50 border border-gray-100 text-gray-600 font-bold text-[10px]">
                                    {{ $order->table ? $order->table->name : 'Takeaway' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm truncate">
                                {{ $order->customer_name }}
                            </h3>
                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-50 text-xs">
                                <div class="flex items-center space-x-2 text-[#FF6B00] font-bold">
                                    <div class="w-7 h-7 rounded-full border-2 border-[#FF6B00] flex items-center justify-center text-[9px] font-black text-[#FF6B00]">
                                        85%
                                    </div>
                                    <span>{{ $order->status === 'processing' ? 'On Progress' : 'Menunggu' }}</span>
                                </div>
                                <a href="{{ route('pos.receipt', $order) }}" target="_blank" class="font-semibold text-gray-400 hover:text-[#FF6B00]">
                                    {{ $order->items->count() }} items →
                                </a>
                            </div>
                        </div>
                    @empty
                        <!-- Mock Sample Cards matching Gambar 2 -->
                        <div class="flex-shrink-0 w-72 p-4 rounded-2xl bg-white border border-gray-100 shadow-xs hover:shadow-md hover:border-orange-200 transition-all cursor-pointer">
                            <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                                <span class="font-mono text-orange-500 font-bold">#231128001</span>
                                <span class="px-2 py-0.5 rounded-md bg-gray-50 border border-gray-100 text-gray-600 font-bold text-[10px]">Table 12</span>
                            </div>
                            <h3 class="font-extrabold text-gray-900 text-sm">Rachmad Jailani</h3>
                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-50 text-xs">
                                <div class="flex items-center space-x-2 text-[#FF6B00] font-bold">
                                    <div class="w-7 h-7 rounded-full border-2 border-[#FF6B00] flex items-center justify-center text-[9px] font-black text-[#FF6B00]">
                                        85%
                                    </div>
                                    <span>On Progress</span>
                                </div>
                                <span class="font-semibold text-gray-400 hover:text-gray-600">6 items →</span>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-72 p-4 rounded-2xl bg-white border border-gray-100 shadow-xs hover:shadow-md hover:border-orange-200 transition-all cursor-pointer">
                            <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                                <span class="font-mono text-orange-500 font-bold">#231128002</span>
                                <span class="px-2 py-0.5 rounded-md bg-gray-50 border border-gray-100 text-gray-600 font-bold text-[10px]">Table 9</span>
                            </div>
                            <h3 class="font-extrabold text-gray-900 text-sm">Steve Houdini</h3>
                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-50 text-xs">
                                <div class="flex items-center space-x-2 text-[#FF6B00] font-bold">
                                    <div class="w-7 h-7 rounded-full border-2 border-orange-300 flex items-center justify-center text-[9px] font-black text-[#FF6B00]">
                                        60%
                                    </div>
                                    <span>On Progress</span>
                                </div>
                                <span class="font-semibold text-gray-400 hover:text-gray-600">9 items →</span>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-72 p-4 rounded-2xl bg-white border border-gray-100 shadow-xs hover:shadow-md hover:border-orange-200 transition-all cursor-pointer">
                            <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                                <span class="font-mono text-orange-500 font-bold">#231128003</span>
                                <span class="px-2 py-0.5 rounded-md bg-gray-50 border border-gray-100 text-gray-600 font-bold text-[10px]">Table 14</span>
                            </div>
                            <h3 class="font-extrabold text-gray-900 text-sm">Maulana Ragnar</h3>
                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-50 text-xs">
                                <div class="flex items-center space-x-2 text-amber-500 font-bold">
                                    <div class="w-7 h-7 rounded-full border-2 border-amber-400 flex items-center justify-center text-[9px] font-black text-amber-500">
                                        40%
                                    </div>
                                    <span>On Progress</span>
                                </div>
                                <span class="font-semibold text-gray-400 hover:text-gray-600">12 items →</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Section: Menu Catalog & Categories (Gambar 2) -->
            <div class="space-y-4">
                <!-- Header with Title, Search Bar & Filter Button -->
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                        Menu
                    </h2>

                    <div class="flex items-center space-x-3 flex-1 max-w-sm justify-end">
                        <div class="relative w-full max-w-xs">
                            <input type="text" x-model="search" x-ref="posSearchInput" placeholder="Cari menu... (F1)"
                                class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200/80 rounded-full text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B00]/20 focus:border-[#FF6B00] shadow-sm transition-all">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <button type="button" @click="activeCategory = 'all'; search = ''"
                            class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-500 hover:text-gray-800 flex items-center justify-center shadow-sm flex-shrink-0"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Category Horizontal Underline Tabs (Gambar 2) -->
                <div class="flex items-center space-x-6 overflow-x-auto pb-1 border-b border-gray-200/80 scrollbar-none text-xs font-semibold">
                    <button type="button" @click="activeCategory = 'all'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'all' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Main Course
                    </button>
                    <button type="button" @click="activeCategory = 'favorit'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'favorit' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Favorite
                    </button>
                    <button type="button" @click="activeCategory = 'food'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'food' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Food
                    </button>
                    <button type="button" @click="activeCategory = 'drink'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'drink' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Drink
                    </button>
                    <button type="button" @click="activeCategory = 'dessert'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'dessert' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Dessert
                    </button>
                    <button type="button" @click="activeCategory = 'salad'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'salad' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Salad
                    </button>
                    <button type="button" @click="activeCategory = 'soup'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'soup' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Soup
                    </button>
                    <button type="button" @click="activeCategory = 'ice-cream'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'ice-cream' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Ice cream
                    </button>
                    <button type="button" @click="activeCategory = 'coffee'"
                        class="pb-2.5 whitespace-nowrap transition-colors relative"
                        :class="activeCategory === 'coffee' ? 'text-[#FF6B00] font-bold border-b-2 border-[#FF6B00]' : 'text-gray-400 hover:text-gray-700'">
                        Coffee
                    </button>
                </div>

                <!-- 4-Column Product Cards Grid (Gambar 2) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($menus as $menu)
                        <div x-show="(activeCategory === 'all' || '{{ $menu->category?->slug }}' === activeCategory) && ('{{ strtolower($menu->name) }}'.includes(search.toLowerCase()) || search === '')"
                            @click="openModifierOrAdd({{ json_encode($menu) }})"
                            class="bg-white rounded-2xl border border-gray-100/90 p-3 shadow-sm hover:shadow-md hover:border-orange-300 transition-all duration-200 cursor-pointer group flex flex-col justify-between">
                            <div>
                                <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 mb-2.5 relative">
                                    <img src="{{ $menu->image }}" alt="{{ $menu->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @if (!$menu->is_available)
                                        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center">
                                            <span class="px-2.5 py-1 rounded-full bg-rose-600 text-white text-[10px] font-black uppercase tracking-wider">
                                                Sold Out
                                            </span>
                                        </div>
                                    @endif
                                    @if ($menu->variants->count() > 0 || $menu->addons->count() > 0)
                                        <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-md bg-black/60 text-white text-[9px] font-bold">
                                            + Varian
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-start justify-between gap-1">
                                    <h3 class="text-xs font-bold text-gray-900 group-hover:text-[#FF6B00] transition-colors line-clamp-1">
                                        {{ $menu->name }}
                                    </h3>
                                    <span class="text-xs font-bold text-[#FF6B00] font-mono whitespace-nowrap">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <p class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">
                                    {{ $menu->description ?? 'Special Chef Selection' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT PANEL: Current Order (Gambar 2)      -->
        <!-- ========================================== -->
        <div class="w-full xl:w-[380px] bg-white border-l border-gray-100 flex flex-col justify-between h-full p-5 sm:p-6 flex-shrink-0 z-20">
            <div class="flex-1 flex flex-col min-h-0">
                <!-- Header with ... Icon matching Gambar 2 -->
                <div class="flex items-center justify-between pb-3">
                    <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">
                        Current Order
                    </h2>
                    <div class="flex items-center space-x-1">
                        <button type="button" @click="clearCart()" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors" title="Kosongkan Keranjang">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        <button type="button" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors text-base font-black" title="Opsi Pesanan">
                            •••
                        </button>
                    </div>
                </div>

                <!-- Recipient & Table Card matching Gambar 2 -->
                <div class="p-3.5 rounded-2xl bg-[#F8F9FA] border border-gray-100 space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <span class="text-[10px] font-semibold text-gray-400 block uppercase tracking-wider">Recipient</span>
                            <input type="text" x-model="recipient"
                                class="w-full font-bold text-gray-900 text-xs bg-transparent border-none p-0 focus:outline-none focus:ring-0">
                        </div>
                        <!-- Order Type Toggle -->
                        <div class="flex items-center space-x-1 bg-gray-200/60 p-0.5 rounded-lg text-[10px] font-bold">
                            <button type="button" @click="orderType = 'dine_in'"
                                class="px-2 py-1 rounded-md transition-all"
                                :class="orderType === 'dine_in' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500'">
                                Dine In
                            </button>
                            <button type="button" @click="orderType = 'takeaway'"
                                class="px-2 py-1 rounded-md transition-all"
                                :class="orderType === 'takeaway' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500'">
                                Takeaway
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-200/60 text-xs">
                        <div>
                            <span class="text-[10px] text-gray-400 block">Transaction ID</span>
                            <span class="font-bold text-gray-900 font-mono text-xs">#INV-LIVE</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 block">Table Number</span>
                            <select x-model="selectedTableId"
                                @change="let t = {{ json_encode($tables) }}.find(tb => tb.id == selectedTableId); selectedTableNumber = t ? t.number : '19'"
                                class="w-full font-bold text-gray-900 text-xs bg-transparent border-none p-0 focus:outline-none cursor-pointer">
                                @foreach ($tables as $tbl)
                                    <option value="{{ $tbl->id }}">{{ $tbl->name }} ({{ $tbl->capacity }}p)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Voucher Promo Input -->
                <div class="flex items-center space-x-2 mb-3">
                    <input type="text" x-model="discountCode" placeholder="Kode Diskon (e.g. WEEKEND15)"
                        class="flex-1 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs uppercase font-mono text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                    <button type="button" @click="
                        if (discountCode.toUpperCase() === 'WEEKEND15') {
                            discountAmount = Math.round(subtotal * 0.15);
                            alert('Diskon 15% WEEKEND15 berhasil diterapkan!');
                        } else if (discountCode.toUpperCase() === 'HEMAT10') {
                            discountAmount = Math.min(25000, Math.round(subtotal * 0.10));
                            alert('Diskon HEMAT10 berhasil diterapkan!');
                        } else {
                            alert('Kode diskon tidak valid atau minimal belanja belum terpenuhi.');
                        }
                    " class="px-3 py-1.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-bold shadow-xs">
                        Terapkan
                    </button>
                </div>

                <!-- Cart Items Scrollable List matching Gambar 2 -->
                <div class="flex-1 overflow-y-auto space-y-3 pr-1 divide-y divide-gray-50 scrollbar-thin">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="pt-3 first:pt-0 flex items-center space-x-3 group">
                            <!-- Small Square Thumbnail matching Gambar 2 -->
                            <img :src="item.image" :alt="item.name"
                                class="w-12 h-12 rounded-xl object-cover bg-gray-100 flex-shrink-0">

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 truncate" x-text="item.name"></h4>
                                <p class="text-[10px] text-gray-400 truncate" x-text="item.variant_name"></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <button type="button" @click="updateQty(index, -1)"
                                        class="w-4 h-4 rounded-md bg-gray-100 text-gray-600 hover:bg-orange-100 hover:text-[#FF6B00] flex items-center justify-center text-[10px] font-bold">
                                        -
                                    </button>
                                    <span class="text-[11px] font-semibold text-gray-500" x-text="item.qty + ' items'"></span>
                                    <button type="button" @click="updateQty(index, 1)"
                                        class="w-4 h-4 rounded-md bg-gray-100 text-gray-600 hover:bg-orange-100 hover:text-[#FF6B00] flex items-center justify-center text-[10px] font-bold">
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Price in Orange matching Gambar 2 -->
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-bold text-[#FF6B00] font-mono" x-text="formatRupiah(item.price * item.qty)"></span>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="py-12 text-center text-xs text-gray-400">
                        Keranjang transaksi masih kosong.
                    </div>
                </div>
            </div>

            <!-- Bottom Calculation Breakdown & Big Orange Process Button (Gambar 2) -->
            <div class="pt-3 border-t border-gray-100 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-400 font-medium">
                    <span>Item</span>
                    <span class="text-gray-900 font-bold" x-text="itemCount + ' Items'"></span>
                </div>
                <div class="flex justify-between text-gray-400 font-medium">
                    <span>Sub Total</span>
                    <span class="text-gray-900 font-bold font-mono" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div x-show="discountAmount > 0" class="flex justify-between text-gray-400 font-medium">
                    <span>Discount</span>
                    <span class="text-emerald-600 font-bold font-mono" x-text="'-' + formatRupiah(discountAmount)"></span>
                </div>
                <div x-show="taxEnabled" class="flex justify-between text-gray-400 font-medium">
                    <span>Tax (PB1 {{ (float)($settings['tax_percentage'] ?? 11) }}%)</span>
                    <span class="text-gray-900 font-bold font-mono" x-text="formatRupiah(tax)"></span>
                </div>
                <div x-show="serviceEnabled" class="flex justify-between text-gray-400 font-medium">
                    <span>Service ({{ (float)($settings['service_charge_percentage'] ?? 5) }}%)</span>
                    <span class="text-gray-900 font-bold font-mono" x-text="formatRupiah(serviceCharge)"></span>
                </div>

                <!-- Grand Total Row matching Gambar 2 -->
                <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                    <span class="text-xs font-extrabold text-gray-900">Total</span>
                    <span class="text-xl font-extrabold text-[#FF6B00] font-mono" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <!-- Action Buttons: Parkir (Hold) & Bayar Sekarang -->
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <button type="button" @click="holdCurrentOrder()" :disabled="cart.length === 0 || isHoldingOrder"
                        class="py-3 px-3 rounded-2xl bg-gray-100 hover:bg-gray-200 disabled:opacity-40 text-gray-700 font-bold text-xs flex items-center justify-center space-x-1.5 transition-all shadow-xs"
                        title="Tahan pesanan sementara untuk melayani antrean lain (F4)">
                        <span x-show="!isHoldingOrder">⏸️ Parkir (F4)</span>
                        <span x-show="isHoldingOrder" class="animate-spin text-xs">⏳</span>
                    </button>
                    <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0"
                        class="col-span-2 py-3 px-4 rounded-2xl bg-[#FF6B00] hover:bg-[#EA580C] disabled:opacity-40 text-white font-bold text-xs tracking-wide shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center space-x-2">
                        <span>Bayar Sekarang (F9)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Kustomisasi Modifiers (Varian, Addon & Catatan)    -->
        <!-- ======================================================== -->
        <div x-show="showModifierModal" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5" @click.away="showModifierModal = false">
                <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900" x-text="activeMenuForModifier?.name"></h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5" x-text="formatRupiah(activeMenuForModifier?.price)"></p>
                    </div>
                    <button type="button" @click="showModifierModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <!-- Variants Selection (Radio) -->
                <template x-if="activeMenuForModifier?.variants && activeMenuForModifier.variants.length > 0">
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">Pilih Varian Menu</label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="v in activeMenuForModifier.variants" :key="v.id">
                                <label class="p-3 rounded-2xl border cursor-pointer flex flex-col justify-between text-xs transition-all"
                                    :class="selectedVariantId === v.id ? 'border-[#FF6B00] bg-orange-50/50 text-[#FF6B00] font-bold' : 'border-gray-200 bg-white text-gray-700'">
                                    <div class="flex items-center justify-between">
                                        <span x-text="v.name"></span>
                                        <input type="radio" name="variant" :value="v.id" x-model="selectedVariantId"
                                            @change="selectedVariantAdjustment = parseFloat(v.price_adjustment)" class="text-[#FF6B00] focus:ring-[#FF6B00]">
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 font-mono" x-text="v.price_adjustment > 0 ? '+ ' + formatRupiah(v.price_adjustment) : 'Gratis'"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Add-ons Selection (Checkboxes) -->
                <template x-if="activeMenuForModifier?.addons && activeMenuForModifier.addons.length > 0">
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">Pilih Tambahan (Add-ons)</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                            <template x-for="addon in activeMenuForModifier.addons" :key="addon.id">
                                <label class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs cursor-pointer hover:bg-gray-50">
                                    <div class="flex items-center space-x-2.5">
                                        <input type="checkbox" :value="addon.id" x-model="selectedAddonIds"
                                            @change="
                                                selectedAddonsTotal = activeMenuForModifier.addons
                                                    .filter(a => selectedAddonIds.includes(a.id))
                                                    .reduce((s, a) => s + parseFloat(a.price), 0)
                                            " class="rounded text-[#FF6B00] border-gray-300 focus:ring-[#FF6B00]">
                                        <span class="font-bold text-gray-800" x-text="addon.name"></span>
                                    </div>
                                    <span class="font-mono text-gray-500 text-[11px]" x-text="'+ ' + formatRupiah(addon.price)"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Item Notes -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-500 mb-1">Catatan Khusus Koki</label>
                    <input type="text" x-model="itemCustomNotes" placeholder="e.g. Pedas level 3, kuah pisah, tanpa daun bawang"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#FF6B00] text-gray-900 placeholder-gray-400">
                </div>

                <!-- Submit Button -->
                <button type="button" @click="confirmModifierAdd()"
                    class="w-full py-3 px-4 rounded-2xl bg-[#FF6B00] hover:bg-[#EA580C] text-white font-bold text-xs shadow-md transition-all">
                    Tambah ke Keranjang
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Pembayaran / Checkout (Cash, QRIS, EDC, Split)    -->
        <!-- ======================================================== -->
        <div x-show="showPaymentModal" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6" @click.away="if (!isSubmitting) showPaymentModal = false">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Proses Pembayaran</h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">Tamu: <span class="text-gray-900 font-bold" x-text="recipient"></span> • <span x-text="orderType === 'dine_in' ? 'Meja ' + selectedTableNumber : 'Takeaway'"></span></p>
                    </div>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <!-- Total Tagihan Box -->
                <div class="p-4 rounded-2xl bg-orange-50 border border-orange-100 text-center">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-orange-600 block">Total Tagihan Final</span>
                    <span class="text-3xl font-black text-[#FF6B00] font-mono mt-1 block" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <!-- Payment Methods Selector Tabs -->
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" @click="paymentMethod = 'cash'"
                        class="p-2.5 rounded-2xl border text-center transition-all flex flex-col items-center justify-center"
                        :class="paymentMethod === 'cash' ? 'border-[#FF6B00] bg-orange-50 text-[#FF6B00] font-bold shadow-xs' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                        <span class="text-base">💵</span>
                        <span class="text-[10px] mt-1">Tunai</span>
                    </button>
                    <button type="button" @click="paymentMethod = 'qris'"
                        class="p-2.5 rounded-2xl border text-center transition-all flex flex-col items-center justify-center"
                        :class="paymentMethod === 'qris' ? 'border-[#FF6B00] bg-orange-50 text-[#FF6B00] font-bold shadow-xs' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                        <span class="text-base">📱</span>
                        <span class="text-[10px] mt-1">QRIS</span>
                    </button>
                    <button type="button" @click="paymentMethod = 'debit'"
                        class="p-2.5 rounded-2xl border text-center transition-all flex flex-col items-center justify-center"
                        :class="paymentMethod === 'debit' ? 'border-[#FF6B00] bg-orange-50 text-[#FF6B00] font-bold shadow-xs' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                        <span class="text-base">💳</span>
                        <span class="text-[10px] mt-1">Debit</span>
                    </button>
                    <button type="button" @click="paymentMethod = 'split'"
                        class="p-2.5 rounded-2xl border text-center transition-all flex flex-col items-center justify-center"
                        :class="paymentMethod === 'split' ? 'border-[#FF6B00] bg-orange-50 text-[#FF6B00] font-bold shadow-xs' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                        <span class="text-base">👥</span>
                        <span class="text-[10px] mt-1">Split Bill</span>
                    </button>
                </div>

                <!-- 1. Cash Section -->
                <div x-show="paymentMethod === 'cash'" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Uang Tunai Diterima (Rp)</label>
                        <input type="number" x-model.number="cashTendered"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-base font-mono font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#FF6B00]">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setExactCash()"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-xs">
                            Uang Pas
                        </button>
                        <button type="button" @click="setQuickCash(50000)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            50.000
                        </button>
                        <button type="button" @click="setQuickCash(100000)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            100.000
                        </button>
                        <button type="button" @click="setQuickCash(200000)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            200.000
                        </button>
                        <button type="button" @click="setQuickCash(500000)"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            500.000
                        </button>
                    </div>

                    <!-- Kembalian Box -->
                    <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200 flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">Kembalian:</span>
                        <span class="font-black text-base font-mono"
                            :class="cashTendered >= grandTotal ? 'text-emerald-600' : 'text-rose-600'"
                            x-text="cashTendered >= grandTotal ? formatRupiah(changeAmount) : 'Kurang ' + formatRupiah(grandTotal - cashTendered)"></span>
                    </div>
                </div>

                <!-- 2. QRIS Section -->
                <div x-show="paymentMethod === 'qris'" class="text-center space-y-3">
                    <div class="w-48 h-48 mx-auto p-3 bg-white border border-gray-200 rounded-3xl shadow-sm flex flex-col items-center justify-center">
                        <!-- Simulated QR Code -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=AMARTA-BOGA-POS-PAYMENT" alt="QRIS Code" class="w-40 h-40 object-contain">
                    </div>
                    <p class="text-xs text-gray-500 font-mono">ID Ref: <span class="font-bold text-gray-900" x-text="qrisRef"></span></p>
                    <p class="text-[11px] text-gray-400">Scan melalui BCA, GoPay, OVO, Dana, ShopeePay, atau Livin Mandiri</p>
                </div>

                <!-- 3. Debit / Credit Section -->
                <div x-show="paymentMethod === 'debit' || paymentMethod === 'credit'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Bank / Mesin EDC</label>
                        <select x-model="cardBank" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-900">
                            <option value="BCA">BCA (EDC 01)</option>
                            <option value="Mandiri">Mandiri (EDC 02)</option>
                            <option value="BRI">BRI (EDC 03)</option>
                            <option value="BNI">BNI (EDC 04)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">4 Digit Terakhir Kartu</label>
                        <input type="text" maxlength="4" x-model="cardLast4" placeholder="e.g. 4821"
                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold text-gray-900">
                    </div>
                </div>

                <!-- 4. Split Bill Section -->
                <div x-show="paymentMethod === 'split'" class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span>Bagi rata menjadi:</span>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="splitCount = 2; initSplitPayments()"
                                class="px-2.5 py-1 rounded-lg border text-xs"
                                :class="splitCount === 2 ? 'bg-[#FF6B00] text-white font-bold' : 'bg-gray-50 text-gray-600'">2 Orang</button>
                            <button type="button" @click="splitCount = 3; initSplitPayments()"
                                class="px-2.5 py-1 rounded-lg border text-xs"
                                :class="splitCount === 3 ? 'bg-[#FF6B00] text-white font-bold' : 'bg-gray-50 text-gray-600'">3 Orang</button>
                            <button type="button" @click="splitCount = 4; initSplitPayments()"
                                class="px-2.5 py-1 rounded-lg border text-xs"
                                :class="splitCount === 4 ? 'bg-[#FF6B00] text-white font-bold' : 'bg-gray-50 text-gray-600'">4 Orang</button>
                        </div>
                    </div>

                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        <template x-for="(sp, idx) in splitPayments" :key="idx">
                            <div class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 flex items-center space-x-2 text-xs">
                                <span class="font-bold text-gray-600 w-16" x-text="'Orang ' + (idx + 1)"></span>
                                <select x-model="sp.method" class="bg-white border border-gray-200 rounded-lg text-xs py-1 px-2 font-bold">
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit</option>
                                </select>
                                <input type="number" x-model.number="sp.amount"
                                    class="flex-1 bg-white border border-gray-200 rounded-lg text-xs font-mono font-bold py-1 px-2 text-right">
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="button" @click="processCheckout()" :disabled="isSubmitting"
                    class="w-full py-3.5 px-4 rounded-2xl bg-[#FF6B00] hover:bg-[#EA580C] text-white font-bold text-sm tracking-wide shadow-lg shadow-orange-500/25 transition-all duration-200 flex items-center justify-center space-x-2">
                    <span x-show="!isSubmitting">Konfirmasi & Cetak Struk</span>
                    <span x-show="isSubmitting" class="flex items-center space-x-2">
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span>Memproses Pesanan...</span>
                    </span>
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Sukses Checkout & Cetak Struk Thermal             -->
        <!-- ======================================================== -->
        <div x-show="showSuccessModal" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    ✓
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">Pembayaran Berhasil!</h3>
                    <p class="text-xs text-gray-400 mt-0.5 font-mono" x-text="lastSuccessOrder?.order?.invoice_number"></p>
                </div>

                <div class="p-3.5 rounded-2xl bg-gray-50 text-xs space-y-1">
                    <div class="flex justify-between text-gray-500">
                        <span>Total Bayar:</span>
                        <span class="font-bold text-gray-900 font-mono" x-text="formatRupiah(lastSuccessOrder?.order?.total_amount)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Kembalian:</span>
                        <span class="font-bold text-emerald-600 font-mono text-sm" x-text="formatRupiah(lastSuccessOrder?.order?.change_amount)"></span>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <!-- Tombol Cetak Struk (Membuka popup print) -->
                    <a :href="lastSuccessOrder?.receipt_url" target="_blank"
                        class="w-full py-3 rounded-2xl bg-[#FF6B00] hover:bg-orange-600 text-white font-bold text-xs shadow-md shadow-orange-500/25 flex items-center justify-center space-x-1.5 transition-all">
                        <span>🖨️ Cetak Struk Thermal</span>
                    </a>

                    <!-- Tombol Unduh PDF -->
                    <a :href="lastSuccessOrder?.pdf_url"
                        class="w-full py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs flex items-center justify-center space-x-1.5 transition-all">
                        <span>📥 Unduh PDF Struk</span>
                    </a>

                    <!-- Tutup Modal & Transaksi Baru -->
                    <button type="button" @click="showSuccessModal = false"
                        class="w-full py-2 text-xs font-bold text-gray-400 hover:text-gray-600">
                        Transaksi Baru (Selesai)
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Kelola Shift Kasir (Buka & Rekonsiliasi Tutup)     -->
        <!-- ======================================================== -->
        <div x-show="showShiftModal" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5" @click.away="showShiftModal = false">
                <!-- Header Modal -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-[#FF6B00] flex items-center justify-center text-lg">
                            💼
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900" x-text="hasActiveShift ? 'Rekonsiliasi & Tutup Shift' : 'Buka Shift Kasir Baru'"></h3>
                            <p class="text-[11px] text-gray-400" x-text="hasActiveShift ? 'Verifikasi saldo fisik uang tunai di laci kasir' : 'Tetapkan modal awal uang tunai sebelum melayani pesanan'"></p>
                        </div>
                    </div>
                    <button type="button" @click="showShiftModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>

                <!-- 1. State: Buka Shift (Belum ada shift aktif) -->
                <div x-show="!hasActiveShift" class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">
                            Modal Awal Uang Tunai (Rp)
                        </label>
                        <input type="number" x-model.number="startingCashInput"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-base font-mono font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#FF6B00]">
                    </div>

                    <!-- Quick Preset Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="startingCashInput = 50000"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            50.000
                        </button>
                        <button type="button" @click="startingCashInput = 100000"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            100.000
                        </button>
                        <button type="button" @click="startingCashInput = 200000"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            200.000
                        </button>
                        <button type="button" @click="startingCashInput = 500000"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-mono font-bold text-gray-700 shadow-xs">
                            500.000
                        </button>
                    </div>

                    <button type="button" @click="openShift()" :disabled="isShiftSubmitting"
                        class="w-full py-3.5 px-4 rounded-2xl bg-[#FF6B00] hover:bg-[#EA580C] text-white font-bold text-xs tracking-wide shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center space-x-2">
                        <span x-show="!isShiftSubmitting">Buka Shift Kasir Sekarang</span>
                        <span x-show="isShiftSubmitting">Membuka Shift...</span>
                    </button>
                </div>

                <!-- 2. State: Tutup Shift (Sedang ada shift aktif) -->
                <div x-show="hasActiveShift" class="space-y-4">
                    <!-- Summary Shift Details -->
                    <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-200 space-y-2 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span>Waktu Mulai:</span>
                            <span class="font-bold text-gray-900 font-mono" x-text="shiftData?.start_time"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Modal Awal:</span>
                            <span class="font-bold text-gray-900 font-mono" x-text="formatRupiah(shiftData?.starting_cash)"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Penjualan Tunai:</span>
                            <span class="font-bold text-emerald-600 font-mono" x-text="formatRupiah(shiftData?.cash_sales)"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Penjualan Non-Tunai:</span>
                            <span class="font-bold text-blue-600 font-mono" x-text="formatRupiah(shiftData?.non_cash_sales)"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Total Pesanan:</span>
                            <span class="font-bold text-gray-900 font-mono" x-text="(shiftData?.orders_count || 0) + ' Order'"></span>
                        </div>
                        <div class="flex justify-between text-gray-900 pt-2 border-t border-gray-200 font-extrabold text-sm">
                            <span>Kas Diharapkan di Laci:</span>
                            <span class="text-[#FF6B00] font-mono" x-text="formatRupiah(shiftData?.expected_cash)"></span>
                        </div>
                    </div>

                    <!-- Input Kas Fisik Aktual -->
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">
                            Hitungan Kas Fisik Aktual di Laci (Rp)
                        </label>
                        <input type="number" x-model.number="actualCashInput"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-base font-mono font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#FF6B00]">
                    </div>

                    <!-- Realtime Variance Preview -->
                    <div class="p-3 rounded-2xl border text-xs flex items-center justify-between"
                        :class="(actualCashInput - (shiftData?.expected_cash || 0)) === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ((actualCashInput - (shiftData?.expected_cash || 0)) > 0 ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-rose-50 border-rose-200 text-rose-800')">
                        <span class="font-bold">Estimasi Selisih (Variance):</span>
                        <span class="font-mono font-black text-sm"
                            x-text="(actualCashInput - (shiftData?.expected_cash || 0)) === 0 ? '✓ Seimbang (Rp 0)' : ((actualCashInput - (shiftData?.expected_cash || 0)) > 0 ? '+ ' + formatRupiah(actualCashInput - shiftData?.expected_cash) + ' (Surplus)' : '- ' + formatRupiah(Math.abs(actualCashInput - shiftData?.expected_cash)) + ' (Kurang)')"></span>
                    </div>

                    <!-- Catatan Shift -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan Penutupan (Opsional)</label>
                        <input type="text" x-model="shiftNotesInput" placeholder="Contoh: Saldo klop, uang pecahan 50rb rapi"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#FF6B00]">
                    </div>

                    <!-- Action Button -->
                    <button type="button" @click="closeShift()" :disabled="isShiftSubmitting"
                        class="w-full py-3.5 px-4 rounded-2xl bg-gray-900 hover:bg-black text-white font-bold text-xs tracking-wide shadow-lg transition-all flex items-center justify-center space-x-2">
                        <span x-show="!isShiftSubmitting">Tutup Shift & Rekonsiliasi Kasir</span>
                        <span x-show="isShiftSubmitting">Memproses Penutupan...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: Daftar Pesanan Ditahan (Held / Parked Orders)      -->
        <!-- ======================================================== -->
        <div x-show="showHeldOrdersModal" x-transition
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5" @click.away="showHeldOrdersModal = false">
                <!-- Header Modal -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-[#FF6B00] flex items-center justify-center text-lg">
                            ⏸️
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900">Pesanan Ditahan (Parkir)</h3>
                            <p class="text-[11px] text-gray-400">Panggil kembali pesanan ke keranjang untuk melanjutkan transaksi</p>
                        </div>
                    </div>
                    <button type="button" @click="showHeldOrdersModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>

                <!-- Daftar Held Orders -->
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    <template x-for="ord in heldOrders" :key="ord.id">
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 hover:border-orange-200 transition-all space-y-2">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-extrabold text-sm text-gray-900" x-text="ord.customer_name"></h4>
                                    <div class="flex items-center space-x-2 text-[11px] text-gray-500 font-mono mt-0.5">
                                        <span x-text="'#' + ord.invoice_number"></span>
                                        <span>•</span>
                                        <span x-text="ord.table ? 'Meja ' + ord.table.number : strtoupper(ord.order_type)"></span>
                                    </div>
                                </div>
                                <span class="text-xs font-black font-mono text-[#FF6B00]" x-text="formatRupiah(ord.total_amount)"></span>
                            </div>

                            <!-- List Item Ringkas -->
                            <div class="text-[11px] text-gray-600 line-clamp-1">
                                <template x-for="(it, idx) in ord.items" :key="idx">
                                    <span x-text="it.quantity + 'x ' + it.menu_name + (idx < ord.items.length - 1 ? ', ' : '')"></span>
                                </template>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-gray-200/60">
                                <button type="button" @click="voidHeldOrder(ord.id)"
                                    class="px-3 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-[11px] font-bold transition-colors">
                                    Void / Batal
                                </button>
                                <button type="button" @click="resumeHeldOrder(ord.id)"
                                    class="px-4 py-1.5 rounded-xl bg-[#FF6B00] hover:bg-[#EA580C] text-white text-[11px] font-bold shadow-xs transition-colors flex items-center space-x-1">
                                    <span>Panggil ke Keranjang →</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="heldOrders.length === 0" class="py-10 text-center space-y-2">
                        <span class="text-3xl block">📋</span>
                        <p class="text-xs font-bold text-gray-500">Tidak ada pesanan yang sedang diparkir/ditahan.</p>
                        <p class="text-[11px] text-gray-400">Klik tombol "Parkir" di keranjang pesanan untuk menahan pesanan pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</x-layouts.app>
