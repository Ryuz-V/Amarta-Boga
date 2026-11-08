<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk Akun' }} - Amarta Boga POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F4F5F8] text-gray-900 antialiased min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-8">
    <!-- Main Card Container matching Gambar 1 -->
    <div class="w-full max-w-5xl bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.07)] border border-gray-100 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[660px]">
        <!-- Left Banner: Red Food Showcase (Gambar 1) -->
        <div class="lg:col-span-6 bg-gradient-to-b from-[#DC2626] to-[#B91C1C] p-8 sm:p-10 flex flex-col justify-between text-white relative overflow-hidden select-none">
            <!-- Subtle Food Illustrations / Doodle Pattern Background -->
            <div class="absolute inset-0 opacity-[0.08] pointer-events-none" style="background-image: radial-gradient(#ffffff 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>
            
            <!-- Floating Decorative Organic Shapes -->
            <div class="absolute -right-24 -top-24 w-72 h-72 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-20 bottom-32 w-64 h-64 bg-[#FFE500]/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header & Headlines -->
            <div class="relative z-10 space-y-4 pt-2">
                <!-- Pill Badge: Fresh everyday! -->
                <div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#FFE500] text-[#991B1B] font-black text-xs sm:text-sm tracking-wide shadow-sm">
                        Fresh everyday!
                    </span>
                </div>

                <!-- Big Headline -->
                <h2 class="text-3xl sm:text-4xl lg:text-[40px] font-extrabold text-white tracking-tight leading-[1.15]">
                    Premium Ingredients
                </h2>

                <!-- Subtitle -->
                <p class="text-white/85 text-sm sm:text-base font-normal leading-relaxed max-w-md">
                    Farm-fresh, premium ingredients in every dish we serve
                </p>

                <!-- Carousel Dots Indicator (3 lines matching Gambar 1) -->
                <div class="flex items-center space-x-2 pt-2">
                    <span class="w-8 h-1.5 bg-white rounded-full transition-all"></span>
                    <span class="w-2.5 h-1.5 bg-white/40 rounded-full"></span>
                    <span class="w-2.5 h-1.5 bg-white/40 rounded-full"></span>
                </div>
            </div>

            <!-- Bottom Showcase: Yellow Wavy Accent & Artisan Pizza Photo -->
            <div class="relative z-10 mt-8 pt-4 flex flex-col items-center">
                <!-- Wavy Yellow Scallop Decorative Layer -->
                <div class="w-full relative flex items-center justify-center">
                    <div class="absolute -top-6 w-56 h-28 bg-[#FFE500] rounded-t-full opacity-90 blur-sm transform -rotate-2"></div>
                    
                    <!-- Pizza Image with Melted Mozzarella & Hand Pull matching Gambar 1 -->
                    <div class="relative w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-transform duration-300">
                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=700&auto=format&fit=crop&q=80" 
                            alt="Artisan Fresh Pizza" 
                            class="w-full h-56 sm:h-64 object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white text-xs font-bold">
                            <span class="bg-black/40 backdrop-blur-md px-3 py-1 rounded-full">Authentic Stone Baked</span>
                            <span class="text-[#FFE500] font-black">★★★★★ 4.9</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Clean White Login Form Slot (Gambar 1) -->
        <div class="lg:col-span-6 p-8 sm:p-12 lg:p-14 flex flex-col justify-center bg-white">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
