<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;

class CartService
{
    protected $session;
    protected $cartKey = 'cart';

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    /**
     * Get all cart items
     */
    public function getCart(): Collection
    {
        $cartItems = collect($this->session->get($this->cartKey, []));
        
        return $cartItems->map(function ($item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                return null;
            }

            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'subtotal' => $product->price * $item['quantity'],
                'image' => $product->image,
                'stock' => $product->stock,
                'slug' => $product->category ? $product->category->slug : null,
                'product' => $product,
            ];
        })->filter()->values();
    }

    /**
     * Add product to cart
     */
    public function add(Product $product, int $quantity = 1): array
    {
        // Validate stock
        if ($product->stock < $quantity) {
            return [
                'success' => false,
                'message' => "Only {$product->stock} items available in stock.",
                'available_stock' => $product->stock
            ];
        }

        $cart = $this->session->get($this->cartKey, []);
        $productId = $product->id;

        // Check if product already exists in cart
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            
            // Validate total quantity against stock
            if ($product->stock < $newQuantity) {
                return [
                    'success' => false,
                    'message' => "Cannot add {$quantity} more items. Only {$product->stock} items available in stock.",
                    'available_stock' => $product->stock,
                    'current_cart_quantity' => $cart[$productId]['quantity']
                ];
            }
            
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'added_at' => now()
            ];
        }

        $this->session->put($this->cartKey, $cart);

        return [
            'success' => true,
            'message' => "{$quantity} × {$product->name} added to cart!"
        ];
    }

    /**
     * Update cart item quantity
     */
    public function update(int $productId, int $quantity): array
    {
        $cart = $this->session->get($this->cartKey, []);
        
        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Item not found in cart.'
            ];
        }

        $product = Product::find($productId);
        
        if (!$product) {
            unset($cart[$productId]);
            $this->session->put($this->cartKey, $cart);
            return [
                'success' => false,
                'message' => 'Product not found.'
            ];
        }

        // Validate stock
        if ($product->stock < $quantity) {
            return [
                'success' => false,
                'message' => "Only {$product->stock} items available in stock.",
                'available_stock' => $product->stock
            ];
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
            $message = 'Item removed from cart.';
        } else {
            $cart[$productId]['quantity'] = $quantity;
            $message = 'Cart updated successfully.';
        }

        $this->session->put($this->cartKey, $cart);

        return [
            'success' => true,
            'message' => $message
        ];
    }

    /**
     * Remove item from cart
     */
    public function remove(int $productId): array
    {
        $cart = $this->session->get($this->cartKey, []);
        
        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Item not found in cart.'
            ];
        }

        unset($cart[$productId]);
        $this->session->put($this->cartKey, $cart);

        return [
            'success' => true,
            'message' => 'Item removed from cart.'
        ];
    }

    /**
     * Clear entire cart
     */
    public function clear(): void
    {
        $this->session->forget($this->cartKey);
    }

    /**
     * Get cart total
     */
    public function getTotal(): float
    {
        return $this->getCart()->sum('subtotal');
    }

    /**
     * Get cart item count
     */
    public function getItemCount(): int
    {
        return $this->getCart()->sum('quantity');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->getCart()->isEmpty();
    }

    /**
     * Get cart summary
     */
    public function getSummary(): array
    {
        $cart = $this->getCart();
        
        return [
            'items' => $cart,
            'total' => $cart->sum('subtotal'),
            'item_count' => $cart->sum('quantity'),
            'is_empty' => $cart->isEmpty()
        ];
    }

    /**
     * Validate cart items against current stock
     */
    public function validateStock(): array
    {
        $cart = $this->getCart();
        $issues = [];

        foreach ($cart as $item) {
            if ($item['stock'] < $item['quantity']) {
                $issues[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'requested_quantity' => $item['quantity'],
                    'available_stock' => $item['stock']
                ];
            }
        }

        return $issues;
    }
}