<x-layouts::app :title="$listing->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Breadcrumbs --}}
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $listing->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="grid gap-8 lg:grid-cols-3">

            {{-- Left — Image + Actions --}}
            <div class="flex flex-col gap-4">
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800">
                    @if ($listing->image)
                        <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->name }}"
                            class="h-64 w-full object-cover lg:h-80">
                    @else
                        <div class="flex h-64 items-center justify-center lg:h-80">
                            <flux:icon name="photo" class="size-16 text-zinc-300" />
                        </div>
                    @endif
                </div>

                {{-- Owner actions --}}
                @can('update', $listing)
                    <div class="flex gap-2">
                        <flux:button href="{{ route('listings.edit', $listing) }}" icon="pencil" class="flex-1">
                            Edit
                        </flux:button>
                        <form method="POST" action="{{ route('listings.destroy', $listing) }}" class="flex-1"
                            onsubmit="return confirm('Are you sure you want to remove this listing?')">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" variant="danger" icon="trash" class="w-full">
                                Delete
                            </flux:button>
                        </form>
                    </div>
                @endcan

                {{-- Inquiry button for non-owners --}}
                @auth
                    @cannot('update', $listing)
                        <flux:button
                            href="{{ route('listings.inquiries.create', $listing) }}"
                            variant="primary"
                            icon="chat-bubble-left-ellipsis"
                            class="w-full"
                        >
                            Send Inquiry
                        </flux:button>
                    @endcannot
                @else
                    <flux:button href="{{ route('login') }}" variant="primary" class="w-full">
                        Log in to Inquire
                    </flux:button>
                @endauth

                {{-- Stats --}}
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text class="text-xs text-zinc-400">
                        <flux:icon name="eye" class="mr-1 inline-block size-4" />
                        {{ number_format($listing->views) }} views
                    </flux:text>
                    <flux:text class="mt-1 text-xs text-zinc-400">
                        Listed by
                        <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $listing->user->name }}</span>
                    </flux:text>
                </div>
            </div>

            {{-- Right — Details --}}
            <div class="flex flex-col gap-6 lg:col-span-2">
                <div class="flex flex-wrap items-start gap-3">
                    <flux:heading size="xl">{{ $listing->name }}</flux:heading>
                    <flux:badge variant="{{ $listing->type === 'adoption' ? 'success' : 'info' }}">
                        {{ ucfirst($listing->type) }}
                    </flux:badge>
                    <flux:badge variant="{{ $listing->status === 'active' ? 'default' : 'danger' }}">
                        {{ ucfirst($listing->status) }}
                    </flux:badge>
                </div>

                @if ($listing->type === 'sale' && $listing->price)
                    <div>
                        <span class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                            ฿{{ number_format($listing->price, 0) }}
                        </span>
                    </div>
                @else
                    <span class="text-xl font-semibold text-green-600 dark:text-green-400">Free / Adoption</span>
                @endif

                {{-- Attributes --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @if ($listing->breed)
                        <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                            <flux:text class="text-xs text-zinc-400">Breed</flux:text>
                            <flux:text class="font-medium">{{ $listing->breed->name }}</flux:text>
                        </div>
                    @endif
                    <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                        <flux:text class="text-xs text-zinc-400">Gender</flux:text>
                        <flux:text class="font-medium">{{ ucfirst($listing->gender) }}</flux:text>
                    </div>
                    @if ($listing->birthdate)
                        <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                            <flux:text class="text-xs text-zinc-400">Age</flux:text>
                            <flux:text class="font-medium">{{ $listing->age }} months</flux:text>
                        </div>
                    @endif
                    @if ($listing->color)
                        <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                            <flux:text class="text-xs text-zinc-400">Color</flux:text>
                            <flux:text class="font-medium">{{ $listing->color }}</flux:text>
                        </div>
                    @endif
                    <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                        <flux:text class="text-xs text-zinc-400">Neutered</flux:text>
                        <flux:text class="font-medium">{{ $listing->is_neutered ? 'Yes' : 'No' }}</flux:text>
                    </div>
                    <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                        <flux:text class="text-xs text-zinc-400">Vaccinated</flux:text>
                        <flux:text class="font-medium">{{ $listing->is_vaccinated ? 'Yes' : 'No' }}</flux:text>
                    </div>
                    @if ($listing->province)
                        <div class="rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                            <flux:text class="text-xs text-zinc-400">Province</flux:text>
                            <flux:text class="font-medium">{{ $listing->province }}</flux:text>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if ($listing->description)
                    <div>
                        <flux:heading size="sm" class="mb-2">About</flux:heading>
                        <flux:text class="whitespace-pre-line leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ $listing->description }}
                        </flux:text>
                    </div>
                @endif

                {{-- Reviews --}}
                <livewire:reviews.review-section :reviewable="$listing" />
            </div>
        </div>
    </div>
</x-layouts::app>
