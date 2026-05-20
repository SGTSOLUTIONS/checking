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
    <div class="route-info panel" id="routeInfo" style="display: none;">
        <button class="panel-close" id="closeRouteInfo">&times;</button>
        <h5><i class="fas fa-route"></i> Route Information</h5>
        <div id="routeSummary" class="route-summary"></div>
        <div id="directionsList" class="directions-list"></div>
        <button class="btn-start-nav" id="startNavigationBtn">
            <i class="fas fa-directions"></i> Open in Google Maps
        </button>
    </div>

    <div class="loading-spinner" id="loadingSpinner" style="display: none;">
        <div class="spinner-border text-primary"></div>
        <div>Calculating route...</div>
    </div>
@endsection

@push('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
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

        .action-btn i { font-size: 18px; }
        .btn-label { font-size: 10px; font-weight: 500; }
        .action-btn:active { transform: scale(0.95); }
        .action-btn.menu-btn { background: rgba(0, 0, 0, 0.85); }
        .action-btn.legend-btn { background: rgba(255, 193, 7, 0.9); }
        .action-btn.search-btn { background: rgba(23, 162, 184, 0.9); }
        .action-btn.filter-btn { background: rgba(40, 167, 69, 0.9); }
        .action-btn.location-btn { background: rgba(220, 53, 69, 0.9); }
        .action-btn.route-btn { background: rgba(111, 66, 193, 0.9); }
        .action-btn.location-btn.active { background: #28a745; }

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
            .btn-label { font-size: 12px; }
        }

        /* Hide desktop buttons on mobile */
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
            min-width: 200px;
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
            display: none !important;
            max-height: 500px;
        }

        .route-info.open {
            display: block !important;
            animation: fadeIn 0.3s ease;
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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

        .step-content { flex: 1; }
        .step-instruction { margin-bottom: 4px; }
        .step-distance { font-size: 11px; color: #ffc107; }

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

        .btn-start-nav:active { transform: scale(0.98); }

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
            text-align: center;
            color: white;
            gap: 12px;
            flex-direction: column;
            align-items: center;
        }

        .loading-spinner .spinner-border { width: 40px; height: 40px; }

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
            .center-btn { bottom: auto; top: 160px; left: 20px; }
        }

        .layer-group { margin-bottom: 15px; }
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
        .layer-group label:hover { background: rgba(255, 255, 255, 0.1); }
        .group-title { font-weight: 600; color: #ffc107; font-size: 12px; margin-bottom: 8px; text-transform: uppercase; }

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
        .legend-color.residential { background: #4CAF50; }
        .legend-color.commercial { background: #2196F3; }
        .legend-color.industrial { background: #FF9800; }
        .legend-color.institutional { background: #9C27B0; }
        .legend-color.mixed { background: #FF5722; }
        .legend-color.government { background: #607D8B; }
        .legend-color.vacant { background: #9E9E9E; }
        .legend-color.default { background: #ff4444; }

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

        .search-results { max-height: 350px; overflow-y: auto; }
        .search-result-item {
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            cursor: pointer;
            border-left: 3px solid #ffc107;
        }
        .result-gisid { font-weight: bold; color: #ffc107; font-size: 13px; margin-bottom: 5px; }
        .result-owner { font-size: 11px; color: #ddd; margin: 3px 0; }
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

        .filter-group { margin-bottom: 15px; }
        .filter-group label { display: block; margin-bottom: 6px; font-size: 12px; color: #ffc107; }
        .filter-group select, .filter-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
        }
        .filter-actions { display: flex; gap: 10px; margin-top: 15px; }
        .apply-btn, .reset-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
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
        .popup-tabs { display: flex; background: #141424; flex-wrap: wrap; }
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
        .popup-tab.active { color: #ff4444; border-bottom: 3px solid #ff4444; }
        .popup-tab-content { display: none; padding: 20px; max-height: 55vh; overflow-y: auto; }
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
            display: inline-block;
            padding: 3px 10px;
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
        }
        .assessment-header {
            background: rgba(255, 193, 7, 0.15);
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .assessment-number { font-weight: 700; font-size: 13px; color: #ffc107; }
        .assessment-body { padding: 12px 15px; }
        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 12px;
            flex-wrap: wrap;
        }
        .assessment-label { width: 80px; color: #aaa; }
        .assessment-value { color: #fff; flex: 1; }
        .shop-item {
            background: rgba(255, 68, 68, 0.1);
            border-radius: 10px;
            padding: 12px;
            margin-top: 8px;
        }
        .shop-name { font-weight: 700; color: #ff4444; font-size: 13px; margin-bottom: 8px; }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }
        .empty-state i { font-size: 50px; margin-bottom: 15px; opacity: 0.5; }
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

            console.log('Ward Data:', wardData);
            console.log('Polygons count:', polygons.length);
            console.log('Lines count:', lines.length);
            console.log('Polygon Datas count:', polygonDatas.length);

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== LOCATION VARIABLES ====================
            let currentLocationLayer = null;
            let accuracyLayer = null;
            let currentPosition = null;
            let locationTracking = false;
            let watchId = null;

            // ==================== ROUTE VARIABLES ====================
            let currentRoute = null;
            let routeSteps = [];
            let routeSource = null;
            let routeLayer = null;
            let destinationMarker = null;
            let selectedBuilding = null;

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
                'EDUCATIONAL': '#00BCD4',
                'HOSPITAL': '#E91E63',
                'HOTEL': '#795548',
                'RELIGIOUS': '#FFC107',
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

            function showToast(message, type = 'info') {
                const alertClass = {
                    'success': '#28a745',
                    'error': '#dc3545',
                    'warning': '#ffc107',
                    'info': '#17a2b8'
                } [type] || '#17a2b8';

                const flashHtml = $('<div>', {
                    class: 'alert alert-dismissible fade show position-fixed',
                    css: {
                        top: '20px',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        zIndex: 10000,
                        background: alertClass,
                        color: 'white',
                        padding: '12px 20px',
                        borderRadius: '10px',
                        minWidth: '200px',
                        textAlign: 'center'
                    },
                    html: `${message}<button type="button" class="btn-close btn-close-white" style="float: right; margin-left: 10px;" data-bs-dismiss="alert"></button>`
                });
                $('body').append(flashHtml);
                setTimeout(() => {
                    flashHtml.fadeOut(300, function() { $(this).remove(); });
                }, 3000);
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel, #routeInfo').removeClass('open');
            }

            // ==================== FORMATTING FUNCTIONS ====================
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

            // ==================== ROUTE FUNCTIONS ====================
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    const [startLon, startLat] = startCoord;
                    const [endLon, endLat] = endCoord;
                    const url = `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true`;

                    console.log('Fetching route from OSRM:', url);
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        throw new Error('No route found');
                    }
                    return data.routes[0];
                } catch (error) {
                    console.warn('OSRM route failed:', error);
                    return getStraightLineRoute(startCoord, endCoord);
                }
            }

            function getStraightLineRoute(startCoord, endCoord) {
                const start = ol.proj.fromLonLat(startCoord);
                const end = ol.proj.fromLonLat(endCoord);
                const distance = ol.sphere.getDistance(start, end);
                const duration = (distance / 1000 / 30) * 60;

                return {
                    distance: distance,
                    duration: duration,
                    geometry: {
                        type: "LineString",
                        coordinates: [startCoord, endCoord]
                    },
                    legs: [{
                        steps: [{
                            maneuver: { type: "depart", instruction: "Start from your location" },
                            distance: distance,
                            duration: duration
                        }, {
                            maneuver: { type: "arrive", instruction: "Arrive at destination" },
                            distance: 0,
                            duration: 0
                        }]
                    }]
                };
            }

            function parseOSRMSteps(route) {
                const steps = [];
                if (route.legs && route.legs[0] && route.legs[0].steps) {
                    route.legs[0].steps.forEach((step, idx) => {
                        let instruction = step.maneuver.instruction || step.maneuver.type;
                        let icon = 'fas fa-arrow-right';
                        switch (step.maneuver.type) {
                            case 'depart': icon = 'fas fa-play'; break;
                            case 'arrive': icon = 'fas fa-flag-checkered'; break;
                            case 'turn':
                                if (step.maneuver.modifier === 'left') icon = 'fas fa-arrow-left';
                                else if (step.maneuver.modifier === 'right') icon = 'fas fa-arrow-right';
                                else icon = 'fas fa-turn-up';
                                break;
                        }
                        steps.push({
                            instruction: instruction,
                            distance: formatDistance(step.distance),
                            icon: icon,
                            type: step.maneuver.type
                        });
                    });
                }
                return steps;
            }

            function drawRouteOnMap(geometry) {
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                if (routeSource) {
                    routeSource.clear();
                }

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

                if (geometry && geometry.type === 'LineString' && geometry.coordinates) {
                    const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                    routeSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coordinates)
                    }));
                    map.addLayer(routeLayer);

                    if (routeSource.getFeatures().length > 0) {
                        const extent = routeSource.getExtent();
                        if (extent && extent[0] !== Infinity) {
                            map.getView().fit(extent, {
                                padding: [80, 80, 80, 80],
                                duration: 800,
                                maxZoom: 18
                            });
                        }
                    }
                }
            }

            async function calculateAndDisplayRoute(startCoord, endCoord, destinationName, buildingGisid = null) {
                $('#loadingSpinner').css('display', 'flex');

                try {
                    if (!endCoord || endCoord.length < 2) {
                        throw new Error('Invalid destination coordinates');
                    }

                    let endLon = parseFloat(endCoord[0]);
                    let endLat = parseFloat(endCoord[1]);
                    let startLon = parseFloat(startCoord[0]);
                    let startLat = parseFloat(startCoord[1]);

                    if (isNaN(endLon) || isNaN(endLat) || isNaN(startLon) || isNaN(startLat)) {
                        throw new Error('Invalid coordinate values');
                    }

                    const startGeographic = [startLon, startLat];
                    const endGeographic = [endLon, endLat];

                    console.log("Route from:", startGeographic, "to:", endGeographic);

                    const route = await getRouteFromOSRM(startGeographic, endGeographic);
                    const totalDistance = route.distance;
                    const totalDuration = route.duration;

                    routeSteps = parseOSRMSteps(route);
                    currentRoute = {
                        distance: totalDistance,
                        duration: totalDuration,
                        geometry: route.geometry,
                        endCoord: endGeographic,
                        placeName: destinationName,
                        gisid: buildingGisid
                    };

                    drawRouteOnMap(route.geometry);

                    $('#routeSummary').html(`
                        <div><strong>Total Distance:</strong> ${formatDistance(totalDistance)}</div>
                        <div><strong>Estimated Time:</strong> ${formatDuration(totalDuration)}</div>
                        <div><strong>Destination:</strong> ${destinationName}</div>
                    `);

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

                    // Add destination marker
                    if (destinationMarker) {
                        map.removeLayer(destinationMarker);
                        destinationMarker = null;
                    }

                    const destProjected = ol.proj.fromLonLat(endGeographic);
                    const destLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.Feature({
                                geometry: new ol.geom.Point(destProjected)
                            })]
                        }),
                        style: new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 14,
                                fill: new ol.style.Fill({ color: '#ff4444' }),
                                stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
                            })
                        })
                    });
                    map.addLayer(destLayer);
                    destinationMarker = destLayer;

                    $('#routeInfo').addClass('open');
                    showToast('Route calculated successfully!', 'success');

                } catch (error) {
                    console.error('Route calculation error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function getRouteToBuilding(gisid, targetCoords) {
                console.log('getRouteToBuilding called with:', gisid, targetCoords);

                if (!targetCoords || targetCoords.length < 2) {
                    showToast('Invalid building coordinates', 'error');
                    return;
                }

                let lon = parseFloat(targetCoords[0]);
                let lat = parseFloat(targetCoords[1]);

                if (isNaN(lon) || isNaN(lat)) {
                    showToast('Invalid coordinate values', 'error');
                    return;
                }

                if (!currentPosition) {
                    showToast('Please enable your location first by clicking the Location button', 'warning');
                    return;
                }

                console.log("Getting route to building:", gisid, "at coordinates:", lon, lat);
                calculateAndDisplayRoute(currentPosition, [lon, lat], `GIS ID: ${gisid}`, gisid);
            }

            function clearRoute() {
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }
                if (routeSource) {
                    routeSource.clear();
                }
                currentRoute = null;
                $('#routeInfo').removeClass('open');
            }

            function startNavigation() {
                if (currentRoute && currentRoute.endCoord) {
                    const [lon, lat] = currentRoute.endCoord;
                    const url = `https://www.google.com/maps/dir/${currentPosition[1]},${currentPosition[0]}/${lat},${lon}`;
                    window.open(url, '_blank');
                } else {
                    showToast('No route available for navigation', 'warning');
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

                    if (building.pointdata && building.pointdata.length) {
                        $.each(building.pointdata, function(j, assessment) {
                            info.assessments.push({
                                id: assessment.id,
                                assessment: assessment.assessment,
                                owner_name: assessment.owner_name || assessment.present_owner_name,
                                phone: assessment.phone || assessment.phone_number,
                                bill_usage: assessment.bill_usage,
                                floor: assessment.floor,
                                qcsqfeet: assessment.qcsqfeet,
                                qcusage: assessment.qcusage
                            });
                        });
                    }

                    // Find coordinates from polygons
                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0, cy = 0, count = 0;
                                    $.each(coords[0], function(k, c) {
                                        if (c && c.length >= 2 && !isNaN(c[0]) && !isNaN(c[1])) {
                                            cx += c[0];
                                            cy += c[1];
                                            count++;
                                        }
                                    });
                                    if (count > 0) {
                                        info.coordinates = [cx / count, cy / count];
                                    }
                                }
                            } catch (e) {
                                console.error("Error parsing coordinates for building:", building.gisid, e);
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
                let gisid = feature.get('gisid');
                let sqfeet = feature.get('sqfeet');
                let geometry = feature.getGeometry();

                let buildingData = polygonDatas.find(p => p.gisid == gisid);
                let buildingUsage = buildingData ? buildingData.building_usage : null;
                let fillColor = getBuildingColor(buildingUsage);

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
                            fill: new ol.style.Fill({ color: '#fff' }),
                            stroke: new ol.style.Stroke({ color: '#000', width: 2 }),
                            padding: [2, 4, 2, 4]
                        })
                    })
                ];
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showToast("Geolocation not supported", 'error');
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
                    console.log('Location obtained:', pos.coords.longitude, pos.coords.latitude);
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    currentPosition = [pos.coords.longitude, pos.coords.latitude];
                    showToast('Location obtained successfully!', 'success');
                }, function(err) {
                    console.error('Geolocation error:', err);
                    let message = "Unable to get location";
                    switch(err.code) {
                        case err.PERMISSION_DENIED:
                            message = "Location permission denied. Please enable location access.";
                            break;
                        case err.POSITION_UNAVAILABLE:
                            message = "Location information unavailable";
                            break;
                        case err.TIMEOUT:
                            message = "Location request timed out";
                            break;
                    }
                    showToast(message, 'error');
                    locationTracking = false;
                    $('#locationBtn').removeClass('active');
                    $('#centerMyLocationBtn').remove();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });

                watchId = navigator.geolocation.watchPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    currentPosition = [pos.coords.longitude, pos.coords.latitude];
                }, function(err) {
                    console.error('Watch position error:', err);
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 5000,
                    timeout: 10000
                });
            }

            function stopLocationTracking() {
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (currentLocationLayer) {
                    map.removeLayer(currentLocationLayer);
                    currentLocationLayer = null;
                }
                if (accuracyLayer) {
                    map.removeLayer(accuracyLayer);
                    accuracyLayer = null;
                }
                locationTracking = false;
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentPosition = null;
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                if (currentLocationLayer) {
                    map.removeLayer(currentLocationLayer);
                }
                if (accuracyLayer) {
                    map.removeLayer(accuracyLayer);
                }

                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, accuracy)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ff4444', width: 2 }),
                        fill: new ol.style.Fill({ color: 'rgba(255,68,68,0.15)' })
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
                            fill: new ol.style.Fill({ color: '#ff4444' }),
                            stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
                        })
                    })
                });
                map.addLayer(currentLocationLayer);

                // Center map on first location
                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            function centerToMyLocation() {
                if (currentPosition) {
                    let coords = ol.proj.fromLonLat(currentPosition);
                    map.getView().setCenter(coords);
                    map.getView().setZoom(19);
                    showToast('Centered on your location', 'info');
                } else {
                    showToast('Location not available. Please enable location tracking first.', 'warning');
                    startLocationTracking();
                }
            }

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                if (!text || !text.trim()) {
                    $('#searchResults').html('<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }

                let term = text.toLowerCase().trim();
                let results = [];

                $.each(allBuildings, function(i, b) {
                    let match = false, type = '', val = '';

                    if (b.gisid && b.gisid.toLowerCase().includes(term)) {
                        match = true; type = 'GIS ID'; val = b.gisid;
                    } else if (b.building_usage && b.building_usage.toLowerCase().includes(term)) {
                        match = true; type = 'Building Usage'; val = b.building_usage;
                    } else if (b.road_name && b.road_name.toLowerCase().includes(term)) {
                        match = true; type = 'Road Name'; val = b.road_name;
                    } else if (b.zone && b.zone.toLowerCase().includes(term)) {
                        match = true; type = 'Zone'; val = b.zone;
                    } else {
                        $.each(b.assessments, function(j, a) {
                            if (a.assessment && a.assessment.toString().toLowerCase().includes(term)) {
                                match = true; type = 'Assessment No'; val = a.assessment;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                match = true; type = 'Owner Name'; val = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                match = true; type = 'Phone'; val = a.phone;
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
                    $res.html('<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    let lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    let lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${r.gisid}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${r.matchType}: ${escapeHtml(r.matchValue)}</div>
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
                    let lon = parseFloat(p.data('lon'));
                    let lat = parseFloat(p.data('lat'));

                    if (lon && lat && !isNaN(lon) && !isNaN(lat) && lon !== 0 && lat !== 0) {
                        selectedBuilding = {
                            gisid: p.data('gisid'),
                            coords: [lon, lat]
                        };
                        getRouteToBuilding(p.data('gisid'), [lon, lat]);
                        closeAllPanels();
                    } else {
                        showToast("Coordinates not available for this building", 'error');
                    }
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
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
                    map.getView().fit(e, { padding: [50, 50, 50, 50], duration: 800 });
                    showPopup(gisid, ol.extent.getCenter(e));
                } else {
                    showToast("Building not found on map", 'error');
                }
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', { class: 'ol-popup', style: 'display:none' })[0];
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
                            shops.push({ ...s, assessmentNumber: a.assessment || 'Bill ' + (i + 1) });
                        });
                    }
                });

                let buildingHtml = `<div class="building-details-content">${[
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
                    <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button>
                    </div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) {
                    popupOverlay.setPosition(coord);
                }

                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    getRouteToBuilding(pd.gisid, [coord[0], coord[1]]);
                });

                $('.assessment-card').off('click').on('click', function() {
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
                            $badge.removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check-circle"></i> QC Complete');
                        } else {
                            $badge.removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-clock"></i> QC Pending');
                        }
                        showToast('QC Saved! Status: ' + (hasValues ? 'QC Complete' : 'QC Pending'), 'info');
                        $('.assessment-form-container').remove();
                    });

                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                console.log('Refreshing layers...');

                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);

                let ps = new ol.source.Vector();
                let polygonCount = 0;

                $.each(polygons, function(i, p) {
                    try {
                        let c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (c && c.length) {
                            // Ensure polygon is valid
                            if (c[0] && c[0].length >= 3) {
                                ps.addFeature(new ol.Feature({
                                    geometry: new ol.geom.Polygon(c),
                                    gisid: p.gisid,
                                    sqfeet: p.sqfeet,
                                    visible: true
                                }));
                                polygonCount++;
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing polygon:", e);
                    }
                });

                console.log('Added', polygonCount, 'polygon features');

                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFunction,
                    visible: true
                });

                let ls = new ol.source.Vector();
                let lineCount = 0;

                $.each(lines, function(i, l) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (c && c.length) {
                            if (c.length === 1 && Array.isArray(c[0][0])) {
                                c = c[0];
                            }
                            if (c.length >= 2) {
                                ls.addFeature(new ol.Feature({
                                    geometry: new ol.geom.LineString(c),
                                    gisid: l.gisid,
                                    road_name: l.road_name
                                }));
                                lineCount++;
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing line:", e);
                    }
                });

                console.log('Added', lineCount, 'line features');

                lineLayer = new ol.layer.Vector({
                    source: ls,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ffc107', width: 3 })
                    }),
                    visible: true
                });

                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);

                // Bring boundary and drone layers to front
                if (boundaryLayer) boundaryLayer.setZIndex(100);
                if (imageLayer) imageLayer.setZIndex(90);
                polygonLayer.setZIndex(50);
                lineLayer.setZIndex(40);

                // Add click handler for popups
                map.on('click', function(e) {
                    let feature = map.forEachFeatureAtPixel(e.pixel, function(f) { return f; });
                    if (feature && feature.get('gisid')) {
                        showPopup(feature.get('gisid'), e.coordinate);
                    } else if (popupElement) {
                        $(popupElement).hide();
                    }
                });

                map.on('pointermove', function(e) {
                    let hasFeature = map.forEachFeatureAtPixel(e.pixel, function(f) { return f; });
                    $('#map').css('cursor', hasFeature ? 'pointer' : '');
                });

                showLoading(false);
            }

            // ==================== MAP INITIALIZATION ====================
            function initMap() {
                showLoading(true);

                console.log('Initializing map...');

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
                        console.log('Drone image loaded');
                    } catch (e) {
                        console.error("Error loading drone image:", e);
                    }
                }

                let bound = wardData.boundary;
                let boundExt = null;

                if (bound && bound.length && bound[0] && bound[0].length) {
                    try {
                        let bc = bound[0].map(c => ol.proj.fromLonLat(c));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([bc])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({ color: '#ff0000', width: 3, lineDash: [10, 5] }),
                                fill: new ol.style.Fill({ color: 'rgba(255,0,0,0.05)' })
                            }),
                            visible: true
                        });
                        let lons = bound[0].map(p => p[0]);
                        let lats = bound[0].map(p => p[1]);
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math.max(...lats)]);
                        console.log('Boundary layer added');
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
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]);
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

                console.log('Map created');

                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);

                if (imageLayer) {
                    map.addLayer(imageLayer);
                    console.log('Drone layer added');
                }
                if (boundaryLayer) {
                    map.addLayer(boundaryLayer);
                    console.log('Boundary layer added');
                }

                setTimeout(() => {
                    if (boundExt) {
                        map.getView().fit(boundExt, { padding: [50, 50, 50, 50], duration: 1000 });
                    }
                }, 500);

                // Add Mobile Bottom Navigation
                if ($('.mobile-bottom-nav').length === 0) {
                    $('body').append(`
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
                    `);
                }

                // Add panels to DOM if they don't exist
                if ($('#layerSwitcher').length === 0) {
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
                }

                if ($('#mapLegend').length === 0) {
                    $('body').append(`
                        <div class="map-legend panel" id="mapLegend">
                            <button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-info-circle"></i> Legend</h5>
                            <div class="legend-item"><div class="legend-color residential"></div><span>Residential</span></div>
                            <div class="legend-item"><div class="legend-color commercial"></div><span>Commercial</span></div>
                            <div class="legend-item"><div class="legend-color industrial"></div><span>Industrial</span></div>
                            <div class="legend-item"><div class="legend-color institutional"></div><span>Institutional</span></div>
                            <div class="legend-item"><div class="legend-color mixed"></div><span>Mixed Use</span></div>
                            <div class="legend-item"><div class="legend-color government"></div><span>Government</span></div>
                            <div class="legend-item"><div class="legend-color vacant"></div><span>Vacant</span></div>
                            <div class="legend-item"><div class="legend-color default"></div><span>Other/Unknown</span></div>
                            <div class="legend-item mt-2"><div class="legend-color" style="background:#ffc107;"></div><span>Roads</span></div>
                            <div class="legend-item"><div class="legend-color" style="background:#ff0000;"></div><span>Ward Boundary</span></div>
                        </div>
                    `);
                }

                if ($('#searchPanel').length === 0) {
                    $('body').append(`
                        <div class="search-panel panel" id="searchPanel">
                            <button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-search"></i> Search Building</h5>
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment...">
                                <button id="doSearchBtn"><i class="fas fa-search"></i> Go</button>
                            </div>
                            <div id="searchResults" class="search-results"></div>
                        </div>
                    `);
                }

                if ($('#filterPanel').length === 0) {
                    $('body').append(`
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
                    `);
                }

                if ($('.zoom-controls').length === 0) {
                    $('body').append(`
                        <div class="zoom-controls">
                            <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                            <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                        </div>
                    `);
                }

                // Desktop button handlers
                $('#menuBtn, #mobileMenuBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#layerSwitcher').addClass('open');
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#mapLegend').addClass('open');
                });

                $('#openSearchBtn, #mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#searchPanel').addClass('open');
                        setTimeout(() => $('#searchInput').focus(), 300);
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#filterBtn, #mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#filterPanel').addClass('open');
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#locationBtn, #mobileLocationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                        clearRoute();
                    } else {
                        startLocationTracking();
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                    setTimeout(() => $(this).removeClass('active'), 500);
                });

                $('#routeBtn, #mobileRouteBtn').on('click', function() {
                    if (selectedBuilding) {
                        getRouteToBuilding(selectedBuilding.gisid, selectedBuilding.coords);
                    } else {
                        showToast('Please search and select a building first', 'warning');
                        $('#openSearchBtn').click();
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                    setTimeout(() => $(this).removeClass('active'), 500);
                });

                // Layer toggles
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

                // Search handlers
                $('#doSearchBtn').on('click', function() {
                    searchBuildings($('#searchInput').val());
                });

                $('#searchInput').on('keypress', function(e) {
                    if (e.which === 13) {
                        searchBuildings($(this).val());
                    }
                });

                // Filter handlers
                $('#applyFilterBtn').on('click', function() {
                    let usage = $('#filterUsage').val();
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

                        if (usage !== 'all' && b) {
                            let buildingUsage = (b.building_usage || '').toUpperCase();
                            if (buildingUsage !== usage && !buildingUsage.includes(usage)) {
                                show = false;
                            }
                        }

                        if (show && type !== 'all' && b) {
                            let hasQC = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        hasQC = true;
                                        return false;
                                    }
                                });
                            }
                            if (type === 'completed' && !hasQC) show = false;
                            if (type === 'pending' && hasQC) show = false;
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
                    $('#filterUsage').val('all');
                    $('#filterType').val('all');
                    $('#filterMinFloors, #filterMaxFloors').val('');
                    let src = polygonLayer.getSource();
                    $.each(src.getFeatures(), function(i, f) {
                        f.set('visible', true);
                    });
                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(`Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    closeAllPanels();
                });

                // Zoom handlers
                $('#zoomInBtn').on('click', function() {
                    map.getView().setZoom(map.getView().getZoom() + 1);
                });

                $('#zoomOutBtn').on('click', function() {
                    map.getView().setZoom(map.getView().getZoom() - 1);
                });

                $('#closeRouteInfo').on('click', function() {
                    clearRoute();
                });

                $('#startNavigationBtn').on('click', function() {
                    startNavigation();
                });

                // Close panels when clicking outside
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length &&
                        !$(e.target).closest('.action-btn').length &&
                        !$(e.target).closest('.mobile-nav-btn').length &&
                        !$(e.target).closest('#centerMyLocationBtn').length &&
                        !$(e.target).closest('.zoom-btn').length) {
                        closeAllPanels();
                    }
                });

                refreshLayers();
            }

            // Start the application
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
