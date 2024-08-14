@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="my-4 text-center text-dark display-4">Product Catalog</h1>

        @if (session('success'))
            <div class="alert alert-success text-center mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="bg-light p-4 rounded shadow-sm">
                    <h4 class="mb-3 text-primary">Categories</h4>
                    <button class="btn btn-outline-primary w-100 mb-3" type="button" data-bs-toggle="collapse"
                        data-bs-target="#categoryCollapse" aria-expanded="false" aria-controls="categoryCollapse">
                        Filter Categories
                    </button>
                    <div class="collapse show" id="categoryCollapse">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('product.index') }}"
                                class="list-group-item list-group-item-action {{ request('category_id') ? '' : 'active' }}">
                                All CATEGORIES
                            </a>
                            @foreach ($categories as $category)
                                <a href="{{ route('product.index', ['category_id' => $category->id]) }}"
                                    class="list-group-item list-group-item-action {{ request('category_id') == $category->id ? 'active' : '' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="col-md-9">
                <input type="text" id="searchInputProduct" class="form-control form-control-lg mb-4"
                    placeholder="Search Brands, Sizes or Categories..."
                    style="border-radius: 30px; font-size: 1.25rem; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                <div class="row" id="productList">
                    @foreach ($products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4 product-item">
                            <div class="card h-100 shadow-sm border-0 rounded-lg">
                                <div id="carousel-{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner rounded-top">
                                        @if ($product->images->isEmpty())
                                            <div class="carousel-item active">
                                                <img src="{{ asset('images/newantipolotirecenter.jpg') }}"
                                                    class="d-block w-100" alt="No Image Available"
                                                    style="height: 180px; object-fit: cover;">
                                            </div>
                                        @else
                                            @foreach ($product->images as $key => $image)
                                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                    <img src="{{ Storage::url($image->image_path) }}" class="d-block w-100"
                                                        alt="{{ $product->name }}"
                                                        style="height: 180px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon bg-dark rounded-circle"
                                            aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon bg-dark rounded-circle"
                                            aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">{{ $product->brand }}</h5>
                                    <p class="card-text text-muted mb-1">Category: {{ $product->category->name }}</p>
                                    <p class="card-text">Size: <strong>{{ $product->size }}</strong></p>
                                    <p class="card-text text-success">Price: ₱{{ number_format($product->price, 2) }}</p>
                                    <p class="card-text">
                                        Stock:
                                        @if ($product->stocks->sum('quantity') > 0)
                                            <span class="badge bg-success">{{ $product->stocks->sum('quantity') }}
                                                units</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </p>
                                    <form action="{{ route('cart.addToCart', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100 mt-3"
                                            @if ($product->stocks->sum('quantity') == 0) disabled @endif>Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
