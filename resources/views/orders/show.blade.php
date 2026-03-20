@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="text-sm text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-gray-700">Home</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('orders.index') }}" class="hover:text-gray-700">My Orders</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900">Order #{{ $order->id }}</span>
                </nav>
            </div>
        </div>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                <span class="px-3 py-1 text-sm font-medium rounded-full 
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'paid') bg-green-100 text-green-800
                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status == 'shipping') bg-purple-100 text-purple-800
                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                    @elseif($order->status == 'canceled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Order Date</p>
                    <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('F d, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($order->total_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Shipping & Billing Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h2>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-700">{{ $order->shipping_address ?? 'Standard shipping' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h2>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-700">{{ $order->billing_address ?? 'Same as shipping' }}</p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Notes</h2>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-700">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                    
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100 last:border-0">
                                <div class="flex-shrink-0">
                                    @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        {{ $item->product->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">${{ number_format($item->quantity * $item->price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="space-y-3">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>${{ number_format($order->total_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax</span>
                                <span>${{ number_format($order->total_price * 0.08, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>${{ number_format($order->total_price * 1.08, 2) }}</span>
                            </div>
                            <!--Integrating Stripe Payment Gateway-->
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full">Pay Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-block text-blue-600 hover:text-blue-700">
                ← Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection