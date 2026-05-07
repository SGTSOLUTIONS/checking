<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>@yield('title', 'Commissioner Dashboard')</title>

    <!-- Bootstrap 5 CSS + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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

        /* Animated gradient orbs */
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
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.15;
            }
            100% {
                transform: translate(40px, 30px) scale(1.2);
                opacity: 0.28;
            }
        }

        @keyframes floatBlob2 {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.12;
            }
            100% {
                transform: translate(-30px, -40px) scale(1.3);
                opacity: 0.25;
            }
        }

        /* Sidebar Styling */
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

        /* Top Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            border-radius: 15px;
            margin-bottom: 20px;
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

        /* Content Panels Animation */
        .content-panel {
            animation: fadeSlideUp 0.5s ease;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Table Styling */
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

        .official-emblem-sm {
            width: 45px;
            height: auto;
        }

        .emblem-img {
            width: 100px;
            height: 100px;
            transition: transform 0.3s ease;
        }

        .badge.bg-primary {
            background-color: #1679AB !important;
        }

        .badge.bg-success {
            background-color: #102C57 !important;
        }

        .badge.bg-info {
            background-color: #FFB1B1 !important;
            color: #102C57;
        }

        .btn-outline-secondary {
            border-color: #FFCBCB;
            color: #102C57;
        }

        .btn-outline-secondary:hover {
            background-color: #FFCBCB;
            border-color: #FFB1B1;
        }

        .text-muted {
            color: #5A6E7A !important;
        }

        .dropdown-item:active {
            background-color: #FFCBCB;
        }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
