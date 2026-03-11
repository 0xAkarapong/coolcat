<?php

namespace Tests\Feature\Reviews;

use App\Models\CatListing;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Livewire\Volt\Volt;

test('guest cannot see the review form', function () {
    $product = Product::factory()->create();

    Volt::test('reviews.review-section', ['reviewable' => $product])
        ->assertDontSee('Leave a Review')
        ->assertSee('log in');
});

test('authenticated user can submit a review for a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user);

    Volt::test('reviews.review-section', ['reviewable' => $product])
        ->assertSee('Leave a Review')
        ->set('rating', 4)
        ->set('comment', 'Great product!')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertSee('Review submitted successfully!');

    $review = Review::where('user_id', $user->id)->where('reviewable_id', $product->id)->first();
    expect($review)->not->toBeNull()
        ->and($review->rating)->toBe(4)
        ->and($review->comment)->toBe('Great product!');
});

test('owner cannot review their own product', function () {
    $owner = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner);

    Volt::test('reviews.review-section', ['reviewable' => $product])
        ->assertDontSee('Leave a Review');
});

test('user cannot review the same product twice', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    Review::factory()->create([
        'user_id' => $user->id,
        'reviewable_id' => $product->id,
        'reviewable_type' => Product::class,
    ]);

    $this->actingAs($user);

    Volt::test('reviews.review-section', ['reviewable' => $product])
        ->assertDontSee('Leave a Review');
});

test('authenticated user can submit a review for a cat listing', function () {
    $user = User::factory()->create();
    $listing = CatListing::factory()->create();

    $this->actingAs($user);

    Volt::test('reviews.review-section', ['reviewable' => $listing])
        ->assertSee('Leave a Review')
        ->set('rating', 5)
        ->set('comment', 'Beautiful cat!')
        ->call('submitReview')
        ->assertHasNoErrors();

    $review = Review::where('user_id', $user->id)->where('reviewable_id', $listing->id)->first();
    expect($review)->not->toBeNull()
        ->and($review->rating)->toBe(5);
});
