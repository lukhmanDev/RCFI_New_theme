@php
<<<<<<< HEAD
    $recipientNotifications = \App\Models\NotificationRecipient::where('user_id', Auth::id())
        ->with('notification')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
    $unreadNotificationsCount = \App\Models\NotificationRecipient::where('user_id', Auth::id())
        ->where('is_read', false)
        ->count();
=======
    $notifications = \App\Models\Notification::orderBy('created_at', 'desc')->take(6)->get();
    $unreadNotificationsCount = \App\Models\Notification::where('is_read', false)->count();
>>>>>>> 931b70b15894ca6c070c71c54872cb207eaf9da3
@endphp
<!-- Topbar Header -->
<header class="topbar">
    <div class="topbar-left">
        
        <button class="sidebar-collapse-btn" onclick="toggleSidebarCollapse()"><i class="bx bx-menu-alt-left"></i></button>

        <div class="topbar-search">
            <i class="bx bx-search"></i>
            <input type="text" id="topbarSearchInput" placeholder="Search anything...">
            <kbd class="topbar-search-kbd">Ctrl + K</kbd>
        </div>
    </div>

    <div class="topbar-right">
        <div class="topbar-icon-wrapper" style="position: relative; display: flex; align-items: center;">
            <button class="topbar-icon-btn" onclick="toggleTopbarNotificationsMenu(event)" style="background: none; border: none; cursor: pointer;">
                <i class="bx bx-bell"></i>
                @if($unreadNotificationsCount > 0)
                    <span class="topbar-badge" id="notif-badge">{{ $unreadNotificationsCount }}</span>
                @endif
            </button>
            
            <!-- Notifications Dropdown -->
            <div class="topbar-notifications-dropdown" id="topbarNotificationsDropdown">
                <div class="topbar-notifications-header">
                    <h4>Notifications</h4>
                    @if($unreadNotificationsCount > 0)
                        <button onclick="markAllNotificationsAsRead(event)" class="mark-read-btn" id="mark-all-read-btn">Mark all as read</button>
                    @endif
                </div>
                <div class="topbar-notifications-list">
<<<<<<< HEAD
                    @forelse($recipientNotifications as $recipient)
                        @php $notif = $recipient->notification; @endphp
                        @if($notif)
                            <a href="{{ $notif->url ?? '#' }}" class="topbar-notifications-item {{ !$recipient->is_read ? 'unread' : '' }}" onclick="markSingleNotificationAsRead(event, {{ $recipient->id }})">
                                <div class="notif-dot"></div>
                                <div class="notif-content">
                                    <span class="notif-title">{{ $notif->title }}</span>
                                    <p class="notif-message">{{ $notif->message }}</p>
                                    <span class="notif-time">{{ $recipient->created_at ? $recipient->created_at->diffForHumans() : 'Just now' }}</span>
                                </div>
                            </a>
                        @endif
=======
                    @forelse($notifications as $notif)
                        <a href="{{ $notif->url ?? '#' }}" class="topbar-notifications-item {{ !$notif->is_read ? 'unread' : '' }}" onclick="markSingleNotificationAsRead(event, {{ $notif->id }})">
                            <div class="notif-dot"></div>
                            <div class="notif-content">
                                <span class="notif-title">{{ $notif->title }}</span>
                                <p class="notif-message">{{ $notif->message }}</p>
                                <span class="notif-time">{{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now' }}</span>
                            </div>
                        </a>
>>>>>>> 931b70b15894ca6c070c71c54872cb207eaf9da3
                    @empty
                        <div class="topbar-notifications-empty">
                            <i class="bx bx-bell-off"></i>
                            <span>No new notifications</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <a href="" class="topbar-icon-btn">
            <i class="bx bx-envelope"></i>
            @php $unreadMessagesCount = $unreadMessagesCount ?? 7; @endphp
            @if($unreadMessagesCount > 0)
                <span class="topbar-badge">{{ $unreadMessagesCount }}</span>
            @endif
        </a>

        <div class="topbar-profile" onclick="toggleTopbarProfileMenu(event)">
            <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6WbkrAqlGF2Xzmb-prbginrkDNrv6zT05ID6KEjTbP2F-gn9w-wg1L3_NiSeXLq3HsqI&usqp=CAU' }}" alt="Profile">
            <div class="topbar-profile-info">
                <span class="topbar-profile-name">{{ Auth::user() ? Auth::user()->name : 'Admin' }}</span>
                <span class="topbar-profile-role">{{ Auth::user() ? Auth::user()->designation : 'Super Admin' }}</span>
            </div>
            <i class="bx bx-chevron-down topbar-chevron" id="topbarChevron"></i>

            <div class="topbar-profile-dropdown" id="topbarProfileDropdown">
                <a href="{{ route('profile.edit') }}"><i class="bx bx-user"></i> My Profile</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="bx bx-log-out-circle"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<style>
    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.65rem 1.25rem;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
    }

    .topbar-left { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0; }
    .topbar-right { display: flex; align-items: center; gap: 0.4rem; flex-shrink: 0; }

    .topbar-toggle,
    .sidebar-collapse-btn {
        display: flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 9px;
        background: transparent; border: none; cursor: pointer;
        color: #475569; font-size: 1.25rem; flex-shrink: 0;
        transition: background 0.15s ease;
    }
    .topbar-toggle:hover,
    .sidebar-collapse-btn:hover { background: #f1f5f9; }

    /* ---- Search ---- */
    .topbar-search {
        display: flex; align-items: center; gap: 0.55rem;
        background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 0.5rem 0.85rem; max-width: 340px; width: 100%;
        transition: border-color 0.15s ease, background 0.15s ease;
    }
    .topbar-search:focus-within { border-color: #cbd5e1; background: #ffffff; }
    .topbar-search i { color: #94a3b8; font-size: 1.05rem; flex-shrink: 0; }
    .topbar-search input {
        border: none; background: transparent; outline: none;
        font-size: 0.86rem; color: #1e293b; flex: 1; min-width: 0; font-family: inherit;
    }
    .topbar-search input::placeholder { color: #94a3b8; }
    .topbar-search-kbd {
        font-size: 0.7rem; font-family: inherit; color: #64748b;
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 5px;
        padding: 0.15rem 0.4rem; flex-shrink: 0;
    }

    /* ---- Icon buttons (bell / mail) ---- */
    .topbar-icon-btn {
        position: relative;
        display: flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 10px;
        color: #475569; font-size: 1.3rem; text-decoration: none;
        transition: background 0.15s ease;
    }
    .topbar-icon-btn:hover { background: #f1f5f9; }
    .topbar-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        border-radius: 999px;
        background: #ef4444;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        line-height: 1;
        box-sizing: border-box;
    }


    /* ---- Notifications Dropdown ---- */
    .topbar-notifications-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 320px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        z-index: 50;
        overflow: hidden;
    }
    .topbar-notifications-dropdown.show {
        display: block;
    }
    .topbar-notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .topbar-notifications-header h4 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    .mark-read-btn {
        background: none;
        border: none;
        color: #4f46e5;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
    }
    .mark-read-btn:hover {
        text-decoration: underline;
    }
    .topbar-notifications-list {
        max-height: 280px;
        overflow-y: auto;
    }
    .topbar-notifications-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        transition: background 0.15s ease;
        position: relative;
    }
    .topbar-notifications-item:hover {
        background: #f8fafc;
    }
    .topbar-notifications-item.unread {
        background: rgba(99, 102, 241, 0.02);
    }
    .topbar-notifications-item .notif-dot {
        width: 6px;
        height: 6px;
        background: transparent;
        border-radius: 50%;
        margin-top: 0.35rem;
        flex-shrink: 0;
    }
    .topbar-notifications-item.unread .notif-dot {
        background: #6366f1;
    }
    .notif-content {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        flex: 1;
        min-width: 0;
    }
    .notif-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        text-align: left;
    }
    .notif-message {
        margin: 0;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.3;
        word-break: break-word;
        text-align: left;
    }
    .notif-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.15rem;
        text-align: left;
    }
    .topbar-notifications-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        color: #94a3b8;
        gap: 0.5rem;
    }
    .topbar-notifications-empty i {
        font-size: 2rem;
    }
    .topbar-notifications-empty span {
        font-size: 0.8rem;
    }

    /* ---- Profile ---- */
    .topbar-profile {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        height: 40px;
        padding: 0 0.65rem 0 0.3rem;
        border-radius: 10px;
        cursor: pointer;
        margin-left: 0.4rem;
        transition: background 0.15s ease;
    }
    .topbar-profile:hover { background: #f1f5f9; }
    .topbar-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }

    .topbar-profile-info { display: flex; flex-direction: column; line-height: 1.2; }
    .topbar-profile-name { font-size: 0.84rem; font-weight: 700; color: #1e293b; white-space: nowrap; }
    .topbar-profile-role { font-size: 0.72rem; color: #64748b; white-space: nowrap; }

    .topbar-chevron {
        font-size: 1.1rem;
        color: #94a3b8;
        transition: transform 0.15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 0.2rem;
    }
    .topbar-profile.open .topbar-chevron { transform: rotate(180deg); }

    .topbar-profile-dropdown {
        display: none;
        position: absolute; top: calc(100% + 10px); right: 0; width: 190px;
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 0.4rem; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); z-index: 30;
    }
    .topbar-profile-dropdown.show { display: block; }
    .topbar-profile-dropdown a,
    .topbar-profile-dropdown button {
        width: 100%; display: flex; align-items: center; gap: 0.5rem;
        padding: 0.55rem 0.65rem; border-radius: 7px;
        font-size: 0.83rem; color: #334155; text-decoration: none;
        background: none; border: none; cursor: pointer; font-family: inherit; text-align: left;
    }
    .topbar-profile-dropdown a:hover,
    .topbar-profile-dropdown button:hover { background: #f1f5f9; }
    .topbar-profile-dropdown button { color: #ef4444; }

    @media (max-width: 640px) {
        .topbar-search { display: none; }
        .topbar-profile-info { display: none; }
    }
</style>

<script>
    function toggleTopbarProfileMenu(event) {
        event.stopPropagation();
        const profile = event.currentTarget;
        const dropdown = document.getElementById('topbarProfileDropdown');
        profile.classList.toggle('open');
        dropdown.classList.toggle('show');

        // Close notifications menu if open
        const notifDropdown = document.getElementById('topbarNotificationsDropdown');
        if (notifDropdown) notifDropdown.classList.remove('show');
    }

    function toggleTopbarNotificationsMenu(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('topbarNotificationsDropdown');
        dropdown.classList.toggle('show');
        
        // Hide profile dropdown if open
        const profileDropdown = document.getElementById('topbarProfileDropdown');
        if (profileDropdown) profileDropdown.classList.remove('show');
        const profile = document.querySelector('.topbar-profile');
        if (profile) profile.classList.remove('open');
    }

    function markAllNotificationsAsRead(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route("notifications.mark_all_read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the badge
                const badge = document.getElementById('notif-badge');
                if (badge) badge.remove();
                
                // Set all notification items as read
                document.querySelectorAll('.topbar-notifications-item').forEach(item => {
                    item.classList.remove('unread');
                });
                
                // Hide mark-all-read button
                const btn = document.getElementById('mark-all-read-btn');
                if (btn) btn.remove();
            }
        })
        .catch(err => console.error('Error marking all as read:', err));
    }

    function markSingleNotificationAsRead(event, id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/admin/notifications/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .catch(err => console.error('Error marking single as read:', err));
    }

    document.addEventListener('click', function () {
        const dropdown = document.getElementById('topbarProfileDropdown');
        const profile = document.querySelector('.topbar-profile');
        if (dropdown) dropdown.classList.remove('show');
        if (profile) profile.classList.remove('open');

        const notifDropdown = document.getElementById('topbarNotificationsDropdown');
        if (notifDropdown) notifDropdown.classList.remove('show');
    });

    // Ctrl+K / Cmd+K focuses the search box
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            document.getElementById('topbarSearchInput')?.focus();
        }
    });
</script>