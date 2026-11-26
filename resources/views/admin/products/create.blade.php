@extends('layouts.admin', ['title' => 'Create Global Product', 'header' => 'Master Data: Add Product'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Global Products
            </a>
            <h1 class="text-xl font-bold text-slate-900">Add Global Product to Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Define standard material specifications that any tenant can adopt</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Product Title *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. UltraTech OPC 53 Grade Cement (50kg)"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                        <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $parent)
                                <optgroup label="{{ $parent->name }}">
                                    <option value="{{ $parent->id }}" {{ old('category_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} (General)
                                    </option>
                                    @foreach($parent->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                            ↳ {{ $child->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Standard Unit of Measure *</label>
                        <select name="unit_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— Select Unit —</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->abbreviation }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name / Manufacturer</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g. UltraTech, TATA Tiscon, Kajaria, Asian Paints"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Global Standard SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. CEM-UT-OPC53"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">HSN / SAC Code</label>
                        <input type="text" name="hsn_code" value="{{ old('hsn_code') }}" placeholder="e.g. 252329 (Cement), 721310 (Steel)"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Benchmark / Base Price (₹)</label>
                        <input type="number" step="0.01" name="base_price" value="{{ old('base_price') }}" placeholder="0.00"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description & Material Specs</label>
                        <textarea name="description" rows="3" placeholder="Technical specifications, grade details, packaging..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catalog Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Available to all tenants)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Save Global Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
