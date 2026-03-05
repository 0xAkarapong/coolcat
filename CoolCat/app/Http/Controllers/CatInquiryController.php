<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatInquiryRequest;
use App\Http\Requests\UpdateCatInquiryRequest;
use App\Models\CatInquiry;
use App\Models\CatListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CatInquiryController extends Controller
{
    /**
     * Seller views all inquiries for their listing.
     */
    public function index(CatListing $listing): View
    {
        $this->authorize('update', $listing);

        $inquiries = $listing->inquiries()
            ->with('buyer')
            ->latest()
            ->paginate(15);

        return view('inquiries.index', compact('listing', 'inquiries'));
    }

    /**
     * Buyer submits a new inquiry on a listing.
     */
    public function store(StoreCatInquiryRequest $request, CatListing $listing): RedirectResponse
    {
        abort_if(
            $listing->user_id === $request->user()->id,
            403,
            'You cannot inquire on your own listing.'
        );

        $inquiry = $listing->inquiries()->create([
            ...$request->validated(),
            'buyer_id' => $request->user()->id,
        ]);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Your inquiry has been submitted.');
    }

    /**
     * Buyer or seller views an inquiry detail.
     */
    public function show(CatInquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        $inquiry->load(['listing.user', 'buyer']);

        return view('inquiries.show', compact('inquiry'));
    }

    /**
     * Seller updates inquiry status / adds a note.
     */
    public function update(UpdateCatInquiryRequest $request, CatInquiry $inquiry): RedirectResponse
    {
        $inquiry->update($request->validated());

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry updated.');
    }

    /**
     * Buyer or seller deletes an inquiry.
     */
    public function destroy(CatInquiry $inquiry): RedirectResponse
    {
        $this->authorize('delete', $inquiry);

        $listingId = $inquiry->listing_id;

        $inquiry->delete();

        return redirect()->route('listings.inquiries.index', $listingId)
            ->with('success', 'Inquiry removed.');
    }
}
