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

          @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin']))
            <!-- Admin Card -->
            <div class="col-md-6">
              <div class="card mb-1">
                <input class="form-control search-kretech-user" type="search" placeholder="Search" aria-label="Search">
              </div>
              <div class="card mb-1">
                <ul class="list-user-app list-group list-group-flush">
                  <li class="list-group-item header-user bg-primary text-light text-center sticky-top">User List</li>
                  @foreach ($user_kretech as $user)
                    <li class="list-group-item item-user">{{ $user->email }}</li>
                  @endforeach
                </ul>
              </div>
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Kretech ID</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div>
                    <div class="ps-3">
                      <h6>CMS</h6>
                      <div class="d-flex justify-content-center align-items-center py-2">
                        total user <span class="badge rounded-pill bg-primary ms-2">{{ count($user_kretech) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Kretech Card -->
          @elseif(auth()->check() && auth()->user()->hasRole(['kretech member']))
            <!-- Kretech Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <a href="{{ route('kretech.dashboard') }}">
                  <div class="card-body">
                    <h5 class="card-title">Kretech ID</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-cart"></i>
                      </div>
                      <div class="ps-3">
                        <h6>CMS</h6>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div><!-- End Kretech Card -->
          @endif

        </div>
      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <div class="col-lg-4">

        @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin']))
          <!-- Recent Activity -->
          <div class="card">
            <div class="filter d-none">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Recent Activity <span>| last six</span></h5>
              <div class="activity">
                @foreach($all_activity as $all_act)
                  <div class="activity-item d-flex">
                    <div class="activite-label">{{ str_replace('ago', '', \Carbon\Carbon::parse($all_act->created_at)->diffForHumans()) }}</div>
                    <i class='bi bi-circle-fill activity-badge text-{{ $all_act->module == "Admin" ? "primary" : "danger" }} align-self-start'></i>
                    <div class="activity-content">
                      {{ ucwords($all_act->name) }}
                      <br>
                      <a class="text-decoration-none text-dark fw-bold">{{ $all_act->activity }}</a>
                      <br>
                      {{ substr($all_act->created_at, 0, 16) }}
                    </div>
                  </div><!-- End activity item-->
                @endforeach
              </div>
            </div>
          </div><!-- End Recent Activity -->
        @endif

      </div><!-- End Right side columns -->

    </div>
  </section>

  <script>
    // live search kretech user
    $(document).ready(function(){
      $('.search-kretech-user').keyup(function(){
        var searchTerm = $(this).val().toLowerCase();
        $('.list-user-app .item-user').each(function(){
          var text = $(this).text().toLowerCase();
          if(text.indexOf(searchTerm) === -1){
            $(this).hide();
          } else {
            $(this).show();
          }
        });
      });
    });
  </script>
@endsection