<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

test('guest is redirected when reviewing', function () {
    $product = Product::factory()->create();
    $this->post(route('products.reviews.store', $product))->assertRedirect();
});

test('buyer can add a review to a product', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($buyer)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great product!',
        ])
        ->assertRedirect(); // back()

    expect(Review::count())->toBe(1);
    expect($product->reviews()->first()->rating)->toBe(5);
});

test('seller cannot review their own product', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $seller->id]);

    $this->actingAs($seller)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great product!',
        ])
        ->assertSessionHas('error', 'You cannot review your own product.');

    expect(Review::count())->toBe(0);
});

test('buyer cannot review the same product twice', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create();

    // First review
    $product->reviews()->create([
        'user_id' => $buyer->id,
        'rating' => 4,
    ]);

    // Second review
    $this->actingAs($buyer)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great product!',
        ])
        ->assertSessionHas('error', 'You have already reviewed this product.');

    expect(Review::count())->toBe(1);
});

test('author can update their review', function () {
    $author = User::factory()->create();
    $review = Review::factory()->create([
        'user_id' => $author->id,
        'rating' => 4,
    ]);

    $this->actingAs($author)
        ->patch(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => 'Even better now',
        ])
        ->assertRedirect();

    expect($review->fresh()->rating)->toBe(5);
    expect($review->fresh()->comment)->toBe('Even better now');
});

test('author can delete their review', function () {
    $author = User::factory()->create();
    $review = Review::factory()->create([
        'user_id' => $author->id,
    ]);

    $this->actingAs($author)
        ->delete(route('reviews.destroy', $review))
        ->assertRedirect();

    expect(Review::count())->toBe(0);
});

test('other users cannot update someone else\'s review', function () {
    $review = Review::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->patch(route('reviews.update', $review), [
            'rating' => 1,
        ])
        ->assertForbidden();
});
