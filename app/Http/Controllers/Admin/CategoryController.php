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
        $categories = $this->getCategoriesQuery($request);

        $editingCategory = null;
        if ($editId = $request->input('edit')) {
            $editingCategory = Category::where('public_id', $editId)->orWhere('id', $editId)->first();
        }

        return view('admin.categories.index', compact('categories', 'editingCategory'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
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
            newValues: ['name' => $category->name]
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Request $request, Category $category): View
    {
        $categories = $this->getCategoriesQuery($request);
        $editingCategory = $category;

        return view('admin.categories.index', compact('categories', 'editingCategory'));
    }

    protected function getCategoriesQuery(Request $request)
    {
        $query = Category::withCount('products');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
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
