<?php

use App\Models\CatListing;
use App\Models\Product;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('homepage featured listings render decimal prices safely', function () {
    collect([247.04225612675927, 58.79678357434193, 199.995])->each(function (
        float $price,
    ): void {
        CatListing::factory()->create([
            'status' => 'active',
            'type' => 'sale',
            'price' => $price,
        ]);
    });

    collect([42.761069288416415, 58.79678357434193, 199.995, 999.9999])->each(
        function (float $price): void {
            Product::factory()->create([
                'is_active' => true,
                'stock' => 10,
                'price' => $price,
            ]);
        },
    );

    $this->get(route('home'))->assertOk();
});
