@extends('layouts.commissioner')

@section('title', 'Ward ' . $ward->ward_no . ' Details')

@section('content')

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

<style>
    .map-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
    }

    #wardMap {
        height: 550px;
        width: 100%;
    }

    .info-panel {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .info-panel h6 {
        color: #1679AB;
        font-weight: 700;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFCBCB;
    }

    .building-item, .road-item {
        padding: 10px;
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s;
        border-radius: 10px;
    }

    .building-item:hover, .road-item:hover {
        background: linear-gradient(135deg, #FFF9F9, white);
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(22, 121, 171, 0.1);
    }

    .btn-back {
        background: white;
        color: #102C57;
        border: none;
        padding: 8px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #FFCBCB;
        transform: translateX(-3px);
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 320px;
        height: 100vh;
        overflow-y: auto;
    }

    .main-content {
        margin-left: 320px;
    }

    @media (max-width: 768px) {
        .sidebar {
            left: -320px;
        }
        .sidebar.show {
            left: 0;
        }
        .main-content {
            margin-left: 0;
        }
    }
</style>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="logo-area text-center">
                <img src="{{ asset('images/coimbatore.jpg') }}" alt="TamilNadu" class="emblem-img"
                    onerror="this.src='https://via.placeholder.com/70x70?text=TN'">
                <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">{{ $corporation->corporation_name }}</h6>
                <small class="text-white-50">e-Governance Suite</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('corporation.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="{{ route('corporation.dashboard') }}#corporation">
                    <i class="fas fa-city"></i> Corporation
                </a>
                <a class="nav-link active" href="#">
                    <i class="fas fa-map-marker-alt"></i> Ward Details
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
        <div class="main-content">
            <div class="p-4">
                <!-- Top Navbar -->
                <nav class="navbar-custom d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <button class="btn btn-outline-secondary menu-toggle me-2" id="menuToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <a href="{{ route('corporation.dashboard') }}" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
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
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Buildings</h6>
                                <h3 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalBuildings) }}</h3>
                                <small class="text-success">With GIS Data</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-building"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Roads</h6>
                                <h3 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalRoads) }}</h3>
                                <small class="text-success">Network Length</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-road"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Points of Interest</h6>
                                <h3 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($totalPoints) }}</h3>
                                <small class="text-info">Landmarks</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-map-pin"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">GIS ID Count</h6>
                                <h3 class="fw-bold mb-0" style="color:#102C57;">{{ number_format($gisIdCount) }}</h3>
                                <small class="text-success">Registered Properties</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-qrcode"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Map and Info Row -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="map-container">
                            <div id="wardMap"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info-panel">
                            <h6><i class="fas fa-building me-2"></i> Buildings with GIS ID</h6>
                            <div style="max-height: 250px; overflow-y: auto;">
                                @forelse($polygons as $polygon)
                                    @if(!empty($polygon->gisid))
                                        <div class="building-item" onclick="zoomToFeature({{ $polygon->id }}, 'polygon')">
                                            <strong><i class="fas fa-home me-2" style="color:#1679AB;"></i>GIS ID:</strong> {{ $polygon->gisid }}<br>
                                            <small class="text-muted">Owner: {{ $polygon->owner_name ?? 'N/A' }}</small>
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-muted text-center py-3">No GIS ID data available</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="info-panel">
                            <h6><i class="fas fa-road me-2"></i> Roads</h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @forelse($roads as $road)
                                    @if(!empty($road->road_name))
                                        <div class="road-item" onclick="zoomToFeature({{ $road->id }}, 'road')">
                                            <strong><i class="fas fa-road me-2" style="color:#FFB1B1;"></i>Road:</strong> {{ $road->road_name }}
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-muted text-center py-3">No road data available</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="info-panel">
                            <h6><i class="fas fa-map-pin me-2"></i> Points of Interest</h6>
                            <div style="max-height: 150px; overflow-y: auto;">
                                @forelse($points as $point)
                                    <div class="building-item" onclick="zoomToFeature({{ $point->id }}, 'point')">
                                        <strong><i class="fas fa-map-marker-alt me-2" style="color:#1679AB;"></i>GIS ID:</strong> {{ $point->gisid ?? 'N/A' }}<br>
                                        <small>Door No: {{ $point->new_door_no ?? 'N/A' }}</small>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">No point data available</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize map
    var map = L.map('wardMap').setView([11.0168, 76.9558], 14);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    var polygonLayer = L.layerGroup().addTo(map);
    var roadLayer = L.layerGroup().addTo(map);
    var pointLayer = L.layerGroup().addTo(map);
    var featureBounds = {};
    var firstFeature = true;

    // Load polygons
    var polygons = @json($polygons);
    polygons.forEach(function(polygon) {
        if (polygon.geojson) {
            try {
                var geojson = JSON.parse(polygon.geojson);
                var layer = L.geoJSON(geojson, {
                    style: { color: '#1679AB', weight: 2, fillColor: '#FFB1B1', fillOpacity: 0.5 },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup('<strong>GIS ID:</strong> ' + (polygon.gisid || 'N/A') + '<br><strong>Owner:</strong> ' + (polygon.owner_name || 'N/A'));
                        if (layer.getBounds) {
                            featureBounds['polygon_' + polygon.id] = layer.getBounds();
                            if (firstFeature) { map.fitBounds(layer.getBounds()); firstFeature = false; }
                        }
                    }
                }).addTo(polygonLayer);
            } catch(e) { console.error(e); }
        }
    });

    // Load roads
    var roads = @json($roads);
    roads.forEach(function(road) {
        if (road.geojson) {
            try {
                var geojson = JSON.parse(road.geojson);
                var layer = L.geoJSON(geojson, {
                    style: { color: '#FFB1B1', weight: 4, opacity: 0.8 },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup('<strong>Road:</strong> ' + (road.road_name || 'N/A'));
                        if (layer.getBounds) featureBounds['road_' + road.id] = layer.getBounds();
                    }
                }).addTo(roadLayer);
            } catch(e) { console.error(e); }
        }
    });

    // Load points
    var points = @json($points);
    points.forEach(function(point) {
        if (point.geojson) {
            try {
                var geojson = JSON.parse(point.geojson);
                var layer = L.geoJSON(geojson, {
                    pointToLayer: function(feature, latlng) {
                        return L.circleMarker(latlng, { radius: 8, fillColor: "#1679AB", color: "#fff", weight: 2, fillOpacity: 0.8 });
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup('<strong>GIS ID:</strong> ' + (point.gisid || 'N/A') + '<br><strong>Door No:</strong> ' + (point.new_door_no || 'N/A'));
                        if (layer.getLatLng) featureBounds['point_' + point.id] = L.latLngBounds([layer.getLatLng()]);
                    }
                }).addTo(pointLayer);
            } catch(e) { console.error(e); }
        }
    });

    function zoomToFeature(id, type) {
        var bounds = featureBounds[type + '_' + id];
        if (bounds) map.fitBounds(bounds, { padding: [50, 50] });
    }

    window.zoomToFeature = zoomToFeature;

    L.control.layers(null, {
        'Buildings': polygonLayer,
        'Roads': roadLayer,
        'Points': pointLayer
    }, { collapsed: false }).addTo(map);

    // Mobile sidebar toggle
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Logout
    function handleLogout(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = "{{ route('corporation.logout') }}";
        }
    }
    document.getElementById('logoutBtn')?.addEventListener('click', handleLogout);
    document.getElementById('logoutDropdown')?.addEventListener('click', handleLogout);
</script>

@endsection
