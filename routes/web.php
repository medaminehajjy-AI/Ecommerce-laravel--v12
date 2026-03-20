<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
//use App\Http\Controllers\PaymentController;

//i added this for stripe payment gateway
/*Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/success', function () {
    return "Payment Successful!";
})->name('success');

Route::get('/cancel', function () {
    return "Payment Cancelled!";
})->name('cancel');
*/
// Public routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/faq', [App\Http\Controllers\HomeController::class, 'faq'])->name('faq');
Route::get('/policy', [App\Http\Controllers\HomeController::class, 'policy'])->name('policy');
Route::get('/termsofservice', [App\Http\Controllers\HomeController::class, 'termsofservice'])->name('termsofservice');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

//Route::post('/products/{product}/edit', [ProductController::class, 'update'])->name('products.update');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{slug}', [App\Http\Controllers\PublicCategoryController::class, 'show'])->name('categories.show');

// Cart routes (public)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update/{productId}', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
});



// Checkout routes (authenticated)
Route::middleware(['auth'])->group(function () {

    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');

    Route::post('/store', [OrderController::class, 'store'])->name('checkout.store');
    Route::post('/create-payment', [OrderController::class, 'createPayment'])->name('checkout.createPayment');
    Route::post('/capture-payment', [OrderController::class, 'capturePayment'])->name('checkout.capturePayment');


    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::resource('products', ProductController::class)->except(['index', 'show', 'adminIndex']);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::get('chart/orders', [AdminController::class, 'chartOrders'])->name('chart.orders');
    Route::get('notifications', [AdminController::class, 'notifications'])->name('notifications');
});

// Authentication routes (Breeze)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')
    ->middleware('auth');
