<?php

namespace App\Http\Controllers;

use App\Models\CatInquiry;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Metrics
        $activeListings = $user->catListings()->active()->count();
        $activeProducts = $user->products()->active()->count();

        // Unread inquiries on user's listings
        $unreadInquiries = CatInquiry::whereHas('listing', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'open')->count();

        // Recent Sales
        $recentSales = Order::with('user', 'items.product')
            ->whereHas('items.product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'activeListings',
            'activeProducts',
            'unreadInquiries',
            'recentSales'
        ));
    }
}
