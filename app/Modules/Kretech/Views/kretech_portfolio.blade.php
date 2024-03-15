@extends('layout.admin_app')
@section('content')
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

                        <!-- Portfolio List -->
                        <table class="table table-striped table-hover" id="table-kretech-portfolio">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Category</th>
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
    </section>

    <script>
        // table kretech portfolio
        $(document).ready(function() {
            $('#table-kretech-portfolio').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('kretech.portfolio') }}",
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'cod',
                        name: 'cod'
                    },
                    {
                        data: 'eml',
                        name: 'eml'
                    }
                ]
            });
        });
    </script>
@endsection
