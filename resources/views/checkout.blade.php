@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Shipping and Checkout</h2>
            <div class="checkout-steps">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div>
            <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <div class="row">
                            <div class="col-6">
                                <h4>SHIPPING DETAILS</h4>
                            </div>
                            <div class="form-group">
                                <label for="address-select">Select Address</label>
                                <select id="address-select" class="form-control">
                                    <option value="">-- Choose Address --</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}">{{ $address->name }}, {{ $address->address }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="hidden" name="selected_address" id="selected_address" value="">
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', '') }}">
                                    <label for="name">Full Name *</label>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', '') }}">
                                    <label for="phone">Phone Number *</label>
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="zip" id="zip" value="{{ old('zip', '') }}">
                                    <label for="zip">Pincode *</label>
                                    @error('zip')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mt-3 mb-3">
                                    <input type="text" class="form-control" name="state" id="state" value="{{ old('state', '') }}">
                                    <label for="state">State *</label>
                                    @error('state')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="city" id="city" value="{{ old('city', '') }}">
                                    <label for="city">Town / City *</label>
                                    @error('city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address', '') }}">
                                    <label for="address">Address *</label>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="locality" id="locality" value="{{ old('locality', '') }}">
                                    <label for="locality">Road Name, Area, Colony *</label>
                                    @error('locality')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="landmark" id="landmark" value="{{ old('landmark', '') }}">
                                    <label for="landmark">Landmark *</label>
                                    @error('landmark')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout__totals-wrapper">
                        <div class="sticky-content">
                            <div class="checkout__totals">
                                <h3>Your Order</h3>
                                <table class="checkout-cart-items">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT</th>
                                            <th class="text-right">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Cart::instance('cart')->content() as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }} x {{ $item->qty }}
                                                </td>
                                                <td class="text-right">
                                                    ${{ $item->subtotal() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table class="checkout-totals">
                                    @if (Session::has('discounts'))
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Discount ({{ Session::get('coupon')['code'] }})</th>
                                                <td class="text-right">${{ Session::get('discounts')['discount'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal After</th>
                                                <td class="text-right">${{ Session::get('discounts')['subtotal'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td class="text-right">Free shipping</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td class="text-right">${{ Session::get('discounts')['tax'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td class="text-right">${{ Session::get('discounts')['total'] }}</td>
                                            </tr>
                                        </tbody>
                                    @else
                                        <tbody>
                                            <tr>
                                                <th>SUBTOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>SHIPPING</th>
                                                <td class="text-right">Free shipping</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td class="text-right">${{ Cart::instance('cart')->tax() }}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->total() }}</td>
                                            </tr>
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                            <div class="checkout__payment-methods">
                                
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        value="cod" id="checkout_payment_method_1" checked>
                                    <label class="form-check-label" for="checkout_payment_method_1">
                                        Cash on delivery
                                    </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                            value="momo" id="checkout_payment_method_2" >
                                        <label class="form-check-label" for="checkout_payment_method_2">
                                            MoMo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                            value="vnpay" id="checkout_payment_method_3">
                                        <label class="form-check-label" for="checkout_payment_method_3">
                                            VNPay
                                        </label>
                                    </div>
                                
                                    <div class="policy-text">
                                        Your personal data will be used to process your order, support your experience
                                        throughout this
                                        website, and for other purposes described in our <a href="terms.html"
                                            target="_blank">privacy
                                            policy</a>.
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-checkout">PLACE ORDER</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection
@push('scripts')
<script>
   document.getElementById('address-select').addEventListener('change', function() {
    var addresses = @json($addresses);
    var selectedId = this.value;
    
    // Điền thông tin địa chỉ vào biểu mẫu
    var selectedAddress = addresses.find(address => address.id == selectedId);
    if (selectedAddress) {
        document.getElementById('name').value = selectedAddress.name;
        document.getElementById('phone').value = selectedAddress.phone;
        document.getElementById('zip').value = selectedAddress.zip;
        document.getElementById('state').value = selectedAddress.state;
        document.getElementById('city').value = selectedAddress.city;
        document.getElementById('address').value = selectedAddress.address;
        document.getElementById('locality').value = selectedAddress.locality;
        document.getElementById('landmark').value = selectedAddress.landmark;

        // Cập nhật giá trị của input ẩn với ID địa chỉ đã chọn
        document.getElementById('selected_address').value = selectedId; // Đảm bảo input này tồn tại và chính xác
    } else {
        // Nếu không có địa chỉ nào được chọn, làm sạch các trường
        document.getElementById('name').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('zip').value = '';
        document.getElementById('state').value = '';
        document.getElementById('city').value = '';
        document.getElementById('address').value = '';
        document.getElementById('locality').value = '';
        document.getElementById('landmark').value = '';
        document.getElementById('selected_address').value = ''; // Xóa input ẩn nếu không có địa chỉ nào được chọn
    }
});
</script>
@endpush