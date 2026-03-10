<x-layouts::app :title="__('Edit Product')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center gap-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('products.index') }}">Marketplace</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('products.show', $product) }}">{{ $product->name }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Edit</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <flux:heading size="xl">Edit Product: {{ $product->name }}</flux:heading>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
            class="grid gap-6 lg:grid-cols-2"
            x-data="{ submitting: false }"
            x-on:submit="if (submitting) { $event.preventDefault(); return } submitting = true">
            @csrf
            @method('PUT')

            {{-- Product Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">General Information</flux:heading>

                <flux:field>
                    <flux:label>Product Name <span class="text-red-500">*</span></flux:label>
                    <flux:input name="name" value="{{ old('name', $product->name) }}" placeholder="e.g. Premium Cat Food" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Category <span class="text-red-500">*</span></flux:label>
                    <flux:select name="category" required>
                        <flux:select.option value="food" :selected="old('category', $product->category) === 'food'">Food</flux:select.option>
                        <flux:select.option value="toy" :selected="old('category', $product->category) === 'toy'">Toys</flux:select.option>
                        <flux:select.option value="accessory" :selected="old('category', $product->category) === 'accessory'">Accessories</flux:select.option>
                        <flux:select.option value="health" :selected="old('category', $product->category) === 'health'">Health & Care</flux:select.option>
                        <flux:select.option value="litter" :selected="old('category', $product->category) === 'litter'">Litter & Box</flux:select.option>
                        <flux:select.option value="grooming" :selected="old('category', $product->category) === 'grooming'">Grooming</flux:select.option>
                        <flux:select.option value="furniture" :selected="old('category', $product->category) === 'furniture'">Furniture</flux:select.option>
                        <flux:select.option value="other" :selected="old('category', $product->category) === 'other'">Other</flux:select.option>
                    </flux:select>
                    <flux:error name="category" />
                </flux:field>

                <flux:field>
                    <flux:label>Description <span class="text-red-500">*</span></flux:label>
                    <flux:textarea name="description" rows="6" placeholder="Describe your product details, features, and benefits..." required>{{ old('description', $product->description) }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>

                <div class="flex flex-col gap-2">
                    <flux:label>Product Image</flux:label>
                    @if ($product->image)
                        <div class="relative w-32 aspect-square overflow-hidden rounded-lg mb-2">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <flux:input type="file" name="image" accept="image/*" />
                    <flux:error name="image" />
                </div>
            </div>

            {{-- Inventory & Pricing --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">Inventory & Pricing</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Price (฿) <span class="text-red-500">*</span></flux:label>
                        <flux:input type="number" name="price" value="{{ old('price', $product->price) }}" placeholder="0.00" min="0" step="0.01" required />
                        <flux:error name="price" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Stock Quantity <span class="text-red-500">*</span></flux:label>
                        <flux:input type="number" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="0" min="0" required />
                        <flux:error name="stock" />
                    </flux:field>
                </div>

                <flux:field>
                    <div class="flex items-center gap-2">
                        <flux:checkbox name="is_active" value="1" :checked="old('is_active', $product->is_active)" />
                        <flux:label>Active (visible to public)</flux:label>
                    </div>
                    <flux:error name="is_active" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button href="{{ route('products.show', $product) }}" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Update Product</flux:button>
                </div>
            </div>
        </form>
    </div>
</x-layouts::app>
