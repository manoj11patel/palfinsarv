<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('company_name', 'like', "%$search%");
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', (bool) $request->status);
        }

        $products = $query->paginate(15);
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:product_categories,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', 'unique:products'],
            'code' => ['required', 'string', 'max:100', 'unique:products'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product): View
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:product_categories,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', "unique:products,name,{$product->id}"],
            'code' => ['required', 'string', 'max:100', "unique:products,code,{$product->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->applications()->exists()) {
            return back()->with('error', 'Cannot delete product with existing applications. Archive instead by disabling it.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}
