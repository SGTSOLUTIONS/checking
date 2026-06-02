@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Action Buttons - Desktop -->
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
            <button class="action-btn route-btn" id="routeBtn" title="Route">
                <i class="fas fa-route"></i>
                <span class="btn-label">Route</span>
            </button>
        </div>
    </div>

    <!-- Route Info Panel -->
    <div class="route-info panel" id="routeInfo">
        <button class="panel-close" id="closeRouteInfo">&times;</button>
        <h5><i class="fas fa-route"></i> Route Information</h5>
        <div id="routeSummary" class="route-summary"></div>
        <div id="directionsList" class="directions-list"></div>
        <button class="btn-start-nav" id="startNavigationBtn">
            <i class="fas fa-directions"></i> Open in Google Maps
        </button>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary"></div>
        <div>Calculating route...</div>
    </div>
@endsection

@push('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
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

        /* ── Mobile Bottom Nav ── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px 20px 0 0;
            z-index: 1003;
            padding: 8px 12px;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .mobile-nav-btn i {
            font-size: 20px;
            color: #aaa;
            margin-bottom: 4px;
        }

        .mobile-nav-btn span {
            font-size: 10px;
            color: #aaa;
            font-weight: 500;
        }

        .mobile-nav-btn.active {
            background: rgba(255, 68, 68, 0.2);
        }

        .mobile-nav-btn.active i,
        .mobile-nav-btn.active span {
            color: #ff4444;
        }

        .mobile-nav-btn:active {
            transform: scale(0.95);
        }

        /* ── Action Buttons (Desktop) ── */
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

        .action-btn.location-btn.active {
            background: #28a745;
        }

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

        @media (max-width: 768px) {
            .action-buttons {
                display: none;
            }
            .mobile-bottom-nav {
                display: flex;
            }
        }

        /* ── Panels ── */
        .panel {
            background: rgba(0, 0, 0, 0.95);
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

        .layer-switcher, .map-legend, .search-panel, .filter-panel, .route-info {
            display: none;
        }

        .layer-switcher.open, .map-legend.open, .search-panel.open, .filter-panel.open, .route-info.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            min-width: 200px;
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            left: 20px;
            min-width: 200px;
        }

        .search-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
        }

        .filter-panel {
            position: absolute;
            top: 100px;
            right: 20px;
            width: 320px;
        }

        .route-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
            max-height: 500px;
        }

        @media (max-width: 768px) {
            .layer-switcher, .map-legend, .search-panel, .filter-panel, .route-info {
                position: fixed;
                bottom: 80px;
                right: 10px;
                left: 10px;
                top: auto;
                width: auto;
                max-height: 60vh;
                z-index: 1004;
            }
            .map-legend {
                left: 10px;
                right: auto;
                min-width: 180px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Route Panel ── */
        .route-summary {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .route-summary div {
            margin: 5px 0;
            font-size: 14px;
        }
        .directions-list {
            max-height: 250px;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        .direction-step {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 12px;
            font-size: 13px;
        }
        .step-number {
            background: #ff4444;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
        }
        .step-instruction {
            margin-bottom: 4px;
        }
        .step-distance {
            font-size: 11px;
            color: #ffc107;
        }
        .btn-start-nav {
            width: 100%;
            padding: 12px;
            background: #28a745;
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-start-nav:active {
            transform: scale(0.98);
        }

        /* ── Loading ── */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 16px;
            z-index: 2000;
            display: none;
            text-align: center;
            color: white;
            gap: 12px;
            flex-direction: column;
            align-items: center;
        }
        .loading-spinner .spinner-border {
            width: 40px;
            height: 40px;
        }

        /* ── Center button ── */
        .center-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
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
        }
        @media (max-width: 768px) {
            .center-btn { bottom: 80px; left: 10px; }
        }
        @media (min-width: 769px) {
            .center-btn { bottom: auto; top: 160px; left: 20px; }
        }

        /* ── Layer Switcher ── */
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
        }

        /* ── Legend ── */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .legend-color {
            width: 24px;
            height: 16px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .legend-color.residential { background: #4CAF50; }
        .legend-color.commercial { background: #2196F3; }
        .legend-color.industrial { background: #FF9800; }
        .legend-color.institutional { background: #9C27B0; }
        .legend-color.mixed { background: #FF5722; }
        .legend-color.government { background: #607D8B; }
        .legend-color.vacant { background: #9E9E9E; }
        .legend-color.default { background: #ff4444; }
        .legend-color.road { background: #ffc107; height: 3px; }
        .legend-color.boundary { background: #ff0000; height: 3px; }

        /* ── Search ── */
        .search-box {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
            flex-wrap: wrap;
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
            min-width: 150px;
        }
        .search-box button {
            padding: 12px 20px;
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
        }

        /* ── Filter Panel ── */
        .filter-group {
            margin-bottom: 15px;
        }
        .filter-group > label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: #ffc107;
            font-weight: 600;
        }
        .filter-group select, .filter-group input[type="number"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 13px;
            outline: none;
        }
        .range-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .range-row input { flex: 1; }
        .range-sep { color: #888; font-size: 14px; flex-shrink: 0; }
        .filter-hint { font-size: 10px; color: #888; margin-top: 4px; }
        .filter-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 14px 0;
        }
        .filter-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #ff4444;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .apply-btn, .reset-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }
        .apply-btn { background: #28a745; color: white; }
        .reset-btn { background: #dc3545; color: white; }
        .filter-count {
            margin-top: 12px;
            font-size: 12px;
            color: #ffc107;
            text-align: center;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        /* ── Zoom Controls ── */
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
            z-index: 1000;
        }
        @media (max-width: 768px) {
            .zoom-controls { bottom: 80px; right: 10px; }
        }
        @media (min-width: 769px) {
            .zoom-controls { bottom: auto; top: 100px; right: 20px; left: auto; }
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
        .zoom-btn:first-child { border-bottom: 1px solid rgba(255, 255, 255, 0.2); }

        /* ── Popup ── */
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
            display: none;
        }
        @media (min-width: 769px) {
            .ol-popup {
                position: absolute !important;
                bottom: auto !important;
                width: auto !important;
                min-width: 400px !important;
                max-width: 500px !important;
                border-radius: 20px !important;
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
            z-index: 1;
        }
        .popup-header h4 { margin: 0; font-size: 18px; color: #ff4444; }
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
            flex-wrap: wrap;
            position: sticky;
            top: 70px;
            z-index: 1;
        }
        .popup-tab {
            flex: 1;
            background: none;
            border: none;
            color: #aaa;
            padding: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
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
        .popup-tab-content.active { display: block; }
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
        .detail-value { color: #eee; flex: 1; font-size: 13px; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 3px solid #ffc107;
            cursor: pointer;
            transition: background 0.2s;
        }
        .assessment-card:hover { background: rgba(255, 255, 255, 0.08); }
        .assessment-header {
            background: rgba(255, 193, 7, 0.15);
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            border-radius: 12px 12px 0 0;
        }
        .assessment-number {
            font-weight: 700;
            font-size: 13px;
            color: #ffc107;
        }
        .assessment-body { padding: 12px 15px; }
        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 12px;
            flex-wrap: wrap;
        }
        .assessment-label { width: 80px; color: #aaa; }
        .assessment-value { color: #fff; flex: 1; }
        .qc-status-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap;
        }
        .qc-field-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
        }
        .qc-field-chip.filled { background: rgba(40, 167, 69, 0.25); color: #66ff99; }
        .qc-field-chip.empty { background: rgba(255, 193, 7, 0.2); color: #ffd966; }
        .shop-item {
            background: rgba(255, 68, 68, 0.1);
            border-radius: 10px;
            padding: 12px;
            margin-top: 8px;
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
            display: block;
        }
        .assessment-form-container {
            margin: 10px;
            padding: 15px;
            background: #1a1a2e;
            border-radius: 12px;
            border-left: 3px solid #ff4444;
        }
        .toast-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            padding: 12px 20px;
            border-radius: 10px;
            min-width: 200px;
            text-align: center;
            animation: slideDown 0.3s ease;
            pointer-events: none;
        }
        @keyframes slideDown {
            from { top: -50px; opacity: 0; }
            to { top: 20px; opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>

    <script>
        $(document).ready(function() {

            // ==================== DATA FROM SERVER ====================
            let polygonDatas = @json($polygonDatas ?? []);
            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);
            let points = @json($points ?? []);
            let wardData = {
                ward_no: @json($ward->ward_no ?? ''),
                drone_image: @json($ward->drone_image ?? null),
                extent_left: @json($ward->extent_left ?? null),
                extent_bottom: @json($ward->extent_bottom ?? null),
                extent_right: @json($ward->extent_right ?? null),
                extent_top: @json($ward->extent_top ?? null),
                boundary: @json($ward->boundary ?? null)
            };

            if (!polygonDatas || !polygons) {
                console.error('Missing required data');
                alert('Error loading map data. Please refresh the page.');
                return;
            }

            console.log(`Loaded ${polygonDatas.length} buildings, ${polygons.length} polygons`);

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';
            let currentLocationMarker = null;
            let accuracyCircle = null;
            let currentPosition = null;
            let locationWatchId = null;
            let isLocationEnabled = false;
            let currentRoute = null;
            let routeLayer = null;
            let routeSource = null;
            let destinationMarker = null;
            let selectedFeature = null;
            let allBuildings = [];

            // ==================== BUILDING USAGE COLORS ====================
            const usageColors = {
                'RESIDENTIAL': '#4CAF50', 'COMMERCIAL': '#2196F3', 'INDUSTRIAL': '#FF9800',
                'INSTITUTIONAL': '#9C27B0', 'MIXED': '#FF5722', 'GOVERNMENT': '#607D8B',
                'VACANT': '#9E9E9E', 'EDUCATIONAL': '#00BCD4', 'HOSPITAL': '#E91E63',
                'HOTEL': '#795548', 'RELIGIOUS': '#FFC107', 'default': '#ff4444'
            };

            function getBuildingColor(buildingUsage) {
                if (!buildingUsage) return usageColors.default;
                const upper = buildingUsage.toUpperCase();
                for (const [key, color] of Object.entries(usageColors)) {
                    if (key !== 'default' && (upper === key || upper.includes(key))) return color;
                }
                return usageColors.default;
            }

            function isPointQCComplete(pt) {
                return !!(pt.qcsqfeet && String(pt.qcsqfeet).trim() !== '' && pt.qcusage && String(pt.qcusage).trim() !== '');
            }

            function showToast(message, type = 'info') {
                const colors = { success: '#28a745', error: '#dc3545', warning: '#ffc107', info: '#17a2b8' };
                const bg = colors[type] || colors.info;
                const id = 'toast_' + Date.now();
                const color = type === 'warning' ? '#333' : 'white';
                $('body').append(`<div id="${id}" class="toast-message" style="background:${bg};color:${color};">${message}</div>`);
                setTimeout(() => $(`#${id}`).fadeOut(300, function() { $(this).remove(); }), 3000);
            }

            function closeAllPanels() {
                $('#layerSwitcher,#mapLegend,#searchPanel,#filterPanel,#routeInfo').removeClass('open');
            }

            function formatDistance(meters) {
                if (!meters || isNaN(meters)) return '0 m';
                return meters < 1000 ? Math.round(meters) + ' m' : (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                if (!seconds || isNaN(seconds)) return '0 min';
                const mins = Math.floor(seconds / 60);
                if (mins < 60) return mins + ' min';
                const hrs = Math.floor(mins / 60);
                const rem = mins % 60;
                return hrs + 'h ' + rem + 'm';
            }

            function escapeHtml(text) {
                if (!text && text !== 0) return '';
                const d = document.createElement('div');
                d.textContent = text;
                return d.innerHTML;
            }

            // ==================== FIXED ROUTE FUNCTIONS (NO CORS ERROR) ====================
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    // Use Laravel proxy route - NO CORS ISSUES!
                    const url = `/api/route/${startCoord[0]}/${startCoord[1]}/${endCoord[0]}/${endCoord[1]}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code !== 'Ok' || !data.routes || !data.routes.length) {
                        throw new Error('No route found');
                    }
                    return data.routes[0];
                } catch (error) {
                    console.warn('Route fetch failed:', error);
                    // Fallback to straight line
                    const distance = calculateDistance(startCoord, endCoord);
                    return {
                        distance: distance,
                        duration: distance / 8.33,
                        geometry: {
                            type: 'LineString',
                            coordinates: [startCoord, endCoord]
                        },
                        legs: [{
                            steps: [
                                { maneuver: { type: 'depart', instruction: 'Start from your location' }, distance: distance / 2, duration: (distance / 8.33) / 2 },
                                { maneuver: { type: 'arrive', instruction: 'Arrive at destination' }, distance: distance / 2, duration: (distance / 8.33) / 2 }
                            ]
                        }]
                    };
                }
            }

            function calculateDistance(c1, c2) {
                const R = 6371000;
                const la1 = c1[1] * Math.PI / 180, la2 = c2[1] * Math.PI / 180;
                const dLa = (c2[1] - c1[1]) * Math.PI / 180;
                const dLo = (c2[0] - c1[0]) * Math.PI / 180;
                const a = Math.sin(dLa / 2) ** 2 + Math.cos(la1) * Math.cos(la2) * Math.sin(dLo / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function parseOSRMSteps(route) {
                if (!route.legs?.[0]?.steps) return [];
                return route.legs[0].steps.map((step, idx) => {
                    let icon = 'fas fa-arrow-right';
                    switch (step.maneuver.type) {
                        case 'depart': icon = 'fas fa-play'; break;
                        case 'arrive': icon = 'fas fa-flag-checkered'; break;
                        case 'turn':
                            if (step.maneuver.modifier === 'left') icon = 'fas fa-arrow-left';
                            if (step.maneuver.modifier === 'right') icon = 'fas fa-arrow-right';
                            break;
                    }
                    return {
                        instruction: step.maneuver.instruction || step.maneuver.type,
                        distance: formatDistance(step.distance),
                        icon
                    };
                });
            }

            function drawRouteOnMap(geometry) {
                if (routeLayer) map.removeLayer(routeLayer);
                if (routeSource) routeSource = new ol.source.Vector();
                routeLayer = new ol.layer.Vector({
                    source: routeSource,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#0066cc', width: 5, lineDash: [10, 8] })
                    })
                });
                if (geometry?.type === 'LineString' && geometry.coordinates) {
                    const coords = geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                    routeSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));
                    map.addLayer(routeLayer);
                    const extent = routeSource.getExtent();
                    if (extent && extent[0] !== Infinity) {
                        map.getView().fit(extent, { padding: [80, 80, 80, 80], duration: 800 });
                    }
                }
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker) {
                    showToast('Please enable your location first', 'warning');
                    return;
                }
                $('#loadingSpinner').css('display', 'flex');
                try {
                    const locCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry().getCoordinates();
                    const currentLL = ol.proj.toLonLat(locCoords);
                    const geometry = feature.getGeometry();
                    const rawCenter = geometry.getType() === 'Point' ? geometry.getCoordinates() : ol.extent.getCenter(geometry.getExtent());
                    const targetLL = ol.proj.toLonLat(rawCenter);
                    let route = await getRouteFromOSRM(currentLL, targetLL);
                    drawRouteOnMap(route.geometry);
                    const gisid = feature.get('gisid');
                    const buildingData = polygonDatas.find(p => p.gisid == gisid);
                    const buildingName = buildingData ? `GIS ID: ${gisid}` : `Building ${gisid}`;
                    const steps = parseOSRMSteps(route);
                    $('#routeSummary').html(`
                        <div><strong>Total Distance:</strong> ${formatDistance(route.distance)}</div>
                        <div><strong>Estimated Time:</strong> ${formatDuration(route.duration)}</div>
                        <div><strong>Destination:</strong> ${escapeHtml(buildingName)}</div>
                    `);
                    const $dl = $('#directionsList').empty();
                    if (steps.length) {
                        steps.forEach((s, i) => $dl.append(`
                            <div class="direction-step">
                                <div class="step-number">${i + 1}</div>
                                <div class="step-content">
                                    <div class="step-instruction"><i class="${s.icon} me-2"></i> ${escapeHtml(s.instruction)}</div>
                                    <div class="step-distance">${s.distance}</div>
                                </div>
                            </div>`));
                    } else {
                        $dl.append(`
                            <div class="direction-step"><div class="step-number">1</div><div class="step-content"><div class="step-instruction"><i class="fas fa-play"></i> Start from your location</div></div></div>
                            <div class="direction-step"><div class="step-number">2</div><div class="step-content"><div class="step-instruction"><i class="fas fa-flag-checkered"></i> Arrive at destination</div></div></div>`);
                    }
                    if (destinationMarker) map.removeLayer(destinationMarker);
                    destinationMarker = new ol.layer.Vector({
                        source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Point(ol.proj.fromLonLat(targetLL)) })] }),
                        style: new ol.style.Style({ image: new ol.style.Circle({ radius: 14, fill: new ol.style.Fill({ color: '#ff4444' }), stroke: new ol.style.Stroke({ color: '#fff', width: 3 }) }) })
                    });
                    map.addLayer(destinationMarker);
                    currentRoute = { startCoord: currentLL, endCoord: targetLL };
                    $('#routeInfo').addClass('open');
                    showToast('Route calculated successfully!', 'success');
                } catch (error) {
                    console.error('Route error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function clearRoute() {
                if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }
                if (destinationMarker) { map.removeLayer(destinationMarker); destinationMarker = null; }
                if (routeSource) routeSource?.clear();
                currentRoute = null;
                $('#routeInfo').removeClass('open');
            }

            function startNavigation() {
                if (currentRoute?.endCoord) {
                    const [lon, lat] = currentRoute.endCoord;
                    const [slon, slat] = currentRoute.startCoord;
                    window.open(`https://www.google.com/maps/dir/${slat},${slon}/${lat},${lon}`, '_blank');
                } else {
                    showToast('No route available for navigation', 'warning');
                }
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showToast('Geolocation not supported by your browser', 'error');
                    return;
                }
                $('#locationBtn,#mobileLocationBtn').addClass('active');
                isLocationEnabled = true;
                if (!$('#centerMyLocationBtn').length) {
                    $('body').append('<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>');
                    $('#centerMyLocationBtn').on('click', centerToMyLocation);
                }
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                        currentPosition = [pos.coords.longitude, pos.coords.latitude];
                        showToast('Location tracking enabled', 'success');
                        if (!sessionStorage.getItem('mapCentered')) {
                            centerToMyLocation();
                            sessionStorage.setItem('mapCentered', 'true');
                        }
                    },
                    err => {
                        const msgs = { [err.PERMISSION_DENIED]: 'Please allow location access', [err.POSITION_UNAVAILABLE]: 'Location information unavailable', [err.TIMEOUT]: 'Location request timed out' };
                        showToast(msgs[err.code] || 'Unknown error', 'error');
                        isLocationEnabled = false;
                        $('#locationBtn,#mobileLocationBtn').removeClass('active');
                        $('#centerMyLocationBtn').remove();
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000,
                        timeout: 15000
                    }
                );
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = navigator.geolocation.watchPosition(
                    pos => { updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy); currentPosition = [pos.coords.longitude, pos.coords.latitude]; },
                    err => console.warn('Watch position error:', err),
                    { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
                );
            }

            function stopLocationTracking() {
                if (locationWatchId) { navigator.geolocation.clearWatch(locationWatchId); locationWatchId = null; }
                if (currentLocationMarker) { map.removeLayer(currentLocationMarker); currentLocationMarker = null; }
                if (accuracyCircle) { map.removeLayer(accuracyCircle); accuracyCircle = null; }
                isLocationEnabled = false;
                $('#locationBtn,#mobileLocationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentPosition = null;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                const coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];
                if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                if (accuracyCircle) map.removeLayer(accuracyCircle);
                accuracyCircle = new ol.layer.Vector({
                    source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Circle(coords, accuracy) })] }),
                    style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ff4444', width: 2 }), fill: new ol.style.Fill({ color: 'rgba(255,68,68,0.15)' }) })
                });
                map.addLayer(accuracyCircle);
                currentLocationMarker = new ol.layer.Vector({
                    source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Point(coords) })] }),
                    style: new ol.style.Style({ image: new ol.style.Circle({ radius: 12, fill: new ol.style.Fill({ color: '#ff4444' }), stroke: new ol.style.Stroke({ color: '#fff', width: 3 }) }) })
                });
                map.addLayer(currentLocationMarker);
            }

            function centerToMyLocation() {
                if (currentPosition) {
                    map.getView().animate({ center: ol.proj.fromLonLat(currentPosition), zoom: 19, duration: 600 });
                    showToast('Centered on your location', 'info');
                } else {
                    showToast('Location not available', 'warning');
                    startLocationTracking();
                }
            }

            // ==================== SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = polygonDatas.map(building => {
                    const info = {
                        gisid: building.gisid, building_usage: building.building_usage, building_type: building.building_type,
                        road_name: building.road_name, zone: building.zone, number_floor: building.number_floor, sqfeet: building.sqfeet,
                        coordinates: null,
                        assessments: (building.pointdata || []).map(a => ({ id: a.id, assessment: a.assessment, owner_name: a.owner_name || a.present_owner_name, phone: a.phone || a.phone_number, bill_usage: a.bill_usage, floor: a.floor, qcsqfeet: a.qcsqfeet, qcusage: a.qcusage }))
                    };
                    for (const poly of polygons) {
                        if (poly.gisid == building.gisid) {
                            try {
                                const c = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                                if (c?.[0]?.[0]) {
                                    let sx = 0, sy = 0, n = 0;
                                    for (const pt of c[0]) { if (pt?.length >= 2 && !isNaN(pt[0]) && !isNaN(pt[1])) { sx += pt[0]; sy += pt[1]; n++; } }
                                    if (n > 0) info.coordinates = [sx / n, sy / n];
                                }
                            } catch(e) {}
                            break;
                        }
                    }
                    return info;
                });
                console.log('Search index built:', allBuildings.length, 'buildings');
            }

            // ==================== POLYGON STYLE ====================
            function polygonStyleFunction(feature) {
                if (feature.get('visible') === false) return null;
                const gisid = feature.get('gisid');
                const buildingData = polygonDatas.find(p => p.gisid == gisid);
                const fillColor = getBuildingColor(buildingData?.building_usage);
                const geometry = feature.getGeometry();
                let center;
                try { center = geometry.getInteriorPoint(); } catch(e) { const ext = geometry.getExtent(); center = new ol.geom.Point([(ext[0] + ext[2]) / 2, (ext[1] + ext[3]) / 2]); }
                return [
                    new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ffffff', width: 2 }), fill: new ol.style.Fill({ color: fillColor }) }),
                    new ol.style.Style({ geometry: center, text: new ol.style.Text({ text: `${gisid}`, font: 'bold 10px Arial', fill: new ol.style.Fill({ color: '#fff' }), stroke: new ol.style.Stroke({ color: '#000', width: 2 }) }) })
                ];
            }

            // ==================== SEARCH ====================
            function searchBuildings(text) {
                if (!text?.trim()) { $('#searchResults').html('<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>'); return; }
                const term = text.toLowerCase().trim();
                const results = [];
                for (const b of allBuildings) {
                    let match = false, type = '', val = '';
                    if (b.gisid?.toLowerCase().includes(term)) { match = true; type = 'GIS ID'; val = b.gisid; }
                    else if (b.building_usage?.toLowerCase().includes(term)) { match = true; type = 'Building Usage'; val = b.building_usage; }
                    else if (b.road_name?.toLowerCase().includes(term)) { match = true; type = 'Road Name'; val = b.road_name; }
                    else if (b.zone?.toLowerCase().includes(term)) { match = true; type = 'Zone'; val = b.zone; }
                    else {
                        for (const a of b.assessments) {
                            if (a.assessment?.toString().toLowerCase().includes(term)) { match = true; type = 'Assessment No'; val = a.assessment; break; }
                            if (a.owner_name?.toLowerCase().includes(term)) { match = true; type = 'Owner Name'; val = a.owner_name; break; }
                            if (a.phone?.toLowerCase().includes(term)) { match = true; type = 'Phone'; val = a.phone; break; }
                        }
                    }
                    if (match) results.push({ gisid: b.gisid, matchType: type, matchValue: val, building: b, coordinates: b.coordinates });
                }
                const $res = $('#searchResults').empty();
                if (!results.length) { $res.html('<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>'); return; }
                for (const r of results) {
                    const lon = r.coordinates?.[0] ?? ''; const lat = r.coordinates?.[1] ?? '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}"><div class="result-gisid"><i class="fas fa-building"></i> ${escapeHtml(r.gisid)}</div><div class="result-owner"><i class="fas fa-tag"></i> ${escapeHtml(r.matchType)}: ${escapeHtml(r.matchValue)}</div><div class="result-owner"><i class="fas fa-location-dot"></i> ${escapeHtml(r.building.road_name || 'No road')} | ${escapeHtml(r.building.zone || 'No zone')}</div><button class="direction-btn"><i class="fas fa-directions"></i> Get Directions</button></div>`);
                }
                $('#searchResults').off('click', '.search-result-item').on('click', '.search-result-item', function(e) {
                    if (!$(e.target).hasClass('direction-btn') && !$(e.target).closest('.direction-btn').length) {
                        zoomToBuilding($(this).data('gisid'));
                        closeAllPanels();
                    }
                });
                $('#searchResults').off('click', '.direction-btn').on('click', '.direction-btn', function(e) {
                    e.stopPropagation();
                    const gisid = $(this).closest('.search-result-item').data('gisid');
                    const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid);
                    if (feature) { selectedFeature = feature; calculateAndDisplayRoute(feature); closeAllPanels(); }
                    else showToast('Building not found on map', 'error');
                });
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer) return;
                const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid);
                if (feature) {
                    map.getView().fit(feature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 800 });
                    showPopup(gisid, ol.extent.getCenter(feature.getGeometry().getExtent()));
                } else { showToast('Building not found on map', 'error'); }
            }

            // ==================== POPUP ====================
            function createPopup() {
                popupElement = $('<div>', { class: 'ol-popup' })[0];
                $('body').append(popupElement);
                return new ol.Overlay({ element: popupElement, positioning: 'bottom-center', stopEvent: true, offset: [0, -10] });
            }

            window.closePopup = function() { $(popupElement).hide(); };
            window.switchTab = function(t) { $('.popup-tab-content, .popup-tab').removeClass('active'); $('#tab-' + t).addClass('active'); $(`.popup-tab[data-tab="${t}"]`).addClass('active'); currentActiveTab = t; };

            function buildQCChips(pt) {
                const sqChip = pt.qcsqfeet && String(pt.qcsqfeet).trim() ? `<span class="qc-field-chip filled"><i class="fas fa-ruler-combined"></i> ${escapeHtml(pt.qcsqfeet)} sqft</span>` : `<span class="qc-field-chip empty"><i class="fas fa-ruler-combined"></i> Sqft missing</span>`;
                const useChip = pt.qcusage && String(pt.qcusage).trim() ? `<span class="qc-field-chip filled"><i class="fas fa-tag"></i> ${escapeHtml(pt.qcusage)}</span>` : `<span class="qc-field-chip empty"><i class="fas fa-tag"></i> Usage missing</span>`;
                return sqChip + useChip;
            }

            function showPopup(gisid, coord) {
                const pd = polygonDatas.find(p => p.gisid == gisid);
                if (!pd) return;
                const assessments = pd.pointdata || [];
                const shops = assessments.flatMap(a => (a.shops || []).map(s => ({ ...s, assessmentNumber: a.assessment || 'Bill' })));
                const buildingHtml = [
                    ['fingerprint', 'GIS ID', pd.gisid], ['building', 'Building Usage', pd.building_usage], ['home', 'Building Type', pd.building_type],
                    ['layer-group', 'Floors', pd.number_floor], ['receipt', 'Total Bills', pd.number_bill], ['store', 'Total Shops', pd.total_shops],
                    ['road', 'Road Name', pd.road_name], ['map-pin', 'Zone', pd.zone]
                ].map(([icon, label, val]) => `<div class="detail-row"><div class="detail-label"><i class="fas fa-${icon}"></i> ${label}:</div><div class="detail-value">${escapeHtml(val) || 'N/A'}</div></div>`).join('');
                const assessmentsHtml = !assessments.length ? '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' : assessments.map((a, i) => {
                    const qcComplete = isPointQCComplete(a);
                    const badgeCls = qcComplete ? 'badge-success' : 'badge-warning';
                    const badgeIcon = qcComplete ? 'fa-check-circle' : 'fa-clock';
                    const badgeTxt = qcComplete ? 'QC Complete' : 'QC Pending';
                    return `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${escapeHtml(a.assessment || '')}" data-qcsqfeet="${escapeHtml(a.qcsqfeet || '')}" data-qcusage="${escapeHtml(a.qcusage || '')}"><div class="assessment-header"><span class="assessment-number"><i class="fas fa-file-invoice"></i> ${escapeHtml(a.assessment || 'Assessment ' + (i + 1))}</span><span class="${badgeCls}"><i class="fas ${badgeIcon}"></i> ${badgeTxt}</span></div><div class="assessment-body">${[['Owner', a.owner_name || a.present_owner_name], ['Phone', a.phone_number], ['Floor', a.floor], ['Usage', a.bill_usage], ['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${escapeHtml(v) || 'N/A'}</div></div>`).join('')}<div class="qc-status-row"><span style="font-size:10px;color:#aaa;margin-right:4px;">QC Fields:</span>${buildQCChips(a)}</div></div></div>`;
                }).join('');
                const shopsHtml = !shops.length ? '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' : shops.map(s => `<div class="shop-item"><div class="shop-name"><i class="fas fa-store"></i> ${escapeHtml(s.shop_name || 'Shop')}</div>${[['Category', s.shop_category], ['Owner', s.shop_owner_name], ['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${escapeHtml(v) || 'N/A'}</div></div>`).join('')}</div>`).join('');
                const html = `<div class="popup-header"><h4><i class="fas fa-building"></i> Building Details</h4><button class="popup-close" onclick="closePopup()">&times;</button></div><div class="popup-tabs"><button class="popup-tab ${currentActiveTab=='building'?'active':''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button><button class="popup-tab ${currentActiveTab=='assessments'?'active':''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button><button class="popup-tab ${currentActiveTab=='shops'?'active':''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${shops.length})</button></div><div id="tab-building" class="popup-tab-content ${currentActiveTab=='building'?'active':''}">${buildingHtml}</div><div id="tab-assessments" class="popup-tab-content ${currentActiveTab=='assessments'?'active':''}"><div style="padding:12px">${assessmentsHtml}</div></div><div id="tab-shops" class="popup-tab-content ${currentActiveTab=='shops'?'active':''}"><div style="padding:16px">${shopsHtml}</div></div><div style="padding:16px; border-top:1px solid rgba(255,255,255,0.1);"><button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button></div>`;
                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coord);
                $('#routeFromPopupBtn').off('click').on('click', function() { closePopup(); const f = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid); if (f) { selectedFeature = f; calculateAndDisplayRoute(f); } });
                $(popupElement).off('click', '.assessment-card').on('click', '.assessment-card', function(e) {
                    if ($(this).next('.assessment-form-container').length) return;
                    $('.assessment-form-container').remove();
                    const $card = $(this);
                    const id = $card.data('id');
                    const num = $card.data('assessment');
                    const existSqfeet = $card.data('qcsqfeet') || '';
                    const existUsage = $card.data('qcusage') || '';
                    const usageOptions = ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Mixed', 'Government', 'Vacant', 'Educational', 'Hospital', 'Hotel', 'Religious'].map(u => `<option value="${u}" ${existUsage === u ? 'selected' : ''}>${u}</option>`).join('');
                    $card.after(`<div class="assessment-form-container"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;"><h4 style="color:#ffc107;margin:0;font-size:14px;"><i class="fas fa-edit"></i> QC Form — ${escapeHtml(num)}</h4><button class="close-form-btn" style="background:none;border:none;color:#ff4444;font-size:20px;cursor:pointer;">&times;</button></div><form class="qc-form"><input type="hidden" name="assessment_id" value="${id}"><div style="margin-bottom:12px;"><label style="color:#ffc107;font-size:12px;display:block;margin-bottom:6px;"><i class="fas fa-ruler-combined"></i> QC Square Feet</label><input type="number" name="qc_sqfeet" value="${escapeHtml(existSqfeet)}" placeholder="Enter square feet" style="width:100%;padding:10px;border-radius:8px;border:1px solid #ff4444;background:#0f0f1a;color:white;font-size:13px;"></div><div style="margin-bottom:12px;"><label style="color:#ffc107;font-size:12px;display:block;margin-bottom:6px;"><i class="fas fa-tag"></i> QC Usage</label><select name="qc_usage" style="width:100%;padding:10px;border-radius:8px;border:1px solid #ff4444;background:#0f0f1a;color:white;font-size:13px;"><option value="">— Select Usage —</option>${usageOptions}</select></div><div style="display:flex;gap:10px;margin-top:14px;"><button type="submit" style="flex:1;background:#28a745;color:white;border:none;padding:11px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;"><i class="fas fa-save"></i> Save QC</button><button type="button" class="cancel-form-btn" style="flex:1;background:#dc3545;color:white;border:none;padding:11px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;">Cancel</button></div></form></div>`);
                    $card.next('.assessment-form-container').find('.close-form-btn, .cancel-form-btn').on('click', function() { $(this).closest('.assessment-form-container').remove(); });
                    $card.next('.assessment-form-container').find('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        const $form = $(this);
                        const aId = $form.find('[name="assessment_id"]').val();
                        const qcSqfeet = $form.find('[name="qc_sqfeet"]').val().trim();
                        const qcUsage = $form.find('[name="qc_usage"]').val().trim();
                        const isComplete = !!(qcSqfeet && qcUsage);
                        const $badge = $card.find('.badge');
                        if (isComplete) { $badge.removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check-circle"></i> QC Complete'); }
                        else { $badge.removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-clock"></i> QC Pending'); }
                        const fakePoint = { qcsqfeet: qcSqfeet, qcusage: qcUsage };
                        $card.find('.qc-status-row').html('<span style="font-size:10px;color:#aaa;margin-right:4px;">QC Fields:</span>' + buildQCChips(fakePoint));
                        $card.data('qcsqfeet', qcSqfeet).data('qcusage', qcUsage);
                        for (const building of polygonDatas) {
                            if (!building.pointdata) continue;
                            for (const pt of building.pointdata) { if (pt.id == aId) { pt.qcsqfeet = qcSqfeet || null; pt.qcusage = qcUsage || null; break; } }
                        }
                        $.ajax({ url: '/commissioner/qc-update', method: 'POST', data: { _token: $('meta[name="csrf-token"]').attr('content'), assessment_id: aId, qcsqfeet: qcSqfeet, qcusage: qcUsage }, success: () => showToast('QC saved successfully', 'success'), error: () => showToast('QC saved locally (server error)', 'warning') });
                        $form.closest('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);
                const ps = new ol.source.Vector();
                for (const p of polygons) {
                    try {
                        const c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (c?.length) { ps.addFeature(new ol.Feature({ geometry: new ol.geom.Polygon(c), gisid: p.gisid, sqfeet: p.sqfeet, visible: true })); }
                    } catch(e) { console.error('Polygon parse error:', e); }
                }
                polygonLayer = new ol.layer.Vector({ source: ps, style: polygonStyleFunction, visible: true });
                const ls = new ol.source.Vector();
                for (const l of lines) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (c?.length) { if (c.length === 1 && Array.isArray(c[0][0])) c = c[0]; ls.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(c), gisid: l.gisid })); }
                    } catch(e) { console.error('Line parse error:', e); }
                }
                lineLayer = new ol.layer.Vector({ source: ls, style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ffc107', width: 3 }) }), visible: true });
                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);
            }

            // ==================== MAP INTERACTIONS ====================
            function setupMapInteractions() {
                map.on('click', function(e) {
                    let clicked = null;
                    map.forEachFeatureAtPixel(e.pixel, function(feature, layer) { if (feature.get('gisid') && layer === polygonLayer) { clicked = feature; return true; } });
                    if (clicked) { selectedFeature = clicked; showPopup(clicked.get('gisid'), e.coordinate); } else { $(popupElement).hide(); }
                });
                map.on('pointermove', function(e) { const hasFeature = map.forEachFeatureAtPixel(e.pixel, (f, l) => f.get('gisid') && l === polygonLayer); map.getTargetElement().style.cursor = hasFeature ? 'pointer' : ''; });
            }

            // ==================== MAP INIT ====================
            function initMap() {
                osmLayer = new ol.layer.Tile({ source: new ol.source.OSM(), visible: true });
                satelliteLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' }), visible: false });
                let hasDrone = false;
                const droneImg = wardData.drone_image;
                if (droneImg && droneImg !== 'null' && droneImg !== '') {
                    try {
                        let imageUrl = droneImg.startsWith('http') || droneImg.startsWith('//') ? droneImg : '/' + droneImg.replace(/^\/+/, '');
                        imageLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: imageUrl, imageExtent: [parseFloat(wardData.extent_left), parseFloat(wardData.extent_bottom), parseFloat(wardData.extent_right), parseFloat(wardData.extent_top)], projection: 'EPSG:3857' }), visible: true, opacity: 0.8 });
                        hasDrone = true;
                    } catch(e) { console.error('Drone image error:', e); }
                }
                let boundExt = null;
                const bound = wardData.boundary;
                if (bound?.length && bound[0]?.length) {
                    try {
                        const bc = bound[0].map(c => ol.proj.fromLonLat(c));
                        boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Polygon([bc]) })] }), style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ff0000', width: 3, lineDash: [10, 5] }), fill: new ol.style.Fill({ color: 'rgba(255,0,0,0.05)' }) }), visible: true });
                        const lons = bound[0].map(p => p[0]), lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.transformExtent([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math.max(...lats)], 'EPSG:4326', 'EPSG:3857');
                    } catch(e) { console.error('Boundary error:', e); }
                }
                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                if (bound?.[0]?.length) { try { const lons = bound[0].map(p => p[0]), lats = bound[0].map(p => p[1]); center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]); } catch(e) {} }
                map = new ol.Map({ target: 'map', layers: [osmLayer, satelliteLayer], view: new ol.View({ center, zoom: 18 }) });
                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);
                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);
                refreshLayers();
                setupMapInteractions();
                if (boundExt) { setTimeout(() => map.getView().fit(boundExt, { padding: [50, 50, 50, 50], duration: 1000 }), 500); }

                // Mobile Bottom Nav
                $('body').append(`<div class="mobile-bottom-nav"><button class="mobile-nav-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i><span>Layers</span></button><button class="mobile-nav-btn" id="mobileSearchBtn"><i class="fas fa-search"></i><span>Search</span></button><button class="mobile-nav-btn" id="mobileLocationBtn"><i class="fas fa-location-dot"></i><span>Location</span></button><button class="mobile-nav-btn" id="mobileRouteBtn"><i class="fas fa-route"></i><span>Route</span></button><button class="mobile-nav-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i><span>Filter</span></button></div>`);

                // Layer Switcher Panel
                $('body').append(`<div class="layer-switcher panel" id="layerSwitcher"><button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button><h5><i class="fas fa-layer-group"></i> Layers</h5><div class="layer-group"><div class="group-title">Base Maps</div><label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label><label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label></div><div class="layer-group"><div class="group-title">Overlays</div><label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label><label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label><label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}</div></div>`);

                // Legend Panel
                $('body').append(`<div class="map-legend panel" id="mapLegend"><button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button><h5><i class="fas fa-info-circle"></i> Legend</h5><div class="legend-item"><div class="legend-color residential"></div><span>Residential</span></div><div class="legend-item"><div class="legend-color commercial"></div><span>Commercial</span></div><div class="legend-item"><div class="legend-color industrial"></div><span>Industrial</span></div><div class="legend-item"><div class="legend-color institutional"></div><span>Institutional</span></div><div class="legend-item"><div class="legend-color mixed"></div><span>Mixed Use</span></div><div class="legend-item"><div class="legend-color government"></div><span>Government</span></div><div class="legend-item"><div class="legend-color vacant"></div><span>Vacant</span></div><div class="legend-item"><div class="legend-color default"></div><span>Other / Unknown</span></div><div style="border-top:1px solid rgba(255,255,255,0.1);margin:10px 0;"></div><div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div><div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div></div>`);

                // Search Panel
                $('body').append(`<div class="search-panel panel" id="searchPanel"><button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button><h5><i class="fas fa-search"></i> Search Building</h5><div class="search-box"><input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment..."><button id="doSearchBtn"><i class="fas fa-search"></i></button></div><div id="searchResults" class="search-results"><div class="empty-state"><i class="fas fa-search"></i><p>Enter a search term above</p></div></div></div>`);

                // Filter Panel
                $('body').append(`<div class="filter-panel panel" id="filterPanel"><button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button><h5><i class="fas fa-filter"></i> Filter Buildings</h5><div class="filter-section-title"><i class="fas fa-building"></i> Building Properties</div><div class="filter-group"><label>Building Usage</label><select id="filterUsage"><option value="all">All Buildings</option><option value="RESIDENTIAL">Residential</option><option value="COMMERCIAL">Commercial</option><option value="INDUSTRIAL">Industrial</option><option value="INSTITUTIONAL">Institutional</option><option value="MIXED">Mixed Use</option><option value="GOVERNMENT">Government</option><option value="VACANT">Vacant</option></select></div><div class="filter-group"><label>Number of Floors</label><div class="range-row"><input type="number" id="filterMinFloors" placeholder="Min" min="0"><span class="range-sep">–</span><input type="number" id="filterMaxFloors" placeholder="Max" min="0"></div></div><hr class="filter-divider"><div class="filter-section-title"><i class="fas fa-clipboard-check"></i> QC Status</div><div class="filter-group"><label>QC Completion</label><select id="filterQC"><option value="all">All Buildings</option><option value="completed">QC Complete (both fields filled)</option><option value="partial">Partial QC (one field filled)</option><option value="pending">QC Pending (no fields filled)</option></select></div><hr class="filter-divider"><div class="filter-section-title"><i class="fas fa-ruler-combined"></i> Area Variation</div><div class="filter-group"><label>Variation Status</label><select id="filterAreaVariation"><option value="all">All</option><option value="match">Match (≤ 5% difference)</option><option value="variation">Variation (> 5% difference)</option><option value="over">Over-reported (survey > MIS)</option><option value="under">Under-reported (survey < MIS)</option></select></div><div class="filter-group"><label>Absolute Variation Range (sq.ft)</label><div class="range-row"><input type="number" id="filterAreaVarMin" placeholder="Min" min="0"><span class="range-sep">–</span><input type="number" id="filterAreaVarMax" placeholder="Max" min="0"></div><div class="filter-hint">Filters by |surveyed sqft − MIS plot area|</div></div><div class="filter-group"><label>Variation Percentage Range (%)</label><div class="range-row"><input type="number" id="filterVarPctMin" placeholder="Min %" min="0" max="999" step="0.1"><span class="range-sep">–</span><input type="number" id="filterVarPctMax" placeholder="Max %" min="0" max="999" step="0.1"></div><div class="filter-hint">Percentage difference vs MIS plot area</div></div><div class="filter-actions"><button class="apply-btn" id="applyFilterBtn"><i class="fas fa-check"></i> Apply</button><button class="reset-btn" id="resetFilterBtn"><i class="fas fa-redo"></i> Reset</button></div><div class="filter-count" id="filterCount"></div></div>`);

                // Zoom Controls
                $('body').append(`<div class="zoom-controls"><button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button><button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button></div>`);

                // ===================== EVENT LISTENERS =====================
                $('#menuBtn,#mobileMenuBtn').on('click', function(e) { e.stopPropagation(); const was = $('#layerSwitcher').hasClass('open'); closeAllPanels(); if (!was) $('#layerSwitcher').addClass('open'); });
                $('#legendBtn').on('click', function(e) { e.stopPropagation(); const was = $('#mapLegend').hasClass('open'); closeAllPanels(); if (!was) $('#mapLegend').addClass('open'); });
                $('#openSearchBtn,#mobileSearchBtn').on('click', function(e) { e.stopPropagation(); const was = $('#searchPanel').hasClass('open'); closeAllPanels(); if (!was) { $('#searchPanel').addClass('open'); setTimeout(() => $('#searchInput').focus(), 300); } });
                $('#filterBtn,#mobileFilterBtn').on('click', function(e) { e.stopPropagation(); const was = $('#filterPanel').hasClass('open'); closeAllPanels(); if (!was) $('#filterPanel').addClass('open'); });
                $('#locationBtn,#mobileLocationBtn').on('click', function() { isLocationEnabled ? (stopLocationTracking(), clearRoute()) : startLocationTracking(); });
                $('#routeBtn,#mobileRouteBtn').on('click', async function() { if (!selectedFeature) { showToast('Please select a building first', 'warning'); $('#openSearchBtn').click(); return; } if (!isLocationEnabled) { if (confirm('Enable location for route calculation?')) { startLocationTracking(); const tryRoute = (attempt) => { setTimeout(() => { if (currentLocationMarker) calculateAndDisplayRoute(selectedFeature); else if (attempt < 3) tryRoute(attempt + 1); else showToast('Could not get location. Try again.', 'error'); }, 2000); }; tryRoute(1); } return; } await calculateAndDisplayRoute(selectedFeature); });
                $('body').on('change', 'input[name="baseLayer"]', function() { currentBaseLayer = $(this).val(); osmLayer.setVisible(currentBaseLayer === 'osm'); satelliteLayer.setVisible(currentBaseLayer === 'satellite'); });
                $('body').on('change', '#toggleBuildings', function() { if (polygonLayer) polygonLayer.setVisible($(this).is(':checked')); });
                $('body').on('change', '#toggleRoads', function() { if (lineLayer) lineLayer.setVisible($(this).is(':checked')); });
                $('body').on('change', '#toggleBoundary', function() { if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked')); });
                if (hasDrone) { $('body').on('change', '#toggleDrone', function() { if (imageLayer) imageLayer.setVisible($(this).is(':checked')); }); }
                $('#doSearchBtn').on('click', () => searchBuildings($('#searchInput').val()));
                $('body').on('keypress', '#searchInput', function(e) { if (e.which === 13) searchBuildings($(this).val()); });

                // Apply Filter
                $('#applyFilterBtn').on('click', function() {
                    const usage = $('#filterUsage').val(), qcStatus = $('#filterQC').val(), minF = $('#filterMinFloors').val(), maxF = $('#filterMaxFloors').val();
                    const areaVarStatus = $('#filterAreaVariation').val(), areaVarMin = $('#filterAreaVarMin').val(), areaVarMax = $('#filterAreaVarMax').val();
                    const varPctMin = $('#filterVarPctMin').val(), varPctMax = $('#filterVarPctMax').val();
                    const src = polygonLayer.getSource();
                    let cnt = 0;
                    src.getFeatures().forEach(function(feature) {
                        const gisid = feature.get('gisid');
                        const b = polygonDatas.find(p => p.gisid == gisid);
                        let show = true;
                        if (!b) { feature.set('visible', false); return; }
                        if (show && usage !== 'all') { const bu = (b.building_usage || '').toUpperCase(); if (bu !== usage && !bu.includes(usage)) show = false; }
                        if (show && (minF || maxF)) { const fl = parseInt(b.number_floor) || 0; if (minF && fl < parseInt(minF)) show = false; if (maxF && fl > parseInt(maxF)) show = false; }
                        if (show && qcStatus !== 'all') {
                            const pts = b.pointdata || []; let filledBoth = 0, filledOne = 0;
                            pts.forEach(pt => { const hasSq = !!(pt.qcsqfeet && String(pt.qcsqfeet).trim()); const hasUse = !!(pt.qcusage && String(pt.qcusage).trim()); if (hasSq && hasUse) filledBoth++; else if (hasSq || hasUse) filledOne++; });
                            if (qcStatus === 'completed' && filledBoth === 0) show = false;
                            if (qcStatus === 'partial' && filledOne === 0) show = false;
                            if (qcStatus === 'pending' && (filledBoth > 0 || filledOne > 0)) show = false;
                        }
                        if (show && (areaVarStatus !== 'all' || areaVarMin || areaVarMax || varPctMin || varPctMax)) {
                            const sqfeet = parseFloat(feature.get('sqfeet') || b.sqfeet) || 0;
                            let misArea = 0;
                            (b.pointdata || []).forEach(pt => { misArea += parseFloat(pt.mis_plot_area || 0); });
                            const variation = sqfeet - misArea, absVariation = Math.abs(variation), varPct = misArea > 0 ? (absVariation / misArea) * 100 : 0;
                            if (areaVarStatus === 'match' && varPct > 5) show = false;
                            if (areaVarStatus === 'variation' && varPct <= 5) show = false;
                            if (areaVarStatus === 'over' && variation <= 0) show = false;
                            if (areaVarStatus === 'under' && variation >= 0) show = false;
                            if (areaVarMin && absVariation < parseFloat(areaVarMin)) show = false;
                            if (areaVarMax && absVariation > parseFloat(areaVarMax)) show = false;
                            if (varPctMin && varPct < parseFloat(varPctMin)) show = false;
                            if (varPctMax && varPct > parseFloat(varPctMax)) show = false;
                        }
                        feature.set('visible', show);
                        if (show) cnt++;
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    const total = src.getFeatures().length;
                    $('#filterCount').html(`<i class="fas fa-building"></i> Showing <strong>${cnt}</strong> of <strong>${total}</strong> buildings`);
                    closeAllPanels();
                    showToast(`Filter applied: ${cnt} buildings shown`, 'info');
                });

                $('#resetFilterBtn').on('click', function() {
                    $('#filterUsage,#filterQC,#filterAreaVariation').val('all');
                    $('#filterMinFloors,#filterMaxFloors,#filterAreaVarMin,#filterAreaVarMax,#filterVarPctMin,#filterVarPctMax').val('');
                    const src = polygonLayer.getSource();
                    src.getFeatures().forEach(f => f.set('visible', true));
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    const total = src.getFeatures().length;
                    $('#filterCount').html(`<i class="fas fa-building"></i> Showing <strong>${total}</strong> of <strong>${total}</strong> buildings`);
                    closeAllPanels();
                    showToast('Filters reset', 'info');
                });

                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));
                $('#closeRouteInfo').on('click', clearRoute);
                $('#startNavigationBtn').on('click', startNavigation);

                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length && !$(e.target).closest('.action-btn').length && !$(e.target).closest('.mobile-nav-btn').length && !$(e.target).closest('#centerMyLocationBtn').length && !$(e.target).closest('.zoom-btn').length) {
                        closeAllPanels();
                    }
                });
            }

            initMap();
            buildSearchIndex();

            $(window).on('resize', function() {
                setTimeout(() => { if (map) map.updateSize(); }, 100);
            });
        });
    </script>
@endpush
