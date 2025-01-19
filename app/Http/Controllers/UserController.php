<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }
    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }
    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order->id)->first();
            return view('user.order_details', compact('order', 'orderItems', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }
    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "canceled";
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', "Order has been canceled successfully !");
    }
    public function addresses()
    {
        $addresses =  Address::where('user_id', Auth::user()->id)->get();
        return view('user.addresses', compact('addresses'));
    }

    public function address_default($id)
    {
        Address::where('user_id', Auth::id())->update(['isDefault' => false]);

        $address = Address::find($id);
        if ($address) {
            $address->isDefault = 1;
            $address->save();
        }

        return redirect()->back()->with('success', 'Default address updated successfully.');
    }
    public function address_remove($id)
    {
        $address = Address::find($id);
        $address->delete();
        return redirect()->back()->with('success', 'Address deleted successfully.');
    }
    public function address_store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:10',
            'locality' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'landmark' => 'required',
            'zip' => 'required|numeric|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $address = new Address();
    $address->name = $request->name;
    $address->phone = $request->phone;
    $address->locality = $request->locality;
    $address->address = $request->address;
    $address->city = $request->city;
    $address->state = $request->state;
    $address->landmark = $request->landmark;
    $address->zip = $request->zip;
    $address->country = "Viet Nam";
    $address->user_id = Auth::id();
    $address->isdefault = 0;
    $address->save();

    return response()->json(['success' => 'Address added successfully!']);
}

}
