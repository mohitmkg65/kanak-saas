<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - Kanak SaaS</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 flex items-center justify-center p-4">

    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2"></div>

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-600 text-white font-bold text-2xl shadow-xl shadow-teal-900/30 mb-3 border border-teal-400/30">
                KS
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Kanak SaaS Platform</h1>
            <p class="text-xs text-indigo-200 mt-1 uppercase tracking-widest font-semibold">Building Material Shop SaaS</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl shadow-black/40 border border-slate-100 p-8">
            @yield('content')
        </div>

        <div class="text-center mt-6 text-xs text-indigo-300/60">
            &copy; {{ date('Y') }} Kanak SaaS Inc. All rights reserved.
        </div>
    </div>

    @stack('scripts')
</body>
</html>
