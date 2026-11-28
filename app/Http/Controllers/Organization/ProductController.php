<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationProductRequest;
use App\Http\Requests\Organization\UpdateOrganizationProductRequest;
use App\Models\Category;
use App\Models\OrganizationProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Services\AuditLogService;
use App\Services\OrganizationContextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected AuditLogService $auditLog
    ) {}

    /**
     * List organization's products (both catalog-linked and custom).
     */
    public function index(Request $request): View
    {
        $org = $this->context->current();

        $query = OrganizationProduct::forOrganization($org->id)->with(['category', 'unit', 'product']);

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

        if ($request->input('source') === 'catalog') {
            $query->fromCatalog();
        } elseif ($request->input('source') === 'custom') {
            $query->custom();
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('organization.products.index', compact('products', 'org', 'categories'));
    }

    /**
     * Show form to add a product — user picks from global catalog or creates custom.
     */
    public function create(Request $request): View
    {
        $org = $this->context->current();
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        // If adding from catalog, pre-fill from the global product
        $catalogProduct = null;
        if ($productId = $request->input('from_catalog')) {
            $catalogProduct = Product::find($productId);

            // Check if already added
            if ($catalogProduct) {
                $alreadyAdded = OrganizationProduct::where('organization_id', $org->id)
                    ->where('product_id', $catalogProduct->id)
                    ->exists();

                if ($alreadyAdded) {
                    return redirect()->route('organization.products.index')
                        ->with('warning', 'This product is already in your catalog.');
                }
            }
        }

        return view('organization.products.create', compact('org', 'categories', 'units', 'catalogProduct'));
    }

    /**
     * Store a new organization product.
     */
    public function store(StoreOrganizationProductRequest $request): RedirectResponse
    {
        $org = $this->context->current();
        $data = $request->validated();

        $data['organization_id'] = $org->id;
        $data['created_by'] = Auth::id();

        // If linking to catalog, check for duplicates
        if (! empty($data['product_id'])) {
            $exists = OrganizationProduct::where('organization_id', $org->id)
                ->where('product_id', $data['product_id'])
                ->exists();

            if ($exists) {
                return redirect()->route('organization.products.index')
                    ->with('warning', 'This catalog product is already in your product list.');
            }
        }

        $product = OrganizationProduct::create($data);

        $this->auditLog->log(
            action: 'organization_product.created',
            module: 'products',
            entityType: OrganizationProduct::class,
            entityId: $product->id,
            newValues: [
                'name' => $product->name,
                'sku' => $product->sku,
                'source' => $product->isFromCatalog() ? 'catalog' : 'custom',
            ]
        );

        return redirect()->route('organization.products.index')
            ->with('success', 'Product added successfully.');
    }

    /**
     * Show product details.
     */
    public function show(OrganizationProduct $product): View
    {
        $org = $this->context->current();

        if ($product->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This product does not belong to your organization.');
        }

        $product->load(['category', 'unit', 'product', 'creator']);

        return view('organization.products.show', compact('product', 'org'));
    }

    /**
     * Edit organization product.
     */
    public function edit(OrganizationProduct $product): View
    {
        $org = $this->context->current();

        if ($product->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This product does not belong to your organization.');
        }

        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('organization.products.edit', compact('product', 'org', 'categories', 'units'));
    }

    /**
     * Update organization product.
     */
    public function update(UpdateOrganizationProductRequest $request, OrganizationProduct $product): RedirectResponse
    {
        $org = $this->context->current();

        if ($product->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This product does not belong to your organization.');
        }

        $oldValues = ['name' => $product->name, 'selling_price' => $product->selling_price];
        $product->update($request->validated());

        $this->auditLog->log(
            action: 'organization_product.updated',
            module: 'products',
            entityType: OrganizationProduct::class,
            entityId: $product->id,
            oldValues: $oldValues,
            newValues: ['name' => $product->name, 'selling_price' => $product->selling_price]
        );

        return redirect()->route('organization.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete organization product.
     */
    public function destroy(OrganizationProduct $product): RedirectResponse
    {
        $org = $this->context->current();

        if ($product->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This product does not belong to your organization.');
        }

        $this->auditLog->log(
            action: 'organization_product.deleted',
            module: 'products',
            entityType: OrganizationProduct::class,
            entityId: $product->id,
            oldValues: ['name' => $product->name, 'sku' => $product->sku]
        );

        $product->delete();

        return redirect()->route('organization.products.index')
            ->with('success', 'Product removed successfully.');
    }

    /**
     * Browse the global product catalog to add products from it.
     */
    public function catalog(Request $request): View
    {
        $org = $this->context->current();

        $query = Product::active()->with(['category', 'unit']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $catalogProducts = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get IDs already added by this org
        $addedProductIds = OrganizationProduct::where('organization_id', $org->id)
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->toArray();

        $categories = Category::active()->orderBy('name')->get();

        return view('organization.products.catalog', compact(
            'catalogProducts',
            'addedProductIds',
            'org',
            'categories'
        ));
    }

    /**
     * API endpoint to get global product details (for AJAX pre-fill).
     */
    public function catalogProductDetails(Product $product): JsonResponse
    {
        $product->load(['category', 'unit']);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'unit_id' => $product->unit_id,
            'unit_name' => $product->unit?->name,
            'hsn_code' => $product->hsn_code,
            'brand' => $product->brand,
            'description' => $product->description,
            'base_price' => $product->base_price,
        ]);
    }
}
