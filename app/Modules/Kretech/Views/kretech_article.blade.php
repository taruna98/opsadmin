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
                <li class="breadcrumb-item active">Article</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Article List</h5>

                        <div class="card-header-action my-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#articleCreateModal">Add <i class="bi bi-plus-lg"></i></button>
                        </div>

                        <!-- Article List -->
                        <table class="table table-striped table-hover" id="table-kretech-article">
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
                        <!-- End Article List -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Create Article Modal -->
        <div class="modal fade" id="articleCreateModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Article Create</h5>
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
                        action="{{ route('kretech.article.store') }}" enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- hidden input --}}
                            <input type="hidden" class="d-none" name="create_id" value="{{ $set_id }}" required>
                            <div class="col-md-4">
                                <label for="create_title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="create_title" id="create_title" value="{{ old('create_title') }}" required>
                                <div class="invalid-feedback">Please enter your title.</div>
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
                                <label for="create_bg_detail" class="form-label">Background Detail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_profile_bg_article_dtl_default.jpg') }}" id="create_bg_detail_preview" alt="BackgroundArticleDetail"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadDetailBgCreate(event)" name="create_bg_detail" id="create_bg_detail">
                                <small id="create_bg_detail_warning" class="text-danger fst-italic">* dimensions must 2880 x 830 in JPG (max size: 500KB)</small>
                                <small id="create_bg_detail_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_create_bg_detail"><i class="bi bi-upload"></i></a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_status" class="form-label">Select Status</label>
                                <select class="form-select" name="create_status" id="create_status" required>
                                    <option selected value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_image_1" class="form-label">Image Thumbnail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_article_thumbnail_default.jpg') }}" id="create_image_1_preview" alt="Article"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgArticleCreate1(event)" name="create_image_1" id="create_image_1">
                                <small id="create_image_1_warning" class="text-danger fst-italic">* dimensions must 760 x 480 in PNG (max size: 500KB)</small>
                                <small id="create_image_1_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_create_image_1"><i class="bi bi-upload"></i></a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="create_description" class="form-label">Description</label>
                                <textarea class="form-control" name="create_description" id="create_description">Content Article Here</textarea>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm my-2" type="submit">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Create Article Modal-->

        <!-- Edit Article Modal -->
        <div class="modal fade" id="articleEditModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Article Edit <span class="edit_art_id d-none"></span></h5>
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
                                <label for="edit_bg_detail" class="form-label">Background Detail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_profile_bg_article_dtl_default.jpg') }}" id="edit_bg_detail_preview" alt="BackgroundArticleDetail"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadDetailBgEdit(event)" name="edit_bg_detail" id="edit_bg_detail">
                                <small id="edit_bg_detail_warning" class="text-danger fst-italic">* dimensions must 2880 x 830 in JPG (max size: 500KB)</small>
                                <small id="edit_bg_detail_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_edit_bg_detail"><i class="bi bi-upload"></i></a>
                                    <a type="button" class="btn btn-danger btn-sm {{ ($delete_art_bg_detail == 1) ? 'd-none' : '' }}" id="btn_delete_edit_bg_detail"><i class="bi bi-trash"></i></a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Select Status</label>
                                <select class="form-select" name="edit_status" id="edit_status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_image_1" class="form-label">Image Thumbnail</label> <br>
                                <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_article_thumbnail_default.jpg') }}" id="edit_image_1_preview" alt="Article"> <br>
                                <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgArticleEdit1(event)" name="edit_image_1" id="edit_image_1">
                                <small id="edit_image_1_warning" class="text-danger fst-italic">* dimensions must 760 x 480 in PNG (max size: 500KB)</small>
                                <small id="edit_image_1_response" class="text-danger fst-italic"></small>
                                <div class="pt-2">
                                    <a type="button" class="btn btn-primary btn-sm" id="btn_upload_edit_image_1"><i class="bi bi-upload"></i></a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" name="edit_description" id="edit_description"></textarea>
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
        <!-- End Edit Article Modal-->

        <!-- Detail Article Modal -->
        <div class="modal fade" id="articleDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Article Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Background Detail</b>
                                        <img class="rounded w-50" id="detail_background_preview" alt="BackgroundDetail">
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Image Thumbnail</b>
                                        <img class="rounded w-50" src="" id="detail_image_1_preview" alt="Article">
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>ID</b>
                                        <a class="text-decoration-none text-dark" id="detail_id">Id</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <b>Title</b>
                                        <a class="text-decoration-none text-dark" id="detail_title">Title</a>
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
                                        <b>Description</b>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <div class="text-decoration-none text-dark" id="detail_description"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Detail Article Modal-->
        
    </section>

    <script>
        // table kretech article
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('kretech.article') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    $('#table-kretech-article').DataTable({
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
            var routeUrl = "{{ url('kretech/article/update/:id') }}".replace(':id', userId);
            // check div with this class
            if ($('.ck.ck-reset.ck-editor.ck-rounded-corners').length > 0) {
                $('.ck.ck-reset.ck-editor.ck-rounded-corners').remove();
            }
            $.ajax({
                url: '/kretech/article/edit/' + userId,
                type: 'GET',
                success: function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_created_at').val(data.cat);
                    $('.edit_art_id').text(data.id);
                    $('#edit_title').val(data.ttl);
                    if (data.ctg == 'art') {
                        $('#edit_category').val('art');
                    } else if (data.ctg == 'article') {
                        $('#edit_category').val('article');
                    } else if (data.ctg == 'coding') {
                        $('#edit_category').val('coding');
                    } else if (data.ctg == 'visual design') {
                        $('#edit_category').val('visual design');
                    }
                    $('#edit_bg_detail_preview').attr('src', window.location.origin + '/assets/img/kretech_img_profile_bg_article_dtl_' + data.cod + '_' + data.id + '.jpg');
                    if (data.stt == '1') {
                        $('#edit_status').val('1');
                    } else {
                        $('#edit_status').val('0');
                    }
                    // edit description article CK Editor
                    var articleEditEditor;
                    CKEDITOR.ClassicEditor.create(document.querySelector('#edit_description'), {
                        ckfinder: {
                            uploadUrl: "{{ route('kretech.article.upload.image') . '?_token=' . csrf_token() }}",
                        },
                        toolbar: {
                            items: [
                                'undo', 'redo', '|',
                                'heading', '|',
                                'bold', 'italic', 'underline', '|',
                                'alignment', '|',
                                'link', 'insertImage', 'blockQuote', 'insertTable', 'htmlEmbed', '|',
                                'outdent', 'indent', '|',
                                'exportPDF', 'exportWord'
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        list: {
                            properties: {
                                styles: true,
                                startIndex: true,
                                reversed: true
                            }
                        },
                        heading: {
                            options: [{
                                    model: 'paragraph',
                                    title: 'Paragraph',
                                    class: 'ck-heading_paragraph'
                                },
                                {
                                    model: 'heading1',
                                    view: 'h1',
                                    title: 'Heading 1',
                                    class: 'ck-heading_heading1'
                                },
                                {
                                    model: 'heading2',
                                    view: 'h2',
                                    title: 'Heading 2',
                                    class: 'ck-heading_heading2'
                                },
                                {
                                    model: 'heading3',
                                    view: 'h3',
                                    title: 'Heading 3',
                                    class: 'ck-heading_heading3'
                                },
                                {
                                    model: 'heading4',
                                    view: 'h4',
                                    title: 'Heading 4',
                                    class: 'ck-heading_heading4'
                                },
                                {
                                    model: 'heading5',
                                    view: 'h5',
                                    title: 'Heading 5',
                                    class: 'ck-heading_heading5'
                                },
                                {
                                    model: 'heading6',
                                    view: 'h6',
                                    title: 'Heading 6',
                                    class: 'ck-heading_heading6'
                                }
                            ]
                        },
                        fontFamily: {
                            options: [
                                'default',
                                'Arial, Helvetica, sans-serif',
                                'Courier New, Courier, monospace',
                                'Georgia, serif',
                                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                'Tahoma, Geneva, sans-serif',
                                'Times New Roman, Times, serif',
                                'Trebuchet MS, Helvetica, sans-serif',
                                'Verdana, Geneva, sans-serif'
                            ],
                            supportAllValues: true
                        },
                        fontSize: {
                            options: [10, 12, 14, 'default', 18, 20, 22],
                            supportAllValues: true
                        },
                        htmlSupport: {
                            allow: [{
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }]
                        },
                        // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
                        htmlEmbed: {
                            showPreviews: true
                        },
                        // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
                        link: {
                            decorators: {
                                addTargetToExternalLinks: true,
                                defaultProtocol: 'https://',
                                toggleDownloadable: {
                                    mode: 'manual',
                                    label: 'Downloadable',
                                    attributes: {
                                        download: 'file'
                                    }
                                }
                            }
                        },
                        // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
                        mention: {
                            feeds: [{
                                marker: '@',
                                feed: [
                                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes',
                                    '@chocolate', '@cookie', '@cotton', '@cream',
                                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread',
                                    '@gummi', '@ice', '@jelly-o',
                                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding',
                                    '@sesame', '@snaps', '@soufflé',
                                    '@sugar', '@sweet', '@topping', '@wafer'
                                ],
                                minimumCharacters: 1
                            }]
                        },
                        removePlugins: [
                            'CKBox',
                            // 'CKFinder',
                            'EasyImage',
                            // 'Base64UploadAdapter',
                            'RealTimeCollaborativeComments',
                            'RealTimeCollaborativeTrackChanges',
                            'RealTimeCollaborativeRevisionHistory',
                            'PresenceList',
                            'Comments',
                            'TrackChanges',
                            'TrackChangesData',
                            'RevisionHistory',
                            'Pagination',
                            'WProofreader',
                            'MathType',
                            'SlashCommand',
                            'Template',
                            'DocumentOutline',
                            'FormatPainter',
                            'TableOfContents',
                            'PasteFromOfficeEnhanced'
                        ]
                    })
                    .then(editor => {
                        editor.data.set(data.dsc);
                        articleEditEditor = editor; // simpan editor dalam variabel articleEditEditor
                    })
                    .catch(error => {
                        console.error(error);
                    });
                    $('#edit_image_1_preview').attr('src', window.location.origin + '/assets/img/kretech_img_article_' + data.id + '_thumbnail.jpg');
                    $('#articleEditModal').modal('show');
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
                url: '/kretech/article/detail/' + userId,
                type: 'GET',
                success: function(data) {
                    $('#detail_background_preview').attr('src', window.location.origin + '/assets/img/' + 'kretech_img_profile_bg_article_dtl_' + data.cod + '_' + data.id + '.jpg');
                    $('#detail_image_1_preview').removeAttr('src');
                    $('#detail_image_1_preview').attr('src', window.location.origin + '/assets/img/' + 'kretech_img_article_' + data.id + '_thumbnail.jpg');
                    // check image src not found in directory
                    var imgSrc = $('#detail_image_1_preview').attr('src');
                    var img = new Image();
                    $(img).on('error', function() {
                        $('#detail_image_1_preview').attr('src', window.location.origin + '/assets/img/kretech_img_content_article_default.jpg');
                    });
                    img.src = imgSrc;
                    $('#detail_id').text(data.id);
                    $('#detail_title').text(data.ttl);
                    $('#detail_category').text(data.ctg);
                    if (data.stt == '1') {
                        $('#detail_status').html('<span class="badge rounded-pill bg-success">Active</span>');
                    } else {
                        $('#detail_status').html('<span class="badge rounded-pill bg-danger">Not Active</span>');
                    }
                    $('#detail_created_at').text(data.cat);
                    $('#detail_updated_at').text(data.uat);
                    $('#detail_description').html(data.dsc);
                    $('#articleDetailModal').modal('show');
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
                    text: 'Anda akan menghapus article background detail Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var artId = $('.edit_art_id').text();
                        $.ajax({
                            url: "{{ route('kretech.article.file') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_article_background_detail',
                                art_id: artId
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('#edit_bg_detail_preview').attr('src', response.src);
                                $('#btn_delete_edit_bg_detail').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Article Background Detail Anda berhasil dihapus!',
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

        // create article image thumbnail
        var loadImgArticleCreate1 = function(event) {
            var output = document.getElementById('create_image_1_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#create_image_1_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_create_image_1").on('click', function() {
                $("#create_image_1").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#create_image_1', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 760 && this.height == 480) {
                            $("#create_image_1_response").text("");
                        } else {
                            $("#create_image_1_warning").text("");
                            $("#create_image_1_response").text("* image dimensions not valid");
                            document.getElementById("create_image_1").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#create_image_1_warning").text("");
                        $("#create_image_1_response").text("* image over size");
                        document.getElementById("create_image_1").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });
        // edit article image thumbnail
        var loadImgArticleEdit1 = function(event) {
            var output = document.getElementById('edit_image_1_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#edit_image_1_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_edit_image_1").on('click', function() {
                $("#edit_image_1").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#edit_image_1', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 760 && this.height == 480) {
                            $("#edit_image_1_response").text("");
                        } else {
                            $("#edit_image_1_warning").text("");
                            $("#edit_image_1_response").text("* image dimensions not valid");
                            document.getElementById("edit_image_1").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#edit_image_1_warning").text("");
                        $("#edit_image_1_response").text("* image over size");
                        document.getElementById("edit_image_1").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // create article CK Editor
        var articleCreateEditor;
        CKEDITOR.ClassicEditor.create(document.querySelector('#create_description'), {
            ckfinder: {
                uploadUrl: "{{ route('kretech.article.upload.image') . '?_token=' . csrf_token() }}",
            },
            toolbar: {
                items: [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'htmlEmbed', '|',
                    'outdent', 'indent', '|',
                    'exportPDF', 'exportWord'
                ],
                shouldNotGroupWhenFull: true
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            heading: {
                options: [{
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph'
                    },
                    {
                        model: 'heading1',
                        view: 'h1',
                        title: 'Heading 1',
                        class: 'ck-heading_heading1'
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2'
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3'
                    },
                    {
                        model: 'heading4',
                        view: 'h4',
                        title: 'Heading 4',
                        class: 'ck-heading_heading4'
                    },
                    {
                        model: 'heading5',
                        view: 'h5',
                        title: 'Heading 5',
                        class: 'ck-heading_heading5'
                    },
                    {
                        model: 'heading6',
                        view: 'h6',
                        title: 'Heading 6',
                        class: 'ck-heading_heading6'
                    }
                ]
            },
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [{
                    marker: '@',
                    feed: [
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes',
                        '@chocolate', '@cookie', '@cotton', '@cream',
                        '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread',
                        '@gummi', '@ice', '@jelly-o',
                        '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding',
                        '@sesame', '@snaps', '@soufflé',
                        '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                }]
            },
            removePlugins: [
                'CKBox',
                // 'CKFinder',
                'EasyImage',
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                'MathType',
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced'
            ]
        })
        .then(editor => {
            articleCreateEditor = editor; // simpan editor dalam variabel articleCreateEditor
        })
        .catch(error => {
            console.error(error);
        });

    </script>
@endsection
