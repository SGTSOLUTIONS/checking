<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TN Municipal Corporation')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        :root{
            --primary:#0B2B40;
            --secondary:#1E7F6E;
            --accent:#F4A261;
            --light:#EEF4F3;
            --white:#ffffff;
            --text:#1f2937;
            --muted:#6b7280;

            --shadow-sm:0 4px 12px rgba(0,0,0,0.05);
            --shadow-md:0 12px 30px rgba(0,0,0,0.08);
        }

        body{
            font-family:'Inter',sans-serif;
            background:var(--light);
            overflow-x:hidden;
            color:var(--text);
        }

        a{
            text-decoration:none;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar{
            width:260px;
            min-height:100vh;
            background:linear-gradient(180deg,#0B2B40 0%,#123A54 100%);
            position:fixed;
            top:0;
            left:0;
            z-index:1000;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            box-shadow:4px 0 20px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .logo-area{
            padding:30px 20px;
            text-align:center;
            border-bottom:1px solid rgba(255,255,255,0.08);
        }

        .emblem-icon{
            width:70px;
            height:70px;
            border-radius:50%;
            background:#F4A261;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            color:#0B2B40;
            font-size:30px;
            margin-bottom:15px;
        }

        .logo-area h4{
            color:#fff;
            font-weight:700;
            margin-bottom:5px;
        }

        .logo-area small{
            color:#cdd8df;
        }

        .sidebar-menu{
            padding:20px 12px;
        }

        .sidebar .nav-link{
            color:#d8e3e8;
            padding:14px 18px;
            border-radius:14px;
            margin-bottom:10px;
            font-weight:500;
            transition:0.3s;
            display:flex;
            align-items:center;
            gap:12px;
        }

        .sidebar .nav-link i{
            width:22px;
        }

        .sidebar .nav-link:hover{
            background:rgba(255,255,255,0.08);
            color:#fff;
            transform:translateX(4px);
        }

        .sidebar .nav-link.active{
            background:linear-gradient(90deg,#1E7F6E,#14695d);
            color:#fff;
            box-shadow:0 8px 20px rgba(30,127,110,0.25);
        }

        .logout-area{
            padding:20px 12px;
            border-top:1px solid rgba(255,255,255,0.08);
        }

        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .main-wrapper{
            margin-left:260px;
            min-height:100vh;
            padding:20px;
            transition:0.3s;
        }

        /* =====================================================
           NAVBAR
        ===================================================== */

        .top-navbar{
            background:#fff;
            border-radius:22px;
            padding:14px 24px;
            box-shadow:var(--shadow-sm);
            margin-bottom:25px;
        }

        .menu-toggle{
            border:none;
            background:#f3f4f6;
            width:42px;
            height:42px;
            border-radius:12px;
        }

        .location-badge{
            background:#f5f7f9;
            padding:10px 16px;
            border-radius:50px;
            font-weight:600;
            font-size:14px;
        }

        .user-avatar{
            width:46px;
            height:46px;
            border-radius:50%;
            background:linear-gradient(135deg,#1E7F6E,#0B2B40);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-size:18px;
        }

        /* =====================================================
           CARD
        ===================================================== */

        .dashboard-card{
            background:#fff;
            border-radius:24px;
            padding:24px;
            box-shadow:var(--shadow-sm);
            transition:0.3s;
            border:none;
        }

        .dashboard-card:hover{
            transform:translateY(-5px);
            box-shadow:var(--shadow-md);
        }

        .card-icon{
            width:60px;
            height:60px;
            border-radius:18px;
            background:#EEF7F5;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
            color:#1E7F6E;
        }

        /* =====================================================
           TABLE
        ===================================================== */

        .table-card{
            background:#fff;
            border-radius:24px;
            overflow:hidden;
            box-shadow:var(--shadow-sm);
        }

        .table-modern{
            margin-bottom:0;
        }

        .table-modern thead{
            background:#F8FAFA;
        }

        .table-modern thead th{
            padding:18px 16px;
            border:none;
            color:#0B2B40;
            font-weight:700;
        }

        .table-modern tbody td{
            padding:18px 16px;
            border-top:1px solid #edf2f7;
            vertical-align:middle;
        }

        .table-modern tbody tr:hover{
            background:#fafdfd;
        }

        /* =====================================================
           BUTTON
        ===================================================== */

        .btn-soft-primary{
            background:#EAF5F2;
            color:#1E7F6E;
            border:none;
            border-radius:40px;
            padding:8px 18px;
            font-weight:600;
            transition:0.3s;
        }

        .btn-soft-primary:hover{
            background:#1E7F6E;
            color:#fff;
        }

        /* =====================================================
           PANEL ANIMATION
        ===================================================== */

        .content-panel{
            animation:fadeSlide .4s ease;
        }

        @keyframes fadeSlide{
            from{
                opacity:0;
                transform:translateY(15px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        /* =====================================================
           MOBILE
        ===================================================== */

        @media(max-width:991px){

            .sidebar{
                left:-270px;
            }

            .sidebar.show{
                left:0;
            }

            .main-wrapper{
                margin-left:0;
            }

            .menu-toggle{
                display:block;
            }
        }

        @media(min-width:992px){

            .menu-toggle{
                display:none;
            }
        }

        @media(max-width:768px){

            .main-wrapper{
                padding:15px;
            }

            .top-navbar{
                padding:14px 18px;
            }

            .dashboard-card{
                padding:18px;
            }

            .table-responsive{
                overflow-x:auto;
            }

            .table-modern{
                min-width:700px;
            }
        }

    </style>
</head>

<body>

<div class="sidebar" id="sidebar">

    <!-- Logo -->
    <div>

        <div class="logo-area">
            <div class="emblem-icon">
                <i class="fas fa-city"></i>
            </div>

            <h4>TN Municipal Corp</h4>
            <small>Commissioner Console</small>
        </div>

        <!-- Menu -->
        <div class="sidebar-menu">

            <nav class="nav flex-column">

                <a href="#" class="nav-link active" data-page="dashboard">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>

                <a href="#" class="nav-link" data-page="wards">
                    <i class="fas fa-map"></i>
                    Wards & Zones
                </a>

                <a href="#" class="nav-link" data-page="analysis">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>

                <a href="#" class="nav-link" data-page="reports">
                    <i class="fas fa-file-alt"></i>
                    Reports
                </a>

            </nav>

        </div>

    </div>

    <!-- Logout -->
    <div class="logout-area">

        <a href="#"
            class="nav-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

            <i class="fas fa-sign-out-alt"></i>
            Logout

        </a>

        <form id="logout-form"
            action="{{ route('corporation.logout') }}"
            method="POST"
            class="d-none">

            @csrf

        </form>

    </div>

</div>

<!-- =====================================================
     MAIN
===================================================== -->

<div class="main-wrapper">

    <!-- NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center gap-3">

            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="location-badge d-none d-md-block">
                <i class="fas fa-map-marker-alt text-success me-1"></i>

                {{ $corporation->name ?? 'Greater Chennai Corporation' }}
            </div>

        </div>

        <!-- USER -->
        <div class="dropdown">

            <div class="d-flex align-items-center gap-3"
                data-bs-toggle="dropdown"
                style="cursor:pointer;">

                <div class="user-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>

                <div class="d-none d-sm-block">

                    <div class="fw-bold">
                        {{ Auth::guard('corporation')->user()->name ?? 'Commissioner' }}
                    </div>

                    <small class="text-muted">
                        Commissioner
                    </small>

                </div>

                <i class="fas fa-chevron-down text-muted"></i>

            </div>

            <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 mt-3">

                <li>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user me-2"></i>
                        Profile
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a href="#"
                        class="dropdown-item text-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

    <!-- PAGE CONTENT -->
    @yield('content-panels')

</div>

<!-- =====================================================
     JS
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');

    if(menuToggle){
        menuToggle.addEventListener('click', function(){
            sidebar.classList.toggle('show');
        });
    }

    const panels = {
        dashboard: document.getElementById('dashboardPanel'),
        wards: document.getElementById('wardsPanel'),
        analysis: document.getElementById('analysisPanel'),
        reports: document.getElementById('reportsPanel')
    };

    function showPanel(panel){

        Object.keys(panels).forEach(function(key){

            if(panels[key]){
                panels[key].style.display = 'none';
            }

        });

        if(panels[panel]){
            panels[panel].style.display = 'block';
        }

        document.querySelectorAll('.sidebar .nav-link').forEach(function(link){

            link.classList.remove('active');

            if(link.dataset.page === panel){
                link.classList.add('active');
            }

        });

        if(window.innerWidth < 992){
            sidebar.classList.remove('show');
        }
    }

    document.querySelectorAll('.sidebar .nav-link').forEach(function(link){

        link.addEventListener('click', function(e){

            const page = this.dataset.page;

            if(page){

                e.preventDefault();

                showPanel(page);
            }

        });

    });

    showPanel('dashboard');

</script>

@stack('scripts')

</body>
</html>
