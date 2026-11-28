<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('hsn_code', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->with('children')->parents()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $categoryMappings = $this->getCategoryMappings();

        return view('admin.products.create', compact('categories', 'units', 'brands', 'categoryMappings'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        }

        if (! empty($data['brand_id'])) {
            $brand = Brand::find($data['brand_id']);
            if ($brand) {
                $data['brand'] = $brand->name;
            }
        }

        $data['created_by'] = Auth::id();

        $product = Product::create($data);

        $this->auditLog->log(
            action: 'product.created',
            module: 'masters.products',
            entityType: Product::class,
            entityId: $product->id,
            newValues: ['name' => $product->name, 'sku' => $product->sku, 'category_id' => $product->category_id]
        );

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category.parent', 'unit', 'brandRelation', 'creator']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'unit', 'brandRelation']);
        $categories = Category::active()->with('children')->parents()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $categoryMappings = $this->getCategoryMappings();

        return view('admin.products.edit', compact('product', 'categories', 'units', 'brands', 'categoryMappings'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (! empty($data['brand_id'])) {
            $brand = Brand::find($data['brand_id']);
            if ($brand) {
                $data['brand'] = $brand->name;
            }
        } elseif (array_key_exists('brand_id', $data) && is_null($data['brand_id'])) {
            $data['brand'] = null;
        }

        $oldValues = ['name' => $product->name, 'status' => $product->status];
        $product->update($data);

        $this->auditLog->log(
            action: 'product.updated',
            module: 'masters.products',
            entityType: Product::class,
            entityId: $product->id,
            oldValues: $oldValues,
            newValues: ['name' => $product->name, 'status' => $product->status]
        );

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    protected function getCategoryMappings(): array
    {
        $categories = Category::with(['units', 'brands', 'parent.units', 'parent.brands'])->get();
        $mappings = [];

        foreach ($categories as $cat) {
            $units = $cat->units;
            if ($cat->parent && $cat->parent->units->isNotEmpty()) {
                $units = $units->concat($cat->parent->units)->unique('id');
            }

            $brands = $cat->brands;
            if ($cat->parent && $cat->parent->brands->isNotEmpty()) {
                $brands = $brands->concat($cat->parent->brands)->unique('id');
            }

            $mappings[$cat->id] = [
                'units' => $units->values()->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'abbreviation' => $u->abbreviation,
                ])->toArray(),
                'brands' => $brands->values()->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'code' => $b->code,
                ])->toArray(),
            ];
        }

        return $mappings;
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->auditLog->log(
            action: 'product.deleted',
            module: 'masters.products',
            entityType: Product::class,
            entityId: $product->id,
            oldValues: ['name' => $product->name, 'sku' => $product->sku]
        );

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
