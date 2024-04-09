@extends('layout.admin_app')
@section('content')
  <style>
    .activity-search {
      width: 25%;
      margin: 0 0 2rem auto;
    }
    
    @media (max-width: 767.98px) {
      .activity-search {
        width: 100%;
      }
    }
  </style>

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

            <input class="form-control activity-search" type="search" placeholder="Search Activity" aria-label="Search">

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

  <script>
    // search activity
    $(document).ready(function(){
      $('.activity-search').on('keyup', function(){
        var searchVal = $(this).val().toLowerCase();
        $('.list-group-item').each(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(searchVal) !== -1);
        });
      });
    });
  </script>
@endsection
