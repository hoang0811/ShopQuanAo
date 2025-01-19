<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Product;
use App\Models\Brand;

class HomeController extends Controller
{
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $slides = Slide::where('status',1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $sproducts = Product::whereNotNull('sale_price')
        ->where('sale_price', '<>', '')
        ->inRandomOrder()
        ->limit(8)
        ->get();
        $fproducts = Product::where('featured',1)->get()->take(8);
    return view('index',compact('slides','categories','brands','sproducts','fproducts'));
    }
    public function contact()
    {
        return view('contact');
    }
    public function contact_store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'comment'=>'required',
        ]);
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();

        return redirect()->back()->with('success', 'Thank you for your contact about me');
 
    }
}
