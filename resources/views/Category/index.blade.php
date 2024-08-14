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
            <h2 class="mb-0">Category List</h2>
            <div class="d-flex">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                        <!-- Adjust min-width here as needed -->
                        <li>
                            <div class="d-flex flex-column px-3"> <!-- Add padding on left and right sides -->
                                <a href="{{ route('category.export', ['search' => $search]) }}"
                                    class="btn btn-success my-1">Export</a> <!-- Adjust margin as needed -->
                                <button type="button" class="btn btn-secondary my-1" data-bs-toggle="modal"
                                    data-bs-target="#importCategoryModal">Import</button> <!-- Adjust margin as needed -->
                            </div>
                        </li>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add
                    Category</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('category.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search categories..."
                            value="{{ $search }}">
                        <button type="submit" class="btn btn-outline-secondary">Search</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form action="{{ route('category.index') }}" method="GET" class="mb-3">
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


        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-view-category"
                                    data-category-id="{{ $category->id }}">View</button>
                                <button class="btn btn-primary btn-sm btn-edit-category"
                                    data-category-id="{{ $category->id }}">Edit</button>
                                @if (!$category->isUsed())
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteCategoryModal"
                                        data-category-id="{{ $category->id }}">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No Category found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    {{ $categories->appends(['perPage' => $perPage])->links() }}
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm" method="POST" action="{{ route('category.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="addCategoryName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="addCategoryName" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Category Modal -->
    <div class="modal fade" id="importCategoryModal" tabindex="-1" aria-labelledby="importCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importCategoryModalLabel">Import Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <p class="mb-2">Before importing, download and fill out the template:</p>
                        <a href="{{ route('category.downloadTemplate') }}" class="btn btn-primary" target="_blank">
                            Download Template
                        </a>
                    </div>

                    <hr>

                    <form id="importCategoryForm" method="POST" action="{{ route('category.import') }}"
                        enctype="multipart/form-data">
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

    <!-- Confirm Delete Category Modal -->
    <div class="modal fade" id="confirmDeleteCategoryModal" tabindex="-1"
        aria-labelledby="confirmDeleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteCategoryModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <p>Are you sure you want to delete this category?</p>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Details Modal -->
    <div class="modal fade" id="categoryDetailsModal" tabindex="-1" aria-labelledby="categoryDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryDetailsModalLabel">Category Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="categoryName"></p>
                </div>
            </div>
        </div>
    </div>
@endsection
