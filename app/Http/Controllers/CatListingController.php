<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatListingRequest;
use App\Http\Requests\UpdateCatListingRequest;
use App\Models\CatBreed;
use App\Models\CatListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CatListingController extends Controller
{
    public function index(): View
    {
        $listings = CatListing::query()
            ->with(['user', 'breed'])
            ->active()
            ->filter(request()->all())
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $breeds = Cache::rememberForever('cat_breeds', fn () => CatBreed::query()->orderBy('name')->get());

        return view('listings.index', compact('listings', 'breeds'));
    }

    public function create(): View
    {
        $breeds = Cache::rememberForever('cat_breeds', fn () => CatBreed::query()->orderBy('name')->get());

        return view('listings.create', compact('breeds'));
    }

    public function store(StoreCatListingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cat-images', 'supabase');
        }

        $listing = $request->user()->catListings()->create($data);

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Your listing has been created.');
    }

    public function show(CatListing $listing): View
    {
        $listing->increment('views');
        $listing->load(['user', 'breed', 'reviews.user']);

        return view('listings.show', compact('listing'));
    }

    public function edit(CatListing $listing): View
    {
        $this->authorize('update', $listing);

        $breeds = Cache::rememberForever('cat_breeds', fn () => CatBreed::query()->orderBy('name')->get());

        return view('listings.edit', compact('listing', 'breeds'));
    }

    public function update(UpdateCatListingRequest $request, CatListing $listing): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($listing->image) {
                Storage::disk('supabase')->delete($listing->image);
            }
            $data['image'] = $request->file('image')->store('cat-images', 'supabase');
        }

        $listing->update($data);

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Your listing has been updated.');
    }

    public function destroy(CatListing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        $listing->delete();

        return redirect()->route('listings.index')
            ->with('success', 'Your listing has been removed.');
    }
}
