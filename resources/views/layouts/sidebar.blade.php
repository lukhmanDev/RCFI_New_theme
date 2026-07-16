<!-- Sidebar Drawer -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.home') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none;">
            <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" class="sidebar-logo sidebar-logo-full">
            <img src="{{ asset('images/logo_collapsed.png') }}" alt="RCFI Logo" class="sidebar-logo-collapsed">
        </a>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.home') }}" class="{{ Route::currentRouteName() === 'admin.home' ? 'active' : '' }}">
                <i class="bx bxs-grid-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @if(Auth::user()->role == 1)
        <li>
            <a href="{{ route('users') }}" class="{{ Route::currentRouteName() === 'users' ? 'active' : '' }}">
                <i class="bx bxs-id-card"></i>
                <span>Staffs</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('donors.index') }}" class="{{ request()->routeIs('donors.*') ? 'active' : '' }}">
                <i class="bx bxs-heart"></i>
                <span>Donors</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applications.index') }}" class="{{ (request()->routeIs('applications.*') && !request()->routeIs('applications.approved.*')) ? 'active' : '' }}">
                <i class="bx bxs-file-doc"></i>
                <span>Applications</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applications.approved.index') }}" class="approved-link {{ request()->routeIs('applications.approved.*') ? 'active' : '' }}">
                <i class="bx bxs-check-circle icon-approved"></i>
                <span>Approved Applications</span>
            </a>
        </li>

        <li>
            <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <i class="bx bxs-briefcase"></i>
                <span>Projects</span>
            </a>
        </li>
        <li>
            <a href="{{ route('contractors.index') }}" class="{{ request()->routeIs('contractors.*') ? 'active' : '' }}">
                <i class="bx bxs-hard-hat"></i>
                <span>Contractors</span>
            </a>
        </li>

        {{-- NOTE: adjust route names below ('reports.index' / 'settings.index')
             to whatever your actual named routes are for these pages. --}}
        <!-- <li>
            <a href="" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bx bxs-bar-chart-alt-2"></i>
                <span>Reports</span>
            </a>
        </li>
        <li>
            <a href="" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bx bxs-cog"></i>
                <span>Settings</span>
            </a>
        </li> -->
    </ul>

    <!-- Relocated Sidebar Profile Section -->
    <div class="sidebar-footer">
        <div class="sidebar-profile" onclick="toggleProfileMenu(event)">
            <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6WbkrAqlGF2Xzmb-prbginrkDNrv6zT05ID6KEjTbP2F-gn9w-wg1L3_NiSeXLq3HsqI&usqp=CAU' }}" alt="Profile">
            <div class="profile-info">
                <span class="profile-name">{{ Auth::user() ? Auth::user()->name : 'Admin' }}</span>
                <span class="profile-role">{{ Auth::user() ? Auth::user()->designation : 'Super Admin' }}</span>
            </div>
            <i class="bx bx-chevron-down profile-chevron" id="profileChevron"></i>

            <div class="profile-dropdown" id="profileDropdown">
                <a href="{{ route('profile.edit') }}"><i class="bx bx-user"></i> My Profile</a>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="submit" class="sidebar-logout">
                <i class="bx bx-log-out-circle"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</nav>

<style>
    /* ============ Sidebar theme (matches light modern dashboard reference) ============ */
    .sidebar {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e2e8f0;
    }

    .sidebar-menu { list-style: none; margin: 0; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.2rem; }

    .sidebar-menu li a {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.7rem 0.85rem;
        border-radius: 10px;
        color: #64748b;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 500;
        white-space: nowrap;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .sidebar-menu li a:hover { background: #f1f5f9; color: #1e293b; }
    .sidebar-menu li a i { font-size: 1.2rem; flex-shrink: 0; width: 1.2rem; text-align: center; color: inherit; }

    .sidebar-menu li a.active {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    .sidebar-menu li a.active i { color: #ffffff !important; }



    /* ============ Collapsed state — proper centering ============ */
    .sidebar.collapsed .sidebar-menu li a {
        justify-content: center;
        padding: 0.7rem;
        gap: 0;
    }
    .sidebar.collapsed .sidebar-menu li a span { display: none; }
    .sidebar.collapsed .sidebar-menu li a i { width: auto; }

    /* ============ Footer / profile ============ */
    .sidebar-footer {
        margin-top: auto;
        border-top: 1px solid #e2e8f0;
        padding: 0.85rem 0.9rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .sidebar-profile {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.55rem 0.65rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .sidebar-profile:hover { background: #f1f5f9; }

    .sidebar-profile img {
        width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;
        border: 2px solid #6366f1;
    }

    .profile-info { display: flex; flex-direction: column; overflow: hidden; flex: 1; }
    .profile-name { font-size: 0.85rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .profile-role { font-size: 0.72rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .profile-chevron { font-size: 1.1rem; color: #64748b; transition: transform 0.15s ease; flex-shrink: 0; }
    .sidebar-profile.open .profile-chevron { transform: rotate(180deg); }

    .profile-dropdown {
        display: none;
        position: absolute;
        bottom: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.4rem;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        z-index: 20;
    }
    .profile-dropdown.show { display: block; }
    .profile-dropdown a {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.55rem 0.65rem; border-radius: 7px;
        font-size: 0.83rem; color: #475569; text-decoration: none;
    }
    .profile-dropdown a:hover { background: #f1f5f9; }

    .sidebar-logout {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        width: 100%; padding: 0.65rem; border-radius: 10px;
        background: transparent; border: 1px solid #e2e8f0;
        color: #ef4444; font-size: 0.85rem; font-weight: 700; cursor: pointer;
        font-family: inherit; transition: background 0.15s ease, border-color 0.15s ease;
    }
    .sidebar-logout:hover { background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2); }

    /* Collapsed footer — avatar + chevron alignment, hide text */
    body.sidebar-collapsed .sidebar-footer {
        padding: 0.85rem 0.6rem 1rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.75rem !important;
        overflow: visible !important;
    }
    body.sidebar-collapsed .sidebar-profile {
        width: 42px !important;
        height: 42px !important;
        padding: 0 !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: transparent !important;
        border: none !important;
    }
    body.sidebar-collapsed .sidebar-profile img {
        margin: 0 auto !important;
        width: 36px !important;
        height: 36px !important;
    }
    body.sidebar-collapsed .profile-info {
        display: none !important;
    }
    body.sidebar-collapsed .profile-chevron {
        display: none !important;
    }
    body.sidebar-collapsed .sidebar-logout {
        width: 42px !important;
        height: 42px !important;
        padding: 0 !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid #e2e8f0 !important;
        background: transparent !important;
    }
    body.sidebar-collapsed .sidebar-logout span {
        display: none !important;
    }
    body.sidebar-collapsed .sidebar-logout i {
        font-size: 1.25rem !important;
        margin: 0 !important;
    }
    body.sidebar-collapsed .profile-dropdown {
        left: calc(100% + 12px) !important;
        bottom: 0 !important;
        top: auto !important;
        margin-left: 0 !important;
        width: 160px !important;
    }
</style>

<script>
    function toggleProfileMenu(event) {
        event.stopPropagation();
        const profile = event.currentTarget;
        const dropdown = document.getElementById('profileDropdown');
        profile.classList.toggle('open');
        dropdown.classList.toggle('show');
    }
    document.addEventListener('click', function () {
        const dropdown = document.getElementById('profileDropdown');
        const profile = document.querySelector('.sidebar-profile');
        if (dropdown) dropdown.classList.remove('show');
        if (profile) profile.classList.remove('open');
    });
</script>