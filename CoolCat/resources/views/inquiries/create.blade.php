<x-layouts::app :title="'Inquire about ' . $listing->name">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('listings.index') }}">Listings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('listings.show', $listing) }}">{{ $listing->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Inquire</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="xl">Send Inquiry to {{ $listing->user->name }}</flux:heading>

        <form method="POST" action="{{ route('listings.inquiries.store', $listing) }}" class="flex flex-col gap-6">
            @csrf

            <flux:field>
                <flux:label>Message</flux:label>
                <flux:textarea name="message" rows="5" placeholder="Hi, I'm interested in this cat! When is a good time to meet?">{{ old('message') }}</flux:textarea>
                <flux:error name="message" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Proposed Meet Date (Optional)</flux:label>
                    <flux:input type="date" name="meet_date" :value="old('meet_date')" min="{{ now()->addDay()->toDateString() }}" />
                    <flux:error name="meet_date" />
                </flux:field>

                <flux:field>
                    <flux:label>Proposed Meet Time (Optional)</flux:label>
                    <flux:input type="time" name="meet_time" :value="old('meet_time')" />
                    <flux:error name="meet_time" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Proposed Location (Optional)</flux:label>
                <flux:input type="text" name="meet_location" :value="old('meet_location')" placeholder="e.g. Central Park" />
                <flux:error name="meet_location" />
            </flux:field>

            <div class="flex space-x-2">
                <flux:button type="submit" variant="primary">Send Inquiry</flux:button>
                <flux:button variant="ghost" href="{{ route('listings.show', $listing) }}">Cancel</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
