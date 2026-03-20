@extends('admin.layouts.app')

@section('title', 'Product Details')
@section('header', 'Product Details')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex space-x-6">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                        <p class="text-gray-500 mt-1">Category: {{ $product->category->name }}</p>
                        <div class="mt-2 flex space-x-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($product->status == 'active') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($product->status) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($product->stock > 10) bg-green-100 text-green-800
                                @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $product->stock }} in stock
                            </span>
                        </div>
                    </div>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Edit
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Product Information</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Name</dt>
                            <dd class="text-sm text-gray-900">{{ $product->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Category</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Price</dt>
                            <dd class="text-sm text-gray-900">${{ number_format($product->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Stock</dt>
                            <dd class="text-sm text-gray-900">{{ $product->stock }} units</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Status</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($product->status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $product->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $product->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($product->orderItems()->with('order.user')->latest()->take(10)->get() as $orderItem)
                                <tr>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="{{ route('admin.orders.show', $orderItem->order) }}" class="text-blue-600 hover:text-blue-900">
                                            #{{ $orderItem->order->id }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $orderItem->order->user->name }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $orderItem->quantity }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($orderItem->price, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $orderItem->order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                        No orders found for this product.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection