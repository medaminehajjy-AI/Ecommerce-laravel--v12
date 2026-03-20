@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="bg-[url('/img/shopimage.jpg')] bg-cover bg-center text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Welcome to Our Store</h1>
            <p class="text-xl mb-8">Discover amazing products at great prices</p>
            <a href="{{ route('products.index') }}" class="bg-white text-cyan-600 px-8 py-3 rounded-lg font-semibold hover:text-white hover:bg-cyan-500 hover:shadow-lg hover:shadow-white transition">
                Shop Now
            </a>
        </div>
    </div>
</div> <br>
<!-- Shop Location || I WE'LL CHANGE IT BY API AFTER -->
<div class="flex justify-center items-center">
    <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d32740.88621161475!2d-5.001388543806442!3d34.01694332380474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzTCsDAwJzM2LjgiTiA0wrA1OCc0NS4zIlc!5e0!3m2!1sfr!2sma!4v1770244733499!5m2!1sfr!2sma" width="600" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<!-- Categories Section -->
@if($featuredCategories->count() > 0)
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Shop by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featuredCategories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="group">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 text-center border border-gray-200 dark:border-gray-700">
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                            {{ $category->name }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $category->products()->count() }} products</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Featured Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-600">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div class="relative">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover rounded-t-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-600 rounded-t-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            @if($product->stock <= 5 && $product->stock > 0)
                                <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Only {{ $product->stock }} left
                                </span>
                            @elseif($product->stock == 0)
                                <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-300 text-sm mb-2">{{ Str::limit($product->description, 80) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section class="py-16 bg-gray-100 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Stay Updated</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-8">Subscribe to our newsletter for the latest products and exclusive offers.</p>
            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" 
                       placeholder="Enter your email" 
                       class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</section>
@endsection