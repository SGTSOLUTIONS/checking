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
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

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
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #E8F4F0 0%, #F0EDE5 100%);
        }

        /* Sidebar Styles */
        .sidebar {
            background: var(--sidebar-gradient);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.15);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
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
            border: 3px solid #D4A13E;
        }

        /* Main Content Wrapper - FIXED */
        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Main Content Area - FIXED */
        .main-content {
            padding: 20px 24px;
            min-height: calc(100vh - 70px);
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

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, rgba(212, 161, 62, 0.15), rgba(232, 106, 95, 0.15));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #D4A13E;
        }

        .table-dark {
            background: linear-gradient(135deg, #0B2B40, #1A6B6E);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1A6B6E, #0B2B40);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #D4A13E, #E86A5F);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .menu-toggle {
                display: block !important;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }

        /* Pagination */
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #1A6B6E, #0B2B40);
            border-color: #1A6B6E;
        }

        .pagination .page-link {
            color: #1A6B6E;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- SIDEBAR - Fixed position -->
    <div class="sidebar" id="sidebar">
        <div class="logo-area">
            <div class="text-center">
                @php
                    $logoUrl = asset('images/TamilNadu_Logo.png');
                    if(auth()->check()) {
                        if(!empty(auth()->user()->profile_picture)) {
                            $profilePath = ltrim(auth()->user()->profile_picture, '/');
                            if(file_exists(public_path($profilePath))) {
                                $logoUrl = asset($profilePath);
                            }
                        } elseif(isset($corporation) && !empty($corporation->logo)) {
                            $corpLogoPath = ltrim($corporation->logo, '/');
                            if(file_exists(public_path($corpLogoPath))) {
                                $logoUrl = asset($corpLogoPath);
                            }
                        }
                    }
                @endphp
                <img src="{{ $logoUrl }}" alt="Logo" class="corporation-logo">
                <h5 class="fw-bold mb-1 mt-2" style="color: #D4A13E;">
                    {{ auth()->user()->name ?? ($corporation->name ?? 'TN Municipal Corp') }}
                </h5>
                <small class="text-white-50">{{ ucfirst(auth()->user()->role ?? 'Commissioner') }}</small>
            </div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('corporation.dashboard') ? 'active' : '' }}"
               href="{{ route('corporation.dashboard') }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('corporation.analytics') ? 'active' : '' }}"
               href="{{ route('corporation.analytics') }}">
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

    <!-- MAIN WRAPPER - This is the key fix -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar-custom">
            <div>
                <button class="btn btn-outline-secondary menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown user-dropdown">
                    <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            @php
                                $profileUrl = asset('images/TamilNadu_Logo.png');
                                if(auth()->check() && !empty(auth()->user()->profile_picture)) {
                                    $profPath = ltrim(auth()->user()->profile_picture, '/');
                                    if(file_exists(public_path($profPath))) {
                                        $profileUrl = asset($profPath);
                                    }
                                }
                            @endphp
                            <img src="{{ $profileUrl }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                        </div>
                        <div class="d-none d-md-block">
                            <span class="fw-bold" style="color:#0B2B40;">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <small class="d-block text-muted">{{ ucfirst(auth()->user()->role ?? 'Commissioner') }}</small>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Dynamic Content - This is where your variations page will appear -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        const logoutBtns = document.querySelectorAll('#logoutBtn, #logoutDropdown');
        logoutBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('logoutForm').submit();
            });
        });

        document.querySelectorAll('.corporation-logo, .corporation-logo-large').forEach(img => {
            img.addEventListener('error', function() {
                this.src = "{{ asset('images/TamilNadu_Logo.png') }}";
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
