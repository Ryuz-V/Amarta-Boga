@props([
    'title' => 'Amarta Boga POS',
    'subtitle' => '',
    'withHeader' => true,
])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Amarta Boga POS</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF6B00">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="AmartaPOS">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#111215] text-gray-900 antialiased h-screen overflow-hidden flex flex-row select-none">
    <!-- Slim Dark Left Navigation Rail (matching Gambar 2) -->
    <x-sidebar />

    <!-- Main Workspace Container: Dark frame wrapping the white rounded canvas -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-[#111215]">
        @if ($withHeader)
            <!-- Top Header for Standard Admin Views -->
            <div class="pt-3 sm:pt-4 pr-0 sm:pr-3.5 pb-3 sm:pb-3.5 flex-1 flex flex-col min-h-0 bg-[#111215]">
                <div class="flex-1 h-full overflow-hidden flex flex-col bg-[#F8F9FC] rounded-tl-[32px] rounded-tr-[32px] sm:rounded-[32px] shadow-2xl">
                    <x-header :title="$title" :subtitle="$subtitle" />
                    
                    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-[#F8F9FC]">
                        <x-alert />
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            <!-- Full Height POS / KDS Screen (matching Gambar 2) -->
            <div class="pt-3.5 sm:pt-4 pr-0 sm:pr-3.5 pb-0 sm:pb-3.5 flex-1 flex flex-col min-h-0 bg-[#111215]">
                <main class="flex-1 h-full overflow-hidden flex flex-col bg-white rounded-tl-[32px] rounded-tr-[32px] sm:rounded-[32px] shadow-2xl">
                    <x-alert />
                    {{ $slot }}
                </main>
            </div>
        @endif
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch((err) => {
                    console.log('SW registration failed: ', err);
                });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
