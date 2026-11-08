<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amarta Boga POS - Sistem Kasir Restoran Modern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-gray-800 antialiased min-h-screen flex flex-col justify-between">
    <!-- Top Header -->
    <header class="bg-white border-b border-gray-100 shadow-soft sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center text-white font-black text-xl shadow-md shadow-brand-500/20">
                    A
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none">Amarta Boga</h1>
                    <p class="text-xs text-gray-400 font-medium">Smart POS Resto & Bar Modern</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
                    Sistem Siap Digunakan (Production Ready)
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">
        <!-- Hero Banner -->
        <div class="bg-gradient-to-r from-gray-900 via-[#181a20] to-[#20242c] rounded-3xl p-8 sm:p-10 text-white shadow-soft-lg mb-10 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl">
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-500/20 text-brand-400 border border-brand-500/30">
                    Point of Sale Web Restoran
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 tracking-tight leading-tight">
                    Fondasi Sistem Kasir & KDS Real-Time Siap Digunakan
                </h2>
                <p class="text-gray-300 text-sm sm:text-base mt-3 leading-relaxed">
                    Arsitektur backend Laravel 11, WebSockets Reverb, skema database MySQL 8 lengkap, 3 role pengguna, dan katalog 30+ menu fusion siap dihubungkan ke UI Kasir, KDS Koki, dan Admin.
                </p>
            </div>
        </div>

        <!-- 3 Role Credentials Box -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Staff / Kasir Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-soft hover:shadow-soft-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-500 flex items-center justify-center font-bold text-lg">
                        🛒
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-brand-50 text-brand-600 font-bold border border-brand-100">Role: STAFF</span>
                </div>
                <h3 class="font-bold text-gray-900 text-lg">Kasir / Front-End POS</h3>
                <p class="text-xs text-gray-500 mt-1 mb-4">Katalog menu, cart kalkulasi otomatis, multi payment, dan cetak thermal.</p>
                <div class="bg-gray-50 rounded-xl p-3 text-xs font-mono text-gray-600 space-y-1">
                    <p><span class="text-gray-400">Email:</span> kasir@amartaboga.com</p>
                    <p><span class="text-gray-400">Password:</span> password</p>
                </div>
            </div>

            <!-- Koki / KDS Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-soft hover:shadow-soft-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center font-bold text-lg">
                        👨‍🍳
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 font-bold border border-amber-100">Role: KOKI</span>
                </div>
                <h3 class="font-bold text-gray-900 text-lg">Koki / Dapur (KDS)</h3>
                <p class="text-xs text-gray-500 mt-1 mb-4">Layar antrean real-time, indikator waktu tunggu, bunyi bel, dan toggle sold out.</p>
                <div class="bg-gray-50 rounded-xl p-3 text-xs font-mono text-gray-600 space-y-1">
                    <p><span class="text-gray-400">Email:</span> koki@amartaboga.com</p>
                    <p><span class="text-gray-400">Password:</span> password</p>
                </div>
            </div>

            <!-- Admin Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-soft hover:shadow-soft-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-lg">
                        📊
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 font-bold border border-indigo-100">Role: ADMIN</span>
                </div>
                <h3 class="font-bold text-gray-900 text-lg">Manajemen & Laporan</h3>
                <p class="text-xs text-gray-500 mt-1 mb-4">CRUD menu & resep, inventaris HPP otomatis, analitik omzet, dan ekspor laporan.</p>
                <div class="bg-gray-50 rounded-xl p-3 text-xs font-mono text-gray-600 space-y-1">
                    <p><span class="text-gray-400">Email:</span> admin@amartaboga.com</p>
                    <p><span class="text-gray-400">Password:</span> password</p>
                </div>
            </div>
        </div>

        <!-- Database Metrics & Stack Summary -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft mb-10">
            <h3 class="font-black text-gray-900 text-xl mb-6 flex items-center space-x-2">
                <span>⚡ Status Inisialisasi & Verifikasi Skema Database</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Category::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Kategori</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Menu::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Menu Makanan/Minuman</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Ingredient::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Bahan Baku (Resep)</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Table::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Meja Resto</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Supplier::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Supplier</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Order::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Sample Order</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-black text-brand-500">{{ \App\Models\Discount::count() }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Promo & Voucher</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <p>&copy; 2026 Amarta Boga POS. Dikembangkan dengan Laravel 11, Tailwind CSS, & Reverb WebSockets.</p>
    </footer>
</body>
</html>
