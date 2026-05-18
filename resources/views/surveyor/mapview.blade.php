{{-- resources/views/surveyor/ward-map.blade.php --}}
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* MAP */
        #map {
            width: 100%;
            height: calc(100vh - 60px);
            border-radius: 0;
            overflow: hidden;
            border: none;
        }

        /* PAGE HEADER - Desktop */
        .page-title {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 16px 25px;
            border-radius: 16px;
            margin-bottom: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .page-title h3 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        /* FLOATING BUTTONS - Desktop */
        #layerToggleBtn,
        #searchToggleBtn,
        #editToggleBtn,
        #liveToggleBtn,
        #routeBtn {
            position: absolute;
            right: 20px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 16px;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            border: none;
        }

        #layerToggleBtn {
            top: 130px;
        }

        #searchToggleBtn {
            top: 195px;
        }

        #liveToggleBtn {
            top: 260px;
        }

        #routeBtn {
            top: 325px;
        }

        #editToggleBtn {
            top: 390px;
        }

        #layerToggleBtn:hover,
        #searchToggleBtn:hover,
        #editToggleBtn:hover,
        #liveToggleBtn:hover,
        #routeBtn:hover {
            transform: scale(1.08);
        }

        /* Edit Panel */
        .edit-Lable {
            position: absolute;
            top: 390px;
            right: 90px;
            z-index: 1100;
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
        }

        .edit-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .edit-Lable select {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        /* Search Label - Desktop */
        .search-Lable {
            position: absolute;
            top: 195px;
            right: 90px;
            z-index: 1100;
            width: 320px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 12px 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .search-Lable input {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            font-size: 14px;
            flex: 1;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-Lable input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .search-Lable button {
            border-radius: 12px;
            padding: 10px 20px;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-Lable button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Search Suggestions */
        .search-suggestions {
            display: none;
            background: white;
            border-radius: 12px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 5px;
        }

        .search-suggestions.show {
            display: block;
        }

        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background: #eff6ff;
        }

        .suggestion-item.selected {
            background: #dbeafe;
        }

        .suggestion-icon {
            width: 24px;
            color: #2563eb;
            font-size: 14px;
        }

        .suggestion-content {
            flex: 1;
        }

        .suggestion-title {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }

        .suggestion-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .suggestion-type {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e2e8f0;
            color: #475569;
        }

        /* Layer Switcher Panel - Desktop */
        .layer-switcher {
            position: absolute;
            top: 125px;
            right: 90px;
            z-index: 1100;
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
        }

        .layer-switcher.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .layer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .layer-header div {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .layer-header i {
            color: #2563eb;
        }

        #closeLayerPanel {
            border: none;
            background: #eff6ff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            color: #1e40af;
            transition: 0.3s;
        }

        #closeLayerPanel:hover {
            background: #dbeafe;
            transform: rotate(90deg);
        }

        .layer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .layer-item:hover {
            background: #eff6ff;
            transform: translateX(4px);
        }

        .layer-item input {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #2563eb;
            position: relative;
            transition: 0.3s;
        }

        .layer-item input:checked+.checkmark {
            background: #2563eb;
        }

        .layer-item input:checked+.checkmark::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 13px;
            top: -1px;
            left: 3px;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            z-index: 1300;
            animation: slideUp 0.3s ease;
            backdrop-filter: blur(10px);
            pointer-events: none;
        }

        .toast-notification.success {
            background: rgba(34, 197, 94, 0.95);
        }

        .toast-notification.error {
            background: rgba(239, 68, 68, 0.95);
        }

        /* Route Info Panel */
        .route-info-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: white;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
            z-index: 1100;
            transition: all 0.3s ease;
            max-width: 400px;
        }

        .route-info-panel.closed {
            transform: translateY(150%);
        }

        .route-info-panel h5 {
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .route-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .route-stat {
            flex: 1;
            text-align: center;
        }

        .route-stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        .route-stat-label {
            font-size: 12px;
            color: #64748b;
        }

        .close-route-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #64748b;
        }

        .start-navigation-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .start-navigation-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1400;
            display: none;
            background: rgba(255, 255, 255, 0.8);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Highlight Layer */
        .highlight-layer {
            z-index: 1000;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }

            .mobile-header {
                display: block;
            }

            #map {
                height: calc(100vh - 56px);
            }

            #layerToggleBtn,
            #searchToggleBtn,
            #editToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                right: auto;
                left: 12px;
                width: 44px;
                height: 44px;
                font-size: 18px;
                border-radius: 12px;
            }

            #layerToggleBtn {
                top: 70px;
            }

            #searchToggleBtn {
                top: 124px;
            }

            #liveToggleBtn {
                top: 178px;
            }

            #routeBtn {
                top: 232px;
            }

            #editToggleBtn {
                top: 286px;
            }

            .layer-switcher {
                top: 60px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: calc(100% - 24px);
                border-radius: 16px;
                padding: 12px;
            }

            .search-Lable {
                top: 124px;
                right: 12px;
                left: 60px;
                width: auto;
                max-width: calc(100% - 80px);
                padding: 10px 12px;
                gap: 8px;
            }

            .edit-Lable {
                top: 286px;
                right: 60px;
                left: auto;
                width: 200px;
                padding: 10px;
            }

            .edit-Lable select {
                padding: 8px;
                font-size: 12px;
            }

            .route-info-panel {
                left: 12px;
                right: 12px;
                bottom: 12px;
                padding: 12px;
            }

            .search-suggestions {
                max-height: 200px;
            }

            .suggestion-item {
                padding: 8px 12px;
            }

            .suggestion-title {
                font-size: 12px;
            }

            .suggestion-subtitle {
                font-size: 10px;
            }

            .route-stats {
                gap: 10px;
            }

            .route-stat-value {
                font-size: 16px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {

            #layerToggleBtn,
            #searchToggleBtn,
            #editToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                width: 38px;
                height: 38px;
                font-size: 16px;
                left: 8px;
            }

            #layerToggleBtn {
                top: 65px;
            }

            #searchToggleBtn {
                top: 113px;
            }

            #liveToggleBtn {
                top: 161px;
            }

            #routeBtn {
                top: 209px;
            }

            #editToggleBtn {
                top: 257px;
            }

            .search-Lable {
                left: 52px;
                padding: 8px 10px;
            }

            .search-Lable input {
                padding: 6px 10px;
                font-size: 12px;
            }

            .search-Lable button {
                padding: 6px 12px;
                font-size: 12px;
            }

            .edit-Lable {
                width: 180px;
                right: 52px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Mobile Header -->
    <div class="mobile-header">
        <h4>
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward {{ $ward->ward_no }} Map
        </h4>
    </div>

    <!-- Desktop Header -->
    <div class="container-fluid py-3 desktop-only">
        <div class="page-title">
            <h3>
                <i class="fas fa-map-marked-alt me-2"></i>
                Ward Map View - Ward {{ $ward->ward_no }}
            </h3>
        </div>
    </div>

    <div id="map"></div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner"></div>

    <!-- Route Info Panel -->
    <div id="routeInfoPanel" class="route-info-panel closed">
        <button class="close-route-btn" id="closeRouteBtn">&times;</button>
        <h5><i class="fas fa-route me-2"></i>Route to Destination</h5>
        <div id="destinationName" style="font-size: 14px; color: #64748b; margin-bottom: 10px;"></div>
        <div class="route-stats">
            <div class="route-stat">
                <div class="route-stat-value" id="routeDistance">0 km</div>
                <div class="route-stat-label">Distance</div>
            </div>
            <div class="route-stat">
                <div class="route-stat-value" id="routeDuration">0 min</div>
                <div class="route-stat-label">Est. Time</div>
            </div>
        </div>
        <button class="start-navigation-btn" id="startNavigationBtn">
            <i class="fas fa-directions me-2"></i>Start Navigation
        </button>
    </div>

    <!-- Floating Action Buttons -->
    <div id="layerToggleBtn" title="Toggle Layers">
        <i class="fas fa-layer-group"></i>
    </div>

    <div id="searchToggleBtn" title="Search">
        <i class="fas fa-search"></i>
    </div>

    <div id="liveToggleBtn" title="My Location">
        <i class="fas fa-location-dot"></i>
    </div>

    <div id="routeBtn" title="Get Route">
        <i class="fas fa-route"></i>
    </div>

    <div id="editToggleBtn" title="Edit Tools">
        <i class="fas fa-pen-to-square"></i>
    </div>

    <!-- Search Panel -->
    <div id="searchLabel" class="search-Lable closed">
        <div class="search-input-wrapper">
            <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID or Road Name..."
                autocomplete="off">
            <button id="searchGisidBtn"><i class="fas fa-search"></i> Search</button>
        </div>
        <div id="searchSuggestions" class="search-suggestions"></div>
    </div>

    <!-- Layer Switcher Panel -->
    <div id="layerSwitcher" class="layer-switcher closed">
        <div class="layer-header">
            <div>
                <i class="fas fa-layer-group"></i>
                <span>Map Layers</span>
            </div>
            <button id="closeLayerPanel">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <label class="layer-item">
            <input type="checkbox" id="osmToggle" checked>
            <span class="checkmark"></span>
            <span>OSM Map</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="satelliteToggle">
            <span class="checkmark"></span>
            <span>Satellite</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="droneToggle" checked>
            <span class="checkmark"></span>
            <span>Drone Image</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="boundaryToggle" checked>
            <span class="checkmark"></span>
            <span>Ward Boundary</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="polygonToggle" checked>
            <span class="checkmark"></span>
            <span>Buildings</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="lineToggle" checked>
            <span class="checkmark"></span>
            <span>Roads</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="pointToggle" checked>
            <span class="checkmark"></span>
            <span>Points</span>
        </label>
    </div>

    <!-- Edit Panel -->
    <div id="editLabel" class="edit-Lable closed">
        <select class="form-select" id="editToolSelect">
            <option value="none">Select Tool</option>
            <option value="Polygon">Draw Polygon</option>
            <option value="Line">Draw Line</option>
            <option value="Point">Draw Point</option>
            <option value="Modify">Modify Feature</option>
            <option value="Delete">Delete Feature</option>
        </select>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash-alt me-2"></i>Delete Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Warning: This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter GIS ID to Delete</label>
                            <input type="text" class="form-control" id="deleteGisIdInput" name="gisid"
                                placeholder="e.g., A1001 or 1001" required>
                        </div>
                        <div id="featurePreview" class="mt-3 p-3 border rounded" style="display: none;">
                            <h6>Feature Details:</h6>
                            <p id="previewText"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Feature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Data passed from controller
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let selectedFeature = null;
            let currentRoute = null;
            let isMobile = window.innerWidth <= 768;
            let searchDebounceTimer = null;
            let currentSuggestions = [];
            let selectedSuggestionIndex = -1;
            let draw, modify, select;
            let featureClickHandler = null;

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

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
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

            // Routes
            let routes = {
                addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
                addLineFeature: "{{ route('surveyor.add.line.feature') }}",
                addPointFeature: "{{ route('surveyor.add.point.feature') }}",
                surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}",
                deleteFeature: "{{ route('surveyor.delete.feature') }}"
            };

            function showToast(message, type = 'info') {
                const toast = $(`<div class="toast-notification ${type}">${message}</div>`);
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
            }

            function showFlashMessage(message, type) {
                showToast(message, type);
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

            // Ward boundary
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
                layers: [osmLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer,
                    pointLayer, routeLayer, highlightLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Feature click handler
            featureClickHandler = function(evt) {
                if (isModifyMode || isDrawingActive) return;
                const feature = map.forEachFeatureAtPixel(evt.pixel, function(feat) {
                    return feat;
                }, {
                    layerFilter: function(layer) {
                        return layer === polygonLayer || layer === lineLayer || layer ===
                        pointLayer;
                    }
                });

                if (feature) {
                    selectedFeature = feature;
                    const gisid = feature.get('gisid');
                    const type = feature.get('type');
                    let info = `<div style="position:absolute; bottom:80px; left:20px; right:20px; background:white; border-radius:12px; padding:12px; z-index:1100; max-width:300px; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                        <button onclick="this.parentElement.remove()" style="float:right; background:none; border:none; font-size:18px;">&times;</button>
                        <strong>GIS ID:</strong> ${gisid}<br>
                        <strong>Type:</strong> ${type}<br>`;
                    if (type === 'Polygon') info +=
                        `<strong>Area:</strong> ${feature.get('sqfeet') || 'N/A'} sqft<br>`;
                    if (type === 'Line' && feature.get('road_name')) info +=
                        `<strong>Road:</strong> ${feature.get('road_name')}<br>`;
                    info +=
                        `<button id="routeFromInfoBtn" class="btn btn-primary btn-sm mt-2" style="width:100%;">Get Route</button></div>`;
                    $('body').append(info);
                    $('#routeFromInfoBtn').click(function() {
                        $('div:has(> #routeFromInfoBtn)').remove();
                        calculateAndDisplayRoute(selectedFeature);
                    });
                    setTimeout(() => {
                        $('div:has(> button[onclick])').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 10000);
                    showToast(`Selected: ${type} ${gisid}`, 'success');
                } else if (selectedFeature) {
                    selectedFeature = null;
                }
            };
            map.on('click', featureClickHandler);

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

            // Setup delete functionality
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

            // Panel toggles
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
            $('#boundaryToggle').change(function() {
                boundaryLayer.setVisible($(this).is(':checked'));
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
            $('#liveToggleBtn').click(toggleLiveLocation);

            function toggleLiveLocation() {
                if (!("geolocation" in navigator)) {
                    showToast("Geolocation not supported", 'error');
                    return;
                }
                showToast('Fetching your location...', 'info');
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords
                        .latitude]);
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
                        `https://www.google.com/maps/dir/?api=1&destination=${currentRoute.endCoord[1]},${currentRoute.endCoord[0]}`,
                        '_blank');
                }
            });

            $(document).click(function(event) {
                if (!$(event.target).closest(
                        '#layerSwitcher, #layerToggleBtn, #searchLabel, #searchToggleBtn, #routeInfoPanel, #routeBtn, #editLabel, #editToggleBtn, #searchSuggestions'
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

            showToast('Map loaded successfully', 'success');
        });
    </script>
@endsection
