@extends('layouts.auth', ['title' => 'Register Organization'])

@section('content')
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900">Register Your Business</h2>
        <p class="text-xs text-slate-500 mt-1">Start managing your building material shop with Kanak SaaS</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-medium">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="organization_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Business / Shop Name *</label>
            <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name') }}" required autofocus
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="e.g. Shree Balaji Building Materials">
        </div>

        <div>
            <label for="owner_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Owner / Contact Name *</label>
            <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="e.g. Ramesh Patel">
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Business Email Address *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="owner@balajibuilding.com">
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="+91 98765 43210">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="city" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">City</label>
                <input type="text" id="city" name="city" value="{{ old('city') }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                    placeholder="Ahmedabad">
            </div>
            <div>
                <label for="state" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">State</label>
                <input type="text" id="state" name="state" value="{{ old('state') }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                    placeholder="Gujarat">
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Password *</label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="At least 8 characters">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                placeholder="Repeat password">
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition-all duration-150 transform active:scale-[0.98]">
            Submit Application
        </button>

        <div class="text-center pt-2 text-xs text-slate-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700">Sign in here</a>
        </div>
    </form>
@endsection
