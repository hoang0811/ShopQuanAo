<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{

    public function index()
    {
        $wishlistItems = Cart::instance('wishlist')->content();
        $productIds = $wishlistItems->pluck('id')->toArray();
        $products = Product::whereIn('id', $productIds)->get();
        foreach ($products as $product) {
            $product->discount_percentage = $this->calculateDiscountPercentage($product->regular_price, $product->sale_price);
        }

        return view('wishlist', compact('products', 'wishlistItems'));
    }
    private function calculateDiscountPercentage($regularPrice, $salePrice)
    {
        if ($regularPrice > 0 && $salePrice < $regularPrice) {
            return round((($regularPrice - $salePrice) / $regularPrice) * 100);
        }
        return 0;
    }
    public function add_to_wishlist(Request $request)
    {
        $product = Product::find($request->id);
        $item = Cart::instance('wishlist')->add($product->id, $product->name);
        return redirect()->back();
    }
    public function remove_wishlist($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }
}
