<!-- Sidebar Drawer -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bx bxs-shield"></i> RCFI Admin
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
            <a href="{{ route('applications.index') }}" class="{{ request()->routeIs('applications.*') ? 'active' : '' }}">
                <i class="bx bxs-file-doc"></i>
                <span>Applications</span>
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
