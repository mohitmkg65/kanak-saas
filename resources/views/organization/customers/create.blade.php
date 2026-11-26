@extends('layouts.organization', ['title' => 'Add Customer', 'header' => 'Customers: Add New Customer'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('organization.customers.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Customers
            </a>
            <h1 class="text-xl font-bold text-slate-900">Add Customer Account</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a retail buyer, contractor, or dealer for your shop</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('organization.customers.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Details -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Primary Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Customer / Contact Person Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rajesh Kumar"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Firm / Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Kumar Builders & Contractors"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Customer Type *</label>
                            <select name="customer_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @foreach($customerTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('customer_type', 'retail') === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN (Optional)</label>
                            <input type="text" name="gstin" value="{{ old('gstin') }}" placeholder="e.g. 27ABCDE1234F1Z5"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. +91 98765 43210"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. rajesh@kumarbuilders.com"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <!-- Financial / Credit Terms -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Credit Terms</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Credit Limit (₹)</label>
                            <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit') }}" placeholder="Leave blank for unlimited or cash-only"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Account Status</label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="blacklisted" {{ old('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted (No Credit)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Billing Address</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Street Address</label>
                            <textarea name="billing_address" rows="2" placeholder="Building, Street, Landmark..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('billing_address') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                                <input type="text" name="billing_city" value="{{ old('billing_city') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                                <input type="text" name="billing_state" value="{{ old('billing_state') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Postal Code</label>
                                <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Country</label>
                                <input type="text" name="billing_country" value="{{ old('billing_country', 'India') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping / Site Delivery Address -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Primary Delivery / Site Address</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Site Delivery Address</label>
                            <textarea name="shipping_address" rows="2" placeholder="Construction site location, plot number, landmark..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('shipping_address') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Postal Code</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Country</label>
                                <input type="text" name="shipping_country" value="{{ old('shipping_country', 'India') }}"
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Internal Notes</label>
                    <textarea name="notes" rows="2" placeholder="Special pricing terms, site delivery instructions..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('organization.customers.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
