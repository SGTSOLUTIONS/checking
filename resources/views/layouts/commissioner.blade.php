<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TN Municipal Corporation | Commissioner Suite</title>

    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-navy: #0B2B40;
            --teal-bright: #1E7F6E;
            --coral-soft: #F4A261;
            --sand-light: #FFF3E0;
            --accent-gold: #E9C46A;
            --gray-cool: #F8F9FA;
            --text-deep: #212529;
            --shadow-sm: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            --shadow-md: 0 20px 25px -12px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: linear-gradient(145deg, #EFF6F5 0%, #E2ECE9 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* modern glass-morphism sidebar */
        .sidebar {
            background: rgba(11, 43, 64, 0.96);
            backdrop-filter: blur(2px);
            color: white;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 6px 0 24px rgba(0, 0, 0, 0.08);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1050;
        }

        .sidebar .nav-link {
            color: #CCE3DE;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.2rem;
        }

        .sidebar .nav-link:hover {
            background: #1E7F6E;
            color: white;
            transform: translateX(4px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(95deg, #1E7F6E, #0E5E50);
            color: white;
            box-shadow: 0 8px 18px rgba(30, 127, 110, 0.3);
        }

        .logo-area {
            padding: 1.8rem 1rem;
            border-bottom: 1px solid rgba(255, 215, 170, 0.2);
            margin-bottom: 1.5rem;
        }

        .emblem-icon {
            background: #F4A261;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 28px;
            margin: 0 auto 12px;
            font-size: 28px;
            color: #0B2B40;
            box-shadow: 0 10px 18px -6px rgba(0,0,0,0.2);
        }

        /* main navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            margin: 16px 24px 0 24px;
            padding: 10px 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(255,255,240,0.6);
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #1E7F6E, #0B2B40);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* stat cards premium */
        .stat-card {
            background: white;
            border-radius: 32px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
            padding: 1.35rem;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-bottom: 3px solid #F4A261;
        }

        .stat-icon {
            background: rgba(30, 127, 110, 0.12);
            border-radius: 28px;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #1E7F6E;
        }

        .content-panel {
            animation: fadeSlide 0.45s ease-out;
        }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(18px);}
            to { opacity: 1; transform: translateY(0);}
        }

        /* ward card grid */
        .ward-grid-card {
            background: white;
            border-radius: 28px;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #eef2f0;
            padding: 1rem;
        }

        .ward-grid-card:hover {
            transform: translateY(-6px);
            background: #FFFFFF;
            box-shadow: 0 20px 30px -12px rgba(0,0,0,0.12);
            border-color: #F4A261;
        }

        .table-modern {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .table-modern thead th {
            background: #0B2B40;
            color: white;
            font-weight: 600;
            border: none;
            padding: 14px 12px;
        }

        .btn-soft-primary {
            background: #EFF6F5;
            color: #1E7F6E;
            border: none;
            border-radius: 40px;
            padding: 8px 18px;
            font-weight: 500;
        }
        .btn-soft-primary:hover {
            background: #1E7F6E;
            color: white;
        }

        .badge-completion {
            background: #E9C46A20;
            color: #B86B2E;
            border-radius: 40px;
            padding: 6px 12px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -280px; top: 0; bottom: 0; width: 280px; transition: left 0.25s; }
            .sidebar.show { left: 0; }
            .navbar-custom { margin: 12px 16px; }
            .main-content { padding-top: 0; }
        }
        @media (min-width: 769px) {
            .menu-toggle { display: none; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR elevated -->
        <div class="col-auto sidebar min-vh-100" id="sidebar">
            <div class="logo-area text-center">
                <div class="emblem-icon mx-auto">
                    <i class="fas fa-city"></i>
                </div>
                <h5 class="fw-bold mb-0 mt-2" style="letter-spacing: -0.3px;">TN Municipal Corp</h5>
                <small class="opacity-75">Commissioner Console</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" data-page="dashboard" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" data-page="wards" href="#"><i class="fas fa-layer-group"></i> Wards & Zones</a>
                <a class="nav-link" data-page="analysis" href="#"><i class="fas fa-chart-pie"></i> Analytics</a>
                <a class="nav-link" data-page="reports" href="#"><i class="fas fa-file-contract"></i> Reports</a>
            </nav>
            <div class="mt-auto p-3 pb-4">
                <hr class="bg-white-50">
                <a class="nav-link text-white-70" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Secure Logout
                </a>
                <form id="logout-form" action="{{ route('corporation.logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>

        <!-- MAIN SECTION -->
        <div class="col main-content">
            <nav class="navbar-custom d-flex justify-content-between align-items-center">
                <button class="btn btn-light rounded-pill menu-toggle shadow-sm" id="menuToggle" style="border: none; background: #F8F9FA;"><i class="fas fa-bars text-dark"></i></button>
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light rounded-pill px-3 py-1 d-none d-md-block">
                        <i class="fas fa-map-pin text-teal"></i> <span class="small fw-semibold">{{ $corporation->name ?? 'Greater Chennai Corp' }}</span>
                    </div>
                    <div class="dropdown user-dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="user-avatar"><i class="fas fa-user-shield"></i></div>
                            <div class="d-none d-sm-block">
                                <span class="fw-semibold">{{ Auth::guard('corporation')->user()->name ?? 'Dr. Meera Iyer' }}</span>
                                <small class="d-block text-muted">Commissioner</small>
                            </div>
                            <i class="fas fa-chevron-down text-secondary"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="p-4 pt-2">
                @yield('content-panels')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(menuToggle) menuToggle.addEventListener('click', () => sidebar.classList.toggle('show'));

    // panels controller
    const panels = {
        dashboard: document.getElementById('dashboardPanel'),
        wards: document.getElementById('wardsPanel'),
        analysis: document.getElementById('analysisPanel'),
        reports: document.getElementById('reportsPanel')
    };
    window.showPanel = function(panelId) {
        Object.keys(panels).forEach(p => { if(panels[p]) panels[p].style.display = 'none'; });
        if(panels[panelId]) panels[panelId].style.display = 'block';
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.classList.remove('active');
            if(link.getAttribute('data-page') === panelId) link.classList.add('active');
        });
        if(window.innerWidth < 769) sidebar.classList.remove('show');
        if(panelId === 'analysis' && !window.chartsDrawn) { setTimeout(() => { if(typeof window.initCharts === 'function') window.initCharts(); window.chartsDrawn = true; }, 120); }
    };
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            if(page && panels[page]) showPanel(page);
        });
    });
</script>

@stack('scripts')
</body>
</html>
