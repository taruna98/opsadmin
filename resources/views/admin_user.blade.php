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
                                <label for="create_image_profile" class="form-label">Password</label>
                                <input type="file" class="form-control" name="create_image_profile" id="create_image_profile" value="{{ old('create_image_profile') }}" required>
                                <div class="invalid-feedback">Please enter your min 6 char password.</div>
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


























@extends('Layout.admin_app')
@section('content')
    {{-- sweetalert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('fol.web.legend.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>{{ $title }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('fol.web.legend.index') }}">Legend</a></div>
                <div class="breadcrumb-item active"><a href="#">{{ $title }}</a></div>
            </div>
        </div>
        <div class="section-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <!-- left column -->
                <div class="col-12">
                    <!-- general form elements -->
                    <div class="card box-primary">
                        <div class="card-header with-border">
                            <h3 class="card-title">Legend</h3>
                        </div>
                        <!-- form start -->
                        <form role="form" method="post" action="{{ route('fol.web.legend.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        ID
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text" class="form-control" id="legend_id" name="legend_id"
                                            value="{{ $last_id + 1 }}" readonly>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Name
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small class="text-danger">
                                            * legend name cannot be changed
                                        </small>
                                        <input type="text" class="form-control" id="legend_name" name="legend_name"
                                            placeholder="ex: lagoon (max 50 char)" maxlength="50">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        On Highlight
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <select class="form-select form-control" aria-label="classes_1 select"
                                            name="legend_classes_1" id="legend_classes_1">
                                            <option value="1">Yes</option>
                                            <option selected value="0">No</option>
                                        </select>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Img Highlight
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>dimensions must 500 x 500 in PNG (max size: 500KB)</small>
                                        <img id="legend_img_highlight_preview" class="w-100" src="" /><br>
                                        <input type="file" class="form-control pb-3 mt-2 d-none" accept=".png"
                                            onchange="loadImgHighlight(event)" name="legend_img_highlight"
                                            id="legend_img_highlight">
                                        <strong id="legend_img_highlight_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        On Slider
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <select class="form-select form-control" aria-label="classes_2 select"
                                            name="legend_classes_2" id="legend_classes_2">
                                            <option value="1">Yes</option>
                                            <option selected value="0">No</option>
                                        </select>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Slider
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>dimensions must 410 x 152 in JPG (max size: 500KB)</small>
                                        <img id="legend_img_slider_preview" class="w-100" src="" /><br>
                                        <input type="file" class="form-control pb-3 mt-2 d-none" accept=".jpg"
                                            onchange="loadImgSlider(event)" name="legend_img_slider" id="legend_img_slider">
                                        <strong id="legend_img_slider_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        On Top
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <select class="form-select form-control" aria-label="classes_3 select"
                                            name="legend_classes_3" id="legend_classes_3">
                                            <option value="1">Yes</option>
                                            <option selected value="0">No</option>
                                        </select>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Top
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>dimensions must 1450 x 490 in JPG (max size: 500KB)</small>
                                        <img id="legend_img_top_preview" class="w-100" src="" /><br>
                                        <input type="file" class="form-control pb-3 mt-2 d-none" accept=".jpg"
                                            onchange="loadImgTop(event)" name="legend_img_top" id="legend_img_top">
                                        <strong id="legend_img_top_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Class</label>
                                    <div class="col-sm-12 col-md-3">
                                        <select class="form-select form-control" aria-label="class select"
                                            name="legend_class" id="legend_class">
                                            <option selected value="">-</option>
                                            <option value="ranger">ranger</option>
                                            <option value="rogue">rogue</option>
                                            <option value="support">support</option>
                                            <option value="tank">tank</option>
                                            <option value="warrior">warrior</option>
                                        </select>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Card
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>dimensions must 260 x 440 in JPG (max size: 500KB)</small>
                                        <img id="legend_img_card_preview" class="w-100" src="" /><br>
                                        <input type="file" class="form-control pb-3 mt-2" accept=".jpg"
                                            onchange="loadImgCard(event)" name="legend_img_card" id="legend_img_card">
                                        <strong id="legend_img_card_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label>
                                    <div class="col-sm-12 col-md-3">
                                        <textarea class="form-control" name="legend_desc" id="legend_desc"
                                            placeholder="ex: legend's as a lorem ipsum (max 600 char)" maxlength="600"
                                            cols="20" rows="10"></textarea>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Detail Background
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>dimensions must 1920 x 1080 in JPG (max size: 600KB)</small>
                                        <img id="legend_img_detail_bg_preview" class="w-100" src="" /><br>
                                        <input type="file" class="form-control pb-3 mt-2" accept=".jpg"
                                            onchange="loadImgDtlBg(event)" name="legend_img_detail_bg"
                                            id="legend_img_detail_bg">
                                        <strong id="legend_img_detail_bg_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        Ability Title
                                        <br>
                                        <span class="text-danger">* separate with a '|'</span>
                                    </label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea class="form-control" name="legend_abil_title" id="legend_abil_title"
                                            placeholder="ex: ability title 1|ability title 2|ability title 3 (max 200 char)"
                                            maxlength="200" cols="20" rows="10"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        Ability Description
                                        <br>
                                        <span class="text-danger">* separate with a '|'</span>
                                    </label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea class="form-control" name="legend_abil_desc" id="legend_abil_desc"
                                            placeholder="ex: ability description 1|ability description 2|ability description 3 (max 300 char)"
                                            maxlength="300" cols="20" rows="10"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        Ability Link
                                        <br>
                                        <span class="text-danger">* separate with a '|'</span>
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <textarea class="form-control" name="legend_abil_link" id="legend_abil_link"
                                            placeholder="ex: ability link 1|ability link 2|ability link 3 (max 200 char)"
                                            maxlength="200" cols="20" rows="10"></textarea>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Ability
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>
                                            dimensions must 256 x 256 in JPG (max size: 500KB)
                                            <br>
                                            <span class="text-danger">* match the number of images and links</span>
                                        </small>
                                        <div id="legend_img_abil_preview"></div>
                                        <input type="file" class="form-control pb-3 mt-2" accept=".jpg" multiple
                                            onchange="loadImgAbil(event)" name="legend_img_abil[]" id="legend_img_abil">
                                        <strong id="legend_img_abil_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        Skin Title
                                        <br>
                                        <span class="text-danger">* separate with a '|'</span>
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <textarea class="form-control" name="legend_skin_title" id="legend_skin_title"
                                            placeholder="ex: skin title 1|skin title 2|skin title 3 (max 200 char)"
                                            maxlength="200" cols="20" rows="10"></textarea>
                                    </div>
                                    <label class="col-form-label text-md-right col-12 col-md-1 col-lg-1">
                                        Image Skin
                                    </label>
                                    <div class="col-sm-12 col-md-3">
                                        <small>
                                            dimensions must 1278 x 724 in JPG (max size: 500KB)
                                            <br>
                                            <span class="text-danger">* match the number of images and title</span>
                                        </small>
                                        <div id="legend_img_skin_preview"></div>
                                        <input type="file" class="form-control pb-3 mt-2" accept=".jpg" multiple
                                            onchange="loadImgSkin(event)" name="legend_img_skin[]" id="legend_img_skin">
                                        <strong id="legend_img_skin_response" class="text-danger"></strong>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                                        Guide Link
                                        <br>
                                        <span class="text-danger">* separate with a '|'</span>
                                    </label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea class="form-control" name="legend_detail_guide_link"
                                            id="legend_detail_guide_link"
                                            placeholder="ex: guide link 1|guide link 2|guide link 3 (max 200 char)"
                                            maxlength="200" cols="20" rows="10"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status</label>
                                    <div class="col-sm-12 col-md-3">
                                        <select class="form-select form-control" aria-label="status select"
                                            name="legend_status" id="legend_status">
                                            <option selected value="1">Active</option>
                                            <option value="0">Non Active</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                    <div class="col-sm-12 col-md-7">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                        <!-- form end -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        var _URL = window.URL || window.webkitURL;

        var loadImgHighlight = function(event) {
            var output = document.getElementById('legend_img_highlight_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#legend_img_highlight").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 500 && this.height == 500) {
                            $("#legend_img_highlight_response ").text("");
                        } else {
                            $("#legend_img_highlight_response").text("image dimensions must 500 x 500");
                            document.getElementById("legend_img_highlight").value = "";
                            document.getElementById("legend_img_highlight_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_highlight_response").text("image max size 500KB");
                document.getElementById("legend_img_highlight").value = "";
                document.getElementById("legend_img_highlight_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgSlider = function(event) {
            var output = document.getElementById('legend_img_slider_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#legend_img_slider").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 410 && this.height == 152) {
                            $("#legend_img_slider_response ").text("");
                        } else {
                            $("#legend_img_slider_response").text("image dimensions must 410 x 152");
                            document.getElementById("legend_img_slider").value = "";
                            document.getElementById("legend_img_slider_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_slider_response").text("image max size 500KB");
                document.getElementById("legend_img_slider").value = "";
                document.getElementById("legend_img_slider_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgTop = function(event) {
            var output = document.getElementById('legend_img_top_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#legend_img_top").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 1450 && this.height == 490) {
                            $("#legend_img_top_response ").text("");
                        } else {
                            $("#legend_img_top_response").text("image dimensions must 1450 x 490");
                            document.getElementById("legend_img_top").value = "";
                            document.getElementById("legend_img_top_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_top_response").text("image max size 500KB");
                document.getElementById("legend_img_top").value = "";
                document.getElementById("legend_img_top_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgCard = function(event) {
            var output = document.getElementById('legend_img_card_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#legend_img_card").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 260 && this.height == 440) {
                            $("#legend_img_card_response ").text("");
                        } else {
                            $("#legend_img_card_response").text("image dimensions must 260 x 440");
                            document.getElementById("legend_img_card").value = "";
                            document.getElementById("legend_img_card_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_card_response").text("image max size 500KB");
                document.getElementById("legend_img_card").value = "";
                document.getElementById("legend_img_card_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgDtlBg = function(event) {
            var output = document.getElementById('legend_img_detail_bg_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $("#legend_img_detail_bg").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 600) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 1920 && this.height == 1080) {
                            $("#legend_img_detail_bg_response ").text("");
                        } else {
                            $("#legend_img_detail_bg_response").text("image dimensions must 1920 x 1080");
                            document.getElementById("legend_img_detail_bg").value = "";
                            document.getElementById("legend_img_detail_bg_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_detail_bg_response").text("image max size 600KB");
                document.getElementById("legend_img_detail_bg").value = "";
                document.getElementById("legend_img_detail_bg_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgAbil = function(event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Create a new image element
                    var img = new Image();
                    img.src = e.target.result;
                    img.onload = function() {
                        if (img.width == 256 && img.height == 256) {
                            img.style.width = '50px';
                            img.style.height = '50px';
                            img.style.marginRight = '10px';
                            var output = document.getElementById('legend_img_abil_preview');
                            output.appendChild(img);
                        }
                    };
                }
                reader.readAsDataURL(file);
            }
        };
        $("#legend_img_abil").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 256 && this.height == 256) {
                            $("#legend_img_abil_response").text("");
                        } else {
                            $("#legend_img_abil_response").text("image dimensions must 256 x 256");
                            document.getElementById("legend_img_abil").value = "";
                            document.getElementById("legend_img_abil_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_abil_response").text("image max size 600KB");
                document.getElementById("legend_img_abil").value = "";
                document.getElementById("legend_img_abil_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

        var loadImgSkin = function(event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Create a new image element
                    var img = new Image();
                    img.src = e.target.result;
                    img.onload = function() {
                        if (img.width == 1278 && img.height == 724) {
                            img.style.width = '80px';
                            img.style.height = '50px';
                            img.style.marginRight = '10px';
                            var output = document.getElementById('legend_img_skin_preview');
                            output.appendChild(img);
                        }
                    };
                }
                reader.readAsDataURL(file);
            }
        };
        $("#legend_img_skin").change(function(e) {
            var file, img;
            if (this.files[0].size / 1024 <= 500) {
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        if (this.width == 1278 && this.height == 724) {
                            $("#legend_img_skin_response").text("");
                        } else {
                            $("#legend_img_skin_response").text("image dimensions must 1278 x 724");
                            document.getElementById("legend_img_skin").value = "";
                            document.getElementById("legend_img_skin_preview").src = window.URL
                                .revokeObjectURL(this
                                    .files);
                        }
                    };
                    img.onerror = function() {
                        alert("not a valid file: " + file.type);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            } else {
                $("#legend_img_skin_response").text("image max size 500KB");
                document.getElementById("legend_img_skin").value = "";
                document.getElementById("legend_img_skin_preview").src = window.URL.revokeObjectURL(this
                    .files);
            }
        });

    </script>
    <script>
        $(document).ready(function() {
            $('#legend_classes_1').change(function() {
                if ($(this).val() == '0') {
                    $('#legend_img_highlight').addClass('d-none');
                } else {
                    $('#legend_img_highlight').removeClass('d-none');
                }
            });
            $('#legend_classes_2').change(function() {
                if ($(this).val() == '0') {
                    $('#legend_img_slider').addClass('d-none');
                } else {
                    $('#legend_img_slider').removeClass('d-none');
                }
            });
            $('#legend_classes_3').change(function() {
                if ($(this).val() == '0') {
                    $('#legend_img_top').addClass('d-none');
                } else {
                    $('#legend_img_top').removeClass('d-none');
                }
            });
        });

    </script>
@endpush
