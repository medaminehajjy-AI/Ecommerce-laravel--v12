<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CartService;
use App\Http\Requests\OrderShowFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class OrderController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show checkout page
     */
    public function create()
    {
        $cartSummary = $this->cartService->getSummary();
        
        if ($cartSummary['is_empty']) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add some items before checking out.');
        }

        $stockIssues = $this->cartService->validateStock();
        
        if (count($stockIssues) > 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart have stock issues. Please update your cart before checking out.');
        }

        return view('checkout.create', compact('cartSummary'));
    }

    /**
     * Process checkout and create order
     */
   /* public function store(Request $request)
    {
        $cartSummary = $this->cartService->getSummary();
        
        if ($cartSummary['is_empty']) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $stockIssues = $this->cartService->validateStock();
        
        if (count($stockIssues) > 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart have stock issues.');
        }

        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $cartSummary['total'],
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($cartSummary['items'] as $cartItem) {
                $product = $cartItem['product'];
                
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                ]);

                // Reduce product stock
                $product->decrement('stock', $cartItem['quantity']);
            }

            // Clear cart
            $this->cartService->clear();

            DB::commit();

            // Here you could send order confirmation email
            // $this->sendOrderConfirmationEmail($order, $request->all());

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Order #' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Checkout failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while processing your order. Please try again.');
        }
    } */

public function store(Request $request)
{
    $cartSummary = $this->cartService->getSummary();

    if ($cartSummary['is_empty']) {
        return response()->json(['error' => 'Cart is empty'], 400);
    }

    /*$request->validate([
        'shipping_address' => 'required|string|max:500',
        'billing_address' => 'required|string|max:500',
        'notes' => 'nullable|string|max:1000',
    ]);*/

    DB::beginTransaction();

    try {

        // 1️⃣ Create order (pending)
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $cartSummary['total'],
            'status' => 'pending',
        ]);
         
        // 2️⃣ Store cart items WITHOUT reducing stock
        foreach ($cartSummary['items'] as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
            ]);
        }

        DB::commit();

        return response()->json([
            'order_id' => $order->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Checkout failed'], 500);
    }
}
public function createPayment(Request $request)
{
    $order = Order::findOrFail($request->order_id);

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $paypalOrder = $provider->createOrder([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => $order->total_price
                ]
            ]
        ]
    ]);

    return response()->json($paypalOrder);
}




public function capturePayment(Request $request)
{
    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->capturePaymentOrder($request->orderID);

    if (isset($response['status']) && $response['status'] == 'COMPLETED') {

        DB::beginTransaction();

        try {

            $order = Order::findOrFail($request->order_id);

            // ✅ Reduce stock now
            foreach ($order->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            // ✅ Mark as paid
            $order->update([
                'status' => 'paid'
            ]);

            // ✅ Clear cart
            $this->cartService->clear();

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Payment capture failed'], 500);
        }
    }

    return response()->json(['error' => 'Payment not completed'], 400);
}












    /**
     * Display user's orders
     */
    public function index()
    {
        $isAdmin = request()->routeIs('admin.orders.*');

        $query = Order::with(['orderItems.product.category', 'user']);

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        if ($isAdmin && request('search')) {
            $search = request('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        if ($isAdmin && request('status')) {
            $query->where('status', request('status'));
        }

        $orders = $query->latest()->paginate(10)->appends(request()->query());

        return view($isAdmin ? 'admin.orders.index' : 'orders.index', compact('orders'));
    }

    /**
     * Display specific order
     */
    public function show(Order $order)
    {
        // Ensure user can only see their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['orderItems.product.category', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Update order status (admin functionality)
     */
    public function update(OrderShowFormRequest $request, Order $order)
    {
        $oldStatus = $order->status;
        $newStatus = $request->input('status');

        if ($oldStatus === $newStatus) {
            return redirect()->back()
                ->with('info', 'Order status is already ' . $newStatus);
        }

        $wasRevenueCountable = in_array($oldStatus, Order::REVENUE_STATUSES);
        $willBeRevenueCountable = in_array($newStatus, Order::REVENUE_STATUSES);

        $order->status = $newStatus;
        $order->save();

        $message = 'Order status updated from ' . $oldStatus . ' to ' . $newStatus;

        if ($wasRevenueCountable && !$willBeRevenueCountable) {
            $message .= '. Revenue adjusted: order removed from revenue.';
        } elseif (!$wasRevenueCountable && $willBeRevenueCountable) {
            $message .= '. Revenue adjusted: order added to revenue.';
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Send order confirmation email (placeholder)
     */
    private function sendOrderConfirmationEmail(Order $order, array $data)
    {
        // Implement email sending logic here
        // You can use Laravel's built-in mail functionality
        // Mail::to($order->user->email)->send(new OrderConfirmationMail($order, $data));
    }
}