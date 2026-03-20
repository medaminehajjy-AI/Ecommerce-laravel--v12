@extends('admin.layouts.app')

@section('title', 'Order #' . $order->id)
@section('header', 'Order #' . $order->id)

@php
use App\Models\Order;
$allStatuses = Order::VALID_STATUSES;
@endphp

@section('content')
<!-- Breadcrumb -->
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="text-sm text-gray-500">
            <a href="{{ route('admin.orders.index') }}" class="hover:text-gray-700">Orders</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Order #{{ $order->id }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
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
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Customer Information</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-900"><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p class="text-gray-900"><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p class="text-gray-900"><strong>Phone:</strong> {{ $order->user->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Order Information</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-900"><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y g:i A') }}</p>
                            <p class="text-gray-900"><strong>Last Updated:</strong> {{ $order->updated_at->format('F d, Y g:i A') }}</p>
                            <p class="text-gray-900"><strong>Payment Status:</strong> 
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Billing Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping & Billing Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Shipping Address</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $order->shipping_address ?? 'Standard shipping' }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Billing Address</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $order->billing_address ?? 'Same as shipping' }}</p>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Order Notes</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Summary & Status Update -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->total_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax (8%)</span>
                        <span>${{ number_format($order->total_price * 0.08, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>${{ number_format($order->total_price * 1.08, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Order Status</h2>
                
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Order Status
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                                @foreach($allStatuses as $status)
                                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md font-semibold hover:bg-blue-700 transition">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items ({{ $order->orderItems->count() }})</h2>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
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
                            <h4 class="text-sm font-medium text-gray-900">
                                <a href="{{ route('admin.products.edit', $item->product) }}" class="hover:text-blue-600">
                                    {{ $item->product->name }}
                                </a>
                            </h4>
                            <p class="text-sm text-gray-500">{{ $item->product->category->name }}</p>
                            <p class="text-xs text-gray-400">{{ Str::limit($item->product->description, 80) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            <p class="text-sm font-semibold text-gray-900">${{ number_format($item->quantity * $item->price, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Order Summary in Mobile -->
            <div class="mt-6 pt-4 border-t border-gray-200 lg:hidden">
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
                        <span>Tax (8%)</span>
                        <span>${{ number_format($order->total_price * 0.08, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>${{ number_format($order->total_price * 1.08, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection