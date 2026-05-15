@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Action Buttons -->
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
            <button class="action-btn route-btn" id="routeBtn" title="Clear Route">
                <i class="fas fa-route"></i>
                <span class="btn-label">Route</span>
            </button>
        </div>

        <!-- Zoom Controls -->
        <div class="zoom-controls">
            <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
            <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
        </div>

        <!-- Layer Switcher -->
        <div class="layer-switcher panel" id="layerSwitcher">
            <button class="panel-close" id="closeLayerPanel">&times;</button>
            <h5><i class="fas fa-layer-group"></i> Layers</h5>

            <div class="layer-group">
                <div class="group-title">Base Map</div>
                <label><input type="radio" name="baseLayer" value="osm" checked> OpenStreetMap</label>
                <label><input type="radio" name="baseLayer" value="satellite"> Satellite</label>
                <label><input type="radio" name="baseLayer" value="drone"> Drone Image</label>
            </div>

            <div class="layer-group">
                <div class="group-title">Map Layers</div>
                <label><input type="checkbox" id="toggleBuildings" checked> Buildings</label>
                <label><input type="checkbox" id="toggleRoads" checked> Roads</label>
                <label><input type="checkbox" id="toggleBoundary" checked> Ward Boundary</label>
            </div>
        </div>

        <!-- Legend -->
        <div class="map-legend panel" id="mapLegend">
            <button class="panel-close" id="closeLegendPanel">&times;</button>
            <h5><i class="fas fa-info-circle"></i> Legend</h5>

            <div class="legend-item">
                <div class="legend-color building"></div>
                <div>Building Polygon</div>
            </div>
            <div class="legend-item">
                <div class="legend-color road"></div>
                <div>Road Line</div>
            </div>
            <div class="legend-item">
                <div class="legend-color boundary"></div>
                <div>Ward Boundary</div>
            </div>
        </div>

        <!-- Search Panel -->
        <div class="search-panel panel" id="searchPanel">
            <button class="panel-close" id="closeSearchPanel">&times;</button>
            <h5><i class="fas fa-search"></i> Search Building</h5>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by GIS ID / Owner / Assessment / Phone / Road">
                <button id="searchBtn">Search</button>
            </div>

            <div class="search-results" id="searchResults">
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>Search for a building</p>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="filter-panel panel" id="filterPanel">
            <button class="panel-close" id="closeFilterPanel">&times;</button>
            <h5><i class="fas fa-filter"></i> Filter</h5>

            <div class="filter-group">
                <label for="filterBuildingUsage">Building Usage</label>
                <input type="text" id="filterBuildingUsage" placeholder="e.g. Residential">
            </div>

            <div class="filter-group">
                <label for="filterRoadName">Road Name</label>
                <input type="text" id="filterRoadName" placeholder="e.g. Gandhi Road">
            </div>

            <div class="filter-group">
                <label for="filterZone">Zone</label>
                <input type="text" id="filterZone" placeholder="e.g. North">
            </div>

            <div class="filter-actions">
                <button class="apply-btn" id="applyFilterBtn">Apply</button>
                <button class="reset-btn" id="resetFilterBtn">Reset</button>
            </div>

            <div class="filter-count" id="filterCount">No filter applied</div>
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

    <!-- Navigation Header -->
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
            color: #fff;
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

        .ol-control {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        $(document).ready(function() {
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

            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            let currentLocationLayer = null,
                accuracyLayer = null,
                currentPosition = null;
            let locationTracking = false,
                watchId = null,
                mapCenteredOnce = false;

            let currentRoute = null;
            let routeSteps = [];
            let navigationMode = false;
            let navigationInterval = null;
            let currentStepIndex = 0;
            let routeSource = null;
            let routeLayer = null;
            let destinationMarker = null;
            let selectedBuilding = null;

            let allBuildings = [];

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

            function showFlashMessage(message, type = 'info') {
                const colors = {
                    success: '#28a745',
                    error: '#dc3545',
                    warning: '#ffc107',
                    info: '#17a2b8'
                };
                const bg = colors[type] || colors.info;
                const $flash = $(`
            <div class="alert position-fixed flash-alert"
                style="top:20px;right:20px;z-index:9999;background:${bg};color:white;padding:12px 20px;border-radius:10px;min-width:250px;">
                <span>${message}</span>
                <button type="button" class="btn-close btn-close-white" style="float:right;margin-left:10px;"></button>
            </div>
        `);
                $('body').append($flash);
                $flash.find('.btn-close').on('click', function() {
                    $flash.remove();
                });
                setTimeout(() => {
                    $flash.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 4000);
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel, #routeInfo').removeClass('open');
            }

            function formatDistance(meters) {
                if (meters < 1000) return meters.toFixed(0) + ' m';
                return (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + ' min';
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return hours + 'h ' + mins + 'm';
            }

            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    const [startLon, startLat] = startCoord;
                    const [endLon, endLat] = endCoord;
                    const url =
                        `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        throw new Error('No route found');
                    }

                    return data.routes[0];
                } catch (error) {
                    console.warn('OSRM route failed, using straight line:', error);
                    return getStraightLineRoute(startCoord, endCoord);
                }
            }

            function getStraightLineRoute(startCoord, endCoord) {
                const distance = ol.sphere.getDistance(startCoord, endCoord);
                const duration = distance / 1.39;

                return {
                    distance: distance,
                    duration: duration,
                    geometry: {
                        type: "LineString",
                        coordinates: [startCoord, endCoord]
                    },
                    legs: [{
                        steps: [{
                                maneuver: {
                                    type: "depart",
                                    instruction: "Start from your location"
                                },
                                distance: distance,
                                duration: duration
                            },
                            {
                                maneuver: {
                                    type: "arrive",
                                    instruction: "Arrive at destination"
                                },
                                distance: 0,
                                duration: 0
                            }
                        ]
                    }]
                };
            }

            function parseOSRMSteps(route) {
                const steps = [];
                let accumulatedDistance = 0;

                if (route.legs && route.legs[0] && route.legs[0].steps) {
                    route.legs[0].steps.forEach((step) => {
                        accumulatedDistance += step.distance;

                        let instruction = step.maneuver.instruction || step.maneuver.type;
                        let icon = 'fas fa-arrow-right';

                        switch (step.maneuver.type) {
                            case 'depart':
                                icon = 'fas fa-play';
                                break;
                            case 'arrive':
                                icon = 'fas fa-flag-checkered';
                                break;
                            case 'turn':
                                if (step.maneuver.modifier === 'left') icon = 'fas fa-arrow-left';
                                else if (step.maneuver.modifier === 'right') icon = 'fas fa-arrow-right';
                                else if (step.maneuver.modifier === 'straight') icon = 'fas fa-arrow-up';
                                else icon = 'fas fa-turn-up';
                                break;
                            default:
                                icon = 'fas fa-arrow-right';
                        }

                        steps.push({
                            instruction: instruction,
                            distance: formatDistance(accumulatedDistance),
                            icon: icon,
                            type: step.maneuver.type
                        });
                    });
                }

                return steps;
            }

            function drawRouteOnMap(geometry) {
                if (!routeLayer) {
                    routeSource = new ol.source.Vector();
                    routeLayer = new ol.layer.Vector({
                        source: routeSource,
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: '#0066cc',
                                width: 5,
                                lineDash: [10, 8]
                            })
                        })
                    });
                    map.addLayer(routeLayer);
                }

                routeSource.clear();

                const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                const feature = new ol.Feature({
                    geometry: new ol.geom.LineString(coordinates)
                });

                routeSource.addFeature(feature);

                map.getView().fit(feature.getGeometry().getExtent(), {
                    padding: [80, 80, 80, 80],
                    duration: 800,
                    maxZoom: 18
                });
            }

            function displayRouteInfo(distance, duration, destinationName) {
                $('#routeSummary').html(`
            <div><strong>Total Distance:</strong> ${formatDistance(distance)}</div>
            <div><strong>Estimated Time:</strong> ${formatDuration(duration)}</div>
            <div><strong>Destination:</strong> ${destinationName}</div>
        `);
            }

            function displayTurnByTurnDirections() {
                const directionsList = $('#directionsList');
                directionsList.empty();

                routeSteps.forEach((step, index) => {
                    directionsList.append(`
                <div class="direction-step">
                    <div class="step-number">${index + 1}</div>
                    <div class="step-content">
                        <div class="step-instruction"><i class="${step.icon} me-2"></i> ${step.instruction}</div>
                        <div class="step-distance">${step.distance}</div>
                    </div>
                </div>
            `);
                });
            }

            async function calculateAndDisplayRoute(startCoord, endCoord, destinationName, buildingGisid = null) {
                $('#loadingSpinner').css('display', 'flex');

                try {
                    const route = await getRouteFromOSRM(startCoord, endCoord);
                    const totalDistance = route.distance;
                    const totalDuration = route.duration;

                    routeSteps = parseOSRMSteps(route);
                    currentRoute = {
                        distance: totalDistance,
                        duration: totalDuration,
                        geometry: route.geometry,
                        endCoord: endCoord,
                        placeName: destinationName,
                        gisid: buildingGisid
                    };

                    drawRouteOnMap(route.geometry);
                    displayRouteInfo(totalDistance, totalDuration, destinationName);
                    displayTurnByTurnDirections();

                    if (destinationMarker) {
                        map.removeLayer(destinationMarker);
                    }

                    destinationMarker = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [
                                new ol.Feature({
                                    geometry: new ol.geom.Point(ol.proj.fromLonLat(
                                        endCoord))
                                })
                            ]
                        }),
                        style: new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 14,
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

                    map.addLayer(destinationMarker);
                    $('#routeInfo').addClass('open');
                    showFlashMessage('Route calculated successfully!', 'success');
                } catch (error) {
                    console.error('Route calculation error:', error);
                    showFlashMessage('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            async function getRouteToBuilding(gisid, coords) {
                if (!currentPosition) {
                    showFlashMessage('Please enable location tracking first', 'warning');
                    startLocationTracking();
                    setTimeout(() => {
                        if (currentPosition) getRouteToBuilding(gisid, coords);
                    }, 2000);
                    return;
                }

                await calculateAndDisplayRoute(currentPosition, coords, `Building: ${gisid}`, gisid);
                closeAllPanels();
            }

            function startNavigation() {
                if (!currentRoute) {
                    showFlashMessage('Please calculate a route first', 'warning');
                    return;
                }

                if (!currentPosition) {
                    showFlashMessage('Please enable live location first', 'warning');
                    return;
                }

                navigationMode = true;
                currentStepIndex = 0;

                $('#navigationHeader').show();
                $('#routeInfo').removeClass('open');

                updateNavigationDisplay();

                if (navigationInterval) clearInterval(navigationInterval);
                navigationInterval = setInterval(updateNavigationStatus, 3000);

                showFlashMessage('Navigation started! Follow the route instructions.', 'success');
            }

            function updateNavigationDisplay() {
                if (!currentRoute) return;

                const remainingDistance = currentRoute.distance * (1 - (currentStepIndex / Math.max(routeSteps
                    .length, 1)));
                const remainingTime = currentRoute.duration * (1 - (currentStepIndex / Math.max(routeSteps.length,
                    1)));

                $('#etaTime').text(formatDuration(remainingTime));
                $('#etaDistance').text(formatDistance(remainingDistance));
                $('#destinationAddress').text(currentRoute.placeName);

                if (currentStepIndex < routeSteps.length) {
                    $('#instructionText').text(routeSteps[currentStepIndex].instruction);
                    $('#instructionDistance').text(routeSteps[currentStepIndex].distance);
                    $('#instructionIcon').attr('class', routeSteps[currentStepIndex].icon);
                    $('#navigationInstruction').show();
                } else {
                    $('#navigationInstruction').hide();
                    if (navigationMode) {
                        showFlashMessage('You have arrived at your destination!', 'success');
                        stopNavigation();
                    }
                }
            }

            function updateNavigationStatus() {
                if (!navigationMode || !currentRoute) return;

                if (currentStepIndex < routeSteps.length - 1) {
                    currentStepIndex++;
                    updateNavigationDisplay();
                } else {
                    stopNavigation();
                    showFlashMessage('You have arrived at your destination!', 'success');
                }
            }

            function stopNavigation() {
                navigationMode = false;
                if (navigationInterval) {
                    clearInterval(navigationInterval);
                    navigationInterval = null;
                }
                $('#navigationHeader').hide();
                $('#navigationInstruction').hide();
            }

            function clearRoute() {
                if (routeLayer && routeSource) routeSource.clear();
                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }
                currentRoute = null;
                routeSteps = [];
                stopNavigation();
                $('#routeInfo').removeClass('open');
            }

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
                                        cx += parseFloat(c[0]);
                                        cy += parseFloat(c[1]);
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

            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showFlashMessage("Geolocation not supported", "error");
                    return;
                }

                $('#locationBtn').addClass('active');
                locationTracking = true;

                if ($('#centerMyLocationBtn').length === 0) {
                    $('body').append(
                        '<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>'
                        );
                    $('#centerMyLocationBtn').on('click', centerToMyLocation);
                }

                navigator.geolocation.getCurrentPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function(err) {
                    showFlashMessage("Unable to get location: " + err.message, "error");
                    locationTracking = false;
                    $('#locationBtn').removeClass('active');
                    $('#centerMyLocationBtn').remove();
                });

                watchId = navigator.geolocation.watchPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                }, function() {}, {
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
                currentPosition = null;
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentLocationLayer = null;
                accuracyLayer = null;
                mapCenteredOnce = false;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);

                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [
                            new ol.Feature({
                                geometry: new ol.geom.Circle(coords, accuracy)
                            })
                        ]
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
                        features: [
                            new ol.Feature({
                                geometry: new ol.geom.Point(coords)
                            })
                        ]
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

                if (!mapCenteredOnce) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    mapCenteredOnce = true;
                }
            }

            function centerToMyLocation() {
                if (currentPosition) {
                    map.getView().setCenter(ol.proj.fromLonLat(currentPosition));
                    map.getView().setZoom(19);
                    showFlashMessage('Centered on your location', 'info');
                } else {
                    showFlashMessage('Location not available. Please enable location tracking first.', 'warning');
                    startLocationTracking();
                }
            }

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

                    if (b.gisid && String(b.gisid).toLowerCase().includes(term)) {
                        match = true;
                        type = 'GIS ID';
                        val = b.gisid;
                    } else if (b.building_usage && String(b.building_usage).toLowerCase().includes(term)) {
                        match = true;
                        type = 'Building Usage';
                        val = b.building_usage;
                    } else if (b.road_name && String(b.road_name).toLowerCase().includes(term)) {
                        match = true;
                        type = 'Road Name';
                        val = b.road_name;
                    } else {
                        $.each(b.assessments, function(j, a) {
                            if (a.assessment_no && String(a.assessment_no).toLowerCase().includes(
                                    term)) {
                                match = true;
                                type = 'Assessment No';
                                val = a.assessment_no;
                                return false;
                            }
                            if (a.owner_name && String(a.owner_name).toLowerCase().includes(term)) {
                                match = true;
                                type = 'Owner Name';
                                val = a.owner_name;
                                return false;
                            }
                            if (a.phone && String(a.phone).toLowerCase().includes(term)) {
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

                let $res = $('#searchResults').empty();

                if (!results.length) {
                    $res.html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    let lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    let lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`
                <div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                    <div class="result-gisid"><i class="fas fa-building"></i> ${r.gisid}</div>
                    <div class="result-owner"><i class="fas fa-tag"></i> ${r.matchType}: ${r.matchValue}</div>
                    <div class="result-owner"><i class="fas fa-location-dot"></i> ${r.building.road_name || 'No road'} | ${r.building.zone || 'No zone'}</div>
                    <button class="direction-btn"><i class="fas fa-directions"></i> Get Directions</button>
                </div>
            `);
                });

                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).closest('.direction-btn').length) {
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
                        selectedBuilding = {
                            gisid: p.data('gisid'),
                            coords: [parseFloat(lon), parseFloat(lat)]
                        };
                        getRouteToBuilding(p.data('gisid'), [parseFloat(lon), parseFloat(lat)]);
                        closeAllPanels();
                    } else {
                        showFlashMessage("Coordinates not available for this building", 'error');
                    }
                });
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer) return;

                let features = polygonLayer.getSource().getFeatures();
                let feature = features.find(f => f.get('gisid') == gisid);

                if (feature) {
                    let extent = feature.getGeometry().getExtent();
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 800,
                        maxZoom: 20
                    });
                    showPopup(gisid, ol.extent.getCenter(extent));
                } else {
                    showFlashMessage("Building not found on map", 'error');
                }
            }

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
                if (popupOverlay) popupOverlay.setPosition(undefined);
            };

            window.switchTab = function(t) {
                $('.popup-tab-content, .popup-tab').removeClass('active');
                $('#tab-' + t).addClass('active');
                $('.popup-tab[data-tab="' + t + '"]').addClass('active');
                currentActiveTab = t;
            };

            function showPopup(gisid, coord3857) {
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

                let buildingHtml = `<div class="building-details-content">${
            [
                ['fingerprint', 'GIS ID', pd.gisid],
                ['building', 'Building Usage', pd.building_usage],
                ['home', 'Building Type', pd.building_type],
                ['layer-group', 'Floors', pd.number_floor],
                ['receipt', 'Total Bills', pd.number_bill],
                ['store', 'Total Shops', pd.total_shops],
                ['road', 'Road Name', pd.road_name],
                ['map-pin', 'Zone', pd.zone]
            ].map(([i,l,v]) =>
                `<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v || 'N/A'}</div></div>`
            ).join('')
        }</div>`;

                let assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) => `
                <div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}">
                    <div class="assessment-header">
                        <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i + 1)}</span>
                        <span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span>
                    </div>
                    <div class="assessment-body">
                        ${[
                            ['Owner', a.owner_name || a.present_owner_name],
                            ['Phone', a.phone_number],
                            ['Floor', a.floor],
                            ['Usage', a.bill_usage],
                            ['Shops', (a.shops || []).length]
                        ].map(([l,v]) => `
                                <div class="assessment-row">
                                    <div class="assessment-label">${l}:</div>
                                    <div class="assessment-value">${v || 'N/A'}</div>
                                </div>
                            `).join('')}
                    </div>
                </div>
            `).join('');

                let shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' :
                    shops.map(s => `
                <div class="shop-item">
                    <div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name || 'Shop'}</div>
                    ${[
                        ['Category', s.shop_category],
                        ['Owner', s.shop_owner_name],
                        ['Mobile', s.shop_mobile]
                    ].map(([l,v]) => `
                            <div class="assessment-row">
                                <div class="assessment-label">${l}:</div>
                                <div class="assessment-value">${v || 'N/A'}</div>
                            </div>
                        `).join('')}
                </div>
            `).join('');

                let popupLonLat = ol.proj.toLonLat(coord3857);

                let html = `
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
            <div id="tab-shops" class="popup-tab-content ${currentActiveTab == 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>
            <div style="padding:16px; border-top:1px solid rgba(255,255,255,0.1);">
                <button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button>
            </div>
        `;

                $(popupElement).html(html).show();

                if ($(window).width() > 768 && popupOverlay) {
                    popupOverlay.setPosition(coord3857);
                }

                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    getRouteToBuilding(pd.gisid, popupLonLat);
                });

                $('.assessment-card').off('click').on('click', function() {
                    $('.assessment-form-container').remove();

                    let id = $(this).data('id');
                    let num = $(this).data('assessment');

                    $(this).after(`
                <div class="assessment-form-container">
                    <button class="close-form-btn" style="float:right; background:none; border:none; color:#ff4444; font-size:20px;">&times;</button>
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
                                <option value="">Select Usage</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Mixed">Mixed</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <button type="submit" style="flex:1; padding:10px; background:#28a745; border:none; border-radius:8px; color:white;">Save QC</button>
                        </div>
                    </form>
                </div>
            `);
                });

                $(document).off('click', '.close-form-btn').on('click', '.close-form-btn', function() {
                    $(this).closest('.assessment-form-container').remove();
                });

                $(document).off('submit', '.qc-form').on('submit', '.qc-form', function(e) {
                    e.preventDefault();
                    showFlashMessage('QC form submission hook ready. Connect your Laravel route here.',
                        'info');
                });
            }

            function createPolygonLayer() {
                const features = [];

                $.each(polygons, function(i, poly) {
                    try {
                        let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) :
                            poly.coordinates;
                        if (!coords || !coords.length) return;

                        const transformed = coords.map(ring =>
                            ring.map(c => ol.proj.fromLonLat([parseFloat(c[0]), parseFloat(c[1])]))
                        );

                        const feature = new ol.Feature({
                            geometry: new ol.geom.Polygon(transformed),
                            gisid: poly.gisid
                        });

                        feature.setStyle(new ol.style.Style({
                            fill: new ol.style.Fill({
                                color: 'rgba(255, 68, 68, 0.35)'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#ff4444',
                                width: 2
                            })
                        }));

                        features.push(feature);
                    } catch (e) {
                        console.error('Polygon parse error', e);
                    }
                });

                polygonLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features
                    })
                });

                map.addLayer(polygonLayer);
            }

            function createLineLayer() {
                const features = [];

                $.each(lines, function(i, line) {
                    try {
                        let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) :
                            line.coordinates;
                        if (!coords || !coords.length) return;

                        const transformed = coords.map(c => ol.proj.fromLonLat([parseFloat(c[0]),
                            parseFloat(c[1])
                        ]));

                        const feature = new ol.Feature({
                            geometry: new ol.geom.LineString(transformed)
                        });

                        features.push(feature);
                    } catch (e) {
                        console.error('Line parse error', e);
                    }
                });

                lineLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ffc107',
                            width: 3
                        })
                    })
                });

                map.addLayer(lineLayer);
            }

            function createBoundaryLayer() {
                if (!wardData.boundary) return;

                try {
                    let coords = typeof wardData.boundary === 'string' ? JSON.parse(wardData.boundary) : wardData
                        .boundary;
                    if (!coords || !coords.length) return;

                    const transformed = coords.map(ring =>
                        ring.map(c => ol.proj.fromLonLat([parseFloat(c[0]), parseFloat(c[1])]))
                    );

                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon(transformed)
                    });

                    boundaryLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [feature]
                        }),
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: '#ff0000',
                                width: 3,
                                lineDash: [8, 8]
                            }),
                            fill: new ol.style.Fill({
                                color: 'rgba(255, 0, 0, 0.02)'
                            })
                        })
                    });

                    map.addLayer(boundaryLayer);
                } catch (e) {
                    console.error('Boundary parse error', e);
                }
            }

            function createImageLayer() {
                if (!wardData.drone_image || !wardData.extent_left || !wardData.extent_bottom || !wardData
                    .extent_right || !wardData.extent_top) {
                    return;
                }

                const imageExtent = ol.proj.transformExtent(
                    [
                        parseFloat(wardData.extent_left),
                        parseFloat(wardData.extent_bottom),
                        parseFloat(wardData.extent_right),
                        parseFloat(wardData.extent_top)
                    ],
                    'EPSG:4326',
                    'EPSG:3857'
                );

                imageLayer = new ol.layer.Image({
                    source: new ol.source.ImageStatic({
                        url: wardData.drone_image,
                        imageExtent: imageExtent
                    }),
                    visible: false
                });

                map.addLayer(imageLayer);
            }

            function setBaseLayer(type) {
                currentBaseLayer = type;

                if (osmLayer) osmLayer.setVisible(type === 'osm');
                if (satelliteLayer) satelliteLayer.setVisible(type === 'satellite');
                if (imageLayer) imageLayer.setVisible(type === 'drone');
            }

            function applyFilters() {
                const usage = $('#filterBuildingUsage').val().toLowerCase().trim();
                const road = $('#filterRoadName').val().toLowerCase().trim();
                const zone = $('#filterZone').val().toLowerCase().trim();

                let visibleCount = 0;

                polygonLayer.getSource().getFeatures().forEach(feature => {
                    const gisid = feature.get('gisid');
                    const pd = polygonDatas.find(p => p.gisid == gisid);

                    let visible = true;

                    if (pd) {
                        if (usage && !(pd.building_usage || '').toLowerCase().includes(usage)) visible =
                            false;
                        if (road && !(pd.road_name || '').toLowerCase().includes(road)) visible = false;
                        if (zone && !(pd.zone || '').toLowerCase().includes(zone)) visible = false;
                    }

                    feature.setStyle(visible ? new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 68, 68, 0.35)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        })
                    }) : null);

                    feature.set('hiddenByFilter', !visible);
                    if (visible) visibleCount++;
                });

                $('#filterCount').text(`Showing ${visibleCount} building(s)`);
            }

            function resetFilters() {
                $('#filterBuildingUsage, #filterRoadName, #filterZone').val('');
                polygonLayer.getSource().getFeatures().forEach(feature => {
                    feature.set('hiddenByFilter', false);
                    feature.setStyle(new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 68, 68, 0.35)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        })
                    }));
                });
                $('#filterCount').text('No filter applied');
            }

            function initMap() {
                showLoading(true);

                osmLayer = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });

                satelliteLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}'
                    }),
                    visible: false
                });

                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: ol.proj.fromLonLat([78.1198, 9.9252]),
                        zoom: 16
                    }),
                    controls: []
                });

                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);

                createImageLayer();
                createPolygonLayer();
                createLineLayer();
                createBoundaryLayer();
                buildSearchIndex();

                if (polygonLayer && polygonLayer.getSource().getFeatures().length) {
                    map.getView().fit(polygonLayer.getSource().getExtent(), {
                        padding: [60, 60, 60, 60],
                        duration: 1000,
                        maxZoom: 19
                    });
                } else if (boundaryLayer && boundaryLayer.getSource().getFeatures().length) {
                    map.getView().fit(boundaryLayer.getSource().getExtent(), {
                        padding: [60, 60, 60, 60],
                        duration: 1000,
                        maxZoom: 19
                    });
                }

                map.on('singleclick', function(evt) {
                    let found = false;

                    map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                        if (layer === polygonLayer && !feature.get('hiddenByFilter')) {
                            found = true;
                            showPopup(feature.get('gisid'), evt.coordinate);
                            return true;
                        }
                    });

                    if (!found && $(window).width() > 768) {
                        closePopup();
                    }
                });

                showLoading(false);
            }

            $('#menuBtn').on('click', function() {
                const panel = $('#layerSwitcher');
                const isOpen = panel.hasClass('open');
                closeAllPanels();
                if (!isOpen) panel.addClass('open');
            });

            $('#legendBtn').on('click', function() {
                const panel = $('#mapLegend');
                const isOpen = panel.hasClass('open');
                closeAllPanels();
                if (!isOpen) panel.addClass('open');
            });

            $('#openSearchBtn').on('click', function() {
                const panel = $('#searchPanel');
                const isOpen = panel.hasClass('open');
                closeAllPanels();
                if (!isOpen) panel.addClass('open');
            });

            $('#filterBtn').on('click', function() {
                const panel = $('#filterPanel');
                const isOpen = panel.hasClass('open');
                closeAllPanels();
                if (!isOpen) panel.addClass('open');
            });

            $('#routeBtn').on('click', function() {
                clearRoute();
                showFlashMessage('Route cleared', 'info');
            });

            $('#closeLayerPanel').on('click', () => $('#layerSwitcher').removeClass('open'));
            $('#closeLegendPanel').on('click', () => $('#mapLegend').removeClass('open'));
            $('#closeSearchPanel').on('click', () => $('#searchPanel').removeClass('open'));
            $('#closeFilterPanel').on('click', () => $('#filterPanel').removeClass('open'));
            $('#closeRouteInfo').on('click', () => $('#routeInfo').removeClass('open'));
            $('#closeNavigation').on('click', stopNavigation);

            $('#zoomInBtn').on('click', function() {
                const view = map.getView();
                view.animate({
                    zoom: view.getZoom() + 1,
                    duration: 200
                });
            });

            $('#zoomOutBtn').on('click', function() {
                const view = map.getView();
                view.animate({
                    zoom: view.getZoom() - 1,
                    duration: 200
                });
            });

            $('input[name="baseLayer"]').on('change', function() {
                setBaseLayer($(this).val());
            });

            $('#toggleBuildings').on('change', function() {
                if (polygonLayer) polygonLayer.setVisible(this.checked);
            });

            $('#toggleRoads').on('change', function() {
                if (lineLayer) lineLayer.setVisible(this.checked);
            });

            $('#toggleBoundary').on('change', function() {
                if (boundaryLayer) boundaryLayer.setVisible(this.checked);
            });

            $('#searchBtn').on('click', function() {
                searchBuildings($('#searchInput').val());
            });

            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    searchBuildings($(this).val());
                }
            });

            $('#applyFilterBtn').on('click', applyFilters);
            $('#resetFilterBtn').on('click', resetFilters);

            $('#locationBtn').on('click', function() {
                if (locationTracking) {
                    stopLocationTracking();
                    showFlashMessage('Location tracking stopped', 'info');
                } else {
                    startLocationTracking();
                    showFlashMessage('Location tracking started', 'success');
                }
            });

            $('#startNavigationBtn').on('click', startNavigation);

            initMap();
        });
    </script>
@endpush
