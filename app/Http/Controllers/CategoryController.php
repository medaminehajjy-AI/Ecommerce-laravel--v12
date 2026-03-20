<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories|regex:/^[a-z0-9-]+$/',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function publicShow($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = $category->products()
            ->where('status', 'active')
            ->with('category')
            ->when(request('min_price'), function ($query, $minPrice) {
                return $query->where('price', '>=', $minPrice);
            })
            ->when(request('max_price'), function ($query, $maxPrice) {
                return $query->where('price', '<=', $maxPrice);
            })
            ->when(request('in_stock'), function ($query) {
                return $query->where('stock', '>', 0);
            })
            ->when(request('search'), function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                           ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }

    private function getSortColumn()
    {
        $sort = request('sort', 'newest');
        switch ($sort) {
            case 'name':
            case 'name_desc':
                return 'name';
            case 'price':
            case 'price_desc':
                return 'price';
            default:
                return 'created_at';
        }
    }

    private function getSortDirection()
    {
        $sort = request('sort', 'newest');
        return in_array($sort, ['name_desc', 'price_desc']) ? 'desc' : 'asc';
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id . '|regex:/^[a-z0-9-]+$/',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
