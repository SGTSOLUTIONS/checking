<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- ✅ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ✅ OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

    <!-- ✅ Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <!-- ✅ Bootstrap 5 JS (Bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --primary: #f8961e;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #e63946;
            --dark: #1d3557;
            --light: #f1faee;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-800: #343a40;
            --gray-900: #212529;
            --sidebar-width: 260px;
            --header-height: 80px;
            --border-radius: 16px;
            --border-radius-sm: 8px;
            --shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            --gradient-secondary: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 100%);
            --gradient-accent: linear-gradient(135deg, var(--accent) 0%, var(--warning) 100%);
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: #fff;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 10px;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(400px);
            opacity: 0;
        }

        .toast-success {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #f8fff9, #ffffff);
        }

        .toast-error {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #fff8f8, #ffffff);
        }

        .toast-warning {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fffdf5, #ffffff);
        }

        .toast-info {
            border-left-color: #17a2b8;
            background: linear-gradient(135deg, #f5fdff, #ffffff);
        }

        .toast-icon {
            font-size: 20px;
            margin-right: 12px;
        }

        .toast-success .toast-icon {
            color: #28a745;
        }

        .toast-error .toast-icon {
            color: #dc3545;
        }

        .toast-warning .toast-icon {
            color: #ffc107;
        }

        .toast-info .toast-icon {
            color: #17a2b8;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 16px;
            color: #999;
            cursor: pointer;
            padding: 0;
            margin-left: 15px;
            transition: color 0.3s;
        }

        .toast-close:hover {
            color: #666;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            width: 100%;
            transform: scaleX(1);
            transform-origin: left;
            animation: progressBar 5s linear forwards;
        }

        @keyframes progressBar {
            to {
                transform: scaleX(0);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f4ff 100%);
            color: var(--gray-800);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow);
        }

        .admin-container {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            grid-template-rows: var(--header-height) 1fr;
            grid-template-areas:
                "sidebar header"
                "sidebar main";
            min-height: 100vh;
        }

        .sidebar {
            grid-area: sidebar;
            background: var(--gradient-primary);
            color: white;
            padding: 0;
            position: fixed;
            width: var(--sidebar-width);
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 25px 0;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section-title {
            padding: 0 25px 12px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            font-weight: 600;
        }

        .nav-section a {
            text-decoration: none;
            color: inherit;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 25px;
            margin: 4px 0;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            border-left: 4px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: white;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .nav-text {
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            opacity: 0.7;
            text-align: center;
        }

        .header {
            grid-area: header;
            background: white;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            z-index: 999;
            position: sticky;
            top: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--gray-600);
        }

        .breadcrumb i {
            font-size: 0.7rem;
            opacity: 0.6;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-bar {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: none;
            background: var(--gray-100);
            border-radius: 50px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .action-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: var(--gray-100);
            color: var(--gray-800);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 50px;
            transition: var(--transition);
            position: relative;
        }

        .user-profile:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(114, 9, 183, 0.3);
            flex-shrink: 0;
            background: var(--gradient-secondary);
        }

        .dropdown-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.2);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--gray-600);
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            width: 280px;
            z-index: 1000;
            display: none;
            overflow: hidden;
            margin-top: 10px;
        }

        .profile-dropdown.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-header {
            padding: 25px;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
        }

        .dropdown-header .user-name {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .dropdown-header .user-role {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .dropdown-item {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 1px solid var(--gray-200);
            text-decoration: none;
            color: inherit;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: var(--gray-100);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .logout-form {
            display: contents;
        }

        .logout-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 15px;
            color: inherit;
            font-size: inherit;
            font-family: inherit;
        }

        .main-content {
            grid-area: main;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            overflow-y: auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 10px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
            border-left: 5px solid var(--primary);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card.primary {
            border-left-color: var(--primary);
        }

        .stat-card.success {
            border-left-color: var(--success);
        }

        .stat-card.warning {
            border-left-color: var(--warning);
        }

        .stat-card.danger {
            border-left-color: var(--danger);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon.primary {
            background: var(--gradient-primary);
        }

        .stat-icon.success {
            background: var(--success);
        }

        .stat-icon.warning {
            background: var(--warning);
        }

        .stat-icon.danger {
            background: var(--danger);
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: var(--dark);
            line-height: 1;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.95rem;
            margin-bottom: 8px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
        }

        .chart-actions {
            display: flex;
            gap: 10px;
        }

        .chart-btn {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
        }

        .chart-btn:hover {
            background: rgba(67, 97, 238, 0.1);
        }

        .chart-wrapper {
            flex: 1;
            position: relative;
        }

        .map-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .map-container {
            width: 100%;
        }

        .map-wrapper {
            position: relative;
            height: 500px;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .map-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 100;
        }

        .control-btn {
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
            color: var(--dark);
            font-size: 1.1rem;
        }

        .control-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        .layer-controls {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            border-radius: var(--border-radius-sm);
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 100;
            width: 220px;
        }

        .layer-title {
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .layer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .layer-checkbox {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }

        .activity-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            gap: 15px;
            padding: 18px;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .activity-item:hover {
            background: var(--gray-100);
            border-left-color: var(--primary);
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .activity-icon.primary {
            background: var(--gradient-primary);
        }

        .activity-icon.success {
            background: var(--success);
        }

        .activity-icon.warning {
            background: var(--warning);
        }

        .activity-icon.danger {
            background: var(--danger);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title-text {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .activity-desc {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .activity-time {
            font-size: 0.8rem;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
            background: var(--gradient-primary);
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .action-desc {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        @media (max-width: 1200px) {

            .charts-section,
            .map-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .admin-container {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "header"
                    "main";
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 0 20px;
            }

            .search-bar {
                width: 200px;
            }

            .user-info {
                display: none;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 20px;
            }

            .header {
                padding: 0 15px;
            }

            .search-bar {
                display: none;
            }
        }
    </style>
    @yield('css')
</head>

<body>
    <div class="admin-container">
        <div id="toast-container" class="toast-container"></div>

        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <div class="logo-text">GeoVision</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.mapExplore') }}"
                        class="nav-item {{ request()->routeIs('admin.mapExplore') ? 'active' : '' }}">
                        <i class="fas fa-map-marked-alt"></i>
                        <span class="nav-text">Map Explorer</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Data Layers</span>
                        <span class="nav-badge">12</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Corporations</div>
                    <a href="{{ route('admin.corporationdata') }}" class="nav-item">
                        <i class="fas fa-chart-bar"></i>
                        <span class="nav-text">Corporation Data</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-item">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                        <span class="nav-badge">3</span>
                    </a>
                    <a href="{{ route('admin.team.index') }}" class="nav-item">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Teams</span>
                        <span class="nav-badge">3</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Data Management</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-chart-pie"></i>
                        <span class="nav-text">Insights</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>
                    <a href="#" class="nav-item">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                        <span class="nav-badge">3</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">Settings</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-shield-alt"></i>
                        <span class="nav-text">Security</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Support</div>
                    <a href="#" class="nav-item">
                        <i class="fas fa-question-circle"></i>
                        <span class="nav-text">Help Center</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-life-ring"></i>
                        <span class="nav-text">Support Tickets</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                GeoVision v3.0.1
            </div>
        </aside>

        <header class="header">
            <div class="header-left">
                <button class="action-btn" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard Overview')</h1>
                    {{-- <div class="breadcrumb">
                        @yield('breadcrumb', '<span>Home</span><i class="fas fa-chevron-right"></i><span>Dashboard</span>')
                    </div> --}}
                </div>
            </div>

            <div class="header-right">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search maps, data, reports...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <div class="header-actions">
                    <button class="action-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <button class="action-btn" title="Messages">
                        <i class="fas fa-envelope"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <button class="action-btn" title="Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>

                <div class="user-profile" id="user-profile">
                    <div class="user-avatar">
                        @if (Auth::user()->profile && file_exists(public_path(Auth::user()->profile)))
                            <img src="{{ asset(Auth::user()->profile) }}" alt="User Profile"
                                style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            <div
                                style="width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ Auth::user()->role }}</div>
                    </div>
                    <i class="fas fa-chevron-down"></i>

                    <div class="profile-dropdown" id="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar">
                                @if (Auth::user()->profile && file_exists(public_path(Auth::user()->profile)))
                                    <img src="{{ asset(Auth::user()->profile) }}" alt="User Profile"
                                        style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div
                                        style="width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.4rem;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-role">{{ Auth::user()->role }}</div>
                        </div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Account Settings</span>
                        </a>
                        <div class="dropdown-item">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                            <span class="nav-badge">5</span>
                        </div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Privacy & Security</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Help & Support</span>
                        </a>
                        <li class="nav-item mt-4">
                            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                @csrf
                                <a href="#" class="dropdown-item"
                                    onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                                    style="text-decoration: none;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </form>
                        </li>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        // Make showToast available globally
        window.showToast = function(type, title, message, duration = 5000) {
            var toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return null;

            var toast = document.createElement('div');
            toast.className = 'toast toast-' + type;

            var icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };

            toast.innerHTML = `
            <i class="fas ${icons[type]} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <p class="toast-message">${message}</p>
            </div>
            <button class="toast-close">
                <i class="fas fa-times"></i>
            </button>
            <div class="toast-progress"></div>
        `;

            toastContainer.appendChild(toast);

            setTimeout(function() {
                toast.classList.add('show');
            }, 100);

            var closeBtn = toast.querySelector('.toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    window.removeToast(toast);
                });
            }

            if (duration > 0) {
                setTimeout(function() {
                    if (toast.parentNode) window.removeToast(toast);
                }, duration);
            }

            return toast;
        };

        window.removeToast = function(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 500);
        };

        // Profile Dropdown
        (function() {
            function setupDropdown() {
                var userProfile = document.getElementById('user-profile');
                var profileDropdown = document.getElementById('profile-dropdown');

                if (userProfile && profileDropdown) {
                    var newUserProfile = userProfile.cloneNode(true);
                    userProfile.parentNode.replaceChild(newUserProfile, userProfile);
                    var finalUserProfile = document.getElementById('user-profile');

                    finalUserProfile.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        var dropdown = document.getElementById('profile-dropdown');
                        if (dropdown) {
                            dropdown.classList.toggle('active');
                        }
                    });

                    document.addEventListener('click', function(e) {
                        var dropdown = document.getElementById('profile-dropdown');
                        var userProf = document.getElementById('user-profile');
                        if (dropdown && dropdown.classList.contains('active')) {
                            if (userProf && !userProf.contains(e.target) && !dropdown.contains(e.target)) {
                                dropdown.classList.remove('active');
                            }
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupDropdown);
            } else {
                setupDropdown();
            }
        })();

        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            var toggleSidebarBtn = document.getElementById('toggle-sidebar');
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function() {
                    var sidebar = document.querySelector('.sidebar');
                    if (sidebar) {
                        sidebar.classList.toggle('active');
                    }
                });
            }
        });
    </script>

    @yield('script')
</body>

</html>
