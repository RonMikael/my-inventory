@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for Customer, Payment, Cart Summary -->
            <div class="col-md-3 bg-light p-3">

                <!-- Branch Selector Dropdown -->
                <div class="dropdown mb-3">
                    <button class="btn btn-info w-100 rounded-pill shadow-sm d-flex align-items-center justify-content-between px-4 py-2 dropdown-toggle" type="button" id="branchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-building fs-4 me-2"></i> 
                        <span id="selectedBranch" class="fw-semibold">Select Branch</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="branchDropdown" id="branchList">
                        @foreach($branches as $branch)
                            <li><a class="dropdown-item branch-item" href="#" data-id="{{ $branch->id }}" data-name="{{ $branch->name }}">
                                <i class="bi bi-building text-primary"></i> {{ $branch->name }} - {{ $branch->location }}
                            </a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Customer Selection -->
                <button type="button" id="customerButton" class="btn btn-primary w-100 mb-3 rounded-pill shadow-sm d-flex align-items-center justify-content-between px-4 py-2" data-bs-toggle="modal" data-bs-target="#customerModal">
                    <i class="bi bi-person-circle fs-4 me-2"></i> 
                    <span class="fw-semibold">Select Customer</span>
                </button>

                <!-- Payment Method Selection -->
                <button type="button" id="paymentMethodButton" class="btn btn-secondary w-100 mb-3 rounded-pill shadow-sm d-flex align-items-center justify-content-between px-4 py-2" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                    <i class="bi bi-credit-card fs-4 me-2"></i> 
                    <span class="fw-semibold">Select Payment Method</span>
                </button>

                <!-- Cart Summary -->
            <div class="card mb-4 shadow-lg border-light">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cart Summary</h5>
                    <span class="badge bg-warning text-dark">{{ count(session('cart')) }} items</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="card-text mb-0">Total:</p>
                        <h4 class="card-title mb-0 fw-bold text-success">₱{{ number_format($total, 2) }}</h4>
                    </div>

                    <!-- Freebies Selection -->
                    <div class="mb-3">
                        <label for="freebieDropdown" class="form-label">Freebie</label>
                        <select id="freebieDropdown" class="form-select">
                            <option value="none">None</option>
                            @foreach($freebies as $freebie)
                                <option value="{{ $freebie->id }}">{{ $freebie->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Discount Selection -->
                    <div class="mb-3">
                        <label for="discountDropdown" class="form-label">Discount</label>
                        <select id="discountDropdown" class="form-select">
                            <option value="none">None</option>
                            <option value="amount">Amount</option>
                            <option value="percentage">Percentage</option>
                        </select>
                        <div id="discountValue" class="mt-2 d-none">
                            <label for="discountInput" class="form-label">Discount Value</label>
                            <input type="number" id="discountInput" class="form-control" placeholder="Enter discount value">
                        </div>
                    </div>

                    <h6 class="mb-4">Product List</h6>
                    <ul class="list-group list-group-flush mb-4">
                        @forelse(session('cart') as $id => $details)
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0">
                                <div>
                                    <h6 class="mb-1">{{ $details['brand'] }}</h6>
                                    <p class="mb-0 text-muted">{{ $details['size'] }} | Qty: {{ $details['quantity'] }}</p>
                                </div>
                                <span class="badge bg-light text-dark">₱{{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted border-0">No items in cart.</li>
                        @endforelse
                    </ul>
                    <form action="{{ route('cart.checkout') }}" method="POST" class="d-grid">
                        @csrf
                        <input type="hidden" name="freebie_id" id="freebieInput" value="none">
                        <input type="hidden" name="discount_type" id="discountTypeInput" value="none">
                        <input type="hidden" name="discount_value" id="discountValueInput" value="0">
                        <button type="submit" class="btn btn-primary btn-lg">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
            </div>

            <!-- Main Content: Cart Items -->
            <div class="col-md-9">
            <div class="d-flex flex-column align-items-center mb-4">
                <h1 class="text-center text-dark fw-bold mb-2">Shopping Cart</h1>
                <p class="text-center text-muted fs-5">Review and manage your selected items</p>
                <div class="border-top pt-2" style="width: 60%;"></div>
            </div>
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
                        <p class="text-center text-muted">Your cart is empty</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-lg shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title text-dark" id="customerModalLabel">Select Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="text" id="customerSearch" class="form-control mb-3" placeholder="Search for customers..." style="border-radius: 0.375rem;">
                    <div class="list-group" id="customerList">
                        @foreach($customers as $customer)
                            <button type="button" class="list-group-item list-group-item-action border-0 rounded-3 shadow-sm mb-2 customer-item" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}">
                                <i class="bi bi-person text-primary"></i> {{ $customer->name }} - {{ $customer->address }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Modal -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-lg shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title text-dark" id="paymentMethodModalLabel">Select Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="text" id="paymentSearch" class="form-control mb-3" placeholder="Search for payment methods..." style="border-radius: 0.375rem;">
                    <div class="list-group" id="paymentList">
                        @foreach(['Cash', 'Bank', 'GCash', 'Other'] as $paymentMethod)
                            <button type="button" class="list-group-item list-group-item-action border-0 rounded-3 shadow-sm mb-2 payment-item" data-method="{{ $paymentMethod }}">
                                <i class="bi bi-credit-card text-primary"></i> {{ $paymentMethod }}
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
                <div class="modal-content border-0 rounded-lg shadow-lg">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title text-danger">Quantity Limit Reached</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted">{{ session('error') }}</p>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
