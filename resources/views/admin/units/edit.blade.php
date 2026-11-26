@extends('layouts.admin', ['title' => 'Edit Unit of Measure', 'header' => 'Master Data: Edit Unit'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.units.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Units
            </a>
            <h1 class="text-xl font-bold text-slate-900">Edit Unit: {{ $unit->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update unit nomenclature, symbol, and assigned material categories</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('admin.units.update', $unit) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unit Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $unit->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Abbreviation / Code *</label>
                        <input type="text" name="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $unit->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $unit->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $unit->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <p class="text-[11px] text-slate-500">Select which categories use this unit of measure</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btn-select-all" class="text-[11px] font-semibold text-teal-600 hover:text-teal-800">
                                Select All
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" id="btn-deselect-all" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700">
                                Deselect All
                            </button>
                        </div>
                    </div>

                    <!-- Category Search / Filter box -->
                    <div class="mb-3">
                        <input type="text" id="category-search" placeholder="Type to filter categories (e.g. Cement, Steel, Tiles)..."
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <!-- Categories Scrollable Grid -->
                    <div class="border border-slate-200 rounded-xl p-4 max-h-72 overflow-y-auto bg-slate-50/50 space-y-4">
                        @php
                            $selectedCategories = old('category_ids', old('categories', $unit->categories->pluck('id')->toArray()));
                        @endphp

                        @foreach($categories as $parent)
                            <div class="category-group bg-white p-3 rounded-lg border border-slate-200/80 shadow-2xs">
                                <label class="flex items-center gap-2 font-bold text-xs text-slate-800 cursor-pointer">
                                    <input type="checkbox" name="category_ids[]" value="{{ $parent->id }}"
                                        {{ in_array($parent->id, $selectedCategories) ? 'checked' : '' }}
                                        class="category-checkbox w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                    <span>{{ $parent->name }}</span>
                                    <span class="text-[10px] font-normal text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">Parent</span>
                                </label>

                                @if($parent->children->isNotEmpty())
                                    <div class="mt-2.5 pl-6 grid grid-cols-1 sm:grid-cols-2 gap-2 border-t border-slate-100 pt-2">
                                        @foreach($parent->children as $child)
                                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer hover:text-slate-900">
                                                <input type="checkbox" name="category_ids[]" value="{{ $child->id }}"
                                                    {{ in_array($child->id, $selectedCategories) ? 'checked' : '' }}
                                                    class="category-checkbox w-3.5 h-3.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                                <span>{{ $child->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.units.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Update Unit
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
                    if (text.includes(term)) {
                        group.style.display = '';
                    } else {
                        group.style.display = 'none';
                    }
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
