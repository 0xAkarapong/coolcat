<x-layouts::app :title="__('Cat Listings')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Cat Listings</flux:heading>
            @auth
                <flux:button variant="primary" href="{{ route('listings.create') }}" icon="plus">
                    Post a Listing
                </flux:button>
            @endauth
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('listings.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <flux:input
                name="search"
                placeholder="Search by name or description..."
                value="{{ request('search') }}"
                icon="magnifying-glass"
            />

            <flux:select name="breed_id" placeholder="All breeds">
                <flux:select.option value="">All Breeds</flux:select.option>
                @foreach ($breeds as $breed)
                    <flux:select.option value="{{ $breed->id }}" :selected="request('breed_id') == $breed->id">
                        {{ $breed->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select name="type" placeholder="Type">
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="adoption" :selected="request('type') === 'adoption'">Adoption</flux:select.option>
                <flux:select.option value="sale" :selected="request('type') === 'sale'">For Sale</flux:select.option>
            </flux:select>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Filter</flux:button>
                <flux:button href="{{ route('listings.index') }}" variant="ghost">Reset</flux:button>
            </div>
        </form>

        {{-- Results --}}
        @if ($listings->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                <flux:icon name="face-frown" class="mb-3 size-10 text-zinc-400" />
                <flux:heading>No listings found</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Try adjusting your filters or check back later.</flux:text>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($listings as $listing)
                    <a href="{{ route('listings.show', $listing) }}"
                        class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">

                        {{-- Image --}}
                        <div class="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            @if ($listing->image)
                                <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->name }}"
                                    class="h-full w-full object-cover transition group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <flux:icon name="photo" class="size-12 text-zinc-300" />
                                </div>
                            @endif

                            <div class="absolute left-2 top-2">
                                <flux:badge variant="{{ $listing->type === 'adoption' ? 'success' : 'info' }}" size="sm">
                                    {{ ucfirst($listing->type) }}
                                </flux:badge>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-1 flex-col gap-1 p-4">
                            <flux:heading size="base">{{ $listing->name }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">
                                {{ $listing->breed?->name ?? 'Unknown breed' }}
                                @if ($listing->gender !== 'unknown')
                                    · {{ ucfirst($listing->gender) }}
                                @endif
                            </flux:text>
                            @if ($listing->province)
                                <flux:text class="flex items-center gap-1 text-xs text-zinc-400">
                                    <flux:icon name="map-pin" class="size-3" />
                                    {{ $listing->province }}
                                </flux:text>
                            @endif
                            <div class="mt-2">
                                @if ($listing->type === 'sale' && $listing->price)
                                    <span class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                                        ฿{{ number_format($listing->price, 0) }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">Free / Adoption</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $listings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
