<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::revenueCountable()->sum('total_price'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $low_stock_products = Product::where('stock', '<', 10)->latest()->take(5)->get();

        $ordersPerMonth = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $orderCounts = array_fill(0, 12, 0);

        foreach ($ordersPerMonth as $order) {
            $orderCounts[$order->month - 1] = $order->count;
        }

        $revenuePerMonth = Order::revenueCountable()
        ->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as revenue')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $revenueData = array_fill(0, 12, 0);

        foreach ($revenuePerMonth as $revenue) {
            $revenueData[$revenue->month - 1] = $revenue->revenue;
        }

        $productsByCategory = Product::select('categories.name', DB::raw('COUNT(products.id) as count'))
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        $categoryNames = $productsByCategory->pluck('name')->toArray();
        $categoryCounts = $productsByCategory->pluck('count')->toArray();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'low_stock_products', 'months', 'orderCounts', 'revenueData', 'categoryNames', 'categoryCounts'));
    }

    public function chartOrders(Request $request)
    {
        $period = $request->get('period', 'month');

        if ($period === 'day') {
            $orders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $labels = $orders->map(fn($o) => \Carbon\Carbon::parse($o->date)->format('M d'))->toArray();
            $data = $orders->pluck('count')->toArray();
        } else {
            $orders = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $labels = $months;
            $data = array_fill(0, 12, 0);

            foreach ($orders as $order) {
                $data[$order->month - 1] = $order->count;
            }
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(37, 99, 235, 0.5)',
                    'borderColor' => 'rgba(37, 99, 235, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ]
            ]
        ]);
    }

    public function notifications(Request $request)
    {
        $lastCheck = $request->session()->get('last_notification_check', now()->subMinutes(5));

        $newUsers = User::where('created_at', '>', $lastCheck)
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at']);

        $newOrders = Order::with('user')
            ->where('created_at', '>', $lastCheck)
            ->latest()
            ->take(5)
            ->get(['id', 'total_price', 'status', 'user_id', 'created_at'])
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'total' => $order->total_price,
                    'status' => $order->status,
                    'user_name' => $order->user->name ?? 'Guest',
                    'created_at' => $order->created_at->toIso8601String(),
                ];
            });

        $total = $newUsers->count() + $newOrders->count();

        $request->session()->put('last_notification_check', now());

        return response()->json([
            'users' => $newUsers->toArray(),
            'orders' => $newOrders->toArray(),
            'total' => $total
        ]);
    }
}
