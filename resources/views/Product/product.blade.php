@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4 text-center">Products</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <input type="text" id="searchInputProduct" class="form-control" placeholder="Search products or categories..." style="margin-bottom: 20px;">
            <a href="{{ route('product.index') }}" class="btn btn-secondary mx-1 {{ request('category_id') ? '' : 'active' }}">All Categories</a>
            @foreach($categories as $category)
                <a href="{{ route('product.index', ['category_id' => $category->id]) }}" class="btn btn-secondary mx-1 {{ request('category_id') == $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="row" id="productList">
        @foreach($products as $product)
            <div class="col-md-4 mb-4 product-item">
                <div class="card h-100 shadow-sm">
                    <div id="carousel-{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ Storage::url($image->image_path) }}" class="product-image d-block w-100" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $product->brand }}</h5>
                        <p class="card-text">{{ $product->category->name }}</p>
                        <p class="card-text text-danger">${{ $product->price }}</p>
                        <form action="{{ route('cart.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary add-to-cart-btn w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
