<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Panel' }} - Kanak SaaS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50">

    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2"></div>

    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-indigo-950 border-r border-indigo-900/60 z-30">
            <!-- Sidebar Header -->
            <div class="flex items-center h-16 px-6 bg-indigo-950/80 border-b border-indigo-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 text-white font-bold flex items-center justify-center text-sm shadow-md shadow-teal-900/40">
                        SA
                    </div>
                    <div>
                        <span class="text-sm font-bold text-white tracking-wide block leading-tight">Super Admin</span>
                        <span class="text-[10px] uppercase font-semibold tracking-wider text-teal-400">Platform Control</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
                <!-- Dashboard -->
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-900 text-white shadow-sm border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                </div>

                <!-- Global Masters -->
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-indigo-400/70 uppercase tracking-wider mb-2">Global Masters</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Categories
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.units.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            Units of Measure
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Global Products
                        </a>
                    </div>
                </div>

                <!-- Platform -->
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-indigo-400/70 uppercase tracking-wider mb-2">Platform</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.organizations.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.organizations.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Organizations
                        </a>
                        <a href="{{ route('admin.customers.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Customer Directory
                        </a>
                    </div>
                </div>

                <!-- Access Control -->
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-indigo-400/70 uppercase tracking-wider mb-2">Access Control</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            System Users
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Roles & Permissions
                        </a>
                    </div>
                </div>

                <!-- System -->
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-indigo-400/70 uppercase tracking-wider mb-2">System</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Settings
                        </a>
                    </div>
                </div>

                <!-- Account -->
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-indigo-400/70 uppercase tracking-wider mb-2">Account</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.profile.show') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('admin.profile.*') ? 'bg-indigo-900 text-white border border-teal-500/30' : 'text-indigo-200 hover:bg-indigo-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profile
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Mobile Drawer Backdrop -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/80 z-40 hidden md:hidden transition-opacity duration-300"></div>

        <!-- Main Workspace -->
        <div class="md:pl-64 flex-1 flex flex-col min-h-screen">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20 shadow-xs">
                <div class="flex items-center gap-3">
                    <button type="button" data-sidebar-toggle class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                        <span class="text-indigo-900 font-semibold">Super Admin</span>
                        <span>/</span>
                        <span class="text-slate-900 font-semibold">{{ $header ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <!-- User Dropdown & Logout -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-900 text-white font-bold flex items-center justify-center text-sm shadow-xs border border-indigo-700">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] font-medium text-teal-600">Platform Owner</div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-4">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-teal-50 border border-teal-200 text-teal-800 rounded-xl text-sm flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-xs">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
