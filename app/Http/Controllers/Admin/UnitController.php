<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use App\Models\Category;
use App\Models\Unit;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $units = $this->getUnitsQuery($request);
        $categories = Category::active()->with('children')->parents()->orderBy('sort_order')->orderBy('name')->get();

        $editingUnit = null;
        if ($editId = $request->input('edit')) {
            $editingUnit = Unit::with('categories')->where('public_id', $editId)->orWhere('id', $editId)->first();
        }

        return view('admin.units.index', compact('units', 'categories', 'editingUnit'));
    }

    public function create(): View
    {
        $categories = Category::active()->with('children')->parents()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.units.create', compact('categories'));
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $categoryIds = $request->input('category_ids', $request->input('categories', []));

        $unit = Unit::create($data);

        if (! empty($categoryIds)) {
            $unit->categories()->sync($categoryIds);
        }

        $this->auditLog->log(
            action: 'unit.created',
            module: 'masters.units',
            entityType: Unit::class,
            entityId: $unit->id,
            newValues: ['name' => $unit->name, 'abbreviation' => $unit->abbreviation]
        );

        return redirect()->route('admin.units.index')->with('success', 'Unit of measure created successfully.');
    }

    public function edit(Request $request, Unit $unit): View
    {
        $unit->load('categories');
        $units = $this->getUnitsQuery($request);
        $categories = Category::active()->with('children')->parents()->orderBy('sort_order')->orderBy('name')->get();
        $editingUnit = $unit;

        return view('admin.units.index', compact('units', 'categories', 'editingUnit'));
    }

    protected function getUnitsQuery(Request $request)
    {
        $query = Unit::with('categories')->withCount('products');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('abbreviation', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->orderBy('name')->paginate(20)->withQueryString();
    }

    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $oldValues = ['name' => $unit->name, 'abbreviation' => $unit->abbreviation];
        $unit->update($request->validated());

        $categoryIds = $request->input('category_ids', $request->input('categories', []));
        $unit->categories()->sync($categoryIds ?? []);

        $this->auditLog->log(
            action: 'unit.updated',
            module: 'masters.units',
            entityType: Unit::class,
            entityId: $unit->id,
            oldValues: $oldValues,
            newValues: ['name' => $unit->name, 'abbreviation' => $unit->abbreviation]
        );

        return redirect()->route('admin.units.index')->with('success', 'Unit of measure updated successfully.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->exists()) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Cannot delete a unit that is assigned to products.');
        }

        $this->auditLog->log(
            action: 'unit.deleted',
            module: 'masters.units',
            entityType: Unit::class,
            entityId: $unit->id,
            oldValues: ['name' => $unit->name]
        );

        $unit->delete();

        return redirect()->route('admin.units.index')->with('success', 'Unit deleted successfully.');
    }
}
