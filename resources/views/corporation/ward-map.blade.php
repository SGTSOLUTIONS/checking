@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Mobile Buttons -->
        <button class="mobile-btn menu-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i></button>
        <button class="mobile-btn legend-btn" id="mobileLegendBtn"><i class="fas fa-info-circle"></i></button>
        <button class="mobile-btn search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
        <button class="mobile-btn filter-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i></button>
        <button class="mobile-btn location-btn" id="mobileLocationBtn"><i class="fas fa-location-dot"></i></button>
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
            overflow: hidden;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100vh;
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* Mobile Buttons */
        .mobile-btn {
            position: fixed;
            z-index: 1002;
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
            transition: all 0.2s ease;
        }

        .mobile-btn:active {
            transform: scale(0.95);
        }

        .menu-btn {
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
        }

        .legend-btn {
            bottom: 20px;
            right: 80px;
            background: rgba(255, 193, 7, 0.9);
        }

        .search-btn {
            bottom: 20px;
            right: 140px;
            background: rgba(23, 162, 184, 0.9);
        }

        .filter-btn {
            bottom: 20px;
            right: 200px;
            background: rgba(40, 167, 69, 0.9);
        }

        .location-btn {
            bottom: 20px;
            left: 20px;
            background: rgba(220, 53, 69, 0.9);
        }

        @media (max-width: 768px) {
            .mobile-btn {
                display: flex;
            }
        }

        /* Panels */
        .panel {
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px;
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 68, 68, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .panel h5 {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            color: #ffc107;
            border-bottom: 2px solid #ff4444;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            min-width: 180px;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: 80px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .layer-switcher.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        /* Legend */
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            min-width: 150px;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: 140px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                pointer-events: auto;
            }

            .map-legend.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        /* Search Panel */
        .search-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
        }

        @media (max-width: 768px) {
            .search-panel {
                position: fixed;
                top: auto;
                bottom: 100px;
                left: 20px;
                right: 20px;
                width: auto;
                transform: translateY(150%);
                opacity: 0;
                visibility: hidden;
            }

            .search-panel.open {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }
        }

        /* Filter Panel */
        .filter-panel {
            position: absolute;
            top: 100px;
            right: 20px;
            width: 280px;
        }

        @media (max-width: 768px) {
            .filter-panel {
                position: fixed;
                bottom: 100px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                opacity: 0;
                visibility: hidden;
            }

            .filter-panel.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        /* Direction Panel */
        .direction-panel {
            position: fixed;
            bottom: 100px;
            left: 20px;
            right: 20px;
            max-width: 400px;
            display: none;
            z-index: 1003;
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

        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .search-box {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 14px;
            outline: none;
        }

        .search-box button {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            background: #ff4444;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }

        .search-results {
            max-height: 350px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            cursor: pointer;
            border-left: 3px solid #ffc107;
        }

        .result-gisid {
            font-weight: bold;
            color: #ffc107;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .direction-btn {
            margin-top: 8px;
            padding: 6px 12px;
            background: #28a745;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-size: 11px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: #ffc107;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .apply-btn,
        .reset-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .apply-btn {
            background: #28a745;
            color: white;
        }

        .reset-btn {
            background: #dc3545;
            color: white;
        }

        .filter-count {
            margin-top: 12px;
            font-size: 12px;
            color: #ffc107;
            text-align: center;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 80px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
            z-index: 1000;
        }

        .zoom-btn {
            width: 45px;
            height: 45px;
            border: none;
            background: transparent;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .layer-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            font-size: 13px;
            cursor: pointer;
        }

        .group-title {
            font-weight: 600;
            color: #ffc107;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 4px;
        }

        .legend-color.building {
            background: rgba(255, 68, 68, 0.5);
            border: 2px solid #ff4444;
        }

        .legend-color.road {
            background: none;
            border: 2px solid #ffc107;
            height: 3px;
            width: 24px;
            margin-top: 10px;
        }

        .legend-color.boundary {
            background: none;
            border: 2px dashed #ff0000;
        }

        .legend-color.route {
            background: #28a745;
            border: 2px solid #28a745;
        }

        .map-loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.95);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            z-index: 2000;
        }

        /* Popup */
        .ol-popup {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(135deg, #0f0f1a, #1a1a2e);
            color: white;
            border-radius: 25px 25px 0 0 !important;
            padding: 0;
            width: 100% !important;
            max-height: 70vh !important;
            z-index: 9999 !important;
            overflow-y: auto;
            animation: slideUpPopup 0.3s ease-out !important;
        }

        @keyframes slideUpPopup {
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
                min-width: 400px !important;
                max-width: 500px !important;
                border-radius: 20px !important;
                animation: none !important;
            }

            .ol-popup:after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                border-width: 10px 10px 0;
                border-style: solid;
                border-color: #1a1a2e transparent transparent;
            }
        }

        .popup-header {
            background: linear-gradient(135deg, #1a1a2e, #0f0f1a);
            padding: 18px 20px;
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
            width: 35px;
            height: 35px;
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
            padding: 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .popup-tab.active {
            color: #ff4444;
            border-bottom: 3px solid #ff4444;
        }

        .popup-tab-content {
            display: none;
            padding: 20px;
            max-height: 55vh;
            overflow-y: auto;
        }

        .popup-tab-content.active {
            display: block;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-wrap: wrap;
        }

        .detail-label {
            font-weight: 600;
            color: #ffc107;
            width: 110px;
            font-size: 12px;
        }

        .detail-value {
            color: #eee;
            flex: 1;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
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
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .assessment-number {
            font-weight: 700;
            font-size: 13px;
            color: #ffc107;
        }

        .assessment-body {
            padding: 12px 15px;
        }

        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 12px;
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
            padding: 12px;
            margin-top: 8px;
            border: 1px solid rgba(255, 68, 68, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
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

        .route-info {
            background: #28a745;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            text-align: center;
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
            let currentLocationLayer = null,
                accuracyLayer = null,
                currentPosition = null;
            let locationTracking = false,
                watchId = null;

            // ==================== ROUTE VARIABLES ====================
            let routeLayer = null,
                startMarker = null,
                endMarker = null;
            let routeCoordinates = [];

            // ==================== SEARCH VARIABLES ====================
            let allBuildings = [];

            // ==================== OPENROUTESERVICE API KEY ====================
            // Get your free API key from https://openrouteservice.org/sign-up/
            const ORS_API_KEY = '5b3ce3597851110001cf6248b5b3c9a9d9c84e6a9c4b5a8b3e7f2d1c'; // Replace with your key

            // ==================== HELPER FUNCTIONS ====================
            function showLoading(show) {
                if (show) {
                    if ($('#mapLoading').length === 0) {
                        $('body').append(
                            '<div id="mapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i> Loading map...</div>'
                            );
                    }
                    $('#mapLoading').show();
                } else {
                    $('#mapLoading').hide();
                }
            }

            function closeAllMobilePanels(exceptPanel = null) {
                if (exceptPanel !== 'layer') $('#layerSwitcher').removeClass('open');
                if (exceptPanel !== 'legend') $('#mapLegend').removeClass('open');
                if (exceptPanel !== 'search') $('#searchPanel').removeClass('open');
                if (exceptPanel !== 'filter') $('#filterPanel').removeClass('open');
            }

            // ==================== BUILD SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = [];
                $.each(polygonDatas, function(i, building) {
                    let info = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        building_type: building.building_type,
                        road_name: building.road_name,
                        zone: building.zone,
                        number_floor: building.number_floor,
                        coordinates: null,
                        assessments: []
                    };
                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly
                                    .coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0,
                                        cy = 0;
                                    $.each(coords[0], function(k, c) {
                                        cx += c[0];
                                        cy += c[1];
                                    });
                                    info.coordinates = [cx / coords[0].length, cy / coords[0]
                                        .length];
                                }
                            } catch (e) {}
                            return false;
                        }
                    });
                    if (building.pointdata) {
                        $.each(building.pointdata, function(j, a) {
                            info.assessments.push({
                                assessment_no: a.assessment,
                                owner_name: a.owner_name || a.present_owner_name,
                                phone: a.phone_number
                            });
                        });
                    }
                    allBuildings.push(info);
                });
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    alert("Geolocation not supported");
                    return;
                }
                $('#mobileLocationBtn').css('background', '#28a745');
                locationTracking = true;
                navigator.geolocation.getCurrentPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function(err) {
                    alert("Unable to get location");
                    locationTracking = false;
                    $('#mobileLocationBtn').css('background', 'rgba(220,53,69,0.9)');
                });
                watchId = navigator.geolocation.watchPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function(err) {}, {
                    enableHighAccuracy: true,
                    maximumAge: 5000,
                    timeout: 10000
                });
            }

            function stopLocationTracking() {
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                locationTracking = false;
                $('#mobileLocationBtn').css('background', 'rgba(220,53,69,0.9)');
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];
                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, accuracy)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,68,68,0.15)'
                        })
                    })
                });
                map.addLayer(accuracyLayer);
                currentLocationLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({
                                color: '#ff4444'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#fff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(currentLocationLayer);
                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            // ==================== REAL ROAD DIRECTION USING OPENROUTESERVICE ====================
            async function getRoadRoute(startLonLat, endLonLat, profile = 'driving-car') {
                showLoading(true);
                let url = `https://api.openrouteservice.org/v2/directions/${profile}/geojson`;

                try {
                    let response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': ORS_API_KEY
                        },
                        body: JSON.stringify({
                            coordinates: [startLonLat, endLonLat],
                            format: 'geojson'
                        })
                    });

                    let data = await response.json();
                    showLoading(false);
                    return data;
                } catch (error) {
                    console.error("Route error:", error);
                    showLoading(false);
                    return null;
                }
            }

            function displayRoadRoute(routeData, startCoords, endCoords, buildingGisid) {
                // Remove existing route layers
                if (routeLayer) map.removeLayer(routeLayer);
                if (startMarker) map.removeLayer(startMarker);
                if (endMarker) map.removeLayer(endMarker);

                if (!routeData || !routeData.features || !routeData.features[0]) {
                    alert("Could not find a valid route. Please try again.");
                    return;
                }

                let feature = routeData.features[0];
                let geometry = feature.geometry;
                let properties = feature.properties;
                let distance = properties.summary.distance / 1000; // km
                let duration = properties.summary.duration / 60; // minutes

                // Convert route coordinates to OpenLayers format
                let routeCoords = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));

                // Create route line layer
                routeLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.LineString(routeCoords)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#28a745',
                            width: 6,
                            lineDash: [10, 10]
                        })
                    })
                });
                map.addLayer(routeLayer);

                // Create start marker (green)
                let startPoint = ol.proj.fromLonLat(startCoords);
                startMarker = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(startPoint)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({
                                color: '#28a745'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#fff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(startMarker);

                // Create end marker (red)
                let endPoint = ol.proj.fromLonLat(endCoords);
                endMarker = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(endPoint)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({
                                color: '#dc3545'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#fff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(endMarker);

                // Show direction panel with detailed info
                let travelModes = {
                    'driving-car': '🚗 Driving',
                    'cycling-regular': '🚲 Cycling',
                    'foot-walking': '🚶 Walking'
                };

                let html = `
                    <div class="direction-panel panel show" id="directionPanel">
                        <button class="close-direction" onclick="$('#directionPanel').remove();">&times;</button>
                        <h5><i class="fas fa-route"></i> Road Direction</h5>
                        <div class="direction-info">
                            <p><strong>📍 From:</strong> Your Location</p>
                            <p><strong>🏢 To:</strong> ${buildingGisid}</p>
                            <p><strong>📏 Distance:</strong> ${distance.toFixed(2)} km (${(distance * 0.621371).toFixed(2)} miles)</p>
                            <p><strong>⏱️ Duration:</strong> ${Math.floor(duration)} min (${(duration / 60).toFixed(1)} hours)</p>
                            <p><strong>🛣️ Mode:</strong> Car (Fastest route)</p>
                        </div>
                        <div class="route-actions" style="display: flex; gap: 10px; margin-top: 15px;">
                            <button id="showFullRouteBtn" style="flex:1; padding:10px; background:#ff4444; border:none; border-radius:8px; color:white; cursor:pointer;">
                                <i class="fas fa-map-marked-alt"></i> Show Full Route
                            </button>
                            <button id="startNavigationBtn" style="flex:1; padding:10px; background:#28a745; border:none; border-radius:8px; color:white; cursor:pointer;">
                                <i class="fas fa-play"></i> Start Navigation
                            </button>
                        </div>
                    </div>`;

                $('#directionPanel').remove();
                $('body').append(html);

                // Fit route in view
                let extent = ol.extent.boundingExtent(routeCoords);
                map.getView().fit(extent, {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });

                // Show full route button
                $('#showFullRouteBtn').on('click', function() {
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                });

                // Start navigation mode - animate along route
                let navigationActive = false;
                let currentIndex = 0;
                let navigationInterval = null;

                $('#startNavigationBtn').on('click', function() {
                    if (navigationActive) {
                        // Stop navigation
                        navigationActive = false;
                        if (navigationInterval) clearInterval(navigationInterval);
                        $(this).html('<i class="fas fa-play"></i> Start Navigation');
                        $(this).css('background', '#28a745');

                        // Reset view
                        map.getView().setCenter(startPoint);
                        map.getView().setZoom(18);
                    } else {
                        // Start navigation
                        navigationActive = true;
                        currentIndex = 0;
                        $(this).html('<i class="fas fa-stop"></i> Stop Navigation');
                        $(this).css('background', '#dc3545');

                        // Animate along route
                        navigationInterval = setInterval(function() {
                            if (!navigationActive) return;
                            if (currentIndex >= routeCoords.length) {
                                clearInterval(navigationInterval);
                                navigationActive = false;
                                $('#startNavigationBtn').html(
                                    '<i class="fas fa-play"></i> Start Navigation').css(
                                    'background', '#28a745');
                                alert("You have reached your destination!");
                                return;
                            }
                            map.getView().setCenter(routeCoords[currentIndex]);
                            map.getView().setZoom(17);
                            currentIndex += 5;
                        }, 500);
                    }
                });
            }

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                if (!text || !text.trim()) {
                    $('#searchResults').html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }
                let term = text.toLowerCase().trim();
                let results = [];
                $.each(allBuildings, function(i, b) {
                    let match = false,
                        type = '',
                        val = '';
                    if (b.gisid && b.gisid.toLowerCase().includes(term)) {
                        match = true;
                        type = 'GIS ID';
                        val = b.gisid;
                    } else if (b.building_usage && b.building_usage.toLowerCase().includes(term)) {
                        match = true;
                        type = 'Building Usage';
                        val = b.building_usage;
                    } else if (b.road_name && b.road_name.toLowerCase().includes(term)) {
                        match = true;
                        type = 'Road Name';
                        val = b.road_name;
                    } else {
                        $.each(b.assessments, function(j, a) {
                            if (a.assessment_no && a.assessment_no.toLowerCase().includes(term)) {
                                match = true;
                                type = 'Assessment No';
                                val = a.assessment_no;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                match = true;
                                type = 'Owner Name';
                                val = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                match = true;
                                type = 'Phone';
                                val = a.phone;
                                return false;
                            }
                        });
                    }
                    if (match) results.push({
                        gisid: b.gisid,
                        matchType: type,
                        matchValue: val,
                        building: b,
                        coordinates: b.coordinates
                    });
                });

                let $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }
                $.each(results, function(i, r) {
                    let lon = r.coordinates ? r.coordinates[0] : '';
                    let lat = r.coordinates ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${r.gisid}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${r.matchType}: ${r.matchValue}</div>
                        <div class="result-owner"><i class="fas fa-location-dot"></i> ${r.building.road_name || 'No road'} | ${r.building.zone || 'No zone'}</div>
                        <button class="direction-btn"><i class="fas fa-directions"></i> Get Road Directions</button>
                    </div>`);
                });

                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn')) {
                        zoomToBuilding($(this).data('gisid'));
                        closeAllMobilePanels();
                    }
                });

                $('.direction-btn').off('click').on('click', async function(e) {
                    e.stopPropagation();
                    let p = $(this).closest('.search-result-item');
                    let gisid = p.data('gisid');
                    let lon = p.data('lon');
                    let lat = p.data('lat');

                    if (!lon || !lat) {
                        alert("Coordinates not available for this building");
                        return;
                    }
                    if (!currentPosition) {
                        alert("Please enable location tracking first");
                        startLocationTracking();
                        return;
                    }

                    // Show loading indicator
                    let btn = $(this);
                    let originalText = btn.html();
                    btn.html('<i class="fas fa-spinner fa-spin"></i> Getting route...').prop('disabled',
                        true);

                    // Get road route
                    let routeData = await getRoadRoute([currentPosition[0], currentPosition[1]], [
                        parseFloat(lon), parseFloat(lat)
                    ], 'driving-car');

                    btn.html(originalText).prop('disabled', false);

                    if (routeData) {
                        displayRoadRoute(routeData, [currentPosition[0], currentPosition[1]], [
                            parseFloat(lon), parseFloat(lat)
                        ], gisid);
                        closeAllMobilePanels();
                    } else {
                        alert("Could not find a road route. Please try again.");
                    }
                });
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer) return;
                let features = polygonLayer.getSource().getFeatures();
                for (let i = 0; i < features.length; i++) {
                    if (features[i].get('gisid') == gisid) {
                        let e = features[i].getGeometry().getExtent();
                        map.getView().fit(e, {
                            padding: [50, 50, 50, 50],
                            duration: 1000
                        });
                        showPopup(gisid, ol.extent.getCenter(e));
                        break;
                    }
                }
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', {
                    class: 'ol-popup',
                    style: 'display:none'
                })[0];
                $('body').append(popupElement);
                return new ol.Overlay({
                    element: popupElement,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -10]
                });
            }

            window.closePopup = function() {
                $('.ol-popup').hide();
            };
            window.switchTab = function(t) {
                $('.popup-tab-content, .popup-tab').removeClass('active');
                $('#tab-' + t).addClass('active');
                $('.popup-tab[data-tab="' + t + '"]').addClass('active');
                currentActiveTab = t;
            };

            function showPopup(gisid, coord) {
                let pd = polygonDatas.find(p => p.gisid == gisid);
                if (!pd) return;
                let assessments = pd.pointdata || [];
                let shops = [];
                $.each(assessments, function(i, a) {
                    if (a.shops) {
                        $.each(a.shops, function(j, s) {
                            shops.push({
                                ...s,
                                assessmentNumber: a.assessment || 'Bill ' + (i + 1)
                            });
                        });
                    }
                });

                let buildingHtml =
                    `<div class="building-details-content">${[['fingerprint','GIS ID',pd.gisid],['building','Building Usage',pd.building_usage],['home','Building Type',pd.building_type],['layer-group','Floors',pd.number_floor],['receipt','Total Bills',pd.number_bill],['store','Total Shops',pd.total_shops],['road','Road Name',pd.road_name],['map-pin','Zone',pd.zone]].map(([i,l,v]) => `<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v || 'N/A'}</div></div>`).join('')}</div>`;

                let assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) =>
                        `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}"><div class="assessment-header"><span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i+1)}</span><span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span></div><div class="assessment-body">${[['Owner', a.owner_name || a.present_owner_name],['Phone', a.phone_number],['Floor', a.floor],['Usage', a.bill_usage],['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}</div></div>`
                        ).join('');

                let shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' : shops.map(s =>
                        `<div class="shop-item"><div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name || 'Shop'}</div>${[['Category', s.shop_category],['Owner', s.shop_owner_name],['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}</div>`
                        ).join('');

                let html =
                    `<div class="popup-header"><h4><i class="fas fa-building"></i> Building Details</h4><button class="popup-close" onclick="closePopup()">&times;</button></div>
                    <div class="popup-tabs"><button class="popup-tab ${currentActiveTab == 'building' ? 'active' : ''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                    <button class="popup-tab ${currentActiveTab == 'assessments' ? 'active' : ''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button>
                    <button class="popup-tab ${currentActiveTab == 'shops' ? 'active' : ''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${shops.length})</button></div>
                    <div id="tab-building" class="popup-tab-content ${currentActiveTab == 'building' ? 'active' : ''}">${buildingHtml}</div>
                    <div id="tab-assessments" class="popup-tab-content ${currentActiveTab == 'assessments' ? 'active' : ''}"><div style="padding:12px">${assessmentsHtml}</div></div>
                    <div id="tab-shops" class="popup-tab-content ${currentActiveTab == 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coord);

                $('.assessment-card').off('click').on('click', function() {
                    let id = $(this).data('id'),
                        num = $(this).data('assessment');
                    $(this).after(
                        `<div class="assessment-form-container"><button class="close-form-btn">&times;</button><h4 style="color:#ffc107; margin-bottom:15px;">QC Form - ${num}</h4>
                        <form class="qc-form"><input type="hidden" name="assessment_id" value="${id}">
                        <div><label>QC Square Feet:</label><input type="number" name="qc_sqfeet" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;"></div>
                        <div><label>QC Usage:</label><select name="qc_usage" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;"><option value="">Select</option><option value="Residential">Residential</option><option value="Commercial">Commercial</option><option value="Industrial">Industrial</option></select></div>
                        <div><label>Tax Amount (₹):</label><input type="number" name="tax_amount" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;"></div>
                        <div style="display:flex; gap:10px;"><button type="submit" style="flex:1; background:#28a745; color:white; border:none; padding:10px;">Save</button><button type="button" class="cancel-form-btn" style="flex:1; background:#dc3545; color:white;">Cancel</button></div></form></div>`
                        );

                    $('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        let has = $(this).find('input[name="qc_sqfeet"]').val() && $(this).find(
                            'select[name="qc_usage"]').val() && $(this).find(
                            'input[name="tax_amount"]').val();
                        let $badge = $(this).closest('.assessment-card').find('.badge');
                        if (has) $badge.removeClass('badge-warning').addClass('badge-success').html(
                            '<i class="fas fa-check-circle"></i> QC Complete');
                        else $badge.removeClass('badge-success').addClass('badge-warning').html(
                            '<i class="fas fa-clock"></i> QC Pending');
                        alert('QC Saved!');
                        $('.assessment-form-container').remove();
                    });
                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== POLYGON STYLE ====================
            function polygonStyleFunction(feature) {
                let gisid = feature.get('gisid'),
                    sqfeet = feature.get('sqfeet'),
                    geom = feature.getGeometry();
                let center;
                try {
                    center = geom.getInteriorPoint();
                    if (!center) {
                        let e = geom.getExtent();
                        center = new ol.geom.Point([(e[0] + e[2]) / 2, (e[1] + e[3]) / 2]);
                    }
                } catch (e) {
                    let ex = geom.getExtent();
                    center = new ol.geom.Point([(ex[0] + ex[2]) / 2, (ex[1] + ex[3]) / 2]);
                }
                if (feature.get('visible') === false) return null;
                return [new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,68,68,0.15)'
                        })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}\n${sqfeet || 0} sqft`,
                            font: 'bold 11px Arial',
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#000',
                                width: 2
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: 'rgba(0,0,0,0.7)'
                            }),
                            padding: [4, 8, 4, 8]
                        })
                    })
                ];
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);
                let ps = new ol.source.Vector();
                $.each(polygons, function(i, p) {
                    try {
                        let c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p
                            .coordinates;
                        if (c && c.length) ps.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(c),
                            gisid: p.gisid,
                            sqfeet: p.sqfeet,
                            visible: true
                        }));
                    } catch (e) {}
                });
                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFunction,
                    visible: true
                });
                let ls = new ol.source.Vector();
                $.each(lines, function(i, l) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                            .coordinates;
                        if (c && c.length) {
                            if (c.length === 1 && Array.isArray(c[0][0])) c = c[0];
                            ls.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(c),
                                gisid: l.gisid
                            }));
                        }
                    } catch (e) {}
                });
                lineLayer = new ol.layer.Vector({
                    source: ls,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ffc107',
                            width: 3
                        })
                    }),
                    visible: true
                });
                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);
                map.on('click', function(e) {
                    let f = map.forEachFeatureAtPixel(e.pixel, f => f);
                    if (f && f.get('gisid')) showPopup(f.get('gisid'), e.coordinate);
                    else if (popupElement) $(popupElement).hide();
                });
                map.on('pointermove', function(e) {
                    $('#map').css('cursor', map.forEachFeatureAtPixel(e.pixel, f => f) ? 'pointer' : '');
                });
                showLoading(false);
            }

            // ==================== MAP INITIALIZATION ====================
            function initMap() {
                showLoading(true);
                osmLayer = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });
                satelliteLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    }),
                    visible: false
                });
                let droneImg = wardData.drone_image,
                    hasDrone = false;
                if (droneImg && wardData.extent_left) {
                    try {
                        imageLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: droneImg,
                                imageExtent: [parseFloat(wardData.extent_left), parseFloat(wardData
                                        .extent_bottom), parseFloat(wardData.extent_right),
                                    parseFloat(wardData.extent_top)
                                ],
                                projection: 'EPSG:3857'
                            }),
                            visible: true
                        });
                        hasDrone = true;
                    } catch (e) {}
                }
                let bound = wardData.boundary,
                    boundExt = null;
                if (bound && bound.length && bound[0].length) {
                    try {
                        let bc = bound[0].map(c => ol.proj.fromLonLat(c));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([bc])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: '#ff0000',
                                    width: 3,
                                    lineDash: [10, 5]
                                }),
                                fill: new ol.style.Fill({
                                    color: 'rgba(255,0,0,0.05)'
                                })
                            }),
                            visible: true
                        });
                        let lons = bound[0].map(p => p[0]),
                            lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math
                            .max(...lats)
                        ]);
                    } catch (e) {}
                }
                let center = ol.proj.fromLonLat([80.2707, 13.0827]),
                    zoom = 18;
                if (bound && bound[0] && bound[0].length) {
                    try {
                        let lons = bound[0].map(p => p[0]),
                            lats = bound[0].map(p => p[1]);
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...
                            lats) + Math.max(...lats)) / 2]);
                    } catch (e) {}
                }
                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: center,
                        zoom: zoom
                    })
                });
                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);
                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);
                setTimeout(() => {
                    if (boundExt) map.getView().fit(boundExt, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }, 500);

                // Add UI panels
                $('body').append(
                    `<div class="layer-switcher panel" id="layerSwitcher"><h5><i class="fas fa-layer-group"></i> Layers</h5><div class="layer-group"><div class="group-title">Base Maps</div><label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label><label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label></div><div class="layer-group"><div class="group-title">Overlays</div><label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label><label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label><label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}</div></div>`
                    );
                $('body').append(
                    `<div class="map-legend panel" id="mapLegend"><h5><i class="fas fa-info-circle"></i> Legend</h5><div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div><div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div><div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div><div class="legend-item"><div class="legend-color route"></div><span>Road Route (Green)</span></div></div>`
                    );
                $('body').append(
                    `<div class="search-panel panel" id="searchPanel"><h5><i class="fas fa-search"></i> Search Building</h5><div class="search-box"><input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment..."><button id="searchBtn"><i class="fas fa-search"></i> Go</button></div><div id="searchResults" class="search-results"></div></div>`
                    );
                $('body').append(
                    `<div class="filter-panel panel" id="filterPanel"><h5><i class="fas fa-filter"></i> Filter Buildings</h5><div class="filter-group"><label>QC Status</label><select id="filterType"><option value="all">All Buildings</option><option value="completed">QC Complete</option><option value="pending">QC Pending</option></select></div><div class="filter-group"><label>Min Floors</label><input type="number" id="filterMinFloors" placeholder="Min"></div><div class="filter-group"><label>Max Floors</label><input type="number" id="filterMaxFloors" placeholder="Max"></div><div class="filter-actions"><button class="apply-btn" id="applyFilterBtn">Apply</button><button class="reset-btn" id="resetFilterBtn">Reset</button></div><div class="filter-count" id="filterCount"></div></div>`
                    );
                $('body').append(
                    `<div class="zoom-controls"><button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button><button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button></div>`
                    );

                // Event handlers
                $('input[name="baseLayer"]').on('change', function() {
                    currentBaseLayer = $(this).val();
                    osmLayer.setVisible(currentBaseLayer === 'osm');
                    satelliteLayer.setVisible(currentBaseLayer === 'satellite');
                });
                $('#toggleBuildings').on('change', function() {
                    if (polygonLayer) polygonLayer.setVisible($(this).is(':checked'));
                });
                $('#toggleRoads').on('change', function() {
                    if (lineLayer) lineLayer.setVisible($(this).is(':checked'));
                });
                $('#toggleBoundary').on('change', function() {
                    if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked'));
                });
                if (hasDrone) $('#toggleDrone').on('change', function() {
                    if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                });
                $('#searchBtn').on('click', () => searchBuildings($('#searchInput').val()));
                $('#searchInput').on('keypress', e => {
                    if (e.which === 13) searchBuildings($(this).val());
                });
                $('#applyFilterBtn').on('click', function() {
                    let type = $('#filterType').val(),
                        minF = $('#filterMinFloors').val(),
                        maxF = $('#filterMaxFloors').val(),
                        src = polygonLayer.getSource(),
                        fts = src.getFeatures(),
                        cnt = 0;
                    $.each(fts, function(i, f) {
                        let g = f.get('gisid'),
                            b = polygonDatas.find(p => p.gisid == g),
                            show = true;
                        if (type === 'completed' && b) {
                            let has = false;
                            if (b.pointdata) $.each(b.pointdata, (k, a) => {
                                if (a.qcsqfeet || a.qcusage) {
                                    has = true;
                                    return false;
                                }
                            });
                            if (!has) show = false;
                        } else if (type === 'pending' && b) {
                            let has = false;
                            if (b.pointdata) $.each(b.pointdata, (k, a) => {
                                if (a.qcsqfeet || a.qcusage) {
                                    has = true;
                                    return false;
                                }
                            });
                            if (has) show = false;
                        }
                        if (show && b && (minF || maxF)) {
                            let fl = parseInt(b.number_floor) || 0;
                            if (minF && fl < parseInt(minF)) show = false;
                            if (maxF && fl > parseInt(maxF)) show = false;
                        }
                        f.set('visible', show);
                        if (show) cnt++;
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(`Showing ${cnt} of ${fts.length} buildings`);
                    closeAllMobilePanels();
                });
                $('#resetFilterBtn').on('click', function() {
                    $('#filterType, #filterMinFloors, #filterMaxFloors').val('');
                    let src = polygonLayer.getSource();
                    $.each(src.getFeatures(), (i, f) => f.set('visible', true));
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(
                        `Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    closeAllMobilePanels();
                });
                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));

                // Mobile button handlers
                $('#mobileMenuBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllMobilePanels();
                    if (!isOpen) $('#layerSwitcher').addClass('open');
                });
                $('#mobileLegendBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#mapLegend').hasClass('open');
                    closeAllMobilePanels();
                    if (!isOpen) $('#mapLegend').addClass('open');
                });
                $('#mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#searchPanel').hasClass('open');
                    closeAllMobilePanels();
                    if (!isOpen) {
                        $('#searchPanel').addClass('open');
                        setTimeout(() => $('#searchInput').focus(), 300);
                    }
                });
                $('#mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#filterPanel').hasClass('open');
                    closeAllMobilePanels();
                    if (!isOpen) $('#filterPanel').addClass('open');
                });
                $('#mobileLocationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                    } else {
                        startLocationTracking();
                        setTimeout(() => {
                            if (currentPosition) map.getView().setCenter(ol.proj.fromLonLat(
                                currentPosition));
                        }, 1000);
                    }
                });

                $(document).on('click touchstart', function(e) {
                    if ($(window).width() <= 768) {
                        if (!$('#layerSwitcher').is(e.target) && !$('#layerSwitcher').has(e.target)
                            .length && !$('#mobileMenuBtn').is(e.target)) $('#layerSwitcher').removeClass(
                            'open');
                        if (!$('#mapLegend').is(e.target) && !$('#mapLegend').has(e.target).length && !$(
                                '#mobileLegendBtn').is(e.target)) $('#mapLegend').removeClass('open');
                        if (!$('#searchPanel').is(e.target) && !$('#searchPanel').has(e.target).length && !
                            $('#mobileSearchBtn').is(e.target)) $('#searchPanel').removeClass('open');
                        if (!$('#filterPanel').is(e.target) && !$('#filterPanel').has(e.target).length && !
                            $('#mobileFilterBtn').is(e.target)) $('#filterPanel').removeClass('open');
                    }
                });

                refreshLayers();
            }

            initMap();
            buildSearchIndex();
            $(window).on('resize', () => setTimeout(() => map?.updateSize(), 100));
        });
    </script>
@endpush
