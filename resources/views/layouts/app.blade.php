<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS Restoran' }} - Amarta Boga</title>
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
<body class="bg-[#111215] text-gray-800 antialiased h-screen overflow-hidden flex flex-row">
    <!-- Dark Sidebar -->
    <x-sidebar />

    <!-- Main Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-[#111215] pt-3 sm:pt-4 pr-0 sm:pr-3.5 pb-3 sm:pb-3.5">
        <div class="flex-1 h-full overflow-hidden flex flex-col bg-[#F8F9FC] rounded-tl-[32px] rounded-tr-[32px] sm:rounded-[32px] shadow-2xl">
            <!-- Top Header -->
            <x-header :title="$title ?? 'Amarta Boga POS'" :subtitle="$subtitle ?? ''" />

            <!-- Flash Alerts & Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-[#F8F9FC]">
                <x-alert />
                {{ $slot }}
            </main>
        </div>
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
