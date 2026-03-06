<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Store a newly created review for a product.
     */
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        // Check if user already reviewed this product
        if ($product->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Prevent sellers from reviewing their own products
        if ($product->user_id === $request->user()->id) {
            return back()->with('error', 'You cannot review your own product.');
        }

        $product->reviews()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    /**
     * Update the specified review.
     */
    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $review->update($request->validated());

        return back()->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
