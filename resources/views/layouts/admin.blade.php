<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Admin Panel</title>
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- Premium CSS Layout and Design System -->
    <style>
        :root {
            --bg-color: #0b0f19;
            --panel-bg: #111827;
            --panel-border: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --accent-purple: #6366f1;
            --accent-cyan: #06b6d4;
            --accent-green: #08A472;
            --accent-red: #ef4444;
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
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
            padding: 0.5rem 1rem;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-logo {
            max-height: 55px;
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

        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            color: #ffffff;
            background-color: #1f2937;
        }

        .sidebar-menu a.active i {
            color: var(--accent-green);
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
            font-weight: 600;
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
            border: 2px solid var(--accent-purple);
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 0.85rem;
            font-weight: 600;
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
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
            font-weight: 600;
            color: #ffffff;
        }

        /* Clean Forms & Inputs styling */
        .form-control-dark {
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            border-radius: 6px;
            padding: 0.65rem 1rem;
            color: #ffffff;
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
            color: #ffffff;
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
            font-weight: 500;
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
            font-weight: 600;
        }

        .table-custom td {
            padding: 1rem;
            border-bottom: 1px solid var(--panel-border);
            color: var(--text-muted);
        }

        .table-custom tr:hover td {
            color: #ffffff;
            background-color: rgba(255,255,255,0.02);
        }

        /* Clean Buttons styling */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #0bc28d, var(--accent-green));
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.1s, opacity 0.2s, box-shadow 0.2s;
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
            background-color: rgba(6, 9, 17, 0.85); 
            backdrop-filter: blur(10px); 
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
            background: #111827; 
            border: 1px solid rgba(239, 68, 68, 0.2); 
            border-radius: 16px; 
            padding: 2.25rem 2rem; 
            width: 90%; 
            max-width: 440px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); 
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
            font-weight: 600; 
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
            font-weight: 600; 
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

        /* Premium Floating Toast Notifications */
        .alert {
            position: fixed !important;
            top: 24px !important;
            right: 24px !important;
            z-index: 99999 !important;
            padding: 1rem 1.5rem !important;
            border-radius: 10px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
            min-width: 300px !important;
            max-width: 450px !important;
            animation: toast-in-out 4s forwards cubic-bezier(0.68, -0.55, 0.27, 1.55) !important;
            backdrop-filter: blur(10px) !important;
            margin-bottom: 0 !important;
        }

        .alert-success {
            background-color: rgba(17, 24, 39, 0.95) !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
            color: var(--accent-green) !important;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.15), 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }

        .alert-danger {
            background-color: rgba(17, 24, 39, 0.95) !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            color: var(--accent-red) !important;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.15), 0 10px 25px rgba(0, 0, 0, 0.5) !important;
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

    @include('layouts.sidebar')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        @include('layouts.header')

        <!-- Main View Area -->
        <main class="content-container">
            @yield('content')
        </main>
    </div>

    <!-- Toggle Script -->
    <script>
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

        // Global Download Excel (CSV) function for all list tables
        function downloadExcel() {
            const table = document.querySelector('.table-custom');
            if (!table) return;
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                // Exclude last column (Action column)
                for (let j = 0; j < cols.length - 1; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s+)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            // Get category/list name for download filename if possible
            let filename = "export.csv";
            const titleEl = document.querySelector('.panel-title');
            if (titleEl) {
                filename = titleEl.innerText.toLowerCase().replace(/[^a-z0-9]+/g, '_') + '.csv';
            }
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Modern Custom Confirm Modal logic
        let activeConfirmCallback = null;

        function showCustomConfirm(message, callback) {
            document.getElementById('customConfirmMessage').innerText = message;
            activeConfirmCallback = callback;
            
            const modal = document.getElementById('customConfirmModal');
            const panel = modal.querySelector('.confirm-panel');
            const iconBox = modal.querySelector('.confirm-icon-box');
            const icon = iconBox ? iconBox.querySelector('i') : null;
            const okBtn = document.getElementById('customConfirmOk');
            
            const isUntick = message.toLowerCase().includes('untick');
            
            if (isUntick) {
                if (panel) panel.classList.add('confirm-warning');
                if (iconBox) iconBox.classList.add('confirm-warning');
                if (okBtn) {
                    okBtn.classList.add('confirm-warning');
                    okBtn.innerText = 'Untick';
                }
                if (icon) {
                    icon.className = 'bx bx-info-circle';
                }
            } else {
                if (panel) panel.classList.remove('confirm-warning');
                if (iconBox) iconBox.classList.remove('confirm-warning');
                if (okBtn) {
                    okBtn.classList.remove('confirm-warning');
                    okBtn.innerText = 'Delete';
                }
                if (icon) {
                    icon.className = 'bx bxs-trash-alt';
                }
            }
            
            modal.style.display = 'flex';
            // Force reflow
            modal.offsetHeight;
            modal.classList.add('show');
        }

        function closeCustomConfirm(confirmed) {
            const modal = document.getElementById('customConfirmModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                if (confirmed && activeConfirmCallback) {
                    activeConfirmCallback();
                }
                activeConfirmCallback = null;
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const confirmCancel = document.getElementById('customConfirmCancel');
            const confirmOk = document.getElementById('customConfirmOk');
            const confirmModal = document.getElementById('customConfirmModal');

            if (confirmCancel) {
                confirmCancel.addEventListener('click', () => closeCustomConfirm(false));
            }
            if (confirmOk) {
                confirmOk.addEventListener('click', () => closeCustomConfirm(true));
            }
            if (confirmModal) {
                confirmModal.addEventListener('click', (e) => {
                    if (e.target.id === 'customConfirmModal') {
                        closeCustomConfirm(false);
                    }
                });
            }
        });

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
                    event.preventDefault();
                    showCustomConfirm(message, () => form.submit());
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initCustomConfirmForms);

        // Global helper to show dynamic toast alerts
        function showToast(message, type = 'success') {
            const existing = document.querySelectorAll('.alert');
            existing.forEach(el => el.remove());
            
            const toast = document.createElement('div');
            toast.className = `alert alert-${type}`;
            toast.innerText = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }

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
        function swapContent(html, url) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newContent = doc.querySelector('.content-container');
            const currentContent = document.querySelector('.content-container');
            
            if (newContent && currentContent) {
                currentContent.innerHTML = newContent.innerHTML;
            }
            
            if (doc.title) {
                document.title = doc.title;
            }

            updateActiveSidebar(url || window.location.href);

            if (newContent) {
                const scripts = newContent.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    newScript.textContent = oldScript.textContent;
                    for (let attr of oldScript.attributes) {
                        newScript.setAttribute(attr.name, attr.value);
                    }
                    document.body.appendChild(newScript);
                    newScript.remove();
                });
            }
            
            window.scrollTo(0, 0);
        }

        function updateActiveSidebar(urlStr) {
            try {
                const url = new URL(urlStr);
                const path = url.pathname;
                
                const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
                sidebarLinks.forEach(link => {
                    const linkUrl = new URL(link.href, window.location.origin);
                    if (linkUrl.pathname === path) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            } catch(e) {}
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
            
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                link.getAttribute('target') === '_blank' || 
                link.getAttribute('download') !== null || 
                link.getAttribute('data-no-pjax') !== null) {
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
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
            if (form.getAttribute('data-no-pjax') !== null) {
                return;
            }
            
            event.preventDefault();
            showLoader();
            
            const action = form.action || window.location.href;
            const method = (form.method || 'POST').toUpperCase();
            const formData = new FormData(form);
            
            try {
                const response = await fetch(action, {
                    method: method === 'GET' ? 'GET' : 'POST',
                    body: method === 'GET' ? null : formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                const finalUrl = response.url || action;
                const html = await response.text();
                
                swapContent(html, finalUrl);
                
                window.history.pushState({ url: finalUrl }, '', finalUrl);
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
            
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                link.getAttribute('target') === '_blank' || 
                link.getAttribute('download') !== null || 
                link.getAttribute('data-no-pjax') !== null) {
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
        window.confirm = function(message) {
            const activeEl = document.activeElement;
            if (activeEl && activeEl.dataset.confirmed) {
                delete activeEl.dataset.confirmed;
                return true;
            }
            
            const activeForm = activeEl ? activeEl.closest('form') : null;
            const activeLink = activeEl ? activeEl.closest('a') : null;
            
            if (activeForm) {
                showCustomConfirm(message, function() {
                    const event = new Event('submit', { cancelable: true, bubbles: true });
                    activeForm.dispatchEvent(event);
                    if (!event.defaultPrevented) {
                        originalSubmit.call(activeForm);
                    }
                });
            } else if (activeLink && activeLink.href) {
                showCustomConfirm(message, function() {
                    loadPage(activeLink.href);
                });
            } else if (activeEl) {
                showCustomConfirm(message, function() {
                    activeEl.dataset.confirmed = 'true';
                    activeEl.click();
                });
            }
            
            return false;
        };

        // PJAX event delegation listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', handleLinkClick);
            document.body.addEventListener('mouseover', handleLinkHover);
            document.body.addEventListener('mouseout', handleLinkMouseout);
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
            const appKey = "{{ env('REVERB_APP_KEY', 'a8xsms5lc52lrzjloqxv') }}";
            const host = "{{ env('REVERB_HOST', 'localhost') }}";
            const port = {{ env('REVERB_PORT', 8080) }};
            const scheme = "{{ env('REVERB_SCHEME', 'http') }}";
            const currentUserId = {{ auth()->id() ?? 'null' }};
            
            if (appKey && host) {
                const pusher = new Pusher(appKey, {
                    wsHost: host,
                    wsPort: port,
                    wssPort: port,
                    forceTLS: scheme === 'https',
                    enabledTransports: ["ws", "wss"],
                    cluster: "mt1"
                });

                const channel = pusher.subscribe('projects');
                channel.bind('project.updated', function(data) {
                    console.log('Realtime project.updated received:', data);
                    if (typeof activeProjectId !== 'undefined' && data.projectId == activeProjectId && data.userId != currentUserId) {
                        console.log('Triggering background reload for project ID:', data.projectId);
                        reloadCurrentPageContent();
                    }
                });
            }
        })();
    </script>

    <!-- Modern Premium Custom Confirm Modal HTML -->
    <div id="customConfirmModal">
        <div class="confirm-panel">
            <div class="confirm-icon-box">
                <i class="bx bxs-trash-alt"></i>
            </div>
            <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">Confirm Action</h3>
            <p id="customConfirmMessage" style="color: #9ca3af; font-size: 0.95rem; line-height: 1.5; margin-bottom: 2rem;">Are you sure you want to proceed?</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="customConfirmCancel" class="confirm-btn-cancel">Cancel</button>
                <button id="customConfirmOk" class="confirm-btn-ok">Delete</button>
            </div>
        </div>
    </div>
</body>
</html>
