@extends('layouts.admin', ['title' => 'Create Brand', 'header' => 'Master Data: Add Brand'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.brands.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Brands
            </a>
            <h1 class="text-xl font-bold text-slate-900">Add Brand & Manufacturer</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register material manufacturers and map to applicable material categories</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Brand Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. UltraTech Cement, Tata Tiscon, Asian Paints"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand Code / Short Name</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. ULTRATECH, TATA, ASIAN"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Website / URL</label>
                        <input type="url" name="website" value="{{ old('website') }}" placeholder="https://www.brandwebsite.com"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Optional notes about manufacturer, products, or distributorship..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Applicable Categories (Multi-select) -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Applicable Material Categories
                            </label>
                            <p class="text-[11px] text-slate-400 mt-0.5">Select the material categories this brand supplies</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btn-select-all" class="text-xs font-semibold text-teal-600 hover:text-teal-800">
                                Select All
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" id="btn-deselect-all" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
                                Deselect All
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" id="category-search" placeholder="Search categories..."
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 max-h-72 overflow-y-auto bg-slate-50/50 space-y-3">
                        @php
                            $selectedCategories = old('category_ids', old('categories', []));
                        @endphp

                        @foreach($categories as $parent)
                            <div class="category-group bg-white p-3 rounded-lg border border-slate-200/80 shadow-2xs">
                                <label class="flex items-center gap-2 font-bold text-xs text-slate-800 cursor-pointer">
                                    <input type="checkbox" name="category_ids[]" value="{{ $parent->id }}"
                                        {{ in_array($parent->id, $selectedCategories) ? 'checked' : '' }}
                                        class="category-checkbox w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                    <span>{{ $parent->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.brands.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        Save Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('category-search');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('.category-group').forEach(function (group) {
                    const text = group.textContent.toLowerCase();
                    group.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        const selectAllBtn = document.getElementById('btn-select-all');
        const deselectAllBtn = document.getElementById('btn-deselect-all');

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function () {
                document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                    cb.checked = true;
                });
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function () {
                document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                    cb.checked = false;
                });
            });
        }
    });
</script>
@endpush
