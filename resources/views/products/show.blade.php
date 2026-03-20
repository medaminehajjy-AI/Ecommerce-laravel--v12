@extends('layouts.app')

@section('title', $product->name)

@section('content')
<!-- Breadcrumb -->
<div class="bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-white">{{ $product->name }}</span>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-96 object-cover rounded-lg">
                @else
                    <div class="w-full h-96 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <!-- Stock Status -->
                <div class="mb-4">
                    @if($product->stock > 10)
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            In Stock ({{ $product->stock }} available)
                        </span>
                    @elseif($product->stock > 0 && $product->stock <= 10)
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                            Only {{ $product->stock }} left
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Title and Price -->
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $product->name }}</h1>
                <div class="mb-6">
                    <span class="text-3xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Category: </span>
                    <a href="{{ route('categories.show', $product->category->slug) }}" 
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        {{ $product->category->name }}
                    </a>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $product->description }}</p>
                </div>

                <!-- Add to Cart Form -->
                <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quantity
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="number" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $product->stock }}"
                                   class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->stock }} available</span>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        @if($product->stock > 0)
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                Add to Cart
                            </button>
                        @else
                            <button disabled 
                                    class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 py-3 px-6 rounded-lg font-semibold cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif
                        
                        <button type="button" 
                                id="wishlist-btn"
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Product Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mt-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Product Details</h3>
                <dl class="grid grid-cols-1 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">#PRD-{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Status</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            @if($product->stock > 0)
                                {{ $product->stock }} units available
                            @else
                                Out of Stock
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Added</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->created_at->format('F d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                        <a href="{{ route('products.show', $relatedProduct) }}" class="block">
                            <div class="relative">
                                @if($relatedProduct->image)
                                    <img src="{{ Storage::url($relatedProduct->image) }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-48 object-cover rounded-t-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                @if($relatedProduct->stock <= 5 && $relatedProduct->stock > 0)
                                    <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                        Only {{ $relatedProduct->stock }} left
                                    </span>
                                @elseif($relatedProduct->stock == 0)
                                    <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        Out of Stock
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $relatedProduct->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">{{ Str::limit($relatedProduct->description, 80) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-blue-600">${{ number_format($relatedProduct->price, 2) }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $relatedProduct->category->name }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.getElementById('add-to-cart-form');
    const quantityInput = document.getElementById('quantity');
    const maxQuantity = parseInt('{{ $product->stock }}');

    addToCartForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const quantity = parseInt(quantityInput.value);
        
        if (quantity < 1) {
            showNotification('Please enter a valid quantity.', 'error');
            return;
        }
        
        if (quantity > maxQuantity) {
            showNotification(`Only ${maxQuantity} items available.`, 'error');
            return;
        }
        
        const formData = new FormData(addToCartForm);
        
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartUI();
                // Reset quantity to 1
                quantityInput.value = 1;
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    });

    // Wishlist button functionality
    const wishlistBtn = document.getElementById('wishlist-btn');
    wishlistBtn.addEventListener('click', function() {
        showNotification('Added to wishlist!', 'success');
        this.classList.toggle('text-red-500');
        this.classList.toggle('border-red-500');
    });
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection