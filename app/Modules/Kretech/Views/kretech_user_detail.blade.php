@extends('layout.admin_app')
@section('content')
    <style>
    </style>

    <div class="pagetitle">
        <h1>{{ $title }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Page Kretech</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kretech.user') }}">User</a></li>
                <li class="breadcrumb-item active">Detail User</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section detail-user">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="{{ URL::asset('assets/img/' . $img_profile . '.jpg') }}" alt="Profile" class="rounded-circle">
                        <h4 class="my-1">{{ $user['profile']['nme'] }}</h4>
                        <p class="my-1 text-muted"><i>{{ $user['profile']['eml'] }}</i></p>
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
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#user-profile">Profile</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-portfolio">Portfolio</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-article">Article</button>
                            </li>

                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active user-profile" id="user-profile">
                                <h5 class="card-title">About</h5>
                                <p class="small fst-italic">{{ $user['profile']['mds'] }}</p>

                                <h5 class="card-title">Details</h5>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">ID</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ $user['profile']['id'] }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Code</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ $user['profile']['cod'] }}</div>
                                </div>
                                
                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Proffesion</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $user['profile']['hsb']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Tools</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $user['profile']['mtl']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Skill</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ str_replace('|', ', ', $user['profile']['msk']) }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Register At</div>
                                    <div class="col-lg-9 col-md-8 fw-bold">{{ $user['profile']['created_at'] }}</div>
                                </div>

                                <div class="row my-2">
                                    <div class="col-lg-3 col-md-4 label">Status</div>
                                    <div class="col-lg-9 col-md-8 fw-bold"><span class="badge bg-{{ ($user['profile']['stt'] == 1) ? 'success' : 'danger' }}">{{ ($user['profile']['stt'] == 1) ? 'Active' : 'Not Active' }}</span></div>
                                </div>

                            </div>

                            <div class="tab-pane fade user-portfolio pt-3" id="user-portfolio">

                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user['portfolio'] as $port)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ ucfirst($port['ttl']) }}</td>
                                                <td>{{ ucfirst($port['ctg']) }}</td>
                                                <td>{{ $port['cat'] }}</td>
                                                <td><button type="button" class="btn btn-primary btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-port-detail{{ $port['id'] }}"><i class="bi bi-eye text-white"></i></button>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                            <div class="tab-pane fade user-article pt-3" id="user-article">
                                
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user['article'] as $artc)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ (strlen($artc['ttl']) > 16) ? substr($artc['ttl'], 0, 16) . '...' : $artc['ttl'] }}</td>
                                                <td>{{ ucfirst($artc['ctg']) }}</td>
                                                <td>{{ substr($artc['cat'], 0, 10) }}</td>
                                                <td><button type="button" class="btn btn-primary btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-artc-detail{{ $artc['id'] }}"><i class="bi bi-eye text-white"></i></button>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>

        {{-- modal portfolio detail --}}
        @foreach ($user['portfolio'] as $port)
            <div class="modal fade" id="modal-port-detail{{ $port['id'] }}" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">{{ $port['id'] }} - {{ strToUpper($port['ttl']) }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Category
                                    <span>{{ $port['ctg'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Client
                                    <span>{{ $port['cln'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Link
                                    <span><a href="" target="_blank">{{ $port['lnk'] }}</a></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Created At
                                    <span>{{ $port['cat'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status
                                    <span class="badge bg-{{ ($port['stt'] == '1') ? 'success' : 'danger' }}">{{ ($port['stt'] == '1') ? 'Active' : 'Not Active' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- modal article detail --}}
        @foreach ($user['article'] as $artc)
            <div class="modal fade" id="modal-artc-detail{{ $artc['id'] }}" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">{{ $artc['id'] }} - {{ strToUpper($artc['ttl']) }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Category
                                    <span>{{ $artc['ctg'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Created At
                                    <span>{{ $artc['cat'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status
                                    <span class="badge bg-{{ ($artc['stt'] == '1') ? 'success' : 'danger' }}">{{ ($artc['stt'] == '1') ? 'Active' : 'Not Active' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-start align-items-center">
                                    Description
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span>{!! $artc['dsc'] !!}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
    </section>

    <script>
    </script>
@endsection