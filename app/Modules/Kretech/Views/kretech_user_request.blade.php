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
        <h1>User Request</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Page Kretech</a></li>
                <li class="breadcrumb-item active">User Request</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <h5 class="card-title">User Request List</h5>

                        <!-- User Request List -->
                        <table class="table table-striped table-hover" id="table-user-request-kretech">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Module</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
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
    </section>

    <script>
        // table user request kretech
        $(document).ready(function() {
            $('#table-user-request-kretech').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('kretech.user_request') }}",
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
                        data: 'module',
                        name: 'module'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row, meta) {
                            if (data == 1) {
                                return '<span class="badge rounded-pill bg-info">Mail Send to User by Request</span>';
                            } else if (data == 2) {
                                return '<span class="badge rounded-pill bg-primary">User Verified Link</span>';
                            } else if (data == -2) {
                                return '<span class="badge rounded-pill bg-secondary">User Not Verified Link</span>';
                            } else if (data == 3) {
                                return '<span class="badge rounded-pill bg-success">User Registered</span>';
                            } else if (data == -3) {
                                return '<span class="badge rounded-pill bg-danger">User Rejected</span>';
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
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
    </script>
@endsection
