@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Action Buttons - Visible on ALL devices -->
        <div class="action-buttons">
            <button class="action-btn menu-btn" id="menuBtn" title="Layers">
                <i class="fas fa-layer-group"></i>
                <span class="btn-label">Layers</span>
            </button>
            <button class="action-btn legend-btn" id="legendBtn" title="Legend">
                <i class="fas fa-info-circle"></i>
                <span class="btn-label">Legend</span>
            </button>
            <button class="action-btn search-btn" id="openSearchBtn" title="Search">
                <i class="fas fa-search"></i>
                <span class="btn-label">Search</span>
            </button>
            <button class="action-btn filter-btn" id="filterBtn" title="Filter">
                <i class="fas fa-filter"></i>
                <span class="btn-label">Filter</span>
            </button>
            <button class="action-btn location-btn" id="locationBtn" title="My Location">
                <i class="fas fa-location-dot"></i>
                <span class="btn-label">Location</span>
            </button>
        </div>
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

        /* ==================== ACTION BUTTONS - VISIBLE ON ALL DEVICES ==================== */
        .action-buttons {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 12px;
            z-index: 1002;
            padding: 0 16px;
            flex-wrap: wrap;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 8px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            min-width: 70px;
        }

        .action-btn i {
            font-size: 18px;
        }

        .btn-label {
            font-size: 10px;
            font-weight: 500;
        }

        .action-btn:active {
            transform: scale(0.95);
        }

        .action-btn.menu-btn {
            background: rgba(0, 0, 0, 0.85);
        }

        .action-btn.legend-btn {
            background: rgba(255, 193, 7, 0.9);
        }

        .action-btn.search-btn {
            background: rgba(23, 162, 184, 0.9);
        }

        .action-btn.filter-btn {
            background: rgba(40, 167, 69, 0.9);
        }

        .action-btn.location-btn {
            background: rgba(220, 53, 69, 0.9);
        }

        .action-btn.location-btn.active {
            background: #28a745;
        }

        /* Desktop adjustment - buttons on right side */
        @media (min-width: 769px) {
            .action-buttons {
                position: absolute;
                bottom: auto;
                top: 80%;
                right: 20px;
                left: auto;
                transform: translateY(-50%);
                flex-direction: column;
                gap: 10px;
            }

            .action-btn {
                flex-direction: row;
                gap: 8px;
                padding: 10px 16px;
                min-width: auto;
            }

            .btn-label {
                font-size: 12px;
            }
        }

        /* ==================== PANELS - COMMON STYLES ==================== */
        .panel {
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px;
            color: white;
            z-index: 1001;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 68, 68, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            max-height: 80vh;
            overflow-y: auto;
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

        .panel h5 i {
            font-size: 18px;
        }

        /* Close button for panels */
        .panel-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #ff4444;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            z-index: 10;
        }

        /* ==================== LAYER SWITCHER PANEL ==================== */
        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            min-width: 200px;
            display: none;
        }

        .layer-switcher.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* ==================== LEGEND PANEL ==================== */
        .map-legend {
            position: absolute;
            bottom: 20px;
            left: 20px;
            min-width: 160px;
            display: none;
        }

        .map-legend.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* ==================== SEARCH PANEL ==================== */
        .search-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
            display: none;
        }

        .search-panel.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* ==================== FILTER PANEL ==================== */
        .filter-panel {
            position: absolute;
            top: 100px;
            right: 20px;
            width: 300px;
            display: none;
        }

        .filter-panel.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* ==================== RESPONSIVE PANEL POSITIONS ==================== */
        @media (max-width: 768px) {

            .layer-switcher,
            .map-legend,
            .search-panel,
            .filter-panel {
                position: fixed;
                bottom: 100px;
                right: 20px;
                left: 20px;
                top: auto;
                width: auto;
                max-height: 60vh;
            }

            .map-legend {
                left: 20px;
                right: auto;
                min-width: 180px;
            }

            .search-panel {
                top: auto;
                bottom: 100px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Panel content styles */
        .layer-group {
            margin-bottom: 15px;
        }

        .layer-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            font-size: 13px;
            cursor: pointer;
            padding: 5px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .layer-group label:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .group-title {
            font-weight: 600;
            color: #ffc107;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        .search-box input:focus {
            border-color: #ffc107;
        }

        .search-box button {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            background: #ff4444;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .search-box button:active {
            transform: scale(0.98);
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
            transition: all 0.2s;
            border-left: 3px solid #ffc107;
        }

        .search-result-item:active {
            background: rgba(255, 68, 68, 0.3);
        }

        .result-gisid {
            font-weight: bold;
            color: #ffc107;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .result-owner {
            font-size: 11px;
            color: #ddd;
            margin: 3px 0;
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
            transition: all 0.2s;
        }

        .direction-btn:active {
            background: #1e7e34;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: #ffc107;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 13px;
            outline: none;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #ffc107;
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
            transition: all 0.2s;
        }

        .apply-btn {
            background: #28a745;
            color: white;
        }

        .apply-btn:active {
            background: #1e7e34;
        }

        .reset-btn {
            background: #dc3545;
            color: white;
        }

        .reset-btn:active {
            background: #c82333;
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

        @media (min-width: 769px) {
            .direction-panel {
                bottom: auto;
                top: 100px;
                left: 20px;
                right: auto;
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

        .direction-info {
            font-size: 13px;
            margin-top: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
        }

        .direction-info p {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }

        .close-direction {
            float: right;
            background: none;
            border: none;
            color: #ff4444;
            font-size: 22px;
            cursor: pointer;
            padding: 0 5px;
        }

        /* Zoom Controls */
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
            z-index: 1000;
        }

        @media (min-width: 769px) {
            .zoom-controls {
                bottom: auto;
                top: 100px;
                left: 20px;
            }
        }

        .zoom-btn {
            width: 45px;
            height: 45px;
            border: none;
            background: transparent;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .zoom-btn:active {
            background: rgba(255, 255, 255, 0.2);
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Loading */
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
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .popup-header h4 {
            margin: 0;
            font-size: 18px;
            color: #ff4444;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .popup-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .popup-close:active {
            background: #ff4444;
            transform: rotate(90deg);
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
            transition: all 0.2s;
        }

        .popup-tab:active {
            background: rgba(255, 68, 68, 0.2);
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
            transition: all 0.2s;
        }

        .assessment-card:active {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
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

        .shop-name {
            font-weight: 700;
            color: #ff4444;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            opacity: 0.5;
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

        /* Scrollbar Styling */
        .search-results::-webkit-scrollbar,
        .popup-tab-content::-webkit-scrollbar {
            width: 5px;
        }

        .search-results::-webkit-scrollbar-track,
        .popup-tab-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .search-results::-webkit-scrollbar-thumb,
        .popup-tab-content::-webkit-scrollbar-thumb {
            background: #ff4444;
            border-radius: 10px;
        }

        /* Custom Center Button */
        .center-btn {
            position: fixed;
            bottom: 20px;
            left: 80px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .center-btn:active {
            transform: scale(0.95);
        }

        @media (min-width: 769px) {
            .center-btn {
                bottom: auto;
                top: 160px;
                left: 20px;
            }
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

            // ==================== SEARCH & DIRECTION VARIABLES ====================
            let allBuildings = [],
                directionLineLayer = null,
                destinationMarkerLayer = null;

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

            // Close all panels
            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel').removeClass('open');
            }

            // Clear route
            function clearRoute() {
                if (directionLineLayer) {
                    map.removeLayer(directionLineLayer);
                    directionLineLayer = null;
                }
                if (destinationMarkerLayer) {
                    map.removeLayer(destinationMarkerLayer);
                    destinationMarkerLayer = null;
                }
                $('#directionPanel').remove();
            }

            // Center map to current location
            function centerToMyLocation() {
                if (currentPosition) {
                    let coords = ol.proj.fromLonLat(currentPosition);
                    map.getView().setCenter(coords);
                    map.getView().setZoom(19);
                    // Flash animation for location marker
                    if (currentLocationLayer) {
                        currentLocationLayer.setVisible(false);
                        setTimeout(() => {
                            if (currentLocationLayer) currentLocationLayer.setVisible(true);
                        }, 200);
                        setTimeout(() => {
                            if (currentLocationLayer) currentLocationLayer.setVisible(false);
                        }, 400);
                        setTimeout(() => {
                            if (currentLocationLayer) currentLocationLayer.setVisible(true);
                        }, 600);
                    }
                } else {
                    alert("Location not available. Please enable location tracking first.");
                    startLocationTracking();
                }
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
                                        .length
                                    ];
                                }
                            } catch (e) {}
                            return false;
                        }
                    });
                    if (building.pointdata) {
                        $.each(building.pointdata, function(j, a) {
                            info.assessments.push({
                                id: a.id,
                                assessment_no: a.assessment,
                                owner_name: a.owner_name || a.present_owner_name,
                                phone: a.phone_number
                            });
                        });
                    }
                    allBuildings.push(info);
                });
                console.log("Search index built with", allBuildings.length, "buildings");
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    alert("Geolocation not supported");
                    return;
                }
                $('#locationBtn').addClass('active');
                locationTracking = true;

                // Add center button if not exists
                if ($('#centerMyLocationBtn').length === 0) {
                    $('body').append(
                        '<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>'
                    );
                    $('#centerMyLocationBtn').on('click', centerToMyLocation);
                }

                navigator.geolocation.getCurrentPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function(err) {
                    alert("Unable to get location: " + err.message);
                    locationTracking = false;
                    $('#locationBtn').removeClass('active');
                    $('#centerMyLocationBtn').remove();
                });

                watchId = navigator.geolocation.watchPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function(err) {
                    console.warn("Watch position error:", err);
                }, {
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
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentLocationLayer = null;
                accuracyLayer = null;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);

                // Accuracy circle
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

                // Location marker
                currentLocationLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 10,
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
            }

            // ==================== DIRECTION FUNCTIONS ====================
            function calculateDistance(lon1, lat1, lon2, lat2) {
                let R = 6371;
                let dLat = (lat2 - lat1) * Math.PI / 180;
                let dLon = (lon2 - lon1) * Math.PI / 180;
                let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function calculateBearing(lon1, lat1, lon2, lat2) {
                let φ1 = lat1 * Math.PI / 180;
                let φ2 = lat2 * Math.PI / 180;
                let λ1 = lon1 * Math.PI / 180;
                let λ2 = lon2 * Math.PI / 180;
                let y = Math.sin(λ2 - λ1) * Math.cos(φ2);
                let x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(λ2 - λ1);
                let θ = Math.atan2(y, x);
                let bearing = (θ * 180 / Math.PI + 360) % 360;
                return bearing;
            }

            function getDirectionText(bearing) {
                if (bearing >= 337.5 || bearing < 22.5) return "North ↑";
                if (bearing >= 22.5 && bearing < 67.5) return "North-East ↗";
                if (bearing >= 67.5 && bearing < 112.5) return "East →";
                if (bearing >= 112.5 && bearing < 157.5) return "South-East ↘";
                if (bearing >= 157.5 && bearing < 202.5) return "South ↓";
                if (bearing >= 202.5 && bearing < 247.5) return "South-West ↙";
                if (bearing >= 247.5 && bearing < 292.5) return "West ←";
                return "North-West ↖";
            }

            function showDirectionToBuilding(gisid, coords) {
                if (!currentPosition) {
                    alert("Please enable location tracking first");
                    startLocationTracking();
                    return;
                }

                // Clear existing route
                clearRoute();

                let from = ol.proj.fromLonLat(currentPosition);
                let to = ol.proj.fromLonLat(coords);

                // Create line feature for route
                directionLineLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.LineString([from, to])
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#28a745',
                            width: 5,
                            lineDash: [15, 10]
                        })
                    })
                });
                map.addLayer(directionLineLayer);

                // Destination marker
                destinationMarkerLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(to)
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
                map.addLayer(destinationMarkerLayer);

                // Calculate route info
                let distance = calculateDistance(currentPosition[0], currentPosition[1], coords[0], coords[1]);
                let bearing = calculateBearing(currentPosition[0], currentPosition[1], coords[0], coords[1]);
                let direction = getDirectionText(bearing);

                let html = `
                    <div class="direction-panel panel show" id="directionPanel">
                        <button class="close-direction" onclick="clearRouteAndPanel()">&times;</button>
                        <h5><i class="fas fa-directions"></i> Route to Building</h5>
                        <div class="direction-info">
                            <p><strong>📍 GIS ID:</strong> ${gisid}</p>
                            <p><strong>📏 Distance:</strong> ${distance.toFixed(2)} km (${(distance * 1000).toFixed(0)} m)</p>
                            <p><strong>🧭 Direction:</strong> ${direction}</p>
                            <p><strong>🚶 Walking:</strong> ${Math.round(distance / 5 * 60)} min</p>
                            <p><strong>🚗 Driving:</strong> ${Math.round(distance / 40 * 60)} min</p>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 12px;">
                            <button id="fitRouteBtn" style="flex:1; padding:10px; background:#ff4444; border:none; border-radius:8px; color:white; cursor:pointer;">
                                <i class="fas fa-map-marked-alt"></i> Fit Route
                            </button>
                            <button id="clearRouteBtn" style="flex:1; padding:10px; background:#dc3545; border:none; border-radius:8px; color:white; cursor:pointer;">
                                <i class="fas fa-trash"></i> Clear Route
                            </button>
                        </div>
                    </div>`;

                $('#directionPanel').remove();
                $('body').append(html);

                $('#fitRouteBtn').on('click', function() {
                    let extent = ol.extent.boundingExtent([from, to]);
                    let paddedExtent = ol.extent.buffer(extent, 100);
                    map.getView().fit(paddedExtent, {
                        padding: [80, 80, 80, 80],
                        duration: 800,
                        maxZoom: 18
                    });
                });

                $('#clearRouteBtn').on('click', function() {
                    clearRoute();
                });

                // Auto-fit route
                let extent = ol.extent.boundingExtent([from, to]);
                let paddedExtent = ol.extent.buffer(extent, 100);
                map.getView().fit(paddedExtent, {
                    padding: [80, 80, 80, 80],
                    duration: 600,
                    maxZoom: 18
                });
            }

            window.clearRouteAndPanel = function() {
                clearRoute();
            };

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                console.log("Searching for:", text);

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
                    } else if (b.zone && b.zone.toLowerCase().includes(term)) {
                        match = true;
                        type = 'Zone';
                        val = b.zone;
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

                    if (match) {
                        results.push({
                            gisid: b.gisid,
                            matchType: type,
                            matchValue: val,
                            building: b,
                            coordinates: b.coordinates
                        });
                    }
                });

                console.log("Found", results.length, "results");

                let $res = $('#searchResults').empty();

                if (!results.length) {
                    $res.html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    let lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    let lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${r.gisid}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${r.matchType}: ${r.matchValue}</div>
                        <div class="result-owner"><i class="fas fa-location-dot"></i> ${r.building.road_name || 'No road'} | ${r.building.zone || 'No zone'}</div>
                        <button class="direction-btn"><i class="fas fa-directions"></i> Get Directions</button>
                    </div>`);
                });

                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn')) {
                        zoomToBuilding($(this).data('gisid'));
                        closeAllPanels();
                    }
                });

                $('.direction-btn').off('click').on('click', function(e) {
                    e.stopPropagation();
                    let p = $(this).closest('.search-result-item');
                    let lon = p.data('lon');
                    let lat = p.data('lat');
                    if (lon && lat) {
                        showDirectionToBuilding(p.data('gisid'), [parseFloat(lon), parseFloat(lat)]);
                        closeAllPanels();
                    } else {
                        alert("Coordinates not available for this building");
                    }
                });
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer) return;
                let features = polygonLayer.getSource().getFeatures();
                let f = null;
                for (let i = 0; i < features.length; i++) {
                    if (features[i].get('gisid') == gisid) {
                        f = features[i];
                        break;
                    }
                }
                if (f) {
                    let e = f.getGeometry().getExtent();
                    let paddedExtent = ol.extent.buffer(e, 20);
                    map.getView().fit(paddedExtent, {
                        padding: [50, 50, 50, 50],
                        duration: 800,
                        maxZoom: 20
                    });
                    showPopup(gisid, ol.extent.getCenter(e));
                } else {
                    alert("Building not found on map");
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
                    `<div class="building-details-content">${[
                    ['fingerprint', 'GIS ID', pd.gisid],
                    ['building', 'Building Usage', pd.building_usage],
                    ['home', 'Building Type', pd.building_type],
                    ['layer-group', 'Floors', pd.number_floor],
                    ['receipt', 'Total Bills', pd.number_bill],
                    ['store', 'Total Shops', pd.total_shops],
                    ['road', 'Road Name', pd.road_name],
                    ['map-pin', 'Zone', pd.zone]
                ].map(([i,l,v]) => `<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v || 'N/A'}</div></div>`).join('')}</div>`;

                let assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) =>
                        `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i+1)}</span>
                                <span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span>
                            </div>
                            <div class="assessment-body">
                                ${[['Owner', a.owner_name || a.present_owner_name], ['Phone', a.phone_number], ['Floor', a.floor], ['Usage', a.bill_usage], ['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                            </div>
                        </div>`
                    ).join('');

                let shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' :
                    shops.map(s => `
                        <div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name || 'Shop'}</div>
                            ${[['Category', s.shop_category], ['Owner', s.shop_owner_name], ['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                        </div>
                    `).join('');

                let html =
                    `
                    <div class="popup-header">
                        <h4><i class="fas fa-building"></i> Building Details</h4>
                        <button class="popup-close" onclick="closePopup()">&times;</button>
                    </div>
                    <div class="popup-tabs">
                        <button class="popup-tab ${currentActiveTab == 'building' ? 'active' : ''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                        <button class="popup-tab ${currentActiveTab == 'assessments' ? 'active' : ''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button>
                        <button class="popup-tab ${currentActiveTab == 'shops' ? 'active' : ''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${shops.length})</button>
                    </div>
                    <div id="tab-building" class="popup-tab-content ${currentActiveTab == 'building' ? 'active' : ''}">${buildingHtml}</div>
                    <div id="tab-assessments" class="popup-tab-content ${currentActiveTab == 'assessments' ? 'active' : ''}"><div style="padding:12px">${assessmentsHtml}</div></div>
                    <div id="tab-shops" class="popup-tab-content ${currentActiveTab == 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) {
                    popupOverlay.setPosition(coord);
                }

                $('.assessment-card').off('click').on('click', function() {
                    let id = $(this).data('id');
                    let num = $(this).data('assessment');
                    $(this).after(`
                        <div class="assessment-form-container">
                            <button class="close-form-btn">&times;</button>
                            <h4 style="color:#ffc107; margin-bottom:15px;">QC Form - ${num}</h4>
                            <form class="qc-form">
                                <input type="hidden" name="assessment_id" value="${id}">
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Square Feet:</label>
                                    <input type="number" name="qc_sqfeet" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Usage:</label>
                                    <select name="qc_usage" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                        <option value="">Select</option>
                                        <option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Industrial">Industrial</option>
                                    </select>
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">Tax Amount (₹):</label>
                                    <input type="number" name="tax_amount" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button type="submit" style="flex:1; background:#28a745; color:white; border:none; padding:10px; border-radius:8px;">Save</button>
                                    <button type="button" class="cancel-form-btn" style="flex:1; background:#dc3545; color:white; border:none; padding:10px; border-radius:8px;">Cancel</button>
                                </div>
                            </form>
                        </div>
                    `);

                    $('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        let hasValues = $(this).find('input[name="qc_sqfeet"]').val() &&
                            $(this).find('select[name="qc_usage"]').val() &&
                            $(this).find('input[name="tax_amount"]').val();
                        let $badge = $(this).closest('.assessment-card').find('.badge');
                        if (hasValues) {
                            $badge.removeClass('badge-warning').addClass('badge-success').html(
                                '<i class="fas fa-check-circle"></i> QC Complete');
                        } else {
                            $badge.removeClass('badge-success').addClass('badge-warning').html(
                                '<i class="fas fa-clock"></i> QC Pending');
                        }
                        alert('QC Saved! Status: ' + (hasValues ? 'QC Complete' : 'QC Pending'));
                        $('.assessment-form-container').remove();
                    });

                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== POLYGON STYLE FUNCTION ====================
            function polygonStyleFunction(feature) {
                let gisid = feature.get('gisid');
                let sqfeet = feature.get('sqfeet');
                let geometry = feature.getGeometry();
                let center;

                try {
                    center = geometry.getInteriorPoint();
                    if (!center) {
                        let extent = geometry.getExtent();
                        center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                    }
                } catch (e) {
                    let extent = geometry.getExtent();
                    center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                }

                let isVisible = feature.get('visible');
                if (isVisible === false) return null;

                return [
                    new ol.style.Style({
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
                        if (c && c.length) {
                            ps.addFeature(new ol.Feature({
                                geometry: new ol.geom.Polygon(c),
                                gisid: p.gisid,
                                sqfeet: p.sqfeet,
                                visible: true
                            }));
                        }
                    } catch (e) {
                        console.error("Error parsing polygon:", e);
                    }
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
                            if (c.length === 1 && Array.isArray(c[0][0])) {
                                c = c[0];
                            }
                            ls.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(c),
                                gisid: l.gisid
                            }));
                        }
                    } catch (e) {
                        console.error("Error parsing line:", e);
                    }
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
                    let feature = map.forEachFeatureAtPixel(e.pixel, function(f) {
                        return f;
                    });
                    if (feature && feature.get('gisid')) {
                        showPopup(feature.get('gisid'), e.coordinate);
                    } else if (popupElement) {
                        $(popupElement).hide();
                    }
                });

                map.on('pointermove', function(e) {
                    let hasFeature = map.forEachFeatureAtPixel(e.pixel, function(f) {
                        return f;
                    });
                    $('#map').css('cursor', hasFeature ? 'pointer' : '');
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

                let droneImg = wardData.drone_image;
                let hasDrone = false;

                if (droneImg && droneImg !== 'null' && droneImg !== '') {
                    try {
                        let imageUrl = droneImg;
                        if (!imageUrl.startsWith('http') && !imageUrl.startsWith('//')) {
                            imageUrl = '/' + imageUrl.replace(/^\/+/, '');
                        }

                        console.log("Loading drone image from:", imageUrl);

                        imageLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: imageUrl,
                                imageExtent: [
                                    parseFloat(wardData.extent_left),
                                    parseFloat(wardData.extent_bottom),
                                    parseFloat(wardData.extent_right),
                                    parseFloat(wardData.extent_top)
                                ],
                                projection: 'EPSG:3857'
                            }),
                            visible: true,
                            opacity: 0.8
                        });
                        hasDrone = true;
                        console.log("Drone image layer created successfully");
                    } catch (e) {
                        console.error("Error loading drone image:", e);
                    }
                }

                let bound = wardData.boundary;
                let boundExt = null;

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
                        let lons = bound[0].map(p => p[0]);
                        let lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math
                            .max(...lats)
                        ]);
                    } catch (e) {
                        console.error("Error parsing boundary:", e);
                    }
                }

                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                let zoom = 18;

                if (bound && bound[0] && bound[0].length) {
                    try {
                        let lons = bound[0].map(p => p[0]);
                        let lats = bound[0].map(p => p[1]);
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...
                            lats) + Math.max(...lats)) / 2]);
                        zoom = 18;
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

                if (hasDrone && imageLayer) {
                    map.addLayer(imageLayer);
                }
                if (boundaryLayer) {
                    map.addLayer(boundaryLayer);
                }

                setTimeout(() => {
                    if (boundExt) {
                        map.getView().fit(boundExt, {
                            padding: [50, 50, 50, 50],
                            duration: 1000
                        });
                    }
                }, 500);

                // Add panels to DOM
                $('body').append(`
                    <div class="layer-switcher panel" id="layerSwitcher">
                        <button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-layer-group"></i> Layers</h5>
                        <div class="layer-group">
                            <div class="group-title">Base Maps</div>
                            <label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label>
                            <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label>
                        </div>
                        <div class="layer-group">
                            <div class="group-title">Overlays</div>
                            <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
                            <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
                            <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>
                            ${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}
                        </div>
                    </div>
                `);

                $('body').append(`
                    <div class="map-legend panel" id="mapLegend">
                        <button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-info-circle"></i> Legend</h5>
                        <div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div>
                        <div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div>
                        <div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div>
                    </div>
                `);

                $('body').append(`
                    <div class="search-panel panel" id="searchPanel">
                        <button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-search"></i> Search Building</h5>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment, Zone...">
                            <button id="doSearchBtn"><i class="fas fa-search"></i> Go</button>
                        </div>
                        <div id="searchResults" class="search-results"></div>
                    </div>
                `);

                $('body').append(`
                    <div class="filter-panel panel" id="filterPanel">
                        <button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button>
                        <h5><i class="fas fa-filter"></i> Filter Buildings</h5>
                        <div class="filter-group">
                            <label>QC Status</label>
                            <select id="filterType">
                                <option value="all">All Buildings</option>
                                <option value="completed">QC Complete</option>
                                <option value="pending">QC Pending</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Min Floors</label>
                            <input type="number" id="filterMinFloors" placeholder="Min">
                        </div>
                        <div class="filter-group">
                            <label>Max Floors</label>
                            <input type="number" id="filterMaxFloors" placeholder="Max">
                        </div>
                        <div class="filter-actions">
                            <button class="apply-btn" id="applyFilterBtn">Apply</button>
                            <button class="reset-btn" id="resetFilterBtn">Reset</button>
                        </div>
                        <div class="filter-count" id="filterCount"></div>
                    </div>
                `);

                $('body').append(`
                    <div class="zoom-controls">
                        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                    </div>
                `);

                // Event listeners
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

                if (hasDrone) {
                    $('#toggleDrone').on('change', function() {
                        if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                    });
                }

                // Search button inside panel
                $('#doSearchBtn').on('click', function() {
                    searchBuildings($('#searchInput').val());
                });

                $('#searchInput').on('keypress', function(e) {
                    if (e.which === 13) {
                        searchBuildings($(this).val());
                    }
                });

                $('#applyFilterBtn').on('click', function() {
                    let type = $('#filterType').val();
                    let minF = $('#filterMinFloors').val();
                    let maxF = $('#filterMaxFloors').val();
                    let src = polygonLayer.getSource();
                    let fts = src.getFeatures();
                    let cnt = 0;

                    $.each(fts, function(i, f) {
                        let g = f.get('gisid');
                        let b = polygonDatas.find(p => p.gisid == g);
                        let show = true;

                        if (type === 'completed' && b) {
                            let has = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        has = true;
                                        return false;
                                    }
                                });
                            }
                            if (!has) show = false;
                        } else if (type === 'pending' && b) {
                            let has = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        has = true;
                                        return false;
                                    }
                                });
                            }
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
                    closeAllPanels();
                });

                $('#resetFilterBtn').on('click', function() {
                    $('#filterType').val('all');
                    $('#filterMinFloors, #filterMaxFloors').val('');
                    let src = polygonLayer.getSource();
                    $.each(src.getFeatures(), function(i, f) {
                        f.set('visible', true);
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(
                        `Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    closeAllPanels();
                });

                $('#zoomInBtn').on('click', function() {
                    let currentZoom = map.getView().getZoom();
                    map.getView().setZoom(currentZoom + 1);
                });

                $('#zoomOutBtn').on('click', function() {
                    let currentZoom = map.getView().getZoom();
                    map.getView().setZoom(currentZoom - 1);
                });

                // Button handlers
                $('#menuBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#layerSwitcher').addClass('open');
                    }
                });

                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#mapLegend').addClass('open');
                    }
                });

                $('#openSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#searchPanel').addClass('open');
                        setTimeout(function() {
                            $('#searchInput').focus();
                        }, 300);
                    }
                });

                $('#filterBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#filterPanel').addClass('open');
                    }
                });

                $('#locationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                    } else {
                        startLocationTracking();
                    }
                });

                // Close panels when clicking outside
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length &&
                        !$(e.target).closest('.action-btn').length &&
                        !$(e.target).closest('#centerMyLocationBtn').length) {
                        closeAllPanels();
                    }
                });

                refreshLayers();
            }

            // Start the application
            initMap();
            buildSearchIndex();

            // Handle window resize
            $(window).on('resize', function() {
                setTimeout(function() {
                    if (map) map.updateSize();
                }, 100);
            });
        });
    </script>
@endpush
