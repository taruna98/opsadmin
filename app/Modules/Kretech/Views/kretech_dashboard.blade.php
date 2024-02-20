@extends('layout.admin_app')
@section('content')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <!-- Portfolio Card -->
                    <div class="col-xxl-6 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Portfolio</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ count($data['portfolio']) }}</h6>
                                        <span class="text-muted small pt-2 ps-1">item</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Portfolio Card -->

                    <!-- Customers Card -->
                    <div class="col-xxl-6 col-md-6">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">Article</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ count($data['article']) }}</h6>
                                        <span class="text-muted small pt-2 ps-1">item</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Customers Card -->

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activity <span>| last two</span></h5>
                        <div class="activity">

                            @foreach($activity as $act)
                                <div class="activity-item d-flex">
                                    <div class="activite-label">{{ str_replace('ago', '', \Carbon\Carbon::parse($act->created_at)->diffForHumans()) }}</div>
                                    <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                    <div class="activity-content">
                                        {{ ucwords($act->name) }}
                                        <br>
                                        <a class="text-decoration-none text-dark fw-bold">{{ $act->activity }}</a>
                                        <br>
                                        {{ substr($act->created_at, 0, 16) }}
                                    </div>
                                </div><!-- End activity item-->
                            @endforeach

                        </div>
                    </div>
                </div><!-- End Recent Activity -->

            </div><!-- End Right side columns -->

        </div>
    </section>
@endsection
