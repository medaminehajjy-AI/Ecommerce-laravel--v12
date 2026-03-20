@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div id="cart-content">
<div class="bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Shopping Cart</h1>
        
        @if($cartSummary['is_empty'])
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-12 text-center border border-gray-200 dark:border-gray-600">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <!-- Stock Issues Warning -->
                    @if(count($stockIssues) > 0)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Stock Issues</h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        @foreach($stockIssues as $issue)
                                            <p>• {{ $issue['product_name'] }}: Only {{ $issue['available_stock'] }} available (you have {{ $issue['requested_quantity'] }})</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
                        <div class="px-6 py-4 border-b dark:border-gray-600">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cart Items (<span data-cart-item-count>{{ $cartSummary['item_count'] }}</span>)</h2>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($cartSummary['items'] as $item)
                                <div class="p-6" data-product-id="{{ $item['product_id'] }}">
                                    <div class="flex items-start space-x-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            @if($item['image'])
                                                <img src="{{ Storage::url($item['image']) }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="w-20 h-20 object-cover rounded-lg">
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                                        <a href="{{ route('products.show', $item['product_id']) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                            {{ $item['name'] }}
                                                        </a>
                                                    </h3>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${{ number_format($item['price'], 2) }} each</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($item['subtotal'], 2) }}</p>
                                                </div>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center justify-between mt-3">
                                                <div class="flex items-center space-x-3">
                                                    <button onclick="updateCartItem({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})" 
                                                            class="w-8 h-8 rounded-md border border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-600 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-500 {{ $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <input type="number" 
                                                           value="{{ $item['quantity'] }}" 
                                                           min="1" 
                                                           max="100"
                                                           onchange="updateCartItem({{ $item['product_id'] }}, this.value)"
                                                           class="w-16 text-center border border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-600 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">
                                                    <button onclick="updateCartItem({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})" 
                                                            class="w-8 h-8 rounded-md border border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-600 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-500 {{ $item['quantity'] >= $item['stock'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $item['quantity'] >= $item['stock'] ? 'disabled' : '' }}>
                                                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($item['stock'] <= 5)
                                                            Only {{ $item['stock'] }} left
                                                        @else
                                                            {{ $item['stock'] }} available
                                                        @endif
                                                    </span>
                                                </div>
                                                <button onclick="removeCartItem({{ $item['product_id'] }})" 
                                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 sticky top-4 border border-gray-200 dark:border-gray-600">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Subtotal (<span data-cart-item-count>{{ $cartSummary['item_count'] }}</span> items)</span>
                                <span data-cart-total>${{ number_format($cartSummary['total'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Shipping</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Tax</span>
                                <span>Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="border-t dark:border-gray-600 pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="text-lg font-bold text-blue-600" data-cart-total>${{ number_format($cartSummary['total'], 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <a href="{{ route('checkout') }}" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                Proceed to Checkout
                            </a>
                            <button onclick="clearCart()" class="w-full border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                Clear Cart
                            </button>
                            <a href="{{ route('home') }}" class="block w-full text-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 py-2">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
</div>

<script>
function updateCartItem(productId, quantity) {
    if (quantity < 1) return;
    
    const maxQuantity = 100;
    if (quantity > maxQuantity) {
        return;
    }

    fetch(`/cart/update/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof updateCartUI === 'function') {
                updateCartUI();
            }
            fetchCartTotals();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function removeCartItem(productId) {
    fetch(`/cart/remove/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof updateCartUI === 'function') {
                updateCartUI();
            }
            // Remove the item row directly from DOM
            const itemRow = document.querySelector(`[data-product-id="${productId}"]`);
            if (itemRow) {
                itemRow.remove();
            }
            // Update cart totals by fetching fresh data
            fetchCartTotals();
            // Check if cart is empty
            checkCartEmpty();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function clearCart() {
    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof updateCartUI === 'function') {
                updateCartUI();
            }
            // Clear all cart items from DOM
            const cartItems = document.querySelectorAll('[data-product-id]');
            cartItems.forEach(item => item.remove());
            // Reset totals to zero
            updateCartTotals(0, 0);
            // Show empty cart message
            showEmptyCart();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function fetchCartTotals() {
    fetch('{{ route('cart.summary') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartTotals(data.cart.item_count, data.cart.total);
                if (data.cart.is_empty) {
                    showEmptyCart();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateCartTotals(itemCount, total) {
    // Update item count display
    const itemCountElements = document.querySelectorAll('[data-cart-item-count]');
    itemCountElements.forEach(el => {
        el.textContent = itemCount;
    });
    
    // Update total display
    const totalElements = document.querySelectorAll('[data-cart-total]');
    totalElements.forEach(el => {
        el.textContent = '$' + parseFloat(total).toFixed(2);
    });
}

function checkCartEmpty() {
    const cartItems = document.querySelectorAll('[data-product-id]');
    if (cartItems.length === 0) {
        showEmptyCart();
    }
}

function showEmptyCart() {
    const cartContent = document.getElementById('cart-content');
    if (cartContent) {
        cartContent.innerHTML = `
            <div class="bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Shopping Cart</h1>
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-12 text-center border border-gray-200 dark:border-gray-600">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        `;
    }
}

function refreshCartPage() {
    fetch('{{ route('cart.index') }}')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#cart-content');
            const currentContent = document.querySelector('#cart-content');
            if (newContent && currentContent) {
                currentContent.innerHTML = newContent.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error refreshing cart:', error);
        });
}
</script>
@endsection