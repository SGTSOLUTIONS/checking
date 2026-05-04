<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Orion Vista | Executive Dashboard</title>
    <!-- Bootstrap 5 CSS (full fluid grid) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- OpenLayers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #b15eff;
            --primary-dark: #7c3aed;
            --primary-glow: rgba(177, 94, 255, 0.4);
            --secondary: #00d2ff;
            --accent: #ff5e7e;
            --dark-bg: #0a0c14;
            --card-bg: rgba(18, 22, 35, 0.8);
            --border-glow: rgba(255, 255, 255, 0.08);
            --text-light: #f0f3fa;
            --text-dim: #a3b3d6;
            --success: #2dd4bf;
            --warning: #fbbf24;
            --danger: #f43f5e;
            --sidebar-width: 280px;
            --header-height: 82px;
            --transition-smooth: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        body {
            background: radial-gradient(circle at 10% 30%, #0c0f1c, #05070c);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif;
            color: var(--text-light);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* glass-morphism core */
        .glass-panel {
            background: rgba(18, 25, 40, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(177, 94, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.02) inset;
        }

        /* dashboard layout - fluid grid */
        .admin-fluid {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ========= SIDEBAR ========= */
        .sidebar-future {
            width: var(--sidebar-width);
            background: rgba(8, 12, 24, 0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(177, 94, 255, 0.3);
            transition: var(--transition-smooth);
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            box-shadow: 12px 0 40px rgba(0, 0, 0, 0.3);
        }

        .logo-area {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(120deg, #fff, var(--primary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-menu {
            flex: 1;
            padding: 32px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-item-glass {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 20px;
            border-radius: 18px;
            font-weight: 500;
            color: var(--text-dim);
            transition: var(--transition-smooth);
            cursor: pointer;
            text-decoration: none;
        }

        .nav-item-glass i {
            width: 24px;
            font-size: 1.3rem;
        }

        .nav-item-glass:hover {
            background: rgba(177, 94, 255, 0.2);
            color: white;
            transform: translateX(6px);
            backdrop-filter: blur(4px);
        }

        .nav-item-glass.active {
            background: linear-gradient(95deg, rgba(177, 94, 255, 0.25), rgba(0, 210, 255, 0.1));
            color: white;
            border-left: 3px solid var(--primary);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
        }

        .badge-new {
            background: var(--accent);
            margin-left: auto;
            font-size: 0.7rem;
            padding: 4px 9px;
            border-radius: 40px;
            font-weight: 600;
        }

        /* ========= MAIN PANEL ========= */
        .main-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            overflow-x: hidden;
        }

        /* header cosmic */
        .header-cosmic {
            height: var(--header-height);
            padding: 0 28px;
            background: rgba(10, 14, 26, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(177, 94, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .page-headline h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 0;
        }

        .search-glass {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 60px;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 300px;
        }

        .search-glass input {
            background: transparent;
            border: none;
            color: white;
            width: 100%;
            outline: none;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.03);
            padding: 6px 18px 6px 12px;
            border-radius: 60px;
            cursor: pointer;
            transition: 0.2s;
        }

        .avatar-glow {
            width: 44px;
            height: 44px;
            background: linear-gradient(145deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 0 12px var(--primary-glow);
        }

        /* content fluid */
        .dashboard-fluid {
            padding: 28px 32px;
            flex: 1;
        }

        /* stats cards */
        .stat-neo {
            background: rgba(15, 20, 32, 0.6);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 1.6rem 1.8rem;
            border: 1px solid rgba(177, 94, 255, 0.3);
            transition: 0.25s ease;
            height: 100%;
        }

        .stat-neo:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
        }

        /* chart containers */
        .chart-futuristic {
            background: rgba(12, 16, 28, 0.65);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: 100%;
        }

        /* map */
        .map-wrapper-glass {
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(177, 94, 255, 0.3);
            background: #070a12;
        }

        #map {
            height: 380px;
            width: 100%;
        }

        /* activity feed */
        .activity-stream {
            max-height: 380px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .activity-item {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            padding: 14px;
            margin-bottom: 12px;
            transition: 0.2s;
            border-left: 3px solid var(--primary);
        }

        /* responsiveness */
        @media (max-width: 1100px) {
            .sidebar-future {
                position: fixed;
                transform: translateX(-100%);
                z-index: 1100;
            }
            .sidebar-future.mobile-open {
                transform: translateX(0);
            }
            .header-cosmic .search-glass {
                width: 200px;
            }
            .dashboard-fluid {
                padding: 20px;
            }
        }
        @media (max-width: 780px) {
            .header-cosmic {
                flex-wrap: wrap;
                height: auto;
                padding: 12px;
            }
            .user-card .user-name {
                display: none;
            }
        }
        /* custom scroll */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #1e1f2c;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
    </style>
    @yield('css')
</head>
<body>
<div class="admin-fluid">
    <!-- SIDEBAR FUTURISTIC -->
    <aside class="sidebar-future" id="mainSidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-satellite-dish"></i></div>
            <div class="logo-text">ORION VISTA</div>
        </div>
        <div class="nav-menu">
            <a href="#" class="nav-item-glass active"><i class="fas fa-chart-line"></i><span>Live Dashboard</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-map"></i><span>Geo Explorer</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-building"></i><span>Corporations</span><span class="badge-new">AI+</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-users"></i><span>User Intelligence</span><span class="badge-new">12</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-chart-pie"></i><span>Analytics Core</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-layer-group"></i><span>Data Layers</span></a>
            <a href="#" class="nav-item-glass"><i class="fas fa-crown"></i><span>Executive Suite</span></a>
        </div>
        <div class="p-3 mt-auto border-top border-light opacity-50 text-center small">
            <i class="fas fa-shield-alt me-1"></i> Secure Nexus v4.0
        </div>
    </aside>

    <div class="main-fluid">
        <!-- HEADER -->
        <header class="header-cosmic">
            <div style="display: flex; align-items: center; gap: 18px;">
                <button id="mobileMenuToggle" class="btn btn-outline-light d-lg-none" style="border-radius: 40px;"><i class="fas fa-bars"></i></button>
                <div class="page-headline"><h1>Cosmic Command</h1></div>
            </div>
            <div class="search-glass">
                <i class="fas fa-search text-secondary"></i>
                <input type="text" placeholder="Search realtime geospatial...">
            </div>
            <div class="user-card" id="userProfileBtn">
                <div class="avatar-glow">AD</div>
                <div class="user-name"><strong>Alex Draven</strong><br><span style="font-size: 12px;">Lead Geospatial</span></div>
                <i class="fas fa-chevron-down ms-1"></i>
                <div class="dropdown-menu-custom" id="profileDropdownMenu" style="display: none; position: absolute; top: 70px; right: 20px; background: #0e1222; border-radius: 24px; padding: 12px; min-width: 200px; z-index: 1200; backdrop-filter: blur(20px); border:1px solid rgba(177,94,255,0.3);">
                    <a class="dropdown-item text-white" href="#"><i class="fas fa-user-circle me-2"></i>Profile</a>
                    <a class="dropdown-item text-white" href="#"><i class="fas fa-cog me-2"></i>Settings</a>
                    <hr class="bg-secondary">
                    <a class="dropdown-item text-white" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </div>
            </div>
        </header>

        <!-- FLUID DASHBOARD CONTENT -->
        <div class="dashboard-fluid">
            <!-- Stat Cards row -->
            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-neo d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-globe-asia fa-2x text-primary mb-2 d-block"></i><span class="text-secondary">Active Regions</span>
                            <h2 class="fw-bold mt-2">24</h2><span class="small text-success">+12% vs last month</span>
                        </div>
                        <div class="bg-primary bg-opacity-25 p-3 rounded-4"><i class="fas fa-map-marked-alt fa-2x"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-neo d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-chart-simple fa-2x text-info mb-2 d-block"></i><span>Corporation Data</span>
                            <h2 class="fw-bold mt-2">1,482</h2><span class="small text-warning">+6.2% insights</span>
                        </div>
                        <div class="bg-info bg-opacity-25 p-3 rounded-4"><i class="fas fa-building"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-neo d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-users fa-2x text-success mb-2 d-block"></i><span>Active Users</span>
                            <h2 class="fw-bold mt-2">8.4K</h2><span class="small text-info">+324 today</span>
                        </div>
                        <div class="bg-success bg-opacity-25 p-3 rounded-4"><i class="fas fa-user-astronaut"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-neo d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-database fa-2x text-warning mb-2 d-block"></i><span>Data Points</span>
                            <h2 class="fw-bold mt-2">96.2M</h2><span class="small text-danger">real-time sync</span>
                        </div>
                        <div class="bg-warning bg-opacity-25 p-3 rounded-4"><i class="fas fa-cloud-arrow-up"></i></div>
                    </div>
                </div>
            </div>

            <!-- CHARTS + MAP grid fluid -->
            <div class="row g-4 mb-5">
                <div class="col-lg-7">
                    <div class="chart-futuristic">
                        <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Revenue & Geospatial Trends</h5><span class="badge bg-primary">Live</span></div>
                        <canvas id="trendChart" height="220" style="max-height: 260px; width: 100%;"></canvas>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="chart-futuristic">
                        <div class="d-flex mb-3"><h5><i class="fas fa-chart-pie me-2 text-secondary"></i> Sector Distribution</h5></div>
                        <canvas id="sectorPie" height="220" style="max-height: 240px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- MAP EXPLORER section (fully fluid) -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="chart-futuristic p-0 overflow-hidden">
                        <div class="p-3 border-bottom border-secondary"><i class="fas fa-map me-2 text-primary"></i> <strong>Live Geospatial Intelligence</strong> <span class="float-end"><i class="fas fa-sync-alt"></i> dynamic layers</span></div>
                        <div class="map-wrapper-glass">
                            <div id="map" style="height: 420px; width: 100%;"></div>
                        </div>
                        <div class="p-2 d-flex gap-2 justify-content-end bg-dark bg-opacity-25">
                            <button id="satelliteBtn" class="btn btn-sm btn-outline-light"><i class="fas fa-globe"></i> Satellite</button>
                            <button id="streetBtn" class="btn btn-sm btn-outline-light"><i class="fas fa-road"></i> Streets</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity + Quick actions double fluid -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="chart-futuristic">
                        <div class="d-flex justify-content-between"><h5><i class="fas fa-bolt me-2 text-warning"></i> Activity Pulse</h5><span>latest events</span></div>
                        <div class="activity-stream mt-3">
                            <div class="activity-item"><i class="fas fa-map-pin me-2 text-primary"></i> <strong>Corp Atlas</strong> updated 3 layers • 2 min ago</div>
                            <div class="activity-item"><i class="fas fa-chart-line me-2 text-success"></i> <strong>Revenue anomaly detection</strong> +18% in APAC</div>
                            <div class="activity-item"><i class="fas fa-user-plus me-2 text-info"></i> 42 new users joined experimental maps</div>
                            <div class="activity-item"><i class="fas fa-database me-2 text-secondary"></i> Data ingestion from 7 new corporation sources</div>
                            <div class="activity-item"><i class="fas fa-shield-alt me-2 text-warning"></i> Security compliance verified – zero threats</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="chart-futuristic">
                        <h5><i class="fas fa-rocket me-2 text-accent"></i> Command Center</h5>
                        <div class="row g-3 mt-1">
                            <div class="col-6"><div class="bg-white bg-opacity-10 rounded-4 p-3 text-center"><i class="fas fa-cloud-upload-alt fa-2x"></i><div class="mt-1 small">Sync Live Feeds</div></div></div>
                            <div class="col-6"><div class="bg-white bg-opacity-10 rounded-4 p-3 text-center"><i class="fas fa-chalkboard-user fa-2x"></i><div class="mt-1 small">Insights Studio</div></div></div>
                            <div class="col-6 mt-2"><div class="bg-white bg-opacity-10 rounded-4 p-3 text-center"><i class="fas fa-file-csv fa-2x"></i><div class="mt-1 small">Export Reports</div></div></div>
                            <div class="col-6 mt-2"><div class="bg-white bg-opacity-10 rounded-4 p-3 text-center"><i class="fas fa-bell fa-2x"></i><div class="mt-1 small">Alert Hub</div></div></div>
                        </div>
                        <div class="mt-4 pt-2 border-top border-light"><i class="fas fa-crown me-1 text-warning"></i> Executive summary: KPI up 23% this quarter</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOAST container -->
<div id="toast-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 9999;"></div>

<script>
    // toast system
    window.showToast = function(type, title, message, duration = 4000) {
        let container = document.getElementById('toast-container');
        let toast = document.createElement('div');
        let bgMap = {success: '#2dd4bf', error: '#f43f5e', warning: '#fbbf24', info: '#3b82f6'};
        toast.className = `bg-dark text-white rounded-4 shadow-lg p-3 mb-2 d-flex align-items-start gap-3 animate__animated`;
        toast.style.borderLeft = `5px solid ${bgMap[type] || '#b15eff'}`;
        toast.style.background = 'rgba(12, 18, 28, 0.95)';
        toast.style.backdropFilter = 'blur(12px)';
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type==='error'? 'fa-exclamation-triangle' : 'fa-circle-info'} me-2 fs-5" style="color:${bgMap[type]}"></i>
                           <div><strong>${title}</strong><div class="small">${message}</div></div>
                           <button class="btn-close btn-close-white ms-3" style="font-size: 12px;"></button>`;
        container.appendChild(toast);
        let closeBtn = toast.querySelector('.btn-close');
        closeBtn.onclick = () => { toast.remove(); };
        setTimeout(() => { if(toast) toast.remove(); }, duration);
        return toast;
    };

    // profile dropdown toggle
    const userBtn = document.getElementById('userProfileBtn');
    const profileMenu = document.getElementById('profileDropdownMenu');
    if(userBtn && profileMenu){
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = profileMenu.style.display === 'block';
            profileMenu.style.display = isVisible ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
            if(!userBtn.contains(e.target) && !profileMenu.contains(e.target)) profileMenu.style.display = 'none';
        });
    }

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('mainSidebar');
    if(toggleBtn){
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
        document.addEventListener('click', (e) => {
            if(window.innerWidth <= 1100 && sidebar.classList.contains('mobile-open') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)){
                sidebar.classList.remove('mobile-open');
            }
        });
    }

    // ---------- CHART.JS initializations ----------
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [
                { label: 'Geospatial Revenue (M$)', data: [28, 35, 42, 48, 62, 78, 94], borderColor: '#b15eff', backgroundColor: 'rgba(177,94,255,0.05)', tension: 0.4, fill: true, pointBackgroundColor: '#b15eff', pointBorderColor: '#fff' },
                { label: 'Corporation Insights', data: [18, 22, 29, 38, 49, 58, 71], borderColor: '#00d2ff', borderDash: [5,5], tension: 0.3 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: { color: '#cbd5e1' } } }, scales: { y: { grid: { color: '#2d3748' }, ticks: { color: '#a0aec0' } }, x: { ticks: { color: '#a0aec0' } } } }
    });
    const pieCtx = document.getElementById('sectorPie').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: { labels: ['Energy', 'Tech Hubs', 'Infra', 'Defense'], datasets: [{ data: [34, 28, 22, 16], backgroundColor: ['#b15eff', '#00d2ff', '#ff5e7e', '#fbbf24'], borderWidth: 0 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#ccd6f0' } } } }
    });

    // ---------- OPENLAYERS MAP (fluid, interactive) ----------
    let mapLayer = null;
    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM(),
                visible: true
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([-74.006, 40.7128]), // NYC
            zoom: 12
        })
    });
    // add some markers (sample geojson-like)
    const markerLayer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [
                new ol.Feature({ geometry: new ol.geom.Point(ol.proj.fromLonLat([-74.0445, 40.6892])) }), // statue
                new ol.Feature({ geometry: new ol.geom.Point(ol.proj.fromLonLat([-74.006, 40.7128])) }) // times square
            ]
        }),
        style: new ol.style.Style({
            image: new ol.style.Circle({ radius: 7, fill: new ol.style.Fill({ color: '#ff5e7e' }), stroke: new ol.style.Stroke({ color: 'white', width: 2 }) })
        })
    });
    map.addLayer(markerLayer);
    // layer switcher
    document.getElementById('satelliteBtn')?.addEventListener('click', () => {
        const newLayer = new ol.layer.Tile({
            source: new ol.source.XYZ({ url: 'https://{a-c}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', attributions: 'CartoDB' })
        });
        map.getLayers().clear();
        map.addLayer(newLayer);
        map.addLayer(markerLayer);
        showToast('info', 'Map style', 'Satellite hybrid mode active', 2000);
    });
    document.getElementById('streetBtn')?.addEventListener('click', () => {
        map.getLayers().clear();
        map.addLayer(new ol.layer.Tile({ source: new ol.source.OSM() }));
        map.addLayer(markerLayer);
        showToast('success', 'Streets', 'Standard Street View restored', 2000);
    });

    // ------------------ simulate initial notification & greet ------------------
    window.addEventListener('load', () => {
        showToast('success', 'Orion Vista Online', 'Fully fluid dashboard ready • Geospatial sync active', 4000);
        // Additional interactive for stat cards
        document.querySelectorAll('.stat-neo').forEach((card, idx) => {
            card.addEventListener('click', () => showToast('info', 'Quick Insight', `Module ${idx+1} data ready for deep analysis`, 1800));
        });
        // quick action mock
        document.querySelectorAll('.bg-white.bg-opacity-10').forEach(el => {
            el.addEventListener('click', () => showToast('warning', 'Command Center', 'Interactive module launching soon', 1500));
        });
    });

    // add responsiveness for map reflow on window resize
    window.addEventListener('resize', () => { setTimeout(() => map.updateSize(), 200); });
    // Simple fake realtime update for demo
    setInterval(() => {
        let randomMsg = ['New corporation layer ingested', 'Live user activity peak +7%', 'Geospatial heatmap updated'];
        let msg = randomMsg[Math.floor(Math.random() * randomMsg.length)];
        console.log('Event:', msg);
        // optional extra dynamic: show a silent toast only if you want intrusive but delightful
        // we avoid spamming too much, but can show once every 30 sec.
    }, 30000);
</script>
@yield('script')
</body>
</html>
