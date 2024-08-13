@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Left Column: Customer, Payment, and Cart Summary -->
            <div class="col-md-4">
                <!-- Customer Selection -->
                <button type="button" id="customerButton" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#customerModal">
                    <i class="bi bi-person-circle"></i> Select Customer
                </button>

                <!-- Payment Method Selection -->
                <button type="button" id="paymentMethodButton" class="btn btn-secondary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                    <i class="bi bi-credit-card"></i> Select Payment Method
                </button>

                <!-- Cart Summary -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Cart Summary</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Total: <span id="cartTotal" class="fw-bold">₱{{ number_format($total, 2) }}</span></p>
                        <h6 class="mb-3">Product List</h6>
                        <ul class="list-group mb-3">
                            @forelse(session('cart') as $id => $details)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $details['brand'] }} | {{ $details['size'] }} | Quantity: {{ $details['quantity'] }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">No items in cart.</li>
                            @endforelse
                        </ul>
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Checkout</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart Items -->
            <div class="col-md-8">
                <h1 class="mb-4 text-center">Your Cart</h1>
                <div class="row">
                    @forelse(session('cart') as $id => $details)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div id="carousel-cart-{{ $id }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @forelse($details['images'] as $key => $image)
                                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                <img src="{{ Storage::url($image) }}" class="product-image-cart d-block w-100" alt="{{ $details['name'] }}">
                                            </div>
                                        @empty
                                            <div class="carousel-item active">
                                                <img src="{{ asset('images/newantipolotirecenter.jpg') }}" class="product-image-cart d-block w-100" alt="No Image Available">
                                            </div>
                                        @endforelse
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
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $details['brand'] }}</h5>
                                    <p class="card-text text-muted">{{ $details['size'] }} - {{ $details['category'] }}</p>
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
                                    <!-- Editable Price -->
                                    <form action="{{ route('cart.updatePrice', ['id' => $id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" step="0.01" name="price" value="{{ $details['price'] }}" class="form-control mb-2 text-center">
                                        <button type="submit" class="btn btn-primary btn-sm">Update Price</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center">Your cart is empty</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Select Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="customerSearch" class="form-control mb-3" placeholder="Search for customers...">
                    <div class="list-group" id="customerList">
                        @foreach($customers as $customer)
                            <button type="button" class="list-group-item list-group-item-action customer-item" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}">
                                <i class="bi bi-person"></i> {{ $customer->name }} - {{ $customer->address }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Modal -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentMethodModalLabel">Select Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="paymentSearch" class="form-control mb-3" placeholder="Search for payment methods...">
                    <div class="list-group" id="paymentList">
                        @foreach(['Cash', 'Bank', 'GCash', 'Other'] as $paymentMethod)
                            <button type="button" class="list-group-item list-group-item-action payment-item" data-method="{{ $paymentMethod }}">
                                <i class="bi bi-credit-card"></i> {{ $paymentMethod }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal for Stock Limit Reached -->
    @if(session('error'))
        <div id="quantityLimitModal" class="modal fade show" tabindex="-1" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quantity Limit Reached</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ session('error') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
