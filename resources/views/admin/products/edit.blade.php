@extends('layouts.admin', ['title' => 'Edit Global Product', 'header' => 'Master Data: Edit Product'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Global Products
            </a>
            <h1 class="text-xl font-bold text-slate-900">Edit Product: {{ $product->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update catalog specifications and benchmark prices</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Product Title *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                        <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            @foreach($categories as $parent)
                                <optgroup label="{{ $parent->name }}">
                                    <option value="{{ $parent->id }}" {{ old('category_id', $product->category_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} (General)
                                    </option>
                                    @foreach($parent->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
                                            ↳ {{ $child->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Units and brands will load dynamically based on the selected category.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unit of Measure *</label>
                        <select id="unit_id" name="unit_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— Select Category First —</option>
                        </select>
                        <p id="unit-helper" class="text-[11px] text-teal-600 font-medium mt-1">Filtered by selected category.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand / Manufacturer</label>
                        <select id="brand_id" name="brand_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— Select Category First —</option>
                        </select>
                        <p id="brand-helper" class="text-[11px] text-teal-600 font-medium mt-1">Filtered by selected category.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Global Standard SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">HSN / SAC Code</label>
                        <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Benchmark Price (₹)</label>
                        <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Update Global Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryMappings = @json($categoryMappings);
        const allUnits = @json($units);
        const allBrands = @json($brands);
        const initialUnitId = "{{ old('unit_id', $product->unit_id) }}";
        const initialBrandId = "{{ old('brand_id', $product->brand_id ?? '') }}";

        const catSelect = document.getElementById('category_id');
        const unitSelect = document.getElementById('unit_id');
        const brandSelect = document.getElementById('brand_id');
        const unitHelper = document.getElementById('unit-helper');
        const brandHelper = document.getElementById('brand-helper');

        function updateUnitsAndBrands(categoryId, selectedUnitId = null, selectedBrandId = null) {
            if (!categoryId || !categoryMappings[categoryId]) {
                unitSelect.innerHTML = '<option value="">— Select Category First —</option>';
                brandSelect.innerHTML = '<option value="">— Select Category First —</option>';
                if (unitHelper) {
                    unitHelper.textContent = 'Please select a category first.';
                    unitHelper.className = 'text-[11px] text-slate-400 mt-1';
                }
                if (brandHelper) {
                    brandHelper.textContent = 'Please select a category first.';
                    brandHelper.className = 'text-[11px] text-slate-400 mt-1';
                }
                return;
            }

            const mapping = categoryMappings[categoryId];
            const hasMappedUnits = mapping.units && mapping.units.length > 0;
            const hasMappedBrands = mapping.brands && mapping.brands.length > 0;

            const availableUnits = hasMappedUnits ? mapping.units : allUnits;
            const availableBrands = hasMappedBrands ? mapping.brands : allBrands;

            // Populate Units
            let unitHtml = '<option value="">— Select Unit —</option>';
            availableUnits.forEach(function (u) {
                const isSelected = selectedUnitId && Number(selectedUnitId) === Number(u.id);
                unitHtml += `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name} (${u.abbreviation})</option>`;
            });
            unitSelect.innerHTML = unitHtml;

            if (unitHelper) {
                if (hasMappedUnits) {
                    unitHelper.textContent = `✓ ${mapping.units.length} unit(s) mapped to this category`;
                    unitHelper.className = 'text-[11px] text-teal-600 font-medium mt-1';
                } else {
                    unitHelper.textContent = 'No specific units mapped to this category (showing all units)';
                    unitHelper.className = 'text-[11px] text-slate-400 mt-1';
                }
            }

            // Populate Brands
            let brandHtml = '<option value="">— Select Brand (or Generic / Unbranded) —</option>';
            availableBrands.forEach(function (b) {
                const isSelected = selectedBrandId && Number(selectedBrandId) === Number(b.id);
                const codeSuffix = b.code ? ` (${b.code})` : '';
                brandHtml += `<option value="${b.id}" ${isSelected ? 'selected' : ''}>${b.name}${codeSuffix}</option>`;
            });
            brandSelect.innerHTML = brandHtml;

            if (brandHelper) {
                if (hasMappedBrands) {
                    brandHelper.textContent = `✓ ${mapping.brands.length} brand(s) mapped to this category`;
                    brandHelper.className = 'text-[11px] text-teal-600 font-medium mt-1';
                } else {
                    brandHelper.textContent = 'No specific brands mapped to this category (showing all brands)';
                    brandHelper.className = 'text-[11px] text-slate-400 mt-1';
                }
            }
        }

        // Initialize on load with existing product category
        if (catSelect.value) {
            updateUnitsAndBrands(catSelect.value, initialUnitId, initialBrandId);
        } else {
            updateUnitsAndBrands('', null, null);
        }

        // Handle category change
        catSelect.addEventListener('change', function () {
            updateUnitsAndBrands(this.value);
        });
    });
</script>
@endpush
