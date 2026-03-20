<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('status', 'active')
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $featuredCategories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->take(6)
            ->get();

        return view('home', compact('featuredProducts', 'featuredCategories'));
    }
    // ***_ privacy policy and termofserv and faq _***
    public function policy()
    {
        return view('footer_links.Policy');
    }
    public function termsofservice()
    {
        return view('footer_links.TermsOfServ');
    }
    public function faq()
    {
        return view('footer_links.FAQ');
    }


}
