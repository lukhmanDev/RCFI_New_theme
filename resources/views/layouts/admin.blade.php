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
            padding: 0 2rem;
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-brand i {
            color: var(--accent-cyan);
            -webkit-text-fill-color: initial;
            font-size: 1.5rem;
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
            color: var(--accent-cyan);
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
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.1s, opacity 0.2s;
        }

        .btn-custom:hover {
            opacity: 0.9;
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
            }

            .topbar-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>

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
    </script>
</body>
</html>
