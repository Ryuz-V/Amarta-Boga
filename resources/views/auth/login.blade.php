<x-layouts.auth title="Masuk Akun">
    <div x-data="{
        email: '{{ old('email', '') }}',
        password: '',
        showPassword: false,
        setDemo(e, p) {
            this.email = e;
            this.password = p;
        }
    }" class="w-full max-w-md mx-auto">
        <!-- Brand Logo at Top Center (Gambar 1) -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-2">
                <!-- Red Playful Logo Mark -->
                <div class="w-9 h-9 rounded-xl bg-[#DC2626] flex items-center justify-center text-white shadow-md shadow-red-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="text-2xl font-black text-[#DC2626] tracking-tight">Amarta Boga</span>
            </div>

            <!-- Welcome Back Headline & Subtitle -->
            <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight mt-6">
                Welcome back!
            </h1>
            <p class="text-sm text-gray-400 font-medium mt-1.5">
                Please enter your details
            </p>
        </div>

        <!-- Session Status / Errors Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200/60 text-rose-700 text-xs flex items-start space-x-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-bold">Gagal Masuk</p>
                    <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-5 p-3.5 rounded-2xl bg-sky-50 border border-sky-200/60 text-sky-700 text-xs flex items-center space-x-2.5">
                <svg class="w-4 h-4 text-sky-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Login Form matching Gambar 1 -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Email
                </label>
                <input id="email" type="email" name="email" x-model="email" required autofocus
                    placeholder="Enter your email"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] transition-all text-gray-900 placeholder-gray-400">
            </div>

            <!-- Password Field with Toggle Eye Icon (Gambar 1) -->
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required
                        placeholder="Enter your password"
                        class="w-full pl-4 pr-11 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] transition-all text-gray-900 placeholder-gray-400">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                        aria-label="Lihat kata sandi">
                        <!-- Closed eye icon matching Gambar 1 -->
                        <svg x-show="!showPassword" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password Row (Gambar 1) -->
            <div class="flex items-center justify-between pt-1 text-xs">
                <label class="flex items-center space-x-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1"
                        class="w-4 h-4 rounded text-[#DC2626] border-gray-300 focus:ring-[#DC2626] cursor-pointer">
                    <span class="text-gray-600 font-medium">Remember me</span>
                </label>
                <a href="#" class="font-bold text-gray-900 hover:text-[#DC2626] transition-colors" onclick="alert('Untuk keperluan demo, silakan gunakan tombol login instan di bawah ini atau hubungi Administrator.'); return false;">
                    Forgot password?
                </a>
            </div>

            <!-- Log in Button (Gambar 1) -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gray-900 hover:bg-[#DC2626] text-white font-bold text-sm tracking-wide shadow-md transition-all duration-200 transform active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span>Log in</span>
                </button>
            </div>
        </form>

        <!-- Quick 1-Click Demo Accounts -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center mb-3">
                Akses Instan Demo (1-Click Login)
            </p>
            <div class="grid grid-cols-3 gap-2">
                <!-- Admin -->
                <button type="button" @click="setDemo('admin@amartaboga.com', 'password')"
                    class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-red-50 hover:border-red-200 text-left transition-all group">
                    <p class="text-xs font-bold text-gray-900 group-hover:text-[#DC2626]">👑 Admin</p>
                    <p class="text-[10px] text-gray-400 truncate">Semua Akses</p>
                </button>

                <!-- Kasir -->
                <button type="button" @click="setDemo('kasir@amartaboga.com', 'password')"
                    class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-orange-50 hover:border-orange-200 text-left transition-all group">
                    <p class="text-xs font-bold text-gray-900 group-hover:text-brand-600">🛒 Kasir</p>
                    <p class="text-[10px] text-gray-400 truncate">Katalog & POS</p>
                </button>

                <!-- Koki -->
                <button type="button" @click="setDemo('koki@amartaboga.com', 'password')"
                    class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-amber-50 hover:border-amber-200 text-left transition-all group">
                    <p class="text-xs font-bold text-gray-900 group-hover:text-amber-600">👨‍🍳 Koki</p>
                    <p class="text-[10px] text-gray-400 truncate">Kitchen (KDS)</p>
                </button>
            </div>
        </div>
    </div>
</x-layouts.auth>
