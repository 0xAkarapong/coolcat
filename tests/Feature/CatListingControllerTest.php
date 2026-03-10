<?php

use App\Models\CatBreed;
use App\Models\CatListing;
use App\Models\User;

// ── Public access ────────────────────────────────────────────────────────────

test('guest can view the listings index', function () {
    CatListing::factory()->count(3)->create(['status' => 'active']);

    $this->get(route('listings.index'))->assertOk();
});

test('guest can view a single listing', function () {
    $listing = CatListing::factory()->create(['status' => 'active']);

    $this->get(route('listings.show', $listing))->assertOk();
});

test('viewing a listing increments its view counter', function () {
    $listing = CatListing::factory()->create(['status' => 'active', 'views' => 0]);

    $this->get(route('listings.show', $listing));

    expect($listing->fresh()->views)->toBe(1);
});

// ── Guest redirects ──────────────────────────────────────────────────────────

test('guest is redirected when accessing create', function () {
    $this->get(route('listings.create'))->assertRedirect();
    $this->assertGuest();
});

test('guest is redirected when submitting a listing', function () {
    $this->post(route('listings.store'))->assertRedirect();
    $this->assertGuest();
});

// ── Authenticated user — creation ────────────────────────────────────────────

test('authenticated user can access the create page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('listings.create'))
        ->assertOk();
});

test('authenticated user can create a listing', function () {
    $user = User::factory()->create();
    $breed = CatBreed::factory()->create();

    $this->actingAs($user)
        ->post(route('listings.store'), [
            'name' => 'Whiskers',
            'breed_id' => $breed->id,
            'gender' => 'female',
            'type' => 'adoption',
            'is_neutered' => true,
            'is_vaccinated' => false,
        ])
        ->assertRedirect();

    expect(CatListing::query()->where('name', 'Whiskers')->exists())->toBeTrue();
});

// ── Owner — edit & update ────────────────────────────────────────────────────

test('owner can view the edit page for their listing', function () {
    $user = User::factory()->create();
    $listing = CatListing::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('listings.edit', $listing))
        ->assertOk();
});

test('non-owner cannot view the edit page for another user\'s listing', function () {
    $listing = CatListing::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('listings.edit', $listing))
        ->assertForbidden();
});

test('owner can update their listing', function () {
    $user = User::factory()->create();
    $listing = CatListing::factory()->for($user)->create(['name' => 'Old Name']);

    $this->actingAs($user)
        ->put(route('listings.update', $listing), ['name' => 'New Name'])
        ->assertRedirect(route('listings.show', $listing));

    expect($listing->fresh()->name)->toBe('New Name');
});

test('non-owner cannot update another user\'s listing', function () {
    $listing = CatListing::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('listings.update', $listing), ['name' => 'Hacked'])
        ->assertForbidden();
});

// ── Owner — delete ────────────────────────────────────────────────────────────

test('owner can delete their listing', function () {
    $user = User::factory()->create();
    $listing = CatListing::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('listings.destroy', $listing))
        ->assertRedirect(route('listings.index'));

    expect($listing->fresh()->trashed())->toBeTrue();
});

test('non-owner cannot delete another user\'s listing', function () {
    $listing = CatListing::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('listings.destroy', $listing))
        ->assertForbidden();
});
