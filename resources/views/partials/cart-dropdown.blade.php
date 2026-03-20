<!-- Cart Dropdown -->
<div class="relative group">
    <button class="flex items-center text-gray-700 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            {{ $cartSummary['item_count'] }}
        </span>
    </button>
    
    <div class="absolute z-50 right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
        @if($cartSummary['is_empty'])
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-500">Your cart is empty</p>
            </div>
        @else
            <div class="max-h-96 overflow-y-auto">
                @foreach($cartSummary['items']->take(3) as $item)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            @if($item['image'])
                                <img src="{{ Storage::url($item['image']) }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">
                                    <a href="{{ route('products.show', $item['product_id']) }}" class="hover:text-blue-600">
                                        {{ $item['name'] }}
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500">{{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ number_format($item['subtotal'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($cartSummary['items']->count() > 3)
                    <div class="p-3 text-center">
                        <p class="text-sm text-gray-500">+{{ $cartSummary['items']->count() - 3 }} more items</p>
                    </div>
                @endif
            </div>
            
            <!-- Cart Summary -->
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="text-lg font-bold text-gray-900">${{ number_format($cartSummary['total'], 2) }}</span>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('cart.index') }}" 
                       class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition">
                        View Cart
                    </a>
                    <button class="w-full bg-gray-800 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-gray-900 transition">
                        Checkout
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>