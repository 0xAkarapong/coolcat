<?php

use App\Http\Controllers\CatInquiryController;
use App\Http\Controllers\CatListingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Models\CatListing;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredListings = CatListing::active()->latest()->take(3)->get();
    $featuredProducts = Product::active()->inStock()->latest()->take(4)->get();

    return view('welcome', compact('featuredListings', 'featuredProducts'));
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();

    // Metrics
    $activeListings = $user->catListings()->active()->count();
    $activeProducts = $user->products()->active()->count();

    // Unread inquiries on user's listings
    $unreadInquiries = \App\Models\CatInquiry::whereHas('listing', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->where('status', 'open')->count();

    // Recent Sales
    $recentSales = \App\Models\Order::with('user', 'items.product')
        ->whereHas('items.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'activeListings',
        'activeProducts',
        'unreadInquiries',
        'recentSales'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// Shop — owner actions (auth required, registered first so /create isn't swallowed by {product})
Route::middleware('auth')->group(function () {
    Route::resource('listings', CatListingController::class)->except(['index', 'show']);
    Route::resource('products', ProductController::class)->except(['index', 'show']);

    // Inquiries — shallow nested under listings
    Route::resource('listings.inquiries', CatInquiryController::class)
        ->shallow()
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // Orders
    Route::resource('orders', OrderController::class)->except(['create', 'edit', 'destroy']);

    // Reviews (Product context)
    Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('products.reviews.store');
    Route::resource('reviews', ReviewController::class)->only(['update', 'destroy']);
});

// Shop — public browsing
Route::resource('listings', CatListingController::class)->only(['index', 'show']);
Route::resource('products', ProductController::class)->only(['index', 'show']);

require __DIR__.'/settings.php';
