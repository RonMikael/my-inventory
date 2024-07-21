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
        <h2 class="mb-0">Customer List</h2>
        <div class="d-flex">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary" id="btn-list-view">
                    <i class="bi bi-list"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-kanban-view">
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
                            <a href="{{ route('customers.export', ['search' => $search]) }}" class="btn btn-success my-1">Export</a> <!-- Adjust margin as needed -->
                            <button type="button" class="btn btn-secondary my-1" data-bs-toggle="modal" data-bs-target="#importCustomerModal">Import</button> <!-- Adjust margin as needed -->
                        </div>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add Customer</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('customer.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search customers..." value="{{ $search }}">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="{{ route('customer.index') }}" method="GET" class="mb-3">
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

    <div id="list-view">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Phone</th>
                        <th class="text-center">Address</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->address }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-view-customer" data-customer-id="{{ $customer->id }}">View</button>
                                <button class="btn btn-primary btn-sm btn-edit-customer" data-customer-id="{{ $customer->id }}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteCustomerModal" data-customer-id="{{ $customer->id }}">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Customer found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    {{ $customers->appends(['perPage' => $perPage])->links() }}
                </ul>
            </nav>
        </div>
    </div>

    <div id="kanban-view" class="d-none">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse($customers as $customer)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $customer->name }}</h5>
                            <p class="card-text"><strong>Email:</strong> {{ $customer->email }}</p>
                            <p class="card-text"><strong>Phone:</strong> {{ $customer->phone }}</p>
                            <p class="card-text"><strong>Address:</strong> {{ $customer->address }}</p>
                            <button type="button" class="btn btn-info btn-sm btn-view-customer" data-customer-id="{{ $customer->id }}">View</button>
                            <button class="btn btn-primary btn-sm btn-edit-customer" data-customer-id="{{ $customer->id }}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteCustomerModal" data-customer-id="{{ $customer->id }}">Delete</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <p class="text-center">No Customer found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modals -->
<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModal">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm" method="POST" action="{{ route('customers.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="addCustomerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="addCustomerName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addCustomerEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="addCustomerEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="addCustomerPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="addCustomerPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="addCustomerAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="addCustomerAddress" name="address">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="editCustomerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editCustomerName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="editCustomerEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerPhone" class="form-label">Phone</label>
                            <input type="number" class="form-control" id="editCustomerPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editCustomerAddress" name="address">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

<!-- Import Customer Modal -->
<div class="modal fade" id="importCustomerModal" tabindex="-1" aria-labelledby="importCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importCustomerModalLabel">Import Customers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="mb-3 text-center">
                <p class="mb-2">Before importing, download and fill out the template:</p>
                <a href="{{ route('customers.downloadTemplate') }}" class="btn btn-primary" target="_blank">
                    Download Template
                </a>
            </div>

                <hr>

                <form id="importCustomerForm" method="POST" action="{{ route('customers.import') }}" enctype="multipart/form-data">
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

<!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteCustomerModal" tabindex="-1" aria-labelledby="confirmDeleteCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteCustomerModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this customer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <form id="deleteCustomerForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Customer Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="customerDetailsContent">
                        <p><strong>Name:</strong> <span id="customerName"></span></p>
                        <p><strong>Email:</strong> <span id="customerEmail"></span></p>
                        <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
                        <p><strong>Address:</strong> <span id="customerAddress"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
