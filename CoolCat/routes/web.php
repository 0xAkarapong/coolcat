<?php

use App\Http\Controllers\CatInquiryController;
use App\Http\Controllers\CatListingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Cat Listings — owner actions (auth required, registered first so /create isn't swallowed by {listing})
Route::middleware('auth')->group(function () {
    Route::resource('listings', CatListingController::class)->except(['index', 'show']);

    // Inquiries — shallow nested under listings
    Route::resource('listings.inquiries', CatInquiryController::class)
        ->shallow()
        ->only(['index', 'store', 'show', 'update', 'destroy']);
});

// Cat Listings — public browsing
Route::resource('listings', CatListingController::class)->only(['index', 'show']);

require __DIR__.'/settings.php';
