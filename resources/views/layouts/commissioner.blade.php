{{-- resources/views/layouts/commissioner.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($corporation) ? $corporation->name : 'Tamil Nadu Municipal Corporation') . ' | Admin Dashboard'</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
   <!-- OpenLayers JS -->    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-dark: #0B2B40;
            --primary-teal: #1A6B6E;
            --accent-gold: #D4A13E;
            --accent-coral: #E86A5F;
            --bg-cream: #FDF8F0;
            --text-dark: #1A2E35;
            --text-light: #6B7A7F;
            --card-white: #FFFFFF;
            --sidebar-gradient: linear-gradient(135deg, #0B2B40 0%, #1A6B6E 100%);
            --card-shadow: 0 10px 30px rgba(11, 43, 64, 0.08);
            --hover-shadow: 0 15px 35px rgba(26, 107, 110, 0.12);
        }

        body {
            /* background: linear-gradient(135deg, #E8F4F0 0%, #F0EDE5 100%); */
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Elements */
        body::before {
            content: "";
            position: fixed;
            width: 500px;
            height: 500px;
            /* background: radial-gradient(circle, rgba(212, 161, 62, 0.08) 0%, transparent 70%); */
            top: -250px;
            right: -150px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob 20s infinite alternate ease-in-out;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(232, 106, 95, 0.06) 0%, transparent 70%);
            bottom: -300px;
            left: -200px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob2 25s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            100% { transform: translate(60px, 40px) scale(1.2); opacity: 0.7; }
        }

        @keyframes floatBlob2 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            100% { transform: translate(-50px, -60px) scale(1.3); opacity: 0.6; }
        }

        /* Sidebar Styles */
        .sidebar {
            background: var(--sidebar-gradient);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: rgba(212, 161, 62, 0.15);
            transition: width 0.3s ease;
            z-index: -1;
        }

        .sidebar .nav-link:hover::before {
            width: 100%;
        }

        .sidebar .nav-link:hover {
            color: white;
            transform: translateX(5px);
            background: rgba(212, 161, 62, 0.1);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
            color: white;
            box-shadow: 0 4px 15px rgba(212, 161, 62, 0.3);
        }

        .sidebar .nav-link i {
            width: 28px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar .logo-area {
            padding: 25px 16px;
            border-bottom: 1px solid rgba(212, 161, 62, 0.3);
            margin-bottom: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
        }

        .corporation-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 10px;
            margin-bottom: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 3px solid #D4A13E;
        }

        .corporation-logo:hover {
            transform: scale(1.05) rotate(5deg);
            border-color: #E86A5F;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            border-radius: 0;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-dropdown {
            cursor: pointer;
            transition: all 0.2s;
            padding: 8px 15px;
            border-radius: 50px;
            background: linear-gradient(135deg, rgba(212, 161, 62, 0.1), rgba(232, 106, 95, 0.1));
        }

        .user-dropdown:hover {
            transform: scale(1.02);
            background: linear-gradient(135deg, rgba(212, 161, 62, 0.2), rgba(232, 106, 95, 0.2));
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .corporation-profile-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #D4A13E;
        }

        /* Main Content */
        .main-content {
            transition: all 0.3s ease;
        }

        /* Card Styles */
        .stat-card {
            background: var(--card-white);
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #D4A13E, #E86A5F, #1A6B6E);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(212, 161, 62, 0.15), rgba(232, 106, 95, 0.15));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #D4A13E;
        }

        /* Content Panel */
        .content-panel {
            animation: fadeSlideUp 0.5s ease;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Table Styles */
        .table-custom {
            background: white;
            border-radius: 20px;
            overflow: hidden;
        }

        .table-custom th {
            background: linear-gradient(135deg, #0B2B40, #1A6B6E);
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

        /* Badges */
        .badge.bg-primary { background: linear-gradient(135deg, #1A6B6E, #0B2B40) !important; }
        .badge.bg-success { background: linear-gradient(135deg, #28a745, #20c997) !important; }
        .badge.bg-warning { background: linear-gradient(135deg, #ffc107, #fd7e14) !important; }
        .badge.bg-info { background: linear-gradient(135deg, #17a2b8, #4cc9f0) !important; }

        /* Buttons */
        .btn-outline-secondary {
            border: 1px solid #D4A13E;
            color: #D4A13E;
            transition: all 0.3s;
        }
        .btn-outline-secondary:hover {
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1A6B6E, #0B2B40);
            border: none;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 161, 62, 0.3);
        }

        /* Dropdown */
        .dropdown-item:active {
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
        }
        .dropdown-item:hover {
            background: rgba(212, 161, 62, 0.1);
        }

        /* Profile Modal */
        .profile-modal-header {
            background: linear-gradient(135deg, #0B2B40, #1A6B6E, #D4A13E);
            color: white;
        }

        .corporation-logo-large {
            width: 130px;
            height: 130px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 15px;
            border: 4px solid #D4A13E;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
        }

        .corporation-logo-large:hover {
            transform: scale(1.05);
            border-color: #E86A5F;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F0EDE5; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #D4A13E, #E86A5F); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1A6B6E; }

        /* Flash message */
        .alert {
            z-index: 10000;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .alert-success { background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; }
        .alert-danger { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
        .alert-info { background: linear-gradient(135deg, #d1ecf1, #bee5eb); color: #0c5460; }

        /* =========================================================
   MODERN PAGINATION UI
========================================================= */

.custom-pagination-wrapper {

    display: flex;

    justify-content: space-between;

    align-items: center;

    flex-wrap: wrap;

    gap: 20px;

    margin-top: 30px;

    padding: 20px;

    background: #ffffff;

    border-radius: 20px;

    box-shadow:
        0 10px 30px rgba(0,0,0,0.08);

}

/* =========================================================
   INFO TEXT
========================================================= */

.pagination-info {

    font-size: 16px;

    font-weight: 600;

    color: #102C57;

}

.pagination-info strong {

    color: #1679AB;

}

/* =========================================================
   PAGINATION CONTAINER
========================================================= */

.custom-pagination {

    display: flex;

    align-items: center;

    gap: 10px;

    flex-wrap: wrap;

}

/* =========================================================
   BUTTONS
========================================================= */

.page-btn {

    min-width: 45px;

    height: 45px;

    padding: 0 14px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 14px;

    background: #ffffff;

    color: #102C57;

    border: 1px solid #d9e2ef;

    text-decoration: none;

    font-size: 15px;

    font-weight: 700;

    transition: all 0.25s ease;

    box-shadow:
        0 4px 10px rgba(0,0,0,0.05);

}

/* =========================================================
   HOVER
========================================================= */

.page-btn:hover {

    background:
        linear-gradient(
            135deg,
            #1679AB 0%,
            #0F4C75 100%
        );

    color: #ffffff;

    transform: translateY(-2px);

    border-color: transparent;

}

/* =========================================================
   ACTIVE
========================================================= */

.page-btn.active {

    background:
        linear-gradient(
            135deg,
            #102C57 0%,
            #1679AB 100%
        );

    color: #ffffff;

    border: none;

    box-shadow:
        0 6px 15px rgba(22,121,171,0.35);

}

/* =========================================================
   DISABLED
========================================================= */

.page-btn.disabled {

    opacity: 0.45;

    pointer-events: none;

    cursor: not-allowed;

}

/* =========================================================
   MOBILE
========================================================= */

@media(max-width: 768px) {

    .custom-pagination-wrapper {

        flex-direction: column;

        align-items: flex-start;

    }

    .custom-pagination {

        width: 100%;

        justify-content: center;

    }

    .page-btn {

        min-width: 40px;

        height: 40px;

        font-size: 14px;

    }

}

/* =========================================================
   OPTIONAL SCROLLBAR
========================================================= */

.custom-pagination::-webkit-scrollbar {

    height: 6px;

}

.custom-pagination::-webkit-scrollbar-thumb {

    background: #1679AB;

    border-radius: 10px;

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
                            // 1. Auth user's profile image (public path)
                            // 2. Auth user's logo
                            // 3. Corporation logo
                            // 4. Default Tamil Nadu logo
                            $logoUrl = asset('images/TamilNadu_Logo.png');

                            if(auth()->check()) {
                                // Check for profile picture
                                if(!empty(auth()->user()->profile_picture)) {
                                    $profilePath = ltrim(auth()->user()->profile_picture, '/');
                                    $fullProfilePath = public_path($profilePath);
                                    if(file_exists($fullProfilePath)) {
                                        $logoUrl = asset($profilePath);
                                    }
                                }
                                // Check for profile field
                                elseif(!empty(auth()->user()->profile)) {
                                    $profilePath = ltrim(auth()->user()->profile, '/');
                                    $fullProfilePath = public_path($profilePath);
                                    if(file_exists($fullProfilePath)) {
                                        $logoUrl = asset($profilePath);
                                    }
                                }
                                // Check for logo field
                                elseif(!empty(auth()->user()->logo)) {
                                    $logoPath = ltrim(auth()->user()->logo, '/');
                                    $fullLogoPath = public_path($logoPath);
                                    if(file_exists($fullLogoPath)) {
                                        $logoUrl = asset($logoPath);
                                    }
                                }
                                // Check for avatar
                                elseif(!empty(auth()->user()->avatar)) {
                                    $avatarPath = ltrim(auth()->user()->avatar, '/');
                                    $fullAvatarPath = public_path($avatarPath);
                                    if(file_exists($fullAvatarPath)) {
                                        $logoUrl = asset($avatarPath);
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
                        <h5 class="fw-bold mb-1 mt-2" style="color: #D4A13E;">
                            {{ auth()->user()->name ?? ($corporation->name ?? 'TN Municipal Corp') }}
                        </h5>
                        <small class="text-white-50">
                            <i class="fas fa-badge-check me-1"></i>
                            {{ ucfirst(auth()->user()->role ?? 'Commissioner') }} | e-Governance Suite
                        </small>
                    </div>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}"
                       href="{{ route('corporation.dashboard') }}">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>
                    <a class="nav-link" {{ request()->routeIs('corporation.analystics') ? 'active' : '' }}"
                       href="{{ route('corporation.analystics') }}">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a>
                </nav>
                <div class="mt-auto p-3">
                    <hr class="bg-secondary" style="opacity:0.2;">
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
                                <circle cx="50" cy="50" r="45" fill="url(#gradient)" />
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#D4A13E" />
                                        <stop offset="100%" style="stop-color:#E86A5F" />
                                    </linearGradient>
                                </defs>
                                <text x="50" y="65" font-size="20" text-anchor="middle" fill="#0B2B40" font-weight="bold">TN</text>
                            </svg>
                        </div>
                        <div class="dropdown user-dropdown">
                            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    @php
                                        $profileUrl = asset('images/TamilNadu_Logo.png');
                                        if(auth()->check()) {
                                            if(!empty(auth()->user()->profile_picture)) {
                                                $profPath = ltrim(auth()->user()->profile_picture, '/');
                                                if(file_exists(public_path($profPath))) {
                                                    $profileUrl = asset($profPath);
                                                }
                                            } elseif(!empty(auth()->user()->profile)) {
                                                $profPath = ltrim(auth()->user()->profile, '/');
                                                if(file_exists(public_path($profPath))) {
                                                    $profileUrl = asset($profPath);
                                                }
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $profileUrl }}"
                                         class="corporation-profile-icon"
                                         alt="{{ auth()->user()->name ?? 'Profile' }}">
                                </div>
                                <div class="d-none d-md-block">
                                    <span class="fw-bold" style="color:#0B2B40;">
                                        {{ auth()->user()->name ?? 'Admin User' }}
                                    </span>
                                    <small class="d-block text-muted">
                                        <i class="fas fa-user-tie me-1"></i>
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
                                <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Dynamic Content -->
                <div class="p-4">
                    @yield('content')

                    <!-- Analysis Panel -->
                    <div id="analysisPanel" class="content-panel" style="display: none;">
                        <div class="animate__animated animate__fadeInUp">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h3 class="fw-bold" style="color:#0B2B40;">
                                        <i class="fas fa-chart-line me-2" style="color:#D4A13E;"></i>
                                        Analytical Insights
                                    </h3>
                                    <p class="text-muted">Real-time analytics and performance metrics</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">
                                            <i class="fas fa-chart-line me-2" style="color:#D4A13E;"></i>
                                            Monthly Tax Collection Trend
                                        </h5>
                                        <canvas id="taxChart" height="250"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">
                                            <i class="fas fa-chart-bar me-2" style="color:#D4A13E;"></i>
                                            Ward-wise Grievance Resolution
                                        </h5>
                                        <canvas id="grievanceChart" height="250"></canvas>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="stat-card p-4">
                                        <h5 class="fw-bold mb-3">
                                            <i class="fas fa-chart-pie me-2" style="color:#D4A13E;"></i>
                                            Corporation Budget Allocation
                                        </h5>
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

    {{-- User Profile Modal --}}
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">
                        <i class="fas fa-user-circle me-2"></i> My Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        @php
                            $modalProfilePic = asset('images/TamilNadu_Logo.png');
                            if(auth()->check()) {
                                if(!empty(auth()->user()->profile_picture)) {
                                    $profPath = ltrim(auth()->user()->profile_picture, '/');
                                    if(file_exists(public_path($profPath))) {
                                        $modalProfilePic = asset($profPath);
                                    }
                                } elseif(!empty(auth()->user()->profile)) {
                                    $profPath = ltrim(auth()->user()->profile, '/');
                                    if(file_exists(public_path($profPath))) {
                                        $modalProfilePic = asset($profPath);
                                    }
                                }
                            }
                        @endphp
                        <img src="{{ $modalProfilePic }}"
                             alt="{{ auth()->user()->name ?? 'User Profile' }}"
                             class="corporation-logo-large mb-3">
                        <h3 class="fw-bold mb-1" style="color:#0B2B40;">{{ auth()->user()->name ?? 'User Name' }}</h3>
                        <p class="text-muted">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="fas fa-user-tie me-1"></i>
                                {{ ucfirst(auth()->user()->role ?? 'Commissioner') }}
                            </span>
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-user me-1"></i> Full Name:
                                </label>
                                <p class="mb-0 fs-5">{{ auth()->user()->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-envelope me-1"></i> Email Address:
                                </label>
                                <p class="mb-0 fs-5">{{ auth()->user()->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-phone me-1"></i> Phone Number:
                                </label>
                                <p class="mb-0 fs-5">{{ auth()->user()->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-building me-1"></i> Department:
                                </label>
                                <p class="mb-0 fs-5">{{ auth()->user()->department ?? 'Municipal Administration' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-calendar me-1"></i> Member Since:
                                </label>
                                <p class="mb-0 fs-5">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d-m-Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary mb-2">
                                    <i class="fas fa-id-card me-1"></i> Role:
                                </label>
                                <p class="mb-0 fs-5">
                                    <span class="badge bg-warning">{{ ucfirst(auth()->user()->role ?? 'Commissioner') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Profile Info:</strong> Your profile picture syncs across the entire platform.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Corporation Profile Modal --}}
    @if(isset($corporation))
    <div class="modal fade" id="corporationProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i> Corporation Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        @php
                            $corpLogo = asset('images/TamilNadu_Logo.png');
                            if(!empty($corporation->logo)) {
                                $logoPath = ltrim($corporation->logo, '/');
                                if(file_exists(public_path($logoPath))) {
                                    $corpLogo = asset($logoPath);
                                }
                            }
                        @endphp
                        <img src="{{ $corpLogo }}"
                             alt="{{ $corporation->name ?? 'Corporation Logo' }}"
                             class="corporation-logo-large mb-3">
                        <h3 class="fw-bold" style="color:#0B2B40;">{{ $corporation->name ?? 'Tamil Nadu Municipal Corporation' }}</h3>
                        <p class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $corporation->district ?? '' }} District, {{ $corporation->state ?? 'Tamil Nadu' }}
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-code me-1"></i> Corporation Code:
                                </label>
                                <p class="mb-0 mt-1">{{ $corporation->code ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-globe me-1"></i> District:
                                </label>
                                <p class="mb-0 mt-1">{{ ucfirst($corporation->district ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-envelope me-1"></i> Email:
                                </label>
                                <p class="mb-0 mt-1">{{ $corporation->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-phone me-1"></i> Phone:
                                </label>
                                <p class="mb-0 mt-1">{{ $corporation->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-calendar-alt me-1"></i> Established:
                                </label>
                                <p class="mb-0 mt-1">{{ $corporation->established_year ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-primary">
                                    <i class="fas fa-chart-line me-1"></i> Status:
                                </label>
                                <p class="mb-0 mt-1">
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ ucfirst($corporation->status ?? 'Active') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
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
                            borderColor: '#D4A13E',
                            backgroundColor: 'rgba(212, 161, 62, 0.1)',
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
                            backgroundColor: '#E86A5F',
                            borderRadius: 8
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
                            backgroundColor: ['#1A6B6E', '#D4A13E', '#E86A5F', '#0B2B40', '#6B7A7F'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });
            }
        }

        @if(request()->routeIs('corporation.dashboard'))
            if (analysisPanel) analysisPanel.style.display = 'none';
        @endif

        // Logo error fallback
        document.querySelectorAll('.corporation-logo, .corporation-logo-large, .corporation-profile-icon').forEach(img => {
            img.addEventListener('error', function() {
                this.src = "{{ asset('images/TamilNadu_Logo.png') }}";
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
