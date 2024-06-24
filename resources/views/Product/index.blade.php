@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h2>Product List</h2>
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addProductModal">Add
            Product</button>
        <input type="text" id="searchInputProductIndex" class="form-control mb-3" placeholder="Search">

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Size</th>
                        <th class="text-center">Reference Number</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Stocks</th>
                        <th class="text-center">Images</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->size }}</td>
                            <td>{{ $product->reference_number }}</td>
                            <td>{{ $product->price }}</td>
                            <td>{{ $product->brand }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                <!-- Button to toggle stock details -->
                                <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#stocks-{{ $product->id }}" aria-expanded="false" aria-controls="stocks-{{ $product->id }}">
                                    Show Stocks (Total: {{ $product->total_stock_quantity }})
                                </button>
                                <!-- Collapsible section for stock details -->
                                <div class="collapse mt-2" id="stocks-{{ $product->id }}">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Stock Room</th>
                                                <th class="text-center">Location</th>
                                                <th class="text-center">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->stocks as $stock)
                                                <tr>
                                                    <td class="text-center">{{ $stock->stock_room }}</td>
                                                    <td class="text-center">{{ $stock->location }}</td>
                                                    <td class="text-center">{{ $stock->quantity }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            <td>
                                @if($product->images->count() > 0)
                                    <div id="carousel-{{ $product->id }}" class="carousel slide">
                                        <div class="carousel-inner">
                                            @foreach ($product->images as $index => $image)
                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                    <img src="{{ Storage::url($image->image_path) }}" alt="Product Image" class="d-block w-100" style="width: 50px; height: 50px;">
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
                                @else
                                    <p>No images available</p>
                                @endif
                            </td>                                                      
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-sm btn-edit-product" data-product-id="{{ $product->id }}" data-bs-toggle="modal" data-bs-target="#editProductModal">Edit</button>
                                <form id="deleteProductForm" action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteProductModal" data-product-id="{{ $product->id }}">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
                
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="size" class="form-label">Size</label>
                            <select id="size" name="size" class="form-control" required>
                                <option value="Small">Small</option>
                                <option value="Medium">Medium</option>
                                <option value="Large">Large</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price">
                        </div>
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select id="brand" name="brand" class="form-control" required>
                                <option value="Brand A">Brand A</option>
                                <option value="Brand B">Brand B</option>
                                <option value="Brand C">Brand C</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="stockContainer">
                            <div class="stock-item mb-3">
                                <div class="mb-3">
                                    <label for="stock_room" class="form-label">Stock Room</label>
                                    <select id="stock_room" class="form-control" name="stocks[0][stock_room]">
                                        <option value="">Select Stock Room</option>
                                        <option value="Building 1">Building 1</option>
                                        <option value="Building 2">Building 2</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <select id="location" class="form-control" name="stocks[0][location]">
                                        <option value="">Select Location</option>
                                        <option value="Rack 1" data-stock-room="Building 1">Rack 1</option>
                                        <option value="Rack 2" data-stock-room="Building 1">Rack 2</option>
                                        <option value="Rack A" data-stock-room="Building 2">Rack A</option>
                                        <option value="Rack B" data-stock-room="Building 2">Rack B</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="stocks[0][quantity]">
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" id="addStockButton">Add Another Stock</button>

                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Product Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteProductModal" tabindex="-1" aria-labelledby="confirmDeleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteProductModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- Adjusted form structure with correct action and method -->
                    <form id="deleteProductForm" action="" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editProductId" name="id">
                        <div class="mb-3">
                            <label for="editSize" class="form-label">Size</label>
                            <select id="editSize" name="size" class="form-control" required>
                                <option value="Small">Small</option>
                                <option value="Medium">Medium</option>
                                <option value="Large">Large</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editReferenceNumber" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="editReferenceNumber" name="reference_number">
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="editPrice" name="price">
                        </div>
                        <div class="mb-3">
                            <label for="editBrand" class="form-label">Brand</label>
                            <select id="editBrand" name="brand" class="form-control" required>
                                <option value="Brand A">Brand A</option>
                                <option value="Brand B">Brand B</option>
                                <option value="Brand C">Brand C</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryId" class="form-label">Category</label>
                            <select class="form-select" id="editCategoryId" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="editStockContainer">
                            <!-- Existing stocks will be appended here by JavaScript -->
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" id="addEditStockButton">Add Another Stock</button>

                        <div class="mb-3">
                            <label for="editImages" class="form-label">Product Images</label>
                            <div id="existingImages">
                                <!-- Existing images will be appended here by JavaScript -->
                            </div>
                            <input type="file" class="form-control mt-2" id="editImages" name="images[]" multiple>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
