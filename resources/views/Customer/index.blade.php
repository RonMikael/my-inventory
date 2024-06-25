@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h2>Customer List</h2>
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add
            Customer</button>
        {{-- <button type="button" class="btn btn-secondary mb-2" id="exportUserButton">Export Users</button> --}}

        <input type="text" id="searchInputCustomer" class="form-control mb-3" placeholder="Search">

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
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
                                <button type="button" class="btn btn-info btn-sm btn-view-customer"
                                    data-customer-id="{{ $customer->id }}">View</button>
                                <button class="btn btn-primary btn-sm btn-edit-customer"
                                    data-customer-id="{{ $customer->id }}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#confirmDeleteCustomerModal" data-customer-id="{{ $customer->id }}">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteCustomerModal" tabindex="-1" aria-labelledby="confirmDeleteCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
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

    <!-- User Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
    

    <!-- Edit User Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
@endsection
