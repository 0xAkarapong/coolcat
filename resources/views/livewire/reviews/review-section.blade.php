<?php

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component
{
    public $reviewable;

    #[Validate('required|integer|min:1|max:5', message: 'Please select a rating.')]
    public $rating = 0;

    #[Validate('nullable|string|max:1000')]
    public $comment = '';

    public function mount($reviewable)
    {
        $this->reviewable = $reviewable;
    }

    public function submitReview()
    {
        $this->validate();

        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }

        // Prevent sellers from reviewing their own items
        if ($this->reviewable->user_id === Auth::id()) {
            session()->flash('error', 'You cannot review your own listing.');
            return;
        }

        // Check if user already reviewed this item
        if ($this->reviewable->reviews()->where('user_id', Auth::id())->exists()) {
            session()->flash('error', 'You have already reviewed this item.');
            return;
        }

        $this->reviewable->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->rating = 5;
        $this->comment = '';
        $this->reviewable->load('reviews.user');

        session()->flash('success', 'Review submitted successfully!');
    }

    public function getCanReviewProperty()
    {
        if (!Auth::check()) {
            return false;
        }
        
        if ($this->reviewable->user_id === Auth::id()) {
            return false;
        }

        return !$this->reviewable->reviews()->where('user_id', Auth::id())->exists();
    }
};
?>

<div class="mt-12 flex flex-col gap-6">
    <flux:heading size="xl">Customer Reviews</flux:heading>

    @if (session()->has('success'))
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Review Form --}}
    @if ($this->can_review)
        <form wire:submit="submitReview" 
              x-data="{ hoverRating: 0 }" 
              class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
            <flux:heading size="lg">Leave a Review</flux:heading>
            
            <div class="flex flex-col gap-1">
                <flux:label>Rating</flux:label>
                <div class="flex gap-2" x-data="{ rating: $wire.entangle('rating') }">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                @click="rating = {{ $i }}" 
                                @mouseenter="hoverRating = {{ $i }}" 
                                @mouseleave="hoverRating = 0"
                                class="focus:outline-none transition-transform hover:scale-110">
                            <flux:icon name="star"
                                class="size-6 transition-colors" 
                                x-bind:class="(hoverRating >= {{ $i }} || rating >= {{ $i }}) ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-300'" />
                        </button>
                    @endfor
                </div>
                @error('rating') <flux:error>{{ $message }}</flux:error> @enderror
            </div>

            <flux:textarea 
                wire:model="comment" 
                label="Your Comment" 
                placeholder="Share your thoughts about this product..." 
                rows="3"
            />
            
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Submit Review</flux:button>
            </div>
        </form>
    @elseif (Auth::guest())
        <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-600">
            <flux:text class="text-zinc-500">
                Please <flux:link href="{{ route('login') }}">log in</flux:link> to leave a review.
            </flux:text>
        </div>
    @endif

    {{-- Reviews List --}}
    @if ($reviewable->reviews->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 py-12 dark:border-zinc-600">
            <flux:text class="text-zinc-500">No reviews yet for this item.</flux:text>
        </div>
    @else
        <div class="grid gap-6">
            @foreach ($reviewable->reviews->sortByDesc('created_at') as $review)
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
                    @if($review->comment)
                        <flux:text>{{ $review->comment }}</flux:text>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
