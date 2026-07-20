<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Manajemen Perpustakaan')</title>

    {{-- Favicon dinamis dari Theme Manager --}}
    @if(theme_asset('favicon'))
        <link rel="icon" href="{{ theme_asset('favicon') }}">
    @endif


    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    @stack('styles')

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f72585;
            --danger-color: #e63946;
            --bg-color: #f8f9fa;
            --text-color: #2b2d42;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* Layout Structure */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            min-height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 20px rgba(0,0,0,0.04);
            transition: all 0.3s;
            z-index: 100;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav {
            padding: 1rem 0;
            list-style: none;
        }

        .sidebar-nav li {
            padding: 0.2rem 1.2rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.2rem;
            color: #6c757d;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar-nav a i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 10px;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: rgba(67, 97, 238, 0.08);
            color: var(--primary-color);
        }

        /* Main Content */
        #page-content-wrapper {
            width: 100%;
            transition: all 0.3s;
        }

        /* Topbar */
        .topbar {
            background: #ffffff;
            height: 70px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        /* Premium Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 1.5rem;
            font-weight: 600;
        }

        /* Gradients for Stat Cards */
        .bg-gradient-primary { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #2a9d8f 0%, #264653 100%); color: white; }
        .bg-gradient-warning { background: linear-gradient(135deg, #f77f00 0%, #d62828 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ef233c 0%, #d90429 100%); color: white; }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: white !important;
            border: none;
            border-radius: 0.3rem;
        }
        
        table.dataTable.no-footer {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
    </style>

    {{-- ===== Theme Manager: override CSS variables dinamis dari database ===== --}}
    <style id="theme-variables">
        {!! $themeCss ?? '' !!}

        body { font-family: var(--font-family, 'Inter', sans-serif); font-size: var(--font-size, 16px); }
        #sidebar { background: var(--sidebar-color, #ffffff); width: var(--sidebar-width, 260px); min-width: var(--sidebar-width, 260px); max-width: var(--sidebar-width, 260px); }
        .topbar { background: var(--navbar-color, #ffffff); }
        .card { background: var(--card-color, #ffffff); border-radius: var(--border-radius, 1rem); }
        a { color: var(--link-color, var(--primary-color)); }
        a:hover { color: var(--hover-color, var(--secondary-color)); }
        .btn-primary { background-color: var(--button-color, var(--primary-color)); border-color: var(--button-color, var(--primary-color)); }
    </style>

    {{-- Custom CSS tambahan dari admin --}}
    @if(theme('custom.css'))
        <style id="theme-custom-css">{!! theme('custom.css') !!}</style>
    @endif
</head>
<body data-theme-mode="{{ theme('general.mode', 'light') }}">


    <div id="wrapper">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Topbar -->
            @include('components.topbar')

            <!-- Main Content -->
            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    {{-- Custom JS tambahan dari admin (Theme Manager) --}}
    @if(theme('custom.js'))
        <script id="theme-custom-js">{!! theme('custom.js') !!}</script>
    @endif
</body>
</html>

