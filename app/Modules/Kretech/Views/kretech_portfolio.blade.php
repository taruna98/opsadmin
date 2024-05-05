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
        <h1>Contents</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Page</a></li>
                <li class="breadcrumb-item">Contents</li>
                <li class="breadcrumb-item active">Portfolio</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Portfolio List</h5>

                        <div class="card-header-action my-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#portfolioCreateModal">Add <i class="bi bi-plus-lg"></i></button>
                        </div>

                        <!-- Portfolio List -->
                        <table class="table table-striped table-hover" id="table-kretech-portfolio">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <!-- End Portfolio List -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Create Portfolio Modal -->
        <div class="modal fade" id="portfolioCreateModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Portfolio Create</h5>
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
                        action="{{ route('kretech.portfolio.store') }}" enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- hidden input --}}
                            <input type="hidden" class="d-none" name="create_id" value="{{ $set_id }}" required>
                            <div class="col-md-4">
                                <label for="create_title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="create_title" id="create_title" value="{{ old('create_title') }}" required>
                                <div class="invalid-feedback">Please enter your title.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_link" class="form-label">Link</label>
                                <input type="text" class="form-control" name="create_link" id="create_link" value="{{ old('create_link') }}" required>
                                <div class="invalid-feedback">Please enter your link.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_bg_detail" class="form-label">Background Detail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg') }}" id="create_bg_detail_preview" alt="BackgroundPortfolioDetail"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadDetailBgCreate(event)" name="create_bg_detail" id="create_bg_detail">
                                <small id="create_bg_detail_warning" class="text-danger fst-italic">* dimensions must 2880 x 830 in JPG (max size: 500KB)</small>
                                <small id="create_bg_detail_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_create_bg_detail"><i class="bi bi-upload"></i></a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_client" class="form-label">Client</label>
                                <input type="text" class="form-control" name="create_client" id="create_client" value="{{ old('create_client') }}" required>
                                <div class="invalid-feedback">Please enter your client.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_category" class="form-label">Select Category</label>
                                <select class="form-select" name="create_category" id="create_category" required>
                                    <option selected value="art">Art</option>
                                    <option value="article">Article</option>
                                    <option value="coding">Coding</option>
                                    <option value="visual design">Visual Design</option>
                                </select>
                                <div class="invalid-feedback">Please select category.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_status" class="form-label">Select Status</label>
                                <select class="form-select" name="create_status" id="create_status" required>
                                    <option selected value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-12 content-input">
                                <label for="create_content" class="form-label me-2">Content <small class="text-danger">(max 5)</small></label>
                                <button class="btn btn-plus-content btn-primary btn-sm my-2" type="button"><i class="bi bi-plus"></i></button>
                                <button class="btn btn-minus-content btn-danger btn-sm my-2" type="button"><i class="bi bi-dash"></i></button>
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="row row-content-{{ $i }} {{ ($i == 1) ? '' : 'd-none' }}">
                                        <div class="col-md-4">
                                            <label for="create_content_title_{{ $i }}" class="form-label">Title</label>
                                            <input type="text" class="form-control" name="create_content_title_{{ $i }}" id="create_content_title_{{ $i }}" value="{{ old('create_content_title_' . $i ) }}" {{ ($i == 1) ? 'required' : '' }}>
                                            <div class="invalid-feedback">Please enter your content title.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="create_content_description_{{ $i }}" class="form-label">Description</label>
                                            <input type="text" class="form-control" name="create_content_description_{{ $i }}" id="create_content_description_{{ $i }}" value="{{ old('create_content_description_' . $i ) }}" {{ ($i == 1) ? 'required' : '' }}>
                                            <div class="invalid-feedback">Please enter your content description.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="create_content_image_{{ $i }}" class="form-label">Image</label> <br>
                                            <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="create_content_image_{{ $i }}_preview" alt="Profile"> <br>
                                            <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgContentCreate{{ $i }}(event)" name="create_content_image_{{ $i }}" id="create_content_image_{{ $i }}">
                                            <small id="create_content_image_{{ $i }}_warning" class="text-danger fst-italic">* dimensions must 960 x 540 in PNG (max size: 500KB)</small>
                                            <small id="create_content_image_{{ $i }}_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_create_content_image_{{ $i }}"><i class="bi bi-upload"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm my-2" type="submit">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Create Portfolio Modal-->

        <!-- Edit Portfolio Modal -->
        <div class="modal fade" id="portfolioEditModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Portfolio Edit <span class="edit_port_id d-none"></span></h5>
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
                            {{-- hidden input --}}
                            <input type="hidden" class="form-control" name="edit_id" id="edit_id" required>
                            <input type="hidden" class="form-control" name="edit_created_at" id="edit_created_at" required>
                            <div class="col-md-4">
                                <label for="edit_title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="edit_title" id="edit_title" required>
                                <div class="invalid-feedback">Please enter your title.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_link" class="form-label">Link</label>
                                <input type="text" class="form-control" name="edit_link" id="edit_link" required>
                                <div class="invalid-feedback">Please enter your link.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_bg_detail" class="form-label">Background Detail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg') }}" id="edit_bg_detail_preview" alt="BackgroundPortfolioDetail"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadDetailBgEdit(event)" name="edit_bg_detail" id="edit_bg_detail">
                                <small id="edit_bg_detail_warning" class="text-danger fst-italic">* dimensions must 2880 x 830 in JPG (max size: 500KB)</small>
                                <small id="edit_bg_detail_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_edit_bg_detail"><i class="bi bi-upload"></i></a>
                                    <a type="button" class="btn btn-danger btn-sm {{ ($delete_port_bg_detail == 1) ? 'd-none' : '' }}" id="btn_delete_edit_bg_detail"><i class="bi bi-trash"></i></a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_client" class="form-label">Client</label>
                                <input type="text" class="form-control" name="edit_client" id="edit_client" required>
                                <div class="invalid-feedback">Please enter your client.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_category" class="form-label">Select Category</label>
                                <select class="form-select" name="edit_category" id="edit_category" required>
                                    <option value="art">Art</option>
                                    <option value="article">Article</option>
                                    <option value="coding">Coding</option>
                                    <option value="visual design">Visual Design</option>
                                </select>
                                <div class="invalid-feedback">Please select category.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_status" class="form-label">Select Status</label>
                                <select class="form-select" name="edit_status" id="edit_status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-12 content-input">
                                <label for="edit_content" class="form-label me-2">Content <small class="text-danger">(max 5)</small></label>
                                <button class="btn btn-plus-content btn-primary btn-sm my-2" type="button"><i class="bi bi-plus"></i></button>
                                <button class="btn btn-minus-content btn-danger btn-sm my-2" type="button"><i class="bi bi-dash"></i></button>
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="row row-content-{{ $i }} {{ ($i == 1) ? '' : 'd-none' }}">
                                        <div class="col-md-4">
                                            <label for="edit_content_title_{{ $i }}" class="form-label">Title</label>
                                            <input type="text" class="form-control" name="edit_content_title_{{ $i }}" id="edit_content_title_{{ $i }}" value="{{ old('edit_content_title_' . $i ) }}" {{ ($i == 1) ? 'required' : '' }}>
                                            <div class="invalid-feedback">Please enter your content title.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_content_description_{{ $i }}" class="form-label">Description</label>
                                            <input type="text" class="form-control" name="edit_content_description_{{ $i }}" id="edit_content_description_{{ $i }}" value="{{ old('edit_content_description_' . $i ) }}" {{ ($i == 1) ? 'required' : '' }}>
                                            <div class="invalid-feedback">Please enter your content description.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_content_image_{{ $i }}" class="form-label">Image</label> <br>
                                            <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="edit_content_image_{{ $i }}_preview" alt="Profile"> <br>
                                            <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgContentEdit{{ $i }}(event)" name="edit_content_image_{{ $i }}" id="edit_content_image_{{ $i }}">
                                            <small id="edit_content_image_{{ $i }}_warning" class="text-danger fst-italic">* dimensions must 960 x 540 in PNG (max size: 500KB)</small>
                                            <small id="edit_content_image_{{ $i }}_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_edit_content_image_{{ $i }}"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm" id="btn_delete_edit_content_item_{{ $i }}"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
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
        <!-- End Edit Portfolio Modal-->

        <!-- Detail Portfolio Modal -->
        <div class="modal fade" id="portfolioDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Portfolio Detail</h5>
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
                                        <b>Title</b>
                                        <a class="text-decoration-none text-dark" id="detail_title">Title</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Link</b>
                                        <a class="text-decoration-none text-dark" id="detail_link">Link</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Client</b>
                                        <a class="text-decoration-none text-dark" id="detail_client">Client</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Category</b>
                                        <a class="text-decoration-none text-dark" id="detail_category">Category</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Status</b>
                                        <a class="text-decoration-none text-dark" id="detail_status">Status</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Created At</b>
                                        <a class="text-decoration-none text-dark" id="detail_created_at">Created At</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Updated At</b>
                                        <a class="text-decoration-none text-dark" id="detail_updated_at">Updated At</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Background Detail</b>
                                        <img class="rounded w-50" id="detail_background_preview" alt="BackgroundDetail">
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Content</b>
                                    </li>
                                    <li class="list-group-item content-item-1 d-none justify-content-between">
                                        <div class="row row-content-1">
                                            <div class="col-md-4">
                                                <label for="detail_content_title_1" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="detail_content_title_1" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_description_1" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="detail_content_description_1" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_image_1" class="form-label">Image</label> <br>
                                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="detail_content_image_1_preview" alt="Profile">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item content-item-2 d-none justify-content-between">
                                        <div class="row row-content-2">
                                            <div class="col-md-4">
                                                <label for="detail_content_title_2" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="detail_content_title_2" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_description_2" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="detail_content_description_2" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_image_2" class="form-label">Image</label> <br>
                                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="detail_content_image_2_preview" alt="Profile">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item content-item-3 d-none justify-content-between">
                                        <div class="row row-content-3">
                                            <div class="col-md-4">
                                                <label for="detail_content_title_3" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="detail_content_title_3" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_description_3" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="detail_content_description_3" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_image_3" class="form-label">Image</label> <br>
                                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="detail_content_image_3_preview" alt="Profile">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item content-item-4 d-none justify-content-between">
                                        <div class="row row-content-4">
                                            <div class="col-md-4">
                                                <label for="detail_content_title_4" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="detail_content_title_4" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_description_4" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="detail_content_description_4" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_image_4" class="form-label">Image</label> <br>
                                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="detail_content_image_4_preview" alt="Profile">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item content-item-5 d-none justify-content-between">
                                        <div class="row row-content-5">
                                            <div class="col-md-4">
                                                <label for="detail_content_title_5" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="detail_content_title_5" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_description_5" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="detail_content_description_5" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="detail_content_image_5" class="form-label">Image</label> <br>
                                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_portfolio_default.jpg') }}" id="detail_content_image_5_preview" alt="Profile">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Detail Portfolio Modal-->

    </section>

    <script>
        // table kretech portfolio
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('kretech.portfolio') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    $('#table-kretech-portfolio').DataTable({
                        data: response,
                        columns: [
                            {
                                data: 'id',
                                name: 'id',
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'ttl',
                                name: 'ttl',
                                render: function(data, type, row, meta) {
                                    return data.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                        return letter.toUpperCase();
                                    });
                                }
                            },
                            {
                                data: 'ctg',
                                name: 'ctg',
                                render: function(data, type, row, meta) {
                                    return data.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                        return letter.toUpperCase();
                                    });
                                }
                            },
                            {
                                data: 'stt',
                                name: 'stt',
                                render: function(data, type, row, meta) {
                                    if (data == 1) {
                                        return '<span class="badge rounded-pill bg-success">Active</span>';
                                    } else {
                                        return '<span class="badge rounded-pill bg-danger">Not Active</span>';
                                    }
                                }
                            },
                            {
                                data: 'cat',
                                name: 'cat',
                                render: function(data, type, row, meta) {
                                    return data.substring(0, 10);
                                }
                            },
                            {
                                data: 'id',
                                name: 'id',
                                render: function(data, type, row, meta) {
                                    var html = '<button class="btn-edit btn btn-primary btn-sm mx-1" id="' + data + '"><i class="bi bi-pencil-square text-white"></i></button>';
                                    html += '<button class="btn-detail btn btn-info btn-sm mx-1" id="' + data + '"><i class="bi bi-eye text-white"></i></button>';
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
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // edit modal
        $(document).on('click', '.btn-edit', function() {
            var userId = $(this).attr('id');
            var routeUrl = "{{ url('kretech/portfolio/update/:id') }}".replace(':id', userId);
            $.ajax({
                url: '/kretech/portfolio/edit/' + userId,
                type: 'GET',
                success: function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_created_at').val(data.cat);
                    $('.edit_port_id').text(data.id);
                    $('#edit_title').val(data.ttl);
                    $('#edit_link').val(data.lnk);
                    $('#edit_bg_detail_preview').attr('src', window.location.origin + '/assets/img/kretech_img_profile_bg_portfolio_dtl_' + data.cod + '_' + data.id + '.jpg');
                    $('#edit_client').val(data.cln);
                    if (data.ctg == 'art') {
                        $('#edit_category').val('art');
                    } else if (data.ctg == 'article') {
                        $('#edit_category').val('article');
                    } else if (data.ctg == 'coding') {
                        $('#edit_category').val('coding');
                    } else if (data.ctg == 'visual design') {
                        $('#edit_category').val('visual design');
                    }
                    if (data.stt == '1') {
                        $('#edit_status').val('1');
                    } else {
                        $('#edit_status').val('0');
                    }
                    for (let i = 0; i < 5; i++) {
                        if ((i + 1) === 1) {
                            $('.row-content-' + (i + 1)).removeClass('d-none');
                            $('#edit_content_title_' + (i + 1)).prop('required', true);
                            $('#edit_content_description_' + (i + 1)).prop('required', true);
                        } else {
                            $('.row-content-' + (i + 1)).addClass('d-none');
                            $('#edit_content_title_' + (i + 1)).prop('required', false);
                            $('#edit_content_description_' + (i + 1)).prop('required', false);
                        }
                    }
                    for (let j = 0; j < data.sbt.split('|').length; j++) {
                        $('.row-content-' + (j + 1)).removeClass('d-none');
                        $('#edit_content_title_' + (j + 1)).prop('required', true);
                        $('#edit_content_title_' + (j + 1)).val(data.sbt.split('|')[j]);
                        $('#edit_content_description_' + (j + 1)).prop('required', true);
                        $('#edit_content_description_' + (j + 1)).val(data.dsc.split('|')[j]);
                        $('#edit_content_image_' + (j + 1) + '_preview').attr('src', window.location.origin + '/assets/img/kretech_img_content_portfolio_' + data.id + '_item_' + (j + 1) + '.jpg');
                    }
                    $('#portfolioEditModal').modal('show');
                    $('.edit-form').attr('action', routeUrl);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // detail modal
        $(document).on('click', '.btn-detail', function() {
            for (var i = 1; i <= 5; i++) {
                if ($('.content-item-' + i).hasClass('d-flex')) {
                    $('.content-item-' + i).removeClass('d-flex');
                    $('.content-item-' + i).addClass('d-none');
                }
            }

            var userId = $(this).attr('id');
            $.ajax({
                url: '/kretech/portfolio/detail/' + userId,
                type: 'GET',
                success: function(data) {
                    $('#detail_id').text(data.id);
                    $('#detail_title').text(data.ttl);
                    $('#detail_link').text(data.lnk);
                    $('#detail_client').text(data.cln);
                    $('#detail_category').text(data.ctg);
                    if (data.stt == '1') {
                        $('#detail_status').html('<span class="badge rounded-pill bg-success">Active</span>');
                    } else {
                        $('#detail_status').html('<span class="badge rounded-pill bg-danger">Not Active</span>');
                    }
                    $('#detail_created_at').text(data.cat);
                    $('#detail_updated_at').text(data.uat);
                    $('#detail_background_preview').attr('src', window.location.origin + '/assets/img/' + 'kretech_img_profile_bg_portfolio_dtl_' + data.cod + '_' + data.id + '.jpg');
                    $.each(data.sbt.split('|'), function(idx1, val1) {
                        $('.content-item-' + (idx1 + 1)).removeClass('d-none');
                        $('.content-item-' + (idx1 + 1)).addClass('d-flex');
                        $('#detail_content_title_' + (idx1 + 1)).val(val1);
                    });
                    $.each(data.dsc.split('|'), function(idx2, val2) {
                        $('#detail_content_description_' + (idx2 + 1)).val(val2);
                        $('#detail_content_image_' + (idx2 + 1) + '_preview').removeAttr('src');
                        $('#detail_content_image_' + (idx2 + 1) + '_preview').attr('src', window.location.origin + '/assets/img/' + 'kretech_img_content_portfolio_' + data.id + '_item_' + (idx2 + 1) + '.jpg');
                        
                        // check image src not found in directory
                        var imgSrc = $('#detail_content_image_' + (idx2 + 1) + '_preview').attr('src');
                        var img = new Image();
                        $(img).on('error', function() {
                            $('#detail_content_image_' + (idx2 + 1) + '_preview').attr('src', window.location.origin + '/assets/img/kretech_img_content_portfolio_default.jpg');
                        });
                        img.src = imgSrc;
                    });
                    $('#portfolioDetailModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // create background detail
        var loadDetailBgCreate = function(event) {
            var output = document.getElementById('create_bg_detail_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var bgDetailPreview = $("#create_bg_detail_preview")[0];
            var bgDetailSrc = bgDetailPreview.getAttribute('src');

            $("#btn_upload_create_bg_detail").on('click', function() {
                $("#create_bg_detail").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_bg_detail', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 2880 && this.height == 830) {
                            $("#create_bg_detail_response").text("");
                        } else {
                            $("#create_bg_detail_warning").text("");
                            $("#create_bg_detail_response").text("* image dimensions not valid");
                            document.getElementById("create_bg_detail").value = "";
                            bgDetailPreview.src = bgDetailSrc;
                        }
                    } else {
                        $("#create_bg_detail_warning").text("");
                        $("#create_bg_detail_response").text("* image over size");
                        document.getElementById("create_bg_detail").value = "";
                        bgDetailPreview.src = bgDetailSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit background detail
        var loadDetailBgEdit = function(event) {
            var output = document.getElementById('edit_bg_detail_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var bgDetailPreview = $("#edit_bg_detail_preview")[0];
            var bgDetailSrc = bgDetailPreview.getAttribute('src');

            $("#btn_upload_edit_bg_detail").on('click', function() {
                $("#edit_bg_detail").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_bg_detail', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 2880 && this.height == 830) {
                            $("#edit_bg_detail_response").text("");
                        } else {
                            $("#edit_bg_detail_warning").text("");
                            $("#edit_bg_detail_response").text("* image dimensions not valid");
                            document.getElementById("edit_bg_detail").value = "";
                            bgDetailPreview.src = bgDetailSrc;
                        }
                    } else {
                        $("#edit_bg_detail_warning").text("");
                        $("#edit_bg_detail_response").text("* image over size");
                        document.getElementById("edit_bg_detail").value = "";
                        bgDetailPreview.src = bgDetailSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // delete background detail
        $(document).ready(function() {
            $('#btn_delete_edit_bg_detail').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus portfolio background detail Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var portId = $('.edit_port_id').text();
                        $.ajax({
                            url: "{{ route('kretech.portfolio.file') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_portfolio_background_detail',
                                port_id: portId
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('#edit_bg_detail_preview').attr('src', response.src);
                                $('#btn_delete_edit_bg_detail').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Portfolio Background Detail Anda berhasil dihapus!',
                                    icon: 'success',
                                    timer: 3000,
                                    // showConfirmButton: false
                                });
                            },
                            error: function(xhr, status, error) {
                                // console.log(xhr.statusText + '|' + xhr.responseJSON.message + ' | ' + status + ' | ' + error);
                                Swal.fire({
                                    title: 'Oops!',
                                    text: xhr.responseJSON.message,
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });

        // create content image 1
        var loadImgContentCreate1 = function(event) {
            var output = document.getElementById('create_content_image_1_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_content_image_1_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_content_image_1").on('click', function() {
                $("#create_content_image_1").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_content_image_1', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#create_content_image_1_response").text("");
                        } else {
                            $("#create_content_image_1_warning").text("");
                            $("#create_content_image_1_response").text("* image dimensions not valid");
                            document.getElementById("create_content_image_1").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_content_image_1_warning").text("");
                        $("#create_content_image_1_response").text("* image over size");
                        document.getElementById("create_content_image_1").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit content image 1
        var loadImgContentEdit1 = function(event) {
            var output = document.getElementById('edit_content_image_1_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_content_image_1_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_content_image_1").on('click', function() {
                $("#edit_content_image_1").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_content_image_1', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#edit_content_image_1_response").text("");
                        } else {
                            $("#edit_content_image_1_warning").text("");
                            $("#edit_content_image_1_response").text("* image dimensions not valid");
                            document.getElementById("edit_content_image_1").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_content_image_1_warning").text("");
                        $("#edit_content_image_1_response").text("* image over size");
                        document.getElementById("edit_content_image_1").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // delete content item 1
        $(document).ready(function() {
            $("#btn_delete_edit_content_item_1").on('click', function() {
                Swal.fire({
                    title: 'Info',
                    text: 'Item ini tidak dapat dihapus!',
                    icon: 'warning',
                    timer: 3000
                });
            });
        });

        // create content image 2
        var loadImgContentCreate2 = function(event) {
            var output = document.getElementById('create_content_image_2_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_content_image_2_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_content_image_2").on('click', function() {
                $("#create_content_image_2").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_content_image_2', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#create_content_image_2_response").text("");
                        } else {
                            $("#create_content_image_2_warning").text("");
                            $("#create_content_image_2_response").text("* image dimensions not valid");
                            document.getElementById("create_content_image_2").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_content_image_2_warning").text("");
                        $("#create_content_image_2_response").text("* image over size");
                        document.getElementById("create_content_image_2").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit content image 2
        var loadImgContentEdit2 = function(event) {
            var output = document.getElementById('edit_content_image_2_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_content_image_2_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_content_image_2").on('click', function() {
                $("#edit_content_image_2").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_content_image_2', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#edit_content_image_2_response").text("");
                        } else {
                            $("#edit_content_image_2_warning").text("");
                            $("#edit_content_image_2_response").text("* image dimensions not valid");
                            document.getElementById("edit_content_image_2").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_content_image_2_warning").text("");
                        $("#edit_content_image_2_response").text("* image over size");
                        document.getElementById("edit_content_image_2").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // delete content item 2
        $(document).ready(function() {
            $("#btn_delete_edit_content_item_2").on('click', function() {
                Swal.fire({
                    title: 'Item ini akan dihapus dan tidak dapat dipulihkan!',
                    text: 'Anda juga akan menghapus title, description dan image',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.row-content-2').addClass('d-none');
                        $('#edit_content_title_2').prop('required', false);
                        $('#edit_content_title_2').val('');
                        $('#edit_content_description_2').prop('required', false);
                        $('#edit_content_description_2').val('');
                        $('#edit_content_image_2_preview').attr('src', window.location.protocol + "//" + window.location.host + '/assets/img/kretech_img_content_portfolio_default.jpg');
                        Swal.fire({
                            title: 'Deleted !',
                            text: 'Item ini telah dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });

        // create content image 3
        var loadImgContentCreate3 = function(event) {
            var output = document.getElementById('create_content_image_3_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_content_image_3_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_content_image_3").on('click', function() {
                $("#create_content_image_3").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_content_image_3', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#create_content_image_3_response").text("");
                        } else {
                            $("#create_content_image_3_warning").text("");
                            $("#create_content_image_3_response").text("* image dimensions not valid");
                            document.getElementById("create_content_image_3").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_content_image_3_warning").text("");
                        $("#create_content_image_3_response").text("* image over size");
                        document.getElementById("create_content_image_3").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit content image 3
        var loadImgContentEdit3 = function(event) {
            var output = document.getElementById('edit_content_image_3_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_content_image_3_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_content_image_3").on('click', function() {
                $("#edit_content_image_3").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_content_image_3', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#edit_content_image_3_response").text("");
                        } else {
                            $("#edit_content_image_3_warning").text("");
                            $("#edit_content_image_3_response").text("* image dimensions not valid");
                            document.getElementById("edit_content_image_3").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_content_image_3_warning").text("");
                        $("#edit_content_image_3_response").text("* image over size");
                        document.getElementById("edit_content_image_3").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // delete content item 3
        $(document).ready(function() {
            $("#btn_delete_edit_content_item_3").on('click', function() {
                Swal.fire({
                    title: 'Item ini akan dihapus dan tidak dapat dipulihkan!',
                    text: 'Anda juga akan menghapus title, description dan image',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.row-content-3').addClass('d-none');
                        $('#edit_content_title_3').prop('required', false);
                        $('#edit_content_title_3').val('');
                        $('#edit_content_description_3').prop('required', false);
                        $('#edit_content_description_3').val('');
                        $('#edit_content_image_3_preview').attr('src', window.location.protocol + "//" + window.location.host + '/assets/img/kretech_img_content_portfolio_default.jpg');
                        Swal.fire({
                            title: 'Deleted !',
                            text: 'Item ini telah dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });

        // create content image 4
        var loadImgContentCreate4 = function(event) {
            var output = document.getElementById('create_content_image_4_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_content_image_4_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_content_image_4").on('click', function() {
                $("#create_content_image_4").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_content_image_4', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#create_content_image_4_response").text("");
                        } else {
                            $("#create_content_image_4_warning").text("");
                            $("#create_content_image_4_response").text("* image dimensions not valid");
                            document.getElementById("create_content_image_4").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_content_image_4_warning").text("");
                        $("#create_content_image_4_response").text("* image over size");
                        document.getElementById("create_content_image_4").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit content image 4
        var loadImgContentEdit4 = function(event) {
            var output = document.getElementById('edit_content_image_4_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_content_image_4_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_content_image_4").on('click', function() {
                $("#edit_content_image_4").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_content_image_4', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#edit_content_image_4_response").text("");
                        } else {
                            $("#edit_content_image_4_warning").text("");
                            $("#edit_content_image_4_response").text("* image dimensions not valid");
                            document.getElementById("edit_content_image_4").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_content_image_4_warning").text("");
                        $("#edit_content_image_4_response").text("* image over size");
                        document.getElementById("edit_content_image_4").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // delete content item 4
        $(document).ready(function() {
            $("#btn_delete_edit_content_item_4").on('click', function() {
                Swal.fire({
                    title: 'Item ini akan dihapus dan tidak dapat dipulihkan!',
                    text: 'Anda juga akan menghapus title, description dan image',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.row-content-4').addClass('d-none');
                        $('#edit_content_title_4').prop('required', false);
                        $('#edit_content_title_4').val('');
                        $('#edit_content_description_4').prop('required', false);
                        $('#edit_content_description_4').val('');
                        $('#edit_content_image_4_preview').attr('src', window.location.protocol + "//" + window.location.host + '/assets/img/kretech_img_content_portfolio_default.jpg');
                        Swal.fire({
                            title: 'Deleted !',
                            text: 'Item ini telah dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });

        // create content image 5
        var loadImgContentCreate5 = function(event) {
            var output = document.getElementById('create_content_image_5_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_content_image_5_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_content_image_5").on('click', function() {
                $("#create_content_image_5").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_content_image_5', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#create_content_image_5_response").text("");
                        } else {
                            $("#create_content_image_5_warning").text("");
                            $("#create_content_image_5_response").text("* image dimensions not valid");
                            document.getElementById("create_content_image_5").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_content_image_5_warning").text("");
                        $("#create_content_image_5_response").text("* image over size");
                        document.getElementById("create_content_image_5").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit content image 5
        var loadImgContentEdit5 = function(event) {
            var output = document.getElementById('edit_content_image_5_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_content_image_5_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_content_image_5").on('click', function() {
                $("#edit_content_image_5").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_content_image_5', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 960 && this.height == 540) {
                            $("#edit_content_image_5_response").text("");
                        } else {
                            $("#edit_content_image_5_warning").text("");
                            $("#edit_content_image_5_response").text("* image dimensions not valid");
                            document.getElementById("edit_content_image_5").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_content_image_5_warning").text("");
                        $("#edit_content_image_5_response").text("* image over size");
                        document.getElementById("edit_content_image_5").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // delete content item 5
        $(document).ready(function() {
            $("#btn_delete_edit_content_item_5").on('click', function() {
                Swal.fire({
                    title: 'Item ini akan dihapus dan tidak dapat dipulihkan!',
                    text: 'Anda juga akan menghapus title, description dan image',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.row-content-5').addClass('d-none');
                        $('#edit_content_title_5').prop('required', false);
                        $('#edit_content_title_5').val('');
                        $('#edit_content_description_5').prop('required', false);
                        $('#edit_content_description_5').val('');
                        $('#edit_content_image_5_preview').attr('src', window.location.protocol + "//" + window.location.host + '/assets/img/kretech_img_content_portfolio_default.jpg');
                        Swal.fire({
                            title: 'Deleted !',
                            text: 'Item ini telah dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });
 
        // button plus or minus row content
        $(document).ready(function() {
            var currentRow = 1;
            $('.btn-plus-content').click(function() {
                if (currentRow < 5) {
                    $('.row-content-' + (currentRow + 1)).removeClass('d-none');
                    $('#create_content_title_' + (currentRow + 1)).prop('required', true);
                    $('#create_content_description_' + (currentRow + 1)).prop('required', true);
                    currentRow++;
                }
            });
            $('.btn-minus-content').click(function() {
                if (currentRow > 1) {
                    $('.row-content-' + currentRow).addClass('d-none'); 
                    $('#create_content_title_' + (currentRow)).prop('required', false);
                    $('#create_content_description_' + (currentRow)).prop('required', false);
                    currentRow--;
                }
            });
        });
        // $(document).ready(function() {
        //     var counter = $('.row-content').length || 1;
        //     $('.btn-plus-content').click(function() {
        //         if (counter < 5) {
        //             counter++;
        //             var newRow = $('.row-content-1').first().clone();
        //             newRow.attr('class', 'row row-content-' + counter);
        //             newRow.find('input').each(function() {
        //                 var oldId = $(this).attr('id');
        //                 var newId = oldId.replace(/_\d+$/, '_' + counter);
        //                 $(this).attr('id', newId).attr('name', newId).val('');
        //             });
        //             newRow.find('.input-img').each(function() {
        //                 var oldId = $(this).attr('id');
        //                 var newId = oldId.replace(/_\d+$/, '_' + counter);
        //                 $(this).attr('id', newId).attr('name', newId).val('').attr('onchange', 'loadImgContent' + counter + '(event)');
        //             });
        //             newRow.find('label').each(function() {
        //                 var oldFor = $(this).attr('for');
        //                 var newFor = oldFor.replace(/_\d+$/, '_' + counter);
        //                 $(this).attr('for', newFor);
        //             });
        //             newRow.find('img').attr('id', 'create_content_image_' + counter + '_preview');
        //             newRow.find('small').each(function() {
        //                 var oldId = $(this).attr('id');
        //                 var newId = oldId.replace(/_\d+$/, '_' + counter);
        //                 $(this).attr('id', newId);
        //             });
        //             newRow.find('.btn-primary').attr('id', 'btn_upload_create_content_image_' + counter);
        //             newRow.find('.btn-danger').attr('id', 'btn_delete_create_content_image_' + counter);
        //             newRow.appendTo('.content-input');
        //         }
        //     });
        //     $('.btn-minus-content').click(function() {
        //         if (counter > 1) {
        //             $('.row-content-' + counter).remove();
        //             counter--;
        //         }
        //     });
        // });
    </script>
@endsection
