@extends('layouts.auth', ['title' => 'Sign In'])

@section('content')
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900">Sign in to your account</h2>
        <p class="text-xs text-slate-500 mt-1">Enter your credentials to access your dashboard</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-medium">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                placeholder="name@company.com">
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center text-slate-600 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                <span class="ms-2 font-medium">Remember me</span>
            </label>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition-all duration-150 transform active:scale-[0.98]">
            Sign In
        </button>

        <div class="text-center pt-2 text-xs text-slate-500">
            Running a building material shop?
            <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700">Register your business</a>
        </div>
    </form>
@endsection
