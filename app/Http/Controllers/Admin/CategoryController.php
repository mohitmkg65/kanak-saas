<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $query = Category::with('parent')->withCount('children', 'products');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->input('type') === 'parent') {
            $query->parents();
        } elseif ($request->input('type') === 'subcategory') {
            $query->whereNotNull('parent_id');
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::parents()->active()->orderBy('name')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        }

        $data['created_by'] = Auth::id();

        $category = Category::create($data);

        $this->auditLog->log(
            action: 'category.created',
            module: 'masters.categories',
            entityType: Category::class,
            entityId: $category->id,
            newValues: ['name' => $category->name, 'parent_id' => $category->parent_id]
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::parents()->active()->where('id', '!=', $category->id)->orderBy('name')->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $oldValues = ['name' => $category->name, 'status' => $category->status];
        $category->update($data);

        $this->auditLog->log(
            action: 'category.updated',
            module: 'masters.categories',
            entityType: Category::class,
            entityId: $category->id,
            oldValues: $oldValues,
            newValues: ['name' => $category->name, 'status' => $category->status]
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that has subcategories. Please delete or move the subcategories first.');
        }

        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that has products assigned to it.');
        }

        $this->auditLog->log(
            action: 'category.deleted',
            module: 'masters.categories',
            entityType: Category::class,
            entityId: $category->id,
            oldValues: ['name' => $category->name]
        );

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
