<x-layouts::app :title="__('My Orders')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}">Dashboard</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Orders</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="xl">Orders</flux:heading>

        <div x-data="{ tab: 'purchases' }" class="flex flex-col gap-6">
            <div class="flex gap-6 border-b border-zinc-200 dark:border-zinc-700">
                <button @click="tab = 'purchases'"
                    :class="tab === 'purchases' ? 'border-b-2 border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                    class="flex items-center gap-2 pb-3 text-sm font-medium transition-colors">
                    <flux:icon name="shopping-bag" class="size-5" />
                    My Purchases
                </button>
                <button @click="tab = 'sales'"
                    :class="tab === 'sales' ? 'border-b-2 border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                    class="flex items-center gap-2 pb-3 text-sm font-medium transition-colors">
                    <flux:icon name="banknotes" class="size-5" />
                    Incoming Orders (Sales)
                </button>
            </div>

            {{-- Purchases Tab --}}
            <div x-show="tab === 'purchases'" x-cloak>
                @if ($buyerOrders->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                        <flux:icon name="shopping-bag" class="mb-3 size-10 text-zinc-400" />
                        <flux:heading>No purchases yet</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">When you buy a product, it will appear here.</flux:text>
                        <flux:button href="{{ route('products.index') }}" variant="primary" class="mt-4">
                            Browse Products
                        </flux:button>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($buyerOrders as $order)
                            <a href="{{ route('orders.show', $order) }}"
                                class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50 sm:flex-row sm:items-center sm:justify-between">
                                
                                <div>
                                    <div class="flex items-center gap-3">
                                        <flux:heading size="sm">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</flux:heading>
                                        <flux:badge variant="{{
                                            match($order->status) {
                                                'completed'  => 'success',
                                                'cancelled'  => 'danger',
                                                'shipped'    => 'info',
                                                'paid'       => 'default',
                                                default      => 'warning', // pending
                                            }
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </flux:badge>
                                    </div>
                                    <flux:text class="mt-1 text-sm text-zinc-500">
                                        {{ $order->created_at->format('M d, Y') }} · {{ $order->items->sum('quantity') }} items
                                    </flux:text>
                                </div>

                                <div class="text-left sm:text-right">
                                    <flux:text class="font-medium text-zinc-900 dark:text-zinc-100">
                                        ฿{{ number_format($order->total, 2) }}
                                    </flux:text>
                                    <flux:text class="text-sm text-zinc-500 hover:text-zinc-800">
                                        View details &rarr;
                                    </flux:text>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sales Tab --}}
            <div x-show="tab === 'sales'" x-cloak>
                @if ($sellerOrders->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                        <flux:icon name="inbox" class="mb-3 size-10 text-zinc-400" />
                        <flux:heading>No incoming orders</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">When someone buys your products, they'll appear here.</flux:text>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($sellerOrders as $order)
                            <a href="{{ route('orders.show', $order) }}"
                                class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50 sm:flex-row sm:items-center sm:justify-between">
                                
                                <div>
                                    <div class="flex items-center gap-3">
                                        <flux:heading size="sm">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</flux:heading>
                                        <flux:badge variant="{{
                                            match($order->status) {
                                                'completed'  => 'success',
                                                'cancelled'  => 'danger',
                                                'shipped'    => 'info',
                                                'paid'       => 'default',
                                                default      => 'warning', // pending
                                            }
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </flux:badge>
                                    </div>
                                    <flux:text class="mt-1 text-sm text-zinc-500">
                                        From: <span class="font-medium">{{ $order->user->name }}</span> · {{ $order->created_at->format('M d, Y') }}
                                    </flux:text>
                                </div>

                                <div class="text-left sm:text-right">
                                    <flux:text class="text-sm text-zinc-500 hover:text-zinc-800">
                                        Manage order &rarr;
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
