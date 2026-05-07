<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TN Municipal Corporation')</title>

    <!-- Bootstrap 5 CSS + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        /* Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
        }

        .sidebar .nav-link {
            color: #FFCBCB;
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
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
        }

        /* Navbar */
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

        /* Main Content */
        .main-content {
            transition: all 0.3s ease;
        }

        /* Cards */
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

        .content-panel {
            animation: fadeSlideUp 0.5s ease;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tables */
        .table-custom {
            background: white;
            border-radius: 20px;
            overflow: hidden;
        }

        .table thead th {
            background: #102C57;
            color: white;
            font-weight: 600;
            border: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                width: 280px;
                z-index: 1050;
                transition: left 0.3s ease;
            }
            .sidebar.show { left: 0; }
            .menu-toggle { display: block; }
        }

        @media (min-width: 769px) {
            .menu-toggle { display: none; }
        }

        .corp-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid #FFCBCB;
        }

        .corp-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #FFF9F9, white);
            box-shadow: 0 10px 25px rgba(22, 121, 171, 0.15);
        }

        .emblem-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .badge.bg-primary { background-color: #1679AB !important; }
        .badge.bg-success { background-color: #102C57 !important; }
        .badge.bg-info { background-color: #FFB1B1 !important; color: #102C57; }

        .ward-list-item {
            transition: all 0.2s ease;
        }
        .ward-list-item:hover {
            background-color: #FFCBCB;
            transform: translateX(5px);
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR -->
        <div class="col-auto sidebar min-vh-100" id="sidebar">
            <div class="logo-area text-center">
                <img src="{{ asset('images/tn-emblem.png') }}" alt="TamilNadu" class="emblem-img" onerror="this.src='https://via.placeholder.com/80x80?text=TN'">
                <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">TN Municipal Corp</h6>
                <small class="text-white-50">e-Governance Suite</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" data-page="dashboard" href="#">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" data-page="wards" href="#">
                    <i class="fas fa-map-marker-alt"></i> Wards
                </a>
                <a class="nav-link" data-page="analysis" href="#">
                    <i class="fas fa-chart-line"></i> Analysis
                </a>
                <a class="nav-link" data-page="reports" href="#">
                    <i class="fas fa-file-alt"></i> Reports
                </a>
            </nav>
            <div class="mt-auto p-3">
                <hr class="bg-secondary" style="opacity:0.3;">
                <form action="{{ route('corporation.logout') }}" method="POST" id="logout-form">
                    @csrf
                    <a class="nav-link text-white-50" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col main-content">
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
                                <small class="d-block text-muted">{{ $corporation->name ?? 'Municipal Corporation' }}</small>
                            </div>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Dynamic Content -->
            <div class="p-4">
                @yield('content-panels')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Panel switching
    const panels = {
        dashboard: document.getElementById('dashboardPanel'),
        wards: document.getElementById('wardsPanel'),
        analysis: document.getElementById('analysisPanel'),
        reports: document.getElementById('reportsPanel')
    };

    function showPanel(panelId) {
        Object.keys(panels).forEach(key => {
            if (panels[key]) panels[key].style.display = 'none';
        });
        if (panels[panelId]) panels[panelId].style.display = 'block';

        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-page') === panelId) {
                link.classList.add('active');
            }
        });
    }

    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            if (page && panels[page]) showPanel(page);
            if (window.innerWidth < 769) sidebar.classList.remove('show');
        });
    });

    @stack('scripts')
</script>
</body>
</html>
