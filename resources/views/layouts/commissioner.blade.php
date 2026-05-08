{{-- resources/views/layouts/commissioner.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($corporation) ? $corporation->name : 'Tamil Nadu Municipal Corporation') . ' | Admin Dashboard'</title>

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
            overflow: hidden;
        }

        .corporation-profile-icon {
            width: 42px;
            height: 42px;
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
        #map {
            height: 75vh;
            border-radius: 20px;
            z-index: 1;
            width: 100%;
        }

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

        /* Flash message */
        .alert {
            z-index: 10000;
        }
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
                        @php
                            // Auth user logo logic - priority order:
                            // 1. Auth user's profile image (if stored in public path)
                            // 2. Auth user's custom logo
                            // 3. Corporation logo from corporation model
                            // 4. Default Tamil Nadu logo
                            $logoUrl = asset('images/TamilNadu_Logo.png');

                            if(auth()->check()) {
                                // Check for auth user's profile image (public path)
                                if(!empty(auth()->user()->profile)) {
                                    // Check if profile path starts with 'uploads/' or similar
                                    $profilePath = auth()->user()->profile;
                                    // Remove leading slash if exists
                                    $profilePath = ltrim($profilePath, '/');
                                    $fullProfilePath = public_path($profilePath);
                                    if(file_exists($fullProfilePath)) {
                                        $logoUrl = asset($profilePath);
                                    }
                                }
                                // Fallback to auth user's logo
                                elseif(!empty(auth()->user()->logo)) {
                                    $logoPath = ltrim(auth()->user()->logo, '/');
                                    $fullLogoPath = public_path($logoPath);
                                    if(file_exists($fullLogoPath)) {
                                        $logoUrl = asset($logoPath);
                                    }
                                }
                                // Fallback to corporation logo
                                elseif(isset($corporation) && !empty($corporation->logo)) {
                                    $corpLogoPath = ltrim($corporation->logo, '/');
                                    $fullCorpLogoPath = public_path($corpLogoPath);
                                    if(file_exists($fullCorpLogoPath)) {
                                        $logoUrl = asset($corpLogoPath);
                                    }
                                }
                            } elseif(isset($corporation) && !empty($corporation->logo)) {
                                $corpLogoPath = ltrim($corporation->logo, '/');
                                $fullCorpLogoPath = public_path($corpLogoPath);
                                if(file_exists($fullCorpLogoPath)) {
                                    $logoUrl = asset($corpLogoPath);
                                }
                            }
                        @endphp
                        <img src="{{ $logoUrl }}"
                             alt="{{ auth()->user()->name ?? ($corporation->name ?? 'Tamil Nadu Municipal Corporation') }}"
                             class="corporation-logo"
                             id="sidebarCorpLogo">
                        <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">
                            {{ auth()->user()->name ?? ($corporation->name ?? 'TN Municipal Corp') }}
                        </h6>
                        <small class="text-white-50">
                            {{ auth()->user()->role ?? ($corporation->district ?? '') }} {{ auth()->user()->role ? '|' : '' }} e-Governance Suite
                        </small>
                    </div>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}"
                       href="{{ route('corporation.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
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
                                    @php
                                        // Auth user profile picture logic (public path)
                                        $profileLogoUrl = asset('images/TamilNadu_Logo.png');

                                        if(auth()->check()) {
                                            // Check for auth user's profile image (public path)
                                            if(!empty(auth()->user()->profile)) {
                                                $profilePath = ltrim(auth()->user()->profile, '/');
                                                $fullProfilePath = public_path($profilePath);
                                                if(file_exists($fullProfilePath)) {
                                                    $profileLogoUrl = asset($profilePath);
                                                }
                                            }
                                            // Fallback to auth user's logo
                                            elseif(!empty(auth()->user()->logo)) {
                                                $logoPath = ltrim(auth()->user()->logo, '/');
                                                $fullLogoPath = public_path($logoPath);
                                                if(file_exists($fullLogoPath)) {
                                                    $profileLogoUrl = asset($logoPath);
                                                }
                                            }
                                            // Fallback to corporation logo
                                            elseif(isset($corporation) && !empty($corporation->logo)) {
                                                $corpLogoPath = ltrim($corporation->logo, '/');
                                                $fullCorpLogoPath = public_path($corpLogoPath);
                                                if(file_exists($fullCorpLogoPath)) {
                                                    $profileLogoUrl = asset($corpLogoPath);
                                                }
                                            }
                                        } elseif(isset($corporation) && !empty($corporation->logo)) {
                                            $corpLogoPath = ltrim($corporation->logo, '/');
                                            $fullCorpLogoPath = public_path($corpLogoPath);
                                            if(file_exists($fullCorpLogoPath)) {
                                                $profileLogoUrl = asset($corpLogoPath);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $profileLogoUrl }}"
                                         class="corporation-profile-icon"
                                         alt="{{ auth()->user()->name ?? ($corporation->name ?? 'Profile') }}">
                                </div>
                                <div class="d-none d-md-block">
                                    <span class="fw-semibold" style="color:#102C57;">
                                        {{ auth()->user()->name ?? ($corporation->commissioner_name ?? 'Admin User') }}
                                    </span>
                                    <small class="d-block text-muted">
                                        {{ ucfirst(auth()->user()->role ?? 'Commissioner') }}
                                    </small>
                                </div>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#userProfileModal">
                                    <i class="fas fa-user-circle me-2"></i> My Profile
                                </a></li>
                                @if(isset($corporation))
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#corporationProfileModal">
                                    <i class="fas fa-building me-2"></i> Corporation Profile
                                </a></li>
                                @endif
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

    {{-- User Profile Modal (Auth User) --}}
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">
                        <i class="fas fa-user-circle me-2"></i> My Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        @php
                            $userProfilePic = asset('images/TamilNadu_Logo.png');
                            if(auth()->check()) {
                                // Check for profile image in public path
                                if(!empty(auth()->user()->profile)) {
                                    $profilePath = ltrim(auth()->user()->profile, '/');
                                    $fullProfilePath = public_path($profilePath);
                                    if(file_exists($fullProfilePath)) {
                                        $userProfilePic = asset($profilePath);
                                    }
                                }
                                // Fallback to logo
                                elseif(!empty(auth()->user()->logo)) {
                                    $logoPath = ltrim(auth()->user()->logo, '/');
                                    $fullLogoPath = public_path($logoPath);
                                    if(file_exists($fullLogoPath)) {
                                        $userProfilePic = asset($logoPath);
                                    }
                                }
                            }
                        @endphp
                        <img src="{{ $userProfilePic }}"
                             alt="{{ auth()->user()->name ?? 'User Profile' }}"
                             class="corporation-logo-large mb-3">
                        <h4 class="fw-bold">{{ auth()->user()->name ?? 'User Name' }}</h4>
                        <p class="text-muted">
                            <span class="badge bg-primary">{{ ucfirst(auth()->user()->role ?? 'Commissioner') }}</span>
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Full Name:</label>
                                <p class="mb-0">{{ auth()->user()->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Email Address:</label>
                                <p class="mb-0">{{ auth()->user()->email ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Phone Number:</label>
                                <p class="mb-0">{{ auth()->user()->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Role:</label>
                                <p class="mb-0">{{ ucfirst(auth()->user()->role ?? 'Commissioner') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Department:</label>
                                <p class="mb-0">{{ auth()->user()->department ?? 'Municipal Administration' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Profile Image Path:</label>
                                <p class="mb-0 text-muted small">{{ auth()->user()->profile ?? 'Not set' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Member Since:</label>
                                <p class="mb-0">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d-m-Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Profile image is stored in: <strong>public/{{ auth()->user()->profile ?? 'Not set' }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Corporation Profile Modal --}}
    @if(isset($corporation))
    <div class="modal fade" id="corporationProfileModal" tabindex="-1" aria-labelledby="corporationProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title" id="corporationProfileModalLabel">
                        <i class="fas fa-building me-2"></i> Corporation Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        @php
                            $modalLogoUrl = asset('images/TamilNadu_Logo.png');
                            if(isset($corporation) && !empty($corporation->logo)) {
                                $logoPath = ltrim($corporation->logo, '/');
                                $fullLogoPath = public_path($logoPath);
                                if(file_exists($fullLogoPath)) {
                                    $modalLogoUrl = asset($logoPath);
                                }
                            }
                        @endphp
                        <img src="{{ $modalLogoUrl }}"
                             alt="{{ $corporation->name ?? 'Corporation Logo' }}"
                             class="corporation-logo-large mb-3">
                        <h4 class="fw-bold">{{ $corporation->name ?? 'Tamil Nadu Municipal Corporation' }}</h4>
                        <p class="text-muted">{{ $corporation->district ?? '' }} District, {{ $corporation->state ?? 'Tamil Nadu' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Corporation Code:</label>
                                <p class="mb-0">{{ $corporation->code ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">District:</label>
                                <p class="mb-0">{{ ucfirst($corporation->district ?? 'N/A') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">State:</label>
                                <p class="mb-0">{{ ucfirst($corporation->state ?? 'Tamil Nadu') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Status:</label>
                                <p class="mb-0">
                                    <span class="badge bg-success">{{ ucfirst($corporation->status ?? 'active') }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Email:</label>
                                <p class="mb-0">{{ $corporation->email ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Phone:</label>
                                <p class="mb-0">{{ $corporation->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Established:</label>
                                <p class="mb-0">{{ $corporation->established_year ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-primary">Created At:</label>
                                <p class="mb-0">{{ isset($corporation->created_at) ? $corporation->created_at->format('d-m-Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
        const dashboardContent = document.querySelector('.dashboard-content-area');
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

        // Logo error fallback to default Tamil Nadu logo
        document.querySelectorAll('.corporation-logo, .corporation-logo-large, .corporation-profile-icon').forEach(img => {
            img.addEventListener('error', function() {
                this.src = "{{ asset('images/TamilNadu_Logo.png') }}";
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
