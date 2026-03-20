@extends('admin.layouts.app')

@section('title', 'Category Details')
@section('header', 'Category Details')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                    <p class="text-gray-500 mt-1">Slug: {{ $category->slug }}</p>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Edit
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Category Information</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Name</dt>
                            <dd class="text-sm text-gray-900">{{ $category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Slug</dt>
                            <dd class="text-sm text-gray-900">{{ $category->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $category->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Statistics</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Total Products</dt>
                            <dd class="text-sm text-gray-900">{{ $category->products()->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Active Products</dt>
                            <dd class="text-sm text-gray-900">{{ $category->products()->where('status', 'active')->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Total Stock</dt>
                            <dd class="text-sm text-gray-900">{{ $category->products()->sum('stock') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($category->products()->count() > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Products in this Category</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($category->products()->take(10)->get() as $product)
                                    <tr>
                                        <td class="px-4 py-2 text-sm">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-sm">${{ number_format($product->price, 2) }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $product->stock }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                @if($product->status == 'active') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($category->products()->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.products.index') }}?category_id={{ $category->id }}" class="text-blue-600 hover:text-blue-900">
                                View all {{ $category->products()->count() }} products →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection