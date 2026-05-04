<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Vela | Dynamic Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- OpenLayers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --primary-dark: #3a0ca3;
            --secondary: #4cc9f0;
            --accent: #f72585;
            --success: #06d6a0;
            --warning: #ffb703;
            --danger: #ef476f;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --sidebar-width: 260px;
            --header-height: 70px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
            --border-radius: 20px;
            --transition: all 0.2s ease;
        }

        body {
            background: var(--gray-50);
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            color: var(--gray-800);
            overflow-x: hidden;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar - Light Modern */
        .sidebar-light {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid var(--gray-200);
            transition: var(--transition);
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .logo-area {
            padding: 24px 20px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--gray-800);
            letter-spacing: -0.3px;
        }

        .nav-menu {
            flex: 1;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 14px;
            font-weight: 500;
            color: var(--gray-600);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
        }

        .nav-item i {
            width: 22px;
            font-size: 1.2rem;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Header */
        .header-light {
            height: var(--header-height);
            padding: 0 28px;
            background: white;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 990;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--gray-800);
        }

        .search-bar {
            background: var(--gray-100);
            border-radius: 40px;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 280px;
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 16px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 40px;
            transition: 0.2s;
        }

        .user-profile:hover {
            background: var(--gray-100);
        }

        .avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        /* Dashboard Content */
        .dashboard-container {
            padding: 28px 32px;
            flex: 1;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: var(--transition);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }

        /* Chart & Map Cards */
        .card-modern {
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            height: 100%;
        }

        .card-header-custom {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: white;
            font-weight: 600;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .map-wrapper {
            border-radius: 16px;
            overflow: hidden;
            height: 360px;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        /* Quick Actions */
        .quick-action-btn {
            background: var(--gray-100);
            border: none;
            padding: 14px;
            border-radius: 16px;
            transition: 0.2s;
            text-align: center;
        }

        .quick-action-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar-light {
                position: fixed;
                transform: translateX(-100%);
                z-index: 1050;
            }
            .sidebar-light.mobile-open {
                transform: translateX(0);
            }
            .dashboard-container {
                padding: 20px;
            }
            .search-bar {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .header-light {
                padding: 0 16px;
            }
            .search-bar {
                display: none;
            }
        }

        /* Toast */
        .toast-custom {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1100;
            background: white;
            border-left: 4px solid var(--primary);
            box-shadow: var(--shadow-md);
            border-radius: 12px;
            padding: 12px 20px;
            min-width: 260px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(60px);}
            to { opacity: 1; transform: translateX(0);}
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar-light" id="mainSidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
            <div class="logo-text">VelaDash</div>
        </div>
        <div class="nav-menu">
            <a href="#" class="nav-item active" data-view="dashboard">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item" data-view="map">
                <i class="fas fa-map-marked-alt"></i> <span>Map Explorer</span>
            </a>
            <a href="#" class="nav-item" data-view="analytics">
                <i class="fas fa-chart-pie"></i> <span>Analytics</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-building"></i> <span>Corporations</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
        </div>
        <div class="p-3 border-top text-muted small text-center">
            <i class="fas fa-shield-alt"></i> Secure v2.0
        </div>
    </aside>

    <div class="main-content">
        <!-- Header -->
        <header class="header-light">
            <div class="d-flex align-items-center gap-3">
                <button id="mobileMenuToggle" class="btn btn-light border d-lg-none rounded-circle p-2">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-title">
                    <h1 id="mainPageTitle">Dashboard Overview</h1>
                </div>
            </div>
            <div class="search-bar">
                <i class="fas fa-search text-muted"></i>
                <input type="text" id="globalSearch" placeholder="Search anything...">
            </div>
            <div class="user-profile" id="userMenuBtn">
                <div class="avatar">AD</div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold small">Alex Draven</div>
                    <div class="text-muted small">Administrator</div>
                </div>
                <i class="fas fa-chevron-down small"></i>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="dashboard-container" id="dynamicContent">
            <!-- Content will be injected dynamically -->
        </main>
    </div>
</div>

<div id="toastRoot" style="position: fixed; bottom: 20px; right: 20px; z-index: 1200;"></div>

<script>
    // ----- Dynamic Data Store (simulates backend/dynamic) -----
    let dashboardData = {
        stats: {
            regions: { value: 24, change: '+12%', trend: 'up' },
            corporations: { value: 1482, change: '+6.2%', trend: 'up' },
            users: { value: '8.4K', change: '+324', trend: 'up' },
            dataPoints: { value: '96.2M', change: 'live', trend: 'steady' }
        },
        trends: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            revenue: [28, 35, 42, 48, 62, 78, 94],
            insights: [18, 22, 29, 38, 49, 58, 71]
        },
        sectors: {
            labels: ['Energy', 'Tech Hubs', 'Infra', 'Defense'],
            data: [34, 28, 22, 16]
        },
        activities: [
            { icon: 'map-pin', text: 'Corp Atlas updated 3 layers', time: '2 min ago', color: 'primary' },
            { icon: 'chart-line', text: 'Revenue anomaly detection +18% in APAC', time: '15 min ago', color: 'success' },
            { icon: 'user-plus', text: '42 new users joined experimental maps', time: '1 hour ago', color: 'info' },
            { icon: 'database', text: 'Data ingestion from 7 new corporation sources', time: '3 hours ago', color: 'secondary' }
        ]
    };

    // Chart instances
    let trendChart, pieChart;
    let currentMap = null;
    let markerLayer = null;

    // Toast utility
    function showToast(message, type = 'info') {
        const container = document.getElementById('toastRoot');
        const toast = document.createElement('div');
        toast.className = 'toast-custom';
        const iconMap = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle', warning: 'fa-triangle-exclamation' };
        toast.innerHTML = `<div class="d-flex gap-3 align-items-center"><i class="fas ${iconMap[type] || 'fa-info-circle'} text-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'}"></i><div class="flex-grow-1"><strong>${type === 'success' ? 'Success' : 'Notification'}</strong><div class="small text-muted">${message}</div></div><button class="btn-close btn-sm" onclick="this.closest('.toast-custom').remove()"></button></div>`;
        container.appendChild(toast);
        setTimeout(() => { if(toast) toast.remove(); }, 4000);
    }

    // Helper to refresh Charts
    function initCharts() {
        const ctxLine = document.getElementById('trendChart')?.getContext('2d');
        const ctxPie = document.getElementById('sectorPie')?.getContext('2d');
        if(!ctxLine || !ctxPie) return;
        if(trendChart) trendChart.destroy();
        if(pieChart) pieChart.destroy();

        trendChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: dashboardData.trends.labels,
                datasets: [
                    { label: 'Geospatial Revenue (M$)', data: dashboardData.trends.revenue, borderColor: '#4361ee', backgroundColor: 'rgba(67,97,238,0.05)', tension: 0.3, fill: true, pointBackgroundColor: '#4361ee', pointBorderColor: '#fff' },
                    { label: 'Corporation Insights', data: dashboardData.trends.insights, borderColor: '#4cc9f0', borderDash: [5,5], tension: 0.3 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
        });

        pieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: { labels: dashboardData.sectors.labels, datasets: [{ data: dashboardData.sectors.data, backgroundColor: ['#4361ee', '#4cc9f0', '#f72585', '#ffb703'], borderWidth: 0 }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Initialize OpenLayers Map
    function initMap() {
        if(currentMap) return;
        currentMap = new ol.Map({
            target: 'map',
            layers: [ new ol.layer.Tile({ source: new ol.source.OSM() }) ],
            view: new ol.View({ center: ol.proj.fromLonLat([-74.006, 40.7128]), zoom: 11 })
        });
        // dynamic markers
        markerLayer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [
                    new ol.Feature({ geometry: new ol.geom.Point(ol.proj.fromLonLat([-74.0445, 40.6892])) }),
                    new ol.Feature({ geometry: new ol.geom.Point(ol.proj.fromLonLat([-73.985, 40.7489])) })
                ]
            }),
            style: new ol.style.Style({ image: new ol.style.Circle({ radius: 8, fill: new ol.style.Fill({ color: '#4361ee' }), stroke: new ol.style.Stroke({ color: 'white', width: 2 }) }) })
        });
        currentMap.addLayer(markerLayer);
        currentMap.updateSize();
    }

    function switchMapLayer(type) {
        if(!currentMap) return;
        const layers = currentMap.getLayers();
        layers.clear();
        if(type === 'satellite') {
            const satLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://{a-c}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png' }) });
            layers.push(satLayer);
        } else {
            layers.push(new ol.layer.Tile({ source: new ol.source.OSM() }));
        }
        if(markerLayer) layers.push(markerLayer);
        showToast(`Switched to ${type === 'satellite' ? 'Satellite' : 'Street'} view`, 'info');
    }

    // Dynamic Dashboard Render (pure dynamic from dashboardData)
    function renderDashboard() {
        const stats = dashboardData.stats;
        const activities = dashboardData.activities;
        const html = `
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="d-flex justify-content-between"><div><i class="fas fa-globe-asia fa-2x text-primary mb-3"></i><div class="text-muted small">Active Regions</div><h2 class="fw-bold mt-1">${stats.regions.value}</h2><span class="small text-success"><i class="fas fa-arrow-up"></i> ${stats.regions.change}</span></div><div class="bg-primary-light rounded-3 p-3"><i class="fas fa-map-marked-alt fa-2x text-primary opacity-50"></i></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="d-flex justify-content-between"><div><i class="fas fa-chart-simple fa-2x text-info mb-3"></i><div class="text-muted small">Corporation Data</div><h2 class="fw-bold mt-1">${stats.corporations.value}</h2><span class="small text-success">${stats.corporations.change}</span></div><div class="bg-info bg-opacity-10 rounded-3 p-3"><i class="fas fa-building fa-2x text-info opacity-50"></i></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="d-flex justify-content-between"><div><i class="fas fa-users fa-2x text-success mb-3"></i><div class="text-muted small">Active Users</div><h2 class="fw-bold mt-1">${stats.users.value}</h2><span class="small text-success">${stats.users.change} today</span></div><div class="bg-success bg-opacity-10 rounded-3 p-3"><i class="fas fa-user-astronaut fa-2x text-success opacity-50"></i></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="d-flex justify-content-between"><div><i class="fas fa-database fa-2x text-warning mb-3"></i><div class="text-muted small">Data Points</div><h2 class="fw-bold mt-1">${stats.dataPoints.value}</h2><span class="small text-secondary">real-time sync</span></div><div class="bg-warning bg-opacity-10 rounded-3 p-3"><i class="fas fa-cloud-arrow-up fa-2x text-warning opacity-50"></i></div></div></div></div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-7"><div class="card-modern"><div class="card-header-custom"><i class="fas fa-chart-line me-2 text-primary"></i> Revenue & Geospatial Trends <span class="badge bg-primary ms-2">Live</span></div><div class="card-body-custom"><canvas id="trendChart" height="220" style="max-height:260px"></canvas></div></div></div>
                <div class="col-lg-5"><div class="card-modern"><div class="card-header-custom"><i class="fas fa-chart-pie me-2 text-secondary"></i> Sector Distribution</div><div class="card-body-custom"><canvas id="sectorPie" height="220" style="max-height:240px"></canvas></div></div></div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12"><div class="card-modern"><div class="card-header-custom d-flex justify-content-between align-items-center"><span><i class="fas fa-map me-2 text-primary"></i> Geospatial Intelligence</span><div class="btn-group btn-group-sm"><button class="btn btn-outline-secondary" id="satelliteBtn"><i class="fas fa-globe"></i> Satellite</button><button class="btn btn-outline-secondary" id="streetBtn"><i class="fas fa-road"></i> Streets</button></div></div><div class="card-body-custom"><div class="map-wrapper" id="mapContainer" style="height:380px"><div id="map" style="height:100%"></div></div></div></div></div>
            </div>
            <div class="row g-4">
                <div class="col-lg-7"><div class="card-modern"><div class="card-header-custom"><i class="fas fa-bolt me-2 text-warning"></i> Activity Pulse</div><div class="card-body-custom"><div id="activityList">${activities.map(act => `<div class="activity-item"><div class="activity-icon"><i class="fas fa-${act.icon} text-primary"></i></div><div class="flex-grow-1"><div class="fw-semibold">${act.text}</div><div class="small text-muted">${act.time}</div></div></div>`).join('')}</div></div></div></div>
                <div class="col-lg-5"><div class="card-modern"><div class="card-header-custom"><i class="fas fa-rocket me-2 text-accent"></i> Quick Actions</div><div class="card-body-custom"><div class="row g-3"><div class="col-6"><div class="quick-action-btn" data-action="sync"><i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block text-primary"></i><small>Sync Feeds</small></div></div><div class="col-6"><div class="quick-action-btn" data-action="insight"><i class="fas fa-chalkboard-user fa-2x mb-2 d-block text-info"></i><small>Insights</small></div></div><div class="col-6"><div class="quick-action-btn" data-action="export"><i class="fas fa-file-csv fa-2x mb-2 d-block text-success"></i><small>Export</small></div></div><div class="col-6"><div class="quick-action-btn" data-action="alerts"><i class="fas fa-bell fa-2x mb-2 d-block text-warning"></i><small>Alerts</small></div></div></div><div class="mt-3 pt-2 border-top text-center"><i class="fas fa-crown text-warning me-1"></i> KPI up 23% this quarter</div></div></div></div>
            </div>
        `;
        document.getElementById('dynamicContent').innerHTML = html;
        initCharts();
        setTimeout(() => { initMap(); }, 100);
        document.getElementById('satelliteBtn')?.addEventListener('click', () => switchMapLayer('satellite'));
        document.getElementById('streetBtn')?.addEventListener('click', () => switchMapLayer('street'));
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', (e) => { showToast(`Action: ${btn.getAttribute('data-action')} triggered`, 'success'); });
        });
    }

    // Mock analytics view (simple dynamic)
    function renderAnalytics() {
        const html = `<div class="card-modern p-4 text-center"><h4><i class="fas fa-chart-simple me-2 text-primary"></i> Analytics Engine</h4><canvas id="dynamicAnalyticsChart" height="300"></canvas><p class="mt-3 text-muted">Dynamic data insights will appear here based on real datasets.</p></div>`;
        document.getElementById('dynamicContent').innerHTML = html;
        const ctx = document.getElementById('dynamicAnalyticsChart')?.getContext('2d');
        if(ctx) {
            new Chart(ctx, { type: 'bar', data: { labels: ['Q1','Q2','Q3','Q4'], datasets: [{ label: 'Revenue', data: [320, 450, 580, 720], backgroundColor: '#4361ee' }] } });
        }
    }

    function renderMapOnly() {
        const html = `<div class="card-modern"><div class="card-header-custom"><i class="fas fa-map-marked-alt me-2 text-primary"></i> Full Map Explorer</div><div class="card-body-custom"><div class="map-wrapper" style="height: 70vh"><div id="map" style="height:100%"></div></div><div class="mt-3 d-flex gap-2"><button id="fullSatelliteBtn" class="btn btn-outline-primary">Satellite</button><button id="fullStreetBtn" class="btn btn-outline-secondary">Street</button></div></div></div>`;
        document.getElementById('dynamicContent').innerHTML = html;
        setTimeout(() => {
            initMap();
            document.getElementById('fullSatelliteBtn')?.addEventListener('click', () => switchMapLayer('satellite'));
            document.getElementById('fullStreetBtn')?.addEventListener('click', () => switchMapLayer('street'));
        }, 50);
    }

    // Navigation handler (dynamic routing without static codes)
    function handleNavigation(view) {
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        const activeNav = Array.from(document.querySelectorAll('.nav-item')).find(el => el.getAttribute('data-view') === view);
        if(activeNav) activeNav.classList.add('active');
        let title = 'Dashboard Overview';
        if(view === 'dashboard') { renderDashboard(); title = 'Dashboard Overview'; }
        else if(view === 'map') { renderMapOnly(); title = 'Map Explorer'; }
        else if(view === 'analytics') { renderAnalytics(); title = 'Analytics Studio'; }
        document.getElementById('mainPageTitle').innerText = title;
    }

    // Event Listeners
    document.querySelectorAll('.nav-item[data-view]').forEach(nav => {
        nav.addEventListener('click', (e) => {
            e.preventDefault();
            const view = nav.getAttribute('data-view');
            handleNavigation(view);
        });
    });

    // Mobile sidebar toggle
    document.getElementById('mobileMenuToggle')?.addEventListener('click', () => {
        document.getElementById('mainSidebar')?.classList.toggle('mobile-open');
    });

    // Global search simulation
    document.getElementById('globalSearch')?.addEventListener('input', (e) => {
        const term = e.target.value.trim();
        if(term.length > 2) showToast(`Searching: "${term}" (demo dynamic)`, 'info');
    });

    // User menu simulation
    document.getElementById('userMenuBtn')?.addEventListener('click', () => showToast('Profile & Settings panel (dynamic)', 'info'));

    // initial load dynamic dashboard
    renderDashboard();
    window.addEventListener('resize', () => { if(currentMap) setTimeout(() => currentMap.updateSize(), 200); });
</script>
</body>
</html>
