<!-- Sidebar Drawer -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.home') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none;">
            <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" class="sidebar-logo">
        </a>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.home') }}" class="{{ Route::currentRouteName() === 'admin.home' ? 'active' : '' }}">
                <i class="bx bxs-dashboard"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @if(Auth::user()->role === 1)
        <li>
            <a href="{{ route('users') }}" class="{{ Route::currentRouteName() === 'users' ? 'active' : '' }}">
                <i class="bx bxs-user-account"></i>
                <span>Staffs</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('donors.index') }}" class="{{ request()->routeIs('donors.*') ? 'active' : '' }}">
                <i class="bx bxs-heart"></i>
                <span>Donors </span>
            </a>
        </li>
        
        <li>
            <a href="{{ route('applications.index') }}" class="{{ Route::currentRouteName() === 'applications.index' || Route::currentRouteName() === 'applications.category' ? 'active' : '' }}">
                <i class="bx bxs-file-doc"></i>
                <span>Applications</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applications.all') }}" class="{{ Route::currentRouteName() === 'applications.all' ? 'active' : '' }}">
                <i class="bx bxs-spreadsheet"></i>
                <span>All Applications</span>
            </a>
        </li>

        <li>
            <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <i class="bx bxs-briefcase"></i>
                <span>Projects</span>
            </a>
        </li>
    </ul>
</nav>
