<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Commissioner Dashboard') - {{ config('app.name') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --primary-color: #2c3e8f;
            --secondary-color: #1e3a5f;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --dark-color: #1f2937;
            --light-color: #f3f4f6;
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-header p {
            font-size: 0.75rem;
            opacity: 0.8;
            margin: 5px 0 0;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu .menu-item {
            padding: 12px 25px;
            margin: 5px 0;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .sidebar-menu .menu-item:hover,
        .sidebar-menu .menu-item.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid white;
            color: white;
        }

        .sidebar-menu .menu-item i {
            width: 24px;
            font-size: 1.2rem;
        }

        .sidebar-menu .menu-item span {
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Header Styles */
        .top-header {
            background: white;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--dark-color);
            transition: all 0.3s ease;
        }

        .toggle-sidebar:hover {
            color: var(--primary-color);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .user-dropdown:hover {
            background: var(--light-color);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            line-height: 1.2;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .user-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Content Area */
        .content-wrapper {
            padding: 30px;
        }

        /* Footer */
        .footer {
            background: white;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 0.85rem;
            color: #6b7280;
        }

        /* Loading Spinner */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 15px;
            }

            .top-header {
                padding: 0 15px;
            }

            .user-info {
                display: none;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-city"></i> CorpManage</h3>
            <p>Commissioner Dashboard</p>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('corporation.dashboard') }}" class="menu-item {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('buildings')">
                <i class="fas fa-building"></i>
                <span>Buildings</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('properties')">
                <i class="fas fa-map-marker-alt"></i>
                <span>Properties</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('tax')">
                <i class="fas fa-chart-line"></i>
                <span>Tax Collection</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('defaulters')">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Defaulters</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('reports')">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('settings')">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="top-header">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="header-right">
                <div class="notification-icon" onclick="showNotifications()">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="notification-badge" id="notificationBadge">3</span>
                </div>

                <div class="user-dropdown" data-bs-toggle="dropdown">
                    @if(Auth::guard('corporation')->user())
                        <img src="{{ Auth::guard('corporation')->user()->profile ?? 'https://ui-avatars.com/api/?background=2c3e8f&color=fff&name=' . urlencode(Auth::guard('corporation')->user()->name) }}"
                             class="user-avatar" alt="User Avatar">
                        <div class="user-info">
                            <div class="user-name">{{ Auth::guard('corporation')->user()->name }}</div>
                            <div class="user-role">{{ ucfirst(Auth::guard('corporation')->user()->role ?? 'Commissioner') }}</div>
                        </div>
                    @endif
                    <i class="fas fa-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-bell me-2"></i>Notifications</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-lock me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('corporation.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Corporation Management System. All rights reserved.</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Toggle Sidebar
        $('#toggleSidebar').click(function() {
            $('#sidebar').toggleClass('collapsed');
            $('#mainContent').toggleClass('expanded');
            localStorage.setItem('sidebarCollapsed', $('#sidebar').hasClass('collapsed'));
        });

        // Load sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            $('#sidebar').addClass('collapsed');
            $('#mainContent').addClass('expanded');
        }

        // Show loading
        function showLoading() {
            $('#loadingOverlay').fadeIn();
        }

        function hideLoading() {
            $('#loadingOverlay').fadeOut();
        }

        // Toast notifications
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Show notification
        function showNotification(message, type = 'success') {
            toastr[type](message);
        }

        // Show section
        function showSection(section) {
            showLoading();
            setTimeout(() => {
                hideLoading();
                showNotification(`Loading ${section} section...`, 'info');
            }, 500);
        }

        // Show notifications
        function showNotifications() {
            Swal.fire({
                title: 'Notifications',
                html: `
                    <div class="text-start">
                        <div class="mb-3">
                            <strong>New Building Added</strong>
                            <p class="text-muted small">A new building has been registered in Ward 12</p>
                            <small class="text-muted">5 minutes ago</small>
                        </div>
                        <div class="mb-3">
                            <strong>Tax Payment Received</strong>
                            <p class="text-muted small">Tax payment of ₹25,000 received from Property #123</p>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                        <div>
                            <strong>System Update</strong>
                            <p class="text-muted small">System will be updated tonight at 2 AM</p>
                            <small class="text-muted">3 hours ago</small>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Close'
            });
        }

        // Auto refresh data every 5 minutes
        let autoRefresh = true;
        setInterval(function() {
            if (autoRefresh && !document.hidden) {
                location.reload();
            }
        }, 300000);

        // Handle page visibility
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Page is visible, refresh data
                $.ajax({
                    url: '{{ route("corporation.commissioner.stats") }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Stats refreshed:', response);
                    }
                });
            }
        });



        // CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
