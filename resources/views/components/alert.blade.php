@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-sm flex items-center justify-between shadow-soft">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">✓</div>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">✕</button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-sm flex items-center justify-between shadow-soft">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold">!</div>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700">✕</button>
    </div>
@endif

@if (session('info'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-5 p-4 rounded-2xl bg-sky-50 border border-sky-200/80 text-sky-800 text-sm flex items-center justify-between shadow-soft">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold">i</div>
            <p class="font-semibold">{{ session('info') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-sky-500 hover:text-sky-700">✕</button>
    </div>
@endif
