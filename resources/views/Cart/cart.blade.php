@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4 text-center">Your Cart</h1>
        <div class="row">
            @if(session('cart'))
                @foreach(session('cart') as $id => $details)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div id="carousel-cart-{{ $id }}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($details['images'] as $key => $image)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                            <img src="{{ Storage::url($image) }}" class="product-image-cart d-block w-100" alt="{{ $details['name'] }}">
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-cart-{{ $id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-cart-{{ $id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                            <div class="card-body-cart text-center">
                                <h5 class="card-title font-weight-bold">{{ $details['name'] }}</h5>
                                <p class="card-text text-muted">{{ $details['category'] }}</p>
                                <div class="d-flex justify-content-center align-items-center my-2">
                                    <form action="{{ route('cart.updateCart', ['id' => $id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="decrement" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">-</button>
                                    </form>
                                    <p class="card-text mx-2 mb-0">{{ $details['quantity'] }}</p>
                                    <form action="{{ route('cart.updateCart', ['id' => $id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="increment" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">+</button>
                                    </form>
                                </div>
                                <p class="card-text text-danger font-weight-bold">${{ $details['price'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center">Your cart is empty</p>
            @endif
        </div>
        @if(session('cart'))
            <div class="text-center mt-4">
                <form action="{{ route('cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-lg btn-success px-5">Checkout</button>
                </form>
            </div>
        @endif
    </div>
@endsection
