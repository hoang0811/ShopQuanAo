<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Session\Session as SessionSession;

class CartController extends Controller
{
    //

    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }
    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        $this->calculateDiscount();
        return redirect()->back();
    }
    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        $this->calculateDiscount();
        return  redirect()->back();
    }
    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        if (Cart::instance('cart')->count() == 0) {
            $this->remove_coupon();
        }
        $this->calculateDiscount();

        return  redirect()->back();
    }
    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        if (Cart::instance('cart')->count() == 0) {
            $this->remove_coupon();
        }
        $this->calculateDiscount();
        return  redirect()->back();
    }
    public function empty_card()
    {
        Cart::instance('cart')->destroy();
        if (Cart::instance('cart')->count() == 0) {
            $this->remove_coupon();
        }
        return  redirect()->back();
    }
    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())->first();
            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid coupon code !');
            } else {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon has been applied !');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid coupon code !');
        }
    }
    public function calculateDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }
            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),

            ]);
        }
    }
    public function remove_coupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon has been removed !');
    }
    public function checkout()
    {

        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user_id = Auth::user()->id;
        $addresses = Address::where('user_id', $user_id)->get();
    return view('checkout', compact('addresses'));
    }
  
    public function place_an_order(Request $request)
    {
        if (Cart::instance('cart')->count() == 0) {
            return redirect()->route('home.index');
        }
        $user_id = Auth::user()->id;
        $selected_address_id = $request->input('selected_address');
        
        $address = Address::where('id', $selected_address_id)->where('user_id', $user_id)->first();
        
        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'locality' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'landmark' => 'required',
                'zip' => 'required|numeric|digits:6',
            ]);
            
            // Create a new address
            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->locality = $request->locality;
            $address->address = $request->address;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->landmark = $request->landmark;
            $address->zip = $request->zip;
            $address->user_id = $user_id;
            $address->country = 'Vietnam'; // Adjust as necessary
            $address->save();
        }
        $this->setAmountforCheckout();
        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = (float) str_replace(',', '', Session::get('checkout')['subtotal']);
        $order->discount = (float) str_replace(',', '', Session::get('checkout')['discount']);
        $order->tax = (float) str_replace(',', '', Session::get('checkout')['tax']);
        $order->total = (float) str_replace(',', '', Session::get('checkout')['total']);
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();


        foreach (Cart::instance('cart')->content() as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
        }


        if ($request->mode == "momo") {
            return $this->processMoMoPayment($order);
           
        } elseif ($request->mode == "vnpay") {
            $vnpayUrl = $this->createVNPayPaymentUrl($order);
            return redirect($vnpayUrl);
        }
        
        else {
           
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
        }

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }
    public function processMoMoPayment(Order $order)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        
        $orderInfo = "Thanh toán đơn hàng #" . $order->id;
        $amount = (string) $order->total; // Ensure this is a string
        $orderId = time() . "";
        $redirectUrl = route('cart.order.confirmation');
        $ipnUrl = route('cart.order.confirmation'); ;
    
        $requestId = time() . "";
        $requestType = "payWithATM";
        $extraData = ""; // Additional data if needed
    
        // Create the raw hash for signature
        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];
        $result = $this->execPostRequest($endpoint, json_encode($data));
       
        $jsonResult = json_decode($result, true);
        
    
        if (isset($jsonResult['payUrl'])) {
            return redirect($jsonResult['payUrl']);
        } else {
            // Handle the error case here
            return back()->withErrors(['msg' => 'Failed to initiate MoMo payment.']);
        }
    }
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order_confirmation', compact('order'));
        }
        return view('cart.index');
    }
    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        }
        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }
    public function createVNPayPaymentUrl($order)
    {
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');
    
       
        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = "Thanh toán đơn hàng " . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; // Mặc định là NCB, có thể đổi tùy ý
        $vnp_IpAddr = request()->ip(); // Lấy IP của khách hàng
    
        // Mảng dữ liệu gửi tới VNPay
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
    
        // Thêm mã ngân hàng nếu có
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
    
        ksort($inputData); // Sắp xếp mảng theo thứ tự a-z
        $hashdata = "";
        $query = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
    
        // Tạo URL thanh toán với chữ ký bảo mật
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
    
        // Trả về URL thanh toán VNPay
        return $vnp_Url;
    }
    
    public function vnpayReturn(Request $request)
    {
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $user_id = Auth::user()->id;
        $vnp_Amount = $request->input('vnp_Amount') / 100;
    
        if ($vnp_ResponseCode == '00') {
            $order = Order::find($vnp_TxnRef);
            if ($order && $order->total == $vnp_Amount) {
                $order->status = 'ordered';
                $order->save();
    
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->order_id = $order->id;
                $transaction->mode = "vnpay";
                $transaction->status = "approved";
                $transaction->save();

                Cart::instance('cart')->destroy();
                Session::forget('checkout');
                Session::forget('coupon');
                Session::forget('discounts');
                Session::put('order_id', $order->id);
    
                return redirect()->route('cart.order.confirmation')->with('success', 'Thanh toán thành công!');
            }
        }
    
        return redirect()->route('cart.index')->with('error', 'Thanh toán không thành công.');
    }
 
}
