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
            <i class="fas fa-play"></i> Start Navigation
        </button>
    </div>

    <!-- Navigation Header (Mobile Style) -->
    <div class="navigation-header" id="navigationHeader">
        <button class="nav-close" id="closeNavigation">&times;</button>
        <div class="navigation-eta">
            <div class="eta-time" id="etaTime">-- min</div>
            <div class="eta-distance" id="etaDistance">-- km</div>
        </div>
        <div class="navigation-address" id="destinationAddress">Destination</div>
    </div>

    <div class="navigation-instruction" id="navigationInstruction">
        <div class="instruction-icon">
            <i class="fas fa-arrow-up" id="instructionIcon"></i>
        </div>
        <div class="instruction-text" id="instructionText">Continue straight</div>
        <div class="instruction-distance" id="instructionDistance">in 500 m</div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary"></div>
        <div>Calculating route...</div>
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

        /* Action Buttons */
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

        .action-btn.route-btn {
            background: rgba(111, 66, 193, 0.9);
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

        /* Panels */
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

        /* Layer Switcher */
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

        /* Legend */
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

        /* Search Panel */
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

        /* Filter Panel */
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

        /* Route Info Panel */
        .route-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
            display: none;
            max-height: 500px;
        }

        .route-info.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @media (max-width: 768px) {

            .layer-switcher,
            .map-legend,
            .search-panel,
            .filter-panel,
            .route-info {
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

            .search-panel,
            .route-info {
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

        /* Route Styles */
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
            max-height: 300px;
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

        /* Navigation Header */
        .navigation-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px;
            z-index: 1003;
            display: none;
            color: white;
        }

        .nav-close {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            color: #ff4444;
            font-size: 24px;
            cursor: pointer;
        }

        .navigation-eta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .eta-time {
            font-size: 22px;
            font-weight: bold;
            color: #ffc107;
        }

        .eta-distance {
            font-size: 16px;
            color: #aaa;
        }

        .navigation-address {
            font-size: 14px;
            color: #ddd;
        }

        /* Navigation Instruction */
        .navigation-instruction {
            position: fixed;
            bottom: 100px;
            left: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 16px;
            z-index: 1003;
            display: none;
            color: white;
            text-align: center;
        }

        .instruction-icon {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff4444;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .instruction-text {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .instruction-distance {
            font-size: 14px;
            color: #ffc107;
        }

        /* Loading Spinner */
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

        /* Center Button */
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
        }

        @media (min-width: 769px) {
            .center-btn {
                bottom: auto;
                top: 160px;
                left: 20px;
            }
        }

        /* Other styles remain same as before */
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
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Popup styles */
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
        }

        .popup-header h4 {
            margin: 0;
            font-size: 18px;
            color: #ff4444;
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
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        $(document).ready(function() {
            // Pass data from controller with JSON_HEX_TAG to prevent XSS and syntax errors
            let polygons = @json($polygons, JSON_HEX_TAG);
            let lines = @json($lines, JSON_HEX_TAG);
            let points = @json($points, JSON_HEX_TAG);
            let pointDatas = @json($pointDatas ?? [], JSON_HEX_TAG);
            let polygonDatas = @json($polygonDatas ?? [], JSON_HEX_TAG);
            let ward = @json($ward ?? [], JSON_HEX_TAG);
            let selectedFeature = null;
            let currentRoute = null;
            let isMobile = window.innerWidth <= 768;
            let searchDebounceTimer = null;
            let currentSuggestions = [];
            let selectedSuggestionIndex = -1;
            let draw, modify, select;
            let featureClickHandler = null;
            let shopTimeout = null;
            let currentShopCount = 0;
            let shopDetailsArray = [];

            // Build search index
            let searchIndex = [];

            polygons.forEach(poly => {
                searchIndex.push({
                    id: poly.gisid,
                    type: 'polygon',
                    title: `GIS ID: ${poly.gisid}`,
                    subtitle: `Building (${poly.sqfeet || 0} sqft)`,
                    icon: 'fas fa-building',
                    data: poly,
                    searchText: `${poly.gisid} building polygon ${poly.sqfeet}`
                });
            });

            lines.forEach(line => {
                if (line.road_name) {
                    searchIndex.push({
                        id: line.gisid,
                        type: 'line',
                        title: line.road_name,
                        subtitle: `Road (GIS ID: ${line.gisid})`,
                        icon: 'fas fa-road',
                        data: line,
                        searchText: `${line.road_name} ${line.gisid} road`
                    });
                } else {
                    searchIndex.push({
                        id: line.gisid,
                        type: 'line',
                        title: `GIS ID: ${line.gisid}`,
                        subtitle: 'Road',
                        icon: 'fas fa-road',
                        data: line,
                        searchText: `${line.gisid} road`
                    });
                }
            });

            points.forEach(point => {
                searchIndex.push({
                    id: point.gisid,
                    type: 'point',
                    title: `GIS ID: ${point.gisid}`,
                    subtitle: 'Point Location',
                    icon: 'fas fa-map-marker-alt',
                    data: point,
                    searchText: `${point.gisid} point location`
                });
            });

            // Routes
            let routes = {
                addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
                addLineFeature: "{{ route('surveyor.add.line.feature') }}",
                addPointFeature: "{{ route('surveyor.add.point.feature') }}",
                surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}",
                deleteFeature: "{{ route('surveyor.delete.feature') }}",
                surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}"
            };

            function showToast(message, type = 'info') {
                $('.toast-notification').remove();
                const toast = $(`<div class="toast-notification ${type}">${message}</div>`);
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
            }

            function showFlashMessage(message, type) {
                showToast(message, type);
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function getSearchSuggestions(query) {
                if (!query || query.length < 2) return [];
                const lowerQuery = query.toLowerCase();
                return searchIndex.filter(item =>
                    item.searchText.toLowerCase().includes(lowerQuery) ||
                    item.title.toLowerCase().includes(lowerQuery)
                ).slice(0, 10);
            }

            function displaySuggestions(suggestions) {
                const container = $('#searchSuggestions');
                container.empty();
                if (suggestions.length === 0) {
                    container.removeClass('show');
                    return;
                }
                suggestions.forEach((suggestion, index) => {
                    container.append(`
                        <div class="suggestion-item" data-index="${index}">
                            <div class="suggestion-icon"><i class="${suggestion.icon}"></i></div>
                            <div class="suggestion-content">
                                <div class="suggestion-title">${escapeHtml(suggestion.title)}</div>
                                <div class="suggestion-subtitle">${escapeHtml(suggestion.subtitle)}</div>
                            </div>
                            <div class="suggestion-type">${suggestion.type === 'polygon' ? 'Building' : (suggestion.type === 'line' ? 'Road' : 'Point')}</div>
                        </div>
                    `);
                });
                container.addClass('show');
                currentSuggestions = suggestions;
            }

            function selectSuggestion(suggestion) {
                $('#searchInput').val(suggestion.title);
                $('#searchSuggestions').removeClass('show');
                navigateToFeature(suggestion.data, suggestion.type);
            }

            function navigateToFeature(data, type) {
                try {
                    let feature;
                    if (type === 'polygon') {
                        const coords = JSON.parse(data.coordinates);
                        feature = new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: data.gisid,
                            type: "Polygon",
                            sqfeet: data.sqfeet || "0"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 22
                        });
                        selectedFeature = feature;
                        showToast("Found GIS ID: " + data.gisid, 'success');
                    } else if (type === 'line') {
                        let coords = typeof data.coordinates === 'string' ? JSON.parse(data.coordinates) : data
                            .coordinates;
                        if (coords.length === 1 && Array.isArray(coords[0])) coords = coords[0];
                        feature = new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: data.gisid,
                            road_name: data.road_name,
                            type: "Line"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 20
                        });
                        selectedFeature = feature;
                        showToast("Found: " + (data.road_name || data.gisid), 'success');
                    } else if (type === 'point') {
                        const coords = JSON.parse(data.coordinates);
                        feature = new ol.Feature({
                            geometry: new ol.geom.Point(coords),
                            gisid: data.gisid,
                            type: "Point"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 22
                        });
                        selectedFeature = feature;
                        const highlightLayer = new ol.layer.Vector({
                            source: new ol.source.Vector(),
                            style: new ol.style.Style({
                                image: new ol.style.Circle({
                                    radius: 15,
                                    fill: new ol.style.Fill({
                                        color: 'rgba(255, 165, 0, 0.7)'
                                    }),
                                    stroke: new ol.style.Stroke({
                                        color: '#ffffff',
                                        width: 3
                                    })
                                })
                            })
                        });
                        highlightLayer.getSource().addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        }));
                        map.addLayer(highlightLayer);
                        setTimeout(() => map.removeLayer(highlightLayer), 3000);
                        showToast("Found GIS ID: " + data.gisid, 'success');
                    }
                    $('#searchLabel').addClass('closed');
                } catch (e) {
                    console.error('Error:', e);
                    showToast("Error displaying feature", 'error');
                }
            }

            // Drone image config
            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            // Create layers
            const osmLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                visible: true
            });
            const satelliteLayer = new ol.layer.Tile({
                source: new ol.source.OSM({
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                }),
                visible: false
            });
            const droneLayer = new ol.layer.Image({
                source: new ol.source.ImageStatic({
                    url: droneImageURL,
                    imageExtent: imageExtent,
                    imageSmoothing: false
                }),
                opacity: 0.90,
                visible: true
            });

            // Style functions
            function createPointStyle(feature) {
                const gisid = feature.get("gisid");
                const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                let color = "blue";
                if (polygonData) {
                    color = pointCount > 0 ? (polygonData.number_bill == pointCount ? "green" : "red") : "blue";
                }
                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 8,
                        fill: new ol.style.Fill({
                            color: color
                        }),
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 2
                        })
                    }),
                    text: new ol.style.Text({
                        text: gisid ? String(gisid) : "",
                        scale: 1.3,
                        offsetY: -15,
                        fill: new ol.style.Fill({
                            color: "#000"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#fff",
                            width: 3
                        })
                    })
                });
            }

            function createPolygonStyle(feature) {
                const gisid = feature.get("gisid");
                const sqft = feature.get("sqfeet") || "0";
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                const color = polygonData ? "red" : "blue";
                const geometry = feature.getGeometry();
                const centerPoint = geometry.getInteriorPoint();
                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 4,
                            lineJoin: "round",
                            lineCap: "round"
                        })
                    }),
                    new ol.style.Style({
                        geometry: centerPoint,
                        text: new ol.style.Text({
                            text: sqft + " SQFT",
                            font: "bold 14px Arial",
                            fill: new ol.style.Fill({
                                color: "#000"
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: "#fff"
                            }),
                            backgroundStroke: new ol.style.Stroke({
                                color: "#000",
                                width: 1
                            }),
                            padding: [4, 6, 4, 6],
                            overflow: true,
                            textAlign: "center",
                            offsetY: 0
                        }),
                        image: new ol.style.Circle({
                            radius: 4,
                            fill: new ol.style.Fill({
                                color: "yellow"
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#000",
                                width: 1
                            })
                        })
                    })
                ];
            }

            function createLineStyle(feature) {
                const road_name = feature.get("road_name");
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "yellow",
                        width: 4,
                        lineJoin: "round",
                        lineCap: "round"
                    }),
                    text: new ol.style.Text({
                        text: road_name ? String(road_name) : "",
                        font: "bold 14px Calibri, sans-serif",
                        placement: "line",
                        overflow: true,
                        fill: new ol.style.Fill({
                            color: "#000"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#fff",
                            width: 3
                        })
                    })
                });
            }

            // Vector sources
            const polygonSource = new ol.source.Vector();
            polygons.forEach(poly => {
                try {
                    let coords = JSON.parse(poly.coordinates);
                    polygonSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid,
                        type: "Polygon",
                        sqfeet: poly.sqfeet || "0"
                    }));
                } catch (e) {
                    console.error('Polygon parse error:', e);
                }
            });

            const lineSource = new ol.source.Vector();
            lines.forEach(l => {
                try {
                    let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                        .coordinates;
                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                        .isArray(coords[0][0])) coords = coords[0];
                    if (coords && coords.length >= 2) {
                        lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: l.gisid,
                            type: "Line",
                            road_name: l.road_name || null
                        }));
                    }
                } catch (e) {
                    console.error('Line parse error:', e);
                }
            });

            const pointSource = new ol.source.Vector();
            points.forEach(p => {
                try {
                    let coords = JSON.parse(p.coordinates);
                    pointSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                        gisid: p.gisid,
                        type: "Point"
                    }));
                } catch (e) {
                    console.error('Point parse error:', e);
                }
            });

            const polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: createPolygonStyle,
                visible: true
            });
            const lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: createLineStyle,
                visible: true
            });
            const pointLayer = new ol.layer.Vector({
                source: pointSource,
                style: createPointStyle,
                visible: true
            });

            // Route layer
            const routeSource = new ol.source.Vector();
            const routeLayer = new ol.layer.Vector({
                source: routeSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#2563eb',
                        width: 5,
                        lineDash: [10, 10]
                    })
                })
            });

            // Highlight layer for delete preview
            const highlightSource = new ol.source.Vector();
            const highlightLayer = new ol.layer.Vector({
                source: highlightSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#ff0000',
                        width: 4
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(255,0,0,0.2)'
                    })
                })
            });
            highlightLayer.setZIndex(1000);

            // Initialize map
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer, droneLayer, polygonLayer, lineLayer, pointLayer,
                    routeLayer, highlightLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Add boundary layer if exists
            if (ward.boundary && ward.boundary[0]) {
                const boundary = ward.boundary[0];
                const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
                const boundaryLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Polygon([transformedBoundary])
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "red",
                            width: 3
                        })
                    })
                });
                map.addLayer(boundaryLayer);
            }

            // COMPLETE handlePointClick function with ALL form fields
            function handlePointClick(properties) {
                const gisid = properties["gisid"];
                resetPointFormFields();
                $('#pointModal').remove();

                $("body").append(`
                    <div class="modal fade" id="pointModal" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                    <h5 class="modal-title">
                                        <i class="fas fa-map-marker-alt me-2"></i>Point Data Collection - GIS ID: ${gisid}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" enctype="multipart/form-data" id="pointForm">
                                    @csrf
                                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                        <!-- Basic Information Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="type" class="form-label">Assessment Type <span class="text-danger">*</span></label>
                                                        <select name="type" id="type" class="form-control" required>
                                                            <option value="OLD">OLD</option>
                                                            <option value="NEW">NEW</option>
                                                            <option value="OTHER WARD">OTHER WARD</option>
                                                            <option value="NO_TAX">NO TAX</option>
                                                            <option value="VACCAND">VACCAND</option>
                                                        </select>
                                                        <div id="type_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="suveyedbtn"></div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="pointgis" class="form-label">GIS ID <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="pointgis" name="point_gisid" value="${gisid}" readonly>
                                                        <div id="point_gisid_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="assessment" class="form-label">Assessment No <span class="text-danger">*</span></label>
                                                        <input type="text" name="assessment" class="form-control" id="assessment">
                                                        <div id="assessment_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="old_assessment" class="form-label">Old Assessment</label>
                                                        <input type="text" name="old_assessment" class="form-control" id="old_assessment">
                                                        <div id="old_assessment_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="owner_name" class="form-label">Owner Name</label>
                                                        <input type="text" name="owner_name" class="form-control" id="owner_name">
                                                        <div id="owner_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="present_owner_name" class="form-label">Present Owner Name</label>
                                                        <input type="text" name="present_owner_name" class="form-control" id="present_owner_name">
                                                        <div id="present_owner_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="no_of_shop" class="form-label">Number of Shops</label>
                                                        <input type="number" name="no_of_shop" class="form-control" id="no_of_shop" min="0" step="1" value="0">
                                                        <div id="no_of_shop_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="no_of_persons" class="form-label">Number of Persons</label>
                                                        <input type="number" name="no_of_persons" class="form-control" id="no_of_persons" min="0" step="1" value="0">
                                                        <div id="no_of_persons_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Property Details Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-building"></i> Property Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="floor" class="form-label">Floor</label>
                                                        <input type="number" name="floor" class="form-control" id="floor" min="0" step="1">
                                                        <div id="floor_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="old_door_no" class="form-label">Old Door No</label>
                                                        <input type="text" name="old_door_no" class="form-control" id="old_door_no">
                                                        <div id="old_door_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="new_door_no" class="form-label">New Door No</label>
                                                        <input type="text" name="new_door_no" class="form-control" id="new_door_no">
                                                        <div id="new_door_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="bill_usage" class="form-label">Bill Usage</label>
                                                        <select name="bill_usage" id="bill_usage" class="form-control">
                                                            <option value="">SELECT USAGE</option>
                                                            <option value="RESIDENTIAL">RESIDENTIAL</option>
                                                            <option value="COMMERCIAL">COMMERCIAL</option>
                                                            <option value="EDUCATIONAL INSTITUTIONS">EDUCATIONAL INSTITUTIONS</option>
                                                            <option value="GOVERNMENT BUILDING">GOVERNMENT BUILDING</option>
                                                            <option value="INDUSTRIAL">INDUSTRIAL</option>
                                                            <option value="OFFICE / LODGE / THEATER / RESTAURANTS">OFFICE / LODGE / THEATER / RESTAURANTS</option>
                                                            <option value="STAR HOTEL">STAR HOTEL</option>
                                                        </select>
                                                        <div id="bill_usage_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="eb" class="form-label">EB Number</label>
                                                        <input type="text" name="eb" class="form-control" id="eb">
                                                        <div id="eb_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shop Details Container -->
                                        <div id="shopDetailsContainer"></div>

                                        <!-- Tax Details Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;">
                                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Tax Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="water_tax" class="form-label">Water Tax</label>
                                                        <input type="text" name="water_tax" class="form-control" id="water_tax" step="0.01" min="0">
                                                        <div id="water_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="old_water_tax" class="form-label">Old Water Tax</label>
                                                        <input type="text" name="old_water_tax" class="form-control" id="old_water_tax" step="0.01" min="0">
                                                        <div id="old_water_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="professional_tax" class="form-label">Professional Tax</label>
                                                        <input type="text" name="professional_tax" class="form-control" id="professional_tax">
                                                        <div id="professional_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="gst" class="form-label">GST</label>
                                                        <input type="text" name="gst" class="form-control" id="gst" placeholder="GST Number">
                                                        <div id="gst_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="trade_income" class="form-label">Trade Income</label>
                                                        <input type="number" name="trade_income" class="form-control" id="trade_income" step="0.01" min="0">
                                                        <div id="trade_income_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Documents Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-id-card"></i> Documents & Contact</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="aadhar_no" class="form-label">Aadhar Number</label>
                                                        <input type="text" name="aadhar_no" class="form-control" id="aadhar_no" maxlength="12" pattern="[0-9]{12}" placeholder="12-digit Aadhar">
                                                        <div id="aadhar_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="ration_no" class="form-label">Ration Number</label>
                                                        <input type="text" name="ration_no" class="form-control" id="ration_no">
                                                        <div id="ration_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="phone" class="form-label">Phone Number</label>
                                                        <input type="tel" name="phone_number" class="form-control" id="phone" pattern="[0-9]{10}" maxlength="10" placeholder="10-digit mobile">
                                                        <div id="phone_number_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quality Check Card -->
                                        <div class="card mb-3 d-none" id="qualityCheckCard">
                                            <div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Quality Check</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_area" class="form-label">QC Area</label>
                                                        <input type="text" name="qc_area" class="form-control" id="qc_area">
                                                        <div id="qc_area_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_usage" class="form-label">QC Usage</label>
                                                        <select name="qc_usage" id="qc_usage" class="form-control">
                                                            <option value="">Select Usage</option>
                                                            <option value="Residential">Residential</option>
                                                            <option value="Commercial">Commercial</option>
                                                            <option value="Mixed">Mixed</option>
                                                        </select>
                                                        <div id="qc_usage_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_name" class="form-label">QC Name</label>
                                                        <input type="text" name="qc_name" class="form-control" id="qc_name" placeholder="QC Officer Name">
                                                        <div id="qc_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_remarks" class="form-label">QC Remarks</label>
                                                        <input type="text" name="qc_remarks" class="form-control" id="qc_remarks">
                                                        <div id="qc_remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-comment"></i> Remarks</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="establishment_remarks" class="form-label">Establishment Remarks</label>
                                                        <textarea name="establishment_remarks" class="form-control" id="establishment_remarks" rows="2" placeholder="Enter establishment remarks..."></textarea>
                                                        <div id="establishment_remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="remarks" class="form-label">Office Remarks</label>
                                                        <textarea name="remarks" class="form-control" id="remarks" rows="2" placeholder="Enter general remarks..."></textarea>
                                                        <div id="remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Shop Details Append Area -->
                                        <div id="append"></div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Close
                                        </button>
                                        <button type="submit" id="pointSubmit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Point Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `);

                const polygonData = polygonDatas.find(data => data.gisid === gisid);
                const polygonNumOfBill = polygonData ? polygonData.number_bill : null;
                const matchingPointsCount = pointDatas.filter(data => data.point_gisid === gisid).length;

                if (polygonNumOfBill > matchingPointsCount) {
                    $("#pointgis").val(gisid);
                    initDynamicShopDetails();
                    $("#pointModal").modal("show");
                } else {
                    showFlashMessage(`Already this building has ${matchingPointsCount} bills`, "error");
                }
            }

            function resetPointFormFields() {
                $("#pointgis, #assessment, #old_assessment, #owner_name, #present_owner_name, #worker_name, #building_data_id,#no_of_persons")
                    .val("");
                $("#floor, #old_door_no, #new_door_no, #plot_area, #eb, #otsarea").val("");
                $("#water_tax, #old_water_tax, #halfyeartax, #balance, #professional_tax, #gst, #trade_income").val(
                    "");
                $("#aadhar_no, #ration_no, #phone").val("");
                $("#qc_area, #qc_name, #qc_remarks").val("");
                $("#establishment_remarks, #remarks").val("");
                $("#type").val("OLD");
                $("#bill_usage, #shop_category, #qc_usage").val("");

                const appendArea = $('#append');
                const container = $('#shopDetailsContainer');
                if (container.length) {
                    const shops = container.find('.shop-item');
                    if (shops.length > 0) {
                        shops.fadeOut(300, function() {
                            container.empty();
                            currentShopCount = 0;
                            $('#no_of_shop').val(0);
                            appendArea.find('.card.mb-3').fadeOut(300, function() {
                                $(this).remove();
                            });
                        });
                    } else {
                        appendArea.empty();
                        currentShopCount = 0;
                        $('#no_of_shop').val(0);
                    }
                } else {
                    appendArea.empty();
                    currentShopCount = 0;
                    $('#no_of_shop').val(0);
                }
                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
            }

            function initDynamicShopDetails() {
                $('#no_of_shop').off('change keyup').on('change keyup', function() {
                    if (shopTimeout) clearTimeout(shopTimeout);
                    shopTimeout = setTimeout(() => {
                        let shopCount = parseInt($(this).val()) || 0;
                        if (shopCount < 0) shopCount = 0;
                        if ($(this).val() !== shopCount.toString()) $(this).val(shopCount);
                        generateShopForms(shopCount);
                    }, 300);
                });
                $(document).on('click', '.remove-shop-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const shopId = $(this).data('shop-id');
                    const currentCount = parseInt($('#no_of_shop').val()) || 0;
                    if (currentCount > 0) {
                        const newCount = currentCount - 1;
                        $('#no_of_shop').val(newCount).trigger('change');
                    }
                });
            }

            function generateShopForms(shopCount) {
                const appendArea = $('#append');
                if (currentShopCount === shopCount) return;
                if (shopCount === 0) {
                    const container = $('#shopDetailsContainer');
                    if (container.length) {
                        const shops = container.find('.shop-item');
                        if (shops.length > 0) {
                            shops.fadeOut(300, function() {
                                container.empty();
                                currentShopCount = 0;
                                $('#no_of_shop').val(0);
                                appendArea.find('.card.mb-3').fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            appendArea.empty();
                            currentShopCount = 0;
                        }
                    } else {
                        appendArea.empty();
                        currentShopCount = 0;
                    }
                    return;
                }
                let container = $('#shopDetailsContainer');
                if (container.length === 0) {
                    const shopCard = $(`
                        <div class="card mb-3">
                            <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; display: flex; justify-content: space-between; align-items: center;">
                                <h6 class="mb-0"><i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount > 1 ? 's' : ''})</h6>
                                <button type="button" class="btn btn-sm btn-light" id="addAllShopsBtn" style="border-radius: 20px;">
                                    <i class="fas fa-plus"></i> Add All
                                </button>
                            </div>
                            <div class="card-body" id="shopDetailsContainer"></div>
                        </div>
                    `);
                    appendArea.append(shopCard);
                    container = $('#shopDetailsContainer');
                    $('#addAllShopsBtn').off('click').on('click', function() {
                        const newCount = currentShopCount + 1;
                        $('#no_of_shop').val(newCount).trigger('change');
                    });
                }
                if (shopCount > currentShopCount) {
                    for (let i = currentShopCount + 1; i <= shopCount; i++) {
                        addShopForm(i, container);
                    }
                } else if (shopCount < currentShopCount) {
                    for (let i = currentShopCount; i > shopCount; i--) {
                        removeShopForm(i, container);
                    }
                }
                currentShopCount = shopCount;
                const header = appendArea.find('.card-header h6');
                if (header.length) {
                    header.html(
                        `<i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount !== 1 ? 's' : ''})`
                    );
                }
            }

            function addShopForm(shopNumber, container) {
                const shopHtml = `
                    <div class="shop-item" data-shop-id="${shopNumber}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-store me-2"></i>Shop ${shopNumber}</h6>
                            <button type="button" class="remove-shop-btn" data-shop-id="${shopNumber}">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prof Tax Assessment</label>
                                <input type="text" name="prof_tax_assessment_${shopNumber}" class="form-control" placeholder="Enter prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Old Prof Tax Assessment</label>
                                <input type="text" name="old_prof_tax_assessment_${shopNumber}" class="form-control" placeholder="Enter old prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Floor</label>
                                <input type="text" name="shop_floor_${shopNumber}" class="form-control" placeholder="Enter floor number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Name</label>
                                <input type="text" name="shop_name_${shopNumber}" class="form-control" placeholder="Enter shop name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Owner Name</label>
                                <input type="text" name="shop_owner_name_${shopNumber}" class="form-control" placeholder="Enter owner name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Category</label>
                                <select name="shop_category_${shopNumber}" class="form-control">
                                    <option value="">Select Category</option>
                                    <option value="Grocery">Grocery</option>
                                    <option value="Clothing">Clothing</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Restaurant">Restaurant</option>
                                    <option value="Pharmacy">Pharmacy</option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Mobile</label>
                                <input type="tel" name="shop_mobile_${shopNumber}" class="form-control" placeholder="Mobile number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_${shopNumber}" class="form-control" placeholder="License number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Number of Employees</label>
                                <input type="number" name="number_of_employee_${shopNumber}" class="form-control" placeholder="Employee count">
                            </div>
                        </div>
                    </div>
                `;
                container.append(shopHtml);
            }

            function removeShopForm(shopNumber, container) {
                $(`.shop-item[data-shop-id="${shopNumber}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            }

            // Point form submission with ALL data
            $("#pointForm").off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const shopCount = parseInt($('#no_of_shop').val()) || 0;
                formData.append('total_shops', shopCount);
                for (let i = 1; i <= shopCount; i++) {
                    formData.append(`prof_tax_assessment_${i}`, $(`input[name="prof_tax_assessment_${i}"]`)
                        .val() || '');
                    formData.append(`old_prof_tax_assessment_${i}`, $(
                        `input[name="old_prof_tax_assessment_${i}"]`).val() || '');
                    formData.append(`shop_floor_${i}`, $(`input[name="shop_floor_${i}"]`).val() || '');
                    formData.append(`shop_name_${i}`, $(`input[name="shop_name_${i}"]`).val() || '');
                    formData.append(`shop_owner_name_${i}`, $(`input[name="shop_owner_name_${i}"]`).val() ||
                        '');
                    formData.append(`shop_category_${i}`, $(`select[name="shop_category_${i}"]`).val() ||
                        '');
                    formData.append(`shop_mobile_${i}`, $(`input[name="shop_mobile_${i}"]`).val() || '');
                    formData.append(`license_${i}`, $(`input[name="license_${i}"]`).val() || '');
                    formData.append(`number_of_employee_${i}`, $(`input[name="number_of_employee_${i}"]`)
                        .val() || '');
                }
                $("#pointSubmit").prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    type: "POST",
                    url: routes.surveyorPointDataUpload,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showFlashMessage(response.message, "success");
                        $("#pointModal").modal("hide");
                        if (response.pointDatas) pointDatas = response.pointDatas;
                        if (response.points) points = response.points;
                        refreshVectorLayer();
                        resetPointFormFields();
                        $('#append').empty();
                        $('#no_of_shop').val('');
                        currentShopCount = 0;
                    },
                    error: function(xhr) {
                        let errorMsg = "An error occurred while processing your request.";
                        if (xhr.responseJSON && xhr.responseJSON.msg) errorMsg = xhr
                            .responseJSON.msg;
                        showFlashMessage(errorMsg, "error");
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(value[0]);
                            });
                        }
                    },
                    complete: function() {
                        $("#pointSubmit").prop("disabled", false).html(
                            '<i class="fas fa-save me-2"></i>Save Point Data');
                    }
                });
            });

            function handlePolygonClick(properties) {
                const gisId = properties["gisid"];
                resetBuildingForm();
                $("#building_gisid").val(gisId);
                let existingData = null;

                // REMOVE OLD MODAL BEFORE APPENDING NEW ONE
                $("#buildingModal").remove();

                // Get road names from PHP and convert to JavaScript array
                let roadNames = @json($uniqueRoadNames ?? []);

                // Build road options HTML
                let roadOptions = '<option value="">Select Road Name</option>';
                if (roadNames && roadNames.length > 0) {
                    roadNames.forEach(function(roadName) {
                        let escapedRoadName = roadName.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        roadOptions += `<option value="${escapedRoadName}">${escapedRoadName}</option>`;
                    });
                }

                const modalHtml = `
                        <div class="modal fade" id="buildingModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-bottom: none;">
                                        <h5 class="modal-title">
                                            <i class="fas fa-building me-2"></i>Building Data Collection - GIS ID: ${gisId}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="buildingForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="gisIdInput" name="gisid" value="${gisId}">
                                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; background: #f8fafc;">

                                            <!-- Image Upload Section with Previews -->
                                            <div class="card mb-4">
                                                <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                                                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Building Images</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="fw-bold mb-2"><i class="fas fa-camera me-1"></i>Image 1</label>
                                                            <div class="image-preview-container border rounded p-3" style="background: #ffffff; min-height: 220px;">
                                                                <img id="buildingImagePreview" src="" alt="Building Image Preview" class="img-fluid" style="display: none; max-height: 200px; width: 100%; object-fit: contain; border-radius: 8px;">
                                                                <div id="noImagePlaceholder" class="text-center text-muted" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px;">
                                                                    <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: #cbd5e1;"></i>
                                                                    <p>No image selected</p>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <label class="btn btn-outline-primary btn-sm w-100">
                                                                    <i class="fas fa-upload me-1"></i> Choose Image
                                                                    <input type="file" name="image" id="building_image" accept="image/*" style="display: none;">
                                                                </label>
                                                            </div>
                                                            <div id="building_image_error" class="error-message text-danger small mt-1"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="fw-bold mb-2"><i class="fas fa-camera me-1"></i>Image 2</label>
                                                            <div class="image-preview-container border rounded p-3" style="background: #ffffff; min-height: 220px;">
                                                                <img id="buildingImagePreview2" src="" alt="Building Image Preview 2" class="img-fluid" style="display: none; max-height: 200px; width: 100%; object-fit: contain; border-radius: 8px;">
                                                                <div id="noImagePlaceholder2" class="text-center text-muted" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px;">
                                                                    <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: #cbd5e1;"></i>
                                                                    <p>No image selected</p>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <label class="btn btn-outline-primary btn-sm w-100">
                                                                    <i class="fas fa-upload me-1"></i> Choose Image
                                                                    <input type="file" name="image2" id="building_image2" accept="image/*" style="display: none;">
                                                                </label>
                                                            </div>
                                                            <div id="building_image2_error" class="error-message text-danger small mt-1"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Basic Information Card -->
                                            <div class="card mb-4">
                                                <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">GIS ID <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="building_gisid" id="building_gisid" value="${gisId}" readonly>
                                                            <div id="building_gisid_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Zone</label>
                                                            <select class="form-select" name="building_zone" id="building_zone">
                                                                <option value="">Select Zone</option>
                                                                <option value="ZONE-A">ZONE-A</option>
                                                                <option value="ZONE-B">ZONE-B</option>
                                                                <option value="ZONE-C">ZONE-C</option>
                                                                <option value="ZONE-D">ZONE-D</option>
                                                                <option value="ZONE-E">ZONE-E</option>
                                                            </select>
                                                            <div id="building_zone_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Number of Bills</label>
                                                            <input type="number" class="form-control" name="number_bill" id="number_bill" min="0">
                                                            <div id="number_bill_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Number of Shops</label>
                                                            <input type="number" class="form-control" name="number_shop" id="number_shop" min="0">
                                                            <div id="number_shop_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Number of Floors</label>
                                                            <input type="number" class="form-control" name="number_floor" id="number_floor" min="0">
                                                            <div id="number_floor_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Percentage</label>
                                                            <select class="form-select" name="percentage" id="percentage">
                                                                <option value="">Select Percentage</option>
                                                                <option value="10">10%</option>
                                                                <option value="20">20%</option>
                                                                <option value="30">30%</option>
                                                                <option value="40">40%</option>
                                                                <option value="50">50%</option>
                                                                <option value="60">60%</option>
                                                                <option value="70">70%</option>
                                                                <option value="80">80%</option>
                                                                <option value="85">85%</option>
                                                                <option value="90">90%</option>
                                                                <option value="100">100%</option>
                                                            </select>
                                                            <div id="percentage_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Building Name</label>
                                                            <input type="text" class="form-control" name="building_name" id="building_name" placeholder="Enter building name">
                                                            <div id="building_name_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Road Name</label>
                                                            <select class="form-select" id="road_name" name="road_name">
                                                                ${roadOptions}
                                                            </select>
                                                            <div id="road_name_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Phone Number</label>
                                                            <input type="tel" class="form-control" name="phone" id="phone_building" placeholder="10-digit mobile number" maxlength="10">
                                                            <div id="phone_building_error" class="error-message text-danger small"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Building Details Card -->
                                            <div class="card mb-4">
                                                <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                                                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Building Details</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Building Usage</label>
                                                            <select class="form-select" name="building_usage" id="building_usage">
                                                                <option value="">Select Usage</option>
                                                                <option value="RESIDENTIAL">Residential</option>
                                                                <option value="COMMERCIAL">Commercial</option>
                                                                <option value="INDUSTRIAL">Industrial</option>
                                                                <option value="INSTITUTIONAL">Institutional</option>
                                                                <option value="MIXED">Mixed</option>
                                                                <option value="GOVERNMENT">Government</option>
                                                                <option value="VACANT">Vacant</option>
                                                            </select>
                                                            <div id="building_usage_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Construction Type</label>
                                                            <select class="form-select" name="construction_type" id="construction_type">
                                                                <option value="">Select Type</option>
                                                                <option value="PERMANENT">Permanent</option>
                                                                <option value="SEMI_PERMANENT">Semi Permanent</option>
                                                                <option value="VACANT_LAND">Vacant Land</option>
                                                                <option value="SHED">Shed</option>
                                                                <option value="CAR_SHED">Car Shed</option>
                                                                <option value="TEMPORARY">Temporary</option>
                                                            </select>
                                                            <div id="construction_type_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Building Type</label>
                                                            <select class="form-select" name="building_type" id="building_type">
                                                                <option value="">Select Type</option>
                                                                <option value="Independent">Independent</option>
                                                                <option value="Flat">Flat</option>
                                                                <option value="Kalyana_Mandapam">Kalyana Mandapam</option>
                                                                <option value="Hotel">Hotel</option>
                                                                <option value="Cinema_Theatre">Cinema Theatre</option>
                                                                <option value="Central_Government_Building">Central Government Building</option>
                                                                <option value="State_Government_Building">State Government Building</option>
                                                                <option value="Municipality_Corporation">Municipality / Corporation</option>
                                                                <option value="Educational_Institution">Educational Institution</option>
                                                                <option value="Hospital">Hospital</option>
                                                                <option value="Commercial_Complex">Commercial Complex</option>
                                                                <option value="Shop">Shop</option>
                                                                <option value="Office">Office</option>
                                                                <option value="Temple">Temple</option>
                                                                <option value="Mosque">Mosque</option>
                                                                <option value="Church">Church</option>
                                                                <option value="Amma_Unavagam">Amma Unavagam</option>
                                                                <option value="Public_Toilet">Public Toilet</option>
                                                                <option value="Vacant Land">Vacant Land</option>
                                                                <option value="Under Construction">Under Construction</option>
                                                                <option value="Others">Others</option>
                                                            </select>
                                                            <div id="building_type_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">UGD Status</label>
                                                            <select class="form-select" name="ugd" id="ugd">
                                                                <option value="">Select Status</option>
                                                                <option value="No_Connection">No Connection</option>
                                                                <option value="Manhole_Available_but_Connection_Not_Given_to_House">Manhole Available but Connection Not Given</option>
                                                                <option value="Stage_1_Completed">Stage 1 Completed</option>
                                                                <option value="Stage_1_2_Completed">Stage 1 & 2 Completed</option>
                                                                <option value="Stage_1_2_Completed_but_Not_Connected">Stage 1 & 2 Completed but Not Connected</option>
                                                                <option value="Stage_1_2_3_Completed">Stage 1, 2 & 3 Completed</option>
                                                                <option value="Direct_Connection_Given">Direct Connection Given</option>
                                                                <option value="1_UGD_Connection_-_3_Stage_Completed">1 UGD Connection - 3 Stage Completed</option>
                                                                <option value="2_UGD_Connection_-_3_Stage_Completed">2 UGD Connection - 3 Stage Completed</option>
                                                            </select>
                                                            <div id="ugd_error" class="error-message text-danger small"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Amenities Card -->
                                            <div class="card mb-4">
                                                <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;">
                                                    <h6 class="mb-0"><i class="fas fa-umbrella me-2"></i>Amenities</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Lift Room</label>
                                                            <select class="form-select" name="liftroom" id="liftroom">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="liftroom_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Head Room</label>
                                                            <select class="form-select" name="headroom" id="headroom">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="headroom_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Overhead Tank</label>
                                                            <select class="form-select" name="overhead_tank" id="overhead_tank">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="overhead_tank_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Rainwater Harvesting</label>
                                                            <select class="form-select" name="rainwater_harvesting" id="rainwater_harvesting">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="rainwater_harvesting_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Parking</label>
                                                            <select class="form-select" name="parking" id="parking">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="parking_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Ramp</label>
                                                            <select class="form-select" name="ramp" id="ramp">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="ramp_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Hoarding</label>
                                                            <select class="form-select" name="hoarding" id="hoarding">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="hoarding_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">CCTV</label>
                                                            <select class="form-select" name="cctv" id="cctv">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="cctv_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Cell Tower</label>
                                                            <select class="form-select" name="cell_tower" id="cell_tower">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="cell_tower_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Solar Panel</label>
                                                            <select class="form-select" name="solar_panel" id="solar_panel">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="solar_panel_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Basement</label>
                                                            <input type="number" class="form-control" name="basement" id="basement" min="0" placeholder="Number of basements">
                                                            <div id="basement_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Water Connection</label>
                                                            <select class="form-select" name="water_connection" id="water_connection">
                                                                <option value="No">No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                            <div id="water_connection_error" class="error-message text-danger small"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remarks Card -->
                                            <div class="card mb-4">
                                                <div class="card-header" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;">
                                                    <h6 class="mb-0"><i class="fas fa-comment me-2"></i>Remarks</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">General Remarks</label>
                                                            <textarea class="form-control" name="remarks" id="remarks_building" rows="3" placeholder="Enter general remarks..."></textarea>
                                                            <div id="remarks_building_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Corporation Remarks</label>
                                                            <textarea class="form-control" name="corporationremarks" id="corporationremarks" rows="3" placeholder="Enter corporation remarks..."></textarea>
                                                            <div id="corporationremarks_error" class="error-message text-danger small"></div>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">QC Remarks</label>
                                                            <textarea class="form-control" name="qc_remarks" id="qc_remarks" rows="2" placeholder="Enter QC remarks..."></textarea>
                                                            <div id="qc_remarks_error" class="error-message text-danger small"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-2"></i>Close
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="buildingsubmitBtn">
                                                <i class="fas fa-save me-2"></i>Save Building Data
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;

                $("body").append(modalHtml);

                // Add image preview functionality after modal is added
                setupImagePreview();
                setupBuildingFormSubmission();

                if (polygonDatas && polygonDatas.length > 0) {
                    existingData = polygonDatas.find(item => item.gisid == gisId);
                }

                if (existingData) {
                    populateBuildingForm(existingData);
                    showFlashMessage('Loading existing building data...', 'info');
                } else {
                    $("#buildingImagePreview").hide().attr("src", "");
                    $("#buildingImagePreview2").hide().attr("src", "");
                    showFlashMessage('Creating new building record...', 'info');
                }

                $("#buildingModal").modal("show");
            }

            function setupImagePreview() {
                // Image 1 preview
                $('#building_image').off('change').on('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#buildingImagePreview').attr('src', event.target.result).show();
                            $('#noImagePlaceholder').hide();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#buildingImagePreview').hide().attr('src', '');
                        $('#noImagePlaceholder').show();
                    }
                });

                // Image 2 preview
                $('#building_image2').off('change').on('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#buildingImagePreview2').attr('src', event.target.result).show();
                            $('#noImagePlaceholder2').hide();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#buildingImagePreview2').hide().attr('src', '');
                        $('#noImagePlaceholder2').show();
                    }
                });
            }

            function setupBuildingFormSubmission() {
                $("#buildingForm").off('submit').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    $("#buildingsubmitBtn").prop("disabled", true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        type: "POST",
                        url: "{{ route('surveyor.polygon.datas.upload') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            showFlashMessage(response.message, "success");
                            $("#buildingModal").modal("hide");
                            if (response.polygonDatas) {
                                polygonDatas = response.polygonDatas;
                            }
                            if (response.polygons) {
                                polygons = response.polygons;
                            }
                            refreshVectorLayer();
                            resetBuildingForm();
                        },
                        error: function(xhr) {
                            let errorMsg = "An error occurred while saving building data";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            showFlashMessage(errorMsg, "error");
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $("#" + key + "_error").html(value[0]);
                                    $("#" + key).addClass("is-invalid");
                                });
                            }
                        },
                        complete: function() {
                            $("#buildingsubmitBtn").prop("disabled", false).html(
                                '<i class="fas fa-save me-2"></i>Save Building Data');
                        }
                    });
                });
            }

            function populateBuildingForm(item) {
                // Basic Information
                $("#building_gisid").val(item.gisid || "");
                $("#number_bill").val(item.number_bill || "");
                $("#number_shop").val(item.number_shop || "");
                $("#number_floor").val(item.number_floor || "");
                $("#building_name").val(item.building_name || "");
                $("#road_name").val(item.road_name || "");
                $("#phone_building").val(item.phone || "");
                $("#building_zone").val(item.zone || item.building_zone || "");
                $("#percentage").val(item.percentage || "");

                // Building Details
                $("#building_usage").val(item.building_usage || "");
                $("#construction_type").val(item.construction_type || "");
                $("#building_type").val(item.building_type || "");
                $("#ugd").val(item.ugd || "");

                // Amenities (Yes/No fields)
                $("#liftroom").val(item.liftroom || "No");
                $("#headroom").val(item.headroom || "No");
                $("#overhead_tank").val(item.overhead_tank || "No");
                $("#rainwater_harvesting").val(item.rainwater_harvesting || "No");
                $("#parking").val(item.parking || "No");
                $("#ramp").val(item.ramp || "No");
                $("#hoarding").val(item.hoarding || "No");
                $("#cctv").val(item.cctv || "No");
                $("#cell_tower").val(item.cell_tower || "No");
                $("#solar_panel").val(item.solar_panel || "No");

                // Property Details
                $("#basement").val(item.basement || "");
                $("#water_connection").val(item.water_connection || "No");

                // Remarks
                $("#remarks_building").val(item.remarks || "");
                $("#corporationremarks").val(item.corporationremarks || "");
                $("#qc_remarks").val(item.qc_remarks || "");

                // Image Previews
                const assetUrl = window.assetUrl || "{{ asset('') }}";

                if (item.image && item.image !== "") {
                    const imageUrl = item.image.startsWith('http') ? item.image : assetUrl + item.image;
                    $("#buildingImagePreview").attr("src", imageUrl).show();
                    $("#noImagePlaceholder").hide();
                } else {
                    $("#buildingImagePreview").hide().attr("src", "");
                    $("#noImagePlaceholder").show();
                }

                if (item.image2 && item.image2 !== "") {
                    const imageUrl2 = item.image2.startsWith('http') ? item.image2 : assetUrl + item.image2;
                    $("#buildingImagePreview2").attr("src", imageUrl2).show();
                    $("#noImagePlaceholder2").hide();
                } else {
                    $("#buildingImagePreview2").hide().attr("src", "");
                    $("#noImagePlaceholder2").show();
                }
            }

            function resetBuildingForm() {
                $("#building_gisid").val("");
                $("#number_bill").val("");
                $("#number_shop").val("");
                $("#number_floor").val("");
                $("#building_name").val("");
                $("#road_name").val("");
                $("#phone_building").val("");
                $("#building_zone").val("");
                $("#percentage").val("");

                $("#building_usage").val("");
                $("#construction_type").val("");
                $("#building_type").val("");
                $("#ugd").val("");

                $("#liftroom").val("No");
                $("#headroom").val("No");
                $("#overhead_tank").val("No");
                $("#rainwater_harvesting").val("No");
                $("#parking").val("No");
                $("#ramp").val("No");
                $("#hoarding").val("No");
                $("#cctv").val("No");
                $("#cell_tower").val("No");
                $("#solar_panel").val("No");

                $("#basement").val("");
                $("#water_connection").val("No");

                $("#remarks_building").val("");
                $("#corporationremarks").val("");
                $("#qc_remarks").val("");

                $("#buildingImagePreview").hide().attr("src", "");
                $("#buildingImagePreview2").hide().attr("src", "");

                $("#building_image").val("");
                $("#building_image2").val("");

                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
            }

            function refreshVectorLayer() {
                polygonSource.clear();
                lineSource.clear();
                pointSource.clear();
                polygons.forEach(poly => {
                    try {
                        let coords = JSON.parse(poly.coordinates);
                        polygonSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            type: "Polygon",
                            sqfeet: poly.sqfeet || "0"
                        }));
                    } catch (e) {}
                });
                lines.forEach(l => {
                    try {
                        let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                            .coordinates;
                        if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                            .isArray(coords[0][0])) coords = coords[0];
                        if (coords && coords.length >= 2) {
                            lineSource.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(coords),
                                gisid: l.gisid,
                                type: "Line",
                                road_name: l.road_name || null
                            }));
                        }
                    } catch (e) {}
                });
                points.forEach(p => {
                    try {
                        let coords = JSON.parse(p.coordinates);
                        pointSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords),
                            gisid: p.gisid,
                            type: "Point"
                        }));
                    } catch (e) {}
                });
                highlightSource.clear();
            }


            // Setup click handler
            function setupOriginalClickHandler() {
                featureClickHandler = function(evt) {
                    if (isModifyMode || isDrawingActive) return;
                    let hasDrawingActive = false;
                    map.getInteractions().forEach((interaction) => {
                        if (interaction instanceof ol.interaction.Draw) hasDrawingActive = true;
                    });
                    if (hasDrawingActive) return;
                    const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    if (feature) {
                        const properties = feature.getProperties();
                        const geometryType = feature.getGeometry().getType();
                        if (geometryType === "Point") handlePointClick(properties);
                        else if (geometryType === "Polygon") handlePolygonClick(properties);
                        else if (geometryType === "LineString" || geometryType === "MultiLineString") {
                            selectedFeature = feature;
                            showToast(`Selected Road: ${properties.road_name || properties.gisid}`, 'success');
                        }
                    }
                };
                map.on('click', featureClickHandler);
            }

            let isModifyMode = false;
            let isDrawingActive = false;

            function removeDrawInteractions() {
                map.getInteractions().forEach(interaction => {
                    if (interaction instanceof ol.interaction.Draw || interaction instanceof ol.interaction
                        .Modify || interaction instanceof ol.interaction.Select) {
                        map.removeInteraction(interaction);
                    }
                });
                isModifyMode = false;
                isDrawingActive = false;
            }

            function activateDrawPolygon() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: polygonSource,
                    type: "Polygon"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addPolygonFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Polygon",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            polygons = response.polygons;
                            points = response.points;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateDrawLine() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: lineSource,
                    type: "LineString"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addLineFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Line",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            lines = response.lines;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateDrawPoint() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: pointSource,
                    type: "Point"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addPointFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Point",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            points = response.points;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateModify() {
                removeDrawInteractions();
                isModifyMode = true;
                select = new ol.interaction.Select({
                    layers: [polygonLayer, lineLayer, pointLayer],
                    condition: ol.events.condition.click
                });
                modify = new ol.interaction.Modify({
                    features: select.getFeatures()
                });
                map.addInteraction(select);
                map.addInteraction(modify);
                modify.on('modifyend', function(evt) {
                    evt.features.forEach(function(feature) {
                        const geometry = feature.getGeometry();
                        const coordinates = geometry.getCoordinates();
                        const type = feature.get('type');
                        const gisid = feature.get('gisid');
                        $.ajax({
                            url: routes.surveyorModifyFeature,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                            },
                            data: {
                                gisid: gisid,
                                type: type,
                                coordinates: JSON.stringify(coordinates)
                            },
                            success: function(response) {
                                if (response.success) {
                                    showFlashMessage('Feature updated successfully',
                                        'success');
                                    if (response.polygons) polygons = response.polygons;
                                    if (response.lines) lines = response.lines;
                                    if (response.points) points = response.points;
                                    refreshVectorLayer();
                                } else showFlashMessage(response.message, 'error');
                            },
                            error: function() {
                                showFlashMessage('Error updating feature', 'error');
                                refreshVectorLayer();
                            }
                        });
                    });
                });
            }

            function activateDelete() {
                removeDrawInteractions();
                $("#editToolSelect").val("none");
                $("#deleteModal").modal("show");
            }

            // Delete functionality
            $("#deleteForm").submit(function(e) {
                e.preventDefault();
                const gisid = $("#deleteGisIdInput").val().trim();
                if (!gisid) {
                    showFlashMessage("Please enter a GIS ID", "error");
                    return;
                }
                $.ajax({
                    url: routes.deleteFeature,
                    type: "POST",
                    data: {
                        gisid: gisid,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showFlashMessage(response.message, "success");
                            $("#deleteModal").modal("hide");
                            if (response.polygons) polygons = response.polygons;
                            if (response.lines) lines = response.lines;
                            if (response.points) points = response.points;
                            refreshVectorLayer();
                            highlightSource.clear();
                            $("#deleteForm")[0].reset();
                        } else showFlashMessage(response.message, "error");
                    },
                    error: function() {
                        showFlashMessage("An error occurred", "error");
                    }
                });
            });

            $("#deleteGisIdInput").on('input', function() {
                const gisid = $(this).val().trim();
                if (!gisid) {
                    highlightSource.clear();
                    return;
                }
                let found = false;
                pointSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) lineSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) polygonSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) highlightSource.clear();
            });

            $("#deleteModal").on('hidden.bs.modal', function() {
                highlightSource.clear();
                $("#deleteForm")[0].reset();
            });

            // Panel toggles for desktop
            $('#layerToggleBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
                $('#searchLabel, #editLabel').addClass('closed');
            });
            $('#closeLayerPanel').click(function() {
                $('#layerSwitcher').addClass('closed');
            });
            $('#closeRouteBtn').click(function() {
                $('#routeInfoPanel').addClass('closed');
                routeSource.clear();
            });
            $('#searchToggleBtn').click(function(e) {
                e.stopPropagation();
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher, #editLabel').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(), 100);
            });
            $('#editToggleBtn').click(function(e) {
                e.stopPropagation();
                $('#editLabel').toggleClass('closed');
                $('#searchLabel, #layerSwitcher').addClass('closed');
            });

            // Mobile bottom navigation handlers
            $('#mobileLayerBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
                $('#searchLabel, #editLabel').addClass('closed');
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileSearchBtn').click(function() {
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher, #editLabel').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(), 100);
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileLocationBtn').click(function() {
                toggleLiveLocation();
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileRouteBtn').click(function() {
                if (!selectedFeature) {
                    showToast('Please search for a location first', 'error');
                    return;
                }
                if (!currentLocationMarker) {
                    if (confirm('Enable location for route calculation?')) {
                        toggleLiveLocation();
                        setTimeout(() => {
                            if (currentLocationMarker) calculateAndDisplayRoute(selectedFeature);
                        }, 2500);
                    }
                    return;
                }
                calculateAndDisplayRoute(selectedFeature);
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileEditBtn').click(function() {
                $('#editLabel').toggleClass('closed');
                $('#searchLabel, #layerSwitcher').addClass('closed');
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });

            $('#editToolSelect').on('change', function() {
                const value = $(this).val();
                removeDrawInteractions();
                if (value === "Polygon") activateDrawPolygon();
                else if (value === "Line") activateDrawLine();
                else if (value === "Point") activateDrawPoint();
                else if (value === "Modify") activateModify();
                else if (value === "Delete") activateDelete();
                else {
                    isModifyMode = false;
                    isDrawingActive = false;
                }
                $('#editLabel').addClass('closed');
            });

            // Layer toggles
            $('#osmToggle').change(function() {
                osmLayer.setVisible($(this).is(':checked'));
            });
            $('#satelliteToggle').change(function() {
                satelliteLayer.setVisible($(this).is(':checked'));
            });
            $('#droneToggle').change(function() {
                droneLayer.setVisible($(this).is(':checked'));
            });
            $('#polygonToggle').change(function() {
                polygonLayer.setVisible($(this).is(':checked'));
            });
            $('#lineToggle').change(function() {
                lineLayer.setVisible($(this).is(':checked'));
            });
            $('#pointToggle').change(function() {
                pointLayer.setVisible($(this).is(':checked'));
            });

            // Search input
            $('#searchInput').on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(searchDebounceTimer);
                if (query.length < 2) {
                    $('#searchSuggestions').removeClass('show');
                    return;
                }
                searchDebounceTimer = setTimeout(() => displaySuggestions(getSearchSuggestions(query)),
                    300);
            });

            $('#searchGisidBtn').on('click', function() {
                const searchvalue = $("#searchInput").val().trim();
                if (!searchvalue) {
                    showToast("Please enter a GIS ID or Road Name", 'error');
                    return;
                }
                let found = polygons.find(p => p.gisid == searchvalue);
                if (found) {
                    navigateToFeature(found, 'polygon');
                    return;
                }
                found = lines.find(l => l.gisid == searchvalue || (l.road_name && l.road_name.toLowerCase()
                    .includes(searchvalue.toLowerCase())));
                if (found) {
                    navigateToFeature(found, 'line');
                    return;
                }
                found = points.find(p => p.gisid == searchvalue);
                if (found) {
                    navigateToFeature(found, 'point');
                    return;
                }
                showToast("GIS ID or Road Name not found", 'error');
            });

            $(document).on('click', '.suggestion-item', function() {
                const index = $(this).data('index');
                if (index !== undefined && currentSuggestions[index]) selectSuggestion(currentSuggestions[
                    index]);
            });

            // Live location
            let currentLocationMarker = null;

            function toggleLiveLocation() {
                if (!("geolocation" in navigator)) {
                    showToast("Geolocation not supported", 'error');
                    return;
                }
                showToast('Fetching your location...', 'info');
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords
                            .latitude
                        ]);
                        map.getView().animate({
                            center: coords,
                            zoom: 18,
                            duration: 1000
                        });
                        if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                        currentLocationMarker = new ol.layer.Vector({
                            source: new ol.source.Vector(),
                            style: new ol.style.Style({
                                image: new ol.style.Circle({
                                    radius: 12,
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
                        currentLocationMarker.getSource().addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        }));
                        map.addLayer(currentLocationMarker);
                        showToast('Location found!', 'success');
                    },
                    function(error) {
                        let msg = "Error getting location";
                        if (error.code === error.PERMISSION_DENIED) msg = "Please enable location permissions";
                        showToast(msg, 'error');
                    }
                );
            }
            $('#liveToggleBtn').click(toggleLiveLocation);

            // Route functions
            async function getRouteFromOSRM(startCoord, endCoord) {
                const url =
                    `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson`;
                const response = await fetch(url);
                const data = await response.json();
                if (data.code !== 'Ok' || !data.routes.length) throw new Error('No route found');
                return data.routes[0];
            }

            function drawRouteOnMap(geometry) {
                routeSource.clear();
                if (geometry.type === 'LineString') {
                    const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                    routeSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coordinates)
                    }));
                    map.getView().fit(routeSource.getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }

            function formatDistance(meters) {
                return meters >= 1000 ? (meters / 1000).toFixed(1) + ' km' : Math.round(meters) + ' m';
            }

            function formatDuration(seconds) {
                return seconds >= 60 ? Math.floor(seconds / 60) + ' min' : Math.floor(seconds) + ' sec';
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker) {
                    showToast('Please enable your location first', 'error');
                    return;
                }
                $('#loadingSpinner').show();
                try {
                    const currentCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry()
                        .getCoordinates();
                    const geometry = feature.getGeometry();
                    let targetCoords = geometry.getType() === 'Point' ? geometry.getCoordinates() : ol.extent
                        .getCenter(geometry.getExtent());
                    const currentLonLat = ol.proj.toLonLat(currentCoords);
                    const targetLonLat = ol.proj.toLonLat(targetCoords);
                    let route;
                    try {
                        route = await getRouteFromOSRM(currentLonLat, targetLonLat);
                    } catch (e) {
                        route = {
                            distance: ol.sphere.getDistance(ol.proj.fromLonLat(currentLonLat), ol.proj
                                .fromLonLat(targetLonLat)),
                            geometry: {
                                type: "LineString",
                                coordinates: [currentLonLat, targetLonLat]
                            }
                        };
                    }
                    drawRouteOnMap(route.geometry);
                    $('#routeDistance').text(formatDistance(route.distance));
                    $('#routeDuration').text(formatDuration(route.duration / 1.39));
                    $('#destinationName').text(`GIS ID: ${feature.get('gisid') || 'Selected Location'}`);
                    $('#routeInfoPanel').removeClass('closed');
                    currentRoute = route;
                } catch (error) {
                    showToast('Error calculating route', 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            $('#routeBtn').click(async function() {
                if (!selectedFeature) {
                    showToast('Please search for a location first', 'error');
                    return;
                }
                if (!currentLocationMarker) {
                    if (confirm('Enable location for route calculation?')) {
                        toggleLiveLocation();
                        setTimeout(() => {
                            if (currentLocationMarker) calculateAndDisplayRoute(
                                selectedFeature);
                        }, 2500);
                    }
                    return;
                }
                await calculateAndDisplayRoute(selectedFeature);
            });

            $('#startNavigationBtn').click(function() {
                if (currentRoute && isMobile) {
                    window.open(
                        `https://www.google.com/maps/dir/?api=1&destination=${currentRoute.endCoord ? currentRoute.endCoord[1] : ''},${currentRoute.endCoord ? currentRoute.endCoord[0] : ''}`,
                        '_blank');
                }
            });

            $(document).click(function(event) {
                if (!$(event.target).closest(
                        '#layerSwitcher, #layerToggleBtn, #searchLabel, #searchToggleBtn, #routeInfoPanel, #routeBtn, #editLabel, #editToggleBtn, #searchSuggestions, .mobile-bottom-nav'
                    ).length) {
                    $('#layerSwitcher, #searchLabel, #editLabel').addClass('closed');
                    $('#searchSuggestions').removeClass('show');
                }
            });

            $(window).resize(function() {
                isMobile = window.innerWidth <= 768;
            });
            $(document).keydown(function(e) {
                if (e.key === 'l' || e.key === 'L') {
                    $('#layerSwitcher').toggleClass('closed');
                    $('#searchLabel, #editLabel').addClass('closed');
                }
                if (e.key === 's' || e.key === 'S') {
                    $('#searchLabel').toggleClass('closed');
                    $('#layerSwitcher, #editLabel').addClass('closed');
                    if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(),
                        100);
                }
                if (e.key === 'Escape') {
                    $('#layerSwitcher, #searchLabel, #routeInfoPanel, #editLabel').addClass('closed');
                    $('#searchSuggestions').removeClass('show');
                }
            });

            setupOriginalClickHandler();
            showToast('Map loaded successfully', 'success');
        });
    </script>
@endpush
