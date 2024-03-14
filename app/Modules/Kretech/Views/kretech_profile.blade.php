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
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
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
                                <form role="form" method="post" action="{{ route('kretech.profile.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3 d-none">
                                        <label for="updatefor" class="col-md-4 col-lg-3 col-form-label">Update For</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="updatefor" type="text" class="form-control" value="profile">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded" src="{{ URL::asset('assets/img/kretech_img_profile_' . $profile['profile']['cod'] . '.jpg') }}" id="profile_image_preview" alt="Profile"> <br>
                                            <input class="d-none" type="file" class="form-control" accept=".jpg" onchange="loadImgProfile(event)" name="profile_image" id="profile_image">
                                            <small id="profile_image_warning" class="text-danger fst-italic">* dimensions must 120 x 120 in PNG (max size: 500KB)</small>
                                            <small id="profile_image_response" class="text-danger fst-italic"></small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-primary btn-sm" id="btn_upload_profile_image"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-danger btn-sm" id="btn_delete_profile_image"><i class="bi bi-trash"></i></a>
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
                                <form role="form" method="post" action="{{ route('kretech.profile.store') }}" enctype="multipart/form-data">
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

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        var _URL = window.URL || window.webkitURL;

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

            // Handle image change event using event delegation
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
                            url: "{{ route('kretech.profile.store') }}",
                            type: 'POST',
                            data: {_token: '{{ csrf_token() }}'},
                            success: function(response) {
                                // console.log(response);

                                // early change image
                                $('.img-profile-nav').attr('src', response.src);
                                $('.img-profile').attr('src', response.src);
                                $('#profile_image_preview').attr('src', response.src);

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
