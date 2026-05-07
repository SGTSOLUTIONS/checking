@extends('layouts.commissioner')

@section('title', 'Commissioner Dashboard')

@section('content')

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR -->
        <div class="col-auto sidebar min-vh-100" id="sidebar">
            <div class="logo-area text-center">
                <img src="{{ asset('images/coimbatore.jpg') }}" alt="TamilNadu" class="emblem-img"
                    onerror="this.src='https://via.placeholder.com/70x70?text=TN'">
                <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">{{ $corporation->corporation_name }}</h6>
                <small class="text-white-50">e-Governance Suite</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" data-page="dashboard" href="#">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" data-page="corporation" href="#">
                    <i class="fas fa-city"></i> Corporation
                </a>
                <a class="nav-link" data-page="ward" href="#">
                    <i class="fas fa-map-marker-alt"></i> Ward
                </a>
            </nav>
            <div class="mt-auto p-3">
                <hr class="bg-secondary" style="opacity:0.3;">
                <a class="nav-link text-white-50" href="#" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
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
                            <text x="50" y="65" font-size="20" text-anchor="middle" fill="#102C57"
                                font-weight="bold">TN</text>
                        </svg>
                    </div>
                    <div class="dropdown user-dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <span class="fw-semibold" style="color:#102C57;">Commissioner</span>
                                <small class="d-block text-muted">Municipal Commissioner</small>
                            </div>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown"><i
                                        class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Dynamic Content Panels -->
            <div class="p-4">
                <!-- DASHBOARD PANEL -->
                <div id="dashboardPanel" class="content-panel">
                    <div class="animate__animated animate__fadeInUp">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                            <h3 class="fw-bold" style="color:#ffffff;"><i class="fas fa-tachometer-alt me-2"
                                    style="color:#1679AB;"></i> Dashboard Overview</h3>
                            <button class="btn btn-primary px-4 py-2 shadow-sm" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Dashboard
                            </button>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Wards</h6>
                                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($ward_count) }}</h2>
                                        <small class="text-success"><i class="fas fa-check-circle"></i> Active Wards</small>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total MIS Records</h6>
                                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($mis_count) }}</h2>
                                        <small class="text-success"><i class="fas fa-database"></i> Property Tax Records</small>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Zones Active</h6>
                                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format(count($zonesWithWards)) }}</h2>
                                        <small class="text-info"><i class="fas fa-location-dot"></i> Administrative Zones</small>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Collections</h6>
                                        <h2 class="fw-bold mb-0" style="color:#102C57;">{{ number_format(count($collections)) }}</h2>
                                        <small class="text-info"><i class="fas fa-building"></i> Zone & Ward Collections</small>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-city"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- WARD CARDS -->
                        <div class="row">
                            @foreach ($collections as $collection)
                                <div class="col-xl-6 mb-4">
                                    <div class="stat-card p-0">
                                        <div style="background: linear-gradient(135deg, #102C57, #1679AB); color: white; padding: 20px; border-radius: 24px 24px 0 0;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1 fw-bold">Zone {{ $collection['zone'] }}</h5>
                                                    <p class="mb-0 opacity-75">Ward No: <strong>{{ $collection['ward_no'] }}</strong></p>
                                                </div>
                                                <div class="text-end">
                                                    <span style="background: rgba(255,255,255,0.2); padding: 8px 14px; border-radius: 12px;">
                                                        WARD {{ $collection['ward_no'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <div class="row g-3 mb-4">
                                                <div class="col-6">
                                                    <div style="background: #f8fafc; border-radius: 16px; padding: 15px; text-align: center;">
                                                        <i class="fas fa-building fa-2x mb-2" style="color:#1679AB;"></i>
                                                        <h4 class="fw-bold mb-0">{{ number_format($collection['buildingCount']) }}</h4>
                                                        <p class="text-muted mb-0">Total Buildings</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div style="background: #f8fafc; border-radius: 16px; padding: 15px; text-align: center;">
                                                        <i class="fas fa-check-circle fa-2x mb-2" style="color:#28a745;"></i>
                                                        <h4 class="fw-bold mb-0">{{ number_format($collection['surveyedBuildingCount']) }}</h4>
                                                        <p class="text-muted mb-0">Surveyed</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div style="background: #f8fafc; border-radius: 16px; padding: 15px; text-align: center;">
                                                        <i class="fas fa-map-pin fa-2x mb-2" style="color:#17a2b8;"></i>
                                                        <h4 class="fw-bold mb-0">{{ number_format($collection['pointCount']) }}</h4>
                                                        <p class="text-muted mb-0">Points</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div style="background: #f8fafc; border-radius: 16px; padding: 15px; text-align: center;">
                                                        <i class="fas fa-road fa-2x mb-2" style="color:#dc3545;"></i>
                                                        <h4 class="fw-bold mb-0">{{ number_format($collection['roadCount']) }}</h4>
                                                        <p class="text-muted mb-0">Roads</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <a href="{{ route('corporation.commissioner.ward.details', $collection['ward_no']) }}"
                                                   class="btn btn-primary px-4">
                                                    <i class="fas fa-eye me-2"></i> View on Map
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- CORPORATION PANEL -->
                <div id="corporationPanel" class="content-panel" style="display: none;">
                    <div class="animate__animated animate__fadeInUp">
                        <h3 class="fw-bold mb-4" style="color:#ffffff;"><i class="fas fa-city me-2"
                                style="color:#1679AB;"></i> Corporation Overview</h3>
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="stat-card p-4 text-center">
                                    <i class="fas fa-landmark fa-3x mb-3" style="color:#1679AB;"></i>
                                    <h5>{{ $corporation->corporation_name }}</h5>
                                    <p class="text-muted">Zone-wise administration</p>
                                    <span class="badge bg-primary">{{ $ward_count }} Wards</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="stat-card p-4 text-center">
                                    <i class="fas fa-charging-station fa-3x mb-3" style="color:#1679AB;"></i>
                                    <h5>Smart City Projects</h5>
                                    <p class="text-muted">Ongoing development projects</p>
                                    <span class="badge bg-success">In Progress</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="stat-card p-4 text-center">
                                    <i class="fas fa-file-invoice-dollar fa-3x mb-3" style="color:#1679AB;"></i>
                                    <h5>Revenue Dashboard</h5>
                                    <p class="text-muted">{{ number_format($mis_count) }} Tax Records</p>
                                    <span class="badge bg-info">Updated</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card p-4 mt-4">
                            <h5 class="fw-bold"><i class="fas fa-building me-2" style="color:#1679AB;"></i> Zones & Performance</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Zone</th>
                                            <th>Wards</th>
                                            <th>Total Buildings</th>
                                            <th>Roads</th>
                                            <th>Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zonesWithWards as $zone)
                                            @php
                                                $totalBuildings = 0;
                                                $totalRoads = 0;
                                                $totalPoints = 0;
                                                foreach($zone['wards'] as $wardData) {
                                                    $totalBuildings += $wardData['buildingCount'];
                                                }
                                            @endphp
                                            <tr>
                                                <td><strong>Zone {{ $zone['zone'] }}</strong></td>
                                                <td>{{ count($zone['wards']) }}</td>
                                                <td>{{ number_format($totalBuildings) }}</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WARD PANEL -->
                <div id="wardPanel" class="content-panel" style="display: none;">
                    <div class="animate__animated animate__fadeInUp">
                        <h3 class="fw-bold mb-4" style="color:#ffffff;"><i class="fas fa-map-marker-alt me-2"
                                style="color:#1679AB;"></i> Ward Management</h3>
                        <div class="row">
                            <div class="col-12">
                                <div class="stat-card p-4">
                                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                                        <h5 class="fw-bold">All Wards List</h5>
                                        <input type="text" class="form-control w-25" placeholder="Search Ward..." id="wardSearch">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="wardTable">
                                            <thead>
                                                <tr>
                                                    <th>Zone</th>
                                                    <th>Ward No.</th>
                                                    <th>Buildings</th>
                                                    <th>Surveyed</th>
                                                    <th>Points</th>
                                                    <th>Roads</th>
                                                    <th>MIS Records</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($collections as $collection)
                                                    <tr>
                                                        <td>Zone {{ $collection['zone'] }}</td>
                                                        <td><strong>{{ $collection['ward_no'] }}</strong></td>
                                                        <td>{{ number_format($collection['buildingCount']) }}</td>
                                                        <td>{{ number_format($collection['surveyedBuildingCount']) }}</td>
                                                        <td>{{ number_format($collection['pointCount']) }}</td>
                                                        <td>{{ number_format($collection['roadCount']) }}</td>
                                                        <td>{{ number_format($collection['misCount']) }}</td>
                                                        <td>
                                                            <a href="{{ route('corporation.commissioner.ward.details', $collection['ward_no']) }}"
                                                               class="btn btn-sm btn-primary">
                                                                <i class="fas fa-map-marked-alt"></i> View Map
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Sidebar toggle for mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Panel switching logic
    const panels = {
        dashboard: document.getElementById('dashboardPanel'),
        corporation: document.getElementById('corporationPanel'),
        ward: document.getElementById('wardPanel')
    };

    const navLinks = document.querySelectorAll('.sidebar .nav-link');

    function showPanel(panelId) {
        Object.keys(panels).forEach(key => {
            if (panels[key]) panels[key].style.display = 'none';
        });
        if (panels[panelId]) panels[panelId].style.display = 'block';
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-page') === panelId) {
                link.classList.add('active');
            }
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            if (page) showPanel(page);
            if (window.innerWidth < 769) sidebar.classList.remove('show');
        });
    });

    // Logout functionality
    function handleLogout(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = "{{ route('corporation.logout') }}";
        }
    }

    const logoutBtns = document.querySelectorAll('#logoutBtn, #logoutDropdown');
    logoutBtns.forEach(btn => {
        btn.addEventListener('click', handleLogout);
    });

    // Ward search filter
    const wardSearch = document.getElementById('wardSearch');
    if (wardSearch) {
        wardSearch.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#wardTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    showPanel('dashboard');
</script>

@endsection
