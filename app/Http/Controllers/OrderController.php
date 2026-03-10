<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders (buyer views their orders, seller views orders containing their products).
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get orders where the user is the buyer
        $buyerOrders = Order::query()
            ->with(['items.product'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Get orders containing products owned by the user (as a seller)
        $sellerOrders = Order::query()
            ->with(['items.product', 'user'])
            ->whereHas('items.product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('orders.index', compact('buyerOrders', 'sellerOrders'));
    }

    /**
     * Place a new order (Checkout).
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        return DB::transaction(function () use ($data, $user) {
            $total = 0;
            $itemsToCreate = [];

            // Verify stock and calculate total
            foreach ($data['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['id']);

                if (! $product->is_active) {
                    abort(422, "Product {$product->name} is no longer available.");
                }

                if ($product->stock < $itemData['quantity']) {
                    abort(422, "Insufficient stock for {$product->name}. Only {$product->stock} remaining.");
                }

                // Prevent buying own products
                if ($product->user_id === $user->id) {
                    abort(422, "You cannot purchase your own product: {$product->name}.");
                }

                $subtotal = $product->price * $itemData['quantity'];
                $total += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $product->price,
                ];

                // Deduct stock
                $product->decrement('stock', $itemData['quantity']);
            }

            // Create Order
            $order = $user->orders()->create([
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'payment_ref' => $data['payment_ref'] ?? null,
                'total' => $total,
                'status' => 'pending',
            ]);

            // Create Order Items
            $order->items()->createMany($itemsToCreate);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
        });
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['user', 'items.product.user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Update order status (Seller action).
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return back()->with('success', 'Order status updated.');
    }
}
