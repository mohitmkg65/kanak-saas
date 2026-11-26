@extends('layouts.admin', ['title' => 'Edit Category', 'header' => 'Master Data: Edit Category'])

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Categories
            </a>
            <h1 class="text-xl font-bold text-slate-900">Edit Category: {{ $category->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update category hierarchy and master taxonomy details</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Parent Category</label>
                    <select name="parent_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">— None (Top-Level Category) —</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Display Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div> --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
