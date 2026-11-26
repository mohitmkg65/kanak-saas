@extends('layouts.admin', ['title' => 'Categories & Subcategories', 'header' => 'Master Data: Categories'])

@section('content')
    <div class="mb-2">
        <h1 class="text-xl font-bold text-slate-900">Categories & Subcategories</h1>
        <p class="text-xs text-slate-500 mt-0.5">Hierarchical building material taxonomy (Cement, Steel, Tiles, Paints, etc.)</p>
    </div>

    <!-- 2-Part Layout: Col-4 (Form) & Col-8 (Table) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Col-4: Input Forms to Add & Edit Categories -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs sticky top-20">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 id="form-title" class="text-base font-bold text-slate-900">
                            {{ $editingCategory ? 'Edit Category: ' . $editingCategory->name : 'Add Category' }}
                        </h2>
                        <p id="form-subtitle" class="text-xs text-slate-500 mt-0.5">
                            {{ $editingCategory ? 'Update category details' : 'Define a global material category' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" id="btn-reset-form"
                        class="{{ $editingCategory ? '' : 'hidden' }} text-xs font-semibold text-teal-600 hover:text-teal-800 transition">
                        + New
                    </a>
                </div>

                <form id="category-form"
                    action="{{ $editingCategory ? route('admin.categories.update', $editingCategory) : route('admin.categories.store') }}"
                    method="POST"
                    class="space-y-4">
                    @csrf
                    
                    <div id="method-container">
                        @if($editingCategory)
                            @method('PUT')
                        @endif
                    </div>

                    {{-- <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Parent Category (Optional)</label>
                        <select name="parent_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— None (This will be a Top-Level Category) —</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Leave empty to create a top-level department like Cement, Steel, Paints.</p>
                    </div> --}}

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                        <input type="text" id="category-name" name="name"
                            value="{{ old('name', $editingCategory?->name) }}"
                            required
                            placeholder="e.g. TMT Bars, White Cement, Floor Tiles"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Slug (Optional)</label>
                        <input type="text" id="category-slug" name="slug"
                            value="{{ old('slug', $editingCategory?->slug) }}"
                            placeholder="Leave blank to auto-generate"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea id="category-description" name="description" rows="3"
                            placeholder="Brief description of materials in this category..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $editingCategory?->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Display Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div> --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                            <select id="category-status" name="status"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="active" {{ old('status', $editingCategory?->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $editingCategory?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.categories.index') }}" id="btn-cancel-edit"
                            class="{{ $editingCategory ? '' : 'hidden' }} px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" id="btn-submit-category"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            {{ $editingCategory ? 'Update Category' : 'Save Category' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Col-8: Table Structure Data in Rows -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Search & Filter Bar -->
                <form method="GET" action="{{ route('admin.categories.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category by name..."
                        class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    
                    {{-- <select name="type" class="w-full sm:w-44 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">All Categories</option>
                        <option value="parent" {{ request('type') === 'parent' ? 'selected' : '' }}>Parent Categories Only</option>
                        <option value="subcategory" {{ request('type') === 'subcategory' ? 'selected' : '' }}>Subcategories Only</option>
                    </select> --}}

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
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Category Name</th>
                                {{-- <th class="px-6 py-3.5">Parent Category</th> --}}
                                {{-- <th class="px-6 py-3.5">Subcategories</th> --}}
                                <th class="px-6 py-3.5">Products Count</th>
                                {{-- <th class="px-6 py-3.5">Sort</th> --}}
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="categories-table-body">
                            @forelse($categories as $category)
                                <tr id="category-row-{{ $category->public_id }}"
                                    class="hover:bg-slate-50/80 transition-colors {{ $editingCategory && $editingCategory->id === $category->id ? 'bg-teal-50/60' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 flex items-center gap-2">
                                            @if($category->parent_id)
                                                <span class="text-slate-300">↳</span>
                                            @endif
                                            <span>{{ $category->name }}</span>
                                        </div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $category->slug }}</div>
                                    </td>
                                    {{-- <td class="px-6 py-4 text-xs">
                                        @if($category->parent)
                                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-semibold rounded-lg border border-indigo-100">
                                                {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-100">
                                                Top-Level Parent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                        {{ $category->children_count }} subcategories
                                    </td> --}}
                                    <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                        {{ $category->products_count }} products
                                    </td>
                                    {{-- <td class="px-6 py-4 text-xs font-mono text-slate-500">
                                        {{ $category->sort_order }}
                                    </td> --}}
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $category->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($category->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                                class="edit-category-btn inline-flex items-center justify-center p-1 text-teal-600 hover:text-teal-800 transition rounded-lg hover:bg-teal-50"
                                                title="Edit Category"
                                                data-id="{{ $category->public_id }}"
                                                data-name="{{ $category->name }}"
                                                data-slug="{{ $category->slug }}"
                                                data-description="{{ $category->description ?? '' }}"
                                                data-status="{{ $category->status }}"
                                                data-action="{{ route('admin.categories.update', $category) }}"
                                                data-edit-url="{{ route('admin.categories.edit', $category) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-flex items-center m-0 p-0" onsubmit="return confirm('Are you sure you want to delete category {{ $category->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center p-1 text-rose-600 hover:text-rose-800 transition rounded-lg hover:bg-rose-50" title="Delete Category">
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
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        No categories found. Use the form on the left to add one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const storeAction = "{{ route('admin.categories.store') }}";
        const indexUrl = "{{ route('admin.categories.index') }}";
        const form = document.getElementById('category-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const methodContainer = document.getElementById('method-container');
        const nameInput = document.getElementById('category-name');
        const slugInput = document.getElementById('category-slug');
        const descInput = document.getElementById('category-description');
        const statusSelect = document.getElementById('category-status');
        const submitBtn = document.getElementById('btn-submit-category');
        const cancelBtn = document.getElementById('btn-cancel-edit');
        const resetBtn = document.getElementById('btn-reset-form');

        // Function to reset form to "Add Category" mode
        function resetToAddMode(pushState = true) {
            form.action = storeAction;
            methodContainer.innerHTML = '';
            form.reset();
            formTitle.textContent = 'Add Category';
            formSubtitle.textContent = 'Define a global material category';
            submitBtn.textContent = 'Save Category';
            cancelBtn.classList.add('hidden');
            resetBtn.classList.add('hidden');

            document.querySelectorAll('#categories-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });

            // Update URL back to index URL as requested
            if (pushState && window.location.href !== indexUrl) {
                window.history.pushState({}, '', indexUrl);
            }
        }

        // Function to set form into "Edit Category" mode
        function setEditMode(data, pushState = true) {
            form.action = data.action;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            nameInput.value = data.name || '';
            slugInput.value = data.slug || '';
            descInput.value = data.description || '';
            statusSelect.value = data.status || 'active';
            formTitle.textContent = 'Edit Category: ' + data.name;
            formSubtitle.textContent = 'Update category details';
            submitBtn.textContent = 'Update Category';
            cancelBtn.classList.remove('hidden');
            resetBtn.classList.remove('hidden');

            // Highlight active row in table
            document.querySelectorAll('#categories-table-body tr').forEach(function (tr) {
                tr.classList.remove('bg-teal-50/60');
            });
            const activeRow = document.getElementById('category-row-' + data.id);
            if (activeRow) {
                activeRow.classList.add('bg-teal-50/60');
            }

            // Update browser URL to edit URL as requested
            if (pushState && data.editUrl && window.location.href !== data.editUrl) {
                window.history.pushState({ categoryId: data.id }, '', data.editUrl);
            }

            // Scroll form into view on mobile devices
            if (window.innerWidth < 1024) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            nameInput.focus();
        }

        // Attach click handlers to all Edit buttons in the table
        document.querySelectorAll('.edit-category-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                // If standard left-click without modifier keys, handle on same page with URL change
                if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                    e.preventDefault();
                    setEditMode({
                        id: this.dataset.id,
                        name: this.dataset.name,
                        slug: this.dataset.slug,
                        description: this.dataset.description,
                        status: this.dataset.status,
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

        // Handle browser Back / Forward buttons
        window.addEventListener('popstate', function () {
            if (window.location.href.includes('/edit')) {
                // If navigated forward/back to an edit URL, find the matching button or reload
                const currentEditBtn = document.querySelector(`.edit-category-btn[data-edit-url="${window.location.href}"]`);
                if (currentEditBtn) {
                    setEditMode({
                        id: currentEditBtn.dataset.id,
                        name: currentEditBtn.dataset.name,
                        slug: currentEditBtn.dataset.slug,
                        description: currentEditBtn.dataset.description,
                        status: currentEditBtn.dataset.status,
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
