@extends('layouts.admin', ['title' => 'Brands', 'header' => 'Master Data: Brands'])

@section('content')
    <div class="mb-2">
        <h1 class="text-xl font-bold text-slate-900">Brands & Manufacturers</h1>
        <p class="text-xs text-slate-500 mt-0.5">Material manufacturers, producers, and certified brands mapped across categories</p>
    </div>

    <!-- 2-Part Layout: Col-4 (Form) & Col-8 (Table) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Col-4: Input Forms to Add & Edit Brands -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs sticky top-20">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 id="form-title" class="text-base font-bold text-slate-900">
                            {{ $editingBrand ? 'Edit Brand: ' . $editingBrand->name : 'Add Brand' }}
                        </h2>
                        <p id="form-subtitle" class="text-xs text-slate-500 mt-0.5">
                            {{ $editingBrand ? 'Update brand details & mapped categories' : 'Register material manufacturers & suppliers' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.brands.index') }}" id="btn-reset-form"
                        class="{{ $editingBrand ? '' : 'hidden' }} text-xs font-semibold text-teal-600 hover:text-teal-800 transition">
                        + New
                    </a>
                </div>

                <form id="brand-form" action="{{ $editingBrand ? route('admin.brands.update', $editingBrand) : route('admin.brands.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div id="method-container">
                        @if($editingBrand)
                            @method('PUT')
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name *</label>
                        <input type="text" id="brand-name" name="name"
                            value="{{ old('name', $editingBrand?->name) }}"
                            required
                            placeholder="e.g. UltraTech Cement, Tata Tiscon, Asian Paints"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand Code / Short Name</label>
                        <input type="text" id="brand-code" name="code"
                            value="{{ old('code', $editingBrand?->code) }}"
                            placeholder="e.g. ULTRATECH, TATA, ASIAN, KAJARIA"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <p class="text-[11px] text-slate-400 mt-1">Short identifier used in inventory SKUs and quick search.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Website / URL</label>
                        <input type="url" id="brand-website" name="website"
                            value="{{ old('website', $editingBrand?->website) }}"
                            placeholder="https://www.brandwebsite.com"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea id="brand-description" name="description" rows="2"
                            placeholder="Optional notes about manufacturer, distributorship terms, or warranty..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $editingBrand?->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select id="brand-status" name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $editingBrand?->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $editingBrand?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                $selectedCategories = old('category_ids', old('categories', $editingBrand ? $editingBrand->categories->pluck('id')->toArray() : []));
                            @endphp

                            @foreach($categories as $category)
                                <div class="category-group bg-white p-2.5 rounded-lg border border-slate-200/80 shadow-2xs">
                                    <label class="flex items-center gap-2 font-bold text-xs text-slate-800 cursor-pointer">
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                            {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                            class="category-checkbox w-3.5 h-3.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                        <span>{{ $category->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.brands.index') }}" id="btn-cancel-edit"
                            class="{{ $editingBrand ? '' : 'hidden' }} px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" id="btn-submit-brand"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            {{ $editingBrand ? 'Update Brand' : 'Save Brand' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Col-8: Table Structure Data in Rows -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                
                <!-- Filter Bar -->
                <form method="GET" action="{{ route('admin.brands.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brand name, code, description..."
                        class="w-full sm:flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    
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

                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.brands.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
                    @endif

                    <div class="ml-auto text-xs font-semibold text-slate-400 whitespace-nowrap">
                        Total: <span class="text-slate-700 font-bold">{{ $brands->total() }}</span> Brands
                    </div>
                </form>

                <!-- Brands Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-3 py-3">Brand Name</th>
                                <th class="px-3 py-3">Code</th>
                                <th class="px-3 py-3">Mapped Categories</th>
                                <th class="px-3 py-3">Products</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-3 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600" id="brands-table-body">
                            @forelse($brands as $brand)
                                <tr id="brand-row-{{ $brand->public_id }}" class="hover:bg-slate-50/80 transition-colors {{ $editingBrand && $editingBrand->id === $brand->id ? 'bg-teal-50/60' : '' }}">
                                    <td class="px-3 py-4">
                                        <div class="font-bold text-slate-900">{{ $brand->name }}</div>
                                        @if($brand->description)
                                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $brand->description }}</div>
                                        @endif
                                        @if($brand->website)
                                            <a href="{{ $brand->website }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-teal-600 hover:underline flex items-center gap-1 mt-0.5">
                                                <span>{{ parse_url($brand->website, PHP_URL_HOST) ?? $brand->website }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        @if($brand->code)
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-mono font-bold text-xs rounded-lg border border-slate-200">
                                                {{ $brand->code }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        @if($brand->categories->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($brand->categories->take(3) as $category)
                                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[11px] font-semibold rounded-md border border-indigo-100">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($brand->categories->count() > 3)
                                                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md" title="{{ $brand->categories->pluck('name')->join(', ') }}">
                                                        +{{ $brand->categories->count() - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">None assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-xs font-semibold text-slate-700">
                                        {{ $brand->products_count }} 
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $brand->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($brand->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.brands.edit', $brand) }}"
                                                class="edit-brand-btn inline-flex items-center justify-center p-1 text-teal-600 hover:text-teal-800 transition rounded-lg hover:bg-teal-50"
                                                title="Edit Brand"
                                                data-id="{{ $brand->public_id }}"
                                                data-name="{{ $brand->name }}"
                                                data-code="{{ $brand->code ?? '' }}"
                                                data-website="{{ $brand->website ?? '' }}"
                                                data-description="{{ $brand->description ?? '' }}"
                                                data-status="{{ $brand->status }}"
                                                data-categories="{{ json_encode($brand->categories->pluck('id')) }}"
                                                data-action="{{ route('admin.brands.update', $brand) }}"
                                                data-edit-url="{{ route('admin.brands.edit', $brand) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline-flex items-center m-0 p-0" onsubmit="return confirm('Delete brand {{ $brand->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center p-1 text-rose-600 hover:text-rose-800 transition rounded-lg hover:bg-rose-50" title="Delete Brand">
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
                                        No brands registered yet. Use the form on the left to add manufacturers and material brands.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($brands->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $brands->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('brand-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const submitBtn = document.getElementById('btn-submit-brand');
        const cancelBtn = document.getElementById('btn-cancel-edit');
        const resetBtn = document.getElementById('btn-reset-form');
        const methodContainer = document.getElementById('method-container');

        const nameInput = document.getElementById('brand-name');
        const codeInput = document.getElementById('brand-code');
        const websiteInput = document.getElementById('brand-website');
        const descInput = document.getElementById('brand-description');
        const statusSelect = document.getElementById('brand-status');

        const storeAction = "{{ route('admin.brands.store') }}";
        const indexUrl = "{{ route('admin.brands.index') }}";

        // Reset to "Add Brand" mode
        function resetToAddMode(pushState = true) {
            form.action = storeAction;
            methodContainer.innerHTML = '';
            formTitle.textContent = 'Add Brand';
            formSubtitle.textContent = 'Register material manufacturers & suppliers';
            submitBtn.textContent = 'Save Brand';
            submitBtn.className = 'px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition';
            cancelBtn.classList.add('hidden');
            if (resetBtn) resetBtn.classList.add('hidden');

            nameInput.value = '';
            codeInput.value = '';
            websiteInput.value = '';
            descInput.value = '';
            statusSelect.value = 'active';

            // Deselect all category checkboxes
            document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                cb.checked = false;
            });

            // Remove highlighted rows in table
            document.querySelectorAll('#brands-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });

            // Restore index URL in browser without page reload
            if (pushState && window.location.href !== indexUrl) {
                window.history.pushState({}, '', indexUrl);
            }
        }

        // Set to "Edit Brand" mode
        function setEditMode(data, pushState = true) {
            form.action = data.action;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            formTitle.textContent = 'Edit Brand: ' + data.name;
            formSubtitle.textContent = 'Update brand details & mapped categories';
            submitBtn.textContent = 'Update Brand';
            submitBtn.className = 'px-5 py-2.5 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl shadow-xs transition';
            cancelBtn.classList.remove('hidden');
            if (resetBtn) resetBtn.classList.remove('hidden');

            nameInput.value = data.name;
            codeInput.value = data.code || '';
            websiteInput.value = data.website || '';
            descInput.value = data.description || '';
            statusSelect.value = data.status || 'active';

            // Sync category checkboxes
            let assignedCategoryIds = [];
            try {
                assignedCategoryIds = JSON.parse(data.categories || '[]');
            } catch (e) {
                assignedCategoryIds = [];
            }
            assignedCategoryIds = assignedCategoryIds.map(function(id) { return Number(id); });

            document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                cb.checked = assignedCategoryIds.includes(Number(cb.value));
            });

            // Highlight active row in table
            document.querySelectorAll('#brands-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });
            const activeRow = document.getElementById('brand-row-' + data.id);
            if (activeRow) {
                activeRow.classList.add('bg-teal-50/60');
            }

            // Update browser URL to edit URL
            if (pushState && data.editUrl && window.location.href !== data.editUrl) {
                window.history.pushState({ brandId: data.id }, '', data.editUrl);
            }

            // Scroll form into view on mobile devices
            if (window.innerWidth < 1024) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            nameInput.focus();
        }

        // Attach click handlers to all Edit buttons in the table
        document.querySelectorAll('.edit-brand-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                    e.preventDefault();
                    setEditMode({
                        id: this.dataset.id,
                        name: this.dataset.name,
                        code: this.dataset.code,
                        website: this.dataset.website,
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
                const currentEditBtn = document.querySelector(`.edit-brand-btn[data-edit-url="${window.location.href}"]`);
                if (currentEditBtn) {
                    setEditMode({
                        id: currentEditBtn.dataset.id,
                        name: currentEditBtn.dataset.name,
                        code: currentEditBtn.dataset.code,
                        website: currentEditBtn.dataset.website,
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
