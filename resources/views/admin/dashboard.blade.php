@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</h3>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_products'] }}</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Categories</h3>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_categories'] }}</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</h3>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</h3>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Orders</h3>
        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending_orders'] }}</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Products</h3>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['low_stock_products'] }}</p>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
<!-- Dynamic Orders Chart -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Live Orders</h3>
        <div class="flex space-x-2">
            <button onclick="updateDynamicChart('day')" class="period-btn px-3 py-1 text-sm rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300" data-period="day">Daily</button>
            <button onclick="updateDynamicChart('month')" class="period-btn px-3 py-1 text-sm rounded bg-blue-600 text-white" data-period="month">Monthly</button>
        </div>
    </div>
    <div class="p-6">
        <canvas id="dynamicOrdersChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>
    <div class="px-6 pb-4 text-sm text-gray-500 dark:text-gray-400">
        Auto-updates every 10 seconds
    </div>
</div>

<!-- Orders Chart -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Orders Per Month</h3>
    </div>
    <div class="p-6">
        <canvas id="ordersChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
<!-- Revenue Chart -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Per Month</h3>
    </div>
    <div class="p-6">
        <canvas id="revenueChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>
</div>

<!-- Category Distribution Chart -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Products by Category</h3>
    </div>
    <div class="p-6">
        <div class="max-h-80 flex justify-center">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Orders</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recent_orders as $order)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->user->name }} - ${{ number_format($order->total_price, 2) }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($order->status == 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                            @elseif($order->status == 'delivered') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                            @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No recent orders</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Low Stock Products</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($low_stock_products as $product)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                            {{ $product->stock }} left
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No low stock products</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function getChartColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            text: isDark ? '#e5e7eb' : '#374151',
            grid: isDark ? '#374151' : '#e5e7eb',
            textSecondary: isDark ? '#9ca3af' : '#6b7280',
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const colors = getChartColors();
        
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Orders',
                    data: @json($orderCounts),
                    backgroundColor: 'rgba(37, 99, 235, 0.5)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                        labels: { color: colors.text }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: colors.textSecondary },
                        grid: { color: colors.grid },
                        borderColor: colors.grid
                    },
                    x: {
                        ticks: { color: colors.textSecondary },
                        grid: { color: colors.grid },
                        borderColor: colors.grid
                    }
                }
            }
        });

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenueData),
                    fill: true,
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { color: colors.text }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            color: colors.textSecondary
                        },
                        grid: { color: colors.grid },
                        borderColor: colors.grid
                    },
                    x: {
                        ticks: { color: colors.textSecondary },
                        grid: { color: colors.grid },
                        borderColor: colors.grid
                    }
                }
            }
        });

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: @json($categoryNames),
                datasets: [{
                    data: @json($categoryCounts),
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                    ],
                    borderColor: [
                        'rgba(37, 99, 235, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(249, 115, 22, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(14, 165, 233, 1)',
                        'rgba(99, 102, 241, 1)',
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: { color: colors.text }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Dynamic Chart
        let dynamicChart = null;
        let currentPeriod = 'month';
        let updateInterval = null;

        function initDynamicChart() {
            const ctx = document.getElementById('dynamicOrdersChart').getContext('2d');
            dynamicChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Orders',
                        data: [],
                        backgroundColor: 'rgba(37, 99, 235, 0.5)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                            labels: { color: colors.text }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: colors.textSecondary },
                            grid: { color: colors.grid },
                            borderColor: colors.grid
                        },
                        x: {
                            ticks: { color: colors.textSecondary },
                            grid: { color: colors.grid },
                            borderColor: colors.grid
                        }
                    }
                }
            });

            updateDynamicChart('month');
            startAutoUpdate();
        }

        function updateDynamicChart(period) {
            currentPeriod = period;
            
            document.querySelectorAll('.period-btn').forEach(btn => {
                if (btn.dataset.period === period) {
                    btn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'hover:bg-gray-300', 'dark:hover:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    btn.classList.add('bg-blue-600', 'text-white');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'hover:bg-gray-300', 'dark:hover:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
                }
            });

            fetch(`{{ route('admin.chart.orders') }}?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    dynamicChart.data.labels = data.labels;
                    dynamicChart.data.datasets[0].data = data.datasets[0].data;
                    dynamicChart.update();
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        function startAutoUpdate() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            updateInterval = setInterval(() => {
                updateDynamicChart(currentPeriod);
            }, 10000);
        }

        initDynamicChart();
    });
</script>
@endsection