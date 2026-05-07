<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>@yield('title', 'Tamil Nadu Municipal Corporation | Commissioner Dashboard')</title>

    <!-- Bootstrap 5 CSS + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Chart.js for Analysis Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --navy-deep: #102C57;
            --ocean-teal: #1679AB;
            --blush-soft: #FFB1B1;
            --pastel-pink: #FFCBCB;
            --bg-light: #FFF9F9;
            --text-dark: #102C57;
            --card-white: #FFFFFF;
            --sidebar-bg: linear-gradient(180deg, #102C57 0%, #0A1F3F 100%);
            --accent-gradient: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
        }

        body {
            background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Animated Gradient Orbs */
        body::before {
            content: "";
            position: fixed;
            width: 300px;
            height: 300px;
            background: #FFB1B1;
            filter: blur(120px);
            opacity: 0.2;
            top: -100px;
            right: -50px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob 12s infinite alternate ease-in-out;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            width: 400px;
            height: 400px;
            background: #FFCBCB;
            filter: blur(140px);
            opacity: 0.18;
            bottom: -100px;
            left: -80px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob2 15s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); opacity: 0.15; }
            100% { transform: translate(40px, 30px) scale(1.2); opacity: 0.28; }
        }

        @keyframes floatBlob2 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.12; }
            100% { transform: translate(-30px, -40px) scale(1.3); opacity: 0.25; }
        }

        /* Sidebar Styling */
        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #FFCBCB;
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: #1679AB;
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(22, 121, 171, 0.4);
        }

        .sidebar .nav-link.active {
            background: #1679AB;
            color: white;
            box-shadow: 0 4px 12px rgba(22, 121, 171, 0.5);
        }

        .sidebar .nav-link i {
            width: 28px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar .logo-area {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255, 177, 177, 0.3);
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar .logo-area .emblem-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #FFCBCB;
            padding: 5px;
            background: white;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        /* Top Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            border-radius: 0;
        }

        .user-dropdown {
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-dropdown:hover {
            transform: scale(1.02);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: #1679AB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .official-emblem-sm {
            width: 45px;
            height: 45px;
        }

        /* Card Styling */
        .stat-card {
            background: white;
            border-radius: 24px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(22, 121, 171, 0.15);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            background: rgba(22, 121, 171, 0.12);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #1679AB;
        }

        /* Ward Card */
        .ward-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }

        .ward-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .ward-header {
            background: linear-gradient(135deg, #102C57, #1679AB);
            color: white;
            padding: 20px;
        }

        .ward-stats {
            padding: 20px;
        }

        .stat-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-box:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                left: -280px;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
            }
        }

        @media (min-width: 993px) {
            .menu-toggle {
                display: none;
            }
        }

        .btn-back {
            background: white;
            color: #102C57;
            border: none;
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #FFCBCB;
            transform: translateX(-3px);
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #1679AB;
            border-radius: 5px;
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="logo-area">
                <img src="{{ asset('storage/' . ($corporation->logo ?? 'images/logo.png')) }}"
                     alt="{{ $corporation->corporation_name ?? 'Corporation' }}"
                     class="emblem-img"
                     onerror="this.src='https://via.placeholder.com/80x80?text=Logo'">
                <h6 class="fw-bold mb-0 mt-3" style="color: #FFCBCB;">{{ $corporation->corporation_name ?? 'Municipal Corp' }}</h6>
                <small class="text-white-50">{{ $corporation->district ?? 'Tamil Nadu' }}</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}"
                   href="{{ route('corporation.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('corporation.wards') ? 'active' : '' }}"
                   href="{{ route('corporation.wards') }}">
                    <i class="fas fa-map-marker-alt"></i> Wards
                </a>
                <a class="nav-link {{ request()->routeIs('corporation.analysis') ? 'active' : '' }}"
                   href="{{ route('corporation.analysis') }}">
                    <i class="fas fa-chart-line"></i> Analysis
                </a>
                <a class="nav-link {{ request()->routeIs('corporation.profile') ? 'active' : '' }}"
                   href="{{ route('corporation.profile') }}">
                    <i class="fas fa-user-circle"></i> Profile
                </a>
            </nav>
            <div class="mt-auto p-3">
                <hr class="bg-secondary" style="opacity:0.3;">
                <form method="POST" action="{{ route('corporation.logout') }}" id="logout-form">
                    @csrf
                    <a class="nav-link text-white-50" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="navbar-custom d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-secondary menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="official-emblem-sm">
                        <svg viewBox="0 0 100 100" fill="none" width="45" height="45">
                            <circle cx="50" cy="50" r="45" fill="#FFCBCB" />
                            <text x="50" y="65" font-size="20" text-anchor="middle" fill="#102C57" font-weight="bold">TN</text>
                        </svg>
                    </div>
                    <div class="dropdown user-dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <span class="fw-semibold" style="color:#102C57;">{{ Auth::guard('corporation')->user()->name ?? 'Commissioner' }}</span>
                                <small class="d-block text-muted">Municipal Commissioner</small>
                            </div>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('corporation.profile') }}"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('corporation.dashboard') }}"><i class="fas fa-dashboard me-2"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle for mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Close sidebar when clicking a link on mobile
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 993) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>

@stack('scripts')

</body>

</html>
