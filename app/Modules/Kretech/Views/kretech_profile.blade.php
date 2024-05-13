@extends('layout.admin_app')
@section('content')
    <div class="pagetitle">
        <h1>Contents</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Page</a></li>
                <li class="breadcrumb-item">Contents</li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="{{ URL::asset('assets/img/kretech_img_profile_' . $profile['profile']['cod'] . '.jpg') }}" alt="Profile" class="rounded-circle img-profile">
                        <h4 class="my-1">{{ ucwords($profile['profile']['nme']) }}</h4>
                        <p class="my-1 text-muted"><i>{{ $profile['profile']['eml'] }}</i></p>
                        <div class="social-links mt-2 d-none">
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview">Overview</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-upload-cv">Upload CV</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-background">Change Background</button>
                            </li>

                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">About</h5>
                                <p class="small fst-italic">{{ $profile['profile']['mds'] }}</p>

                                <h5 class="card-title">Details</h5>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Code</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ $profile['profile']['cod'] }}</div>
                                </div>
                                
                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Profession</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $profile['profile']['hsb']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Tools</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $profile['profile']['mtl']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Skill</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $profile['profile']['msk']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Register At</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ $profile['profile']['created_at'] }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Status</div>
                                    <div class="col-lg-9 col-md-8 fw-bold"><span class="badge bg-{{ ($profile['profile']['stt'] == 1) ? 'success' : 'danger' }}">{{ ($profile['profile']['stt'] == 1) ? 'Active' : 'Not Active' }}</span></div>
                                </div>

                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                @if ($errors->any())
                                    <div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="bg-danger my-1 rounded"><span class="text-white px-1">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Profile Edit Form -->
                                <form role="form" method="post" action="{{ route('kretech.profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3 d-none">
                                        <label for="updatefor" class="col-md-4 col-lg-3 col-form-label">Update For</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="updatefor" type="text" class="form-control" value="profile">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="profile_image" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded" src="{{ URL::asset('assets/img/kretech_img_profile_' . $profile['profile']['cod'] . '.jpg') }}" id="profile_image_preview" alt="Profile"> <br>
                                            <input class="d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgProfile(event)" name="profile_image" id="profile_image">
                                            <small id="profile_image_warning" class="text-danger fst-italic">* dimensions must 120 x 120 in PNG (max size: 500KB)</small>
                                            <small id="profile_image_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_profile_image"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm {{ ($delete_image_profile == 1) ? 'd-none' : '' }}" id="btn_delete_profile_image"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="name" class="col-md-4 col-lg-3 col-form-label">Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" type="text" class="form-control" id="name" value="{{ ucwords($profile['profile']['nme']) }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                                        <div class="col-md-8 col-lg-9">
                                            <textarea name="about" class="form-control" id="about" style="height: 120px">{{ $profile['profile']['mds'] }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="profession" class="col-md-4 col-lg-3 col-form-label">Profession</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="profession" type="text" class="form-control" id="profession" value="{{ str_replace('|', ', ', $profile['profile']['hsb']) }}">
                                            <small class="text-danger fst-italic">* please separate profession by comma ','</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="tools" class="col-md-4 col-lg-3 col-form-label">Tools</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="tools" type="text" class="form-control" id="tools" value="{{ str_replace('|', ', ', $profile['profile']['mtl']) }}">
                                            <small class="text-danger fst-italic">* please separate tools by comma ','</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="skill" class="col-md-4 col-lg-3 col-form-label">Skill</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="skill" type="text" class="form-control" id="skill" value="{{ str_replace('|', ', ', $profile['profile']['msk']) }}">
                                            <small class="text-danger fst-italic">* please separate skill by comma ','</small>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Edit</button>
                                    </div>
                                </form><!-- End Profile Edit Form -->

                            </div>

                            <div class="tab-pane fade profile-upload-cv pt-3" id="profile-upload-cv">
                                @if ($errors->any())
                                    <div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="bg-danger my-1 rounded"><span class="text-white px-1">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Upload CV Form -->
                                <form role="form" method="post" action="{{ route('kretech.profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3 d-none">
                                        <label for="updatefor" class="col-md-4 col-lg-3 col-form-label">Update For</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="updatefor" type="text" class="form-control" value="cv">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="profile_cv_for" class="col-12 col-form-label">Curiculum Vitae</label>
                                        <div class="col-12">
                                            <span class="profile_cv_url d-none">{{ $cv_url }}</span>
                                            <span class="text-center d-none" id="loading_animation" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                <i class="fas fa-spinner fa-spin fa-2xl" style="color: #000"></i>
                                            </span>
                                            <div class="bg-secondary rounded p-1" id="profile_cv_preview">
                                                <embed class="w-100" style="height: 50vh" id="profile_cv_view" type="application/pdf">
                                            </div>
                                            <input type="file" class="form-control d-none" accept=".pdf" onchange="loadCv1(event)" name="profile_cv" id="profile_cv">
                                            <small id="profile_cv_response" class="text-danger fst-italic">* file must in PDF (max size: 2MB)</small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_profile_cv"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm {{ ($delete_profile_cv == 1) ? 'd-none' : '' }}" id="btn_delete_profile_cv"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form><!-- End Upload CV Form -->
                                
                            </div>

                            <div class="tab-pane fade profile-change-password pt-3" id="profile-change-password">
                                @if ($errors->any())
                                    <div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="bg-danger my-1 rounded"><span class="text-white px-1">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Change Password Form -->
                                <form role="form" method="post" action="{{ route('kretech.profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3 d-none">
                                        <label for="updatefor" class="col-md-4 col-lg-3 col-form-label">Update For</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="updatefor" type="text" class="form-control" value="password">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="current_password" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="input-group">
                                                <input name="current_password" type="password" class="form-control" id="current_password">
                                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="current_password"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="new_password" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="input-group">
                                                <input name="new_password" type="password" class="form-control" id="new_password">
                                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="new_password"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="new_password_2" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="input-group">
                                                <input name="new_password_2" type="password" class="form-control" id="new_password_2">
                                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="new_password_2"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Change</button>
                                    </div>
                                </form><!-- End Change Password Form -->

                            </div>

                            <div class="tab-pane fade profile-change-background pt-3" id="profile-change-background">
                                @if ($errors->any())
                                    <div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="bg-danger my-1 rounded"><span class="text-white px-1">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Change Background Form -->
                                <form role="form" method="post" action="{{ route('kretech.profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3 d-none">
                                        <label for="updatefor" class="col-md-4 col-lg-3 col-form-label">Update For</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="updatefor" type="text" class="form-control" value="background">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="background_home_for" class="col-md-4 col-lg-3 col-form-label">Background Home</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded img-fluid" src="{{ URL::asset('assets/img/kretech_img_profile_bg_home_' . $profile['profile']['cod'] . '.jpg') }}" id="background_home_preview" alt="BackgroundHome"> <br>
                                            <input class="d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgBgHome(event)" name="background_home" id="background_home">
                                            <small id="background_home_warning" class="text-danger fst-italic">* dimensions must 2880 x 1984 in JPG (max size: 500KB)</small>
                                            <small id="background_home_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_background_home"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm {{ ($delete_background_home == 1) ? 'd-none' : '' }}" id="btn_delete_background_home"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="background_service_for" class="col-md-4 col-lg-3 col-form-label">Background Service</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded img-fluid" src="{{ URL::asset('assets/img/kretech_img_profile_bg_service_' . $profile['profile']['cod'] . '.jpg') }}" id="background_service_preview" alt="BackgroundService"> <br>
                                            <input class="d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgBgService(event)" name="background_service" id="background_service">
                                            <small id="background_service_warning" class="text-danger fst-italic">* dimensions must 1440 x 692 in JPG (max size: 500KB)</small>
                                            <small id="background_service_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_background_service"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm {{ ($delete_background_service == 1) ? 'd-none' : '' }}" id="btn_delete_background_service"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="background_article_for" class="col-md-4 col-lg-3 col-form-label">Background Article</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded img-fluid" src="{{ URL::asset('assets/img/kretech_img_profile_bg_article_' . $profile['profile']['cod'] . '.jpg') }}" id="background_article_preview" alt="BackgroundArticle"> <br>
                                            <input class="d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgBgArticle(event)" name="background_article" id="background_article">
                                            <small id="background_article_warning" class="text-danger fst-italic">* dimensions must 1440 x 790 in JPG (max size: 500KB)</small>
                                            <small id="background_article_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_background_article"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm {{ ($delete_background_article == 1) ? 'd-none' : '' }}" id="btn_delete_background_article"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Edit</button>
                                    </div>
                                </form><!-- End Profile Edit Form -->

                            </div>

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        // background home image
        var loadImgBgHome = function(event) {
            var output = document.getElementById('background_home_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var backgroundHomePreview = $("#background_home_preview")[0];
            var backgroundHomeSrc = backgroundHomePreview.getAttribute('src');

            $("#btn_upload_background_home").on('click', function() {
                $("#background_home").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#background_home', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 2880 && this.height == 1984) {
                            $("#background_home_response").text("");
                        } else {
                            $("#background_home_warning").text("");
                            $("#background_home_response").text("* image dimensions not valid");
                            document.getElementById("background_home").value = "";
                            backgroundHomePreview.src = backgroundHomeSrc;
                        }
                    } else {
                        $("#background_home_warning").text("");
                        $("#background_home_response").text("* image over size");
                        document.getElementById("background_home").value = "";
                        backgroundHomePreview.src = backgroundHomeSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // delete background home image
        $(document).ready(function() {
            $('#btn_delete_background_home').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus background home Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kretech.profile.update') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_background_home'
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('#background_home_preview').attr('src', response.src);
                                $('#btn_delete_background_home').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Background Home Anda berhasil dihapus!',
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

        // background service image
        var loadImgBgService = function(event) {
            var output = document.getElementById('background_service_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var backgroundServicePreview = $("#background_service_preview")[0];
            var backgroundServiceSrc = backgroundServicePreview.getAttribute('src');

            $("#btn_upload_background_service").on('click', function() {
                $("#background_service").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#background_service', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 1440 && this.height == 692) {
                            $("#background_service_response").text("");
                        } else {
                            $("#background_service_warning").text("");
                            $("#background_service_response").text("* image dimensions not valid");
                            document.getElementById("background_service").value = "";
                            backgroundServicePreview.src = backgroundServiceSrc;
                        }
                    } else {
                        $("#background_service_warning").text("");
                        $("#background_service_response").text("* image over size");
                        document.getElementById("background_service").value = "";
                        backgroundServicePreview.src = backgroundServiceSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // delete background service image
        $(document).ready(function() {
            $('#btn_delete_background_service').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus background service Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kretech.profile.update') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_background_service'
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('#background_service_preview').attr('src', response.src);
                                $('#btn_delete_background_service').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Background Service Anda berhasil dihapus!',
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

        // background article image
        var loadImgBgArticle = function(event) {
            var output = document.getElementById('background_article_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var backgroundArticlePreview = $("#background_article_preview")[0];
            var backgroundArticleSrc = backgroundArticlePreview.getAttribute('src');

            $("#btn_upload_background_article").on('click', function() {
                $("#background_article").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#background_article', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 1440 && this.height == 790) {
                            $("#background_article_response").text("");
                        } else {
                            $("#background_article_warning").text("");
                            $("#background_article_response").text("* image dimensions not valid");
                            document.getElementById("background_article").value = "";
                            backgroundArticlePreview.src = backgroundArticleSrc;
                        }
                    } else {
                        $("#background_article_warning").text("");
                        $("#background_article_response").text("* image over size");
                        document.getElementById("background_article").value = "";
                        backgroundArticlePreview.src = backgroundArticleSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // delete background article image
        $(document).ready(function() {
            $('#btn_delete_background_article').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus background article Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kretech.profile.update') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_background_article'
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('#background_article_preview').attr('src', response.src);
                                $('#btn_delete_background_article').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Background Article Anda berhasil dihapus!',
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

        // profile image
        var loadImgProfile = function(event) {
            var output = document.getElementById('profile_image_preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        $(document).ready(function() {
            var profileImagePreview = $("#profile_image_preview")[0];
            var profileImageSrc = profileImagePreview.getAttribute('src');

            $("#btn_upload_profile_image").on('click', function() {
                $("#profile_image").click();
            });

            // handle image change event using event delegation
            $(document).on('change', '#profile_image', function(e) {
                var file = e.target.files[0];
                var img = new Image();

                img.onload = function() {
                    if (file.size / 1024 <= 500) {
                        if (this.width == 120 && this.height == 120) {
                            $("#profile_image_response").text("");
                        } else {
                            $("#profile_image_warning").text("");
                            $("#profile_image_response").text("* image dimensions not valid");
                            document.getElementById("profile_image").value = "";
                            profileImagePreview.src = profileImageSrc;
                        }
                    } else {
                        $("#profile_image_warning").text("");
                        $("#profile_image_response").text("* image over size");
                        document.getElementById("profile_image").value = "";
                        profileImagePreview.src = profileImageSrc;
                    }
                };

                img.onerror = function() {
                    alert("not a valid file: " + file.type);
                };

                img.src = URL.createObjectURL(file);
            });
        });

        // delete profile image
        $(document).ready(function() {
            $('#btn_delete_profile_image').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus avatar Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kretech.profile.update') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_profile_image'
                            },
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('.img-profile-nav').attr('src', response.src);
                                $('.img-profile').attr('src', response.src);
                                $('#profile_image_preview').attr('src', response.src);
                                $('#btn_delete_profile_image').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'Avatar Anda berhasil dihapus!',
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

        // upload profile cv
        var loadCv1 = function(event) {
            $('#loading_animation').removeClass('d-none');
            var fileCv1 = $('#profile_cv').prop('files')[0];
            if (fileCv1 && fileCv1.size <= 2048576) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    setTimeout(function() {
                        $('#profile_cv_response').text('');
                        $('#profile_cv_preview').removeClass('d-none');
                        $('#profile_cv_view').attr('src', URL.createObjectURL(fileCv1) + '#toolbar=0');
                        $('#loading_animation').addClass('d-none');
                    }, 1000);
                };
                reader.onerror = function(event) {
                    // console.error('file error : ', event.target.error);
                    $('#profile_cv_response').text('* file\'s problem');
                    $('#loading_animation').addClass('d-none');
                };
                reader.readAsDataURL(fileCv1);
            } else {
                $('#profile_cv_response').text("* file\'s over size");
                $('#loading_animation').addClass('d-none');
            }
        }
        $(document).ready(function() {
            var cv_url = $('.profile_cv_url').text();
            if (cv_url != '0') {
                $('#profile_cv_view').attr('src', window.location.origin + '/file/pdf/' + cv_url + '_CV.pdf#toolbar=0');
            }
            $('#btn_upload_profile_cv').on('click', function() {
                $('#profile_cv').click();
            });
        });

        // delete profile cv
        $(document).ready(function() {
            $('#btn_delete_profile_cv').click(function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Anda akan menghapus CV Anda!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085D6',
                    cancelButtonColor: '#D33',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kretech.profile.update') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'delete_profile_cv'
                            },
                            success: function(response) {
                                // console.log(response);

                                // early delete cv
                                $('#profile_cv_view').attr('src', '');
                                $('#btn_delete_profile_cv').addClass('d-none');

                                Swal.fire({
                                    title: 'Yeay!',
                                    text: 'CV Anda berhasil dihapus!',
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

        // button toggle see password
        $(document).ready(function() {
            $('.btn-toggle-password').click(function() {
                var targetInputId = $(this).data('target');
                var passwordInput = $('#' + targetInputId);
                var type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).find('i').toggleClass('bi-eye bi-eye-slash');
            });
        });
    </script>
@endsection
