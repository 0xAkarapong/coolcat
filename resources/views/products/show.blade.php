<x-layouts::app :title="$product->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        {{-- Breadcrumbs / Back --}}
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('products.index') }}" variant="ghost" icon="arrow-left" size="sm">
                Back to Marketplace
            </flux:button>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Image Gallery (Single for now) --}}
            <div class="overflow-hidden rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                @if ($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                        class="h-full w-full object-cover">
                @else
                    <div class="flex aspect-square items-center justify-center">
                        <flux:icon name="photo" class="size-24 text-zinc-300" />
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <flux:badge variant="neutral">{{ ucfirst($product->category) }}</flux:badge>
                        @can('update', $product)
                            <div class="flex gap-2">
                                <flux:button href="{{ route('products.edit', $product) }}" icon="pencil-square" size="sm">
                                    Edit
                                </flux:button>
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" icon="trash" size="sm">
                                        Delete
                                    </flux:button>
                                </form>
                            </div>
                        @endcan
                    </div>
                    <flux:heading size="2xl">{{ $product->name }}</flux:heading>
                    <div class="flex items-center gap-4">
                        <span class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                            ฿{{ number_format($product->price, 2) }}
                        </span>
                        @if ($product->stock > 0)
                            <flux:badge variant="success" size="sm">In Stock ({{ $product->stock }})</flux:badge>
                        @else
                            <flux:badge variant="danger" size="sm">Out of Stock</flux:badge>
                        @endif
                    </div>
                </div>

                <flux:separator />

                <div class="flex flex-col gap-2">
                    <flux:heading size="lg">Description</flux:heading>
                    <flux:text class="text-base leading-relaxed">
                        {{ $product->description }}
                    </flux:text>
                </div>

                <flux:separator />

                {{-- Seller Info --}}
                <div class="flex items-center gap-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon name="user" class="size-6 text-zinc-400" />
                    </div>
                    <div class="flex flex-col">
                        <flux:text class="font-medium text-zinc-900 dark:text-zinc-100">
                            Seller: {{ $product->user->name }}
                        </flux:text>
                        <flux:text class="text-xs text-zinc-500">
                            Member since {{ $product->user->created_at->format('M Y') }}
                        </flux:text>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-4 flex gap-4">
                    @if ($product->stock > 0)
                        <flux:button variant="primary" class="flex-1" icon="shopping-cart">
                            Add to Cart
                        </flux:button>
                    @else
                        <flux:button variant="primary" class="flex-1" icon="shopping-cart" disabled>
                            Add to Cart
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-12 flex flex-col gap-6">
            <flux:heading size="xl">Customer Reviews</flux:heading>

            @if ($product->reviews->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 py-12 dark:border-zinc-600">
                    <flux:text class="text-zinc-500">No reviews yet for this product.</flux:text>
                </div>
            @else
                <div class="grid gap-6">
                    @foreach ($product->reviews as $review)
                        <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="flex">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <flux:icon name="star"
                                                class="size-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-300' }}" />
                                        @endfor
                                    </div>
                                    <flux:text class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $review->user->name }}
                                    </flux:text>
                                </div>
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $review->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <flux:text>{{ $review->comment }}</flux:text>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
