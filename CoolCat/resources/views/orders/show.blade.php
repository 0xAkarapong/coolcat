<x-layouts::app :title="'Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT)">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('orders.index') }}">Orders</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Order Detail</flux:heading>
                <flux:text class="text-zinc-500">
                    Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                </flux:text>
            </div>
            
            <div class="flex items-center gap-3">
                <flux:text class="text-sm font-medium uppercase text-zinc-500">Status</flux:text>
                <flux:badge size="lg" variant="{{
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
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            
            {{-- Left: Items & Totals --}}
            <div class="flex flex-col gap-6 lg:col-span-2">
                <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                        <flux:heading size="sm">Items Ordered</flux:heading>
                    </div>
                    
                    <div class="flex flex-col divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4 p-4">
                                @if ($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="size-16 rounded-md object-cover">
                                @else
                                    <div class="flex size-16 shrink-0 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="photo" class="size-6 text-zinc-400" />
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <a href="{{ route('products.show', $item->product) }}" class="font-medium hover:underline text-zinc-900 dark:text-zinc-100">
                                        {{ $item->product->name }}
                                    </a>
                                    <flux:text class="text-sm text-zinc-500">
                                        Seller: {{ $item->product->user->name }}
                                    </flux:text>
                                </div>
                                
                                <div class="text-right">
                                    <flux:text class="font-medium">฿{{ number_format($item->price, 2) }}</flux:text>
                                    <flux:text class="text-sm text-zinc-500">Qty: {{ $item->quantity }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-zinc-200 bg-zinc-50 p-4 text-right dark:border-zinc-700 dark:bg-zinc-800/50">
                        <flux:text class="text-sm text-zinc-500">Total Amount</flux:text>
                        <flux:heading size="lg" class="mt-1">฿{{ number_format($order->total, 2) }}</flux:heading>
                    </div>
                </div>
            </div>

            {{-- Right: Shipping & Management --}}
            <div class="flex flex-col gap-6">
                
                {{-- Shipping Info --}}
                <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <flux:heading size="sm" class="mb-4">Shipping Information</flux:heading>
                    
                    <div class="flex flex-col gap-3">
                        <div>
                            <flux:text class="text-xs font-medium uppercase text-zinc-400">Recipient</flux:text>
                            <flux:text class="font-medium">{{ $order->shipping_name }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-xs font-medium uppercase text-zinc-400">Phone</flux:text>
                            <flux:text>{{ $order->shipping_phone }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-xs font-medium uppercase text-zinc-400">Address</flux:text>
                            <flux:text class="whitespace-pre-line">{{ $order->shipping_address }}</flux:text>
                        </div>
                        @if ($order->payment_ref)
                            <div>
                                <flux:text class="text-xs font-medium uppercase text-zinc-400">Payment Ref</flux:text>
                                <flux:text class="font-mono text-sm">{{ $order->payment_ref }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Seller Controls --}}
                @can('update', $order)
                    <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                        <flux:heading size="sm" class="mb-4">Update Order Status</flux:heading>
                        
                        <form method="POST" action="{{ route('orders.update', $order) }}" class="flex flex-col gap-4">
                            @csrf
                            @method('PATCH')
                            
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select name="status">
                                    <flux:select.option value="pending" :selected="$order->status === 'pending'">Pending</flux:select.option>
                                    <flux:select.option value="paid" :selected="$order->status === 'paid'">Paid</flux:select.option>
                                    <flux:select.option value="shipped" :selected="$order->status === 'shipped'">Shipped</flux:select.option>
                                    <flux:select.option value="completed" :selected="$order->status === 'completed'">Completed</flux:select.option>
                                    <flux:select.option value="cancelled" :selected="$order->status === 'cancelled'">Cancelled</flux:select.option>
                                </flux:select>
                                <flux:error name="status" />
                            </flux:field>

                            <flux:button type="submit" variant="primary" class="w-full">Update Status</flux:button>
                        </form>
                    </div>
                @endcan

            </div>
        </div>

    </div>
</x-layouts::app>
