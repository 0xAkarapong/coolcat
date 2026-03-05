<x-layouts::app :title="'Inquiry — ' . $inquiry->listing->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('listings.show', $inquiry->listing) }}">{{ $inquiry->listing->name }}</flux:breadcrumbs.item>
            @can('update', $inquiry)
                <flux:breadcrumbs.item href="{{ route('listings.inquiries.index', $inquiry->listing) }}">Inquiries</flux:breadcrumbs.item>
            @endcan
            <flux:breadcrumbs.item>Detail</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Inquiry Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <flux:heading size="xl">Inquiry Detail</flux:heading>
                    <flux:badge variant="{{
                        match($inquiry->status) {
                            'confirmed'  => 'success',
                            'rejected'   => 'danger',
                            'completed'  => 'info',
                            'cancelled'  => 'warning',
                            default      => 'default',
                        }
                    }}">
                        {{ ucfirst($inquiry->status) }}
                    </flux:badge>
                </div>

                {{-- Buyer info --}}
                <div class="flex items-center gap-3 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                    <flux:avatar :name="$inquiry->buyer->name" />
                    <div>
                        <flux:text class="font-medium">{{ $inquiry->buyer->name }}</flux:text>
                        <flux:text class="text-sm text-zinc-500">{{ $inquiry->buyer->email }}</flux:text>
                    </div>
                </div>

                {{-- Message --}}
                <div>
                    <flux:text class="mb-1 text-xs font-medium uppercase tracking-wide text-zinc-400">Message</flux:text>
                    <flux:text class="whitespace-pre-line text-zinc-700 dark:text-zinc-300">
                        {{ $inquiry->message ?? '— No message provided.' }}
                    </flux:text>
                </div>

                {{-- Meeting info --}}
                @if ($inquiry->meet_date || $inquiry->meet_time || $inquiry->meet_location)
                    <div class="rounded-lg border border-zinc-100 p-4 dark:border-zinc-700">
                        <flux:text class="mb-2 text-xs font-medium uppercase tracking-wide text-zinc-400">Proposed Meeting</flux:text>
                        <div class="flex flex-col gap-1.5">
                            @if ($inquiry->meet_date)
                                <flux:text class="flex items-center gap-2 text-sm">
                                    <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                    {{ $inquiry->meet_date->format('l, d M Y') }}
                                    @if ($inquiry->meet_time)
                                        at {{ $inquiry->meet_time }}
                                    @endif
                                </flux:text>
                            @endif
                            @if ($inquiry->meet_location)
                                <flux:text class="flex items-center gap-2 text-sm">
                                    <flux:icon name="map-pin" class="size-4 text-zinc-400" />
                                    {{ $inquiry->meet_location }}
                                </flux:text>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Seller note --}}
                @if ($inquiry->seller_note)
                    <div>
                        <flux:text class="mb-1 text-xs font-medium uppercase tracking-wide text-zinc-400">Seller's Note</flux:text>
                        <flux:text class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
                            {{ $inquiry->seller_note }}
                        </flux:text>
                    </div>
                @endif

                {{-- Timestamps --}}
                <flux:text class="text-xs text-zinc-400">
                    Submitted {{ $inquiry->created_at->diffForHumans() }}
                    @if ($inquiry->updated_at->ne($inquiry->created_at))
                        · Updated {{ $inquiry->updated_at->diffForHumans() }}
                    @endif
                </flux:text>
            </div>

            {{-- Actions Sidebar --}}
            <div class="flex flex-col gap-4">

                {{-- Seller actions --}}
                @can('update', $inquiry)
                    <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                        <flux:heading size="sm" class="mb-4">Update Status</flux:heading>
                        <form method="POST" action="{{ route('inquiries.update', $inquiry) }}" class="flex flex-col gap-4">
                            @csrf
                            @method('PATCH')

                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select name="status">
                                    <flux:select.option value="confirmed" :selected="$inquiry->status === 'confirmed'">Confirmed</flux:select.option>
                                    <flux:select.option value="rejected" :selected="$inquiry->status === 'rejected'">Rejected</flux:select.option>
                                    <flux:select.option value="completed" :selected="$inquiry->status === 'completed'">Completed</flux:select.option>
                                    <flux:select.option value="cancelled" :selected="$inquiry->status === 'cancelled'">Cancelled</flux:select.option>
                                </flux:select>
                                <flux:error name="status" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Note to Buyer</flux:label>
                                <flux:textarea name="seller_note" rows="4" placeholder="Add a note for the buyer...">{{ old('seller_note', $inquiry->seller_note) }}</flux:textarea>
                                <flux:error name="seller_note" />
                            </flux:field>

                            <flux:button type="submit" variant="primary" class="w-full">Save</flux:button>
                        </form>
                    </div>
                @endcan

                {{-- Delete inquiry --}}
                @can('delete', $inquiry)
                    <form method="POST" action="{{ route('inquiries.destroy', $inquiry) }}"
                        onsubmit="return confirm('Are you sure you want to remove this inquiry?')">
                        @csrf
                        @method('DELETE')
                        <flux:button type="submit" variant="danger" icon="trash" class="w-full">
                            Delete Inquiry
                        </flux:button>
                    </form>
                @endcan

                {{-- Back to listing --}}
                <flux:button href="{{ route('listings.show', $inquiry->listing) }}" variant="ghost" class="w-full">
                    Back to Listing
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>
