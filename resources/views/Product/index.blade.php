@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Product List</h2>
            <p class="text-muted mb-0">Price Range: ${{ $minPrice }} - ${{ $maxPrice }}</p> <!-- Displaying min and max price -->
        </div>
        <div class="d-flex">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary" id="btn-list-viewproduct">
                    <i class="bi bi-list"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-kanban-viewproduct">
                    <i class="bi bi-kanban"></i>
                </button>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;"> <!-- Adjust min-width here as needed -->
                    <li>
                        <div class="d-flex flex-column px-3"> <!-- Add padding on left and right sides -->
                            <a href="{{ route('product.export', ['search' => $search]) }}" class="btn btn-success my-1">Export</a> <!-- Adjust margin as needed -->
                            <button type="button" class="btn btn-secondary my-1" data-bs-toggle="modal" data-bs-target="#importProductModal">Import</button> <!-- Adjust margin as needed -->
                        </div>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('products.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ $search }}">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="{{ route('products.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <label class="input-group-text" for="perPageSelect">Items per page:</label>
                    <select class="form-select" id="perPageSelect" name="perPage" onchange="this.form.submit()">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

        <div id="list-viewproduct">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
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
                                    <td colspan="8" class="text-center">No Product found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        {{ $products->appends(['perPage' => $perPage])->links() }}
                    </ul>
                </nav>
            </div>
        </div>

        <div id="kanban-viewproduct" class="d-none">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @forelse($products as $product)
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->brand }}</h5>
                                <p class="card-text"><strong>Size:</strong> {{ $product->size }}</p>
                                <p class="card-text"><strong>Reference Number:</strong> {{ $product->reference_number }}</p>
                                <p class="card-text"><strong>Price:</strong> {{ $product->price }}</p>
                                <p class="card-text"><strong>Category:</strong> {{ $product->category->name }}</p>
                                <button class="btn btn-primary btn-sm btn-edit-product"
                                        data-product-id="{{ $product->id }}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteProductModal" data-product-id="{{ $product->id }}">Delete
                                </button>
                            </div>
                            <ul class="list-group list-group-flush">
    <li class="list-group-item">
        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse"
                data-bs-target="#stocks-{{ $product->id }}" aria-expanded="false"
                aria-controls="stocks-{{ $product->id }}">
            Show Stocks (Total: {{ $product->total_stock_quantity }})
        </button>
        <div class="collapse mt-2" id="stocks-{{ $product->id }}">
            <ul class="list-group">
                @foreach ($product->stocks as $stock)
                    <li class="list-group-item">
                        <strong>Stock Room:</strong> {{ $stock->stock_room }}<br>
                        <strong>Location:</strong> {{ $stock->location }}<br>
                        <strong>Quantity:</strong> {{ $stock->quantity }}
                    </li>
                @endforeach
            </ul>
        </div>
    </li>
    <li class="list-group-item">
        @if($product->images->count() > 0)
            <div id="kanban-carousel-{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($product->images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($image->image_path) }}"
                                class="d-block w-100" style="height: 150px; object-fit: cover;"
                                alt="Product Image">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button"
                        data-bs-target="#kanban-carousel-{{ $product->id }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button"
                        data-bs-target="#kanban-carousel-{{ $product->id }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @else
            <p>No images available</p>
        @endif
    </li>
</ul>

                        </div>
                    </div>
                @empty
                    <div class="col">
                        <p class="text-center">No Product found.</p>
                    </div>
                @endforelse
            </div>
        </div>


<!-- Modals -->
 <!-- Add Product Modal -->
     <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                            <option value="90/80 - 17">90/80 - 17</option>
                            <option value="80/80 - 17">80/80 - 17</option>
                            <option value="90/90 - 14">90/90 - 14</option>
                            <option value="100/80 X 14">100/80 X 14</option>
                            <option value="60/80 - 17">60/80 - 17</option>
                            <option value="80/90 - 17">80/90 - 17</option>
                            <option value="80/80 - 14">80/80 - 14</option>
                            <option value="70/90 - 17">70/90 - 17</option>
                            <option value="100/80 - 17">100/80 - 17</option>
                            <option value="130/70 X 13">130/70 X 13</option>
                            <option value="70/90 - 14">70/90 - 14</option>
                            <option value="80/90 - 14">80/90 - 14</option>
                            <option value="110/80 - 14">110/80 - 14</option>
                            <option value="140/70 X 14">140/70 X 14</option>
                            <option value="70/80 - 17">70/80 - 17</option>
                            <option value="300 X 17">300 X 17</option>
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
                            <option value="CORSA PLATINUM CROSS S">CORSA PLATINUM CROSS S</option>
                            <option value="CST CM619 TT">CST CM619 TT</option>
                            <option value="CST C6577">CST C6577</option>
                            <option value="CST C6525">CST C6525</option>
                            <option value="MICHELIN PILOT STREET">MICHELIN PILOT STREET</option>
                            <option value="MAXXIS MAG 1">MAXXIS MAG 1</option>
                            <option value="CST C6531">CST C6531</option>
                            <option value="FALCON">FALCON</option>
                            <option value="FALCON RAPTOR">FALCON RAPTOR</option>
                            <option value="BLACKSTONE">BLACKSTONE</option>
                            <option value="CST A6603">CST A6603</option>
                            <option value="MIZZLE">MIZZLE</option>
                            <option value="MAXXIS M6233">MAXXIS M6233</option>
                            <option value="CST CM615 TL">CST CM615 TL</option>
                            <option value="MAXXIS M6234W TL">MAXXIS M6234W TL</option>
                            <option value="CST C6507">CST C6507</option>
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
                                        <option value="BUILDING 2">BUILDING 2</option>
                                        <option value="DISPLAY">DISPLAY</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <select id="location" class="form-control" name="stocks[0][location]">
                                        <option value="">Select Location</option>

                                        <option value="HANGOVER" data-stock-room="BUILDING 2">HANGOVER</option>
                                        <option value="RACK 9" data-stock-room="BUILDING 2">RACK 9</option>
                                        <option value="RACK 1" data-stock-room="BUILDING 2">RACK 1</option>
                                        <option value="RACK 2" data-stock-room="BUILDING 2">RACK 2</option>
                                        <option value="RACK 3" data-stock-room="BUILDING 2">RACK 3</option>

                                        <option value="RACK 1" data-stock-room="DISPLAY">RACK 1</option>
                                        <option value="RACK 2" data-stock-room="DISPLAY">RACK 2</option>
                                        <option value="RACK 3" data-stock-room="DISPLAY">RACK 3</option>
                                        <option value="RACK 4" data-stock-room="DISPLAY">RACK 4</option>
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

<!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                            <option value="90/80 - 17">90/80 - 17</option>
                            <option value="80/80 - 17">80/80 - 17</option>
                            <option value="90/90 - 14">90/90 - 14</option>
                            <option value="100/80 X 14">100/80 X 14</option>
                            <option value="60/80 - 17">60/80 - 17</option>
                            <option value="80/90 - 17">80/90 - 17</option>
                            <option value="80/80 - 14">80/80 - 14</option>
                            <option value="70/90 - 17">70/90 - 17</option>
                            <option value="100/80 - 17">100/80 - 17</option>
                            <option value="130/70 X 13">130/70 X 13</option>
                            <option value="70/90 - 14">70/90 - 14</option>
                            <option value="80/90 - 14">80/90 - 14</option>
                            <option value="110/80 - 14">110/80 - 14</option>
                            <option value="140/70 X 14">140/70 X 14</option>
                            <option value="70/80 - 17">70/80 - 17</option>
                            <option value="300 X 17">300 X 17</option>
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
                                <option value="CORSA PLATINUM CROSS S">CORSA PLATINUM CROSS S</option>
                                <option value="CST CM619 TT">CST CM619 TT</option>
                                <option value="CST C6577">CST C6577</option>
                                <option value="CST C6525">CST C6525</option>
                                <option value="MICHELIN PILOT STREET">MICHELIN PILOT STREET</option>
                                <option value="MAXXIS MAG 1">MAXXIS MAG 1</option>
                                <option value="CST C6531">CST C6531</option>
                                <option value="FALCON">FALCON</option>
                                <option value="FALCON RAPTOR">FALCON RAPTOR</option>
                                <option value="BLACKSTONE">BLACKSTONE</option>
                                <option value="CST A6603">CST A6603</option>
                                <option value="MIZZLE">MIZZLE</option>
                                <option value="MAXXIS M6233">MAXXIS M6233</option>
                                <option value="CST CM615 TL">CST CM615 TL</option>
                                <option value="MAXXIS M6234W TL">MAXXIS M6234W TL</option>
                                <option value="CST C6507">CST C6507</option>
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

<!-- Import Product Modal -->
<div class="modal fade" id="importProductModal" tabindex="-1" aria-labelledby="importProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProductModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="mb-3 text-center">
                <p class="mb-2">Before importing, download and fill out the template:</p>
                <a href="{{ route('products.downloadTemplate') }}" class="btn btn-primary" target="_blank">
                    Download Template
                </a>
            </div>

                <hr>

                <form id="importProductForm" method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Choose File</label>
                        <input type="file" class="form-control" id="importFile" name="file" required>
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Product Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteProductModal" tabindex="-1" aria-labelledby="confirmDeleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
        <div class="modal-dialog modal-dialog-centered">
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

<!-- Modal for confirming stock deletion -->
    <div class="modal fade" id="confirmDeleteStockModal" tabindex="-1" aria-labelledby="confirmDeleteStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteStockModalLabel">Confirm Stock Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this stock? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteStockButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
