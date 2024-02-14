<li class="nav-heading">HOME</li>

<li class="nav-item">
    <a class="nav-link {{ (request()->segment(2) == 'dashboard') ? '' : 'collapsed' }}" href="{{ route('kretech.dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
    </a>
</li><!-- End Dashboard Nav -->

<li class="nav-heading">PAGE</li>

<li class="nav-item">
    <a class="nav-link {{ (request()->segment(2) == 'profile' || request()->segment(2) == 'portfolio' || request()->segment(2) == 'article') ? '' : 'collapsed' }}" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Contents</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse {{ (request()->segment(2) == 'profile' || request()->segment(2) == 'portfolio' || request()->segment(2) == 'article') ? 'show' : 'hide' }}" data-bs-parent="#sidebar-nav">
        <li>
            <a href="{{ route('kretech.profile') }}" class="{{ (request()->segment(2) == 'profile') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Profile</span>
            </a>
        </li>
        <li>
            <a href="{{ route('kretech.portfolio') }}" class="{{ (request()->segment(2) == 'portfolio') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Portfolio</span>
            </a>
        </li>
        <li>
            <a href="{{ route('kretech.article') }}" class="{{ (request()->segment(2) == 'article') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Article</span>
            </a>
        </li>
    </ul>
</li><!-- End Contents Nav -->

<li class="nav-heading">HISTORY</li>

<li class="nav-item">
    <a class="nav-link {{ (request()->segment(2) == 'activity') ? '' : 'collapsed' }}" href="{{ route('kretech.activity') }}">
        <i class="bi bi-person-lines-fill"></i>
        <span>Activity</span>
    </a>
</li><!-- End Activity Nav -->
