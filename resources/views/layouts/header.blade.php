<!-- Topbar Header -->
<header class="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()"><i class="bx bx-menu"></i></button>
    <button class="sidebar-collapse-btn" onclick="toggleSidebarCollapse()"><i class="bx bx-menu-alt-left"></i></button>
    <div class="topbar-title" style="margin-left: 0.5rem;">@yield('title')</div>
    
    <div class="topbar-profile" onclick="toggleProfileMenu(event)">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6WbkrAqlGF2Xzmb-prbginrkDNrv6zT05ID6KEjTbP2F-gn9w-wg1L3_NiSeXLq3HsqI&usqp=CAU" alt="Profile">
        <div class="profile-info">
            <span class="profile-name">{{ Auth::user() ? Auth::user()->name : 'Admin' }}</span>
            <span class="profile-role">{{ Auth::user() ? Auth::user()->designation : 'Super Admin' }}</span>
        </div>
        <div class="profile-dropdown" id="profileDropdown">
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="submit"><i class="bx bx-log-out-circle"></i> Log Out</button>
            </form>
        </div>
    </div>
</header>
