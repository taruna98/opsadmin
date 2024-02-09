@extends('layout.admin_app')
@section('content')
    <div class="pagetitle">
        <h1>User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Administrator</a></li>
                <li class="breadcrumb-item active">User</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <h5 class="card-title">User List</h5>

                        <div class="card-header-action my-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#userCreateModal">Add <i class="bi bi-plus-lg"></i></button>
                        </div>

                        <!-- User List -->
                        <table class="table table-striped table-hover" id="table-users">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ ucwords($user['name']) }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>{{ ucwords($user['role_name']) }}</td>
                                        <td>
                                            @if ($user['is_active'] == 1)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Not Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn-edit btn btn-primary btn-sm" id="{{ $user['id'] }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn-detail btn btn-info btn-sm" id="{{ $user['id'] }}">
                                                <i class="bi bi-eye text-white"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- End User List -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div class="modal fade" id="userCreateModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Create</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="m-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="m-0 p-0">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form class="row g-3 needs-validation" role="form" method="POST"
                            action="{{ route('user.store') }}" enctype="multipart/form-data" novalidate>
                            @csrf
                            @if ($errors->has('login'))
                                <div class="alert alert-danger" role="alert">
                                    {{ $errors->first('login') }}
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label for="create_name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="create_name" id="create_name"
                                    value="{{ old('create_name') }}" required>
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="create_email" id="create_email"
                                    value="{{ old('create_email') }}" required>
                                <div class="invalid-feedback">Please enter your email.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="create_password" id="create_password"
                                    value="{{ old('create_password') }}" minlength="6" required>
                                <div class="invalid-feedback">Please enter your min 6 char password.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_role" class="form-label">Select Role</label>
                                <select class="form-select" name="create_role" id="create_role" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role['name'] }}">{{ ucwords($role['name']) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select role.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_status" class="form-label">Select Status</label>
                                <select class="form-select" name="create_status" id="create_status" required>
                                    <option selected value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                {{-- <button type="button" class="btn btn-secondary btn-sm my-2 me-2" data-bs-dismiss="modal">Close</button> --}}
                                <button class="btn btn-primary btn-sm my-2" type="submit">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Create User Modal-->

        <!-- Edit User Modal -->
        <div class="modal fade" id="userEditModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="m-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="m-0 p-0">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form class="row g-3 edit-form needs-validation" role="form" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            @if ($errors->has('login'))
                                <div class="alert alert-danger" role="alert">
                                    {{ $errors->first('login') }}
                                </div>
                            @endif
                            <div class="col-md-6 d-none">
                                <input type="hidden" class="form-control" name="edit_id" id="edit_id" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="edit_name" id="edit_name" required>
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="edit_email" id="edit_email" readonly>
                                <div class="invalid-feedback">Please enter your email.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="edit_password" id="edit_password"
                                    minlength="6">
                                <div class="invalid-feedback">Please enter your min 6 char password.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_role" class="form-label">Select Role</label>
                                <select class="form-select" name="edit_role" id="edit_role" required>
                                    @foreach ($rolez as $role)
                                        <option value="{{ $role['name'] }}">{{ ucwords($role['name']) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select role.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Select Status</label>
                                <select class="form-select" name="edit_status" id="edit_status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                {{-- <button type="button" class="btn btn-secondary btn-sm my-2 me-2" data-bs-dismiss="modal">Close</button> --}}
                                <button class="btn btn-primary btn-sm my-2" type="submit">Edit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Edit User Modal-->

        <!-- Detail User Modal -->
        <div class="modal fade" id="userDetailModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>ID</b>
                                        <a class="text-decoration-none text-dark" id="detail_id">Id</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Name</b>
                                        <a class="text-decoration-none text-dark" id="detail_name">Name</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Email</b>
                                        <a class="text-decoration-none text-dark" id="detail_email">Email</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Role</b>
                                        <a class="text-decoration-none text-dark" id="detail_role">Role</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Status</b>
                                        <a class="text-decoration-none text-dark" id="detail_status">Status</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Register At</b>
                                        <a class="text-decoration-none text-dark" id="detail_register_at">Register At</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Detail User Modal-->
    </section>

    <script>
        // table user
        $(document).ready(function() {
            // $('#table-users').DataTable();
        });

        // edit modal
        $(document).ready(function() {
            $('.btn-edit').on('click', function() {
                var userId = $(this).attr('id');
                var routeUrl = "{{ url('user/update/:id') }}".replace(':id', userId);
                $.ajax({
                    url: '/user/edit/' + userId,
                    type: 'GET',
                    success: function(data) {
                        $('#edit_id').val(data.id);
                        $('#edit_name').val(data.name);
                        $('#edit_email').val(data.email);
                        $('#edit_role').val(data.roles[0].name);
                        $('#edit_status').val(data.is_active);
                        $('#userEditModal').modal('show');
                        $('.edit-form').attr('action', routeUrl);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        // detail modal
        $(document).ready(function() {
            $('.btn-detail').on('click', function() {
                var userId = $(this).attr('id');
                $.ajax({
                    url: '/user/detail/' + userId,
                    type: 'GET',
                    success: function(data) {
                        $('#detail_id').text(data.id);
                        $('#detail_name').text(data.name);
                        $('#detail_email').text(data.email);
                        $('#detail_role').text(data.roles[0].name);
                        if (data.is_active == 1) {
                            $('#detail_status').html(
                                '<span class="badge rounded-pill text-white bg-primary">Active</span>'
                            );
                        } else if (data.is_active == 0) {
                            $('#detail_status').html(
                                '<span class="badge rounded-pill text-white bg-danger">Not Active</span>'
                            );
                        }
                        $('#detail_register_at').text(new Date(data.created_at).toISOString()
                            .slice(0, 16).replace('T', ' '));
                        $('#userDetailModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
