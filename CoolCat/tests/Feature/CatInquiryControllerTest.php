<?php

use App\Models\CatInquiry;
use App\Models\CatListing;
use App\Models\User;

// ── Guest redirects ───────────────────────────────────────────────────────────

test('guest is redirected when submitting an inquiry', function () {
    $listing = CatListing::factory()->create(['status' => 'active']);

    $this->post(route('listings.inquiries.store', $listing))->assertRedirect();
    $this->assertGuest();
});

// ── Store ─────────────────────────────────────────────────────────────────────

test('buyer can submit an inquiry on a listing', function () {
    $buyer = User::factory()->create();
    $listing = CatListing::factory()->create(['status' => 'active']);

    $this->actingAs($buyer)
        ->post(route('listings.inquiries.store', $listing), [
            'message' => 'I am interested in this cat.',
        ])
        ->assertRedirect();

    expect(CatInquiry::query()->where('buyer_id', $buyer->id)->exists())->toBeTrue();
});

test('buyer cannot inquire on their own listing', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);

    $this->actingAs($seller)
        ->post(route('listings.inquiries.store', $listing))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

test('seller can view inquiries for their listing', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);
    CatInquiry::factory()->create(['listing_id' => $listing->id]);

    $this->actingAs($seller)
        ->get(route('listings.inquiries.index', $listing))
        ->assertOk();
});

test('non-owner cannot view inquiries for another listing', function () {
    $listing = CatListing::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('listings.inquiries.index', $listing))
        ->assertForbidden();
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('buyer can view their own inquiry detail', function () {
    $buyer = User::factory()->create();
    $listing = CatListing::factory()->create();
    $inquiry = CatInquiry::factory()->create([
        'listing_id' => $listing->id,
        'buyer_id' => $buyer->id,
    ]);

    $this->actingAs($buyer)
        ->get(route('inquiries.show', $inquiry))
        ->assertOk();
});

test('seller can view an inquiry on their listing', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);
    $inquiry = CatInquiry::factory()->create(['listing_id' => $listing->id]);

    $this->actingAs($seller)
        ->get(route('inquiries.show', $inquiry))
        ->assertOk();
});

test('non-participant cannot view an inquiry', function () {
    $inquiry = CatInquiry::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('inquiries.show', $inquiry))
        ->assertForbidden();
});

// ── Update ────────────────────────────────────────────────────────────────────

test('seller can update inquiry status', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);
    $inquiry = CatInquiry::factory()->create([
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->patch(route('inquiries.update', $inquiry), ['status' => 'confirmed'])
        ->assertRedirect(route('inquiries.show', $inquiry));

    expect($inquiry->fresh()->status)->toBe('confirmed');
});

test('seller can add a seller note when updating', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);
    $inquiry = CatInquiry::factory()->create(['listing_id' => $listing->id]);

    $this->actingAs($seller)
        ->patch(route('inquiries.update', $inquiry), [
            'status' => 'confirmed',
            'seller_note' => 'Looking forward to meeting you!',
        ]);

    expect($inquiry->fresh()->seller_note)->toBe('Looking forward to meeting you!');
});

test('buyer cannot update inquiry status', function () {
    $buyer = User::factory()->create();
    $listing = CatListing::factory()->create();
    $inquiry = CatInquiry::factory()->create([
        'listing_id' => $listing->id,
        'buyer_id' => $buyer->id,
    ]);

    $this->actingAs($buyer)
        ->patch(route('inquiries.update', $inquiry), ['status' => 'confirmed'])
        ->assertForbidden();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('buyer can delete their own inquiry', function () {
    $buyer = User::factory()->create();
    $listing = CatListing::factory()->create();
    $inquiry = CatInquiry::factory()->create([
        'listing_id' => $listing->id,
        'buyer_id' => $buyer->id,
    ]);

    $this->actingAs($buyer)
        ->delete(route('inquiries.destroy', $inquiry))
        ->assertRedirect();

    expect(CatInquiry::find($inquiry->id))->toBeNull();
});

test('seller can delete an inquiry on their listing', function () {
    $seller = User::factory()->create();
    $listing = CatListing::factory()->create(['user_id' => $seller->id]);
    $inquiry = CatInquiry::factory()->create(['listing_id' => $listing->id]);

    $this->actingAs($seller)
        ->delete(route('inquiries.destroy', $inquiry))
        ->assertRedirect();

    expect(CatInquiry::find($inquiry->id))->toBeNull();
});

test('non-participant cannot delete an inquiry', function () {
    $inquiry = CatInquiry::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('inquiries.destroy', $inquiry))
        ->assertForbidden();
});
