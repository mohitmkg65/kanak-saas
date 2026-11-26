@extends('layouts.organization', ['title' => 'Settings', 'header' => 'Organization Settings'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-900">Organization Settings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage business profile information and localization settings for {{ $org->name }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('organization.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Business Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Business Name *</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $org->name) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Legal Name</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name', $org->legal_name) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Business Email</label>
                            <input type="email" name="business_email" value="{{ old('business_email', $org->email) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Business Phone</label>
                            <input type="text" name="business_phone" value="{{ old('business_phone', $org->phone) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Address</label>
                            <input type="text" name="address" value="{{ old('address', $org->address) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">City</label>
                            <input type="text" name="city" value="{{ old('city', $org->city) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">State</label>
                            <input type="text" name="state" value="{{ old('state', $org->state) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Country</label>
                            <input type="text" name="country" value="{{ old('country', $org->country) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $org->postal_code) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Localization</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Currency *</label>
                            <input type="text" name="currency" value="{{ old('currency', $org->currency) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Timezone *</label>
                            <input type="text" name="timezone" value="{{ old('timezone', $org->timezone) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Locale *</label>
                            <input type="text" name="locale" value="{{ old('locale', $org->locale) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
