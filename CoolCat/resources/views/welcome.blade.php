<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CoolCat') }} - Find Your Purr-fect Companion</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxStyles
</head>
<body class="bg-white text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">

    {{-- Top Navigation --}}
    <header class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="flex size-8 items-center justify-center rounded-lg bg-orange-500 text-white">
                    <flux:icon name="sparkles" class="size-5" />
                </div>
                <span class="text-xl font-bold tracking-tight">CoolCat</span>
            </a>

            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('listings.index') }}" class="font-medium hover:text-orange-500 transition-colors">Adopt</a>
                <a href="{{ route('products.index') }}" class="font-medium hover:text-orange-500 transition-colors">Shop</a>
            </nav>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium hover:underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium hover:underline">Log in</a>
                    @if (Route::has('register'))
                        <flux:button href="{{ route('register') }}" variant="primary" size="sm">Sign up</flux:button>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- Hero Section --}}
        <section class="relative overflow-hidden bg-zinc-50 py-24 dark:bg-zinc-900">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=2043&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat opacity-10 dark:opacity-20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-50 to-transparent dark:from-zinc-900"></div>
            
            <div class="relative mx-auto max-w-4xl px-6 text-center">
                <flux:heading size="xl" class="!text-5xl !font-extrabold tracking-tight sm:!text-6xl text-zinc-900 dark:text-white">
                    Find Your <span class="text-orange-500">Purr-fect</span> Companion
                </flux:heading>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                    CoolCat is the premier community for connecting loving homes with incredible feline friends, and the ultimate shop for premium cat supplies.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <flux:button href="{{ route('listings.index') }}" variant="primary" icon="heart" class="w-full sm:w-auto">
                        Browse Cats for Adoption
                    </flux:button>
                    <flux:button href="{{ route('products.index') }}" variant="outline" icon="shopping-bag" class="w-full sm:w-auto">
                        Shop Supplies
                    </flux:button>
                </div>
            </div>
        </section>

        {{-- Featured Cats --}}
        <section class="mx-auto max-w-7xl px-6 py-20 pb-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <flux:heading size="xl">Featured Cats</flux:heading>
                    <flux:text class="text-zinc-500">Looking for a loving home today.</flux:text>
                </div>
                <a href="{{ route('listings.index') }}" class="text-sm font-medium text-orange-500 hover:text-orange-600 flex items-center gap-1">
                    View all <flux:icon name="arrow-right" class="size-4" />
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($featuredListings as $listing)
                    <a href="{{ route('listings.show', $listing) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800 relative">
                            @if ($listing->image)
                                <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center text-zinc-400">
                                    <flux:icon name="photo" class="size-12 opacity-50" />
                                </div>
                            @endif
                            <div class="absolute top-3 right-3">
                                <flux:badge size="sm" variant="solid" color="white">{{ ucfirst($listing->type) }}</flux:badge>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-lg leading-tight group-hover:text-orange-500 transition-colors">{{ $listing->name }}</h3>
                                    <p class="text-sm text-zinc-500 mt-0.5">{{ $listing->breed?->name ?? 'Mixed Breed' }} • {{ $listing->age }} mos</p>
                                </div>
                                <span class="font-semibold text-lg">฿{{ number_format($listing->price) }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <flux:icon name="face-smile" class="mx-auto mb-3 size-8 text-zinc-400" />
                        <flux:heading>No cats available right now</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">Check back later for new feline friends.</flux:text>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Featured Products --}}
        <section class="bg-zinc-50 py-20 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <flux:heading size="xl">Premium Supplies</flux:heading>
                        <flux:text class="text-zinc-500">Everything your new companion needs.</flux:text>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-orange-500 hover:text-orange-600 flex items-center gap-1">
                        Shop all <flux:icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($featuredProducts as $product)
                        <a href="{{ route('products.show', $product) }}" class="group flex flex-col rounded-2xl bg-white p-4 transition hover:shadow-md dark:bg-zinc-800 border border-transparent dark:hover:border-zinc-700">
                            <div class="aspect-square w-full overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900 mb-4">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center text-zinc-400">
                                        <flux:icon name="shopping-bag" class="size-8 opacity-50" />
                                    </div>
                                @endif
                            </div>
                            <h3 class="font-medium text-zinc-900 dark:text-white line-clamp-1">{{ $product->name }}</h3>
                            <p class="text-sm text-zinc-500 mt-1 mb-2">{{ ucfirst($product->category) }}</p>
                            <div class="mt-auto font-semibold">฿{{ number_format($product->price, 2) }}</div>
                        </a>
                    @empty
                        <div class="col-span-full py-12 text-center text-zinc-500">
                            No products available at the moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-zinc-200 bg-white py-12 dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <flux:icon name="sparkles" class="size-5 text-orange-500" />
                <span class="font-bold text-lg">CoolCat</span>
            </div>
            <p class="text-sm text-zinc-500 tracking-wide">
                &copy; {{ date('Y') }} CoolCat Platform. All rights reserved.
            </p>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
