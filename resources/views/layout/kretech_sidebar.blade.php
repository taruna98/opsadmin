<li class="nav-heading">HOME</li>

<li class="nav-item">
    <a class="nav-link " href="/">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
    </a>
</li><!-- End Dashboard Nav -->

<li class="nav-heading">PAGE</li>

<li class="nav-item">
    <a class="nav-link" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#"> <!-- collapsed -->
        <i class="bi bi-menu-button-wide"></i><span>Contents</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
        <li>
            <a href="{{ route('kretech.profile') }}" class="active">
                <i class="bi bi-circle"></i><span>Profile</span>
            </a>
        </li>
        <li>
            <a href="{{ route('kretech.portfolio') }}">
                <i class="bi bi-circle"></i><span>Portfolio</span>
            </a>
        </li>
        <li>
            <a href="{{ route('kretech.article') }}">
                <i class="bi bi-circle"></i><span>Article</span>
            </a>
        </li>
    </ul>
</li><!-- End Contents Nav -->

<li class="nav-heading">HISTORY</li>

<li class="nav-item">
    <a class="nav-link collapsed" href="/">
        <i class="bi bi-grid"></i>
        <span>Activity Log</span>
    </a>
</li><!-- End Activity Log Nav -->