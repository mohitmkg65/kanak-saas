@extends('layouts.admin', ['title' => 'My Profile', 'header' => 'Profile Management'])

@section('content')
    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Profile Info Form -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <h2 class="text-base font-bold text-slate-900 mb-1">Profile Details</h2>
            <p class="text-xs text-slate-500 mb-6">Update your personal account details</p>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Update Profile</button>
                </div>
            </form>
        </div>

        <!-- Password Change Form -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <h2 class="text-base font-bold text-slate-900 mb-1">Change Password</h2>
            <p class="text-xs text-slate-500 mb-6">Ensure your account is using a long, random password to stay secure</p>

            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Current Password *</label>
                    <input type="password" name="current_password" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">New Password *</label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl shadow-md">Change Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection
