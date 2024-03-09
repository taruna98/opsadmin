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
                        <img src="{{ URL::asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
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

                                <!-- Profile Edit Form -->
                                <form>
                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img class="rounded" src="{{ URL::asset('assets/img/profile-img.jpg') }}" alt="Profile"> <br>
                                            <input class="d-none" type="file" class="form-control pb-3 mt-2" accept=".png" id="profile-image">
                                            <small class="text-danger fst-italic">* dimensions must 120 x 120 in PNG (max size: 500KB)</small>
                                            <div class="pt-2">
                                                <a type="button" class="btn btn-upload-profile-image btn-primary btn-sm"><i class="bi bi-upload"></i></a>
                                                <a type="button" class="btn btn-delete-profile-image btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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
                                            <input name="profession" type="email" class="form-control" id="profession" value="{{ str_replace('|', ', ', $profile['profile']['hsb']) }}">
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
                                <!-- Change Password Form -->
                                <form>

                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control"
                                                id="currentPassword">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control"
                                                id="newPassword">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewpassword" type="password" class="form-control"
                                                id="renewPassword">
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
        $(document).ready(function() {
            // Ketika tombol di-klik, klikkan juga input file
            $(".btn-upload-profile-image").on('click', function() {
                $("#profile-image").click();
            });

            // Jika pengguna memilih file, tampilkan nama file yang dipilih
            $("#profile-image").change(function() {
                if (this.files.length > 0) {
                    // Di sini Anda dapat menambahkan logika untuk menampilkan nama file yang dipilih
                    // Misalnya: alert(this.files[0].name);
                }
            });
        });
    </script>
@endsection
