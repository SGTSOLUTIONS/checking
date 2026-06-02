@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Desktop Action Buttons -->
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

        <!-- Mobile Bottom Navigation -->
        <div class="mobile-bottom-nav">
            <button class="mobile-nav-btn" id="mobileMenuBtn">
                <i class="fas fa-layer-group"></i>
                <span>Layers</span>
            </button>
            <button class="mobile-nav-btn" id="mobileSearchBtn">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </button>
            <button class="mobile-nav-btn" id="mobileLocationBtn">
                <i class="fas fa-location-dot"></i>
                <span>Location</span>
            </button>
            <button class="mobile-nav-btn" id="mobileRouteBtn">
                <i class="fas fa-route"></i>
                <span>Route</span>
            </button>
            <button class="mobile-nav-btn" id="mobileFilterBtn">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <!-- Panels -->
    <div class="layer-switcher panel" id="layerSwitcher">
        <button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button>
        <h5><i class="fas fa-layer-group"></i> Layers</h5>
        <div class="layer-group">
            <div class="group-title">Base Maps</div>
            <label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i>
                OpenStreetMap</label>
            <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i>
                Satellite</label>
        </div>
        <div class="layer-group">
            <div class="group-title">Overlays</div>
            <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
            <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
            <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward
                Boundary</label>
        </div>
    </div>

    <div class="map-legend panel" id="mapLegend">
        <button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button>
        <h5><i class="fas fa-info-circle"></i> Legend</h5>
        <div class="legend-item">
            <div class="legend-color residential"></div><span>Residential</span>
        </div>
        <div class="legend-item">
            <div class="legend-color commercial"></div><span>Commercial</span>
        </div>
        <div class="legend-item">
            <div class="legend-color industrial"></div><span>Industrial</span>
        </div>
        <div class="legend-item">
            <div class="legend-color institutional"></div><span>Institutional</span>
        </div>
        <div class="legend-item">
            <div class="legend-color mixed"></div><span>Mixed Use</span>
        </div>
        <div class="legend-item">
            <div class="legend-color government"></div><span>Government</span>
        </div>
        <div class="legend-item">
            <div class="legend-color vacant"></div><span>Vacant</span>
        </div>
        <div class="legend-item">
            <div class="legend-color default"></div><span>Other/Unknown</span>
        </div>
        <div class="legend-item mt-2">
            <div class="legend-color road"></div><span>Roads</span>
        </div>
        <div class="legend-item">
            <div class="legend-color boundary"></div><span>Ward Boundary</span>
        </div>
    </div>

    <div class="search-panel panel" id="searchPanel">
        <button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button>
        <h5><i class="fas fa-search"></i> Search Building</h5>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment...">
            <button id="doSearchBtn"><i class="fas fa-search"></i> Go</button>
        </div>
        <div id="searchResults" class="search-results"></div>
    </div>

    <div class="filter-panel panel" id="filterPanel">
        <button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button>
        <h5><i class="fas fa-filter"></i> Filter Buildings</h5>
        <div class="filter-group">
            <label>Building Usage</label>
            <select id="filterUsage">
                <option value="all">All Buildings</option>
                <option value="RESIDENTIAL">Residential</option>
                <option value="COMMERCIAL">Commercial</option>
                <option value="INDUSTRIAL">Industrial</option>
                <option value="INSTITUTIONAL">Institutional</option>
                <option value="MIXED">Mixed Use</option>
                <option value="GOVERNMENT">Government</option>
                <option value="VACANT">Vacant</option>
            </select>
        </div>
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

    <div class="zoom-controls">
        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
    </div>

    <button id="centerMyLocationBtn" class="center-btn" style="display: none;">
        <i class="fas fa-crosshairs"></i> Center to My Location
    </button>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary"></div>
        <div>Processing...</div>
    </div>
@endsection

@push('styles')
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

        /* Mobile Bottom Navigation */
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

        /* Action Buttons - Desktop */
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
                top: 50%;
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

        /* Panels */
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

        .layer-switcher,
        .map-legend,
        .search-panel,
        .filter-panel {
            display: none;
        }

        .layer-switcher.open,
        .map-legend.open,
        .search-panel.open,
        .filter-panel.open {
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
            width: 300px;
        }

        @media (max-width: 768px) {

            .layer-switcher,
            .map-legend,
            .search-panel,
            .filter-panel {
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
            .zoom-controls {
                bottom: 80px;
                right: 10px;
            }
        }

        @media (min-width: 769px) {
            .zoom-controls {
                bottom: auto;
                top: 100px;
                right: 20px;
                left: auto;
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
            .center-btn {
                bottom: 80px;
                left: 10px;
            }
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
            margin-bottom: 10px;
            font-size: 11px;
        }

        .legend-color {
            width: 24px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-color.residential {
            background: #4CAF50;
        }

        .legend-color.commercial {
            background: #2196F3;
        }

        .legend-color.industrial {
            background: #FF9800;
        }

        .legend-color.institutional {
            background: #9C27B0;
        }

        .legend-color.mixed {
            background: #FF5722;
        }

        .legend-color.government {
            background: #607D8B;
        }

        .legend-color.vacant {
            background: #9E9E9E;
        }

        .legend-color.default {
            background: #ff4444;
        }

        .legend-color.road {
            background: #ffc107;
            height: 3px;
        }

        .legend-color.boundary {
            background: #ff0000;
            height: 3px;
        }

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

        .badge-danger {
            background: #dc3545;
            color: white;
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
            flex-wrap: wrap;
            gap: 8px;
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
            border-left: 3px solid #28a745;
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
        }

        @keyframes slideDown {
            from {
                top: -50px;
                opacity: 0;
            }

            to {
                top: 20px;
                opacity: 1;
            }
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
            margin-top: 10px;
        }

        .btn-start-nav:active {
            transform: scale(0.98);
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

        .route-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 350px;
            max-width: calc(100% - 40px);
            max-height: 400px;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 15px;
            color: white;
            z-index: 1001;
            display: none;
        }

        .route-info.open {
            display: block;
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
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .direction-step {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 12px;
            font-size: 12px;
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
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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

            console.log(`Loaded ${polygonDatas.length} buildings and ${polygons.length} polygons`);

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== LOCATION VARIABLES ====================
            let currentLocationMarker = null;
            let accuracyCircle = null;
            let currentPosition = null;
            let locationWatchId = null;
            let isLocationEnabled = false;

            // ==================== ROUTE VARIABLES ====================
            let currentRoute = null;
            let routeLayer = null;
            let routeSource = null;
            let destinationMarker = null;
            let selectedFeature = null;

            // ==================== SEARCH VARIABLES ====================
            let allBuildings = [];

            // ==================== BUILDING USAGE COLORS ====================
            const usageColors = {
                'RESIDENTIAL': '#4CAF50',
                'COMMERCIAL': '#2196F3',
                'INDUSTRIAL': '#FF9800',
                'INSTITUTIONAL': '#9C27B0',
                'MIXED': '#FF5722',
                'GOVERNMENT': '#607D8B',
                'VACANT': '#9E9E9E',
                'default': '#ff4444'
            };

            function getBuildingColor(buildingUsage) {
                if (!buildingUsage) return usageColors.default;
                const upperUsage = buildingUsage.toUpperCase();
                for (const [key, color] of Object.entries(usageColors)) {
                    if (upperUsage.includes(key) || key === upperUsage) {
                        return color;
                    }
                }
                return usageColors.default;
            }

            // ==================== HELPER FUNCTIONS ====================
            function showToast(message, type = 'info') {
                const alertClass = {
                    'success': '#28a745',
                    'error': '#dc3545',
                    'warning': '#ffc107',
                    'info': '#17a2b8'
                } [type] || '#17a2b8';

                const toastId = 'toast_' + Date.now();
                const toastHtml =
                    `<div id="${toastId}" class="toast-message" style="background: ${alertClass}; color: white;">${message}</div>`;
                $('body').append(toastHtml);

                setTimeout(() => {
                    $(`#${toastId}`).fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel, .route-info').removeClass('open');
            }

            function formatDistance(meters) {
                if (!meters || isNaN(meters)) return '0 m';
                if (meters < 1000) return Math.round(meters) + ' m';
                return (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                if (!seconds || isNaN(seconds)) return '0 min';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + ' min';
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return hours + 'h ' + mins + 'm';
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // ==================== QC FUNCTIONS ====================
            function updateAssessmentQC(assessmentId, pointGisid, wardNo, qcData, callback) {
                $.ajax({
                    url: '{{ route('corporation.update.assessment.qc') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        assessment_id: assessmentId,
                        point_gisid: pointGisid,
                        ward_no: wardNo,
                        qc_sqfeet: qcData.qc_sqfeet,
                        qc_usage: qcData.qc_usage,
                        qc_name: qcData.qc_name,
                        qc_remarks: qcData.qc_remarks,
                        tax_amount: qcData.tax_amount,
                        balance: qcData.balance
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('QC updated successfully!', 'success');
                            if (callback) callback(response);
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToast('Error updating QC', 'error');
                    }
                });
            }

            // ==================== ROUTE FUNCTIONS ====================
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    const url =
                        `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        throw new Error('No route found');
                    }
                    return data.routes[0];
                } catch (error) {
                    console.warn('OSRM route failed:', error);
                    const distance = calculateDistance(startCoord, endCoord);
                    return {
                        distance: distance,
                        duration: distance / 8.33,
                        geometry: {
                            type: "LineString",
                            coordinates: [startCoord, endCoord]
                        }
                    };
                }
            }

            function calculateDistance(coord1, coord2) {
                const R = 6371000;
                const lat1 = coord1[1] * Math.PI / 180;
                const lat2 = coord2[1] * Math.PI / 180;
                const deltaLat = (coord2[1] - coord1[1]) * Math.PI / 180;
                const deltaLon = (coord2[0] - coord1[0]) * Math.PI / 180;

                const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
                    Math.cos(lat1) * Math.cos(lat2) *
                    Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker) {
                    showToast('Please enable your location first', 'warning');
                    return;
                }

                $('#loadingSpinner').show();

                try {
                    const currentCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry()
                        .getCoordinates();
                    const currentLonLat = ol.proj.toLonLat(currentCoords);

                    const geometry = feature.getGeometry();
                    let targetCoords;
                    if (geometry.getType() === 'Point') {
                        targetCoords = geometry.getCoordinates();
                    } else {
                        targetCoords = ol.extent.getCenter(geometry.getExtent());
                    }
                    const targetLonLat = ol.proj.toLonLat(targetCoords);

                    const route = await getRouteFromOSRM(currentLonLat, targetLonLat);

                    if (routeLayer) map.removeLayer(routeLayer);
                    if (routeSource) routeSource.clear();

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

                    if (route.geometry && route.geometry.type === 'LineString' && route.geometry.coordinates) {
                        const coordinates = route.geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                        routeSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coordinates)
                        }));
                        map.addLayer(routeLayer);
                    }

                    const gisid = feature.get('gisid');
                    const buildingData = polygonDatas.find(p => p.gisid == gisid);
                    const buildingName = buildingData ? `GIS ID: ${gisid}` : `Building ${gisid}`;

                    $('.route-summary').html(`
                        <div><strong>Total Distance:</strong> ${formatDistance(route.distance)}</div>
                        <div><strong>Estimated Time:</strong> ${formatDuration(route.duration)}</div>
                        <div><strong>Destination:</strong> ${buildingName}</div>
                    `);
                    $('.route-info').addClass('open');
                    showToast('Route calculated successfully!', 'success');

                } catch (error) {
                    console.error('Route calculation error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function clearRoute() {
                if (routeLayer) map.removeLayer(routeLayer);
                if (destinationMarker) map.removeLayer(destinationMarker);
                if (routeSource) routeSource.clear();
                $('.route-info').removeClass('open');
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showToast("Geolocation is not supported", 'error');
                    return;
                }

                $('#locationBtn').addClass('active');
                isLocationEnabled = true;
                $('#centerMyLocationBtn').show();

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                        currentPosition = [pos.coords.longitude, pos.coords.latitude];
                        showToast('Location tracking enabled', 'success');
                    },
                    function(err) {
                        showToast("Please allow location access", 'error');
                        isLocationEnabled = false;
                        $('#locationBtn').removeClass('active');
                        $('#centerMyLocationBtn').hide();
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000,
                        timeout: 15000
                    }
                );

                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = navigator.geolocation.watchPosition(
                    function(pos) {
                        updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    },
                    function(err) {
                        console.warn('Watch position error:', err);
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 5000,
                        timeout: 10000
                    }
                );
            }

            function stopLocationTracking() {
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                if (accuracyCircle) map.removeLayer(accuracyCircle);
                isLocationEnabled = false;
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').hide();
                currentPosition = null;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                const coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                if (accuracyCircle) map.removeLayer(accuracyCircle);

                accuracyCircle = new ol.layer.Vector({
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
                map.addLayer(accuracyCircle);

                currentLocationMarker = new ol.layer.Vector({
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
                map.addLayer(currentLocationMarker);
            }

            function centerToMyLocation() {
                if (currentPosition) {
                    const coords = ol.proj.fromLonLat(currentPosition);
                    map.getView().setCenter(coords);
                    map.getView().setZoom(19);
                    showToast('Centered on your location', 'info');
                } else {
                    showToast('Location not available', 'warning');
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
                        feature: null,
                        assessments: []
                    };

                    if (building.pointdata && building.pointdata.length) {
                        $.each(building.pointdata, function(j, assessment) {
                            info.assessments.push({
                                id: assessment.id,
                                assessment: assessment.assessment,
                                owner_name: assessment.owner_name || assessment
                                    .present_owner_name,
                                phone: assessment.phone_number,
                                bill_usage: assessment.bill_usage,
                                floor: assessment.floor,
                                qcsqfeet: assessment.qcsqfeet,
                                qcusage: assessment.qcusage,
                                qc_name: assessment.qc_name
                            });
                        });
                    }

                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly
                                    .coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0,
                                        cy = 0,
                                        count = 0;
                                    $.each(coords[0], function(k, c) {
                                        if (c && c.length >= 2 && !isNaN(c[0]) && !isNaN(c[
                                                1])) {
                                            cx += c[0];
                                            cy += c[1];
                                            count++;
                                        }
                                    });
                                    if (count > 0) info.coordinates = [cx / count, cy / count];
                                }
                            } catch (e) {
                                console.error("Error parsing coordinates:", e);
                            }
                            return false;
                        }
                    });
                    allBuildings.push(info);
                });
                console.log("Search index built with", allBuildings.length, "buildings");
            }

            // ==================== POLYGON STYLE FUNCTION ====================
            function polygonStyleFunction(feature) {
                const gisid = feature.get('gisid');
                const isVisible = feature.get('visible');
                if (isVisible === false) return null;

                const buildingData = polygonDatas.find(p => p.gisid == gisid);
                const buildingUsage = buildingData ? buildingData.building_usage : null;
                const fillColor = getBuildingColor(buildingUsage);

                const geometry = feature.getGeometry();
                let center;
                try {
                    center = geometry.getInteriorPoint();
                    if (!center) {
                        const extent = geometry.getExtent();
                        center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                    }
                } catch (e) {
                    const extent = geometry.getExtent();
                    center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                }

                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ffffff',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: fillColor,
                            opacity: 0.7
                        })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}`,
                            font: 'bold 10px Arial',
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#000',
                                width: 2
                            }),
                            padding: [2, 4, 2, 4]
                        })
                    })
                ];
            }

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                if (!text || !text.trim()) {
                    $('#searchResults').html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }

                const term = text.toLowerCase().trim();
                const results = [];

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
                            if (a.assessment && a.assessment.toString().toLowerCase().includes(
                                term)) {
                                match = true;
                                type = 'Assessment No';
                                val = a.assessment;
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

                const $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    const lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    const lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${escapeHtml(r.gisid)}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${escapeHtml(r.matchType)}: ${escapeHtml(r.matchValue)}</div>
                        <div class="result-owner"><i class="fas fa-location-dot"></i> ${escapeHtml(r.building.road_name || 'No road')} | ${escapeHtml(r.building.zone || 'No zone')}</div>
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
                    const $item = $(this).closest('.search-result-item');
                    const gisid = $item.data('gisid');
                    const lon = parseFloat($item.data('lon'));
                    const lat = parseFloat($item.data('lat'));

                    if (lon && lat && !isNaN(lon) && !isNaN(lat)) {
                        const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') ==
                            gisid);
                        if (feature) {
                            selectedFeature = feature;
                            calculateAndDisplayRoute(feature);
                            closeAllPanels();
                        } else {
                            showToast("Building not found on map", 'error');
                        }
                    } else {
                        showToast("Coordinates not available", 'error');
                    }
                });
            }

            function zoomToBuilding(gisid) {
                const features = polygonLayer.getSource().getFeatures();
                const feature = features.find(f => f.get('gisid') == gisid);
                if (feature) {
                    map.getView().fit(feature.getGeometry().getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 800
                    });
                    showPopup(gisid, ol.extent.getCenter(feature.getGeometry().getExtent()));
                } else {
                    showToast("Building not found on map", 'error');
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
                $(`.popup-tab[data-tab="${t}"]`).addClass('active');
                currentActiveTab = t;
            };

            function showPopup(gisid, coord) {
                const pd = polygonDatas.find(p => p.gisid == gisid);
                if (!pd) return;

                const assessments = pd.pointdata || [];
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

                const buildingHtml =
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

                const assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) => {
                        const hasQc = a.qcsqfeet || a.qcusage;
                        return `<div class="assessment-card" data-id="${a.id || ''}" data-point-gisid="${a.point_gisid || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i+1)}</span>
                                <span class="badge ${hasQc ? 'badge-success' : 'badge-warning'}">${hasQc ? 'QC Done' : 'QC Pending'}</span>
                            </div>
                            <div class="assessment-body">
                                ${[['Owner', a.owner_name || a.present_owner_name], ['Phone', a.phone_number], ['Floor', a.floor], ['Usage', a.bill_usage], ['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                                ${hasQc ? `<div class="assessment-row"><div class="assessment-label">QC SqFt:</div><div class="assessment-value">${a.qcsqfeet || '-'}</div></div>
                                               <div class="assessment-row"><div class="assessment-label">QC Usage:</div><div class="assessment-value">${a.qcusage || '-'}</div></div>
                                               <div class="assessment-row"><div class="assessment-label">QC By:</div><div class="assessment-value">${a.qc_name || '-'}</div></div>` : ''}
                            </div>
                        </div>`;
                    }).join('');

                const shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' :
                    shops.map(s => `
                        <div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${escapeHtml(s.shop_name || 'Shop')}</div>
                            ${[['Category', s.shop_category], ['Owner', s.shop_owner_name], ['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                        </div>
                    `).join('');

                const html = `
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
                    <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button>
                    </div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coord);

                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') ==
                        gisid);
                    if (feature) {
                        selectedFeature = feature;
                        calculateAndDisplayRoute(feature);
                    }
                });

                $('.assessment-card').off('click').on('click', function(e) {
                    e.stopPropagation();
                    const assessmentId = $(this).data('id');
                    const pointGisid = $(this).data('point-gisid');
                    const $card = $(this);

                    if ($card.find('.assessment-form-container').length) return;

                    $card.after(`
                        <div class="assessment-form-container">
                            <h5 style="color:#28a745; margin-bottom:15px;"><i class="fas fa-clipboard-check"></i> QC Verification Form</h5>
                            <div class="mb-2">
                                <label style="color:#ffc107; font-size:12px;">QC Square Feet:</label>
                                <input type="number" name="qc_sqfeet" class="form-control form-control-sm" placeholder="Enter verified sq. feet" style="background:#0f0f1a; color:white; border:1px solid #28a745;">
                            </div>
                            <div class="mb-2">
                                <label style="color:#ffc107; font-size:12px;">QC Usage:</label>
                                <select name="qc_usage" class="form-control form-control-sm" style="background:#0f0f1a; color:white; border:1px solid #28a745;">
                                    <option value="">Select Usage</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Industrial">Industrial</option>
                                    <option value="Institutional">Institutional</option>
                                    <option value="Mixed">Mixed Use</option>
                                    <option value="Government">Government</option>
                                    <option value="Vacant">Vacant</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label style="color:#ffc107; font-size:12px;">Half Year Tax (₹):</label>
                                <input type="number" name="tax_amount" class="form-control form-control-sm" placeholder="Enter tax amount" style="background:#0f0f1a; color:white; border:1px solid #28a745;">
                            </div>
                            <div class="mb-2">
                                <label style="color:#ffc107; font-size:12px;">Balance (₹):</label>
                                <input type="number" name="balance" class="form-control form-control-sm" placeholder="Enter balance" style="background:#0f0f1a; color:white; border:1px solid #28a745;">
                            </div>
                            <div class="mb-2">
                                <label style="color:#ffc107; font-size:12px;">QC Remarks:</label>
                                <textarea name="qc_remarks" class="form-control form-control-sm" rows="2" placeholder="Additional remarks" style="background:#0f0f1a; color:white; border:1px solid #28a745;"></textarea>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-sm btn-success save-qc-btn" style="flex:1;">✓ Save QC</button>
                                <button class="btn btn-sm btn-secondary cancel-qc-btn" style="flex:1;">✗ Cancel</button>
                            </div>
                        </div>
                    `);

                    $('.save-qc-btn').off('click').on('click', function() {
                        const qcSqfeet = $(this).closest('.assessment-form-container').find(
                            'input[name="qc_sqfeet"]').val();
                        const qcUsage = $(this).closest('.assessment-form-container').find(
                            'select[name="qc_usage"]').val();
                        const taxAmount = $(this).closest('.assessment-form-container').find(
                            'input[name="tax_amount"]').val();
                        const balance = $(this).closest('.assessment-form-container').find(
                            'input[name="balance"]').val();
                        const qcRemarks = $(this).closest('.assessment-form-container').find(
                            'textarea[name="qc_remarks"]').val();

                        updateAssessmentQC(assessmentId, pointGisid, '{{ $ward->ward_no }}', {
                            qc_sqfeet: qcSqfeet,
                            qc_usage: qcUsage,
                            qc_name: '{{ Auth::guard('corporation')->user()->name ?? 'QC User' }}',
                            qc_remarks: qcRemarks,
                            tax_amount: taxAmount,
                            balance: balance
                        }, function(response) {
                            $card.find('.badge').removeClass('badge-warning').addClass(
                                'badge-success').html('QC Done');
                            $('.assessment-form-container').remove();
                            showToast('QC saved successfully!', 'success');
                        });
                    });

                    $('.cancel-qc-btn').off('click').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);

                const ps = new ol.source.Vector();
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

                const ls = new ol.source.Vector();
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
            }

            // ==================== MAP INTERACTIONS ====================
            function setupMapInteractions() {
                map.on('click', function(e) {
                    let clickedFeature = null;
                    map.forEachFeatureAtPixel(e.pixel, function(feature, layer) {
                        if (feature.get('gisid') && layer === polygonLayer) {
                            clickedFeature = feature;
                            return true;
                        }
                    });

                    if (clickedFeature) {
                        const gisid = clickedFeature.get('gisid');
                        selectedFeature = clickedFeature;
                        showPopup(gisid, e.coordinate);
                    } else {
                        $('.ol-popup').hide();
                    }
                });

                map.on('pointermove', function(e) {
                    const hasFeature = map.forEachFeatureAtPixel(e.pixel, function(feature, layer) {
                        return feature.get('gisid') && layer === polygonLayer;
                    });
                    map.getTargetElement().style.cursor = hasFeature ? 'pointer' : '';
                });
            }

            // ==================== MAP INITIALIZATION ====================
            function initMap() {
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

                let boundExt = null;
                if (wardData.boundary && wardData.boundary.length && wardData.boundary[0].length) {
                    try {
                        const bc = wardData.boundary[0].map(c => ol.proj.fromLonLat(c));
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
                        const lons = wardData.boundary[0].map(p => p[0]);
                        const lats = wardData.boundary[0].map(p => p[1]);
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math
                            .max(...lats)
                        ]);
                    } catch (e) {
                        console.error("Error parsing boundary:", e);
                    }
                }

                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                if (boundExt) center = [(boundExt[0] + boundExt[2]) / 2, (boundExt[1] + boundExt[3]) / 2];

                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: center,
                        zoom: 18
                    })
                });

                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);
                if (boundaryLayer) map.addLayer(boundaryLayer);
                refreshLayers();
                setupMapInteractions();

                if (boundExt) map.getView().fit(boundExt, {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });

                // Event Listeners
                $('#menuBtn, #mobileMenuBtn').on('click', function(e) {
                    e.stopPropagation();
                    const isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#layerSwitcher').addClass('open');
                });

                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    const isOpen = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#mapLegend').addClass('open');
                });

                $('#openSearchBtn, #mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    const isOpen = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#searchPanel').addClass('open');
                });

                $('#filterBtn, #mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    const isOpen = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#filterPanel').addClass('open');
                });

                $('#locationBtn, #mobileLocationBtn').on('click', function() {
                    if (isLocationEnabled) stopLocationTracking();
                    else startLocationTracking();
                });

                $('#routeBtn, #mobileRouteBtn').on('click', async function() {
                    if (!selectedFeature) {
                        showToast('Please select a building first', 'warning');
                        $('#openSearchBtn').click();
                        return;
                    }
                    if (!isLocationEnabled) {
                        if (confirm('Enable location for route calculation?')) startLocationTracking();
                        return;
                    }
                    await calculateAndDisplayRoute(selectedFeature);
                });

                $('#centerMyLocationBtn').on('click', centerToMyLocation);
                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));

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

                $('#doSearchBtn').on('click', () => searchBuildings($('#searchInput').val()));
                $('#searchInput').on('keypress', (e) => {
                    if (e.which === 13) searchBuildings($('#searchInput').val());
                });

                $('#applyFilterBtn').on('click', function() {
                    const usage = $('#filterUsage').val();
                    const type = $('#filterType').val();
                    const minF = $('#filterMinFloors').val();
                    const maxF = $('#filterMaxFloors').val();
                    const src = polygonLayer.getSource();
                    const fts = src.getFeatures();
                    let cnt = 0;

                    $.each(fts, function(i, f) {
                        const g = f.get('gisid');
                        const b = polygonDatas.find(p => p.gisid == g);
                        let show = true;

                        if (usage !== 'all' && b) {
                            const bu = (b.building_usage || '').toUpperCase();
                            if (bu !== usage && !bu.includes(usage)) show = false;
                        }
                        if (show && type !== 'all' && b) {
                            let hasQC = false;
                            if (b.pointdata) $.each(b.pointdata, function(k, a) {
                                if (a.qcsqfeet || a.qcusage) {
                                    hasQC = true;
                                    return false;
                                }
                            });
                            if (type === 'completed' && !hasQC) show = false;
                            if (type === 'pending' && hasQC) show = false;
                        }
                        if (show && b && (minF || maxF)) {
                            const fl = parseInt(b.number_floor) || 0;
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
                    $('#filterUsage, #filterType').val('all');
                    $('#filterMinFloors, #filterMaxFloors').val('');
                    const src = polygonLayer.getSource();
                    $.each(src.getFeatures(), (i, f) => f.set('visible', true));
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(
                        `Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    closeAllPanels();
                });

                $('.route-info .panel-close').on('click', function() {
                    clearRoute();
                });

                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest(
                            '.panel, .action-btn, .mobile-nav-btn, #centerMyLocationBtn, .zoom-btn')
                        .length) {
                        closeAllPanels();
                    }
                });
            }

            initMap();
            buildSearchIndex();

            $(window).on('resize', function() {
                setTimeout(() => {
                    if (map) map.updateSize();
                }, 100);
            });
        });
    </script>
@endpush
