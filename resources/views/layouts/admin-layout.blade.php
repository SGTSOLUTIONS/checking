<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TN Municipal | Heritage e-Governance Suite</title>

    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- OpenLayers & Chart.js for advanced dashboard -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-tn: #e67e22;
            --primary-dark: #b45f1b;
            --secondary-gold: #f39c12;
            --deep-blue: #1e3a5f;
            --terracotta: #d35400;
            --sand-light: #fef5e8;
            --shadow-elegant: 0 20px 35px -12px rgba(0, 0, 0, 0.2);
            --border-radius-xl: 2rem;
            --sidebar-width: 280px;
            --header-height: 80px;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            min-height: 100vh;
            background: #f4f2ef;
            position: relative;
            overflow-x: hidden;
        }

        /* Heritage Tamil Nadu Background */
        .heritage-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .heritage-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: slowZoom 24s ease infinite alternate;
        }

        @keyframes slowZoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.07);
            }
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(125deg, rgba(30, 58, 95, 0.4) 0%, rgba(230, 126, 34, 0.2) 100%);
            z-index: -1;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(243, 156, 18, 0.3);
            border-radius: 50%;
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.2;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1rem;
            left: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            max-width: 380px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 576px) {
            .toast-container {
                left: auto;
                right: 1.5rem;
            }
        }

        .toast {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border-left: 4px solid;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-success {
            border-left-color: #27ae60;
        }

        .toast-error {
            border-left-color: #e74c3c;
        }

        .toast-warning {
            border-left-color: #f39c12;
        }

        .toast-info {
            border-left-color: #2980b9;
        }

        .toast-icon {
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 800;
            font-size: 0.85rem;
        }

        .toast-message {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .toast-close {
            background: none;
            border: none;
            color: #7e8b9e;
            cursor: pointer;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
            width: 0;
            animation: progressShrink linear forwards;
        }

        @keyframes progressShrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* ========= MODERN ADMIN LAYOUT WITH HERITAGE TOUCH ========= */
        .admin-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 5;
        }

        /* SIDEBAR - Heritage themed */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(145deg, #1e3a5f 0%, #0f2c48 100%);
            backdrop-filter: blur(2px);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.2);
            border-right: 1px solid rgba(230, 126, 34, 0.3);
        }

        .sidebar-header {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: #e67e22;
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .logo-text {
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.3px;
        }

        .logo-sub {
            font-size: 0.65rem;
            opacity: 0.8;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 24px 0;
        }

        .nav-section-title {
            padding: 0 24px 12px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.6;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 24px;
            margin: 4px 12px;
            border-radius: 14px;
            transition: 0.2s;
            cursor: pointer;
            color: #f0f3f8;
            text-decoration: none;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(230, 126, 34, 0.2);
            color: white;
            border-left: 3px solid #e67e22;
        }

        .nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        .nav-badge {
            margin-left: auto;
            background: #e67e22;
            padding: 2px 8px;
            border-radius: 40px;
            font-size: 0.7rem;
        }

        .sidebar-footer {
            padding: 20px 24px;
            font-size: 0.7rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* MAIN CONTENT AREA */
        .main-content-area {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }

        /* Header */
        .heritage-header {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--header-height);
            border-bottom: 1px solid rgba(230, 126, 34, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a5f, #e67e22);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-bar {
            position: relative;
            width: 280px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            font-size: 0.85rem;
            background: #f8fafc;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #e67e22;
        }

        .action-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            background: #fef5e8;
            color: #1e3a5f;
            transition: 0.2s;
            position: relative;
        }

        .action-btn:hover {
            background: #e67e22;
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fffbf5;
            padding: 6px 18px 6px 12px;
            border-radius: 60px;
            cursor: pointer;
            border: 1px solid #ffe1b9;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e67e22;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .profile-dropdown {
            position: absolute;
            top: 70px;
            right: 30px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.2);
            width: 280px;
            display: none;
            z-index: 200;
            overflow: hidden;
        }

        .profile-dropdown.show {
            display: block;
            animation: fadeDrop 0.2s;
        }

        @keyframes fadeDrop {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dashboard Content Styles */
        .dashboard-wrapper {
            padding: 30px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 28px;
            padding: 1.5rem;
            border-left: 6px solid #e67e22;
            box-shadow: var(--shadow-elegant);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            background: #fef2e6;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #e67e22;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1e3a5f;
            line-height: 1;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .card-chart {
            background: white;
            border-radius: 28px;
            padding: 1.2rem;
            box-shadow: var(--shadow-elegant);
        }

        .map-wrapper {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            height: 450px;
            position: relative;
            box-shadow: var(--shadow-elegant);
            margin-bottom: 30px;
        }

        #map,
        #exploreMap {
            width: 100%;
            height: 100%;
        }

        .activity-list {
            background: white;
            border-radius: 28px;
            padding: 1.5rem;
            box-shadow: var(--shadow-elegant);
        }

        .activity-item {
            padding: 14px 0;
            border-bottom: 1px solid #f0e6dc;
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .btn-tn {
            background: linear-gradient(95deg, #e67e22, #f39c12);
            border: none;
            color: white;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content-area {
                margin-left: 0;
            }

            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .dashboard-wrapper {
                padding: 20px;
            }

            .search-bar {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="heritage-bg">
        <img src="https://images.pexels.com/photos/16466766/pexels-photo-16466766.png"
            alt="Tamil Nadu Heritage Building">
    </div>
    <div class="bg-overlay"></div>
    <div class="particles" id="particles"></div>
    <div id="toast-container" class="toast-container"></div>

    <!-- Unified admin + heritage layout -->
    <div class="admin-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-temple"></i></div>
                <div>
                    <div class="logo-text">TN Municipal</div>
                    <div class="logo-sub">e-Governance · Heritage</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-title">MAIN</div>
                <a href="#" class="nav-item active" data-nav="dashboard"><i
                        class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="#" class="nav-item" data-nav="map"><i class="fas fa-map-marked-alt"></i><span>Map
                        Explorer</span><span class="nav-badge">LIVE</span></a>
                <a href="#" class="nav-item" data-nav="property"><i
                        class="fas fa-file-invoice-dollar"></i><span>Property Tax</span></a>
                <div class="nav-section-title">ADMINISTRATION</div>
                <a href="#" class="nav-item"><i class="fas fa-city"></i><span>Corporations</span></a>
                <a href="#" class="nav-item"><i class="fas fa-users"></i><span>Citizen Registry</span><span
                        class="nav-badge">1.2k</span></a>
                <a href="#" class="nav-item"><i class="fas fa-chart-line"></i><span>Revenue Insights</span></a>
                <div class="nav-section-title">SUPPORT</div>
                <a href="#" class="nav-item"><i class="fas fa-headset"></i><span>Helpdesk</span></a>
            </nav>
            <div class="sidebar-footer">TN Municipal · v2.0 | <i class="fas fa-landmark"></i> Pride of Tamil Nadu</div>
        </aside>

        <div class="main-content-area">
            <header class="heritage-header">
                <div>
                    <h1 class="page-title" id="dynamic-page-title">Tax Administration Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Search properties, zones...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <button class="action-btn" title="Notifications"><i class="fas fa-bell"></i><span
                            class="notification-badge">5</span></button>
                    <button class="action-btn" title="Messages"><i class="fas fa-envelope"></i><span
                            class="notification-badge">3</span></button>
                    <div class="user-profile" id="userProfileBtn">
                        <div class="user-avatar"><i class="fas fa-user-tie"></i></div>
                        <div><strong>Thiru. Selvam IAS</strong><br><small>Corporation Commissioner</small></div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div style="padding: 20px; text-align:center; background:#fef5e8;"><i
                                class="fas fa-user-circle fa-3x" style="color:#e67e22;"></i><br><strong>Selvam,
                                IAS</strong><br><span>Admin Role</span></div>
                        <a href="#" class="dropdown-item"
                            style="display:block; padding:12px 20px; text-decoration:none; color:#1e3a5f;"><i
                                class="fas fa-user"></i> My Profile</a>
                        <a href="#" class="dropdown-item"
                            style="display:block; padding:12px 20px; text-decoration:none; color:#1e3a5f;"><i
                                class="fas fa-cog"></i> Settings</a>
                        <hr class="m-0">
                        <button id="fakeLogoutBtn"
                            style="width:100%; text-align:left; padding:12px 20px; background:none; border:none;"><i
                                class="fas fa-sign-out-alt"></i> Logout (Demo)</button>
                    </div>
                </div>
            </header>

            <main id="main-dynamic-content">
                <!-- DASHBOARD VIEW -->
                <div id="dashboard-view">
                    <div class="dashboard-wrapper">
                        <div class="stat-grid">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                                <div>
                                    <div class="stat-value">₹48.2 Cr</div>
                                    <div>Annual Collection</div><small>↑ 12.4% vs last year</small>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-building"></i></div>
                                <div>
                                    <div class="stat-value">1,24,560</div>
                                    <div>Total Properties</div><small>Assessed</small>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-charging-station"></i></div>
                                <div>
                                    <div class="stat-value">92.3%</div>
                                    <div>Tax Compliance</div><small>➕ 5.2% increase</small>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
                                <div>
                                    <div class="stat-value">15,234</div>
                                    <div>Pending Rebates</div><small>Under processing</small>
                                </div>
                            </div>
                        </div>
                        <div class="charts-row">
                            <div class="card-chart"><canvas id="taxChart" height="200"></canvas>
                                <p class="text-center mt-2 fw-semibold">Quarterly Tax Collection (Cr)</p>
                            </div>
                            <div class="card-chart"><canvas id="pieChart" height="200"></canvas>
                                <p class="text-center mt-2">Zone-wise Contribution</p>
                            </div>
                        </div>
                        <div class="map-wrapper">
                            <div id="map"></div>
                            <div
                                style="position:absolute; bottom:15px; left:15px; background:rgba(255,255,255,0.9); border-radius:30px; padding:6px 15px; font-size:12px;">
                                <i class="fas fa-map-marker-alt" style="color:#e67e22;"></i> Greater Chennai GIS</div>
                        </div>
                        <div class="activity-list">
                            <h5><i class="fas fa-history"></i> Recent Tax Payments</h5>
                            <div class="activity-item"><i class="fas fa-receipt"
                                    style="color:#e67e22; font-size:1.3rem;"></i>
                                <div><strong>#TN-2421-89</strong> - ₹12,400 paid by Ramesh Flats, T.Nagar<br><small>Just
                                        now</small></div>
                            </div>
                            <div class="activity-item"><i class="fas fa-check-circle"
                                    style="color:#27ae60; font-size:1.3rem;"></i>
                                <div><strong>#TN-5678-23</strong> - ₹8,750 paid online, Mylapore<br><small>10 mins
                                        ago</small></div>
                            </div>
                            <div class="activity-item"><i class="fas fa-file-signature"
                                    style="font-size:1.3rem;"></i>
                                <div><strong>New property registration</strong> - Anna Nagar zone +45 new
                                    assessments<br><small>1 hour ago</small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MAP EXPLORER VIEW (Hidden initially) -->
                <div id="map-view" style="display: none;">
                    <div class="dashboard-wrapper">
                        <div class="card-chart mb-4">
                            <h4><i class="fas fa-globe-asia"></i> TN Property Tax Geo-Explorer</h4>
                            <p>Interactive map with ward boundaries, tax heat zones & property layers</p>
                        </div>
                        <div class="map-wrapper" style="height: 520px;">
                            <div id="exploreMap" style="height:100%"></div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded-4"><i class="fas fa-chart-simple"></i> <strong>Zone
                                        Performance:</strong> North Zone collection +18% this quarter</div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded-4"><i class="fas fa-fire"></i> High-value properties:
                                    2,340 properties above ₹50L valuation</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- PROPERTY TAX VIEW (Demo) -->
                <div id="property-view" style="display: none;">
                    <div class="dashboard-wrapper">
                        <div class="card-chart">
                            <h4><i class="fas fa-file-invoice"></i> Property Tax Management</h4>
                            <p>Quick assessment & collection overview</p>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3"><i class="fas fa-check-circle text-success"></i>
                                        On-time payments: 78%</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3"><i class="fas fa-chart-line"></i> Monthly
                                        target: ₹4.2 Cr</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3"><i
                                            class="fas fa-exclamation-triangle text-warning"></i> Defaulters: 2,341
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toast system (unified)
        window.showToast = function(type, title, message, duration = 4500) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML =
                `<i class="fas ${icons[type]} toast-icon"></i><div class="toast-content"><div class="toast-title">${escapeHtml(title)}</div><p class="toast-message">${escapeHtml(message)}</p></div><button class="toast-close"><i class="fas fa-times"></i></button><div class="toast-progress" style="animation-duration: ${duration/1000}s;"></div>`;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 20);
            toast.querySelector('.toast-close').addEventListener('click', () => removeToast(toast));
            setTimeout(() => removeToast(toast), duration);

            function removeToast(t) {
                t.classList.remove('show');
                t.classList.add('hide');
                setTimeout(() => t.remove(), 350);
            }

            function escapeHtml(s) {
                return String(s).replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }
        };

        // Particles generation
        (function createParticles() {
            const container = document.getElementById('particles');
            if (!container) return;
            for (let i = 0; i < 55; i++) {
                let p = document.createElement('div');
                p.classList.add('particle');
                let s = Math.random() * 5 + 2;
                p.style.width = s + 'px';
                p.style.height = s + 'px';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = Math.random() * 14 + 8 + 's';
                p.style.animationDelay = Math.random() * 12 + 's';
                p.style.background = `rgba(230,126,34,${Math.random()*0.4+0.1})`;
                container.appendChild(p);
            }
        })();

        // Profile dropdown toggle
        const profileBtn = document.getElementById('userProfileBtn');
        const dropdown = document.getElementById('profileDropdown');
        if (profileBtn && dropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove(
                    'show');
            });
        }
        document.getElementById('fakeLogoutBtn')?.addEventListener('click', () => showToast('success', 'Signed out',
            'Demo: redirected to login portal', 2000));

        // Sidebar toggle for mobile
        const toggleBtn = document.createElement('button');
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.className = 'btn btn-tn rounded-circle position-fixed d-lg-none';
        toggleBtn.style.position = 'fixed';
        toggleBtn.style.bottom = '20px';
        toggleBtn.style.left = '20px';
        toggleBtn.style.zIndex = '1090';
        toggleBtn.style.width = '50px';
        toggleBtn.style.height = '50px';
        toggleBtn.style.borderRadius = '50%';
        toggleBtn.style.backgroundColor = '#e67e22';
        toggleBtn.style.color = 'white';
        toggleBtn.style.border = 'none';
        document.body.appendChild(toggleBtn);
        const sidebar = document.getElementById('sidebar');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Charts initialization
        let taxChart, pieChart;
        const ctx1 = document.getElementById('taxChart')?.getContext('2d');
        const ctx2 = document.getElementById('pieChart')?.getContext('2d');
        if (ctx1) taxChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [{
                    label: 'Collection (Cr)',
                    data: [11.2, 13.5, 14.8, 18.2],
                    borderColor: '#e67e22',
                    backgroundColor: 'rgba(230,126,34,0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
        if (ctx2) pieChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['North', 'South', 'Central', 'East'],
                datasets: [{
                    data: [34, 28, 22, 16],
                    backgroundColor: ['#e67e22', '#f39c12', '#d35400', '#e98c46']
                }]
            }
        });

        // OpenLayers Maps
        let mainMap, explorerMap;

        function initMainMap() {
            if (document.getElementById('map') && typeof ol !== 'undefined') {
                mainMap = new ol.Map({
                    target: 'map',
                    layers: [new ol.layer.Tile({
                        source: new ol.source.OSM()
                    })],
                    view: new ol.View({
                        center: ol.proj.fromLonLat([80.2707, 13.0827]),
                        zoom: 12
                    })
                });
            }
        }

        function initExplorerMap() {
            if (document.getElementById('exploreMap') && typeof ol !== 'undefined') {
                explorerMap = new ol.Map({
                    target: 'exploreMap',
                    layers: [new ol.layer.Tile({
                        source: new ol.source.OSM()
                    })],
                    view: new ol.View({
                        center: ol.proj.fromLonLat([80.2707, 13.0827]),
                        zoom: 12
                    })
                });
            }
        }
        setTimeout(() => {
            initMainMap();
            initExplorerMap();
        }, 300);

        // Navigation logic (Dashboard / Map Explorer / Property Tax)
        const navLinks = document.querySelectorAll('.nav-item');
        const dashboardDiv = document.getElementById('dashboard-view');
        const mapDiv = document.getElementById('map-view');
        const propertyDiv = document.getElementById('property-view');
        const pageTitleEl = document.getElementById('dynamic-page-title');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                const navType = link.getAttribute('data-nav');
                dashboardDiv.style.display = 'none';
                mapDiv.style.display = 'none';
                propertyDiv.style.display = 'none';
                if (navType === 'map') {
                    mapDiv.style.display = 'block';
                    pageTitleEl.innerText = 'GIS Map Explorer | TN Heritage GIS';
                    setTimeout(() => {
                        if (explorerMap) explorerMap.updateSize();
                        else initExplorerMap();
                    }, 150);
                    showToast('info', 'Map Explorer', '📍 Geo-spatial tax zones & property layers loaded',
                        2000);
                } else if (navType === 'property') {
                    propertyDiv.style.display = 'block';
                    pageTitleEl.innerText = 'Property Tax Management | TN Municipal';
                    showToast('success', 'Tax Module', 'Quick view: 92% compliance this month', 1800);
                } else {
                    dashboardDiv.style.display = 'block';
                    pageTitleEl.innerText = 'Tax Administration Dashboard | TN Municipal';
                    if (mainMap) mainMap.updateSize();
                    showToast('success', 'Vanakkam!',
                        'Welcome to TN Unified Municipal Portal • Heritage meets e-Governance', 3000);
                }
            });
        });

        // Initial welcome toast
        setTimeout(() => {
            showToast('success', 'Welcome!', 'TN Municipal e-Governance Suite Loaded', 4000);
        }, 500);
    </script>
    @yield('scripts')
</body>

</html>
