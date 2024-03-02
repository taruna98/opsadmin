<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">
    
    <!-- Sidebar Home Here -->
    @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin', 'kretech member']))
      @if(request()->segment(1) == '' || request()->segment(1) == 'user' || request()->segment(1) == 'activity-log')
        @include('layout.home_sidebar')
      @endif
    @endif
    
    <!-- Sidebar Kretech ID Here -->
    @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin', 'kretech member']))
      @if(request()->segment(1) == 'kretech')
        @include('layout.kretech_sidebar')
      @endif
    @endif

    @if(auth()->check() && auth()->user()->hasAnyRole(['owner', 'admin']) && request()->segment(1) == '')
      <li class="nav-heading">ADMINISTRATOR</li>
      
      <li class="nav-item">
        <a class="nav-link {{ (request()->segment(1) == 'user') ? '' : 'collapsed' }}" href="{{ route('user') }}">
          <i class="bi bi-people"></i>
          <span>User</span>
        </a>
      </li><!-- End Users Nav -->
      
      <li class="nav-item">
        <a class="nav-link {{ (request()->segment(1) == 'activity-log') ? '' : 'collapsed' }}" href="{{ route('activity_log') }}">
          <i class="bi bi-person-lines-fill"></i>
          <span>Activity Log</span>
        </a>
      </li><!-- End Activity Log Nav -->
    @endif

  </ul>

</aside>