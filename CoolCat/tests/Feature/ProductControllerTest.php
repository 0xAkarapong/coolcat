<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ── Public access ────────────────────────────────────────────────────────────

test('guest can view the products index', function () {
    Product::factory()->count(3)->create(['is_active' => true]);

    $this->get(route('products.index'))->assertOk();
});

test('guest can view an active product', function () {
    $product = Product::factory()->create(['is_active' => true]);

    $this->get(route('products.show', $product))->assertOk();
});

test('guest cannot view an inactive product', function () {
    $product = Product::factory()->create(['is_active' => false]);

    $this->get(route('products.show', $product))->assertForbidden();
});

// ── Guest redirects ──────────────────────────────────────────────────────────

test('guest is redirected when accessing create product', function () {
    $this->get(route('products.create'))->assertRedirect();
});

test('guest is redirected when submitting a product', function () {
    $this->post(route('products.store'))->assertRedirect();
});

// ── Authenticated user — creation ────────────────────────────────────────────

test('authenticated user can access the product create page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('products.create'))
        ->assertOk();
});

test('authenticated user can create a product', function () {
    Storage::fake('supabase');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Premium Cat Food',
            'description' => 'Best food for cats.',
            'category' => 'food',
            'price' => 299.00,
            'stock' => 50,
            'is_active' => true,
            'image' => UploadedFile::fake()->image('food.jpg'),
        ])
        ->assertRedirect();

    $product = Product::where('name', 'Premium Cat Food')->first();
    expect($product)->not->toBeNull()
        ->and($product->user_id)->toBe($user->id)
        ->and($product->image)->not->toBeNull();

    Storage::disk('supabase')->assertExists($product->image);
});

// ── Owner — edit & update ────────────────────────────────────────────────────

test('owner can view the edit page for their product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('products.edit', $product))
        ->assertOk();
});

test('non-owner cannot view the edit page for another user\'s product', function () {
    $product = Product::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('products.edit', $product))
        ->assertForbidden();
});

test('owner can update their product', function () {
    Storage::fake('supabase');
    $user = User::factory()->create();
    $product = Product::factory()->for($user)->create([
        'name' => 'Old Name',
        'image' => 'product-images/old.jpg',
    ]);

    $this->actingAs($user)
        ->put(route('products.update', $product), [
            'name' => 'New Name',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ])
        ->assertRedirect(route('products.show', $product));

    $product->refresh();
    expect($product->name)->toBe('New Name')
        ->and($product->image)->not->toBe('product-images/old.jpg');

    Storage::disk('supabase')->assertExists($product->image);
    Storage::disk('supabase')->assertMissing('product-images/old.jpg');
});

test('non-owner cannot update another user\'s product', function () {
    $product = Product::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('products.update', $product), ['name' => 'Hacked'])
        ->assertForbidden();
});

// ── Owner — delete ────────────────────────────────────────────────────────────

test('owner can delete their product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    expect($product->fresh()->trashed())->toBeTrue();
});

test('non-owner cannot delete another user\'s product', function () {
    $product = Product::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('products.destroy', $product))
        ->assertForbidden();
});
