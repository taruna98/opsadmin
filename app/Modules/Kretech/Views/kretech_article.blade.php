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
                                <label for="create_status" class="form-label">Select Status</label>
                                <select class="form-select" name="create_status" id="create_status" required>
                                    <option selected value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
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
                        <h5 class="modal-title">Article Edit</h5>
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
                                <label for="edit_status" class="form-label">Select Status</label>
                                <select class="form-select" name="edit_status" id="edit_status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                <div class="invalid-feedback">Please select status.</div>
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
