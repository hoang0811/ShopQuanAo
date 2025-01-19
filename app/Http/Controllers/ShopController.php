<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
{

    $size = $request->query('size') ? $request->query('size') : 12;
    $o_column = "";
    $o_order = "";
    $order = $request->query('order') ? $request->query('order') : -1;
    $f_brands = $request->query('brands','');
    $f_categories = $request->query('categories','');



    switch ($order) {
        case 1:
            $o_column = 'created_at';
            $o_order = 'desc';
            break;
        case 2:
            $o_column = 'created_at';
            $o_order = 'asc';
            break;
        case 3:
            $o_column = 'sale_price';
            $o_order = 'asc';
            break;
        case 4:
            $o_column = 'sale_price';
            $o_order = 'desc';
            break;
        case 5:
            $o_column = 'name';
            $o_order = 'asc';
            break;
        case 6:
            $o_column = 'name';
            $o_order = 'desc';
            break;
        default:
            $o_column = 'id';
            $o_order = 'desc';
            
    }


    $brands = Brand::orderBy('name', 'asc')->get();
    $categories = Category::orderBy('name', 'asc')->get();
    $products = Product::when($f_brands, function($query) use($f_brands) {
        return $query->whereIn('brand_id', explode(',', $f_brands));
    })
    ->when($f_categories, function($query) use($f_categories) {
        return $query->whereIn('category_id', explode(',', $f_categories));
    })
    ->orderBy($o_column, $o_order)
    ->paginate($size);

    foreach ($products as $product) {
        $product->discount_percentage = $this->calculateDiscountPercentage($product->regular_price, $product->sale_price);
    }

    return view('shop', compact('products', 'size', 'order','brands','f_brands','categories','f_categories'));
}

    private function calculateDiscountPercentage($regularPrice, $salePrice)
    {
        if ($regularPrice > 0 && $salePrice < $regularPrice) {
            return round((($regularPrice - $salePrice) / $regularPrice) * 100);
        }
        return 0;
    }
    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'products'));
    }
   
}
