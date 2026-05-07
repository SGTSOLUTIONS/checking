@extends('layouts.commissioner')

@section('title', 'Ward Details - Ward ' . $ward->ward_no)

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ward {{ $ward->ward_no }} Details | {{ $corporation->corporation_name }}</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

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
            --sidebar-bg: linear-gradient(180deg, #102C57 0%, #0A1F3F 100%);
        }

        body {
            background: linear-gradient(135deg, #102C57 0%, #1679AB 100%);
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 320px;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #FFCBCB;
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: #1679AB;
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: #1679AB;
            color: white;
        }

        .sidebar .nav-link i {
            width: 28px;
            margin-right: 10px;
        }

        .sidebar .logo-area {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255, 177, 177, 0.3);
            margin-bottom: 20px;
        }

        /* Zone Section Styles */
        .zone-section {
            margin-bottom: 20px;
        }

        .zone-title {
            padding: 10px 20px;
            background: rgba(22, 121, 171, 0.3);
            margin: 10px 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ward-link {
            padding-left: 50px !important;
            font-size: 14px;
        }

        .ward-link.active {
            background: #1679AB;
        }

        /* Main Content */
        .main-content {
            margin-left: 320px;
            padding: 20px;
        }

        /* Top Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        /* Map Container */
        .map-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        }

        #wardMap {
            height: 600px;
            width: 100%;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(22, 121, 171, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1679AB;
        }

        .info-panel {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-panel h6 {
            color: #1679AB;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .building-item, .road-item {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.3s;
        }

        .building-item:hover, .road-item:hover {
            background: #f8fafc;
            transform: translateX(5px);
        }

        .menu-toggle {
            display: none;
            background: #1679AB;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
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
            .menu-toggle {
                display: block;
            }
        }

        .badge-custom {
            background: #1679AB;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="logo-area text-center">
                <img src="{{ asset('images/coimbatore.jpg') }}" alt="TamilNadu" class="emblem-img"
                     onerror="this.src='https://via.placeholder.com/70x70?text=TN'" style="width: 70px;">
                <h6 class="fw-bold mb-0 mt-2" style="color: #FFCBCB;">{{ $corporation->corporation_name }}</h6>
                <small class="text-white-50">e-Governance Suite</small>
            </div>

            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('corporation.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                @foreach($zonesWithWards as $zoneData)
                    <div class="zone-section">
                        <div class="zone-title">
                            <i class="fas fa-location-dot me-2"></i> Zone {{ $zoneData['zone'] }}
                        </div>
                        @foreach($zoneData['wards'] as $wardData)
                            <a class="nav-link ward-link {{ $ward->ward_no == $wardData['ward_no'] ? 'active' : '' }}"
                               href="{{ route('corporation.commissioner.ward.details', $wardData['ward_no']) }}">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Ward {{ $wardData['ward_no'] }}
                                <small class="d-block text-white-50" style="font-size: 11px;">
                                    {{ $wardData['buildingCount'] }} Buildings
                                </small>
                            </a>
                        @endforeach
                    </div>
                @endforeach
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
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i> Menu
            </button>

            <!-- Top Navbar -->
            <nav class="navbar-custom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #102C57;">
                        <i class="fas fa-map-marked-alt me-2" style="color: #1679AB;"></i>
                        Ward {{ $ward->ward_no }} - Zone {{ $ward->zone }}
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="fw-semibold" style="color:#102C57;">Commissioner</span>
                </div>
            </nav>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Buildings</h6>
                            <h3 class="fw-bold mb-0">{{ number_format(count($polygons)) }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Roads</h6>
                            <h3 class="fw-bold mb-0">{{ number_format(count($roads)) }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-road"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Points of Interest</h6>
                            <h3 class="fw-bold mb-0">{{ number_format(count($points)) }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-map-pin"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">GIS ID Count</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($polygons->where('gisid', '!=', null)->count()) }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
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
                    <!-- Building Information Panel -->
                    <div class="info-panel">
                        <h6><i class="fas fa-building me-2"></i> Buildings with GIS ID</h6>
                        <div style="max-height: 250px; overflow-y: auto;">
                            @forelse($polygons as $polygon)
                                @if($polygon->gisid)
                                    <div class="building-item" onclick="zoomToFeature({{ $polygon->id }}, 'polygon')">
                                        <strong>GIS ID:</strong> {{ $polygon->gisid }}<br>
                                        <small class="text-muted">Owner: {{ $polygon->owner_name ?? 'N/A' }}</small>
                                    </div>
                                @endif
                            @empty
                                <p class="text-muted">No GIS ID data available</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Road Information Panel -->
                    <div class="info-panel">
                        <h6><i class="fas fa-road me-2"></i> Roads</h6>
                        <div style="max-height: 200px; overflow-y: auto;">
                            @forelse($roads as $road)
                                @if($road->road_name)
                                    <div class="road-item" onclick="zoomToFeature({{ $road->id }}, 'road')">
                                        <strong>Road:</strong> {{ $road->road_name }}
                                    </div>
                                @endif
                            @empty
                                <p class="text-muted">No road data available</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Points Information Panel -->
                    <div class="info-panel">
                        <h6><i class="fas fa-map-pin me-2"></i> Points of Interest</h6>
                        <div style="max-height: 150px; overflow-y: auto;">
                            @forelse($points as $point)
                                <div class="building-item" onclick="zoomToFeature({{ $point->id }}, 'point')">
                                    <strong>GIS ID:</strong> {{ $point->gisid ?? 'N/A' }}<br>
                                    <small>Door No: {{ $point->new_door_no ?? 'N/A' }}</small>
                                </div>
                            @empty
                                <p class="text-muted">No point data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize map
    var map = L.map('wardMap').setView([11.0168, 76.9558], 13);

    // Add tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Store layers
    var polygonLayer = L.layerGroup().addTo(map);
    var roadLayer = L.layerGroup().addTo(map);
    var pointLayer = L.layerGroup().addTo(map);

    // Store feature coordinates for zoom
    var featureBounds = {};

    // Load polygons
    var polygons = @json($polygons);
    polygons.forEach(function(polygon) {
        if (polygon.geojson) {
            try {
                var geojson = JSON.parse(polygon.geojson);
                var layer = L.geoJSON(geojson, {
                    style: {
                        color: '#1679AB',
                        weight: 2,
                        fillColor: '#FFB1B1',
                        fillOpacity: 0.5
                    },
                    onEachFeature: function(feature, layer) {
                        var popupContent = '<div class="popup-content">' +
                            '<strong>GIS ID:</strong> ' + (polygon.gisid || 'N/A') + '<br>' +
                            '<strong>Owner:</strong> ' + (polygon.owner_name || 'N/A') + '<br>' +
                            '<strong>ID:</strong> ' + polygon.id +
                            '</div>';
                        layer.bindPopup(popupContent);

                        // Store bounds for zoom
                        if (layer.getBounds) {
                            featureBounds['polygon_' + polygon.id] = layer.getBounds();
                        }
                    }
                }).addTo(polygonLayer);
            } catch(e) {
                console.error('Error parsing polygon GeoJSON:', e);
            }
        }
    });

    // Load roads
    var roads = @json($roads);
    roads.forEach(function(road) {
        if (road.geojson) {
            try {
                var geojson = JSON.parse(road.geojson);
                var layer = L.geoJSON(geojson, {
                    style: {
                        color: '#FFB1B1',
                        weight: 3,
                        opacity: 0.8
                    },
                    onEachFeature: function(feature, layer) {
                        var popupContent = '<div class="popup-content">' +
                            '<strong>Road Name:</strong> ' + (road.road_name || 'N/A') + '<br>' +
                            '<strong>ID:</strong> ' + road.id +
                            '</div>';
                        layer.bindPopup(popupContent);

                        if (layer.getBounds) {
                            featureBounds['road_' + road.id] = layer.getBounds();
                        }
                    }
                }).addTo(roadLayer);
            } catch(e) {
                console.error('Error parsing road GeoJSON:', e);
            }
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
                        return L.circleMarker(latlng, {
                            radius: 8,
                            fillColor: "#1679AB",
                            color: "#fff",
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        });
                    },
                    onEachFeature: function(feature, layer) {
                        var popupContent = '<div class="popup-content">' +
                            '<strong>GIS ID:</strong> ' + (point.gisid || 'N/A') + '<br>' +
                            '<strong>Door No:</strong> ' + (point.new_door_no || 'N/A') + '<br>' +
                            '<strong>Owner:</strong> ' + (point.owner_name || 'N/A') +
                            '</div>';
                        layer.bindPopup(popupContent);

                        if (layer.getLatLng) {
                            featureBounds['point_' + point.id] = L.latLngBounds([layer.getLatLng()]);
                        }
                    }
                }).addTo(pointLayer);
            } catch(e) {
                console.error('Error parsing point GeoJSON:', e);
            }
        }
    });

    // Zoom to feature function
    function zoomToFeature(id, type) {
        var bounds = featureBounds[type + '_' + id];
        if (bounds) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    // Layer control
    L.control.layers(null, {
        'Buildings': polygonLayer,
        'Roads': roadLayer,
        'Points': pointLayer
    }, { collapsed: false }).addTo(map);

    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Logout functionality
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = "{{ route('corporation.logout') }}";
        }
    });
</script>

</body>
</html>

@endsection
