<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Livewire\Volt\Volt;

test('guest can add a product to their cart', function () {
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    Volt::test('cart.shopping-cart')
        ->dispatch('add-to-cart', productId: $product->id)
        ->assertDispatched('cart-updated');

    $cart = Cart::where('user_id', null)->first();
    expect($cart)->not->toBeNull();
    expect($cart->items)->toHaveCount(1);
    expect($cart->items->first()->product_id)->toBe($product->id);
    expect((int) $cart->items->first()->price)->toBe(100);
});

test('logged in user can add a product to their cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    $this->actingAs($user);

    Volt::test('cart.shopping-cart')
        ->dispatch('add-to-cart', productId: $product->id)
        ->assertDispatched('cart-updated');

    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart)->not->toBeNull();
    expect($cart->items)->toHaveCount(1);
});

test('adding the same product twice increments quantity', function () {
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    Volt::test('cart.shopping-cart')
        ->dispatch('add-to-cart', productId: $product->id)
        ->dispatch('add-to-cart', productId: $product->id);

    $cart = Cart::where('user_id', null)->first();
    expect($cart->items->first()->quantity)->toBe(2);
});

test('inactive products cannot be added to the cart', function () {
    $product = Product::factory()->create([
        'price' => 100,
        'stock' => 10,
        'is_active' => false,
    ]);

    Volt::test('cart.shopping-cart')
        ->dispatch('add-to-cart', productId: $product->id)
        ->assertNotDispatched('cart-updated');

    expect(Cart::count())->toBe(1);
    expect(Cart::first()->items)->toHaveCount(0);
});

test('out of stock products cannot be added to the cart', function () {
    $product = Product::factory()->create([
        'price' => 100,
        'stock' => 0,
        'is_active' => true,
    ]);

    Volt::test('cart.shopping-cart')
        ->dispatch('add-to-cart', productId: $product->id)
        ->assertNotDispatched('cart-updated');

    expect(Cart::count())->toBe(1);
    expect(Cart::first()->items)->toHaveCount(0);
});
