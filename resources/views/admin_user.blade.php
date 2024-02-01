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

                        <!-- User List -->
                        <table class="table table-striped table-hover">
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
                                            <a href="" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i></a>
                                            <a href="" class="btn btn-danger btn-sm"><i class="bi bi-ban"></i></a>
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
    </section>
@endsection
