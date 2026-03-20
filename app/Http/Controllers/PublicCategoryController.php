<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class PublicCategoryController extends Controller
{
    public function show($slug)
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
}
