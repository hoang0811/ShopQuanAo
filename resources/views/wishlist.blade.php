@extends('layouts.app')
@push('styles')
    <style>
        .product-card {
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card::before {
            content: '';
            position: absolute;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        }

        .product-card:hover::before {
            opacity: 1;
        }
    </style>
@endpush
@section('content')
    <section class="products-grid container">
        <h2 class="section-title text-center mb-3 pb-xl-3 mb-xl-4">Wishlist Products</h2>

        <div class="row">
            @foreach ($wishlistItems as $item)
            @php
                $product = $products->firstWhere('id', $item->id);
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card product-card_style3 mb-3 mb-md-4 mb-xxl-5">
                    <div class="pc__img-wrapper">
                        <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                            <img loading="lazy" src="{{ asset('uploads/products') }}/{{ $product->image }}" width="330"
                                height="400" alt="{{ $product->name }}" class="pc__img">
                        </a>
                        @if ($product->sale_price)
                            <div class="pc-labels position-absolute top-0 start-0 w-100 d-flex justify-content-between">
                                <div class="pc-labels__right ms-auto">
                                    <span class="pc-label pc-label_sale d-block text-white">-
                                        {{ $product->discount_percentage }}%</span>
                                </div>
                            </div>
                        @endif
                        @if ($product->quantity == 0)
                            <div class="pc-labels position-absolute top-0 start-0 w-100 d-flex justify-content-between">
                                <div class="pc-labels__right ms-auto">
                                    <span class="pc-label pc-label_sale d-block text-white">Sold Out</span>
                                </div>
                            </div>
                        @endif
                    </div>
        
                    <div class="pc__info position-relative">
                        <h6 class="pc__title">{{ $product->name }}</h6>
                        <div class="product-card__price d-flex align-items-center">
                            @if ($product->sale_price)
                                <span class="money price price-old">${{ $product->regular_price }}</span>
                                <span class="money price price-sale">${{ $product->sale_price }}</span>
                            @else
                                <span class="money price">{{ $product->regular_price }}</span>
                            @endif
        
                            <form action="{{ route('wishlist.remove', ['rowId' => $item->rowId]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist" title="Remove From Wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        

        </div><!-- /.row -->

        <div class="text-center mt-2">
            <a class="btn-link btn-link_lg default-underline text-uppercase fw-medium" href="#">Load More</a>
        </div>
    </section>
@endsection
