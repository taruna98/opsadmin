@extends('layout.admin_app')
@section('content')
  <div class="pagetitle">
    <h1>Activity</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="">Member</a></li>
        <li class="breadcrumb-item active">Activity</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">
      <!-- Side columns -->
      <div class="col-lg-12">

        <!-- Activity -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Recent Activities</h5>

            <!-- List group with Activity -->
            <div class="list-group">
              @foreach ($activity as $act)
                <a class="list-group-item list-group-item-action" aria-current="true">
                  <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">{{ ucwords($act['name']) }}</h5>
                      <small>{{ substr($act['created_at'], 0, 16) }}</small>
                  </div>
                  <p class="mb-1 fw-bold">{{ $act['activity'] }}</p>
                  <small>{{ $act['module'] }} - {{ $act['scene'] }}</small>
                </a>
              @endforeach
            </div><!-- End List group Activity -->

          </div>
        </div><!-- End Activity -->

      </div><!-- End Side columns -->
    </div>
  </section>
@endsection
