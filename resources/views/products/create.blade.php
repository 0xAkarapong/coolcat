<x-layouts::app :title="__('Add Product')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center gap-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('products.index') }}">Marketplace</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Add Product</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <flux:heading size="xl">Add a New Product</flux:heading>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
            class="grid gap-6 lg:grid-cols-2"
            x-data="{ submitting: false }"
            x-on:submit="if (submitting) { $event.preventDefault(); return } submitting = true">
            @csrf

            {{-- Product Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">General Information</flux:heading>

                <flux:field>
                    <flux:label>Product Name <span class="text-red-500">*</span></flux:label>
                    <flux:input name="name" value="{{ old('name') }}" placeholder="e.g. Premium Cat Food" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Category <span class="text-red-500">*</span></flux:label>
                    <flux:select name="category" required>
                        <flux:select.option value="food" :selected="old('category') === 'food'">Food</flux:select.option>
                        <flux:select.option value="toy" :selected="old('category') === 'toy'">Toys</flux:select.option>
                        <flux:select.option value="accessory" :selected="old('category') === 'accessory'">Accessories</flux:select.option>
                        <flux:select.option value="health" :selected="old('category') === 'health'">Health & Care</flux:select.option>
                        <flux:select.option value="litter" :selected="old('category') === 'litter'">Litter & Box</flux:select.option>
                        <flux:select.option value="grooming" :selected="old('category') === 'grooming'">Grooming</flux:select.option>
                        <flux:select.option value="furniture" :selected="old('category') === 'furniture'">Furniture</flux:select.option>
                        <flux:select.option value="other" :selected="old('category') === 'other'">Other</flux:select.option>
                    </flux:select>
                    <flux:error name="category" />
                </flux:field>

                <flux:field>
                    <flux:label>Description <span class="text-red-500">*</span></flux:label>
                    <flux:textarea name="description" rows="6" placeholder="Describe your product details, features, and benefits..." required>{{ old('description') }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>Product Image</flux:label>
                    <flux:input type="file" name="image" accept="image/*" />
                    <flux:error name="image" />
                </flux:field>
            </div>

            {{-- Inventory & Pricing --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">Inventory & Pricing</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Price (฿) <span class="text-red-500">*</span></flux:label>
                        <flux:input type="number" name="price" value="{{ old('price') }}" placeholder="0.00" min="0" step="0.01" required />
                        <flux:error name="price" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Stock Quantity <span class="text-red-500">*</span></flux:label>
                        <flux:input type="number" name="stock" value="{{ old('stock', 0) }}" placeholder="0" min="0" required />
                        <flux:error name="stock" />
                    </flux:field>
                </div>

                <flux:field>
                    <div class="flex items-center gap-2">
                        <flux:checkbox name="is_active" value="1" :checked="old('is_active', true)" />
                        <flux:label>Active (visible to public)</flux:label>
                    </div>
                    <flux:error name="is_active" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button href="{{ route('products.index') }}" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" icon="plus">Add Product</flux:button>
                </div>
            </div>
        </form>
    </div>
</x-layouts::app>
