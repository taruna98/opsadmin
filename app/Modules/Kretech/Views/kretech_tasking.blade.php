@extends('layout.admin_app')
@section('content')
    <style>
        .list-user-app {
            height: 10rem;
            width: auto;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .list-user-app .header-user {
            z-index: 1;
        }
    </style>

    <div class="pagetitle">
        <h1>Tasking</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Task Kretech</a></li>
                <li class="breadcrumb-item active">Tasking</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Tasking Admin -->
    @if (auth()->check() &&
            auth()->user()->hasAnyRole(['owner', 'admin']))
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h5 class="card-title">Tasking List</h5>

                            <!-- Tasking List -->
                            <table class="table table-striped table-hover" id="table-tasking-kretech">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">User ID</th>
                                        <th scope="col">Module</th>
                                        <th scope="col">Scene</th>
                                        <th scope="col">Task</th>
                                        <th scope="col">Admin ID</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <!-- End User List -->

                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Tasking Modal -->
            <div class="modal fade" id="taskingDetailModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tasking Detail</h5>
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
                                            <b>User ID</b>
                                            <a class="text-decoration-none text-dark" id="detail_user_id">User ID</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Module</b>
                                            <a class="text-decoration-none text-dark" id="detail_module">Module</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Scene</b>
                                            <a class="text-decoration-none text-dark" id="detail_scene">Scene</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Task</b>
                                            <a class="text-decoration-none text-dark" id="detail_task">Task</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Admin ID</b>
                                            <a class="text-decoration-none text-dark" id="detail_admin_id">Admin ID</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Status</b>
                                            <a class="text-decoration-none text-dark" id="detail_status">Status</a>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <b>Request At</b>
                                            <a class="text-decoration-none text-dark" id="detail_request_at">Request At</a>
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
            // table member kretech
            $(document).ready(function() {
                $('#table-tasking-kretech').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('kretech.tasking') }}",
                        type: 'GET'
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'user_id',
                            name: 'user_id',
                            render: function(data, type, row, meta) {
                                if (data == 0) {
                                    return '<span class="badge rounded-pill bg-info">User Register</span>';
                                } else {
                                    return '<span class="badge rounded-pill bg-primary">' + data +
                                        '</span>';
                                }
                            }
                        },
                        {
                            data: 'module',
                            name: 'module'
                        },
                        {
                            data: 'scene',
                            name: 'scene'
                        },
                        {
                            data: 'task',
                            name: 'task'
                        },
                        {
                            data: 'admin_id',
                            name: 'admin_id',
                            render: function(data, type, row, meta) {
                                if (data == 0) {
                                    return '<span class="badge rounded-pill bg-primary">No Action</span>';
                                }
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row, meta) {
                                if (data == 1) {
                                    return '<span class="badge rounded-pill bg-info">Mail Send to User by Request</span>';
                                } else if (data == 2) {
                                    return '<span class="badge rounded-pill bg-info">User Verified Link</span>';
                                }
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, row, meta) {
                                var html = '<button class="btn-detail btn btn-info btn-sm mx-1" id="' +
                                    data + '"><i class="bi bi-eye text-white"></i></button>';
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
            });

            // detail modal
            $(document).on('click', '.btn-detail', function() {
                var taskId = $(this).attr('id');
                $.ajax({
                    url: '/kretech/tasking/detail/' + taskId,
                    type: 'GET',
                    success: function(data) {
                        $('#detail_id').text(data.id);
                        if (data.user_id == 0) {
                            $('#detail_user_id').html(
                                '<span class="badge rounded-pill bg-info">User Register</span>');
                        } else {
                            $('#detail_user_id').html(
                                '<span class="badge rounded-pill bg-primary">' + data.user_id +
                                '</span>');
                        }
                        $('#detail_module').text(data.module);
                        $('#detail_scene').text(data.scene);
                        $('#detail_task').text(data.task);
                        if (data.admin_id == 0) {
                            $('#detail_admin_id').html(
                                '<span class="badge rounded-pill bg-primary">No Action</span>');
                        }
                        if (data.status == 1) {
                            $('#detail_status').html(
                                '<span class="badge rounded-pill bg-info">Mail Send to User by Request</span>'
                            );
                        } else if (data.status == 2) {
                            $('#detail_status').html(
                                '<span class="badge rounded-pill bg-info">User Verified Link</span>');
                        }
                        $('#detail_request_at').text(data.created_at);
                        $('#taskingDetailModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        </script>
    @endif
@endsection
