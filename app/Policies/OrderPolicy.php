<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Buyer can view their own order
        if ($user->id === $order->user_id) {
            return true;
        }

        // Sellers can view the order if it contains their products
        return $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
    }

    /**
     * Determine whether the user can update the model (e.g., change status).
     */
    public function update(User $user, Order $order): bool
    {
        // Only sellers involved in the order can update it
        // (In a real marketplace, an order might be split by seller. For simplicity here,
        // if a seller has products in this order, they can update the status.)
        return $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
    }
}
