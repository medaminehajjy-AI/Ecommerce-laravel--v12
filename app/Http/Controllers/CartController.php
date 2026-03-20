<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cartSummary = $this->cartService->getSummary();
        $stockIssues = $this->cartService->validateStock();

        return view('cart.index', compact('cartSummary', 'stockIssues'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $result = $this->cartService->add($product, $request->quantity);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:100'
        ]);

        $result = $this->cartService->update($productId, $request->quantity);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, int $productId)
    {
        $result = $this->cartService->remove($productId);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $this->cartService->clear();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully.'
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }

    /**
     * Get cart summary (AJAX)
     */
    public function summary()
    {
        $cartSummary = $this->cartService->getSummary();
        
        return response()->json([
            'success' => true,
            'cart' => $cartSummary
        ]);
    }
}
