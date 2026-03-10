<x-layouts::app :title="__('My Inquiries')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}">Dashboard</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>My Inquiries</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="xl">My Inquiries</flux:heading>

        <div x-data="{ tab: 'sent' }" class="flex flex-col gap-6">
            <div class="flex gap-6 border-b border-zinc-200 dark:border-zinc-700">
                <button @click="tab = 'sent'"
                    :class="tab === 'sent' ? 'border-b-2 border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                    class="flex items-center gap-2 pb-3 text-sm font-medium transition-colors">
                    <flux:icon name="paper-airplane" class="size-5" />
                    Sent Inquiries
                </button>
                <button @click="tab = 'received'"
                    :class="tab === 'received' ? 'border-b-2 border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                    class="flex items-center gap-2 pb-3 text-sm font-medium transition-colors">
                    <flux:icon name="inbox-arrow-down" class="size-5" />
                    Received Inquiries
                </button>
            </div>

            {{-- Sent Inquiries Tab --}}
            <div x-show="tab === 'sent'" x-cloak>
                @if ($sentInquiries->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                        <flux:icon name="paper-airplane" class="mb-3 size-10 text-zinc-400" />
                        <flux:heading>No sent inquiries</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">When you inquire about a listing, it will appear here.</flux:text>
                        <flux:button href="{{ route('listings.index') }}" variant="primary" class="mt-4">
                            Browse Listings
                        </flux:button>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($sentInquiries as $inquiry)
                            <a href="{{ route('inquiries.show', $inquiry) }}"
                                class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50 sm:flex-row sm:items-center sm:justify-between">
                                
                                <div>
                                    <div class="flex items-center gap-3">
                                        <flux:heading size="sm">{{ $inquiry->listing->name }}</flux:heading>
                                        <flux:badge variant="{{
                                            match($inquiry->status) {
                                                'confirmed'  => 'success',
                                                'completed'  => 'info',
                                                'rejected'   => 'danger',
                                                'cancelled'  => 'warning',
                                                default      => 'default',
                                            }
                                        }}">
                                            {{ ucfirst($inquiry->status) }}
                                        </flux:badge>
                                    </div>
                                    <flux:text class="mt-1 text-sm text-zinc-500">
                                        Listed by <span class="font-medium">{{ $inquiry->listing->user->name }}</span> · {{ $inquiry->created_at->format('M d, Y') }}
                                    </flux:text>
                                </div>

                                <div class="text-left sm:text-right">
                                    <flux:text class="text-sm text-zinc-500 hover:text-zinc-800">
                                        View details &rarr;
                                    </flux:text>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Received Inquiries Tab --}}
            <div x-show="tab === 'received'" x-cloak>
                @if ($receivedInquiries->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                        <flux:icon name="inbox-arrow-down" class="mb-3 size-10 text-zinc-400" />
                        <flux:heading>No incoming inquiries</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">When someone inquires about your listings, they'll appear here.</flux:text>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($receivedInquiries as $inquiry)
                            <a href="{{ route('inquiries.show', $inquiry) }}"
                                class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50 sm:flex-row sm:items-center sm:justify-between">
                                
                                <div>
                                    <div class="flex items-center gap-3">
                                        <flux:heading size="sm">For: {{ $inquiry->listing->name }}</flux:heading>
                                        <flux:badge variant="{{
                                            match($inquiry->status) {
                                                'confirmed'  => 'success',
                                                'completed'  => 'info',
                                                'rejected'   => 'danger',
                                                'cancelled'  => 'warning',
                                                default      => 'default',
                                            }
                                        }}">
                                            {{ ucfirst($inquiry->status) }}
                                        </flux:badge>
                                    </div>
                                    <flux:text class="mt-1 text-sm text-zinc-500">
                                        From: <span class="font-medium">{{ $inquiry->buyer->name }}</span> · {{ $inquiry->created_at->format('M d, Y') }}
                                    </flux:text>
                                </div>

                                <div class="text-left sm:text-right">
                                    <flux:text class="text-sm text-zinc-500 hover:text-zinc-800">
                                        Manage inquiry &rarr;
                                    </flux:text>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-layouts::app>
