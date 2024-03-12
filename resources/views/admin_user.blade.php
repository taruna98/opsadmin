@extends('layout.admin_app')
@section('content')
    <style>
        /* style for datatable */
        table.dataTable {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
        }

        table.dataTable tr>td,
        table.dataTable tr>th {
            border: none;
        }

        table.dataTable td,
        table.dataTable th {
            padding: 10px !important;
        }
    </style>

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
                                {{-- @foreach ($users as $user)
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
                                @endforeach --}}
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
                                <label for="create_img_profile" class="form-label">Image Profile</label>
                                <br>
                                <small>dimensions must 120 x 120 in JPG (max size: 500KB)</small>
                                <img id="create_img_profile_preview" class="w-100" src="" /><br>
                                <input type="file" class="form-control pb-3 mt-2" accept=".jpg"
                                    onchange="loadCreateImgProfile(event)" name="create_img_profile"
                                    id="create_img_profile">
                                <strong id="create_img_profile_response" class="text-danger"></strong>
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
                                <label for="edit_img_profile" class="form-label">Image Profile</label>
                                <br>
                                <small>dimensions must 120 x 120 in JPG (max size: 500KB)</small>
                                <img id="edit_img_profile_preview" class="w-100" src="" /><br>
                                <input type="file" class="form-control pb-3 mt-2" accept=".jpg"
                                    onchange="loadEditImgCard(event)" name="edit_img_profile" id="edit_img_profile">
                                <strong id="edit_img_profile_response" class="text-danger"></strong>
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
                                        <img id="detail_img_profile" class="w-25 rounded-circle" src="" />
                                        <a class="text-decoration-none text-dark fw-bold my-auto"
                                            id="detail_name">Name</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>ID</b>
                                        <a class="text-decoration-none text-dark" id="detail_id">Id</a>
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
        var _URL = window.URL || window.webkitURL;

        // image profile create
        var loadCreateImgProfile = function(event) {
            var output = document.getElementById('create_img_profile_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#create_img_profile").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 120 && this.height == 120) {
                            $("#create_img_profile_response ").text("");
                        } else {
                            $("#create_img_profile_response").text("image dimensions must 120 x 120");
                            document.getElementById("create_img_profile").value = "";
                            document.getElementById("create_img_profile_preview").src = window.URL
                                .revokeObjectURL(this.files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#create_img_profile_response").text("image max size 500KB");
                document.getElementById("create_img_profile").value = "";
                document.getElementById("create_img_profile_preview").src = window.URL.revokeObjectURL(this.files);
            }
        });

        // image profile edit
        var loadEditImgCard = function(event) {
            var output = document.getElementById('edit_img_profile_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#edit_img_profile").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 120 && this.height == 120) {
                            $("#edit_img_profile_response ").text("");
                        } else {
                            $("#edit_img_profile_response").text("image dimensions must 120 x 120");
                            document.getElementById("edit_img_profile").value = "";
                            document.getElementById("edit_img_profile_preview").src = window.URL
                                .revokeObjectURL(this.files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#edit_img_profile_response").text("image max size 500KB");
                document.getElementById("edit_img_profile").value = "";
                document.getElementById("edit_img_profile_preview").src = window.URL.revokeObjectURL(this.files);
            }
        });

        // table user
        var rolesData;
        $(document).ready(function() {
            getRolesData();
            fill_datatable();

            function getRolesData() {
                $.ajax({
                    url: "{{ route('user.roles') }}",
                    type: "GET",
                    success: function(data) {
                        rolesData = data;
                        // buildRoleDropdown();
                    }
                });
            }

            function buildRoleDropdown() {
                var dropdown =
                    '<select class="form-select my-2" id="filter_role"> <option value="" selected disabled>Select Role</option> <option value="">all role</option>';
                $.each(rolesData, function(key, val) {
                    dropdown += '<option value="' + val.name + '">' + val.name + '</option>';
                });
                dropdown += '</select>';

                $('#table-users_filter').append(dropdown);

                $('#filter_role').on('change', function() {
                    var filter_role = $(this).val();

                    if (filter_role != '') {
                        $('#table-users').DataTable().destroy();
                        fill_datatable(filter_role);
                    } else {
                        $('#filter_role').val('');
                        $('#table-users').DataTable().destroy();
                        fill_datatable();
                    }
                });
            }

            function fill_datatable(filter_role = '') {
                var table = $('#table-users').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('user') }}",
                        type: 'GET',
                        data: {
                            filter_role: filter_role
                        }
                    },
                    initComplete: function(settings, json) {
                        // console.log(json);
                        buildRoleDropdown();
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'roles',
                            name: 'roles',
                            render: function(data, type, row, meta) {
                                return data[0]['name'];
                            }
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            render: function(data, type, row, meta) {
                                if (data == 1) {
                                    return '<span class="badge rounded-pill bg-success">Active</span>';
                                } else {
                                    return '<span class="badge rounded-pill bg-danger">Not Active</span>';
                                }
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, row, meta) {
                                var html =
                                    '<button class="btn-edit btn btn-primary btn-sm mx-1" id="' +
                                    data + '"><i class="bi bi-pencil-square"></i></button>';
                                html += '<button class="btn-detail btn btn-info btn-sm mx-1" id="' +
                                    data + '"><i class="bi bi-eye text-white"></i></button>';
                                return html;
                            }
                        }
                    ],
                    columnDefs: [{
                        'orderable': false,
                        'targets': [0, 3, 5]
                    }],
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    pageLength: 5
                });
            }
        });

        // edit modal
        $(document).on('click', '.btn-edit', function() {
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
                    $('#edit_img_profile_preview').attr('src', window.location.origin +
                        '/assets/img/admin_img_profile_' + data.email.split('@')[0] + '.jpg');
                    $('#edit_status').val(data.is_active);
                    $('#userEditModal').modal('show');
                    $('.edit-form').attr('action', routeUrl);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // detail modal
        $(document).on('click', '.btn-detail', function() {
            var userId = $(this).attr('id');
            $.ajax({
                url: '/user/detail/' + userId,
                type: 'GET',
                success: function(data) {
                    $('#detail_id').text(data.id);
                    $('#detail_img_profile').attr('src', window.location.origin +
                        '/assets/img/admin_img_profile_' + data.email.split('@')[0] + '.jpg');
                    $('#detail_name').text(data.name);
                    $('#detail_email').text(data.email);
                    $('#detail_role').text(data.roles[0].name);
                    if (data.is_active == 1) {
                        $('#detail_status').html(
                            '<span class="badge rounded-pill text-white bg-success">Active</span>'
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
    </script>
@endsection
