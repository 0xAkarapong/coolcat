<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

test('guest is redirected when ordering', function () {
    $this->post(route('orders.store'))->assertRedirect();
});

test('buyer can place an order successfully', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $seller->id,
        'price' => 100,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($buyer)
        ->post(route('orders.store'), [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '1234567890',
            'shipping_address' => '123 Cat Street',
            'items' => [
                ['id' => $product->id, 'quantity' => 2],
            ],
        ])
        ->assertRedirect(); // Redirects to orders.show

    // Check Order
    $order = Order::first();
    expect($order)->not->toBeNull();
    expect((int) $order->total)->toBe(200);
    expect($order->items)->toHaveCount(1);

    // Check stock reduction
    expect($product->fresh()->stock)->toBe(3);
});

test('buyer cannot purchase their own product', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $seller->id,
        'price' => 100,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($seller)
        ->post(route('orders.store'), [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '1234567890',
            'shipping_address' => '123 Cat Street',
            'items' => [['id' => $product->id, 'quantity' => 1]],
        ])
        ->assertStatus(422); // Abort
});

test('buyer cannot purchase more than available stock', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create([
        'stock' => 2,
        'is_active' => true,
    ]);

    $this->actingAs($buyer)
        ->post(route('orders.store'), [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '1234567890',
            'shipping_address' => '123 Cat Street',
            'items' => [['id' => $product->id, 'quantity' => 5]],
        ])
        ->assertStatus(422); // Abort
});

test('buyer can view their own order', function () {
    $buyer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $buyer->id]);

    $this->actingAs($buyer)
        ->get(route('orders.show', $order))
        ->assertOk();
});

test('seller can view their received order', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $seller->id]);

    $order = Order::factory()->create(['user_id' => $buyer->id]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 10,
    ]);

    $this->actingAs($seller)
        ->get(route('orders.show', $order))
        ->assertOk();
});

test('seller can update order status', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $seller->id]);

    $order = Order::factory()->create(['status' => 'pending']);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 10,
    ]);

    $this->actingAs($seller)
        ->patch(route('orders.update', $order), ['status' => 'shipped'])
        ->assertRedirect(); // back()

    expect($order->fresh()->status)->toBe('shipped');
});

test('buyer cannot update order status', function () {
    $buyer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $buyer->id, 'status' => 'pending']);

    $this->actingAs($buyer)
        ->patch(route('orders.update', $order), ['status' => 'shipped'])
        ->assertForbidden();
});
