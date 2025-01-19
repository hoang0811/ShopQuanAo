@extends('layouts.app')
@push('styles')
    <style>
        .my-account__address-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            position: relative;
        }

        .my-account__address-item__title h5 {
            margin: 0;
            font-size: 1.1rem;
        }

        .my-account__address-item__detail p {
            margin: 5px 0;
        }

        .my-account__address-item .btn {
            margin-left: 5px;
        }
    </style>
@endpush
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Addresses</h2>
            <div class="row">
                <div class="col-lg-3">
                    @include('user.account-nav')
                </div>
                <div class="col-lg-9">
                    <div class="page-content my-account__address">
                        <div id="add-address-form" style="display:none;">
                            <h5>Add New Address</h5>
                            <form id="addressForm" action="{{ route('user.address.store') }}" method="POST">
                                @csrf
                                <div class="row mt-5">
                                  <div class="col-md-6">
                                      <div class="form-floating my-3">
                                          <input type="hidden" name="selected_address" id="selected_address" value="">
                                          <input type="text" class="form-control" name="name" id="name"
                                              value="{{ old('name', $address->name ?? '') }}">
                                          <label for="name">Full Name *</label>
                                          <span class="text-danger" id="name-error"></span> 
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="phone" id="phone"
                                              value="{{ old('phone', $address->phone ?? '') }}">
                                          <label for="phone">Phone Number *</label>
                                          <span class="text-danger" id="phone-error"></span> 
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="zip" id="zip"
                                              value="{{ old('zip', $address->zip ?? '') }}">
                                          <label for="zip">Pincode *</label>
                                          <span class="text-danger" id="zip-error"></span> 
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <div class="form-floating mt-3 mb-3">
                                          <input type="text" class="form-control" name="state" id="state"
                                              value="{{ old('state', $address->state ?? '') }}">
                                          <label for="state">State *</label>
                                          <span class="text-danger" id="state-error"></span> 
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="city" id="city"
                                              value="{{ old('city', $address->city ?? '') }}">
                                          <label for="city">Town / City *</label>
                                          <span class="text-danger" id="city-error"></span>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="address" id="address"
                                              value="{{ old('address', $address->address ?? '') }}">
                                          <label for="address">House no, Building Name *</label>
                                          <span class="text-danger" id="address-error"></span>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="locality" id="locality"
                                              value="{{ old('locality', $address->locality ?? '') }}">
                                          <label for="locality">Road Name, Area, Colony *</label>
                                          <span class="text-danger" id="locality-error"></span> 
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-floating my-3">
                                          <input type="text" class="form-control" name="landmark" id="landmark"
                                              value="{{ old('landmark', $address->landmark ?? '') }}">
                                          <label for="landmark">Landmark *</label>
                                          <span class="text-danger" id="landmark-error"></span> 
                                      </div>
                                  </div>
                              </div>
                                <button type="submit" class="btn btn-primary">Save Address</button>
                            </form>
                            
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="notice">The following addresses will be used on the checkout page by default.</p>
                            </div>
                            <div class="col-6 text-right">
                                <a href="#" class="btn btn-sm btn-info" id="add-address-btn">Add New</a>
                            </div>
                        </div>
                        <div class="my-account__address-list row">
                            <h5>Shipping Address</h5>
                            <div class="d-flex flex-wrap">
                                @foreach ($addresses as $address)
                                    <div class="my-account__address-item col-md-4 mb-4">
                                      <div class="my-account__address-item__title d-flex justify-content-between align-items-center">
                                        <h5>{{ $address->name }}
                                            </h5>
                                        
                                            <div>
                                                @if ($address->isDefault == '1')
                                                    <input type="checkbox" checked disabled>
                                                @else
                                                    <form action="{{ route('address.default', $address->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        <input type="checkbox" name="setDefault" value="1"
                                                            onchange="this.form.submit()">
                                                    </form>
                                                @endif
                                                <a href="#" class="btn btn-sm btn-secondary">Edit</a>
                                                <form action="{{ route('address.delete', $address->id) }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>

                                            </div>
                                        </div>
                                        <div class="my-account__address-item__detail">
                                          <p>
                                            @if ($address->isDefault == '1')
                                                <p>(Default)</p>
                                            @endif
                                        </p>
                                            <p><strong>Address : </strong>{{ $address->address }}</p>
                                            <p><strong>Locality : </strong>{{ $address->locality }}</p>
                                            <p><strong>City : </strong>{{ $address->city }}, {{ $address->country }}</p>
                                            <p><strong>Landmark : </strong>{{ $address->landmark }}</p>
                                            <p><strong>Zip Code : </strong>{{ $address->zip }}</p>
                                            <p><strong>Mobile : </strong> {{ $address->phone }}</p>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <hr>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
@endsection
@push('scripts')
    <script>
        document.getElementById('add-address-btn').addEventListener('click', function(event) {
            event.preventDefault(); // Ngăn không cho trang reload
            var form = document.getElementById('add-address-form');
            if (form.style.display === 'none') {
                form.style.display = 'block'; // Hiển thị form
            } else {
                form.style.display = 'none'; // Ẩn form
            }
        });

        // Sử dụng AJAX để submit form và xử lý lỗi tại chỗ
        document.getElementById('addressForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var form = this;
            var formData = new FormData(form);

            // Xóa hết lỗi trước khi kiểm tra lại
            document.querySelectorAll('.text-danger').forEach(function(element) {
                element.innerText = '';
            });

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    // Hiển thị lỗi lên form
                    for (var field in data.errors) {
                        document.getElementById(`${field}-error`).innerText = data.errors[field][0];
                    }
                } else {
                    // Xử lý thành công, reset form
                    form.reset();
                    form.style.display = 'none';
                    alert('Address added successfully!');
                    window.location.reload(); // Tải lại trang để cập nhật danh sách địa chỉ
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
@endpush
