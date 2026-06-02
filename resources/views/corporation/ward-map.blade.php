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
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
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

        /* ========== MODERN COLOR COMBO - WARM TEAL & GOLD ========== */
        :root {
            --primary: #1A6B6E;
            --primary-light: #2a8a8e;
            --accent: #D4A13E;
            --accent-dark: #b8872e;
            --danger: #E86A5F;
            --bg-dark: #0B2B40;
            --glass-bg: rgba(11, 43, 64, 0.95);
            --glass-light: rgba(26, 107, 110, 0.9);
            --text-light: #FDF8F0;
            --text-dim: #cbd5e1;
        }

        /* ── Mobile Bottom Nav (Icons Always Visible) ── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px 24px 0 0;
            z-index: 1003;
            padding: 10px 12px;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid var(--accent);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.4);
        }

        .mobile-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 16px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            gap: 4px;
        }

        .mobile-nav-btn i {
            font-size: 22px;
            color: var(--text-dim);
        }

        .mobile-nav-btn span {
            font-size: 11px;
            color: var(--text-dim);
            font-weight: 500;
        }

        .mobile-nav-btn.active i,
        .mobile-nav-btn.active span {
            color: var(--accent);
        }

        .mobile-nav-btn:active {
            transform: scale(0.94);
            background: rgba(212, 161, 62, 0.15);
        }

        /* ── Desktop Action Buttons ── */
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
            gap: 6px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            color: white;
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 10px 18px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
            min-width: 80px;
        }

        .action-btn i {
            font-size: 20px;
            color: var(--accent);
        }

        .btn-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: var(--text-light);
        }

        .action-btn:active {
            transform: scale(0.96);
        }

        .action-btn.location-btn.active {
            background: #28a745;
            border-color: #28a745;
        }

        .action-btn.location-btn.active i {
            color: white;
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

        /* ── Panels (Folder Menu, Legend, etc.) ── */
        .panel {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 20px;
            color: var(--text-light);
            z-index: 1001;
            transition: all 0.3s ease;
            border: 1px solid var(--accent);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            max-height: 80vh;
            overflow-y: auto;
        }

        .panel h5 {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--accent);
            border-bottom: 2px solid var(--danger);
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel h5 i {
            color: var(--accent);
            font-size: 18px;
        }

        .panel-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(232, 106, 95, 0.2);
            border: none;
            color: var(--danger);
            font-size: 20px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            z-index: 10;
        }

        .layer-switcher,
        .map-legend,
        .search-panel,
        .filter-panel,
        .route-info {
            display: none;
        }

        .layer-switcher.open,
        .map-legend.open,
        .search-panel.open,
        .filter-panel.open,
        .route-info.open {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            min-width: 220px;
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

            .layer-switcher,
            .map-legend,
            .search-panel,
            .filter-panel,
            .route-info {
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

        /* ── Layer Switcher - FOLDER MENU WITH VISIBLE ICONS ── */
        .layer-group {
            margin-bottom: 20px;
        }

        .layer-group label {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            margin: 12px 0 !important;
            font-size: 13px !important;
            cursor: pointer !important;
            padding: 8px 12px !important;
            border-radius: 12px !important;
            transition: all 0.2s !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .layer-group label:hover {
            background: rgba(212, 161, 62, 0.2) !important;
        }

        .layer-group label i {
            font-size: 16px !important;
            width: 24px !important;
            color: var(--accent) !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        .group-title {
            font-weight: 700;
            color: var(--accent);
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .group-title i {
            color: var(--accent);
            font-size: 14px;
        }

        /* Radio & Checkbox styling */
        .layer-group input[type="radio"],
        .layer-group input[type="checkbox"] {
            margin-right: 8px;
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        /* ── Legend Panel ── */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .legend-color {
            width: 24px;
            height: 16px;
            border-radius: 4px;
            flex-shrink: 0;
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
            background: var(--danger);
        }

        .legend-color.road {
            background: var(--accent);
            height: 3px;
        }

        .legend-color.boundary {
            background: #ff0000;
            height: 3px;
        }

        /* ── Search Panel ── */
        .search-box {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--accent);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 14px;
            outline: none;
        }

        .search-box button {
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            background: var(--accent);
            color: var(--bg-dark);
            cursor: pointer;
            font-weight: 700;
        }

        .search-results {
            max-height: 350px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            cursor: pointer;
            border-left: 3px solid var(--accent);
        }

        .result-gisid {
            font-weight: bold;
            color: var(--accent);
            font-size: 13px;
            margin-bottom: 5px;
        }

        .result-gisid i {
            margin-right: 6px;
        }

        .direction-btn {
            margin-top: 8px;
            padding: 6px 12px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-size: 11px;
        }

        /* ── Filter Panel ── */
        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group>label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--accent);
            font-weight: 600;
        }

        .filter-group select,
        .filter-group input[type="number"] {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid var(--accent);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 13px;
        }

        .range-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .range-row input {
            flex: 1;
        }

        .filter-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--danger);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .apply-btn {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 10px;
            border: none;
            flex: 1;
            font-weight: 600;
        }

        .reset-btn {
            background: var(--danger);
            color: white;
            padding: 10px;
            border-radius: 10px;
            border: none;
            flex: 1;
            font-weight: 600;
        }

        .filter-count {
            margin-top: 12px;
            font-size: 12px;
            color: var(--accent);
            text-align: center;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        /* ── Route Panel ── */
        .route-summary {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
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
            background: var(--danger);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-start-nav {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        /* ── Zoom Controls ── */
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            z-index: 1000;
            border: 1px solid var(--accent);
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

        /* ── Center Button ── */
        .center-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--accent);
            border-radius: 40px;
            padding: 12px 18px;
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

        /* ── Popup ── */
        .ol-popup {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(135deg, #0B2B40, #1A6B6E);
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
            background: var(--bg-dark);
            padding: 18px 20px;
            border-bottom: 2px solid var(--accent);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-header h4 {
            margin: 0;
            font-size: 18px;
            color: var(--accent);
        }

        .popup-tabs {
            display: flex;
            background: #0a2a3a;
            flex-wrap: wrap;
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
            color: var(--accent);
            border-bottom: 3px solid var(--accent);
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
            color: var(--accent);
            width: 110px;
            font-size: 12px;
        }

        .detail-value {
            color: #eee;
            flex: 1;
            font-size: 13px;
        }

        .badge-success {
            background: #28a745;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
        }

        .badge-warning {
            background: var(--accent);
            color: #333;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
        }

        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 3px solid var(--accent);
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }

        .toast-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            padding: 12px 20px;
            border-radius: 12px;
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

        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 20px;
            z-index: 2000;
            display: none;
            text-align: center;
            color: white;
            gap: 12px;
            flex-direction: column;
            align-items: center;
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
                'RESIDENTIAL': '#4CAF50',
                'COMMERCIAL': '#2196F3',
                'INDUSTRIAL': '#FF9800',
                'INSTITUTIONAL': '#9C27B0',
                'MIXED': '#FF5722',
                'GOVERNMENT': '#607D8B',
                'VACANT': '#9E9E9E',
                'EDUCATIONAL': '#00BCD4',
                'HOSPITAL': '#E91E63',
                'HOTEL': '#795548',
                'RELIGIOUS': '#FFC107',
                'default': '#E86A5F'
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
                return !!(pt.qcsqfeet && String(pt.qcsqfeet).trim() !== '' &&
                    pt.qcusage && String(pt.qcusage).trim() !== '');
            }

            function showToast(message, type = 'info') {
                const colors = {
                    success: '#28a745',
                    error: '#E86A5F',
                    warning: '#D4A13E',
                    info: '#1A6B6E'
                };
                const bg = colors[type] || colors.info;
                const id = 'toast_' + Date.now();
                $('body').append(
                    `<div id="${id}" class="toast-message" style="background:${bg};color:white;">${message}</div>`
                    );
                setTimeout(() => $(`#${id}`).fadeOut(300, function() {
                    $(this).remove();
                }), 3000);
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
                return hrs + 'h ' + (mins % 60) + 'm';
            }

            function escapeHtml(text) {
                if (!text && text !== 0) return '';
                const d = document.createElement('div');
                d.textContent = text;
                return d.innerHTML;
            }

            // ==================== ROUTE FUNCTIONS ====================
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    const url =
                        `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.code !== 'Ok' || !data.routes || !data.routes.length) throw new Error(
                        'No route found');
                    return data.routes[0];
                } catch (error) {
                    const distance = calculateDistance(startCoord, endCoord);
                    return {
                        distance,
                        duration: distance / 8.33,
                        geometry: {
                            type: 'LineString',
                            coordinates: [startCoord, endCoord]
                        },
                        legs: [{
                            steps: [{
                                    maneuver: {
                                        type: 'depart',
                                        instruction: 'Start from your location'
                                    },
                                    distance: distance / 2,
                                    duration: (distance / 8.33) / 2
                                },
                                {
                                    maneuver: {
                                        type: 'arrive',
                                        instruction: 'Arrive at destination'
                                    },
                                    distance: distance / 2,
                                    duration: (distance / 8.33) / 2
                                }
                            ]
                        }]
                    };
                }
            }

            function calculateDistance(c1, c2) {
                const R = 6371000;
                const la1 = c1[1] * Math.PI / 180,
                    la2 = c2[1] * Math.PI / 180;
                const dLa = (c2[1] - c1[1]) * Math.PI / 180;
                const dLo = (c2[0] - c1[0]) * Math.PI / 180;
                const a = Math.sin(dLa / 2) ** 2 + Math.cos(la1) * Math.cos(la2) * Math.sin(dLo / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function drawRouteOnMap(geometry) {
                if (routeLayer) map.removeLayer(routeLayer);
                if (routeSource) routeSource = new ol.source.Vector();
                routeLayer = new ol.layer.Vector({
                    source: routeSource,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#D4A13E',
                            width: 5,
                            lineDash: [10, 8]
                        })
                    })
                });
                if (geometry?.type === 'LineString' && geometry.coordinates) {
                    const coords = geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                    routeSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coords)
                    }));
                    map.addLayer(routeLayer);
                }
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker) {
                    showToast('Please enable your location first', 'warning');
                    return;
                }
                $('#loadingSpinner').css('display', 'flex');
                try {
                    const locCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry()
                        .getCoordinates();
                    const currentLL = ol.proj.toLonLat(locCoords);
                    const geometry = feature.getGeometry();
                    const rawCenter = geometry.getType() === 'Point' ? geometry.getCoordinates() : ol.extent
                        .getCenter(geometry.getExtent());
                    const targetLL = ol.proj.toLonLat(rawCenter);
                    let route = await getRouteFromOSRM(currentLL, targetLL);
                    drawRouteOnMap(route.geometry);
                    const gisid = feature.get('gisid');
                    $('#routeSummary').html(
                        `<div><strong>Total Distance:</strong> ${formatDistance(route.distance)}</div><div><strong>Estimated Time:</strong> ${formatDuration(route.duration)}</div><div><strong>Destination:</strong> GIS ID: ${gisid}</div>`
                        );
                    if (destinationMarker) map.removeLayer(destinationMarker);
                    destinationMarker = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.Feature({
                                geometry: new ol.geom.Point(ol.proj.fromLonLat(
                                    targetLL))
                            })]
                        }),
                        style: new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 14,
                                fill: new ol.style.Fill({
                                    color: '#E86A5F'
                                }),
                                stroke: new ol.style.Stroke({
                                    color: '#fff',
                                    width: 3
                                })
                            })
                        })
                    });
                    map.addLayer(destinationMarker);
                    currentRoute = {
                        startCoord: currentLL,
                        endCoord: targetLL
                    };
                    $('#routeInfo').addClass('open');
                    showToast('Route calculated successfully!', 'success');
                } catch (error) {
                    showToast('Error calculating route', 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function clearRoute() {
                if (routeLayer) map.removeLayer(routeLayer);
                if (destinationMarker) map.removeLayer(destinationMarker);
                if (routeSource) routeSource?.clear();
                currentRoute = null;
                $('#routeInfo').removeClass('open');
            }

            function startNavigation() {
                if (currentRoute?.endCoord) {
                    const [lon, lat] = currentRoute.endCoord;
                    const [slon, slat] = currentRoute.startCoord;
                    window.open(`https://www.google.com/maps/dir/${slat},${slon}/${lat},${lon}`, '_blank');
                }
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showToast('Geolocation not supported', 'error');
                    return;
                }
                $('#locationBtn,#mobileLocationBtn').addClass('active');
                isLocationEnabled = true;
                if (!$('#centerMyLocationBtn').length) {
                    $('body').append(
                        '<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>'
                        );
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
                        showToast('Please allow location access', 'error');
                        isLocationEnabled = false;
                        $('#locationBtn,#mobileLocationBtn').removeClass('active');
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000,
                        timeout: 15000
                    }
                );
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = navigator.geolocation.watchPosition(pos => {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    currentPosition = [pos.coords.longitude, pos.coords.latitude];
                }, err => console.warn(err), {
                    enableHighAccuracy: true
                });
            }

            function stopLocationTracking() {
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                if (currentLocationMarker) map.removeLayer(currentLocationMarker);
                if (accuracyCircle) map.removeLayer(accuracyCircle);
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
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, accuracy)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#D4A13E',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(212,161,62,0.15)'
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
                                color: '#E86A5F'
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
                    map.getView().animate({
                        center: ol.proj.fromLonLat(currentPosition),
                        zoom: 19,
                        duration: 600
                    });
                    showToast('Centered on your location', 'info');
                } else {
                    showToast('Location not available', 'warning');
                    startLocationTracking();
                }
            }

            // ==================== SEARCH ====================
            function buildSearchIndex() {
                allBuildings = polygonDatas.map(building => {
                    const info = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        road_name: building.road_name,
                        zone: building.zone,
                        coordinates: null
                    };
                    for (const poly of polygons) {
                        if (poly.gisid == building.gisid) {
                            try {
                                const c = typeof poly.coordinates === 'string' ? JSON.parse(poly
                                    .coordinates) : poly.coordinates;
                                if (c?.[0]?.[0]) {
                                    let sx = 0,
                                        sy = 0,
                                        n = 0;
                                    for (const pt of c[0]) {
                                        if (pt?.length >= 2 && !isNaN(pt[0]) && !isNaN(pt[1])) {
                                            sx += pt[0];
                                            sy += pt[1];
                                            n++;
                                        }
                                    }
                                    if (n > 0) info.coordinates = [sx / n, sy / n];
                                }
                            } catch (e) {}
                            break;
                        }
                    }
                    return info;
                });
            }

            function searchBuildings(text) {
                if (!text?.trim()) {
                    $('#searchResults').html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }
                const term = text.toLowerCase().trim();
                const results = [];
                for (const b of allBuildings) {
                    if (b.gisid?.toLowerCase().includes(term)) results.push({
                        gisid: b.gisid,
                        matchType: 'GIS ID',
                        matchValue: b.gisid,
                        building: b,
                        coordinates: b.coordinates
                    });
                    else if (b.building_usage?.toLowerCase().includes(term)) results.push({
                        gisid: b.gisid,
                        matchType: 'Building Usage',
                        matchValue: b.building_usage,
                        building: b,
                        coordinates: b.coordinates
                    });
                    else if (b.road_name?.toLowerCase().includes(term)) results.push({
                        gisid: b.gisid,
                        matchType: 'Road Name',
                        matchValue: b.road_name,
                        building: b,
                        coordinates: b.coordinates
                    });
                }
                const $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html(
                        '<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }
                for (const r of results) {
                    $res.append(
                        `<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${r.coordinates?.[0] || ''}" data-lat="${r.coordinates?.[1] || ''}"><div class="result-gisid"><i class="fas fa-building"></i> ${escapeHtml(r.gisid)}</div><div class="result-owner"><i class="fas fa-tag"></i> ${escapeHtml(r.matchType)}: ${escapeHtml(r.matchValue)}</div><button class="direction-btn"><i class="fas fa-directions"></i> Get Directions</button></div>`
                        );
                }
                $('#searchResults').off('click', '.search-result-item').on('click', '.search-result-item', function(
                    e) {
                    if (!$(e.target).hasClass('direction-btn') && !$(e.target).closest('.direction-btn')
                        .length) {
                        zoomToBuilding($(this).data('gisid'));
                        closeAllPanels();
                    }
                });
                $('#searchResults').off('click', '.direction-btn').on('click', '.direction-btn', function(e) {
                    e.stopPropagation();
                    const gisid = $(this).closest('.search-result-item').data('gisid');
                    const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') ==
                        gisid);
                    if (feature) {
                        selectedFeature = feature;
                        calculateAndDisplayRoute(feature);
                        closeAllPanels();
                    } else showToast('Building not found on map', 'error');
                });
            }

            function zoomToBuilding(gisid) {
                const feature = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid);
                if (feature) {
                    map.getView().fit(feature.getGeometry().getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 800
                    });
                    showPopup(gisid, ol.extent.getCenter(feature.getGeometry().getExtent()));
                } else showToast('Building not found', 'error');
            }

            // ==================== POPUP ====================
            function createPopup() {
                popupElement = $('<div>', {
                    class: 'ol-popup'
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
                $(popupElement).hide();
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
                const buildingHtml =
                    `<div class="detail-row"><div class="detail-label"><i class="fas fa-fingerprint"></i> GIS ID:</div><div class="detail-value">${escapeHtml(pd.gisid)}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-building"></i> Usage:</div><div class="detail-value">${escapeHtml(pd.building_usage) || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-layer-group"></i> Floors:</div><div class="detail-value">${escapeHtml(pd.number_floor) || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-road"></i> Road:</div><div class="detail-value">${escapeHtml(pd.road_name) || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-map-pin"></i> Zone:</div><div class="detail-value">${escapeHtml(pd.zone) || 'N/A'}</div></div>`;
                const html =
                    `<div class="popup-header"><h4><i class="fas fa-building"></i> Building Details</h4><button class="popup-close" onclick="closePopup()">&times;</button></div>
                    <div class="popup-tabs"><button class="popup-tab active" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                    <button class="popup-tab" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button></div>
                    <div id="tab-building" class="popup-tab-content active">${buildingHtml}</div>
                    <div id="tab-assessments" class="popup-tab-content"><div style="padding:12px">${assessments.map(a => `<div class="assessment-card" style="padding:12px;margin-bottom:10px;background:rgba(255,255,255,0.05);border-radius:12px;"><strong>${escapeHtml(a.assessment || 'Assessment')}</strong><br>Owner: ${escapeHtml(a.owner_name || a.present_owner_name || 'N/A')}<br>Usage: ${escapeHtml(a.bill_usage || 'N/A')}</div>`).join('') || '<div class="empty-state">No assessments</div>'}</div></div>
                    <div style="padding:16px; border-top:1px solid rgba(255,255,255,0.1);"><button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions</button></div>`;
                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coord);
                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    const f = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid);
                    if (f) {
                        selectedFeature = f;
                        calculateAndDisplayRoute(f);
                    }
                });
            }

            // ==================== POLYGON STYLE ====================
            function polygonStyleFunction(feature) {
                if (feature.get('visible') === false) return null;
                const gisid = feature.get('gisid');
                const buildingData = polygonDatas.find(p => p.gisid == gisid);
                const fillColor = getBuildingColor(buildingData?.building_usage);
                const geometry = feature.getGeometry();
                let center;
                try {
                    center = geometry.getInteriorPoint();
                } catch (e) {
                    const ext = geometry.getExtent();
                    center = new ol.geom.Point([(ext[0] + ext[2]) / 2, (ext[1] + ext[3]) / 2]);
                }
                return [new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ffffff',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: fillColor
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
                            })
                        })
                    })
                ];
            }

            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);
                const ps = new ol.source.Vector();
                for (const p of polygons) {
                    try {
                        const c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (c?.length) ps.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(c),
                            gisid: p.gisid,
                            sqfeet: p.sqfeet,
                            visible: true
                        }));
                    } catch (e) {}
                }
                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFunction,
                    visible: true
                });
                const ls = new ol.source.Vector();
                for (const l of lines) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (c?.length) {
                            if (c.length === 1 && Array.isArray(c[0][0])) c = c[0];
                            ls.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(c),
                                gisid: l.gisid
                            }));
                        }
                    } catch (e) {}
                }
                lineLayer = new ol.layer.Vector({
                    source: ls,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#D4A13E',
                            width: 3
                        })
                    }),
                    visible: true
                });
                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);
            }

            // ==================== MAP INIT ====================
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
                let hasDrone = false;
                const droneImg = wardData.drone_image;
                if (droneImg && droneImg !== 'null' && droneImg !== '') {
                    try {
                        imageLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: droneImg.startsWith('http') ? droneImg : '/' + droneImg
                                    .replace(/^\/+/, ''),
                                imageExtent: [parseFloat(wardData.extent_left), parseFloat(wardData
                                        .extent_bottom), parseFloat(wardData.extent_right),
                                    parseFloat(wardData.extent_top)
                                ],
                                projection: 'EPSG:3857'
                            }),
                            visible: true,
                            opacity: 0.8
                        });
                        hasDrone = true;
                    } catch (e) {}
                }
                let boundExt = null;
                const bound = wardData.boundary;
                if (bound?.length && bound[0]?.length) {
                    try {
                        const bc = bound[0].map(c => ol.proj.fromLonLat(c));
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
                        const lons = bound[0].map(p => p[0]),
                            lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.transformExtent([Math.min(...lons), Math.min(...lats), Math.max(...lons),
                            Math.max(...lats)
                        ], 'EPSG:4326', 'EPSG:3857');
                    } catch (e) {}
                }
                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                if (bound?.[0]?.length) {
                    try {
                        const lons = bound[0].map(p => p[0]),
                            lats = bound[0].map(p => p[1]);
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...
                            lats) + Math.max(...lats)) / 2]);
                    } catch (e) {}
                }
                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center,
                        zoom: 18
                    })
                });
                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);
                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);
                refreshLayers();
                map.on('click', function(e) {
                    let clicked = null;
                    map.forEachFeatureAtPixel(e.pixel, function(feature, layer) {
                        if (feature.get('gisid') && layer === polygonLayer) {
                            clicked = feature;
                            return true;
                        }
                    });
                    if (clicked) {
                        selectedFeature = clicked;
                        showPopup(clicked.get('gisid'), e.coordinate);
                    } else {
                        $(popupElement).hide();
                    }
                });

                // Mobile Bottom Nav
                $('body').append(
                    `<div class="mobile-bottom-nav"><button class="mobile-nav-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i><span>Layers</span></button><button class="mobile-nav-btn" id="mobileSearchBtn"><i class="fas fa-search"></i><span>Search</span></button><button class="mobile-nav-btn" id="mobileLocationBtn"><i class="fas fa-location-dot"></i><span>Location</span></button><button class="mobile-nav-btn" id="mobileRouteBtn"><i class="fas fa-route"></i><span>Route</span></button><button class="mobile-nav-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i><span>Filter</span></button></div>`
                    );
                $('body').append(
                    `<div class="layer-switcher panel" id="layerSwitcher"><button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button><h5><i class="fas fa-layer-group"></i> Layers</h5><div class="layer-group"><div class="group-title"><i class="fas fa-map"></i> Base Maps</div><label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label><label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label></div><div class="layer-group"><div class="group-title"><i class="fas fa-overlay"></i> Overlays</div><label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label><label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label><label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}</div></div>`
                    );
                $('body').append(
                    `<div class="map-legend panel" id="mapLegend"><button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button><h5><i class="fas fa-info-circle"></i> Legend</h5><div class="legend-item"><div class="legend-color residential"></div><span>Residential</span></div><div class="legend-item"><div class="legend-color commercial"></div><span>Commercial</span></div><div class="legend-item"><div class="legend-color industrial"></div><span>Industrial</span></div><div class="legend-item"><div class="legend-color mixed"></div><span>Mixed Use</span></div><div class="legend-item"><div class="legend-color government"></div><span>Government</span></div><div class="legend-item"><div class="legend-color vacant"></div><span>Vacant</span></div><div class="legend-item"><div class="legend-color default"></div><span>Other</span></div><div style="border-top:1px solid rgba(255,255,255,0.1);margin:10px 0;"></div><div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div><div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div></div>`
                    );
                $('body').append(
                    `<div class="search-panel panel" id="searchPanel"><button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button><h5><i class="fas fa-search"></i> Search Building</h5><div class="search-box"><input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment..."><button id="doSearchBtn"><i class="fas fa-search"></i></button></div><div id="searchResults" class="search-results"><div class="empty-state"><i class="fas fa-search"></i><p>Enter a search term above</p></div></div></div>`
                    );
                $('body').append(
                    `<div class="filter-panel panel" id="filterPanel"><button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button><h5><i class="fas fa-filter"></i> Filter Buildings</h5><div class="filter-group"><label>Building Usage</label><select id="filterUsage"><option value="all">All Buildings</option><option value="RESIDENTIAL">Residential</option><option value="COMMERCIAL">Commercial</option><option value="INDUSTRIAL">Industrial</option><option value="MIXED">Mixed Use</option><option value="GOVERNMENT">Government</option><option value="VACANT">Vacant</option></select></div><div class="filter-actions"><button class="apply-btn" id="applyFilterBtn"><i class="fas fa-check"></i> Apply</button><button class="reset-btn" id="resetFilterBtn"><i class="fas fa-redo"></i> Reset</button></div><div class="filter-count" id="filterCount"></div></div>`
                    );
                $('body').append(
                    `<div class="zoom-controls"><button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button><button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button></div>`
                    );

                // Event Listeners
                $('#menuBtn,#mobileMenuBtn').on('click', function(e) {
                    e.stopPropagation();
                    const was = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!was) $('#layerSwitcher').addClass('open');
                });
                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    const was = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!was) $('#mapLegend').addClass('open');
                });
                $('#openSearchBtn,#mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    const was = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!was) {
                        $('#searchPanel').addClass('open');
                        setTimeout(() => $('#searchInput').focus(), 300);
                    }
                });
                $('#filterBtn,#mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    const was = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!was) $('#filterPanel').addClass('open');
                });
                $('#locationBtn,#mobileLocationBtn').on('click', function() {
                    isLocationEnabled ? (stopLocationTracking(), clearRoute()) : startLocationTracking();
                });
                $('#routeBtn,#mobileRouteBtn').on('click', async function() {
                    if (!selectedFeature) {
                        showToast('Please select a building first', 'warning');
                        $('#openSearchBtn').click();
                        return;
                    }
                    if (!isLocationEnabled && confirm('Enable location for route calculation?')) {
                        startLocationTracking();
                        setTimeout(() => {
                            if (currentLocationMarker) calculateAndDisplayRoute(
                            selectedFeature);
                        }, 2000);
                        return;
                    }
                    await calculateAndDisplayRoute(selectedFeature);
                });
                $('body').on('change', 'input[name="baseLayer"]', function() {
                    currentBaseLayer = $(this).val();
                    osmLayer.setVisible(currentBaseLayer === 'osm');
                    satelliteLayer.setVisible(currentBaseLayer === 'satellite');
                });
                $('body').on('change', '#toggleBuildings', function() {
                    if (polygonLayer) polygonLayer.setVisible($(this).is(':checked'));
                });
                $('body').on('change', '#toggleRoads', function() {
                    if (lineLayer) lineLayer.setVisible($(this).is(':checked'));
                });
                $('body').on('change', '#toggleBoundary', function() {
                    if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked'));
                });
                if (hasDrone) $('body').on('change', '#toggleDrone', function() {
                    if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                });
                $('#doSearchBtn').on('click', () => searchBuildings($('#searchInput').val()));
                $('#applyFilterBtn').on('click', function() {
                    const usage = $('#filterUsage').val();
                    const src = polygonLayer.getSource();
                    let cnt = 0;
                    src.getFeatures().forEach(function(feature) {
                        const gisid = feature.get('gisid');
                        const b = polygonDatas.find(p => p.gisid == gisid);
                        let show = true;
                        if (show && usage !== 'all') {
                            const bu = (b?.building_usage || '').toUpperCase();
                            if (bu !== usage && !bu.includes(usage)) show = false;
                        }
                        feature.set('visible', show);
                        if (show) cnt++;
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').html(
                        `<i class="fas fa-building"></i> Showing <strong>${cnt}</strong> of <strong>${src.getFeatures().length}</strong> buildings`
                        );
                    closeAllPanels();
                    showToast(`Filter applied: ${cnt} buildings shown`, 'info');
                });
                $('#resetFilterBtn').on('click', function() {
                    $('#filterUsage').val('all');
                    const src = polygonLayer.getSource();
                    src.getFeatures().forEach(f => f.set('visible', true));
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').html(
                        `<i class="fas fa-building"></i> Showing <strong>${src.getFeatures().length}</strong> of <strong>${src.getFeatures().length}</strong> buildings`
                        );
                    closeAllPanels();
                    showToast('Filters reset', 'info');
                });
                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));
                $('#closeRouteInfo').on('click', clearRoute);
                $('#startNavigationBtn').on('click', startNavigation);
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length && !$(e.target).closest('.action-btn')
                        .length && !$(e.target).closest('.mobile-nav-btn').length && !$(e.target).closest(
                            '#centerMyLocationBtn').length && !$(e.target).closest('.zoom-btn').length) {
                        closeAllPanels();
                    }
                });
                if (boundExt) {
                    setTimeout(() => map.getView().fit(boundExt, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    }), 500);
                }
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
