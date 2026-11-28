<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $brands = $this->getBrandsQuery($request);
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();

        $editingBrand = null;
        if ($editId = $request->input('edit')) {
            $editingBrand = Brand::with('categories')->where('public_id', $editId)->orWhere('id', $editId)->first();
        }

        return view('admin.brands.index', compact('brands', 'categories', 'editingBrand'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.brands.create', compact('categories'));
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $categoryIds = $request->input('category_ids', $request->input('categories', []));

        $brand = Brand::create($data);

        if (! empty($categoryIds)) {
            $brand->categories()->sync($categoryIds);
        }

        $this->auditLog->log(
            action: 'brand.created',
            module: 'masters.brands',
            entityType: Brand::class,
            entityId: $brand->id,
            newValues: ['name' => $brand->name, 'code' => $brand->code]
        );

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Request $request, Brand $brand): View
    {
        $brand->load('categories');
        $brands = $this->getBrandsQuery($request);
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
        $editingBrand = $brand;

        return view('admin.brands.index', compact('brands', 'categories', 'editingBrand'));
    }

    protected function getBrandsQuery(Request $request)
    {
        $query = Brand::with('categories')->withCount('products');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->orderBy('name')->paginate(20)->withQueryString();
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $oldValues = ['name' => $brand->name, 'code' => $brand->code];
        $brand->update($request->validated());

        $categoryIds = $request->input('category_ids', $request->input('categories', []));
        $brand->categories()->sync($categoryIds ?? []);

        $this->auditLog->log(
            action: 'brand.updated',
            module: 'masters.brands',
            entityType: Brand::class,
            entityId: $brand->id,
            oldValues: $oldValues,
            newValues: ['name' => $brand->name, 'code' => $brand->code]
        );

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Cannot delete a brand that is assigned to products.');
        }

        $this->auditLog->log(
            action: 'brand.deleted',
            module: 'masters.brands',
            entityType: Brand::class,
            entityId: $brand->id,
            oldValues: ['name' => $brand->name]
        );

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
