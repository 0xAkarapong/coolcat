<?php

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    #[Computed]
    public function cart()
    {
        if (Auth::check()) {
            return Cart::with("items.product")->firstOrCreate([
                "user_id" => Auth::id(),
            ]);
        }

        $sessionId = Session::getId();
        return Cart::with("items.product")->firstOrCreate([
            "user_id" => null,
            "session_id" => $sessionId,
        ]);
    }

    #[Computed]
    public function subtotal()
    {
        return $this->cart->items->sum(
            fn($item) => $item->price * $item->quantity,
        );
    }

    #[On("cart-updated")]
    public function refreshCart()
    {
        unset($this->cart);
        unset($this->subtotal);
    }

    #[On("add-to-cart")]
    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_active || $product->stock < 1) {
            return;
        }

        $item = $this->cart
            ->items()
            ->where("product_id", $product->id)
            ->first();

        if ($item) {
            if ($product->stock <= $item->quantity) {
                return;
            }

            $item->increment("quantity");
        } else {
            $this->cart->items()->create([
                "product_id" => $product->id,
                "quantity" => 1,
                "price" => $product->price,
            ]);
        }

        $this->dispatch("cart-updated");
        $this->modal('cart')->show();
    }

    public function removeItem($itemId)
    {
        $this->cart->items()->where("id", $itemId)->delete();
        $this->dispatch("cart-updated");
    }

    public function updateQuantity($itemId, $quantity)
    {
        if ($quantity < 1) {
            return;
        }

        $item = $this->cart->items()->find($itemId);
        if ($item && $item->product->stock >= $quantity) {
            $item->update(["quantity" => $quantity]);
            $this->dispatch("cart-updated");
        }
    }
};
?>

<div>
    <flux:modal name="cart" class="w-full sm:w-96" position="right">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="xl">Your Cart</flux:heading>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" size="sm" />
                </flux:modal.close>
            </div>

            <div class="flex-1 overflow-y-auto w-full">
                @if($this->cart->items->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-zinc-500">
                        <flux:icon name="shopping-cart" class="size-12 mb-4 opacity-50" />
                        <flux:text>Your cart is empty.</flux:text>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($this->cart->items as $item)
                        <div class="flex gap-4">
                            <div class="size-16 rounded bg-zinc-100 dark:bg-zinc-800 shrink-0 overflow-hidden">
                                @if($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex flex-col flex-1">
                                <flux:text class="font-medium truncate">{{ $item->product->name }}</flux:text>
                                <flux:text class="text-xs text-zinc-500">฿{{ number_format($item->price, 2) }}</flux:text>

                                <div class="flex items-center gap-2 mt-2">
                                    <flux:button variant="ghost" size="sm" icon="minus" class="size-6 p-0" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" />
                                    <flux:text class="text-sm w-4 text-center">{{ $item->quantity }}</flux:text>
                                    <flux:button variant="ghost" size="sm" icon="plus" class="size-6 p-0" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" />
                                    <div class="flex-1"></div>
                                    <flux:button variant="danger" size="sm" icon="trash" class="size-6 p-0" wire:click="removeItem({{ $item->id }})" />
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($this->cart->items->isNotEmpty())
            <div class="pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex justify-between items-center mb-4">
                    <flux:text class="font-medium">Subtotal</flux:text>
                    <flux:text class="font-bold text-lg">฿{{ number_format($this->subtotal, 2) }}</flux:text>
                </div>
                <flux:button variant="primary" class="w-full" href="{{ Route::has('orders.create') ? route('orders.create') : '#' }}">
                    Proceed to Checkout
                </flux:button>
            </div>
            @endif
        </div>
    </flux:modal>
</div>
