@extends('layouts.admin', ['title' => 'Units of Measure', 'header' => 'Master Data: Units of Measure'])

@section('content')
    <div class="mb-2">
        <h1 class="text-xl font-bold text-slate-900">Units of Measure (UOM)</h1>
        <p class="text-xs text-slate-500 mt-0.5">Standardized measurement units for building materials mapped across multiple categories</p>
    </div>

    <!-- 2-Part Layout: Col-4 (Form) & Col-8 (Table) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Col-4: Input Forms to Add & Edit Units -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs sticky top-20">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 id="form-title" class="text-base font-bold text-slate-900">
                            {{ $editingUnit ? 'Edit Unit: ' . $editingUnit->name : 'Add Unit of Measure' }}
                        </h2>
                        <p id="form-subtitle" class="text-xs text-slate-500 mt-0.5">
                            {{ $editingUnit ? 'Update unit details & mapped categories' : 'Define standard physical measurement unit' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.units.index') }}" id="btn-reset-form"
                        class="{{ $editingUnit ? '' : 'hidden' }} text-xs font-semibold text-teal-600 hover:text-teal-800 transition">
                        + New
                    </a>
                </div>

                <form id="unit-form" action="{{ $editingUnit ? route('admin.units.update', $editingUnit) : route('admin.units.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div id="method-container">
                        @if($editingUnit)
                            @method('PUT')
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unit Full Name *</label>
                        <input type="text" id="unit-name" name="name"
                            value="{{ old('name', $editingUnit?->name) }}"
                            required
                            placeholder="e.g. Metric Ton, Square Feet, Bag"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Abbreviation / Code *</label>
                        <input type="text" id="unit-abbreviation" name="abbreviation"
                            value="{{ old('abbreviation', $editingUnit?->abbreviation) }}"
                            required
                            placeholder="e.g. MT, Sqft, KG, Bag, Nos"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <p class="text-[11px] text-slate-400 mt-1">Short symbol printed on invoices, orders, and ledger.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea id="unit-description" name="description" rows="2"
                            placeholder="Optional notes about unit conversion or packaging..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $editingUnit?->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select id="unit-status" name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $editingUnit?->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $editingUnit?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Applicable Categories (Multi-select) -->
                    <div class="pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Applicable Categories
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-select-all" class="text-[11px] font-semibold text-teal-600 hover:text-teal-800">
                                    All
                                </button>
                                <span class="text-slate-300">|</span>
                                <button type="button" id="btn-deselect-all" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700">
                                    None
                                </button>
                            </div>
                        </div>

                        <!-- Category Search Filter -->
                        <div class="mb-2">
                            <input type="text" id="category-search" placeholder="Type to filter categories..."
                                class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <!-- Categories Scrollable Checkbox List -->
                        <div class="border border-slate-200 rounded-xl p-3 max-h-56 overflow-y-auto bg-slate-50/50 space-y-3">
                            @php
                                $selectedCategories = old('category_ids', old('categories', $editingUnit ? $editingUnit->categories->pluck('id')->toArray() : []));
                            @endphp

                            @foreach($categories as $parent)
                                <div class="category-group bg-white p-2.5 rounded-lg border border-slate-200/80 shadow-2xs">
                                    <label class="flex items-center gap-2 font-bold text-xs text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="category_ids[]" value="{{ $parent->id }}"
                                            {{ in_array($parent->id, $selectedCategories) ? 'checked' : '' }}
                                            class="category-checkbox w-3.5 h-3.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                        <span>{{ $parent->name }}</span>
                                    </label>

                                    {{-- @if($parent->children->isNotEmpty())
                                        <div class="mt-2 pl-5 grid grid-cols-1 gap-1.5 border-t border-slate-100 pt-1.5">
                                            @foreach($parent->children as $child)
                                                <label class="flex items-center gap-2 text-[11px] text-slate-600 cursor-pointer hover:text-slate-900">
                                                    <input type="checkbox" name="category_ids[]" value="{{ $child->id }}"
                                                        {{ in_array($child->id, $selectedCategories) ? 'checked' : '' }}
                                                        class="category-checkbox w-3.5 h-3.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                                    <span>{{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif --}}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.units.index') }}" id="btn-cancel-edit"
                            class="{{ $editingUnit ? '' : 'hidden' }} px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" id="btn-submit-unit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            {{ $editingUnit ? 'Update Unit' : 'Save Unit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Col-8: Table Structure Data in Rows -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Search & Filter Bar -->
                <form method="GET" action="{{ route('admin.units.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by unit name or abbreviation..." class="w-full sm:w-72 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    
                    <div class="relative w-full sm:w-36">
                        <select name="status" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 appearance-none pr-8">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                            xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
                        </svg>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.units.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3.5">Unit Name</th>
                                <th class="px-2 py-3.5">Code</th>
                                <th class="px-2 py-3.5">Mapped Categories</th>
                                <th class="px-2 py-3.5">Products</th>
                                <th class="px-2 py-3.5">Status</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="units-table-body">
                            @forelse($units as $unit)
                                <tr id="unit-row-{{ $unit->public_id }}" class="hover:bg-slate-50/80 transition-colors {{ $editingUnit && $editingUnit->id === $unit->id ? 'bg-teal-50/60' : '' }}">
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900">{{ $unit->name }}</div>
                                        @if($unit->description)
                                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $unit->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-4">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-mono font-bold text-xs rounded-lg border border-slate-200">
                                            {{ $unit->abbreviation }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4">
                                        @if($unit->categories->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($unit->categories->take(3) as $category)
                                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[11px] font-semibold rounded-md border border-indigo-100">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($unit->categories->count() > 3)
                                                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md" title="{{ $unit->categories->pluck('name')->join(', ') }}">
                                                        +{{ $unit->categories->count() - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">None assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-4 text-xs font-semibold text-slate-700">
                                        {{ $unit->products_count }} 
                                    </td>
                                    <td class="px-2 py-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $unit->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.units.edit', $unit) }}"
                                                class="edit-unit-btn inline-flex items-center justify-center p-1 text-teal-600 hover:text-teal-800 transition rounded-lg hover:bg-teal-50"
                                                title="Edit Unit"
                                                data-id="{{ $unit->public_id }}"
                                                data-name="{{ $unit->name }}"
                                                data-abbreviation="{{ $unit->abbreviation }}"
                                                data-description="{{ $unit->description ?? '' }}"
                                                data-status="{{ $unit->status }}"
                                                data-categories="{{ json_encode($unit->categories->pluck('id')) }}"
                                                data-action="{{ route('admin.units.update', $unit) }}"
                                                data-edit-url="{{ route('admin.units.edit', $unit) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="inline-flex items-center m-0 p-0" onsubmit="return confirm('Delete unit {{ $unit->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center p-1 text-rose-600 hover:text-rose-800 transition rounded-lg hover:bg-rose-50" title="Delete Unit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        No units of measure found. Use the form on the left to add one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($units->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $units->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const storeAction = "{{ route('admin.units.store') }}";
        const indexUrl = "{{ route('admin.units.index') }}";
        const form = document.getElementById('unit-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const methodContainer = document.getElementById('method-container');
        const nameInput = document.getElementById('unit-name');
        const abbrInput = document.getElementById('unit-abbreviation');
        const descInput = document.getElementById('unit-description');
        const statusSelect = document.getElementById('unit-status');
        const submitBtn = document.getElementById('btn-submit-unit');
        const cancelBtn = document.getElementById('btn-cancel-edit');
        const resetBtn = document.getElementById('btn-reset-form');

        // Function to reset form to "Add Unit" mode
        function resetToAddMode(pushState = true) {
            form.action = storeAction;
            methodContainer.innerHTML = '';
            form.reset();
            formTitle.textContent = 'Add Unit of Measure';
            formSubtitle.textContent = 'Define standard physical measurement unit';
            submitBtn.textContent = 'Save Unit';
            cancelBtn.classList.add('hidden');
            resetBtn.classList.add('hidden');

            // Uncheck all category checkboxes
            document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                cb.checked = false;
            });

            document.querySelectorAll('#units-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });

            // Update URL back to index URL as requested
            if (pushState && window.location.href !== indexUrl) {
                window.history.pushState({}, '', indexUrl);
            }
        }

        // Function to set form into "Edit Unit" mode
        function setEditMode(data, pushState = true) {
            form.action = data.action;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            nameInput.value = data.name || '';
            abbrInput.value = data.abbreviation || '';
            descInput.value = data.description || '';
            statusSelect.value = data.status || 'active';
            formTitle.textContent = 'Edit Unit: ' + data.name;
            formSubtitle.textContent = 'Update unit details & mapped categories';
            submitBtn.textContent = 'Update Unit';
            cancelBtn.classList.remove('hidden');
            resetBtn.classList.remove('hidden');

            // Check matching category checkboxes
            let assignedCategoryIds = [];
            try {
                assignedCategoryIds = Array.isArray(data.categories) ? data.categories : JSON.parse(data.categories || '[]');
            } catch (e) {
                assignedCategoryIds = [];
            }
            assignedCategoryIds = assignedCategoryIds.map(function(id) { return Number(id); });

            document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                cb.checked = assignedCategoryIds.includes(Number(cb.value));
            });

            // Highlight active row in table
            document.querySelectorAll('#units-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });
            const activeRow = document.getElementById('unit-row-' + data.id);
            if (activeRow) {
                activeRow.classList.add('bg-teal-50/60');
            }

            // Update browser URL to edit URL as requested
            if (pushState && data.editUrl && window.location.href !== data.editUrl) {
                window.history.pushState({ unitId: data.id }, '', data.editUrl);
            }

            // Scroll form into view on mobile devices
            if (window.innerWidth < 1024) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            nameInput.focus();
        }

        // Attach click handlers to all Edit buttons in the table
        document.querySelectorAll('.edit-unit-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                    e.preventDefault();
                    setEditMode({
                        id: this.dataset.id,
                        name: this.dataset.name,
                        abbreviation: this.dataset.abbreviation,
                        description: this.dataset.description,
                        status: this.dataset.status,
                        categories: this.dataset.categories,
                        action: this.dataset.action,
                        editUrl: this.dataset.editUrl
                    });
                }
            });
        });

        // Cancel button click
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetToAddMode();
            });
        }

        // Reset "+ New" button click
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetToAddMode();
            });
        }

        // Category search filter
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

        // Select All / Deselect All
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

        // Handle browser Back / Forward buttons
        window.addEventListener('popstate', function () {
            if (window.location.href.includes('/edit')) {
                const currentEditBtn = document.querySelector(`.edit-unit-btn[data-edit-url="${window.location.href}"]`);
                if (currentEditBtn) {
                    setEditMode({
                        id: currentEditBtn.dataset.id,
                        name: currentEditBtn.dataset.name,
                        abbreviation: currentEditBtn.dataset.abbreviation,
                        description: currentEditBtn.dataset.description,
                        status: currentEditBtn.dataset.status,
                        categories: currentEditBtn.dataset.categories,
                        action: currentEditBtn.dataset.action,
                        editUrl: currentEditBtn.dataset.editUrl
                    }, false);
                } else {
                    window.location.reload();
                }
            } else {
                resetToAddMode(false);
            }
        });
    });
</script>
@endpush
