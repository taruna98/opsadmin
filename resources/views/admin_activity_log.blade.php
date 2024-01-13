@extends('layout.admin_app')
@section('content')
  <div class="pagetitle">
    <h1>Activity Log</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="">Administrator</a></li>
        <li class="breadcrumb-item active">Activity Log</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">
      <!-- Side columns -->
      <div class="col-lg-12">

        <!-- Activity Log -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Recent Activities</h5>

            <!-- List group with Activity Log -->
            <div class="list-group">
              <a class="list-group-item list-group-item-action" aria-current="true">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">admin</h5>
                    <small>3 days ago</small>
                </div>
                <p class="mb-1">activity</p>
                <small>id content</small>
              </a>
            </div><!-- End List group Activity Log -->

          </div>
        </div><!-- End Activity Log -->

      </div><!-- End Side columns -->
    </div>
  </section>
@endsection
