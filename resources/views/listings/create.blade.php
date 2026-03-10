<x-layouts::app :title="__('Post a Listing')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center gap-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Post a Listing</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <flux:heading size="xl">Post a New Listing</flux:heading>

        <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data"
            class="grid gap-6 lg:grid-cols-2"
            x-data="{ submitting: false }"
            x-on:submit="if (submitting) { $event.preventDefault(); return } submitting = true">
            @csrf

            {{-- Cat Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">Cat Details</flux:heading>

                <flux:field>
                    <flux:label>Cat Name <span class="text-red-500">*</span></flux:label>
                    <flux:input name="name" value="{{ old('name') }}" placeholder="e.g. Whiskers" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Breed</flux:label>
                    <flux:select name="breed_id">
                        <flux:select.option value="">Unknown / Mixed</flux:select.option>
                        @foreach ($breeds as $breed)
                            <flux:select.option value="{{ $breed->id }}" :selected="old('breed_id') == $breed->id">
                                {{ $breed->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="breed_id" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Gender <span class="text-red-500">*</span></flux:label>
                        <flux:select name="gender">
                            <flux:select.option value="unknown" :selected="old('gender', 'unknown') === 'unknown'">Unknown</flux:select.option>
                            <flux:select.option value="male" :selected="old('gender') === 'male'">Male</flux:select.option>
                            <flux:select.option value="female" :selected="old('gender') === 'female'">Female</flux:select.option>
                        </flux:select>
                        <flux:error name="gender" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Birthdate</flux:label>
                        <flux:input type="date" name="birthdate" value="{{ old('birthdate') }}" />
                        <flux:error name="birthdate" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Color</flux:label>
                    <flux:input name="color" value="{{ old('color') }}" placeholder="e.g. Orange tabby" />
                    <flux:error name="color" />
                </flux:field>

                <div class="flex gap-6">
                    <flux:field>
                        <flux:checkbox name="is_neutered" value="1" :checked="old('is_neutered')" />
                        <flux:label>Neutered</flux:label>
                    </flux:field>
                    <flux:field>
                        <flux:checkbox name="is_vaccinated" value="1" :checked="old('is_vaccinated')" />
                        <flux:label>Vaccinated</flux:label>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Photo</flux:label>
                    <flux:input type="file" name="image" accept="image/*" />
                    <flux:error name="image" />
                </flux:field>
            </div>

            {{-- Listing Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">Listing Details</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Type <span class="text-red-500">*</span></flux:label>
                        <flux:select name="type">
                            <flux:select.option value="adoption" :selected="old('type', 'adoption') === 'adoption'">Adoption</flux:select.option>
                            <flux:select.option value="sale" :selected="old('type') === 'sale'">For Sale</flux:select.option>
                        </flux:select>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Price (฿)</flux:label>
                        <flux:input type="number" name="price" value="{{ old('price') }}" placeholder="Leave blank if free" min="0" step="0.01" />
                        <flux:error name="price" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Province</flux:label>
                    <flux:input name="province" value="{{ old('province') }}" placeholder="e.g. Bangkok" />
                    <flux:error name="province" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea name="description" rows="6" placeholder="Describe the cat's personality, health, and what you're looking for in a new owner...">{{ old('description') }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button href="{{ route('listings.index') }}" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" icon="paper-airplane">Post Listing</flux:button>
                </div>
            </div>
        </form>
    </div>
</x-layouts::app>
