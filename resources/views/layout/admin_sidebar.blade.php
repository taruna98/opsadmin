<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">
    
    <!-- Sidebar Home Here -->
    @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin']))
      @include('layout.home_sidebar')
    @endif
    
    <!-- Sidebar Kretech ID Here -->
    @if(auth()->check() && auth()->user()->hasRole('kretech member'))
      @include('layout.kretech_sidebar')
    @endif

    @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin']))
      <li class="nav-heading">ADMINISTRATOR</li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('user') }}">
          <i class="bi bi-people"></i>
          <span>User</span>
        </a>
      </li><!-- End Users Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('activity_log') }}">
          <i class="bi bi-person-lines-fill"></i>
          <span>Activity Log</span>
        </a>
      </li><!-- End Users Nav -->
    @endif

  </ul>

</aside>