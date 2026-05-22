<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProductCategory::withCount('products')->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%");
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', (bool) $request->status);
        }

        $categories = $query->paginate(15);

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories'],
            'code' => ['required', 'string', 'max:100', 'unique:product_categories'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ProductCategory::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function edit(ProductCategory $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:product_categories,name,{$category->id}"],
            'code' => ['required', 'string', 'max:100', "unique:product_categories,code,{$category->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with existing products. Delete or move products first.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }
}
