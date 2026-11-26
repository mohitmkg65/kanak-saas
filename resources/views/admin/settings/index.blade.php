@extends('layouts.admin', ['title' => 'System Settings', 'header' => 'Platform Settings'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-900">Platform Settings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure global platform defaults and support contact information</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Platform Name *</label>
                        <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? 'Kanak Building Material SaaS') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Support Email *</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? 'support@kanaksaas.com') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Support Phone</label>
                        <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '+1 (800) 555-0199') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Default Timezone *</label>
                        <input type="text" name="default_timezone" value="{{ old('default_timezone', $settings['default_timezone'] ?? 'UTC') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Default Currency *</label>
                        <input type="text" name="default_currency" value="{{ old('default_currency', $settings['default_currency'] ?? 'USD') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
