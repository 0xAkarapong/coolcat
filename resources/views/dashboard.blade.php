<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Welcome back, {{ auth()->user()->name }}!</flux:heading>
                <flux:text class="text-zinc-500">Here's a quick overview of your CoolCat activity.</flux:text>
            </div>
            
            <div class="flex gap-2">
                <flux:button href="{{ route('listings.create') }}" variant="primary" icon="plus">New Listing</flux:button>
                <flux:button href="{{ route('products.create') }}" variant="outline" icon="plus">New Product</flux:button>
            </div>
        </div>

        {{-- Metrics Grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <a href="{{ route('listings.index') }}" class="group flex flex-col rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-orange-500 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-orange-500">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400">
                        <flux:icon name="heart" class="size-5" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500">Active Adoptions</flux:text>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $activeListings }}</span>
                </div>
            </a>

            <a href="{{ route('products.index') }}" class="group flex flex-col rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-emerald-500 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-emerald-500">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <flux:icon name="shopping-bag" class="size-5" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500">Active Products</flux:text>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $activeProducts }}</span>
                </div>
            </a>

            <a href="{{ route('orders.index') }}" class="group flex flex-col rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-blue-500 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-blue-500">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                        <flux:icon name="banknotes" class="size-5" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500">Recent Sales</flux:text>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ collect($recentSales)->count() }}</span>
                </div>
            </a>

            <a href="{{ route('inquiries.my') }}" class="group flex flex-col rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-indigo-500 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-indigo-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                            <flux:icon name="inbox" class="size-5" />
                        </div>
                        <flux:text class="text-sm font-medium text-zinc-500">Inquiries</flux:text>
                    </div>
                    @if($unreadInquiries > 0)
                        <span class="flex size-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">{{ $unreadInquiries }}</span>
                    @endif
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $unreadInquiries }}</span>
                    <span class="text-sm font-medium text-zinc-500">Waiting response</span>
                </div>
            </a>
            
        </div>

        {{-- Main Activity Area --}}
        <div class="grid gap-6 lg:grid-cols-3">
            
            {{-- Order Activity --}}
            <div class="flex flex-col gap-4 lg:col-span-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="sm">Recent Sales</flux:heading>
                        <a href="{{ route('orders.index') }}" class="text-sm font-medium text-orange-500 hover:underline">View all</a>
                    </div>
                    
                    @if($recentSales->isEmpty())
                        <div class="py-8 text-center text-zinc-500">No recent sales yet.</div>
                    @else
                        <div class="flex flex-col divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($recentSales as $sale)
                                <a href="{{ route('orders.show', $sale) }}" class="flex items-center justify-between py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <div>
                                        <flux:text class="font-medium text-zinc-900 dark:text-zinc-100">Order #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</flux:text>
                                        <flux:text class="text-sm text-zinc-500">Buyer: {{ $sale->user->name }} • {{ $sale->items->sum('quantity') }} items</flux:text>
                                    </div>
                                    <div class="text-right">
                                        <flux:text class="font-medium">฿{{ number_format($sale->total, 2) }}</flux:text>
                                        <flux:badge size="sm" variant="{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'default') }}">
                                            {{ ucfirst($sale->status) }}
                                        </flux:badge>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats / Helpful Links --}}
            <div class="flex flex-col gap-6">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4">Quick Links</flux:heading>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('listings.index') }}" class="flex items-center gap-2 rounded-md p-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                            <flux:icon name="heart" class="size-4" /> Go to Adoptions
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center gap-2 rounded-md p-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                            <flux:icon name="shopping-bag" class="size-4" /> Go to Marketplace
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-md p-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                            <flux:icon name="cog" class="size-4" /> Account Settings
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-layouts::app>
