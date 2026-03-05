<x-layouts::app :title="'Inquiries for ' . $listing->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('listings.show', $listing) }}">{{ $listing->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Inquiries</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Inquiries</flux:heading>
                <flux:text class="text-zinc-500">For listing: <span class="font-medium">{{ $listing->name }}</span></flux:text>
            </div>
            <flux:badge>{{ $inquiries->total() }} total</flux:badge>
        </div>

        @if ($inquiries->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                <flux:icon name="chat-bubble-left-ellipsis" class="mb-3 size-10 text-zinc-400" />
                <flux:heading>No inquiries yet</flux:heading>
                <flux:text class="mt-1 text-zinc-500">When buyers send inquiries, they'll appear here.</flux:text>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($inquiries as $inquiry)
                    <a href="{{ route('inquiries.show', $inquiry) }}"
                        class="flex items-start justify-between rounded-xl border border-zinc-200 p-4 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50">
                        <div class="flex items-start gap-4">
                            <flux:avatar :name="$inquiry->buyer->name" class="shrink-0" />
                            <div>
                                <flux:text class="font-medium">{{ $inquiry->buyer->name }}</flux:text>
                                <flux:text class="mt-0.5 text-sm text-zinc-500 line-clamp-2">
                                    {{ $inquiry->message ?? 'No message provided.' }}
                                </flux:text>
                                @if ($inquiry->meet_date)
                                    <flux:text class="mt-1 flex items-center gap-1 text-xs text-zinc-400">
                                        <flux:icon name="calendar" class="size-3" />
                                        Meet: {{ $inquiry->meet_date->format('d M Y') }}
                                        @if ($inquiry->meet_time) at {{ $inquiry->meet_time }} @endif
                                    </flux:text>
                                @endif
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
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
                            <flux:text class="text-xs text-zinc-400">{{ $inquiry->created_at->diffForHumans() }}</flux:text>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
