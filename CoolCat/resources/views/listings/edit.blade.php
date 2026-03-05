<x-layouts::app :title="'Edit: ' . $listing->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('listings.show', $listing) }}">{{ $listing->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Edit</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="xl">Edit Listing</flux:heading>

        <form method="POST" action="{{ route('listings.update', $listing) }}" enctype="multipart/form-data"
            class="grid gap-6 lg:grid-cols-2">
            @csrf
            @method('PUT')

            {{-- Cat Details --}}
            <div class="flex flex-col gap-5 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="sm">Cat Details</flux:heading>

                <flux:field>
                    <flux:label>Cat Name <span class="text-red-500">*</span></flux:label>
                    <flux:input name="name" value="{{ old('name', $listing->name) }}" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Breed</flux:label>
                    <flux:select name="breed_id">
                        <flux:select.option value="">Unknown / Mixed</flux:select.option>
                        @foreach ($breeds as $breed)
                            <flux:select.option value="{{ $breed->id }}"
                                :selected="old('breed_id', $listing->breed_id) == $breed->id">
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
                            <flux:select.option value="unknown" :selected="old('gender', $listing->gender) === 'unknown'">Unknown</flux:select.option>
                            <flux:select.option value="male" :selected="old('gender', $listing->gender) === 'male'">Male</flux:select.option>
                            <flux:select.option value="female" :selected="old('gender', $listing->gender) === 'female'">Female</flux:select.option>
                        </flux:select>
                        <flux:error name="gender" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Birthdate</flux:label>
                        <flux:input type="date" name="birthdate"
                            value="{{ old('birthdate', $listing->birthdate?->format('Y-m-d')) }}" />
                        <flux:error name="birthdate" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Color</flux:label>
                    <flux:input name="color" value="{{ old('color', $listing->color) }}" />
                    <flux:error name="color" />
                </flux:field>

                <div class="flex gap-6">
                    <flux:field>
                        <flux:checkbox name="is_neutered" value="1"
                            :checked="old('is_neutered', $listing->is_neutered)" />
                        <flux:label>Neutered</flux:label>
                    </flux:field>
                    <flux:field>
                        <flux:checkbox name="is_vaccinated" value="1"
                            :checked="old('is_vaccinated', $listing->is_vaccinated)" />
                        <flux:label>Vaccinated</flux:label>
                    </flux:field>
                </div>

                {{-- Current Image --}}
                @if ($listing->image)
                    <div>
                        <flux:text class="mb-2 text-xs text-zinc-500">Current photo</flux:text>
                        <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->name }}"
                            class="h-32 w-32 rounded-lg object-cover">
                    </div>
                @endif

                <flux:field>
                    <flux:label>{{ $listing->image ? 'Replace Photo' : 'Photo' }}</flux:label>
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
                            <flux:select.option value="adoption" :selected="old('type', $listing->type) === 'adoption'">Adoption</flux:select.option>
                            <flux:select.option value="sale" :selected="old('type', $listing->type) === 'sale'">For Sale</flux:select.option>
                        </flux:select>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Price (฿)</flux:label>
                        <flux:input type="number" name="price"
                            value="{{ old('price', $listing->price) }}" min="0" step="0.01" />
                        <flux:error name="price" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select name="status">
                        <flux:select.option value="active" :selected="old('status', $listing->status) === 'active'">Active</flux:select.option>
                        <flux:select.option value="reserved" :selected="old('status', $listing->status) === 'reserved'">Reserved</flux:select.option>
                        <flux:select.option value="sold" :selected="old('status', $listing->status) === 'sold'">Sold</flux:select.option>
                        <flux:select.option value="closed" :selected="old('status', $listing->status) === 'closed'">Closed</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>Province</flux:label>
                    <flux:input name="province" value="{{ old('province', $listing->province) }}" />
                    <flux:error name="province" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea name="description" rows="6">{{ old('description', $listing->description) }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button href="{{ route('listings.show', $listing) }}" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Save Changes</flux:button>
                </div>
            </div>
        </form>
    </div>
</x-layouts::app>
