@extends('layouts.app')

@section('title', $category->name)

@section('content')
<!-- Category Header -->
<div class="bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-white">{{ $category->name }}</span>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $category->name }}</h1>
        <p class="text-gray-600 dark:text-gray-300">{{ $category->products()->count() }} products in this category</p>
    </div>
</div>

<!-- Filters and Products -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
                
                <!-- Price Filter -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Price Range</h4>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}"
                                   placeholder="Min" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                        </div>
                        <div class="flex items-center">
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}"
                                   placeholder="Max" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Stock Filter -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Availability</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="in_stock" 
                                   value="1" 
                                   {{ request('in_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Stock Only</span>
                        </label>
                    </div>
                </div>

                <!-- Sort By -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Sort By</h4>
                    <select name="sort" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price (Low to High)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                        <option value="newest" {{ (request('sort') == 'newest' || !request('sort')) ? 'selected' : '' }}>Newest First</option>
                    </select>
                </div>

                <button type="submit" form="filter-form" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                    Apply Filters
                </button>

                <a href="{{ route('categories.show', $category->slug) }}" class="block w-full text-center text-blue-600 dark:text-blue-400 text-sm mt-3 hover:text-blue-700 dark:hover:text-blue-300">
                    Clear Filters
                </a>
            </div>
        </aside>

        <!-- Products Grid -->
        <main class="flex-1">
            <form id="filter-form" method="GET" class="hidden"></form>

            <!-- Results Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <p class="text-gray-600 dark:text-gray-300">
                        Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                    </p>
                    <form method="GET" class="hidden lg:block">
                        @foreach(request()->except(['page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $val)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="newest" {{ (request('sort') == 'newest' || !request('sort')) ? 'selected' : '' }}>Newest First</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                            <a href="{{ route('products.show', $product) }}" class="block">
                                <div class="relative">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-48 object-cover rounded-t-lg">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    @if($product->stock <= 5 && $product->stock > 0)
                                        <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                            Only {{ $product->stock }} left
                                        </span>
                                    @elseif($product->stock == 0)
                                        <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">{{ Str::limit($product->description, 100) }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                                        @if($product->stock > 0)
                                            <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                                                Add to Cart
                                            </button>
                                        @else
                                            <button disabled class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-3 py-1 rounded text-sm cursor-not-allowed">
                                                Out of Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No products found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters or browse all products.</p>
                    <a href="{{ route('categories.show', $category->slug) }}" class="mt-4 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        Clear Filters
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update filter form when any filter changes
    const filterInputs = document.querySelectorAll('input[name="min_price"], input[name="max_price"], input[name="in_stock"], select[name="sort"]');
    const filterForm = document.getElementById('filter-form');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update form data
            const formData = new FormData();
            
            // Add current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('page'); // Remove page parameter on filter change
            
            // Add filter values
            filterInputs.forEach(inp => {
                if (inp.type === 'checkbox') {
                    if (inp.checked) {
                        formData.set(inp.name, inp.value);
                    }
                } else if (inp.value) {
                    formData.set(inp.name, inp.value);
                }
            });
            
            // Build new URL
            const newParams = new URLSearchParams(urlParams);
            for (let [key, value] of formData.entries()) {
                newParams.set(key, value);
            }
            
            // Navigate to new URL
            window.location.href = `${window.location.pathname}?${newParams.toString()}`;
        });
    });
});
</script>
@endsection