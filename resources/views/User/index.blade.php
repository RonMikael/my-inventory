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
        <h2 class="mb-0">User List</h2>
        <div class="d-flex">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;"> <!-- Adjust min-width here as needed -->
                    <li>
                        <div class="d-flex flex-column px-3"> <!-- Add padding on left and right sides -->
                            <a href="{{ route('user.export', ['search' => $search]) }}" class="btn btn-success my-1">Export</a> <!-- Adjust margin as needed -->
                            <button type="button" class="btn btn-secondary my-1" data-bs-toggle="modal" data-bs-target="#importUserModal">Import</button> <!-- Adjust margin as needed -->
                        </div>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('user.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ $search }}">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="{{ route('user.index') }}" method="GET" class="mb-3">
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
                        <th class="text-center">Role</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-view-user"
                                    data-user-id="{{ $user->id }}">View</button>
                                <button class="btn btn-primary btn-sm btn-edit-user"
                                    data-user-id="{{ $user->id }}">Edit</button>
                                <button class="btn btn-warning btn-sm btn-edit-user-role"
                                    data-user-id="{{ $user->id }}">Update Role</button>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#confirmDeleteUserModal" data-user-id="{{ $user->id }}">Delete</button>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No User found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        {{ $users->appends(['perPage' => $perPage])->links() }}
    </ul>
</nav>
    </div>
</div>

<!-- Modals -->
<!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm" method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="addUserName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="addUserName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="addUserEmail" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="addUserEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="input-group-text" id="eye-toggle"><i class="fa fa-eye"
                                            aria-hidden="true"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password-confirm"
                                        name="password_confirmation" required>
                                    <span class="input-group-text" id="confirm-password-eye-toggle"><i class="fa fa-eye"
                                            aria-hidden="true"></i></span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
    </div>

<!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="editUserName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editUserName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editUserEmail" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="editUserEmail" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
    </div>

<!-- Import User Modal -->
<div class="modal fade" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importUserModalLabel">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="mb-3 text-center">
                <p class="mb-2">Before importing, download and fill out the template:</p>
                <a href="{{ route('users.downloadTemplate') }}" class="btn btn-primary" target="_blank">
                    Download Template
                </a>
            </div>

                <hr>

                <form id="importUserForm" method="POST" action="{{ route('users.import') }}" enctype="multipart/form-data">
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
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteUserModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form id="deleteUserForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
    </div>

<!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="userDetailsContent">
                            <p><strong>Name:</strong> <span id="userName"></span></p>
                            <p><strong>Role:</strong> <span id="userRole"></span></p>
                            <p><strong>Email:</strong> <span id="userEmail"></span></p>
                        </div>
                    </div>
                </div>
            </div>
    </div>

<!-- Edit User Role Modal -->
    <div class="modal fade" id="editUserRoleModal" tabindex="-1" aria-labelledby="editUserRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserRoleModalLabel">Edit User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserRoleForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="editUserRole" class="form-label">Role</label>
                            <select class="form-select" id="editUserRole" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="super admin">Super Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
