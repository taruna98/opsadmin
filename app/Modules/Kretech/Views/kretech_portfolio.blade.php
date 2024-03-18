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
                            <div class="col-md-6">
                                <label for="create_title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="create_title" id="create_title" value="{{ old('create_title') }}" required>
                                <div class="invalid-feedback">Please enter your title.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_link" class="form-label">Link</label>
                                <input type="text" class="form-control" name="create_link" id="create_link" value="{{ old('create_link') }}" required>
                                <div class="invalid-feedback">Please enter your link.</div>
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
                                            <img class="rounded w-100" src="{{ URL::asset('assets/img/kretech_img_content_item_default.jpg') }}" id="create_content_image_{{ $i }}_preview" alt="Profile"> <br>
                                            <input class="input-img d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgContent{{ $i }}(event)" name="create_content_image_{{ $i }}" id="create_content_image_{{ $i }}">
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

    </section>

    <script>
        // create content image 1
        var loadImgContent1 = function(event) {
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

        // create content image 2
        var loadImgContent2 = function(event) {
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

        // create content image 3
        var loadImgContent3 = function(event) {
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

        // create content image 4
        var loadImgContent4 = function(event) {
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

        // create content image 5
        var loadImgContent5 = function(event) {
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
                                    var html = '<button class="btn-edit btn btn-primary btn-sm mx-1" id="' + data + '"><i class="bi bi-eye text-white"></i></button>';
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
 
        // button plus or minus row content
        $(document).ready(function() {
            var currentRow = 1;
            $('.btn-plus-content').click(function() {
                if (currentRow < 5) {
                    $('.row-content-' + (currentRow + 1)).removeClass('d-none');
                    $('#create_content_title_' + (currentRow + 1)).prop("required", true);
                    $('#create_content_description_' + (currentRow + 1)).prop("required", true);
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
