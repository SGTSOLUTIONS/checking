@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Mobile Buttons -->
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i></button>
        <button class="mobile-legend-btn" id="mobileLegendBtn"><i class="fas fa-info-circle"></i></button>
        <button class="mobile-search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
        <button class="mobile-filter-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i></button>
        <button class="mobile-location-btn" id="mobileLocationBtn"><i class="fas fa-location-dot"></i></button>
    </div>
@endsection

@push('styles')
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: auto;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100vh;
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* Mobile Buttons */
        .mobile-menu-btn,
        .mobile-legend-btn,
        .mobile-search-btn,
        .mobile-filter-btn,
        .mobile-location-btn {
            position: fixed;
            z-index: 1002;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            font-size: 20px;
        }

        .mobile-menu-btn {
            bottom: 20px;
            right: 20px;
        }

        .mobile-legend-btn {
            bottom: 20px;
            right: 80px;
            background: rgba(255, 193, 7, 0.9);
        }

        .mobile-search-btn {
            bottom: 20px;
            right: 140px;
            background: rgba(23, 162, 184, 0.9);
        }

        .mobile-filter-btn {
            bottom: 20px;
            right: 200px;
            background: rgba(40, 167, 69, 0.9);
        }

        .mobile-location-btn {
            bottom: 20px;
            left: 80px;
            background: rgba(220, 53, 69, 0.9);
        }

        @media (max-width: 768px) {

            .mobile-menu-btn,
            .mobile-legend-btn,
            .mobile-search-btn,
            .mobile-filter-btn,
            .mobile-location-btn {
                display: flex;
            }
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            min-width: 160px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: 80px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                min-width: 200px;
                z-index: 1003;
            }

            .layer-switcher.open {
                transform: translateX(0);
            }
        }

        /* Legend */
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            min-width: 140px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            pointer-events: none;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: 140px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                pointer-events: auto;
                z-index: 1003;
            }

            .map-legend.open {
                transform: translateX(0);
            }
        }

        /* Search Panel - FIXED POSITION */
        .search-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 12px;
            padding: 15px;
            min-width: 320px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .search-panel {
                position: fixed;
                top: auto;
                bottom: 200px;
                left: 20px;
                right: 20px;
                transform: translateY(150%);
                min-width: auto;
                z-index: 1003;
                display: block !important;
            }

            .search-panel.open {
                transform: translateY(0);
            }
        }

        /* Filter Panel */
        .filter-panel {
            position: absolute;
            top: 100px;
            right: 20px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 12px;
            padding: 15px;
            min-width: 250px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .filter-panel {
                position: fixed;
                bottom: 200px;
                right: 20px;
                left: auto;
                top: auto;
                transform: translateX(120%);
                min-width: 220px;
                z-index: 1003;
            }

            .filter-panel.open {
                transform: translateX(0);
            }
        }

        /* Direction Panel */
        .direction-panel {
            position: absolute;
            bottom: 100px;
            left: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 12px;
            padding: 15px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1001;
            display: none;
            max-width: 400px;
        }

        @media (max-width: 768px) {
            .direction-panel {
                bottom: 100px;
                left: 10px;
                right: 10px;
            }
        }

        .direction-panel.show {
            display: block;
            animation: slideUp 0.3s ease;
        }

        .direction-info {
            font-size: 12px;
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .close-direction {
            float: right;
            background: none;
            border: none;
            color: #ff4444;
            font-size: 20px;
            cursor: pointer;
        }

        /* Current Location Marker */
        .current-location-marker {
            width: 20px;
            height: 20px;
            background: #ff4444;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .accuracy-circle {
            background: rgba(255, 68, 68, 0.2);
            border-radius: 50%;
            border: 1px solid rgba(255, 68, 68, 0.5);
        }

        .layer-switcher h5,
        .map-legend h5,
        .search-panel h5,
        .filter-panel h5 {
            margin-bottom: 10px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
            color: #ffc107;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
        }

        .search-box button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background: #ff4444;
            color: white;
            cursor: pointer;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 10px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
        }

        .search-result-item:hover {
            background: rgba(255, 68, 68, 0.3);
        }

        .result-gisid {
            font-weight: bold;
            color: #ffc107;
            font-size: 12px;
        }

        .direction-btn {
            margin-top: 8px;
            padding: 5px 10px;
            background: #28a745;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 11px;
        }

        /* Rest of your existing styles remain the same */
        .layer-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            font-size: 12px;
            cursor: pointer;
        }

        .group-title {
            font-weight: 600;
            color: #ffc107;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border-radius: 3px;
        }

        .legend-color.building {
            background: rgba(255, 68, 68, 0.5);
            border: 2px solid #ff4444;
        }

        .legend-color.road {
            background: none;
            border: 2px solid #ffc107;
            height: 3px;
            margin-top: 8px;
        }

        .legend-color.boundary {
            background: none;
            border: 2px dashed #ff0000;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            overflow: hidden;
            z-index: 1000;
        }

        .zoom-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .map-loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 2000;
        }

        /* Popup styles */
        .ol-popup {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(135deg, #0f0f1a, #1a1a2e);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 0;
            width: 100% !important;
            max-height: 75vh !important;
            z-index: 9999 !important;
            overflow-y: auto;
            animation: slideUp 0.3s ease-out !important;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @media (min-width: 769px) {
            .ol-popup {
                position: absolute !important;
                bottom: auto !important;
                width: auto !important;
                min-width: 380px !important;
                max-width: 450px !important;
                border-radius: 16px !important;
                animation: none !important;
            }
        }

        .popup-header {
            background: linear-gradient(135deg, #1a1a2e, #0f0f1a);
            padding: 16px 18px;
            border-bottom: 2px solid #ff4444;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
        }

        .popup-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
        }

        .popup-tabs {
            display: flex;
            background: #141424;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .popup-tab {
            flex: 1;
            background: none;
            border: none;
            color: #aaa;
            padding: 12px;
            cursor: pointer;
            font-weight: 600;
        }

        .popup-tab.active {
            color: #ff4444;
            border-bottom: 2px solid #ff4444;
        }

        .popup-tab-content {
            display: none;
            padding: 16px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .popup-tab-content.active {
            display: block;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            flex-wrap: wrap;
        }

        .detail-label {
            font-weight: 600;
            color: #ffc107;
            width: 110px;
            font-size: 11px;
        }

        .detail-value {
            color: #eee;
            flex: 1;
            font-size: 12px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 6px;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 3px solid #ffc107;
            cursor: pointer;
        }

        .assessment-header {
            background: rgba(255, 193, 7, 0.15);
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
        }

        .assessment-number {
            font-weight: 700;
            font-size: 12px;
            color: #ffc107;
        }

        .assessment-body {
            padding: 12px;
        }

        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 11px;
            flex-wrap: wrap;
        }

        .assessment-label {
            width: 80px;
            color: #aaa;
        }

        .assessment-value {
            color: #fff;
            flex: 1;
        }

        .shop-item {
            background: rgba(255, 68, 68, 0.1);
            border-radius: 10px;
            padding: 10px;
            margin-top: 8px;
        }

        .shop-name {
            font-weight: 700;
            color: #ff4444;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #888;
        }

        .assessment-form-container {
            margin: 10px;
            padding: 15px;
            background: #1a1a2e;
            border-radius: 12px;
            border-left: 3px solid #ff4444;
        }

        .close-form-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            float: right;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        $(document).ready(function() {
            // ==================== DATA FROM SERVER ====================
            let polygonDatas = @json($polygonDatas ?? []);
            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);
            let wardData = {
                ward_no: @json($ward->ward_no ?? ''),
                drone_image: @json($ward->drone_image ?? null),
                extent_left: @json($ward->extent_left ?? null),
                extent_bottom: @json($ward->extent_bottom ?? null),
                extent_right: @json($ward->extent_right ?? null),
                extent_top: @json($ward->extent_top ?? null),
                boundary: @json($ward->boundary ?? null)
            };

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== LOCATION VARIABLES ====================
            let currentLocationMarker = null;
            let accuracyCircle = null;
            let currentPosition = null;
            let locationTracking = false;
            let watchId = null;

            // ==================== SEARCH & FILTER VARIABLES ====================
            let allBuildings = [];

            // ==================== DIRECTION VARIABLES ====================
            let directionLine = null;
            let destinationMarker = null;

            // ==================== BUILD SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = [];
                $.each(polygonDatas, function(i, building) {
                    let buildingInfo = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        building_type: building.building_type,
                        road_name: building.road_name,
                        zone: building.zone,
                        number_floor: building.number_floor,
                        geometry: null,
                        coordinates: null,
                        assessments: []
                    };

                    // Get geometry coordinates
                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            buildingInfo.geometry = poly.coordinates;
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly
                                    .coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let centerLon = 0,
                                        centerLat = 0;
                                    $.each(coords[0], function(k, coord) {
                                        centerLon += coord[0];
                                        centerLat += coord[1];
                                    });
                                    buildingInfo.coordinates = [centerLon / coords[0].length,
                                        centerLat / coords[0].length
                                    ];
                                }
                            } catch (e) {}
                            return false;
                        }
                    });

                    if (building.pointdata) {
                        $.each(building.pointdata, function(j, assessment) {
                            buildingInfo.assessments.push({
                                assessment_no: assessment.assessment,
                                owner_name: assessment.owner_name || assessment
                                    .present_owner_name,
                                phone: assessment.phone_number
                            });
                        });
                    }
                    allBuildings.push(buildingInfo);
                });
                console.log('Search index built with', allBuildings.length, 'buildings');
            }

            // ==================== LIVE LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    alert("Geolocation is not supported by your browser");
                    return;
                }

                $('#mobileLocationBtn').css('background', '#28a745');
                locationTracking = true;

                // Get current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    updateLocationOnMap(position.coords.longitude, position.coords.latitude, position.coords
                        .accuracy);
                }, function(error) {
                    console.error("Geolocation error:", error);
                    alert("Unable to get your location. Please check permissions.");
                    locationTracking = false;
                    $('#mobileLocationBtn').css('background', 'rgba(220, 53, 69, 0.9)');
                });

                // Watch for position changes
                watchId = navigator.geolocation.watchPosition(function(position) {
                    updateLocationOnMap(position.coords.longitude, position.coords.latitude, position.coords
                        .accuracy);
                }, function(error) {
                    console.error("Watch position error:", error);
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 5000,
                    timeout: 10000
                });
            }

            function stopLocationTracking() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                if (currentLocationMarker) {
                    map.removeLayer(currentLocationMarker);
                    currentLocationMarker = null;
                }
                if (accuracyCircle) {
                    map.removeLayer(accuracyCircle);
                    accuracyCircle = null;
                }
                locationTracking = false;
                $('#mobileLocationBtn').css('background', 'rgba(220, 53, 69, 0.9)');
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coordinates = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                // Remove old marker
                if (currentLocationMarker) {
                    map.removeLayer(currentLocationMarker);
                }
                if (accuracyCircle) {
                    map.removeLayer(accuracyCircle);
                }

                // Create accuracy circle
                let radiusInMeters = accuracy;
                let radiusInDegrees = radiusInMeters / 111320;
                let circleGeometry = new ol.geom.Circle(ol.proj.fromLonLat([lon, lat]), radiusInMeters);

                accuracyCircle = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: circleGeometry
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 68, 68, 0.15)'
                        })
                    })
                });
                map.addLayer(accuracyCircle);

                // Create location marker
                let markerElement = $('<div>', {
                    class: 'current-location-marker'
                })[0];

                currentLocationMarker = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(coordinates)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 10,
                            fill: new ol.style.Fill({
                                color: '#ff4444'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#ffffff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(currentLocationMarker);

                // Auto center on first location
                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coordinates);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            function centerToCurrentLocation() {
                if (currentPosition) {
                    let coordinates = ol.proj.fromLonLat(currentPosition);
                    map.getView().setCenter(coordinates);
                    map.getView().setZoom(18);
                } else {
                    startLocationTracking();
                    setTimeout(() => {
                        if (currentPosition) {
                            let coordinates = ol.proj.fromLonLat(currentPosition);
                            map.getView().setCenter(coordinates);
                            map.getView().setZoom(18);
                        }
                    }, 2000);
                }
            }

            // ==================== DIRECTION TO BUILDING ====================
            function showDirectionToBuilding(buildingGisid, buildingCoords) {
                if (!currentPosition) {
                    alert(
                        "Please enable location tracking first. Click the location button to get your current location.");
                    startLocationTracking();
                    return;
                }

                // Remove existing direction line
                if (directionLine) {
                    map.removeLayer(directionLine);
                }
                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                }

                let fromLonLat = currentPosition;
                let toLonLat = buildingCoords;

                // Create line between current location and building
                let fromPoint = ol.proj.fromLonLat(fromLonLat);
                let toPoint = ol.proj.fromLonLat(toLonLat);

                let lineGeometry = new ol.geom.LineString([fromPoint, toPoint]);

                directionLine = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: lineGeometry
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#28a745',
                            width: 4,
                            lineDash: [10, 10]
                        })
                    })
                });
                map.addLayer(directionLine);

                // Create destination marker
                let destMarkerElement = $('<div>', {
                    class: 'destination-marker',
                    style: 'width: 30px; height: 30px; background: #28a745; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 10px rgba(0,0,0,0.5);'
                })[0];

                destinationMarker = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(toPoint)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 15,
                            fill: new ol.style.Fill({
                                color: '#28a745'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#ffffff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(destinationMarker);

                // Calculate distance
                let distance = calculateDistance(fromLonLat[0], fromLonLat[1], toLonLat[0], toLonLat[1]);
                let bearing = calculateBearing(fromLonLat[0], fromLonLat[1], toLonLat[0], toLonLat[1]);

                // Show direction panel
                let directionHtml = `
                    <div class="direction-panel show" id="directionPanel">
                        <button class="close-direction" onclick="$('#directionPanel').remove();">&times;</button>
                        <h5><i class="fas fa-directions"></i> Direction to Building</h5>
                        <div class="direction-info">
                            <p><strong>GIS ID:</strong> ${buildingGisid}</p>
                            <p><strong>Distance:</strong> ${distance.toFixed(2)} km (${(distance * 0.621371).toFixed(2)} miles)</p>
                            <p><strong>Bearing:</strong> ${bearing.toFixed(0)}° (${getDirectionName(bearing)})</p>
                            <p><strong>Estimated walking time:</strong> ${Math.round(distance / 5 * 60)} minutes</p>
                            <p><strong>Estimated driving time:</strong> ${Math.round(distance / 40 * 60)} minutes</p>
                        </div>
                        <button id="fitBothBtn" style="width:100%; margin-top:10px; padding:8px; background:#ff4444; border:none; border-radius:8px; color:white; cursor:pointer;">
                            <i class="fas fa-map-marked-alt"></i> Show Full Route
                        </button>
                    </div>
                `;

                // Remove existing panel
                $('#directionPanel').remove();
                $('body').append(directionHtml);

                // Fit both locations in view
                $('#fitBothBtn').on('click', function() {
                    let extent = ol.extent.boundingExtent([fromPoint, toPoint]);
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                });

                // Fit both locations in view
                let extent = ol.extent.boundingExtent([fromPoint, toPoint]);
                map.getView().fit(extent, {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });
            }

            function calculateDistance(lon1, lat1, lon2, lat2) {
                let R = 6371; // Earth's radius in km
                let dLat = (lat2 - lat1) * Math.PI / 180;
                let dLon = (lon2 - lon1) * Math.PI / 180;
                let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            function calculateBearing(lon1, lat1, lon2, lat2) {
                let lat1Rad = lat1 * Math.PI / 180;
                let lat2Rad = lat2 * Math.PI / 180;
                let dLon = (lon2 - lon1) * Math.PI / 180;
                let y = Math.sin(dLon) * Math.cos(lat2Rad);
                let x = Math.cos(lat1Rad) * Math.sin(lat2Rad) -
                    Math.sin(lat1Rad) * Math.cos(lat2Rad) * Math.cos(dLon);
                let bearing = Math.atan2(y, x) * 180 / Math.PI;
                return (bearing + 360) % 360;
            }

            function getDirectionName(bearing) {
                let directions = ['North', 'North-East', 'East', 'South-East', 'South', 'South-West', 'West',
                    'North-West'
                ];
                let index = Math.round(bearing / 45) % 8;
                return directions[index];
            }

            // ==================== SEARCH FUNCTION ====================
            function searchBuildings(searchText) {
                if (!searchText || searchText.trim() === '') {
                    $('#searchResults').html(
                        '<div class="no-results"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }
                let term = searchText.toLowerCase().trim();
                let results = [];
                $.each(allBuildings, function(i, building) {
                    let matchFound = false;
                    let matchType = '';
                    let matchValue = '';
                    if (building.gisid && building.gisid.toLowerCase().includes(term)) {
                        matchFound = true;
                        matchType = 'GIS ID';
                        matchValue = building.gisid;
                    } else if (building.building_usage && building.building_usage.toLowerCase().includes(
                            term)) {
                        matchFound = true;
                        matchType = 'Building Usage';
                        matchValue = building.building_usage;
                    } else if (building.road_name && building.road_name.toLowerCase().includes(term)) {
                        matchFound = true;
                        matchType = 'Road Name';
                        matchValue = building.road_name;
                    } else {
                        $.each(building.assessments, function(j, a) {
                            if (a.assessment_no && a.assessment_no.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Assessment No';
                                matchValue = a.assessment_no;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Owner Name';
                                matchValue = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Phone';
                                matchValue = a.phone;
                                return false;
                            }
                        });
                    }
                    if (matchFound) {
                        results.push({
                            gisid: building.gisid,
                            matchType: matchType,
                            matchValue: matchValue,
                            building: building,
                            coordinates: building.coordinates
                        });
                    }
                });
                displaySearchResults(results);
            }

            // ==================== DISPLAY SEARCH RESULTS ====================
            function displaySearchResults(results) {
                let $results = $('#searchResults');
                $results.empty();
                if (results.length === 0) {
                    $results.html(
                        '<div class="no-results"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }
                $.each(results, function(i, result) {
                    let html = `
                        <div class="search-result-item" data-gisid="${result.gisid}" data-lon="${result.coordinates ? result.coordinates[0] : ''}" data-lat="${result.coordinates ? result.coordinates[1] : ''}">
                            <div class="result-gisid"><i class="fas fa-building"></i> ${result.gisid}</div>
                            <div class="result-owner"><i class="fas fa-tag"></i> Match: ${result.matchType} - ${result.matchValue}</div>
                            <div class="result-owner"><i class="fas fa-location-dot"></i> ${result.building.road_name || 'No road'} | ${result.building.zone || 'No zone'}</div>
                            <button class="direction-btn" data-gisid="${result.gisid}" data-lon="${result.coordinates ? result.coordinates[0] : ''}" data-lat="${result.coordinates ? result.coordinates[1] : ''}">
                                <i class="fas fa-directions"></i> Get Directions
                            </button>
                        </div>
                    `;
                    $results.append(html);
                });

                // Click on result to zoom
                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn')) {
                        let gisid = $(this).data('gisid');
                        zoomToBuilding(gisid);
                        $('#searchPanel').removeClass('open');
                    }
                });

                // Direction button click
                $('.direction-btn').off('click').on('click', function(e) {
                    e.stopPropagation();
                    let gisid = $(this).data('gisid');
                    let lon = $(this).data('lon');
                    let lat = $(this).data('lat');
                    if (lon && lat) {
                        showDirectionToBuilding(gisid, [parseFloat(lon), parseFloat(lat)]);
                        $('#searchPanel').removeClass('open');
                    } else {
                        alert("Building coordinates not available");
                    }
                });
            }

            // ==================== ZOOM TO BUILDING ====================
            function zoomToBuilding(gisid) {
                let source = polygonLayer.getSource();
                let features = source.getFeatures();
                let targetFeature = null;
                $.each(features, function(i, feature) {
                    if (feature.get('gisid') == gisid) {
                        targetFeature = feature;
                        return false;
                    }
                });
                if (targetFeature) {
                    let extent = targetFeature.getGeometry().getExtent();
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                    let center = ol.extent.getCenter(extent);
                    showPopup(gisid, center);
                }
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', {
                    class: 'ol-popup',
                    style: 'display:none'
                })[0];
                $('body').append(popupElement);
                popupOverlay = new ol.Overlay({
                    element: popupElement,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -10],
                    autoPan: {
                        animation: {
                            duration: 250
                        }
                    }
                });
                return popupOverlay;
            }

            window.closePopup = function() {
                $('.ol-popup').hide();
            };

            window.switchTab = function(tabId) {
                $('.popup-tab-content').removeClass('active');
                $('.popup-tab').removeClass('active');
                $('#tab-' + tabId).addClass('active');
                $('.popup-tab[data-tab="' + tabId + '"]').addClass('active');
                currentActiveTab = tabId;
            };

            // ==================== REST OF YOUR EXISTING FUNCTIONS ====================
            // (showPopup, applyFilters, resetFilters, initMap, etc. remain the same)
            // I'm including the essential ones here, but keep your existing code for:
            // showPopup, applyFilters, resetFilters, initMap, addLayerSwitcher,
            // addLegend, addZoomControls, addMobileControls, polygonStyleFunction, refreshLayers

            // ... (keep all your existing functions here) ...

            // ==================== MODIFIED MOBILE CONTROLS WITH SEARCH PANEL FIX ====================
            function addMobileControls() {
                $('#mobileMenuBtn').on('click', function() {
                    $('#layerSwitcher').toggleClass('open');
                    $('#mapLegend').removeClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#filterPanel').removeClass('open');
                });
                $('#mobileLegendBtn').on('click', function() {
                    $('#mapLegend').toggleClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#filterPanel').removeClass('open');
                });
                $('#mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#searchPanel').toggleClass('open');
                    $('#filterPanel').removeClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#mapLegend').removeClass('open');
                });
                $('#mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#filterPanel').toggleClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#mapLegend').removeClass('open');
                });
                $('#mobileLocationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                        $(this).css('background', 'rgba(220, 53, 69, 0.9)');
                    } else {
                        startLocationTracking();
                        $(this).css('background', '#28a745');
                        setTimeout(() => centerToCurrentLocation(), 1000);
                    }
                });

                $(document).on('click', function(e) {
                    if ($(window).width() <= 768) {
                        if (!$('#layerSwitcher').is(e.target) && !$('#layerSwitcher').has(e.target)
                            .length && !$('#mobileMenuBtn').is(e.target))
                            $('#layerSwitcher').removeClass('open');
                        if (!$('#mapLegend').is(e.target) && !$('#mapLegend').has(e.target).length && !$(
                                '#mobileLegendBtn').is(e.target))
                            $('#mapLegend').removeClass('open');
                        if (!$('#searchPanel').is(e.target) && !$('#searchPanel').has(e.target).length && !
                            $('#mobileSearchBtn').is(e.target))
                            $('#searchPanel').removeClass('open');
                        if (!$('#filterPanel').is(e.target) && !$('#filterPanel').has(e.target).length && !
                            $('#mobileFilterBtn').is(e.target))
                            $('#filterPanel').removeClass('open');
                    }
                });
            }

            // Initialize everything
            function init() {
                initMap();
                buildSearchIndex();
            }

            init();
        });
    </script>
@endpush
