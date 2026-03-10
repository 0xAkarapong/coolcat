<?php

namespace App\Http\Controllers;

use App\Models\CatListing;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredListings = CatListing::with('breed')->active()->latest()->take(3)->get();
        $featuredProducts = Product::active()->inStock()->latest()->take(4)->get();

        return view('welcome', compact('featuredListings', 'featuredProducts'));
    }
}
