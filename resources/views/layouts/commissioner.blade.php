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
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
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
            --bg:#EEF4F3;
            --white:#ffffff;
            --text:#1f2937;
            --muted:#6b7280;

            --shadow-sm:0 4px 15px rgba(0,0,0,0.05);
            --shadow-md:0 10px 30px rgba(0,0,0,0.08);
        }

        body{
            font-family:'Inter',sans-serif;
            background:var(--bg);
            overflow-x:hidden;
            color:var(--text);
        }

        a{
            text-decoration:none;
        }

        /* =====================================================
           LAYOUT
        ===================================================== */

        .layout-wrapper{
            display:flex;
            min-height:100vh;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar{
            width:260px;
            background:linear-gradient(180deg,#0B2B40 0%,#10344d 100%);
            position:fixed;
            left:0;
            top:0;
            bottom:0;
            z-index:1000;
            display:flex;
            flex-direction:column;
            box-shadow:5px 0 20px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .logo-area{
            padding:40px 20px 30px;
            text-align:center;
            border-bottom:1px solid rgba(255,255,255,0.08);
        }

        .emblem-icon{
            width:70px;
            height:70px;
            border-radius:50%;
            background:#F4A261;
            color:#0B2B40;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            font-size:30px;
            margin-bottom:18px;
        }

        .logo-area h3{
            color:#fff;
            font-weight:700;
            margin-bottom:5px;
        }

        .logo-area small{
            color:#d1d5db;
        }

        .sidebar-menu{
            padding:20px 12px;
            flex:1;
        }

        .sidebar .nav-link{
            color:#d8e3e8;
            padding:15px 18px;
            border-radius:16px;
            margin-bottom:10px;
            font-weight:600;
            transition:0.3s;
            display:flex;
            align-items:center;
            gap:14px;
        }

        .sidebar .nav-link i{
            width:22px;
            font-size:17px;
        }

        .sidebar .nav-link:hover{
            background:rgba(255,255,255,0.07);
            transform:translateX(4px);
            color:#fff;
        }

        .sidebar .nav-link.active{
            background:linear-gradient(90deg,#1E7F6E,#166659);
            color:#fff;
            box-shadow:0 8px 20px rgba(30,127,110,0.3);
        }

        .logout-area{
            padding:15px 12px 20px;
            border-top:1px solid rgba(255,255,255,0.08);
        }

        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .main-content{
            margin-left:260px;
            width:calc(100% - 260px);
            min-height:100vh;
            padding:25px;
        }

        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar{
            background:#fff;
            border-radius:22px;
            padding:16px 24px;
            margin-bottom:25px;
            box-shadow:var(--shadow-sm);

            display:flex;
            align-items:center;
            justify-content:space-between;
        }

        .menu-toggle{
            width:42px;
            height:42px;
            border:none;
            border-radius:12px;
            background:#f3f4f6;
            display:none;
        }

        .location-pill{
            background:#f5f7f8;
            padding:10px 18px;
            border-radius:50px;
            font-size:14px;
            font-weight:600;
        }

        .user-box{
            display:flex;
            align-items:center;
            gap:14px;
            cursor:pointer;
        }

        .user-avatar{
            width:50px;
            height:50px;
            border-radius:50%;
            background:linear-gradient(135deg,#1E7F6E,#0B2B40);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-size:18px;
        }

        /* =====================================================
           CARDS
        ===================================================== */

        .dashboard-card{
            background:#fff;
            border-radius:28px;
            padding:24px;
            box-shadow:var(--shadow-sm);
            transition:0.3s;
            border:none;
            height:100%;
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
            color:#1E7F6E;

            display:flex;
            align-items:center;
            justify-content:center;

            font-size:24px;
        }

        /* =====================================================
           TABLE
        ===================================================== */

        .table-card{
            background:#fff;
            border-radius:28px;
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
            padding:18px;
            font-weight:700;
            color:#0B2B40;
            border:none;
        }

        .table-modern tbody td{
            padding:18px;
            vertical-align:middle;
            border-top:1px solid #eef2f7;
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
            padding:8px 18px;
            border-radius:50px;
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

            .main-content{
                margin-left:0;
                width:100%;
                padding:15px;
            }

            .menu-toggle{
                display:block;
            }

        }

        @media(max-width:768px){

            .topbar{
                padding:15px;
            }

            .dashboard-card{
                padding:20px;
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

<div class="layout-wrapper">

    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar" id="sidebar">

        <div>

            <div class="logo-area">

                <div class="emblem-icon">
                    <i class="fas fa-city"></i>
                </div>

                <h3>TN Municipal Corp</h3>

                <small>Commissioner Console</small>

            </div>

            <div class="sidebar-menu">

                <nav class="nav flex-column">

                    <a href="#"
                        class="nav-link active"
                        data-page="dashboard">

                        <i class="fas fa-chart-pie"></i>

                        Dashboard

                    </a>

                    <a href="#"
                        class="nav-link"
                        data-page="wards">

                        <i class="fas fa-map"></i>

                        Wards & Zones

                    </a>

                    <a href="#"
                        class="nav-link"
                        data-page="analysis">

                        <i class="fas fa-chart-line"></i>

                        Analytics

                    </a>

                    <a href="#"
                        class="nav-link"
                        data-page="reports">

                        <i class="fas fa-file-alt"></i>

                        Reports

                    </a>

                </nav>

            </div>

        </div>

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

    </aside>

    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <!-- TOPBAR -->

        <div class="topbar">

            <div class="d-flex align-items-center gap-3">

                <button class="menu-toggle" id="menuToggle">

                    <i class="fas fa-bars"></i>

                </button>

                <div class="location-pill">

                    <i class="fas fa-map-marker-alt text-success me-1"></i>

                    {{ $corporation->name ?? 'Chennai Corporation' }}

                </div>

            </div>

            <div class="dropdown">

                <div class="user-box"
                    data-bs-toggle="dropdown">

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

                        <a href="#"
                            class="dropdown-item">

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

    </main>

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
