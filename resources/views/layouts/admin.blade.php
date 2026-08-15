<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>@yield('title', 'Dashboard') | Admin Panel</title>
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js"></script>
    <script src="{{ asset('js/india_states_geo.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script>
        window.BROADCAST_DRIVER = "{{ config('broadcasting.default', env('BROADCAST_CONNECTION', 'log')) }}";
        if (typeof window.Echo === 'undefined') {
            const createDummyEcho = function() {
                const dummyChannel = {
                    listen: function() { return dummyChannel; },
                    listenToAll: function() { return dummyChannel; },
                    stopListening: function() { return dummyChannel; },
                    whisper: function() { return dummyChannel; },
                    error: function() { return dummyChannel; },
                    subscribed: function() { return dummyChannel; },
                };
                return {
                    private: function() { return dummyChannel; },
                    channel: function() { return dummyChannel; },
                    encryptedPrivate: function() { return dummyChannel; },
                    join: function() { return dummyChannel; },
                    leave: function() {},
                    leaveChannel: function() {},
                    disconnect: function() {},
                    socketId: function() { return undefined; },
                };
            };
            window.Echo = createDummyEcho();
        }
    </script>
    @vite(['resources/js/app.js'])

    <!-- Premium CSS Layout and Design System -->
    @if(request('embed'))
    <style>
        .sidebar, .sidebar-panel, .top-header, .top-navbar, header, nav, .app-header, .sidebar-wrapper, .navbar, aside, .header-panel, .group-header-panel, #sidebar, .sidebar-menu, .main-header, .layout-navbar, .layout-menu {
            display: none !important;
        }
        body, main, .main-content, .content-body, .wrapper, .page-wrapper, .admin-container, .content-wrapper {
            margin-left: 0 !important;
            margin-top: 0 !important;
            padding: 0.5rem 1rem !important;
            background: #ffffff !important;
            width: 100% !important;
            min-height: auto !important;
            overflow-y: auto !important;
        }
    </style>
    @endif
    <style>
        :root {
            --bg-color: #f5f7fb;
            --panel-bg: #ffffff;
            --panel-border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent-purple: #10b981;
            --accent-cyan: #10b981;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --sidebar-width: 260px;
        }

        /* Global Emerald Green Table Styling matching screenshot */
        table thead tr,
        .table thead tr,
        .table-custom thead tr {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        table thead th,
        .table thead th,
        .table-custom thead th {
            background-color: #10b981 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
        table thead th a,
        .table thead th a,
        .table-custom thead th a {
            color: #ffffff !important;
        }
        .page-btn.active,
        .pagination .active .page-link {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation Layout */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--panel-bg);
            border-right: 1px solid var(--panel-border);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            overflow: hidden;
        }

        .sidebar-logo {
            max-height: 48px;
            max-width: 100%;
            object-fit: contain;
            display: block;
            transition: transform 0.2s ease;
        }

        .sidebar-logo-collapsed {
            display: none;
            max-height: 45px;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.2s ease;
        }

        .sidebar-brand:hover .sidebar-logo,
        .sidebar-brand:hover .sidebar-logo-collapsed {
            transform: scale(1.03);
        }

        .sidebar-menu {
            list-style: none;
            padding: 1.5rem 1rem;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-menu a:hover {
            color: var(--text-main);
            background-color: #f1f5f9;
        }

        .sidebar-menu a.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, var(--accent-purple), #4f46e5);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }

        .sidebar-menu a.active i {
            color: #ffffff !important;
        }

        .sidebar-menu i {
            font-size: 1.25rem;
            transition: color 0.2s;
        }

        /* Main Content wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
            min-width: 0; /* Prevents overflow in flexbox layout */
            width: calc(100% - var(--sidebar-width));
        }

        /* Topbar Header styling */
        .topbar {
            background-color: var(--panel-bg);
            border-bottom: 1px solid var(--panel-border);
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .topbar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            cursor: pointer;
        }

        .topbar-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #10b981;
            object-fit: cover;
            flex-shrink: 0;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Dropdown style */
        .profile-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            width: 150px;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .profile-dropdown button,
        .profile-dropdown a {
            background: none;
            border: none;
            color: var(--text-muted);
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            text-align: left;
            width: 100%;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-dropdown button:hover,
        .profile-dropdown a:hover {
            background-color: #1f2937;
            color: var(--text-main);
        }

        /* Container Area styling */
        .content-container {
            padding: 2rem;
            flex-grow: 1;
            min-width: 0; /* Prevents flex children from overflowing */
            width: 100%;
        }

        /* Premium Dashboard Card components */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .stat-details h3 {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-details p {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.cyan {
            background-color: rgba(6, 182, 212, 0.1);
            color: var(--accent-cyan);
        }
        
        .stat-icon.purple {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--accent-purple);
        }

        .stat-icon.green {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
        }

        /* Premium Data Table styling */
        .panel {
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .panel-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Clean Forms & Inputs styling */
        .form-control-dark {
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            border-radius: 6px;
            padding: 0.65rem 1rem;
            color: var(--text-main);
            font-size: 0.9rem;
            width: 100%;
            transition: all 0.2s;
        }

        .form-control-dark:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 1px var(--accent-cyan);
        }

        .form-select-dark {
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            border-radius: 6px;
            padding: 0.65rem 1rem;
            color: var(--text-main);
            font-size: 0.9rem;
            width: 100%;
            transition: all 0.2s;
        }

        .form-select-dark:focus {
            outline: none;
            border-color: var(--accent-cyan);
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* Clean Tables styling */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .table-custom th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid var(--panel-border);
            color: var(--text-main);
            font-weight: 700;
        }

        .table-custom td {
            padding: 1rem;
            border-bottom: 1px solid var(--panel-border);
            color: var(--text-muted);
        }

        .table-custom tr:hover td {
            color: var(--text-main);
            background-color: #f8fafc;
        }

        .table-custom td a,
        .table td a,
        table td a {
            color: #10b981 !important;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .table-custom td a:hover,
        .table td a:hover,
        table td a:hover {
            color: #059669 !important;
        }

        /* Clean Buttons styling */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.1s, opacity 0.2s, box-shadow 0.2s;
        }

        .btn-primary,
        .btn-indigo,
        .btn-custom-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }

        .btn-custom:hover {
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(8, 164, 114, 0.25);
        }

        .btn-custom:active {
            transform: scale(0.98);
        }

        .btn-danger-custom {
            background: transparent;
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-danger-custom:hover {
            background-color: var(--accent-red);
            color: #ffffff;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .topbar-toggle {
                display: block;
            }
        }

        /* Custom Modern Confirm Modal Styles */
        #customConfirmModal {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh; 
            background-color: rgba(15, 23, 42, 0.3); 
            backdrop-filter: blur(6px); 
            display: none; 
            align-items: center; 
            justify-content: center; 
            z-index: 9999; 
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        #customConfirmModal.show {
            display: flex;
            opacity: 1;
        }
        .confirm-panel {
            background: #ffffff; 
            border: 1px solid var(--panel-border); 
            border-radius: 16px; 
            padding: 2.25rem 2rem; 
            width: 90%; 
            max-width: 440px; 
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); 
            transform: scale(0.9); 
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); 
            text-align: center;
        }
        #customConfirmModal.show .confirm-panel {
            transform: scale(1);
        }
        .confirm-icon-box {
            background-color: rgba(239, 68, 68, 0.12); 
            color: #ef4444; 
            border-radius: 50%; 
            width: 60px; 
            height: 60px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 1.25rem; 
            border: 1px solid rgba(239, 68, 68, 0.25);
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        @keyframes pulse-yellow {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }
        .confirm-panel.confirm-warning {
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
        }
        .confirm-icon-box.confirm-warning {
            background-color: rgba(245, 158, 11, 0.12) !important;
            color: #f59e0b !important;
            border: 1px solid rgba(245, 158, 11, 0.25) !important;
            animation: pulse-yellow 2s infinite !important;
        }
        .confirm-btn-ok.confirm-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important;
        }
        .confirm-btn-ok.confirm-warning:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4) !important;
        }
        .confirm-btn-cancel {
            flex: 1; 
            background: transparent; 
            border: 1px solid #1f2937; 
            color: #9ca3af; 
            padding: 0.75rem 1.5rem; 
            border-radius: 8px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            font-size: 0.9rem;
        }
        .confirm-btn-cancel:hover {
            background-color: rgba(255, 255, 255, 0.04);
            color: #ffffff;
            border-color: #374151;
        }
        .confirm-btn-ok {
            flex: 1; 
            background: linear-gradient(135deg, #ef4444, #dc2626); 
            border: none; 
            color: #ffffff; 
            padding: 0.75rem 1.5rem; 
            border-radius: 8px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            font-size: 0.9rem; 
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }
        .confirm-btn-ok:hover {
            background: linear-gradient(135deg, #ff6b6b, #ef4444);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }
        .confirm-btn-ok:active {
            transform: translateY(0);
        }

        /* Responsive Layout Adjustments for Laptops & Small Screens */
        @media (max-width: 1400px) {
            .content-container {
                padding: 1.25rem;
            }
            .panel {
                padding: 1.25rem;
                border-radius: 8px;
            }
        }
        @media (max-width: 1200px) {
            .content-container {
                padding: 1rem;
            }
            .panel {
                padding: 1rem;
            }
        }

        /* Permanently hide address/contact detail columns from application tables */
        .table-custom .col-village,
        .table-custom .col-post,
        .table-custom .col-panchayath,
        .table-custom .col-district,
        .table-custom .col-state,
        .table-custom .col-contact1,
        .table-custom .col-contact2,
        .table-custom .col-committee {
            display: none !important;
        }

        /* Responsive Column Adjustments for remaining columns */
        @media (max-width: 1300px) {
            .table-custom .col-location { display: none !important; }
        }
        @media (max-width: 1000px) {
            .table-custom .col-year { display: none !important; }
        }
        @media (max-width: 900px) {
            .table-custom .col-reg { display: none !important; }
        }
        @media (max-width: 800px) {
            .table-custom .col-committee { display: none !important; }
        }

        /* Global Force Overflow Container Bounds */
        div[style*="overflow-x: auto"] {
            width: 100% !important;
            display: block !important;
            max-width: 100% !important;
        }

        /* Global Auto-Removing Toast System (5 seconds) */
        #globalToastContainer {
            position: fixed !important;
            top: 24px !important;
            right: 24px !important;
            z-index: 999999 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
            max-width: 420px !important;
            width: calc(100vw - 48px) !important;
            pointer-events: none !important;
        }
        .custom-toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 1rem 1.25rem;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
            border: 1px solid #cbd5e1;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            animation: toast-slide-in 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .custom-toast.toast-success { border-left: 5px solid #10b981; }
        .custom-toast.toast-error { border-left: 5px solid #ef4444; }
        .custom-toast.toast-warning { border-left: 5px solid #f59e0b; }
        .custom-toast.toast-info { border-left: 5px solid #3b82f6; }
        .custom-toast .toast-icon { font-size: 1.35rem; flex-shrink: 0; margin-top: 0.1rem; }
        .custom-toast.toast-success .toast-icon { color: #10b981; }
        .custom-toast.toast-error .toast-icon { color: #ef4444; }
        .custom-toast.toast-warning .toast-icon { color: #f59e0b; }
        .custom-toast.toast-info .toast-icon { color: #3b82f6; }
        .custom-toast .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.08);
        }
        .custom-toast .toast-progress-bar {
            height: 100%;
            width: 100%;
            animation: toast-progress-shrink 5s linear forwards;
        }
        .custom-toast.toast-success .toast-progress-bar { background: #10b981; }
        .custom-toast.toast-error .toast-progress-bar { background: #ef4444; }
        .custom-toast.toast-warning .toast-progress-bar { background: #f59e0b; }
        .custom-toast.toast-info .toast-progress-bar { background: #3b82f6; }
        .custom-toast .toast-close {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            line-height: 1;
        }
        .custom-toast .toast-close:hover { color: #0f172a; }
        @keyframes toast-slide-in {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes toast-slide-out {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
        @keyframes toast-progress-shrink {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* Suppress inline static alert banners in favor of global auto-removing toast notifications */
        .content-container > div[style*="background-color: rgba(16, 185, 129"],
        .content-container > div[style*="background-color: rgba(239, 68, 68"],
        .content-container > .alert,
        main > div[style*="background-color: rgba(16, 185, 129"],
        main > div[style*="background-color: rgba(239, 68, 68"] {
            display: none !important;
        }

        .alert-success {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(16, 185, 129, 0.2) !important;
            color: var(--accent-green) !important;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
        }

        .alert-danger {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            color: var(--accent-red) !important;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
        }

        .alert-success::before {
            content: "\eac4" !important; /* Boxicons check mark circle */
            font-family: 'boxicons' !important;
            font-size: 1.25rem !important;
            color: var(--accent-green) !important;
        }

        .alert-danger::before {
            content: "\ea8b" !important; /* Boxicons error circle */
            font-family: 'boxicons' !important;
            font-size: 1.25rem !important;
            color: var(--accent-red) !important;
        }

        /* Toast animation */
        @keyframes toast-in-out {
            0% {
                transform: translateX(120%);
                opacity: 0;
            }
            8% {
                transform: translateX(0);
                opacity: 1;
            }
            90% {
                transform: translateX(0);
                opacity: 1;
            }
            100% {
                transform: translateX(120%);
                opacity: 0;
                display: none;
            }
        }

        /* Hide Details button from project tables */
        .btn-dots {
            display: none !important;
        }

        /* Sidebar Collapsed State (Desktop only) */
        @media (min-width: 769px) {
            body.sidebar-collapsed .sidebar {
                width: 70px;
            }
            body.sidebar-collapsed .main-wrapper {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            body.sidebar-collapsed .sidebar-logo-full {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-logo-collapsed {
                display: block !important;
            }
            body.sidebar-collapsed .sidebar-menu span {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-menu a {
                justify-content: center;
                padding: 0.75rem;
            }
            body.sidebar-collapsed .sidebar-menu i {
                font-size: 1.5rem;
                margin: 0;
            }
            body.sidebar-collapsed .sidebar-profile {
                justify-content: center;
                padding: 1rem 0.5rem;
            }
            body.sidebar-collapsed .sidebar-profile .profile-info {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-profile .profile-dropdown {
                left: 75px;
                right: auto;
                bottom: 10px;
                width: 150px;
                box-shadow: 10px 0 25px rgba(0, 0, 0, 0.5);
            }
        }

        .sidebar-collapse-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            margin-right: 1rem;
        }
        .sidebar-collapse-btn:hover {
            background-color: var(--panel-border);
            color: #ffffff;
        }
        @media (max-width: 768px) {
            .sidebar-collapse-btn {
                display: none !important;
            }
        }

        /* Sidebar Profile Styles */
        .sidebar-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            cursor: pointer;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--panel-border);
            background-color: rgba(17, 24, 39, 0.3);
            margin-top: auto;
            transition: all 0.3s ease;
        }

        .sidebar-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #10b981;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-profile .profile-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: opacity 0.3s ease;
        }

        .sidebar-profile .profile-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-profile .profile-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-profile .profile-dropdown {
            position: absolute;
            bottom: calc(100% + 5px);
            top: auto;
            left: 1rem;
            right: 1rem;
            width: calc(100% - 2rem);
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 8px;
            box-shadow: 0 -10px 25px rgba(15, 23, 42, 0.08);
            display: none;
            flex-direction: column;
            z-index: 200;
        }

        /* Generic Pagination styles */
        .page-btn:hover:not([disabled]) {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        .page-btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed !important;
            background-color: #f8fafc !important;
        }

        /* ==========================================
           Light-Theme Dark Inline Style Overrides
        ========================================== */
        
        /* Headers & dividers */
        .content-container h3[style*="color: #ffffff"],
        .content-container h3[style*="color: #fff"],
        .content-container h3[style*="color:#ffffff"],
        .content-container h3[style*="color:#fff"] {
            color: var(--text-main) !important;
        }
        
        .content-container div[style*="background-color: rgba(255,255,255,0.08)"],
        .content-container div[style*="background-color:rgba(255,255,255,0.08)"] {
            background-color: #e2e8f0 !important;
        }
        
        /* Table overrides (addresses invisible text columns in old subpage designs) */
        .table-custom td[style*="color: #ffffff"], 
        .table-custom td[style*="color: #fff"],
        .table-custom td[style*="color:#ffffff"],
        .table-custom td[style*="color:#fff"] {
            color: var(--text-main) !important;
        }
        
        .table-custom th[style*="color: #ffffff"], 
        .table-custom th[style*="color: #fff"],
        .table-custom th[style*="color:#ffffff"],
        .table-custom th[style*="color:#fff"] {
            color: var(--text-main) !important;
        }

        /* Search inputs & overlays inside subpages */
        #tableSearchInput, #tableSearch {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: var(--text-main) !important;
        }
        #tableSearchInput::placeholder, #tableSearch::placeholder {
            color: #94a3b8 !important;
        }

        /* Modal styling overrides to support light theme */
        div[id*="Modal"] label, div[id*="modal"] label,
        div[id*="Modal"] .form-label, div[id*="modal"] .form-label {
            color: var(--text-muted) !important;
        }
        
        div[id*="Modal"] h2, div[id*="modal"] h2,
        div[id*="Modal"] h3, div[id*="modal"] h3,
        div[id*="Modal"] h4, div[id*="modal"] h4 {
            color: var(--text-main) !important;
        }
        
        div[id*="Modal"] div[style*="color: #ffffff"], 
        div[id*="Modal"] div[style*="color: #fff"],
        div[id*="Modal"] div[style*="color:#ffffff"], 
        div[id*="Modal"] div[style*="color:#fff"],
        div[id*="modal"] div[style*="color: #ffffff"],
        div[id*="modal"] div[style*="color: #fff"] {
            color: var(--text-main) !important;
        }

        div[id*="Modal"] td[style*="color: #ffffff"],
        div[id*="Modal"] td[style*="color: #fff"],
        div[id*="Modal"] td[style*="color:#ffffff"],
        div[id*="Modal"] td[style*="color:#fff"],
        div[id*="modal"] td[style*="color: #ffffff"],
        div[id*="modal"] td[style*="color: #fff"] {
            color: var(--text-main) !important;
        }

        div[id*="Modal"] input, div[id*="modal"] input,
        div[id*="Modal"] select, div[id*="modal"] select,
        div[id*="Modal"] textarea, div[id*="modal"] textarea {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: var(--text-main) !important;
        }

        /* Modals parent background alignment */
        div[id*="Modal"] .panel, div[id*="modal"] .panel {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        div[id*="Modal"] .panel-header, div[id*="modal"] .panel-header {
            border-bottom: 1px solid #e2e8f0 !important;
        }

        /* Globally style Excel export buttons to look like clean secondary outline buttons */
        .panel-header a[href*="export"], 
        .panel-header a[href*="download"],
        .panel-header a[style*="2ecc71"],
        .panel-header a[style*="27ae60"] {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            box-shadow: none !important;
        }
        .panel-header a[href*="export"]:hover,
        .panel-header a[href*="download"]:hover,
        .panel-header a[style*="2ecc71"]:hover,
        .panel-header a[style*="27ae60"]:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        /* Prevent CLS layout flash on projects subpages */
        .group-header-panel, .controls-row {
            display: none !important;
        }

        /* Modern light-theme Modal design overrides */
        .modal-overlay {
            background-color: rgba(15, 23, 42, 0.3) !important;
            backdrop-filter: blur(6px) !important;
        }
        .modal-content-custom {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08) !important;
        }
        .modal-header-custom {
            background: #ffffff !important;
            color: #1e293b !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 1.25rem 1.5rem !important;
        }
        .modal-header-custom h3 {
            color: #1e293b !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
        }
        .modal-close-btn {
            color: #94a3b8 !important;
            font-size: 1.5rem !important;
            background: none !important;
            border: none !important;
            line-height: 1 !important;
        }
        .modal-close-btn:hover {
            color: #ef4444 !important;
        }
        .modal-body-custom {
            background-color: #ffffff !important;
            padding: 1.5rem !important;
        }
        .form-group-custom label {
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem !important;
        }
        .form-group-custom input, 
        .form-group-custom select, 
        .form-group-custom textarea {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #1e293b !important;
            font-family: inherit !important;
            border-radius: 8px !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.9rem !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }
        .form-group-custom input:focus, 
        .form-group-custom select:focus, 
        .form-group-custom textarea:focus {
            border-color: #6366f1 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }
        .form-group-custom input:disabled,
        .form-group-custom select:disabled {
            background-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
        }
        .modal-body-custom .btn-custom,
        .modal-content-custom button[type="submit"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            width: 100% !important;
            margin-top: 1rem !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15) !important;
            transition: opacity 0.2s ease !important;
        }
        .modal-body-custom .btn-custom:hover,
        .modal-content-custom button[type="submit"]:hover {
            opacity: 0.95 !important;
        }

        /* Project Details Page Overrides to prevent invisible white text and align styles with Light Mode */
        .detail-header-panel {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: var(--text-main) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
        }
        .detail-header-panel h2 {
            color: var(--text-main) !important;
        }
        .details-value {
            color: var(--text-main) !important;
        }
        .stage-tab {
            color: var(--text-muted) !important;
        }
        .stage-tab.active {
            color: #10b981 !important;
            background-color: rgba(16, 185, 129, 0.08) !important;
            border-bottom-color: #10b981 !important;
        }
        .stage-tab.completed {
            color: #06b6d4 !important;
        }
        .warning-box {
            background-color: rgba(99, 102, 241, 0.05) !important;
            border: 1px solid rgba(99, 102, 241, 0.15) !important;
            color: #4f46e5 !important;
        }
        .stage-success-banner {
            background-color: rgba(16, 185, 129, 0.05) !important;
            border: 1px solid rgba(16, 185, 129, 0.15) !important;
            color: #065f46 !important;
        }
        .stage-table td {
            color: var(--text-main) !important;
        }
        .stage-table tr:hover td {
            background-color: #f8fafc !important;
        }
        .stage-content-panel select,
        .stage-content-panel input[type="text"],
        .stage-content-panel input[type="number"],
        .stage-content-panel input[type="date"],
        .stage-content-panel textarea {
            background-color: #f8fafc !important;
            color: var(--text-main) !important;
            border: 1px solid #e2e8f0 !important;
        }
        .stage-content-panel select:focus,
        .stage-content-panel input[type="text"]:focus,
        .stage-content-panel input[type="number"]:focus,
        .stage-content-panel input[type="date"]:focus,
        .stage-content-panel textarea:focus {
            background-color: #ffffff !important;
            border-color: #6366f1 !important;
        }
        .stage-content-panel div[style*="background: rgba(255, 255, 255, 0.01)"],
        .stage-content-panel div[style*="background:rgba(255,255,255,0.01)"],
        .stage-content-panel div[style*="background: rgba(255, 255, 255, 0.02)"],
        .stage-content-panel div[style*="background:rgba(255,255,255,0.02)"] {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        /* Generic inline white text colors overridden globally inside content panel */
        .content [style*="color: #ffffff"],
        .content [style*="color:#ffffff"],
        .content [style*="color: #fff"],
        .content [style*="color:#fff"],
        .content [style*="color: white"],
        .content [style*="color:white"],
        .content [style*="color: #FFFFFF"],
        .content [style*="color:#FFFFFF"] {
            color: var(--text-main) !important;
        }
        .content select[style*="color: #ffffff"],
        .content select[style*="color:#ffffff"],
        .content select[style*="color: #fff"],
        .content select[style*="color:#fff"] {
            color: var(--text-main) !important;
        }
        .content input[style*="color: #ffffff"],
        .content input[style*="color:#ffffff"],
        .content input[style*="color: #fff"],
        .content input[style*="color:#fff"] {
            color: var(--text-main) !important;
        }

        /* Additional Notes box styling adjustments to prevent dark background low contrast text */
        p[style*="background-color: #121824"],
        p[style*="background-color:#121824"],
        p[style*="background-color: #121824;"],
        p[style*="background-color:#121824;"] {
            background-color: #f8fafc !important;
            color: var(--text-main) !important;
            border-color: #e2e8f0 !important;
        }
        .content h5[style*="color: var(--accent-cyan)"],
        .content h5[style*="color:var(--accent-cyan)"],
        .content h5[style*="color: var(--accent-cyan);"],
        .content h5[style*="color:var(--accent-cyan);"] {
            color: #4f46e5 !important;
            font-weight: 700 !important;
        }
        div[style*="background-color: rgba(255, 255, 255, 0.02)"],
        div[style*="background-color:rgba(255,255,255,0.02)"],
        div[style*="background-color: rgba(255, 255, 255, 0.01)"],
        div[style*="background-color:rgba(255,255,255,0.01)"],
        div[style*="background: rgba(255, 255, 255, 0.02)"],
        div[style*="background:rgba(255,255,255,0.02)"] {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
    </style>
</head>
<body>
    <div id="pjax-loader-bar" style="position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(135deg, #08A472, #2ecc71); z-index: 9999; transition: width 0.2s ease, opacity 0.4s ease; opacity: 0; pointer-events: none;"></div>
    <script>
        (function() {
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed && window.innerWidth > 768) {
                document.body.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <!-- Global Toast Notifications Container -->
    <div id="globalToastContainer"></div>

    @include('layouts.sidebar')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        @include('layouts.header')

        <!-- Main View Area -->
        <main class="content-container">
            @yield('content')
        </main>
    </div>

    <!-- Global Toast Engine & Toggle Script -->
    <script>
        function showToast(message, type = 'success', duration = 5000) {
            let container = document.getElementById('globalToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'globalToastContainer';
                document.body.appendChild(container);
            }

            const iconMap = {
                success: 'bx-check-circle',
                error: 'bx-error-circle',
                warning: 'bx-error',
                info: 'bx-info-circle'
            };

            const icon = iconMap[type] || iconMap.success;

            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            toast.innerHTML = `
                <i class="bx ${icon} toast-icon"></i>
                <div style="flex: 1; line-height: 1.4; word-break: break-word;">${message}</div>
                <button class="toast-close" onclick="removeToast(this.parentElement)">&times;</button>
                <div class="toast-progress">
                    <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
                </div>
            `;

            container.appendChild(toast);

            const timer = setTimeout(() => {
                removeToast(toast);
            }, duration);

            toast.dataset.timerId = timer;
        }

        function removeToast(toast) {
            if (!toast || toast.dataset.removing) return;
            toast.dataset.removing = 'true';
            if (toast.dataset.timerId) clearTimeout(parseInt(toast.dataset.timerId));
            toast.style.animation = 'toast-slide-out 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }

        window.showToast = showToast;
        window.removeToast = removeToast;

        window.addEventListener('show-toast', function(e) {
            const d = e.detail || {};
            showToast(d.message || d[0] || 'Notification', d.type || 'success', d.duration || 5000);
        });
        window.addEventListener('toast', function(e) {
            const d = e.detail || {};
            showToast(d.message || d[0] || 'Notification', d.type || 'success', d.duration || 5000);
        });

        function triggerSessionToasts() {
            @if (session('success'))
                showToast("{{ session('success') }}", 'success', 5000);
            @endif
            @if (session('error'))
                showToast("{{ session('error') }}", 'error', 5000);
            @endif
            @if (session('warning'))
                showToast("{{ session('warning') }}", 'warning', 5000);
            @endif
            @if (session('status'))
                showToast("{{ session('status') }}", 'info', 5000);
            @endif
            @if (isset($errors) && $errors->any())
                @foreach($errors->all() as $error)
                    showToast("{{ $error }}", 'error', 5000);
                @endforeach
            @endif
        }

        document.addEventListener('DOMContentLoaded', triggerSessionToasts);
        document.addEventListener('pjax:complete', triggerSessionToasts);
    </script>
    <script>
        // DOMContentLoaded interceptor to support deferred script loading via PJAX page transitions
        (function() {
            const originalAddEventListener = document.addEventListener;
            document.addEventListener = function(type, listener, options) {
                if (type === 'DOMContentLoaded' && (document.readyState === 'complete' || document.readyState === 'interactive')) {
                    setTimeout(listener, 0);
                } else {
                    originalAddEventListener.call(document, type, listener, options);
                }
            };
        })();

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function toggleSidebarCollapse() {
            const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
        }

        function toggleProfileMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function() {
            document.getElementById('profileDropdown').style.display = 'none';
        });

        // Global ExcelJS Styled Excel Export (.xlsx) function for all list tables
        async function downloadExcel(tableSelector = '.table-custom', customFilename = null) {
            const table = document.querySelector(tableSelector);
            if (!table) return;

            if (typeof ExcelJS === 'undefined') {
                alert('ExcelJS library is loading. Please try again in a moment.');
                return;
            }

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Export Data');

            const rows = table.querySelectorAll('tr');
            rows.forEach((tr) => {
                const rowData = [];
                const cols = tr.querySelectorAll('td, th');
                
                // Exclude last column if it contains Action buttons
                const lastColText = cols.length > 0 ? cols[cols.length - 1].innerText.toLowerCase() : '';
                const colCount = cols.length > 1 && (lastColText.includes('action') || lastColText.includes('edit')) ? cols.length - 1 : cols.length;

                for (let j = 0; j < colCount; j++) {
                    let cellText = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                    rowData.push(cellText);
                }
                if (rowData.length > 0) {
                    worksheet.addRow(rowData);
                }
            });

            // 1. Style Header Row (Row 1): Bold font, Green Background (#4CAF50), Centered alignment
            const headerRow = worksheet.getRow(1);
            headerRow.font = { name: 'Calibri', size: 11, bold: true, color: { argb: 'FFFFFF' } };
            headerRow.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF4CAF50' } // Green background (#4CAF50)
            };
            headerRow.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            headerRow.height = 28;

            // 2. Auto-fit column widths based on max character length (header + data rows)
            worksheet.columns.forEach(column => {
                let maxLength = 0;
                column.eachCell({ includeEmpty: true }, cell => {
                    const cellVal = cell.value !== null && cell.value !== undefined ? cell.value.toString() : '';
                    if (cellVal.length > maxLength) {
                        maxLength = cellVal.length;
                    }
                });
                column.width = Math.max(maxLength + 4, 12);
            });

            // 3. Trigger Excel (.xlsx) file download in browser
            let filename = customFilename || "export.xlsx";
            const titleEl = document.querySelector('.panel-title');
            if (titleEl && !customFilename) {
                filename = titleEl.innerText.toLowerCase().replace(/[^a-z0-9]+/g, '_') + '.xlsx';
            }

            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        }

        let activeConfirmCallback = null;
        let activeConfirmIsRejection = false;
        let activeConfirmIsSponsorDate = false;
        const originalNativeConfirm = window.confirm;
        window.originalNativeConfirm = originalNativeConfirm;

        function showCustomConfirm(message, callback, isRejection = false, isSponsorDate = false) {
            const modal = document.getElementById('customConfirmModal');
            const msgEl = document.getElementById('customConfirmMessage');

            // Fallback: if modal is not in DOM yet, use native confirm without recursion
            if (!modal || !msgEl) {
                const nativeConfirm = window.originalNativeConfirm || originalNativeConfirm;
                if (nativeConfirm.call(window, message)) {
                    if (typeof callback === 'function') callback();
                }
                return;
            }

            msgEl.innerText = message;
            activeConfirmCallback = callback;
            activeConfirmIsRejection = isRejection;

            const isUntick = message.toLowerCase().includes('untick');
            const isSponsor = message.toLowerCase().includes('sponsor') && !message.toLowerCase().includes('un-sponsor') && !message.toLowerCase().includes('unsponsor');
            const isUnsponsor = message.toLowerCase().includes('un-sponsor') || message.toLowerCase().includes('unsponsor');
            const isSuspend = message.toLowerCase().includes('suspend');
            const isActivate = message.toLowerCase().includes('activate') || message.toLowerCase().includes('reactivate');
            const isDelete = message.toLowerCase().includes('delete') || message.toLowerCase().includes('remove');
            
            activeConfirmIsSponsorDate = isSponsorDate || isSponsor;

            const panel = modal.querySelector('.confirm-panel');
            const iconBox = modal.querySelector('.confirm-icon-box');
            const icon = iconBox ? iconBox.querySelector('i') : null;
            const okBtn = document.getElementById('customConfirmOk');
            const remarksContainer = document.getElementById('confirmRemarksContainer');
            const remarksInput = document.getElementById('confirmRemarksInput');
            const remarksError = document.getElementById('confirmRemarksError');

            const sponsorDateContainer = document.getElementById('confirmSponsorDateContainer');
            const sponsorDateInput = document.getElementById('confirmSponsorDateInput');
            const sponsorDateError = document.getElementById('confirmSponsorDateError');

            if (remarksError) remarksError.style.display = 'none';
            if (remarksInput) remarksInput.style.borderColor = '#374151';
            if (sponsorDateError) sponsorDateError.style.display = 'none';
            if (sponsorDateInput) sponsorDateInput.style.borderColor = '#374151';
            
            if (remarksContainer) {
                remarksContainer.style.display = isRejection ? 'block' : 'none';
                if (remarksInput) remarksInput.value = '';
            }

            if (sponsorDateContainer) {
                sponsorDateContainer.style.display = activeConfirmIsSponsorDate ? 'block' : 'none';
                if (sponsorDateInput) {
                    sponsorDateInput.value = new Date().toISOString().split('T')[0];
                }
            }

            if (isRejection) {
                if (panel) {
                    panel.classList.add('confirm-warning');
                    panel.style.borderColor = '';
                }
                if (iconBox) {
                    iconBox.classList.add('confirm-warning');
                    iconBox.style.backgroundColor = '';
                    iconBox.style.color = '';
                    iconBox.style.borderColor = '';
                    iconBox.style.animation = '';
                }
                if (okBtn) {
                    okBtn.classList.add('confirm-warning');
                    okBtn.innerText = 'Reject';
                    okBtn.style.background = '';
                    okBtn.style.boxShadow = '';
                }
                if (icon) {
                    icon.className = 'bx bx-x-circle';
                }
            } else if (isSponsor) {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                    iconBox.style.color = '#10b981';
                    iconBox.style.borderColor = 'rgba(16, 185, 129, 0.25)';
                    iconBox.style.animation = 'none';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Sponsor';
                    okBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    okBtn.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.25)';
                }
                if (icon) {
                    icon.className = 'bx bxs-award';
                }
            } else if (isUnsponsor) {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = 'rgba(239, 68, 68, 0.12)';
                    iconBox.style.color = '#ef4444';
                    iconBox.style.borderColor = 'rgba(239, 68, 68, 0.25)';
                    iconBox.style.animation = 'none';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Un-sponsor';
                    okBtn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                    okBtn.style.boxShadow = '0 4px 12px rgba(239, 68, 68, 0.25)';
                }
                if (icon) {
                    icon.className = 'bx bx-x';
                }
            } else if (isActivate) {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                    iconBox.style.color = '#10b981';
                    iconBox.style.borderColor = 'rgba(16, 185, 129, 0.25)';
                    iconBox.style.animation = 'none';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Activate';
                    okBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    okBtn.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.25)';
                }
                if (icon) {
                    icon.className = 'bx bx-user-check';
                }
            } else if (isSuspend) {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = 'rgba(239, 68, 68, 0.12)';
                    iconBox.style.color = '#ef4444';
                    iconBox.style.borderColor = 'rgba(239, 68, 68, 0.25)';
                    iconBox.style.animation = 'none';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Suspend';
                    okBtn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                    okBtn.style.boxShadow = '0 4px 12px rgba(239, 68, 68, 0.25)';
                }
                if (icon) {
                    icon.className = 'bx bx-user-x';
                }
            } else if (isUntick) {
                if (panel) {
                    panel.classList.add('confirm-warning');
                    panel.style.borderColor = '';
                }
                if (iconBox) {
                    iconBox.classList.add('confirm-warning');
                    iconBox.style.backgroundColor = '';
                    iconBox.style.color = '';
                    iconBox.style.borderColor = '';
                    iconBox.style.animation = '';
                }
                if (okBtn) {
                    okBtn.classList.add('confirm-warning');
                    okBtn.innerText = 'Untick';
                    okBtn.style.background = '';
                    okBtn.style.boxShadow = '';
                }
                if (icon) {
                    icon.className = 'bx bx-info-circle';
                }
            } else if (isDelete) {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = '';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = '';
                    iconBox.style.color = '';
                    iconBox.style.borderColor = '';
                    iconBox.style.animation = '';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Delete';
                    okBtn.style.background = '';
                    okBtn.style.boxShadow = '';
                }
                if (icon) {
                    icon.className = 'bx bxs-trash-alt';
                }
            } else {
                if (panel) {
                    panel.classList.remove('confirm-warning');
                    panel.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                }
                if (iconBox) {
                    iconBox.classList.remove('confirm-warning');
                    iconBox.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                    iconBox.style.color = '#10b981';
                    iconBox.style.borderColor = 'rgba(16, 185, 129, 0.25)';
                    iconBox.style.animation = 'none';
                }
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Confirm';
                    okBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    okBtn.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.25)';
                }
                if (icon) {
                    icon.className = 'bx bx-check-circle';
                }
            }
            
            modal.style.display = 'flex';
            modal.offsetHeight;
            modal.classList.add('show');
        }

        function closeCustomConfirm(confirmed) {
            const modal = document.getElementById('customConfirmModal');
            const remarksInput = document.getElementById('confirmRemarksInput');
            const remarksError = document.getElementById('confirmRemarksError');
            const remarks = remarksInput ? remarksInput.value.trim() : '';

            const sponsorDateInput = document.getElementById('confirmSponsorDateInput');
            const sponsorDateError = document.getElementById('confirmSponsorDateError');
            const sponsoredDate = sponsorDateInput ? sponsorDateInput.value.trim() : '';

            if (confirmed && activeConfirmIsRejection && !remarks) {
                if (remarksError) remarksError.style.display = 'block';
                if (remarksInput) {
                    remarksInput.style.borderColor = '#ef4444';
                    remarksInput.focus();
                }
                return;
            }

            if (confirmed && activeConfirmIsSponsorDate && !sponsoredDate) {
                if (sponsorDateError) sponsorDateError.style.display = 'block';
                if (sponsorDateInput) {
                    sponsorDateInput.style.borderColor = '#ef4444';
                    sponsorDateInput.focus();
                }
                return;
            }

            if (remarksError) remarksError.style.display = 'none';
            if (remarksInput) remarksInput.style.borderColor = '#374151';
            if (sponsorDateError) sponsorDateError.style.display = 'none';
            if (sponsorDateInput) sponsorDateInput.style.borderColor = '#374151';

            const callback = activeConfirmCallback;
            const wasSponsor = activeConfirmIsSponsorDate;
            activeConfirmCallback = null;
            activeConfirmIsRejection = false;
            activeConfirmIsSponsorDate = false;
            
            modal.classList.remove('show');
            modal.style.display = 'none';

            if (confirmed && callback) {
                if (wasSponsor) {
                    callback(sponsoredDate);
                } else {
                    callback(remarks);
                }
            }
        }

        document.body.addEventListener('click', function(e) {
            if (e.target && (e.target.id === 'customConfirmCancel' || e.target.closest('#customConfirmCancel'))) {
                closeCustomConfirm(false);
            } else if (e.target && (e.target.id === 'customConfirmOk' || e.target.closest('#customConfirmOk'))) {
                closeCustomConfirm(true);
            } else if (e.target && e.target.id === 'customConfirmModal') {
                closeCustomConfirm(false);
            }
        });

        function confirmApplicationRejection(event, form) {
            if (form.dataset && form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return true;
            }
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            showCustomConfirm('Are you sure you want to reject this application?', (remarks) => {
                let remarksInput = form.querySelector('input[name="remarks"]');
                if (!remarksInput) {
                    remarksInput = document.createElement('input');
                    remarksInput.type = 'hidden';
                    remarksInput.name = 'remarks';
                    form.appendChild(remarksInput);
                }
                remarksInput.value = remarks || '';
                form.dataset.confirmed = 'true';
                if (typeof handleFormSubmit === 'function') {
                    handleFormSubmit({ target: form, preventDefault: () => {} });
                } else {
                    originalSubmit.call(form);
                }
            }, true);
            return false;
        }

        function confirmApplicationDeletion(event, form) {
            if (form.dataset && form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return true;
            }
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            showCustomConfirm('Are you sure you want to delete this application? This action cannot be undone.', () => {
                form.dataset.confirmed = 'true';
                if (typeof handleFormSubmit === 'function') {
                    handleFormSubmit({ target: form, preventDefault: () => {} });
                } else {
                    originalSubmit.call(form);
                }
            });
            return false;
        }

        function initCustomConfirmForms() {
            document.querySelectorAll('form[onsubmit], form[data-confirm]').forEach(form => {
                let message = null;
                const onsubmitValue = form.getAttribute('onsubmit');
                if (onsubmitValue) {
                    const match = onsubmitValue.match(/confirm\(['\"]([^\)]+?)['\"]\)/);
                    if (match) {
                        message = match[1];
                        form.removeAttribute('onsubmit');
                    }
                }
                if (!message && form.dataset.confirm) {
                    message = form.dataset.confirm;
                }
                if (!message) return;

                form.addEventListener('submit', function(event) {
                    if (form.dataset.confirmed === 'true') {
                        delete form.dataset.confirmed;
                        return;
                    }
                    event.preventDefault();
                    showCustomConfirm(message, () => {
                        form.dataset.confirmed = 'true';
                        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initCustomConfirmForms);



        // Global PJAX Loader Bar functions
        function showLoader() {
            const bar = document.getElementById('pjax-loader-bar');
            if (!bar) return;
            bar.style.opacity = '1';
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = '50%'; }, 10);
            setTimeout(() => { bar.style.width = '85%'; }, 300);
        }
        
        function hideLoader() {
            const bar = document.getElementById('pjax-loader-bar');
            if (!bar) return;
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => { bar.style.width = '0%'; }, 400);
            }, 150);
        }

        // Swap loaded HTML content into container and execute scripts
        
        // Global Fallback Modal Handlers
        window.openModal = function() {
            const modal = document.getElementById('addAppModal') || document.getElementById('addProjectModal') || document.getElementById('addModal') || document.getElementById('createModal');
            if (modal) modal.style.display = 'flex';
        };

        window.closeModal = function() {
            const modal = document.getElementById('addAppModal') || document.getElementById('addProjectModal') || document.getElementById('addModal') || document.getElementById('createModal');
            if (modal) modal.style.display = 'none';
        };

        window.closeEditModal = function() {
            const modal = document.getElementById('editAppModal') || document.getElementById('editProjectModal') || document.getElementById('editModal');
            if (modal) modal.style.display = 'none';
        };

        window.closeDetailsModal = function() {
            const modal = document.getElementById('detailsAppModal') || document.getElementById('detailsProjectModal') || document.getElementById('detailsModal');
            if (modal) modal.style.display = 'none';
        };

        window.toggleCulturalCenterNearby = function(el) {
            if (!el) return;
            const isYes = el.value === 'Yes';
            const form = el.closest('form');
            if (!form) return;
            const wrapper = form.querySelector('.distance-cc-wrapper, .edit-distance-cc-wrapper');
            const input = form.querySelector('.distance-cc-input, .edit-distance-cc-input');
            
            if (wrapper) {
                wrapper.style.display = isYes ? 'block' : 'none';
            }
            if (input) {
                input.required = isYes;
                if (!isYes) input.value = '';
            }
        };

        window.toggleEducationCenterNearby = function(el) {
            if (!el) return;
            const isYes = el.value === 'Yes';
            const form = el.closest('form');
            if (!form) return;
            const wrapper = form.querySelector('.distance-ec-wrapper, .edit-distance-ec-wrapper');
            const input = form.querySelector('.distance-ec-input, .edit-distance-ec-input');
            
            if (wrapper) {
                wrapper.style.display = isYes ? 'block' : 'none';
            }
            if (input) {
                input.required = isYes;
                if (!isYes) input.value = '';
            }
        };

        function swapContent(html, url) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newContent = doc.querySelector('.content-container');
            const currentContent = document.querySelector('.content-container');
            
            if (newContent && currentContent) {
                // Clean up any modal overlays that were moved to document.body by the previous page
                document.querySelectorAll('body > .modal-overlay, body > [id$="Modal"], body > [id$="modal"]').forEach(el => {
                    if (el.id === 'customConfirmModal') return;
                    if (!el.closest('.content-container') && !el.closest('#sidebar') && !el.closest('header') && !el.id.includes('pjax')) {
                        el.remove();
                    }
                });
                currentContent.innerHTML = newContent.innerHTML;
            }
            
            if (doc.title) {
                document.title = doc.title;
            }

            updateActiveSidebar(url || window.location.href);

            if (newContent) {
                const scripts = newContent.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    try {
                        const newScript = document.createElement('script');
                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                            newScript.async = false;
                        } else {
                            newScript.textContent = oldScript.textContent;
                        }
                        for (let attr of oldScript.attributes) {
                            if (attr.name !== 'src') {
                                newScript.setAttribute(attr.name, attr.value);
                            }
                        }
                        document.body.appendChild(newScript);
                        if (newScript.parentNode) {
                            newScript.remove();
                        }
                    } catch(err) {
                        console.warn('Script execution warning during PJAX swap:', err);
                    }
                });
            }
            
            window.scrollTo(0, 0);
            initAllTablePagers();
            document.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url } }));
        }

        function updateActiveSidebar(urlStr) {
            try {
                const url = new URL(urlStr || window.location.href, window.location.origin);
                const path = url.pathname;
                
                const isApprovedApps = path.startsWith('/admin/applications/approved');
                const isApplications = path.startsWith('/admin/applications') && !isApprovedApps;
                const isProjects = path.startsWith('/admin/projects');
                const isAgencies = path.startsWith('/admin/donors');
                const isContractors = path.startsWith('/admin/contractors');
                const isClusters = path.startsWith('/admin/clusters');
                const isThemes = path.startsWith('/admin/themes') || path.startsWith('/admin/subthemes');
                const isStaffs = path.startsWith('/admin/users');
                const isSocialAidFundReport = path.startsWith('/admin/reports/social-aid-funds');
                const isProjectReport = path.startsWith('/admin/reports/projects') || path.startsWith('/admin/reports/single-project');
                const isDashboard = path === '/dashboard' || path === '/admin/dashboard';

                const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
                sidebarLinks.forEach(link => {
                    const linkUrl = new URL(link.href, window.location.origin);
                    const linkPath = linkUrl.pathname;
                    
                    let isActive = false;

                    if (linkPath.includes('/admin/applications/approved')) {
                        isActive = isApprovedApps;
                    } else if (linkPath.includes('/admin/applications')) {
                        isActive = isApplications;
                    } else if (linkPath.includes('/admin/projects')) {
                        isActive = isProjects;
                    } else if (linkPath.includes('/admin/donors')) {
                        isActive = isAgencies;
                    } else if (linkPath.includes('/admin/contractors')) {
                        isActive = isContractors;
                    } else if (linkPath.includes('/admin/clusters')) {
                        isActive = isClusters;
                    } else if (linkPath.includes('/admin/themes')) {
                        isActive = isThemes;
                    } else if (linkPath.includes('/admin/users')) {
                        isActive = isStaffs;
                    } else if (linkPath.includes('/admin/reports/social-aid-funds')) {
                        isActive = isSocialAidFundReport;
                    } else if (linkPath.includes('/admin/reports/projects')) {
                        isActive = isProjectReport;
                    } else if (linkPath.includes('/dashboard')) {
                        isActive = isDashboard;
                    } else {
                        isActive = (path === linkPath) || (linkPath !== '/' && path.startsWith(linkPath));
                    }

                    if (isActive) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            } catch(e) {
                console.error('Error updating active sidebar:', e);
            }
        }

        // Local PJAX cache map and prefetch timeout tracker
        const pjaxCache = new Map();
        let hoverTimeout = null;

        function prefetchLink(url) {
            if (pjaxCache.has(url)) return;
            
            const promise = fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(res => {
                if (res.ok) {
                    return res.text().then(html => ({ html, url: res.url || url }));
                }
                throw new Error('Prefetch response not OK');
            }).catch(err => {
                pjaxCache.delete(url);
            });
            
            pjaxCache.set(url, promise);
        }

        function handleLinkHover(event) {
            const link = event.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                link.getAttribute('target') === '_blank' || 
                link.getAttribute('download') !== null || 
                link.getAttribute('data-no-pjax') !== null ||
                href.includes('/export') || href.includes('/download') ||
                href.includes('export=') || href.includes('download=')) {
                return;
            }
            
            try {
                const url = new URL(link.href);
                if (url.origin === window.location.origin) {
                    clearTimeout(hoverTimeout);
                    hoverTimeout = setTimeout(() => {
                        prefetchLink(link.href);
                    }, 65); // 65ms hover debounce to avoid prefetching on generic pointer sweeps
                }
            } catch (e) {}
        }

        function handleLinkMouseout() {
            clearTimeout(hoverTimeout);
        }

        // Load page via AJAX (utilizing cache if prefetch completed)
        async function loadPage(url, push = true) {
            showLoader();
            try {
                let data = null;
                if (pjaxCache.has(url)) {
                    data = await pjaxCache.get(url);
                    pjaxCache.delete(url); // Clear cache after consuming
                }
                
                if (!data) {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Network response not OK');
                    }
                    
                    const html = await response.text();
                    data = { html, url: response.url || url };
                }
                
                swapContent(data.html, data.url);
                
                if (push) {
                    window.history.pushState({ url: data.url }, '', data.url);
                }
            } catch (error) {
                console.error('PJAX navigation error, loading standard page:', error);
                window.location.href = url;
            } finally {
                hideLoader();
            }
        }

        // Silent refresh of current page
        async function reloadCurrentPageContent() {
            try {
                const response = await fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                if (response.ok) {
                    const html = await response.text();
                    swapContent(html, window.location.href);
                }
            } catch(e) {
                console.error('Silent refresh failed:', e);
            }
        }

        // Intercept local form submissions
        async function handleFormSubmit(event) {
            const form = event.target;
            const action = form.getAttribute('action') || window.location.href;
            if (form.getAttribute('data-no-pjax') !== null || form.getAttribute('data-no-ajax') !== null || (form.getAttribute('method') || '').toUpperCase() === 'GET' || form.id === 'globalSponsorForm' || action.includes('export') || action.includes('download') || action.includes('upload-photo') || action.includes('upload_photo') || action.includes('delete-photo') || action.includes('delete_photo') || action.includes('logout')) {
                return;
            }
            
            // Check if confirmation is required before proceeding with submit
            if (!form.dataset.confirmed || form.dataset.confirmed !== 'true') {
                const methodInput = form.querySelector('input[name="_method"]');
                const isDeleteMethod = (methodInput && methodInput.value.toUpperCase() === 'DELETE') || ((form.getAttribute('method') || '').toUpperCase() === 'DELETE');
                const onsubmitStr = form.getAttribute('onsubmit') || '';
                const isDeleteAction = isDeleteMethod || onsubmitStr.includes('confirm') || form.dataset.confirm || action.includes('/destroy') || action.includes('/delete');

                if (isDeleteAction) {
                    if (event && event.preventDefault) event.preventDefault();
                    if (event && event.stopPropagation) event.stopPropagation();
                    let confirmMessage = "Are you sure you want to delete this item? This action cannot be undone.";
                    const match = onsubmitStr.match(/confirm\(['"]([^)]+?)['"]\)/);
                    if (match && match[1]) {
                        confirmMessage = match[1];
                    } else if (form.dataset.confirm) {
                        confirmMessage = form.dataset.confirm;
                    }

                    showCustomConfirm(confirmMessage, function() {
                        form.dataset.confirmed = 'true';
                        handleFormSubmit({ target: form, preventDefault: () => {} });
                    });
                    return;
                }
            }
            delete form.dataset.confirmed;
            
            if (event && event.preventDefault) event.preventDefault();
            showLoader();
            
            const method = (form.getAttribute('method') || 'POST').toUpperCase();
            let fetchUrl = action;
            let body = null;
            if (method === 'GET') {
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, val] of formData.entries()) {
                    if (val !== null && val !== '') {
                        params.append(key, val);
                    }
                }
                const qStr = params.toString();
                fetchUrl = action.split('?')[0] + (qStr ? '?' + qStr : '');
            } else {
                body = new FormData(form);
            }
            
            try {
                const response = await fetch(fetchUrl, {
                    method: method,
                    body: body,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                const finalUrl = response.url || action;
                if (finalUrl.includes('/login')) {
                    window.location.href = finalUrl;
                    return;
                }
                const html = await response.text();
                
                swapContent(html, finalUrl);
                
                if (typeof closeModal === 'function') closeModal();
                if (typeof closeEditModal === 'function') closeEditModal();
                
                if (method === 'GET' || response.redirected || finalUrl !== action) {
                    window.history.pushState({ url: finalUrl }, '', finalUrl);
                }
            } catch (error) {
                console.error('Form submit PJAX error, calling fallback:', error);
                const orig = HTMLFormElement.prototype.submit;
                HTMLFormElement.prototype.submit = originalSubmit;
                form.submit();
                HTMLFormElement.prototype.submit = orig;
            } finally {
                hideLoader();
            }
        }

        // Intercept local link clicks
        function handleLinkClick(event) {
            const link = event.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                link.getAttribute('target') === '_blank' || 
                link.getAttribute('download') !== null || 
                link.getAttribute('data-no-pjax') !== null ||
                href.includes('/export') || href.includes('/download') ||
                href.includes('export=') || href.includes('download=')) {
                return;
            }
            
            try {
                const url = new URL(link.href);
                if (url.origin === window.location.origin) {
                    event.preventDefault();
                    loadPage(link.href);
                }
            } catch (e) {}
        }

        // App-wide Shared Download Delegate (Catches all Download / Export buttons across Projects, Applications, Donors, Contractors, Reports, etc.)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('a[href*="export"], a[href*="download"], a[download], .download-excel-btn, .btn-export');
            if (!btn) return;

            const href = btn.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                e.preventDefault();
                e.stopPropagation();

                // Trigger direct native browser file download
                const downloadAnchor = document.createElement('a');
                downloadAnchor.href = href;
                downloadAnchor.setAttribute('download', '');
                downloadAnchor.style.display = 'none';
                document.body.appendChild(downloadAnchor);
                downloadAnchor.click();
                setTimeout(() => {
                    if (downloadAnchor.parentNode) {
                        downloadAnchor.parentNode.removeChild(downloadAnchor);
                    }
                }, 500);
            }
        }, true);

        // Override default programmatic form.submit() to dispatch submit event
        const originalSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function() {
            const event = new Event('submit', { cancelable: true, bubbles: true });
            this.dispatchEvent(event);
            if (!event.defaultPrevented) {
                originalSubmit.call(this);
            }
        };

        // Custom Confirm override integrating with PJAX
        let isProgrammaticConfirm = false;
        window.confirm = function(message) {
            if (isProgrammaticConfirm) {
                return true;
            }

            const clickedEl = window.event ? window.event.target : document.activeElement;
            const activeEl = clickedEl ? (clickedEl.closest('button') || clickedEl.closest('a') || clickedEl.closest('input') || clickedEl) : null;
            const activeForm = clickedEl ? clickedEl.closest('form') : null;
            const activeLink = clickedEl ? clickedEl.closest('a') : null;

            if ((activeEl && activeEl.dataset.confirmed) || (activeForm && activeForm.dataset.confirmed)) {
                if (activeEl) delete activeEl.dataset.confirmed;
                if (activeForm) delete activeForm.dataset.confirmed;
                return true;
            }
            
            const formAction = activeForm ? (activeForm.getAttribute('action') || '') : '';
            const isRejection = (activeForm && formAction.includes('/reject')) || message.toLowerCase().includes('reject');

            if (activeForm) {
                showCustomConfirm(message, function(remarks) {
                    if (isRejection) {
                        let remarksInput = activeForm.querySelector('input[name="remarks"]');
                        if (!remarksInput) {
                            remarksInput = document.createElement('input');
                            remarksInput.type = 'hidden';
                            remarksInput.name = 'remarks';
                            activeForm.appendChild(remarksInput);
                        }
                        remarksInput.value = remarks || '';
                    }
                    isProgrammaticConfirm = true;
                    activeForm.dataset.confirmed = 'true';
                    if (activeEl) activeEl.dataset.confirmed = 'true';
                    const event = new Event('submit', { cancelable: true, bubbles: true });
                    activeForm.dispatchEvent(event);
                    if (!event.defaultPrevented) {
                        originalSubmit.call(activeForm);
                    }
                    isProgrammaticConfirm = false;
                }, isRejection);
            } else if (activeLink && activeLink.href) {
                showCustomConfirm(message, function() {
                    activeLink.dataset.confirmed = 'true';
                    if (activeEl) activeEl.dataset.confirmed = 'true';
                    loadPage(activeLink.href);
                });
            } else if (activeEl) {
                showCustomConfirm(message, function() {
                    isProgrammaticConfirm = true;
                    activeEl.dataset.confirmed = 'true';
                    activeEl.click();
                    isProgrammaticConfirm = false;
                });
            }
            
            return false;
        };

        // PJAX event delegation listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', handleLinkClick);
            document.body.addEventListener('mouseover', handleLinkHover);
            document.body.addEventListener('mouseout', handleLinkMouseout);
            
            // Global Event Delegation for dynamically appended buttons (e.g. Add Programme across tabs/PJAX)
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('#btn-add-programme-main, .btn-add-programme-trigger, .btn-add-prog, [title="Add Programme"], [onclick*="openAddProgrammeModal"]');
                if (btn) {
                    if (typeof window.openAddProgrammeModal === 'function') {
                        e.preventDefault();
                        window.openAddProgrammeModal(btn);
                    } else {
                        const modal = document.getElementById('addProgrammeModal');
                        if (modal) {
                            e.preventDefault();
                            document.body.appendChild(modal);
                            modal.style.setProperty('z-index', '999999', 'important');
                            modal.style.setProperty('display', 'flex', 'important');
                        }
                    }
                }
            });

            document.body.addEventListener('submit', function(e) {
                if (e.target && e.target.tagName === 'FORM') {
                    handleFormSubmit(e);
                }
            });
            
            window.addEventListener('popstate', function(event) {
                if (event.state && event.state.url) {
                    loadPage(event.state.url, false);
                } else {
                    loadPage(window.location.href, false);
                }
            });
            
            window.history.replaceState({ url: window.location.href }, '', window.location.href);
        });

        // Laravel Reverb WebSockets Real-time connection client
        (function() {
            @php
                $broadcastDriver = config('broadcasting.default', env('BROADCAST_CONNECTION', 'log'));
                $reverbKey = env('REVERB_APP_KEY');
                $reverbHost = env('REVERB_HOST', '127.0.0.1');
                $reverbPort = env('REVERB_PORT', 8080);
                $reverbScheme = env('REVERB_SCHEME', 'http');
            @endphp
            const broadcastDriver = "{{ $broadcastDriver }}";
            const appKey = "{{ $reverbKey }}";
            const host = "{{ $reverbHost }}";
            const port = {{ $reverbPort }};
            const scheme = "{{ $reverbScheme }}";
            
            // Only connect if broadcast driver is reverb or pusher
            if (broadcastDriver !== 'reverb' && broadcastDriver !== 'pusher') {
                return;
            }

            // Skip attempting localhost WebSocket connections if user is accessing on a live hosted domain
            const isLocalClient = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1');
            const isServerLocal = (host === '127.0.0.1' || host === 'localhost');
            if (!isLocalClient && isServerLocal) {
                return;
            }

            if (typeof Pusher !== 'undefined' && appKey && (broadcastDriver === 'reverb' || broadcastDriver === 'pusher')) {
                try {
                    Pusher.logToConsole = false;
                    const pusher = new Pusher(appKey, {
                        wsHost: host,
                        wsPort: port,
                        wssPort: port,
                        forceTLS: scheme === 'https',
                        enabledTransports: ["ws", "wss"],
                        cluster: "mt1",
                        unavailableTimeout: 1000,
                        maxReconnectionAttempts: 1
                    });

                    pusher.connection.bind('state_change', function(states) {
                        if (states.current === 'failed' || states.current === 'unavailable' || states.current === 'disconnected') {
                            try { pusher.disconnect(); } catch(e) {}
                        }
                    });

                    pusher.connection.bind('error', function(err) {
                        try { pusher.disconnect(); } catch(e) {}
                    });

                    window.echoPusher = pusher;

                    const channel = pusher.subscribe('projects');
                    channel.bind('project.updated', function(data) {
                        console.log('Realtime project.updated received:', data);
                        window.dispatchEvent(new CustomEvent('reverb:project.updated', { detail: data }));
                    });

                    // Real-time Applications listener across channels
                    const roleName = "{{ auth()->user()->role ?? 'others' }}";
                    const userId = "{{ auth()->id() }}";
                    const deptId = "{{ auth()->user()->department_id ?? 0 }}";

                    const roleChannels = [
                        'role.' + roleName,
                        'user.' + userId
                    ];
                    if (deptId && deptId !== '0') {
                        roleChannels.push('role.hod.' + deptId);
                    }

                    roleChannels.forEach(cName => {
                        try {
                            const appChan = pusher.subscribe('private-' + cName);
                            if (appChan) {
                                appChan.bind('application.created', function(data) {
                                    onRealtimeApplicationChange('created', data);
                                });
                                appChan.bind('application.updated', function(data) {
                                    onRealtimeApplicationChange('updated', data);
                                });
                                appChan.bind('application.deleted', function(data) {
                                    onRealtimeApplicationChange('deleted', data);
                                });
                            }
                        } catch(e) {}
                    });
                } catch (e) {
                    // Ignore offline websocket server errors
                }
            }
        })();

        // Global helper for live real-time UI application table updates
        function onRealtimeApplicationChange(op, data) {
            if (window.Livewire) {
                window.Livewire.dispatch('$refresh');
            }
            const path = window.location.pathname;
            if (path.includes('/applications') || path.includes('/dashboard') || path.includes('/projects')) {
                reloadCurrentPageContent();
            }
        }

        // Global Hover Popover handler for staff leave balances & details
        document.addEventListener('mouseover', function(e) {
            const trigger = e.target.closest('.staff-hover-container');
            if (!trigger) return;
            
            const popover = trigger.querySelector('.staff-leave-popover');
            if (!popover) return;
            
            const rect = trigger.getBoundingClientRect();
            popover.style.display = 'block';
            popover.style.position = 'fixed';
            popover.style.zIndex = '999999';
            popover.style.left = Math.max(10, rect.left) + 'px';
            
            const spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 280) {
                popover.style.top = Math.max(10, rect.top - 280) + 'px';
            } else {
                popover.style.top = (rect.bottom + 8) + 'px';
            }
        });

        document.addEventListener('mouseout', function(e) {
            const trigger = e.target.closest('.staff-hover-container');
            if (!trigger) return;
            const popover = trigger.querySelector('.staff-leave-popover');
            if (popover) {
                popover.style.display = 'none';
            }
        });

        // Global helper for updating project status in UI
        function updateProjectStatusUI(projectId, newStatus) {
            if (!projectId) return;
            const nowSuspended = (newStatus === 'Suspended');
            const color = nowSuspended ? '#ef4444' : '#10b981';

            // 1. Update action button state
            const btn = document.getElementById('suspend-btn-' + projectId) || document.querySelector(`[data-project-id="${projectId}"][id^="suspend-btn"]`);
            if (btn) {
                btn.dataset.status = newStatus;
                btn.title = nowSuspended ? 'Reactivate Project' : 'Suspend Project';
                btn.style.backgroundColor = nowSuspended ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)';
                btn.style.color = nowSuspended ? '#10b981' : '#f59e0b';
                btn.style.borderColor = nowSuspended ? 'rgba(16,185,129,0.4)' : 'rgba(245,158,11,0.4)';
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = 'bx ' + (nowSuspended ? 'bx-lock-open-alt' : 'bx-lock-alt');
                }

                // 2. Direct row status cell update
                const tr = btn.closest('tr');
                if (tr) {
                    const table = tr.closest('table');
                    if (table) {
                        const headers = Array.from(table.querySelectorAll('thead th'));
                        const statusIndex = headers.findIndex(h => h.textContent.trim().toLowerCase() === 'status');
                        if (statusIndex !== -1 && tr.cells[statusIndex]) {
                            const statusCell = tr.cells[statusIndex];
                            const labelText = statusCell.querySelector('.status-label-text');
                            const dot = statusCell.querySelector('.status-dot');
                            if (labelText) {
                                labelText.textContent = newStatus;
                                labelText.style.color = color;
                            }
                            if (dot) {
                                dot.style.backgroundColor = color;
                                dot.style.background = color;
                            }
                        }
                    }
                }
            }

            // 3. Fallback: Update any matching badge element across DOM
            const badges = document.querySelectorAll('#status-badge-' + projectId + ', [data-project-id="' + projectId + '"].status-badge-container');
            badges.forEach(badge => {
                badge.dataset.status = newStatus;
                const labelText = badge.querySelector('.status-label-text');
                const dot = badge.querySelector('.status-dot');
                if (labelText) {
                    labelText.textContent = newStatus;
                    labelText.style.color = color;
                }
                if (dot) {
                    dot.style.backgroundColor = color;
                    dot.style.background = color;
                }
            });
        }
        window.updateProjectStatusUI = updateProjectStatusUI;

        // Global Realtime listener for Reverb project status updates
        window.addEventListener('reverb:project.updated', function(e) {
            const data = e.detail || {};
            const projectId = data.projectId;
            const action = data.action;
            const payload = data.payload || {};

            if (action === 'status_toggled' && projectId && payload.status) {
                updateProjectStatusUI(projectId, payload.status);
            }
        });

        // ==========================================
        // Client-Side Generic Table Pagination
        // ==========================================
        function formatAllCustomTables() {
            document.querySelectorAll('table.table-custom').forEach(table => {
                if (table.closest('.modal')) return;

                // Find header indexes
                const headers = Array.from(table.querySelectorAll('thead th'));
                const headerText = headers.map(h => h.textContent.trim().toLowerCase());

                const nameIndex = headerText.findIndex(t => t.includes('name of applicant') || t.includes('applicant name') || t === 'applicant');
                const appIdIndex = headerText.findIndex(t => t.includes('application id') || t.includes('project id') || t === 'app id' || t === 'project id');
                const statusIndex = headerText.findIndex(t => t === 'status');
                const sponsorStatusIndex = headerText.findIndex(t => t.includes('sponsor status') || t === 'sponsor');
                const actionIndex = headerText.findIndex(t => t === 'action');

                const rows = Array.from(table.querySelectorAll('tbody tr'));
                
                rows.forEach(row => {
                    // Skip if row already formatted
                    if (row.dataset.formatted === 'true') return;
                    row.dataset.formatted = 'true';

                    // 1. Format Name column to show name
                    if (nameIndex !== -1) {
                        const cell = row.cells[nameIndex];
                        if (cell) {
                            const nameText = cell.textContent.trim();
                            if (nameText && nameText !== 'N/A') {
                                cell.innerHTML = `
                                    <div style="font-weight: 700; color: #1e293b;">${nameText}</div>
                                `;
                            }
                        }
                    }

                    // 2. Format Application ID and Project ID columns
                    if (appIdIndex !== -1) {
                        const cell = row.cells[appIdIndex];
                        if (cell) {
                            cell.style.color = '#10b981';
                            cell.style.fontWeight = '700';
                            cell.style.fontSize = '0.88rem';
                            const links = cell.querySelectorAll('a');
                            links.forEach(link => {
                                link.style.color = '#10b981';
                                link.style.fontWeight = '700';
                            });
                        }
                    }

                    // 3. Format Status column to status dot & text label
                    if (statusIndex !== -1) {
                        const cell = row.cells[statusIndex];
                        if (cell) {
                            const projBtn = row.querySelector('[data-project-id]');
                            let projId = projBtn ? projBtn.dataset.projectId : '';
                            const badgeSpan = cell.querySelector('[data-project-id]') || cell.querySelector('[id^="status-badge-"]');
                            if (badgeSpan && badgeSpan.dataset.projectId) {
                                projId = badgeSpan.dataset.projectId;
                            }

                            const staffStatusEl = cell.querySelector('.staff-status');
                            let statusText = '';
                            if (staffStatusEl) {
                                statusText = staffStatusEl.textContent.trim();
                            } else {
                                const nonDotSpans = Array.from(cell.querySelectorAll('span')).filter(s => s.textContent.trim().length > 0);
                                statusText = (nonDotSpans.length > 0 ? nonDotSpans[0].textContent : cell.textContent).trim();
                            }
                            
                            let color = '#94a3b8';
                            const lowerStatus = statusText.toLowerCase();
                            if (lowerStatus === 'approved') {
                                color = '#10b981';
                                statusText = 'Approved';
                            } else if (lowerStatus === 'active') {
                                color = '#10b981';
                                statusText = 'Active';
                            } else if (lowerStatus === 'pending') {
                                color = '#f59e0b';
                                statusText = 'Pending';
                            } else if (lowerStatus === 'rejected') {
                                color = '#ef4444';
                                statusText = 'Rejected';
                            } else if (lowerStatus === 'suspended') {
                                color = '#ef4444';
                                statusText = 'Suspended';
                            }

                            cell.innerHTML = `
                                <div id="status-badge-${projId}" data-project-id="${projId}" class="status-badge-container" style="display: inline-flex; align-items: center; gap: 0.35rem; vertical-align: middle; justify-content: center; width: 100%;">
                                    <span class="status-dot" style="width: 8px; height: 8px; background: ${color}; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
                                    <span class="status-label-text" style="color: ${color}; font-weight: 700; font-size: 0.82rem;">${statusText || 'Active'}</span>
                                </div>
                            `;
                        }
                    }

                    // 4. Format Action buttons to square borders
                    if (actionIndex !== -1) {
                        const cell = row.cells[actionIndex];
                        if (cell) {
                            const btns = cell.querySelectorAll('button, a.btn-custom, a.btn-danger-custom, .btn-action-icon');
                            btns.forEach(btn => {
                                btn.style.background = 'transparent';
                                btn.style.borderRadius = '8px';
                                btn.style.width = '32px';
                                btn.style.height = '32px';
                                btn.style.display = 'inline-flex';
                                btn.style.alignItems = 'center';
                                btn.style.justifyContent = 'center';
                                btn.style.transition = 'all 0.15s ease';
                                btn.style.margin = '0 0.2rem';
                                btn.style.boxShadow = 'none';
                                btn.style.transform = 'none';
                                
                                if (btn.classList.contains('btn-danger-custom') || btn.classList.contains('btn-delete')) {
                                    btn.style.color = '#ef4444';
                                    btn.style.border = '1px solid #e2e8f0';
                                } else {
                                    btn.style.border = '1px solid #e2e8f0';
                                    if (btn.classList.contains('btn-dots') || btn.title === 'Details' || btn.title === 'View') {
                                        btn.style.color = '#475569';
                                    } else if (btn.classList.contains('btn-edit') || btn.title === 'Edit') {
                                        btn.style.color = '#3b82f6';
                                    } else if (btn.classList.contains('btn-view') || btn.title === 'Approve' || btn.title === 'Stage Details') {
                                        btn.style.color = '#10b981';
                                    }
                                }
                            });
                        }
                    }
                });

                // Sort table rows strictly by Status / Sponsor Status order:
                // For Approved Social Aid Applications: Not Sponsored (1) -> Sponsored (2)
                // For Social Aid Projects List (orphan-care, differently-abled, family-aid): Active (1) -> Suspended (2)
                // For Approved Application Registry (non-social aid): Not Started (1) -> In Progress / Not set / Phases (2) -> Completed (3)
                // For General Applications List: Pending (1) -> Approved (2) -> Rejected (3) -> Other (4)
                if (statusIndex !== -1 || sponsorStatusIndex !== -1) {
                    const tbody = table.querySelector('tbody');
                    if (tbody) {
                        const rowsArray = Array.from(tbody.querySelectorAll('tr'));
                        const currentPath = window.location.pathname.toLowerCase();
                        const isApprovedPage = currentPath.includes('/approved-applications') || currentPath.includes('/approved');
                        const isProjectsPage = currentPath.includes('/projects') || currentPath.includes('/project');
                        const isSocialAid = ['orphan-care', 'differently-abled', 'family-aid'].some(s => currentPath.includes(s));

                        const getStatusRank = (row) => {
                            if (isApprovedPage && isSocialAid) {
                                const cellIndex = (sponsorStatusIndex !== -1 && row.cells[sponsorStatusIndex]) ? sponsorStatusIndex : statusIndex;
                                if (cellIndex === -1 || !row.cells[cellIndex]) return 99;
                                const txt = row.cells[cellIndex].textContent.trim().toLowerCase();
                                if (txt.includes('not sponsored')) return 1;
                                if (txt.includes('sponsored')) return 2;
                                return 3;
                            }

                            if (statusIndex === -1 || !row.cells[statusIndex]) return 99;
                            const txt = row.cells[statusIndex].textContent.trim().toLowerCase();

                            if (isSocialAid && isProjectsPage && !isApprovedPage) {
                                if (txt.includes('active')) return 1;
                                if (txt.includes('suspended')) return 2;
                                return 3;
                            }

                            if (isApprovedPage && !isSocialAid) {
                                if (txt.includes('not started')) return 1;
                                if (txt.includes('completed')) return 3;
                                return 2; // Not set, In Progress, Phase names, etc.
                            }

                            if (txt.includes('pending')) return 1;
                            if (txt.includes('approved')) return 2;
                            if (txt.includes('rejected')) return 3;
                            return 4;
                        };

                        rowsArray.sort((a, b) => getStatusRank(a) - getStatusRank(b));
                        rowsArray.forEach(row => tbody.appendChild(row));
                    }
                }
            });
        }

        function alignProjectsLayout() {
            const headerPanel = document.querySelector('.group-header-panel');
            const controlsRow = document.querySelector('.controls-row');
            const panel = document.querySelector('.panel');
            
            if (headerPanel && controlsRow && panel) {
                // If modern filter toolbar is present, do not destructively alter the layout
                if (headerPanel.classList.contains('no-auto-align') || controlsRow.classList.contains('no-auto-align') || controlsRow.querySelector('select')) {
                    return;
                }
                
                // Get the title text
                const titleText = headerPanel.textContent.trim();
                
                // Get buttons from controls row
                const buttonsWrapper = controlsRow.querySelector('div[style*="display: flex"]');
                const buttonsHtml = buttonsWrapper ? buttonsWrapper.innerHTML : '';
                
                // Get search input value / attributes
                const originalSearch = controlsRow.querySelector('#tableSearch');
                
                // Create panel-header
                const panelHeader = document.createElement('div');
                panelHeader.className = 'panel-header';
                panelHeader.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;';
                panelHeader.innerHTML = `
                    <h2 class="panel-title" style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-main); text-transform: capitalize;">${titleText.toLowerCase()}</h2>
                    <div style="display: flex; gap: 0.75rem;">
                        ${buttonsHtml}
                    </div>
                `;
                
                // Create search row
                const searchRow = document.createElement('div');
                searchRow.style.cssText = 'margin-bottom: 1.25rem; display: flex; justify-content: flex-end;';
                searchRow.innerHTML = `
                    <div style="position: relative; width: 100%; max-width: 320px;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"><i class="bx bx-search"></i></span>
                        <input type="text" id="tableSearch" placeholder="Search projects..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.875rem; outline: none; transition: border-color 0.2s;" onkeyup="if(typeof filterTable === 'function') filterTable()">
                    </div>
                `;
                
                // Remove the old elements
                headerPanel.parentNode.removeChild(headerPanel);
                controlsRow.parentNode.removeChild(controlsRow);
                
                // Insert the new elements at the top of the panel
                panel.insertBefore(searchRow, panel.firstChild);
                panel.insertBefore(panelHeader, panel.firstChild);
                
                // Restore search listener if there was text
                if (originalSearch) {
                    const newSearch = panel.querySelector('#tableSearch');
                    if (newSearch) {
                        newSearch.value = originalSearch.value;
                        newSearch.addEventListener('keyup', () => {
                            if (typeof filterTable === 'function') filterTable();
                        });
                    }
                }
            }
        }

        function initAllTablePagers() {
            alignProjectsLayout();
            formatAllCustomTables();
            document.querySelectorAll('table.table-custom').forEach(table => {
                // Skip nested tables, modal tables, or tables with no-paginate class
                if (table.classList.contains('no-paginate') || table.closest('.modal')) return;
                // Avoid double pagination setup
                if (table.dataset.paginated === 'true') {
                    if (table.pagerUpdate) {
                        table.pagerUpdate();
                    }
                    return;
                }
                
                setupTablePagination(table);
            });
        }

        function setupTablePagination(table) {
            let pageSize = parseInt(table.getAttribute('data-page-size')) || 10;
            let currentPage = 1;
            table.dataset.paginated = 'true';
            
            // Create pagination container
            const container = document.createElement('div');
            container.className = 'table-pagination-container';
            container.style.cssText = 'display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.85rem; color: #64748b; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #ffffff; width: 100%; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;';
            
            // Insert container
            const parent = table.parentNode;
            if (parent.style.overflowX === 'auto') {
                parent.parentNode.insertBefore(container, parent.nextSibling);
            } else {
                table.parentNode.insertBefore(container, table.nextSibling);
            }
            
            function update() {
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                
                const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('empty-row'));
                if (rows.length === 0) {
                    container.innerHTML = '<div>Showing 0 to 0 of 0 results</div>';
                    return;
                }
                
                // Filter visible rows (not hidden by search filters)
                const visibleRows = rows.filter(row => row.style.display !== 'none' || row.dataset.pageHidden === 'true');
                
                const totalRows = visibleRows.length;
                const totalPages = Math.ceil(totalRows / pageSize) || 1;
                
                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;
                
                const startIndex = (currentPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, totalRows);
                
                // Show/hide rows based on active page
                visibleRows.forEach((row, idx) => {
                    if (idx >= startIndex && idx < endIndex) {
                        row.style.display = '';
                        row.dataset.pageHidden = 'false';
                    } else {
                        row.style.display = 'none';
                        row.dataset.pageHidden = 'true';
                    }
                });
                
                // Keep filtered-out rows hidden
                rows.forEach(row => {
                    if (!visibleRows.includes(row)) {
                        row.style.display = 'none';
                        row.dataset.pageHidden = 'false';
                    }
                });
                
                // Render pagination controls UI
                renderControls(container, currentPage, totalPages, startIndex + 1, endIndex, totalRows, (page) => {
                    currentPage = page;
                    update();
                }, pageSize, (newSize) => {
                    pageSize = newSize;
                    table.setAttribute('data-page-size', newSize);
                    currentPage = 1;
                    update();
                });
            }
            
            table.pagerUpdate = update;
            
            // Listen for filter input changes to reset pagination
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.classList.contains('table-page-size-select')) return;
                input.addEventListener('input', () => {
                    rowsReset();
                    currentPage = 1;
                    setTimeout(update, 100);
                });
                input.addEventListener('change', () => {
                    rowsReset();
                    currentPage = 1;
                    setTimeout(update, 100);
                });
            });
            
            function rowsReset() {
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                tbody.querySelectorAll('tr').forEach(row => {
                    if (row.dataset.pageHidden === 'true') {
                        row.style.display = '';
                        row.dataset.pageHidden = 'false';
                    }
                });
            }
            
            update();
        }

        function renderControls(container, currentPage, totalPages, startIdx, endIdx, totalRows, onPageChange, pageSize, onPageSizeChange) {
            if (totalRows === 0) {
                container.innerHTML = '<div>Showing 0 to 0 of 0 results</div>';
                return;
            }
            
            const currentSize = pageSize || 10;
            const pageSizeSelectHtml = `
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: #64748b;">
                    <span>Show</span>
                    <select class="table-page-size-select" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #334155; outline: none; cursor: pointer; font-weight: 600;">
                        <option value="10" ${currentSize === 10 ? 'selected' : ''}>10</option>
                        <option value="25" ${currentSize === 25 ? 'selected' : ''}>25</option>
                        <option value="50" ${currentSize === 50 ? 'selected' : ''}>50</option>
                        <option value="100" ${currentSize === 100 ? 'selected' : ''}>100</option>
                    </select>
                    <span>entries</span>
                </div>
            `;

            const info = `<div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                ${pageSizeSelectHtml}
                <span style="color: #cbd5e1;">|</span>
                <div>Showing ${startIdx} to ${endIdx} of ${totalRows} results</div>
            </div>`;
            
            let buttonsHtml = '<div style="display: flex; gap: 0.25rem; align-items: center;">';
            
            // Prev button
            buttonsHtml += `<button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; color: #475569;"><i class="bx bx-chevron-left"></i></button>`;
            
            // Page numbers
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage + 1 < maxVisible) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            if (startPage > 1) {
                buttonsHtml += `<button class="page-btn" data-page="1" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; color: #475569;">1</button>`;
                if (startPage > 2) {
                    buttonsHtml += '<span style="padding: 0 0.25rem; color: #94a3b8;">...</span>';
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                const activeStyle = isActive 
                    ? 'background: #10b981; border-color: #10b981; color: #ffffff; font-weight: 600;' 
                    : 'background: #ffffff; border-color: #e2e8f0; color: #475569;';
                buttonsHtml += `<button class="page-btn" data-page="${i}" style="border: 1px solid; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; ${activeStyle}">${i}</button>`;
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    buttonsHtml += '<span style="padding: 0 0.25rem; color: #94a3b8;">...</span>';
                }
                buttonsHtml += `<button class="page-btn" data-page="${totalPages}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; color: #475569;">${totalPages}</button>`;
            }
            
            // Next button
            buttonsHtml += `<button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; color: #475569;"><i class="bx bx-chevron-right"></i></button>`;
            
            buttonsHtml += '</div>';
            
            container.innerHTML = info + buttonsHtml;
            
            // Bind click events
            container.querySelectorAll('.page-btn').forEach(btn => {
                if (btn.hasAttribute('disabled')) return;
                btn.addEventListener('click', () => {
                    const page = parseInt(btn.getAttribute('data-page'));
                    onPageChange(page);
                });
            });

            // Bind page size select change event
            const sizeSelect = container.querySelector('.table-page-size-select');
            if (sizeSelect && onPageSizeChange) {
                sizeSelect.addEventListener('change', (e) => {
                    const newSize = parseInt(e.target.value) || 10;
                    onPageSizeChange(newSize);
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            initAllTablePagers();
        });

        // Global Pincode Master Auto-Fill Listener
        (function() {
            let pincodeTimer = null;

            document.addEventListener('input', function(e) {
                const target = e.target;
                if (!target || target.tagName !== 'INPUT') return;

                const name = (target.name || '').toLowerCase();
                const id = (target.id || '').toLowerCase();

                const isPincodeField = name.includes('pin_code') || name.includes('pincode') || 
                                       id.includes('pin_code') || id.includes('pincode') || 
                                       id.endsWith('pin');

                if (!isPincodeField) return;

                const rawVal = target.value.trim().replace(/\D/g, '');
                if (rawVal.length === 6) {
                    clearTimeout(pincodeTimer);
                    pincodeTimer = setTimeout(() => {
                        lookupAndFillPincode(rawVal, target);
                    }, 250);
                }
            });

            function lookupAndFillPincode(pincode, inputEl) {
                fetch('/admin/pincode-lookup/' + pincode)
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.success) return;

                        // Scope search to the surrounding form or modal panel
                        const parent = inputEl.closest('form') || inputEl.closest('.panel') || inputEl.closest('.modal') || document;

                        const setFieldValue = (el, val, force = false) => {
                            if (!el) return;
                            if (force || !el.value || el.value.trim() === '') {
                                el.value = val;
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                                el.dispatchEvent(new Event('change', { bubbles: true }));

                                // Flash soft green highlight
                                const origBg = el.style.backgroundColor;
                                el.style.transition = 'background-color 0.3s ease';
                                el.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                                setTimeout(() => {
                                    el.style.backgroundColor = origBg || '';
                                }, 1200);
                            }
                        };

                        const targetId = (inputEl.id || '').toLowerCase();
                        const targetName = (inputEl.name || '').toLowerCase();
                        const isLocality = targetId.includes('locality_') || targetName.includes('locality_');
                        const isLandOwner = targetId.includes('land_owner_') || targetName.includes('land_owner_');
                        const isEdit = targetId.startsWith('edit_') || targetName.startsWith('edit_') || !!parent.querySelector('#edit_applicant_name');

                        const getScopedField = (fieldName) => {
                            let candidateIds = [];
                            if (isLocality) {
                                if (fieldName === 'post') candidateIds = [isEdit ? 'edit_locality_post' : 'locality_post', isEdit ? 'edit_locality_post_office' : 'locality_post_office'];
                                else if (fieldName === 'village') candidateIds = [isEdit ? 'edit_locality_village' : 'locality_village'];
                                else if (fieldName === 'place') candidateIds = [isEdit ? 'edit_locality_place' : 'locality_place', isEdit ? 'edit_locality_place' : 'locality_place'];
                                else if (fieldName === 'district') candidateIds = [isEdit ? 'edit_locality_district' : 'locality_district'];
                                else if (fieldName === 'state') candidateIds = [isEdit ? 'edit_locality_state' : 'locality_state'];
                            } else if (isLandOwner) {
                                if (fieldName === 'post') candidateIds = [isEdit ? 'edit_land_owner_post' : 'land_owner_post'];
                                else if (fieldName === 'village') candidateIds = [isEdit ? 'edit_land_owner_village' : 'land_owner_village'];
                                else if (fieldName === 'place') candidateIds = [isEdit ? 'edit_land_owner_place' : 'land_owner_place'];
                                else if (fieldName === 'district') candidateIds = [isEdit ? 'edit_land_owner_district' : 'land_owner_district'];
                                else if (fieldName === 'state') candidateIds = [isEdit ? 'edit_land_owner_state' : 'land_owner_state'];
                            } else {
                                if (fieldName === 'post') candidateIds = [isEdit ? 'edit_post' : 'post', isEdit ? 'edit_post_office' : 'post_office'];
                                else if (fieldName === 'village') candidateIds = [isEdit ? 'edit_village' : 'village'];
                                else if (fieldName === 'place') candidateIds = [isEdit ? 'edit_location' : 'location', isEdit ? 'edit_place' : 'place'];
                                else if (fieldName === 'district') candidateIds = [isEdit ? 'edit_district' : 'district'];
                                else if (fieldName === 'state') candidateIds = [isEdit ? 'edit_state' : 'state'];
                            }

                            for (let cid of candidateIds) {
                                let el = parent.querySelector('#' + cid) ||
                                         parent.querySelector(`[name="meta[${cid}]"]`) ||
                                         parent.querySelector(`[name="${cid}"]`);
                                if (el) return el;
                            }
                            return null;
                        };

                        // Auto-fill State & District (Always update when pincode matched)
                        const stateEl = getScopedField('state');
                        setFieldValue(stateEl, data.state, true);

                        const districtEl = getScopedField('district');
                        setFieldValue(districtEl, data.district, true);

                        // Auto-fill Post Office
                        const poEl = getScopedField('post');
                        if (poEl) {
                            setFieldValue(poEl, data.post_office, true);

                            // Create datalist for post office options if multiple exist
                            if (data.post_offices && data.post_offices.length > 0) {
                                const dlId = 'po_list_' + pincode;
                                let dl = document.getElementById(dlId);
                                if (!dl) {
                                    dl = document.createElement('datalist');
                                    dl.id = dlId;
                                    document.body.appendChild(dl);
                                }
                                dl.innerHTML = data.post_offices.map(po => `<option value="${po}"></option>`).join('');
                                poEl.setAttribute('list', dlId);
                            }
                        }

                        // Place & Village auto-fill removed as requested (user enters place & village manually)

                        // Success visual feedback on Pin Code input field
                        inputEl.style.transition = 'border-color 0.3s ease, box-shadow 0.3s ease';
                        inputEl.style.borderColor = '#10b981';
                        inputEl.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
                        setTimeout(() => {
                            inputEl.style.borderColor = '';
                            inputEl.style.boxShadow = '';
                        }, 1800);
                    })
                    .catch(err => console.error('Pincode lookup error:', err));
            }
        })();

        // Global Aadhaar Number Standard Auto-Formatter (XXXX XXXX XXXX)
        (function() {
            function formatAadhaarString(val) {
                if (!val) return '';
                const digits = val.toString().replace(/\D/g, '').slice(0, 12);
                const parts = [];
                for (let i = 0; i < digits.length; i += 4) {
                    parts.push(digits.substring(i, i + 4));
                }
                return parts.join(' ');
            }

            function initAadhaarFields() {
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = (input.name || '').toLowerCase();
                    const id = (input.id || '').toLowerCase();

                    if (name.includes('aadhar') || name.includes('adhaar') || id.includes('aadhar') || id.includes('adhaar')) {
                        input.setAttribute('maxlength', '14');
                        if (!input.placeholder || input.placeholder.toLowerCase().includes('enter') || input.placeholder === '' || input.placeholder === '1234 5678 9012') {
                            input.placeholder = 'XXXX XXXX XXXX';
                        }
                        if (input.value) {
                            const formatted = formatAadhaarString(input.value);
                            if (input.value !== formatted) {
                                input.value = formatted;
                            }
                        }
                    }
                });
            }

            document.addEventListener('input', function(e) {
                const target = e.target;
                if (!target || target.tagName !== 'INPUT') return;

                const name = (target.name || '').toLowerCase();
                const id = (target.id || '').toLowerCase();

                const isAadhaarField = name.includes('aadhar') || name.includes('adhaar') || 
                                       id.includes('aadhar') || id.includes('adhaar');

                if (isAadhaarField) {
                    const selectionStart = target.selectionStart;
                    const prevLen = target.value.length;

                    const formatted = formatAadhaarString(target.value);
                    target.value = formatted;

                    if (selectionStart !== null) {
                        const diff = target.value.length - prevLen;
                        target.setSelectionRange(selectionStart + diff, selectionStart + diff);
                    }
                }
            });

            document.addEventListener('DOMContentLoaded', initAadhaarFields);

            // Re-initialize for dynamic forms or modal popups
            const observer = new MutationObserver(() => {
                initAadhaarFields();
            });
            observer.observe(document.body, { childList: true, subtree: true });

            window.formatAadhaarDisplay = formatAadhaarString;
        })();

        // Global Date of Birth -> Age Auto-Calculator & Readonly Age Handler
        (function() {
            function calculateAgeFromDob(dobStr) {
                if (!dobStr) return '';
                const birthDate = new Date(dobStr);
                if (isNaN(birthDate.getTime())) return '';
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age >= 0 ? age : 0;
            }

            function isActualAgeField(input) {
                if (!input || input.tagName !== 'INPUT') return false;
                const id = (input.id || '').toLowerCase();
                const name = (input.name || '').toLowerCase();

                if (id.includes('agency') || name.includes('agency') || id.includes('village') || name.includes('village')) return false;

                return (id === 'age' || id === 'edit_age' || id.endsWith('_age') || id.startsWith('age_') ||
                        name === 'age' || name === 'meta[age]' || name.endsWith('[age]'));
            }

            function syncAgeForDob(dobInput) {
                if (!dobInput) return;
                const parent = dobInput.closest('tr') || dobInput.closest('.grid') || dobInput.closest('div[style*="grid"]') || dobInput.closest('form') || dobInput.closest('.panel') || dobInput.closest('.modal') || document;
                
                const allInputs = parent.querySelectorAll('input');
                let ageInput = null;
                allInputs.forEach(input => {
                    if (isActualAgeField(input)) {
                        ageInput = input;
                    }
                });

                if (ageInput) {
                    ageInput.readOnly = true;
                    ageInput.setAttribute('readonly', 'readonly');
                    ageInput.style.cursor = 'not-allowed';
                    ageInput.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';

                    if (dobInput.value) {
                        const calculatedAge = calculateAgeFromDob(dobInput.value);
                        if (calculatedAge !== '' && ageInput.value != calculatedAge) {
                            ageInput.value = calculatedAge;
                            ageInput.dispatchEvent(new Event('input', { bubbles: true }));
                            ageInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                }
            }

            function initReadonlyAgeFields() {
                const allInputs = document.querySelectorAll('input');
                allInputs.forEach(input => {
                    if (isActualAgeField(input)) {
                        const container = input.closest('form') || input.closest('.modal') || input.closest('.panel') || input.closest('tr') || document;
                        const hasDob = container ? container.querySelector('input[id*="dob"], input[name*="[dob]"]') : null;

                        if (hasDob || input.hasAttribute('readonly')) {
                            input.readOnly = true;
                            input.setAttribute('readonly', 'readonly');
                            input.style.cursor = 'not-allowed';
                            input.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                        } else {
                            input.readOnly = false;
                            input.removeAttribute('readonly');
                            input.style.cursor = 'text';
                            input.style.backgroundColor = '';
                        }
                    } else if (input.id === 'agency_number' || input.id === 'edit_agency_number' || input.name === 'agency_number') {
                        input.readOnly = false;
                        input.removeAttribute('readonly');
                        input.style.cursor = 'text';
                        input.style.backgroundColor = '';
                    }
                });

                const dobInputs = document.querySelectorAll('input[id*="dob"], input[name*="[dob]"]');
                dobInputs.forEach(dobInput => {
                    syncAgeForDob(dobInput);
                });
            }

            document.addEventListener('input', function(e) {
                const target = e.target;
                if (!target || target.tagName !== 'INPUT') return;
                const name = (target.name || '').toLowerCase();
                const id = (target.id || '').toLowerCase();

                if (id.includes('dob') || name.includes('dob')) {
                    syncAgeForDob(target);
                }
            });

            document.addEventListener('change', function(e) {
                const target = e.target;
                if (!target || target.tagName !== 'INPUT') return;
                const name = (target.name || '').toLowerCase();
                const id = (target.id || '').toLowerCase();

                if (id.includes('dob') || name.includes('dob')) {
                    syncAgeForDob(target);
                }
            });

            document.addEventListener('DOMContentLoaded', initReadonlyAgeFields);

            const observer = new MutationObserver(() => {
                initReadonlyAgeFields();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        })();

        // Global Title Case Auto-Capitalizer (First Letter Capital, Remaining Small)
        (function() {
            function toTitleCase(str) {
                if (!str) return str;
                return str.toLowerCase().replace(/(?:^|\s|-|\/|\()\S/g, function(match) {
                    return match.toUpperCase();
                });
            }

            function shouldCapitalize(input) {
                if (!input || (input.tagName !== 'INPUT' && input.tagName !== 'TEXTAREA')) return false;
                const type = (input.type || 'text').toLowerCase();
                if (type !== 'text' && type !== 'search' && input.tagName !== 'TEXTAREA') return false;
                
                const id = (input.id || '').toLowerCase();
                const name = (input.name || '').toLowerCase();

                if (id.includes('aadhar') || name.includes('aadhar') || 
                    id.includes('pincode') || name.includes('pincode') || id.includes('pin_code') || name.includes('pin_code') ||
                    id.includes('mobile') || name.includes('mobile') ||
                    id.includes('phone') || name.includes('phone') ||
                    id.includes('contact') || name.includes('contact') ||
                    id.includes('whatsapp') || name.includes('whatsapp') ||
                    id.includes('email') || name.includes('email') || id.includes('password') ||
                    input.classList.contains('no-capitalize')) {
                    return false;
                }
                return true;
            }

            document.addEventListener('blur', function(e) {
                const target = e.target;
                if (shouldCapitalize(target) && target.value) {
                    const formatted = toTitleCase(target.value);
                    if (formatted !== target.value) {
                        target.value = formatted;
                        target.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            }, true);

            const style = document.createElement('style');
            style.innerHTML = `
                input[type="text"]:not([id*="aadhar"]):not([id*="pincode"]):not([id*="pin_code"]):not([id*="mobile"]):not([id*="phone"]):not([id*="contact"]):not([name*="mobile"]):not([name*="phone"]):not([name*="contact"]):not([type="email"]):not(.no-capitalize),
                textarea:not(.no-capitalize) {
                    text-transform: capitalize;
                }
            `;
            document.head.appendChild(style);
        })();

        // Global Numeric Enforcer for Mobile, Phone, and Pin Code Fields (0-9 Digits Only, 6 Digits for Pin, 10 Digits for Phone)
        (function() {
            function isNumericOnlyField(input) {
                if (!input || input.tagName !== 'INPUT') return false;
                const id = (input.id || '').toLowerCase();
                const name = (input.name || '').toLowerCase();
                const type = (input.type || '').toLowerCase();
                return type === 'tel' || 
                       id.includes('mobile') || name.includes('mobile') || 
                       id.includes('phone') || name.includes('phone') || 
                       id.includes('contact') || name.includes('contact') || 
                       id.includes('whatsapp') || name.includes('whatsapp') ||
                       id.includes('pincode') || name.includes('pincode') || 
                       id.includes('pin_code') || name.includes('pin_code') ||
                       id === 'pin' || name === 'pin' || id.endsWith('_pin') || name.endsWith('[pin]');
            }

            function isPinCodeField(input) {
                if (!input || input.tagName !== 'INPUT') return false;
                const id = (input.id || '').toLowerCase();
                const name = (input.name || '').toLowerCase();
                return id.includes('pincode') || name.includes('pincode') || 
                       id.includes('pin_code') || name.includes('pin_code') ||
                       id === 'pin' || name === 'pin' || id.endsWith('_pin') || name.endsWith('[pin]');
            }

            function isPhoneField(input) {
                if (!input || input.tagName !== 'INPUT') return false;
                const id = (input.id || '').toLowerCase();
                const name = (input.name || '').toLowerCase();
                return id.includes('mobile') || name.includes('mobile') || 
                       id.includes('phone') || name.includes('phone') || 
                       id.includes('contact') || name.includes('contact') || 
                       id.includes('whatsapp') || name.includes('whatsapp');
            }

            function enforceNumericInput(input) {
                if (!isNumericOnlyField(input)) return;
                
                let maxLen = null;
                if (isPinCodeField(input)) {
                    maxLen = 6;
                } else if (isPhoneField(input)) {
                    maxLen = 10;
                }

                if (maxLen) {
                    input.setAttribute('maxlength', maxLen.toString());
                    input.setAttribute('pattern', '[0-9]{' + maxLen + '}');
                    input.setAttribute('inputmode', 'numeric');
                }

                let cleaned = input.value.replace(/[^0-9]/g, '');
                if (maxLen && cleaned.length > maxLen) {
                    cleaned = cleaned.slice(0, maxLen);
                }

                if (input.value !== cleaned) {
                    input.value = cleaned;
                }
            }

            document.addEventListener('input', function(e) {
                enforceNumericInput(e.target);
            });

            document.addEventListener('focusin', function(e) {
                if (isNumericOnlyField(e.target)) {
                    enforceNumericInput(e.target);
                }
            });

            document.addEventListener('keypress', function(e) {
                if (isNumericOnlyField(e.target)) {
                    const charCode = e.which ? e.which : e.keyCode;
                    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                        e.preventDefault();
                    }
                }
            });

            function applyAttributesToAll() {
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    if (isPinCodeField(input)) {
                        input.setAttribute('maxlength', '6');
                        input.setAttribute('pattern', '[0-9]{6}');
                        input.setAttribute('inputmode', 'numeric');
                    } else if (isPhoneField(input)) {
                        input.setAttribute('maxlength', '10');
                        input.setAttribute('pattern', '[0-9]{10}');
                        input.setAttribute('inputmode', 'numeric');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', applyAttributesToAll);
            const observer = new MutationObserver(applyAttributesToAll);
            observer.observe(document.body, { childList: true, subtree: true });
        })();

        // Global Copy Applicant Address to Locality/Land Handler
        window.copyApplicantAddressToLocality = function(checkbox) {
            if (!checkbox) return;
            const container = checkbox.closest('form') || checkbox.closest('.modal') || checkbox.closest('.panel') || document;
            if (!checkbox.checked) return;

            function getFieldValue(ids) {
                for (let id of ids) {
                    const el = container.querySelector('#' + id) || 
                               container.querySelector('[name="meta[' + id + ']"]') || 
                               container.querySelector('[name="' + id + '"]');
                    if (el && el.value !== undefined && el.value !== '') return el.value;
                }
                return '';
            }

            function setFieldValue(ids, val) {
                for (let id of ids) {
                    const el = container.querySelector('#' + id) || 
                               container.querySelector('[name="meta[' + id + ']"]') || 
                               container.querySelector('[name="' + id + '"]');
                    if (el) {
                        el.value = val;
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }

            const isEdit = (checkbox.id && checkbox.id.startsWith('edit_')) || 
                           (checkbox.name && checkbox.name.startsWith('edit_')) || 
                           !!container.querySelector('#edit_applicant_name') || 
                           !!container.querySelector('#edit_pin_code');

            if (isEdit) {
                const pin = getFieldValue(['edit_pin_code', 'edit_pin', 'pin_code', 'pin']);
                const place = getFieldValue(['edit_location', 'edit_place', 'location', 'place']);
                const village = getFieldValue(['edit_village', 'village']);
                const post = getFieldValue(['edit_post', 'edit_post_office', 'post', 'post_office']);
                const panchayat = getFieldValue(['edit_panchayath', 'edit_panchayat', 'panchayath', 'panchayat']);
                const district = getFieldValue(['edit_district', 'district']);
                const state = getFieldValue(['edit_state', 'state']);

                setFieldValue(['edit_locality_pin_code', 'edit_locality_pin', 'edit_land_pin_code', 'edit_land_pin', 'edit_land_owner_pin'], pin);
                setFieldValue(['edit_locality_place', 'edit_locality_location', 'edit_land_location', 'edit_land_place', 'edit_land_owner_place'], place);
                setFieldValue(['edit_locality_village', 'edit_land_village', 'edit_land_owner_village'], village);
                setFieldValue(['edit_locality_post', 'edit_locality_post_office', 'edit_land_post', 'edit_land_owner_post'], post);
                setFieldValue(['edit_locality_panchayath', 'edit_locality_panchayat', 'edit_land_panchayath', 'edit_land_owner_panchayath'], panchayat);
                setFieldValue(['edit_locality_district', 'edit_land_district', 'edit_land_owner_district'], district);
                setFieldValue(['edit_locality_state', 'edit_land_state', 'edit_land_owner_state'], state);
            } else {
                const pin = getFieldValue(['pin_code', 'pin']);
                const place = getFieldValue(['location', 'place']);
                const village = getFieldValue(['village']);
                const post = getFieldValue(['post', 'post_office']);
                const panchayat = getFieldValue(['panchayath', 'panchayat']);
                const district = getFieldValue(['district']);
                const state = getFieldValue(['state']);

                setFieldValue(['locality_pin_code', 'locality_pin', 'land_pin_code', 'land_pin', 'land_owner_pin'], pin);
                setFieldValue(['locality_place', 'locality_location', 'land_location', 'land_place', 'land_owner_place'], place);
                setFieldValue(['locality_village', 'land_village', 'land_owner_village'], village);
                setFieldValue(['locality_post', 'locality_post_office', 'land_post', 'land_owner_post'], post);
                setFieldValue(['locality_panchayath', 'locality_panchayat', 'land_panchayath', 'land_owner_panchayath'], panchayat);
                setFieldValue(['locality_district', 'land_district', 'land_owner_district'], district);
            }
        };

        window.toggleFinancialSupportPurpose = function(radio) {
            if (!radio) return;
            const container = radio.closest('form') || radio.closest('.panel') || radio.closest('.modal') || document;
            const wrapper = container.querySelector('.financial-support-purpose-wrapper');
            const input = container.querySelector('.financial-support-purpose-input');
            
            if (radio.value === 'Yes' && radio.checked) {
                if (wrapper) wrapper.style.display = 'block';
                if (input) input.setAttribute('required', 'required');
            } else {
                if (wrapper) wrapper.style.display = 'none';
                if (input) {
                    input.removeAttribute('required');
                }
            }
        };

        window.toggleCurrentStatusOther = function(select) {
            if (!select) return;
            const container = select.closest('div');
            if (!container) return;
            const wrapper = container.querySelector('.current-status-other-wrapper');
            const input = container.querySelector('.current-status-other-input');
            
            if (select.value === 'Other') {
                if (wrapper) wrapper.style.display = 'block';
                if (input) input.setAttribute('required', 'required');
            } else {
                if (wrapper) wrapper.style.display = 'none';
                if (input) {
                    input.removeAttribute('required');
                }
            }
        };

        window.calculateTotalStudents = function(inputEl) {
            if (!inputEl) return;
            const container = inputEl.closest('form') || inputEl.closest('.modal') || inputEl.closest('.panel') || document;
            const boysInput = container.querySelector('#students_boys, #edit_students_boys');
            const girlsInput = container.querySelector('#students_girls, #edit_students_girls');
            const totalInput = container.querySelector('.total-students-input');
            
            if (totalInput) {
                const boysVal = boysInput ? boysInput.value.trim() : '';
                const girlsVal = girlsInput ? girlsInput.value.trim() : '';
                
                if (boysVal !== '' || girlsVal !== '') {
                    const boys = parseInt(boysVal) || 0;
                    const girls = parseInt(girlsVal) || 0;
                    const sum = boys + girls;
                    if (totalInput) totalInput.value = sum;
                } else {
                    if (totalInput) totalInput.value = '';
                }
            }
        };

        window.toggleEducationCenterNearby = function(radio) {
            if (!radio) return;
            const container = radio.closest('form') || radio.closest('.modal') || radio.closest('.panel') || document;
            const wrapper = container.querySelector('.distance-ec-wrapper');
            const input = container.querySelector('.distance-ec-input');
            
            if (radio.value === 'Yes' && radio.checked) {
                if (wrapper) wrapper.style.display = 'block';
                if (input) input.setAttribute('required', 'required');
            } else {
                if (wrapper) wrapper.style.display = 'none';
                if (input) {
                    input.removeAttribute('required');
                }
            }
        };
    </script>

    <!-- Modern Premium Custom Confirm Modal HTML -->
    <div id="customConfirmModal">
        <div class="confirm-panel">
            <div class="confirm-icon-box">
                <i class="bx bxs-trash-alt"></i>
            </div>
            <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">Confirm Action</h3>
            <p id="customConfirmMessage" style="color: #9ca3af; font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">Are you sure you want to proceed?</p>
            <div id="confirmRemarksContainer" style="display: none; width: 100%; margin-bottom: 1.5rem; text-align: left; box-sizing: border-box;">
                <label style="display: block; color: #9ca3af; font-size: 0.85rem; margin-bottom: 0.4rem; font-weight: 500;">Rejection Reason <span style="color: #ef4444;">*</span></label>
                <textarea id="confirmRemarksInput" placeholder="Provide Rejection Reason (Required)..." style="width: 100%; height: 70px; background-color: #1f2937; border: 1px solid #374151; color: #ffffff; padding: 0.5rem; border-radius: 6px; font-size: 0.85rem; outline: none; resize: vertical; box-sizing: border-box;"></textarea>
                <div id="confirmRemarksError" style="display: none; color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem; font-weight: 500;">Rejection reason is mandatory.</div>
            </div>
            <div id="confirmSponsorDateContainer" style="display: none; width: 100%; margin-bottom: 1.5rem; text-align: left; box-sizing: border-box;">
                <label style="display: block; color: #cbd5e1; font-size: 0.85rem; margin-bottom: 0.4rem; font-weight: 600;">Sponsored Date <span style="color: #ef4444;">*</span></label>
                <input type="date" id="confirmSponsorDateInput" style="width: 100%; height: 42px; background-color: #1f2937; border: 1px solid #374151; color: #ffffff; padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;">
                <div id="confirmSponsorDateError" style="display: none; color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem; font-weight: 500;">Please select a valid date.</div>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="customConfirmCancel" class="confirm-btn-cancel">Cancel</button>
                <button id="customConfirmOk" class="confirm-btn-ok">Delete</button>
            </div>
        </div>
    </div>
    <!-- Global Premium Sponsor Date Modal HTML -->
    <div id="sponsorDateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1rem;">
        <div style="background: #ffffff; width: 100%; max-width: 420px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden;">
            
            <!-- Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: #10b981;">
                        <i class="bx bxs-award" style="font-size: 1.35rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">Sponsor Application</h3>
                        <p style="font-size: 0.78rem; color: #64748b; margin: 0; margin-top: 2px;">Select the official sponsored date</p>
                    </div>
                </div>
                <button type="button" onclick="closeSponsorDateModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.4rem; cursor: pointer; padding: 0.2rem; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#94a3b8'">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <!-- Body -->
            <div style="padding: 1.5rem;">
                <form id="globalSponsorForm" data-no-ajax="true" onsubmit="submitGlobalSponsorForm(event)">
                    <input type="hidden" id="global_sponsor_app_id">
                    <input type="hidden" id="global_sponsor_category_slug">

                    <div style="margin-bottom: 1.25rem;">
                        <label for="global_sponsored_date_input" style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">
                            Sponsored Date <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" id="global_sponsored_date_input" required class="form-control" style="width: 100%; height: 42px; padding: 0 0.85rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #1e293b; outline: none; transition: border-color 0.15s;">
                    </div>

                    <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" onclick="closeSponsorDateModal()" style="height: 40px; padding: 0 1.25rem; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" id="globalSponsorSubmitBtn" style="height: 40px; padding: 0 1.25rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="bx bx-check" style="font-size: 1.1rem;"></i> Submit Sponsor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSponsorDateModal(appId, categorySlug, callback) {
            const appIdEl = document.getElementById('global_sponsor_app_id');
            if (appIdEl) appIdEl.value = appId || '';

            const catSlugEl = document.getElementById('global_sponsor_category_slug');
            if (catSlugEl) catSlugEl.value = categorySlug || 'orphan-care';
            
            const today = new Date().toISOString().split('T')[0];
            const dateInputEl = document.getElementById('global_sponsored_date_input');
            if (dateInputEl) dateInputEl.value = today;
            
            window._sponsorModalCallback = callback || null;
            
            const modal = document.getElementById('sponsorDateModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeSponsorDateModal() {
            const modal = document.getElementById('sponsorDateModal');
            if (modal) {
                modal.style.display = 'none';
            }
            window._sponsorModalCallback = null;
        }

        async function submitGlobalSponsorForm(e) {
            e.preventDefault();
            const appIdEl = document.getElementById('global_sponsor_app_id');
            const appId = appIdEl ? appIdEl.value : '';

            const catSlugEl = document.getElementById('global_sponsor_category_slug');
            const categorySlug = catSlugEl ? catSlugEl.value : 'orphan-care';

            const dateInputEl = document.getElementById('global_sponsored_date_input');
            const sponsoredDate = dateInputEl ? dateInputEl.value : '';

            if (!sponsoredDate) {
                alert('Please select a valid sponsored date.');
                return;
            }

            if (window._sponsorModalCallback) {
                const cb = window._sponsorModalCallback;
                closeSponsorDateModal();
                cb(sponsoredDate);
                return;
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

            try {
                const response = await fetch(`/admin/applications/${categorySlug}/${appId}/toggle-sponsor`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        category: categorySlug,
                        sponsored_date: sponsoredDate
                    })
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    closeSponsorDateModal();
                    if (typeof window.onSponsorStatusUpdated === 'function') {
                        window.onSponsorStatusUpdated(appId, result.sponsor_status || 'Sponsored', categorySlug, result.sponsored_date || sponsoredDate);
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(result.error || 'Failed to update sponsor status.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while saving sponsorship.');
            }
        }

        window.openSponsorDateModal = openSponsorDateModal;
        window.closeSponsorDateModal = closeSponsorDateModal;
        window.submitGlobalSponsorForm = submitGlobalSponsorForm;

        function downloadDirectPdf(event, url) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            let iframe = document.getElementById('direct-pdf-print-iframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'direct-pdf-print-iframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                iframe.style.visibility = 'hidden';
                document.body.appendChild(iframe);
            }
            iframe.src = url;
            return false;
        }

        // Global Backdrop Click Handler: Close modal forms when clicking outside the modal panel box
        document.addEventListener('click', function(e) {
            const target = e.target;
            if (!target) return;

            const isBackdrop = target.classList.contains('modal-overlay') || 
                               target.id === 'addAppModal' || 
                               target.id === 'editAppModal' || 
                               target.id === 'detailsAppModal' || 
                               target.id === 'approveAppModal' ||
                               target.id === 'addProjectModal' || 
                               target.id === 'editProjectModal' || 
                               target.id === 'addProgrammeModal' ||
                               (target.id && target.id.toLowerCase().includes('modal') && getComputedStyle(target).position === 'fixed');

            if (isBackdrop) {
                if (target.id === 'addAppModal' && typeof window.closeModal === 'function') {
                    window.closeModal();
                } else if (target.id === 'editAppModal' && typeof window.closeEditModal === 'function') {
                    window.closeEditModal();
                } else if (target.id === 'detailsAppModal' && typeof window.closeDetailsModal === 'function') {
                    window.closeDetailsModal();
                } else if (target.id === 'addProjectModal' && typeof window.closeProjectModal === 'function') {
                    window.closeProjectModal();
                } else if (target.id === 'editProjectModal' && typeof window.closeEditProjectModal === 'function') {
                    window.closeEditProjectModal();
                } else {
                    target.style.display = 'none';
                }
            }
        });
    </script>

    <!-- Global Notification Modal -->
    <div id="globalNotificationModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center; padding: 1rem;">
        <div style="background: #ffffff; width: 100%; max-width: 440px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; text-align: center; padding: 1.75rem 1.5rem;">
            <div id="globalNotificationIconBox" style="width: 56px; height: 56px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i id="globalNotificationIcon" class="bx bx-check-circle"></i>
            </div>
            <h3 id="globalNotificationTitle" style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0;">Notification</h3>
            <p id="globalNotificationMessage" style="font-size: 0.95rem; color: #475569; margin: 0 0 1.5rem 0; line-height: 1.5;"></p>
            <button type="button" onclick="closeNotificationModal()" style="background: #10b981; color: #ffffff; border: none; padding: 0.6rem 2rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: background 0.2s;">
                OK
            </button>
        </div>
    </div>
    <script>
        function showNotificationModal(message, type = 'success', title = '') {
            const modal = document.getElementById('globalNotificationModal');
            const iconBox = document.getElementById('globalNotificationIconBox');
            const icon = document.getElementById('globalNotificationIcon');
            const titleEl = document.getElementById('globalNotificationTitle');
            const msgEl = document.getElementById('globalNotificationMessage');
            if (!modal) return;

            const config = {
                success: { bg: 'rgba(16, 185, 129, 0.1)', color: '#10b981', icon: 'bx-check-circle', title: 'Success' },
                error: { bg: 'rgba(239, 68, 68, 0.1)', color: '#ef4444', icon: 'bx-error-circle', title: 'Error' },
                warning: { bg: 'rgba(245, 158, 11, 0.1)', color: '#f59e0b', icon: 'bx-error', title: 'Warning' },
                info: { bg: 'rgba(59, 130, 246, 0.1)', color: '#3b82f6', icon: 'bx-info-circle', title: 'Information' }
            };

            const theme = config[type] || config.success;
            if (iconBox) { iconBox.style.background = theme.bg; iconBox.style.color = theme.color; }
            if (icon) { icon.className = `bx ${theme.icon}`; }
            if (titleEl) { titleEl.innerText = title || theme.title; }
            if (msgEl) { msgEl.innerText = message; }
            modal.style.display = 'flex';
        }

        function closeNotificationModal() {
            const modal = document.getElementById('globalNotificationModal');
            if (modal) modal.style.display = 'none';
        }

        window.showNotificationModal = showNotificationModal;
        window.closeNotificationModal = closeNotificationModal;
    </script>
</body>
</html>
