@extends('layouts.admin', ['title' => 'Units of Measure', 'header' => 'Master Data: Units of Measure'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Units of Measure (UOM)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Standardized measurement units for building materials mapped across multiple categories</p>
        </div>
        <a href="{{ route('admin.units.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Add Unit
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.units.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by unit name or abbreviation..."
                class="w-full sm:w-72 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="status" class="w-full sm:w-36 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
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
                        <th class="px-6 py-3.5">Unit Name</th>
                        <th class="px-6 py-3.5">Code</th>
                        <th class="px-6 py-3.5">Mapped Categories</th>
                        <th class="px-6 py-3.5">Products</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($units as $unit)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $unit->name }}</div>
                                @if($unit->description)
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $unit->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-mono font-bold text-xs rounded-lg border border-slate-200">
                                    {{ $unit->abbreviation }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($unit->categories->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 max-w-sm">
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
                                    <span class="text-xs text-slate-400 italic">All / None assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $unit->products_count }} products
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $unit->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.units.edit', $unit) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit</a>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Delete unit {{ $unit->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No units of measure found.
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
@endsection
