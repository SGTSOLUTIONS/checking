{{-- resources/views/layouts/commissioner.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $corporation->name ?? 'Tamil Nadu Municipal Corporation') . ' | Admin Dashboard'</title>

    <!-- Bootstrap 5 CSS + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- OpenLayers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>
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
            text-align: center;
        }

        .corporation-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 8px;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .corporation-logo:hover {
            transform: scale(1.05);
        }

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

        .corporation-profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFB1B1;
        }

        .main-content {
            transition: all 0.3s ease;
        }

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

        .table-custom {
            background: white;
            border-radius: 20px;
            overflow: hidden;
        }

        .table-custom th {
            background: #102C57;
            color: white;
            font-weight: 600;
            border: none;
        }

        .table thead th {
            background: #102C57;
            color: white;
            font-weight: 600;
            border: none;
        }

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
            .sidebar.show {
                left: 0;
            }
            .menu-toggle {
                display: block;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }

        .official-emblem-sm {
            width: 45px;
            height: auto;
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
            border-color: #FFB1B1;
        }

        .badge.bg-primary { background-color: #1679AB !important; }
        .badge.bg-success { background-color: #102C57 !important; }
        .badge.bg-info { background-color: #FFB1B1 !important; color: #102C57; }
        .btn-outline-secondary { border-color: #FFCBCB; color: #102C57; }
        .btn-outline-secondary:hover { background-color: #FFCBCB; border-color: #FFB1B1; }
        .text-muted { color: #5A6E7A !important; }
        .dropdown-item:active { background-color: #FFCBCB; }

        /* Map container */
        #wardMap { height: 550px; border-radius: 20px; z-index: 1; width: 100%; }
        .ol-popup {
            position: absolute;
            background-color: white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #cccccc;
            bottom: 12px;
            left: -50px;
            min-width: 250px;
            z-index: 1000;
        }
        .ol-popup:after, .ol-popup:before {
            top: 100%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }
        .ol-popup:after {
            border-top-color: white;
            border-width: 10px;
            left: 48px;
            margin-left: -10px;
        }
        .ol-popup:before {
            border-top-color: #cccccc;
            border-width: 11px;
            left: 48px;
            margin-left: -11px;
        }
        .ol-attribution { display: none; }

        /* Building list sidebar */
        .building-list {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 16px;
        }
        .building-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.2s;
        }
        .building-item:hover {
            background-color: #FFCBCB;
            transform: translateX(5px);
        }
        .building-item.active {
            background-color: #1679AB;
            color: white;
        }

        /* Profile modal */
        .profile-modal-header {
            background: linear-gradient(135deg, #102C57, #1679AB);
            color: white;
        }
        .corporation-logo-large {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 12px;
            border: 3px solid #FFB1B1;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #FFCBCB; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #1679AB; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- SIDEBAR -->
            <div class="col-auto sidebar min-vh-100" id="sidebar">
                <div class="logo-area">
                    <div class="text-center">
                        {{-- Corporation Logo from assets --}}
                        @php
                            $logoUrl = asset('assets/corporation-logo/default-logo.png');
                            if(isset($corporation) && !empty($corporation->logo)) {
                                // Check if logo exists in storage
                                $fullLogoPath = storage_path('app/public/' . $corporation->logo);
                                if(file_exists($fullLogoPath)) {
                                    $logoUrl = asset('storage/' . $corporation->logo);
                                }
                            }
                        @endphp
                        <img src="{{ $logoUrl }}"
                             alt="{{ $corporation->name ?? 'Tamil Nadu Municipal Corporation' }}"
                             class="corporation-logo"
                             id="sidebarCorpLogo">
                        <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">{{ $corporation->name ?? 'TN Municipal Corp' }}</h6>
                        <small class="text-white-50">{{ $corporation->district ?? '' }} District | e-Governance Suite</small>
                    </div>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}"
                       href="{{ route('corporation.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('corporation.ward.details*') ? 'active' : '' }}"
                       href="{{ route('corporation.ward.details', ['ward_no' => 1]) }}">
                        <i class="fas fa-map-marker-alt"></i> Ward Details
                    </a>
                    <a class="nav-link" href="#" data-page="analysis" id="analysisNavLink">
                        <i class="fas fa-chart-line"></i> Analysis
                    </a>
                </nav>
                <div class="mt-auto p-3">
                    <hr class="bg-secondary" style="opacity:0.3;">
                    <form method="POST" action="{{ route('corporation.logout') }}" id="logoutForm">
                        @csrf
                        <a class="nav-link text-white-50" href="#" id="logoutBtn">
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
                                    @if(isset($corporation) && !empty($corporation->logo) && file_exists(storage_path('app/public/' . $corporation->logo)))
                                        <img src="{{ asset('storage/' . $corporation->logo) }}"
                                             class="corporation-profile-icon"
                                             alt="Profile">
                                    @else
                                        <i class="fas fa-building"></i>
                                    @endif
                                </div>
                                <div class="d-none d-md-block">
                                    <span class="fw-semibold" style="color:#102C57;">{{ $corporation->name ?? 'Admin User' }}</span>
                                    <small class="d-block text-muted">Municipal Corporation</small>
                                </div>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#corporationProfileModal">
                                    <i class="fas fa-building me-2"></i> Corporation Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#commissionerProfileModal">
                                    <i class="fas fa-user-tie me-2"></i> Commissioner Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Dynamic Content -->
                <div class="p-4">
                    @yield('content')

                    <!-- Analysis Panel (hidden by default) -->
                    <div id="analysisPanel" class="content-panel" style="display: none;">
                        <div class="animate__animated animate__fadeInUp">
                            <h3 class="fw-bold mb-4" style="color:#ffffff;"><i class="fas fa-chart-line me-2" style="color:#1679AB;"></i> Analytical Insights</h3>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">Monthly Tax Collection Trend</h5>
                                        <canvas id="taxChart" height="250"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">Ward-wise Grievance Resolution</h5>
                                        <canvas id="grievanceChart" height="250"></canvas>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">Corporation Budget Allocation</h5>
                                        <canvas id="budgetChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Corporation Profile Modal --}}
    <div class="modal fade" id="corporationProfileModal" tabindex="-1" aria-labelledby="corporationProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title" id="corporationProfileModalLabel">
                        <i class="fas fa-building me-2"></i> Corporation Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @php
                        $modalLogoUrl = asset('assets/corporation-logo/default-logo.png');
                        if(isset($corporation) && !empty($corporation->logo)) {
                            $fullLogoPath = storage_path('app/public/' . $corporation->logo);
                            if(file_exists($fullLogoPath)) {
                                $modalLogoUrl = asset('storage/' . $corporation->logo);
                            }
                        }
                    @endphp
                    <img src="{{ $modalLogoUrl }}"
                         alt="{{ $corporation->name ?? 'Corporation Logo' }}"
                         class="corporation-logo-large mb-3">

                    <div class="text-start mt-3">
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Corporation Name:</div>
                            <div class="col-7">{{ $corporation->name ?? 'Tamil Nadu Municipal Corporation' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Corporation Code:</div>
                            <div class="col-7">{{ $corporation->code ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">District:</div>
                            <div class="col-7">{{ $corporation->district ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">State:</div>
                            <div class="col-7">{{ $corporation->state ?? 'Tamil Nadu' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Status:</div>
                            <div class="col-7">
                                <span class="badge bg-success">{{ ucfirst($corporation->status ?? 'active') }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Total Wards:</div>
                            <div class="col-7">{{ $ward_count ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Email:</div>
                            <div class="col-7">{{ $corporation->email ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Phone:</div>
                            <div class="col-7">{{ $corporation->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Established:</div>
                            <div class="col-7">{{ $corporation->established_year ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Address:</div>
                            <div class="col-7">{{ $corporation->address ?? 'Ripon Building, Chennai, Tamil Nadu - 600003' }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Commissioner Profile Modal --}}
    <div class="modal fade" id="commissionerProfileModal" tabindex="-1" aria-labelledby="commissionerProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title" id="commissionerProfileModalLabel">
                        <i class="fas fa-user-tie me-2"></i> Commissioner Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 40px;">
                        <i class="fas fa-user-tie"></i>
                    </div>

                    <div class="text-start mt-3">
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Name:</div>
                            <div class="col-7">{{ $corporation->commissioner_name ?? 'Dr. K. Senthil Raj, IAS' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Designation:</div>
                            <div class="col-7">Municipal Commissioner</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Corporation:</div>
                            <div class="col-7">{{ $corporation->name ?? 'Tamil Nadu Municipal Corporation' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">District:</div>
                            <div class="col-7">{{ $corporation->district ?? 'Chennai' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Date of Joining:</div>
                            <div class="col-7">{{ $corporation->commissioner_joining_date ?? '01-06-2023' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Email:</div>
                            <div class="col-7">{{ $corporation->commissioner_email ?? 'commissioner@tnmunicipal.gov.in' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Phone:</div>
                            <div class="col-7">{{ $corporation->commissioner_phone ?? '9876543210' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold text-primary">Office Address:</div>
                            <div class="col-7">{{ $corporation->commissioner_office ?? 'Commissioner\'s Office, Main Building, Chennai - 600003' }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

        // Logout handling
        const logoutBtns = document.querySelectorAll('#logoutBtn, #logoutDropdown');
        logoutBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('logoutForm').submit();
            });
        });

        // Analysis Panel toggle
        const analysisNavLink = document.getElementById('analysisNavLink');
        const dashboardContent = document.querySelector('.dashboard-content-area, .ward-details-content');
        const analysisPanel = document.getElementById('analysisPanel');
        let chartsInitialized = false;

        if (analysisNavLink) {
            analysisNavLink.addEventListener('click', (e) => {
                e.preventDefault();
                if (dashboardContent) dashboardContent.style.display = 'none';
                if (analysisPanel) analysisPanel.style.display = 'block';

                // Update active state
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                analysisNavLink.classList.add('active');

                if (!chartsInitialized) {
                    initAnalysisCharts();
                    chartsInitialized = true;
                }
            });
        }

        function initAnalysisCharts() {
            const taxCtx = document.getElementById('taxChart')?.getContext('2d');
            if (taxCtx) {
                new Chart(taxCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Tax Collection (₹ Crores)',
                            data: [6.2, 7.1, 8.4, 9.2, 10.1, 11.5],
                            borderColor: '#1679AB',
                            backgroundColor: 'rgba(22, 121, 171, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: true }
                });
            }

            const grievanceCtx = document.getElementById('grievanceChart')?.getContext('2d');
            if (grievanceCtx) {
                new Chart(grievanceCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Ward 1-20', 'Ward 21-40', 'Ward 41-60', 'Ward 61-80', 'Ward 81-100'],
                        datasets: [{
                            label: 'Resolved Grievances (%)',
                            data: [92, 88, 94, 79, 96],
                            backgroundColor: '#FFB1B1',
                            borderRadius: 8,
                            borderColor: '#1679AB',
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, scales: { y: { max: 100 } } }
                });
            }

            const budgetCtx = document.getElementById('budgetChart')?.getContext('2d');
            if (budgetCtx) {
                new Chart(budgetCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Infrastructure', 'Sanitation', 'Water Supply', 'Street Lighting', 'Admin'],
                        datasets: [{
                            data: [38, 25, 18, 12, 7],
                            backgroundColor: ['#1679AB', '#102C57', '#FFB1B1', '#FFCBCB', '#5A6E7A'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });
            }
        }

        // Show dashboard by default if on dashboard route
        @if(request()->routeIs('corporation.dashboard'))
            if (analysisPanel) analysisPanel.style.display = 'none';
        @endif

        // Corporation logo error fallback
        document.querySelectorAll('.corporation-logo, .corporation-logo-large, .corporation-profile-icon').forEach(img => {
            img.addEventListener('error', function() {
                this.src = "{{ asset('assets/corporation-logo/default-logo.png') }}";
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
