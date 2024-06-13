@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Product</h2>
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size" value="{{ $product->size }}" required>
            </div>
            <div class="mb-3">
                <label for="reference_number" class="form-label">Reference Number</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ $product->reference_number }}" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" value="{{ $product->brand }}" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="stockContainer">
                @foreach($product->stocks as $index => $stock)
                    <div class="stock-item mb-3">
                        <label for="stock_room" class="form-label">Stock Room</label>
                        <input type="text" class="form-control" name="stocks[{{ $index }}][stock_room]" value="{{ $stock->stock_room }}" required>
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="stocks[{{ $index }}][location]" value="{{ $stock->location }}" required>
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="stocks[{{ $index }}][quantity]" value="{{ $stock->quantity }}" required>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="addStockButton">Add Another Stock</button>

            <div class="mb-3">
                <label for="images" class="form-label">Product Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
            </div>

            <div class="mb-3">
                @foreach ($product->images as $image)
                    <img src="{{ asset('images/' . $image->image_path) }}" alt="Product Image" width="50" height="50">
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
@endsection
