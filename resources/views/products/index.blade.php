<x-layouts::app :title="__('Product Marketplace')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Marketplace</flux:heading>
            @auth
                <flux:button variant="primary" href="{{ route('products.create') }}" icon="plus">
                    Add Product
                </flux:button>
            @endauth
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            <flux:input
                name="search"
                placeholder="Search products..."
                value="{{ request('search') }}"
                icon="magnifying-glass"
            />

            <flux:select name="category" placeholder="All Categories">
                <flux:select.option value="">All Categories</flux:select.option>
                <flux:select.option value="food" :selected="request('category') === 'food'">Food</flux:select.option>
                <flux:select.option value="toy" :selected="request('category') === 'toy'">Toys</flux:select.option>
                <flux:select.option value="accessory" :selected="request('category') === 'accessory'">Accessories</flux:select.option>
                <flux:select.option value="health" :selected="request('category') === 'health'">Health & Care</flux:select.option>
                <flux:select.option value="litter" :selected="request('category') === 'litter'">Litter & Box</flux:select.option>
                <flux:select.option value="grooming" :selected="request('category') === 'grooming'">Grooming</flux:select.option>
                <flux:select.option value="furniture" :selected="request('category') === 'furniture'">Furniture</flux:select.option>
                <flux:select.option value="other" :selected="request('category') === 'other'">Other</flux:select.option>
            </flux:select>

            <flux:select name="in_stock" placeholder="Stock Availability">
                <flux:select.option value="">All Statuses</flux:select.option>
                <flux:select.option value="1" :selected="request('in_stock') == '1'">In Stock</flux:select.option>
            </flux:select>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Filter</flux:button>
                <flux:button href="{{ route('products.index') }}" variant="ghost">Reset</flux:button>
            </div>
        </form>

        {{-- Results --}}
        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 py-16 dark:border-zinc-700">
                <flux:icon name="face-frown" class="mb-3 size-10 text-zinc-400" />
                <flux:heading>No products found</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Try adjusting your filters or check back later.</flux:text>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($products as $product)
                    <a href="{{ route('products.show', $product) }}"
                        class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">

                        {{-- Image --}}
                        <div class="relative aspect-square overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <flux:icon name="photo" class="size-12 text-zinc-300" />
                                </div>
                            @endif

                            <div class="absolute left-2 top-2">
                                <flux:badge variant="neutral" size="sm">
                                    {{ ucfirst($product->category) }}
                                </flux:badge>
                            </div>

                            @if ($product->stock <= 0)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-white backdrop-blur-sm">
                                    <span class="font-bold uppercase tracking-wider">Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-1 flex-col gap-1 p-4">
                            <flux:heading size="base">{{ $product->name }}</flux:heading>
                            <flux:text class="line-clamp-2 text-sm text-zinc-500">
                                {{ $product->description }}
                            </flux:text>

                            <div class="mt-auto pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-zinc-800 dark:text-zinc-100">
                                        ฿{{ number_format($product->price, 2) }}
                                    </span>
                                    <flux:text class="text-xs text-zinc-400">
                                        {{ $product->stock }} in stock
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
