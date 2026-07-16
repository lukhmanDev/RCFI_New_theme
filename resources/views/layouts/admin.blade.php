<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_collapsed.png') }}">
    <title>@yield('title', 'Dashboard') | Admin Panel</title>
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- Premium CSS Layout and Design System -->
    <style>
        :root {
            --bg-color: #f5f7fb;
            --panel-bg: #ffffff;
            --panel-border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent-purple: #6366f1;
            --accent-cyan: #0ea5e9;
            --accent-green: #10b981;
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
            border: 2px solid var(--accent-purple);
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

        /* Clean Buttons styling */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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

        /* Premium Floating Toast Notifications */
        .alert {
            position: fixed !important;
            top: 24px !important;
            right: 24px !important;
            z-index: 99999 !important;
            padding: 1rem 1.5rem !important;
            border-radius: 10px !important;
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
            min-width: 300px !important;
            max-width: 450px !important;
            animation: toast-in-out 4s forwards cubic-bezier(0.68, -0.55, 0.27, 1.55) !important;
            backdrop-filter: blur(10px) !important;
            margin-bottom: 0 !important;
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
            border: 2px solid var(--accent-purple);
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
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            width: 100% !important;
            margin-top: 1rem !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15) !important;
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

        function showCustomConfirm(message, callback, isRejection = false) {
            document.getElementById('customConfirmMessage').innerText = message;
            activeConfirmCallback = callback;
            
            const modal = document.getElementById('customConfirmModal');
            const panel = modal.querySelector('.confirm-panel');
            const iconBox = modal.querySelector('.confirm-icon-box');
            const icon = iconBox ? iconBox.querySelector('i') : null;
            const okBtn = document.getElementById('customConfirmOk');
            const remarksContainer = document.getElementById('confirmRemarksContainer');
            const remarksInput = document.getElementById('confirmRemarksInput');
            
            const isUntick = message.toLowerCase().includes('untick');
            
            if (remarksContainer) {
                remarksContainer.style.display = isRejection ? 'block' : 'none';
                if (remarksInput) remarksInput.value = '';
            }

            if (isRejection) {
                if (panel) panel.classList.add('confirm-warning');
                if (iconBox) iconBox.classList.add('confirm-warning');
                if (okBtn) {
                    okBtn.classList.add('confirm-warning');
                    okBtn.innerText = 'Reject';
                }
                if (icon) {
                    icon.className = 'bx bx-x-circle';
                }
            } else if (isUntick) {
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
            const remarksInput = document.getElementById('confirmRemarksInput');
            const remarks = remarksInput ? remarksInput.value : '';
            
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                if (confirmed && activeConfirmCallback) {
                    activeConfirmCallback(remarks);
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
            initAllTablePagers();
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
            if (form.getAttribute('data-no-pjax') !== null) {
                return;
            }
            
            event.preventDefault();
            showLoader();
            
            const action = form.getAttribute('action') || window.location.href;
            const method = (form.getAttribute('method') || 'POST').toUpperCase();
            const formData = new FormData(form);
            
            try {
                const response = await fetch(action, {
                    method: method === 'GET' ? 'GET' : 'POST',
                    body: method === 'GET' ? null : formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                const finalUrl = response.url || action;
                const html = await response.text();
                
                swapContent(html, finalUrl);
                
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
                const actionIndex = headerText.findIndex(t => t === 'action');

                const rows = Array.from(table.querySelectorAll('tbody tr'));
                
                rows.forEach(row => {
                    // Skip if row already formatted
                    if (row.dataset.formatted === 'true') return;
                    row.dataset.formatted = 'true';

<<<<<<< HEAD
                    // 1. Format Name column to show name
                    if (nameIndex !== -1) {
                        const cell = row.cells[nameIndex];
                        if (cell) {
                            const nameText = cell.textContent.trim();
                            if (nameText && nameText !== 'N/A') {
                                cell.innerHTML = `
                                    <div style="font-weight: 700; color: #1e293b;">${nameText}</div>
=======
                    // 1. Format Name column to show circular initials avatar next to name
                    if (nameIndex !== -1) {
                        const cell = row.cells[nameIndex];
                        if (cell && !cell.querySelector('.avatar-wrapper-js')) {
                            const nameText = cell.textContent.trim();
                            if (nameText && nameText !== 'N/A') {
                                // Extract initials
                                const words = nameText.split(' ').filter(w => w.length > 0);
                                let initials = '';
                                words.forEach(w => {
                                    initials += w.charAt(0).toUpperCase();
                                });
                                initials = initials.substring(0, 2);

                                // Pastel colors map based on name hash
                                const colors = [
                                    { bg: '#eff6ff', text: '#3b82f6' },
                                    { bg: '#ecfdf5', text: '#10b981' },
                                    { bg: '#fff7ed', text: '#f97316' },
                                    { bg: '#f5f3ff', text: '#8b5cf6' },
                                    { bg: '#fdf2f8', text: '#ec4899' },
                                    { bg: '#f0fdf4', text: '#15803d' }
                                ];
                                let hash = 0;
                                for (let i = 0; i < nameText.length; i++) {
                                    hash = nameText.charCodeAt(i) + ((hash << 5) - hash);
                                }
                                const color = colors[Math.abs(hash) % colors.length];

                                cell.innerHTML = `
                                    <div class="avatar-wrapper-js" style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: ${color.bg}; color: ${color.text}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem; flex-shrink: 0;">
                                            ${initials}
                                        </div>
                                        <div style="font-weight: 700; color: #1e293b;">${nameText}</div>
                                    </div>
>>>>>>> 931b70b15894ca6c070c71c54872cb207eaf9da3
                                `;
                            }
                        }
                    }

                    // 2. Format Application ID and Project ID columns
                    if (appIdIndex !== -1) {
                        const cell = row.cells[appIdIndex];
                        if (cell) {
                            cell.style.color = '#4f46e5';
                            cell.style.fontWeight = '700';
                            cell.style.fontSize = '0.88rem';
                        }
                    }

                    // 3. Format Status column to status dot
                    if (statusIndex !== -1) {
                        const cell = row.cells[statusIndex];
                        if (cell) {
                            const statusSpan = cell.querySelector('span');
                            const statusText = (statusSpan ? statusSpan.textContent : cell.textContent).trim();
                            
                            let color = '#94a3b8';
                            if (statusText === 'Approved' || statusText === 'Active') {
                                color = '#10b981';
                            } else if (statusText === 'Pending') {
                                color = '#f59e0b';
                            } else if (statusText === 'Rejected' || statusText === 'Suspended') {
                                color = '#ef4444';
                            }

                            cell.innerHTML = `
                                <div style="display: inline-flex; align-items: center; gap: 0.35rem; vertical-align: middle; justify-content: center; width: 100%;">
                                    <span style="width: 8px; height: 8px; background: ${color}; border-radius: 50%; display: inline-block;"></span>
                                    <span style="color: ${color}; font-weight: 700; font-size: 0.82rem;">${statusText}</span>
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
            });
        }

        function alignProjectsLayout() {
            const headerPanel = document.querySelector('.group-header-panel');
            const controlsRow = document.querySelector('.controls-row');
            const panel = document.querySelector('.panel');
            
            if (headerPanel && controlsRow && panel) {
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
            const pageSize = parseInt(table.getAttribute('data-page-size')) || 10;
            let currentPage = 1;
            table.dataset.paginated = 'true';
            
            // Create pagination container
            const container = document.createElement('div');
            container.className = 'table-pagination-container';
            container.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem; font-size: 0.85rem; color: #64748b; padding-top: 1rem; border-top: 1px solid #e2e8f0;';
            
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
                });
            }
            
            table.pagerUpdate = update;
            
            // Listen for filter input changes to reset pagination
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
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

        function renderControls(container, currentPage, totalPages, startIdx, endIdx, totalRows, onPageChange) {
            if (totalRows === 0) {
                container.innerHTML = '<div>Showing 0 to 0 of 0 results</div>';
                return;
            }
            
            const info = `<div>Showing ${startIdx} to ${endIdx} of ${totalRows} results</div>`;
            
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
                    ? 'background: #4f46e5; border-color: #4f46e5; color: #ffffff; font-weight: 600;' 
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
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            initAllTablePagers();
        });
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
                <label style="display: block; color: #9ca3af; font-size: 0.85rem; margin-bottom: 0.4rem; font-weight: 500;">Rejection Reason</label>
                <textarea id="confirmRemarksInput" placeholder="Provide rejection reason (optional)…" style="width: 100%; height: 70px; background-color: #1f2937; border: 1px solid #374151; color: #ffffff; padding: 0.5rem; border-radius: 6px; font-size: 0.85rem; outline: none; resize: vertical; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="customConfirmCancel" class="confirm-btn-cancel">Cancel</button>
                <button id="customConfirmOk" class="confirm-btn-ok">Delete</button>
            </div>
        </div>
    </div>
</body>
</html>
