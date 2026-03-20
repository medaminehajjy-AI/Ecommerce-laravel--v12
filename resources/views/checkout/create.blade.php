@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Checkout</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        @foreach($cartSummary['items'] as $item)
                            <div class="flex items-center space-x-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{ Storage::url($item['image']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>Subtotal ({{ $cartSummary['item_count'] }} items)</span>
                            <span>${{ number_format($cartSummary['total'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>Tax</span>
                            <span>${{ number_format($cartSummary['total'] * 0.08, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span>Total</span>
                            <span>${{ number_format($cartSummary['total'] * 1.08, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="lg:col-span-1">
                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Shipping Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipping Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Shipping Address <span class="text-red-500">*</span>
                                </label>
                                <textarea id="shipping_address" 
                                          name="shipping_address" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500"
                                          required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Billing Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Billing Address <span class="text-red-500">*</span>
                                </label>
                                <textarea id="billing_address" 
                                          name="billing_address" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500"
                                          required>{{ old('billing_address') }}</textarea>
                                @error('billing_address')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Notes (Optional)</h2>
                        
                        <div>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Any special instructions for your order..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Place Order - ${{ number_format($cartSummary['total'] * 1.08, 2) }}
                    </button>
                       </form>
                       <br>
                       
                                <script src="https://www.paypal.com/sdk/js?client-id=AUi91mLUIFdM0B64vzAx6CKjHyBZ7d131iQ-XXOiEDkLgmi2qTDj8aQfkTGnLq6GoV56QnOoyWEDAhhG&currency=USD"></script>

                        <div id="paypal-button-container"></div>

                        <script>
                            let createdOrderId = null;
                             paypal.Buttons({
                                   
                                createOrder: function() {
                                    return fetch('{{ route('checkout.store') }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        createdOrderId = data.order_id;
                                        return fetch('{{ route('checkout.createPayment') }}', {
                                            method: 'POST',
                                            headers: { 
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ order_id: data.order_id })
                                        })
                                        .then(res => res.json())
                                        .then(order => order.id);
                                    });
                                },

                                onApprove: function(data) {
                                    return fetch('{{ route('checkout.capturePayment') }}', {
                                        method: 'POST',
                                        headers: { 
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            orderID: data.orderID,
                                            order_id: createdOrderId,
                                        })
                                    })
                                    .then(() => {
                                        window.location.href = "/orders";
                                    });
                                }

                            }).render('#paypal-button-container');
                        </script>
                        </div>
        </div>
    </div>
</div>
@endsection