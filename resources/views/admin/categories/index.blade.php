@extends('layouts.admin', ['title' => 'Categories & Subcategories', 'header' => 'Master Data: Categories'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Categories & Subcategories</h1>
            <p class="text-xs text-slate-500 mt-0.5">Hierarchical building material taxonomy (Cement, Steel, Tiles, Paints, etc.)</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Add Category
        </a>
    </div>
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

            <select name="status" class="w-full sm:w-36 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                    xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
                </svg>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    @if($category->parent_id)
                                        <span class="text-slate-300">↳</span>
                                    @endif
                                    {{ $category->name }}
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
                            <td class="px-6 py-4 text-right space-x-2">
                                {{-- <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit</a> --}}
                                <button class="">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-teal-600 hover:text-teal-800 transition" title="Edit Category">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                </button>
                                {{-- <span class="text-slate-200">|</span> --}}
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete category {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 transition" title="Delete Category">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No categories found. Click "+ Add Category" to create one.
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
@endsection
