<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} - Amarta Boga POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-gray-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-soft-lg border border-gray-100 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
        <!-- Left Side: Brand & Visual Banner (Dark Charcoal theme) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-sidebar to-[#1a1c23] p-8 sm:p-10 flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-60 h-60 bg-brand-600/15 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center text-white font-extrabold text-2xl shadow-lg shadow-brand-500/30">
                        A
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white leading-none">Amarta Boga</h1>
                        <p class="text-xs text-brand-300 font-semibold tracking-wider uppercase mt-1">Point of Sale System</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-400 border border-brand-500/30">
                        Resto & Cafe Edition
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-snug">
                        Sistem Kasir & Dapur Cerdas Terintegrasi Real-Time
                    </h2>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Manajemen pesanan meja, pemantauan dapur interaktif (KDS), kalkulasi transaksi otomatis, dan laporan analitik bisnis dalam satu platform.
                    </p>
                </div>
            </div>

            <!-- Features Highlights -->
            <div class="relative z-10 space-y-3 pt-6 border-t border-gray-800/80 text-xs text-gray-300">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold">✓</div>
                    <span>Sinkronisasi pesanan langsung via Laravel Reverb</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold">✓</div>
                    <span>Dukungan cetak thermal 58mm & 80mm</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 rounded-lg bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold">✓</div>
                    <span>Pengurangan stok otomatis berbasis resep menu</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Content slot (Login form) -->
        <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
