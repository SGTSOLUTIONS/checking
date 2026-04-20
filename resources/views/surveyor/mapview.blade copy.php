@extends('layouts.surveyor-layout')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Base Map Styles */
        #map {
            width: 100%;
            height: 90vh;
            border-radius: 10px;
            border: 2px solid #ddd;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            #map {
                height: 100vh;
                border-radius: 0;
                border: none;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .desktop-only {
                display: none !important;
            }

            /* Mobile Toolbar */
            .mobile-toolbar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                z-index: 1002;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                padding: 10px;
                display: flex;
                justify-content: space-around;
                align-items: center;
            }

            .mobile-toolbar-btn {
                display: flex;
                flex-direction: column;
                align-items: center;
                background: none;
                border: none;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 12px;
                color: #666;
                transition: all 0.3s ease;
                flex: 1;
                max-width: 80px;
            }

            .mobile-toolbar-btn.active {
                background: #0066cc;
                color: white;
            }

            .mobile-toolbar-btn i {
                font-size: 18px;
                margin-bottom: 4px;
            }

            /* Mobile Search Overlay */
            .mobile-search-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
                z-index: 1003;
                display: none;
                align-items: center;
                justify-content: center;
            }

            .mobile-search-container {
                background: white;
                border-radius: 12px;
                padding: 20px;
                width: 90%;
                max-width: 400px;
            }

            /* Bottom Sheets */
            .bottom-sheet {
                position: fixed;
                bottom: -100%;
                left: 0;
                right: 0;
                background: white;
                border-radius: 20px 20px 0 0;
                box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.15);
                z-index: 1002;
                transition: bottom 0.3s ease;
                max-height: 80vh;
                overflow-y: auto;
            }

            .bottom-sheet.open {
                bottom: 0;
            }

            .swipe-handle {
                width: 40px;
                height: 4px;
                background: #ddd;
                border-radius: 2px;
                margin: 10px auto;
            }

            .bottom-sheet-content {
                padding: 20px;
            }

            /* Navigation UI for Mobile */
            .navigation-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 15px;
                z-index: 1001;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                display: none;
            }

            .navigation-eta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .eta-time {
                font-size: 22px;
                font-weight: bold;
                color: #2c3e50;
            }

            .eta-distance {
                font-size: 16px;
                color: #666;
            }

            .navigation-address {
                font-size: 16px;
                color: #666;
            }

            .navigation-instruction {
                position: fixed;
                bottom: 80px;
                left: 20px;
                right: 20px;
                background: white;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                z-index: 1001;
                display: none;
            }

            .instruction-text {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 8px;
                color: #2c3e50;
            }

            .instruction-distance {
                font-size: 16px;
                color: #666;
            }

            .instruction-icon {
                position: absolute;
                top: -25px;
                left: 50%;
                transform: translateX(-50%);
                background: #0066cc;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
        }

        /* Desktop Styles */
        @media (min-width: 769px) {

            .mobile-toolbar,
            .mobile-search-overlay,
            .bottom-sheet,
            .navigation-header,
            .navigation-instruction {
                display: none !important;
            }
        }

        /* Common Styles */
        .ol-layer div {
            border: none !important;
            outline: none !important;
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1000;
            width: 220px;
        }

        .layer-switcher h4 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .layer-group {
            margin-bottom: 15px;
        }

        .layer-group h5 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #555;
        }

        .layer-option {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .layer-option input {
            margin-right: 8px;
        }

        .layer-option label {
            cursor: pointer;
            margin: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .layer-option i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }

        /* Search Box */
        .search-container {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 10px;
            width: 300px;
        }

        .search-container h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        .search-box {
            display: flex;
            margin-bottom: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 14px;
        }

        .search-box button {
            background: #4a6ee0;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            padding: 8px 15px;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #3a5ed0;
        }

        .search-results {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 4px;
            display: none;
        }

        .search-result-item {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            font-size: 14px;
        }

        .search-result-item:hover {
            background: #f5f5f5;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        /* Navigation Controls */
        .navigation-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 10px;
            z-index: 1000;
            display: none;
        }

        .navigation-controls button {
            background: #4a6ee0;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .navigation-controls button:hover {
            background: #3a5ed0;
        }

        /* Feature Info */
        .feature-info {
            position: absolute;
            bottom: 20px;
            right: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1000;
            max-width: 300px;
            display: none;
        }

        .feature-info h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        .feature-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .feature-info .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: #777;
        }

        /* Live Location Button */
        .live-location-btn {
            position: absolute;
            top: 10px;
            left: 320px;
            z-index: 1000;
            background: #4a6ee0;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .live-location-btn:hover {
            background: #3a5ed0;
        }

        .live-location-btn.active {
            background: #28a745;
        }

        /* Route Information Panel */
        .route-info {
            position: absolute;
            top: 80px;
            left: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1000;
            max-width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .route-summary {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .directions-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .direction-step {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .direction-step:last-child {
            border-bottom: none;
        }

        .step-number {
            background: #0066cc;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
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
            font-weight: 500;
            margin-bottom: 3px;
        }

        .step-distance {
            font-size: 12px;
            color: #666;
        }

        .close-directions {
            position: absolute;
            top: 5px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #777;
        }

        /* Distance Info */
        .distance-info {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 10px 15px;
            z-index: 1000;
            display: none;
            font-size: 14px;
            font-weight: bold;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            z-index: 1004;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Mobile Layer Switcher */
        .mobile-layer-switcher {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1003;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .mobile-layer-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Editing Tools */
        .editing-tools {
            position: absolute;
            top: 10px;
            right: 250px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1000;
            width: 200px;
        }

        .editing-tools h4 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .tool-group {
            margin-bottom: 15px;
        }

        .tool-group h5 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #555;
        }

        .tool-btn {
            display: flex;
            align-items: center;
            width: 100%;
            background: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .tool-btn:hover {
            background: #f5f5f5;
        }

        .tool-btn.active {
            background: #4a6ee0;
            color: white;
            border-color: #4a6ee0;
        }

        .tool-btn i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }

        /* Mobile Editing Tools */
        .mobile-editing-tools {
            position: fixed;
            bottom: 70px;
            left: 0;
            right: 0;
            background: white;
            z-index: 1002;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: none;
        }

        .mobile-tool-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }

        .mobile-tool-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: none;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px 5px;
            font-size: 11px;
            color: #666;
            transition: all 0.3s ease;
        }

        .mobile-tool-btn.active {
            background: #4a6ee0;
            color: white;
            border-color: #4a6ee0;
        }

        .mobile-tool-btn i {
            font-size: 16px;
            margin-bottom: 4px;
        }

        /* Editing Controls */
        .editing-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 10px;
            z-index: 1000;
            display: none;
        }

        .editing-controls button {
            background: #4a6ee0;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .editing-controls button:hover {
            background: #3a5ed0;
        }

        .editing-controls button.cancel {
            background: #dc3545;
        }

        .editing-controls button.cancel:hover {
            background: #c82333;
        }

        /* Delete Feature Preview */
        #featurePreview {
            background-color: #f8f9fa;
            border-color: #dee2e6 !important;
        }

        #previewText {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Delete button loading state */
        .btn-danger:disabled {
            opacity: 0.7;
        }

        /* Highlight for delete preview */
        .ol-layer .ol-delete-highlight {
            stroke: #dc3545;
            stroke-width: 5;
            fill: rgba(220, 53, 69, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-3 desktop-only">
        <h3 class="fw-bold mb-3">
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward Map View - Ward {{ $ward->ward_no }}
        </h3>
    </div>

    <div id="map"></div>

    <!-- Mobile Toolbar -->
    <div class="mobile-toolbar">
        <button class="mobile-toolbar-btn" id="mobileSearchBtn">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileLocationBtn">
            <i class="fas fa-location-arrow"></i>
            <span>Location</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileLayersBtn">
            <i class="fas fa-layer-group"></i>
            <span>Layers</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileRouteBtn">
            <i class="fas fa-route"></i>
            <span>Route</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </button>
    </div>

    <!-- Mobile Search Overlay -->
    <div class="mobile-search-overlay" id="mobileSearchOverlay">
        <div class="mobile-search-container">
            <h4 class="mb-3"><i class="fas fa-search me-2"></i>Search GIS ID</h4>
            <div class="search-box">
                <input type="text" id="mobileSearchInput" placeholder="Enter GIS ID..." autofocus>
                <button id="mobileSearchSubmit"><i class="fas fa-search"></i></button>
            </div>
            <div class="search-results" id="mobileSearchResults"></div>
            <button class="btn btn-secondary w-100 mt-3" id="closeMobileSearch">Cancel</button>
        </div>
    </div>
    <!-- Mobile Search Overlay -->
    <div class="delgisid d-none">
        <input type="text" id="delete-gisid" placeholder="GIS ID to delete">
        <button class="btn btn-primary w-100 mt-3" id="deleteGisid">Delete</button>
    </div>


    <!-- Mobile Layer Switcher -->
    <div class="mobile-layer-switcher" id="mobileLayerSwitcher">
        <div class="mobile-layer-container">
            <h4 class="mb-3"><i class="fas fa-layer-group me-2"></i>Map Layers</h4>

            <div class="layer-group">
                <h5>Base Maps</h5>
                <div class="layer-option">
                    <input type="radio" id="mobileOsm" name="mobileBaseLayer" value="osm" checked>
                    <label for="mobileOsm"><i class="fas fa-map"></i> OpenStreetMap</label>
                </div>
                <div class="layer-option">
                    <input type="radio" id="mobileTerrain" name="mobileBaseLayer" value="terrain">
                    <label for="mobileTerrain"><i class="fas fa-mountain"></i> Terrain</label>
                </div>
                <div class="layer-option">
                    <input type="radio" id="mobileSatellite" name="mobileBaseLayer" value="satellite">
                    <label for="mobileSatellite"><i class="fas fa-satellite"></i> Satellite</label>
                </div>
            </div>

            <div class="layer-group">
                <h5>Overlay Layers</h5>
                <div class="layer-option">
                    <input type="checkbox" id="mobileDroneLayer" checked>
                    <label for="mobileDroneLayer"><i class="fas fa-drone"></i> Drone Image</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobileBoundaryLayer" checked>
                    <label for="mobileBoundaryLayer"><i class="fas fa-vector-square"></i> Boundary</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobilePolygonLayer" checked>
                    <label for="mobilePolygonLayer"><i class="fas fa-draw-polygon"></i> Polygons</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobileLineLayer" checked>
                    <label for="mobileLineLayer"><i class="fas fa-road"></i> Lines</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobilePointLayer" checked>
                    <label for="mobilePointLayer"><i class="fas fa-map-marker-alt"></i> Points</label>
                </div>
            </div>

            <button class="btn btn-primary w-100 mt-3" id="closeMobileLayers">Apply</button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <div>Calculating route...</div>
        </div>
    </div>

    <!-- Navigation Header -->
    <div class="navigation-header" id="navigationHeader">
        <div class="navigation-eta">
            <div class="eta-time" id="etaTime">-- min</div>
            <div class="eta-distance" id="etaDistance">-- km</div>
        </div>
        <div class="navigation-address" id="destinationAddress">Destination</div>
    </div>

    <!-- Navigation Instruction -->
    <div class="navigation-instruction" id="navigationInstruction">
        <div class="instruction-icon">
            <i class="fas fa-arrow-up" id="instructionIcon"></i>
        </div>
        <div class="instruction-text" id="instructionText">Continue straight</div>
        <div class="instruction-distance" id="instructionDistance">in 500 m</div>
    </div>

    <!-- Bottom Sheet for Route Info -->
    <div class="bottom-sheet" id="routeBottomSheet">
        <div class="swipe-handle"></div>
        <div class="bottom-sheet-content">
            <h4 class="mb-3"><i class="fas fa-route me-2"></i>Route Information</h4>
            <div id="mobileRouteSummary" class="route-summary"></div>
            <div id="mobileDirectionsList" class="directions-list"></div>
            <button class="btn btn-primary w-100 mt-3" id="startNavigationFromSheet">
                <i class="fas fa-play me-2"></i>Start Navigation
            </button>
            <button class="btn btn-outline-secondary w-100 mt-2" id="closeRouteSheet">
                Close
            </button>
        </div>
    </div>
    <!-- Desktop Components -->
    <div class="layer-switcher desktop-only">
        <h4><i class="fas fa-layer-group me-2"></i>Map Layers</h4>
        <div class="layer-group">
            <h5>Base Maps</h5>
            <div class="layer-option">
                <input type="radio" id="osm" name="baseLayer" value="osm" checked>
                <label for="osm"><i class="fas fa-map"></i> OpenStreetMap</label>
            </div>
            <div class="layer-option">
                <input type="radio" id="terrain" name="baseLayer" value="terrain">
                <label for="terrain"><i class="fas fa-mountain"></i> Terrain</label>
            </div>
            <div class="layer-option">
                <input type="radio" id="satellite" name="baseLayer" value="satellite">
                <label for="satellite"><i class="fas fa-satellite"></i> Satellite</label>
            </div>
        </div>
        <div class="layer-group">
            <h5>Overlay Layers</h5>
            <div class="layer-option">
                <input type="checkbox" id="droneLayer" checked>
                <label for="droneLayer"><i class="fas fa-drone"></i> Drone Image</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="boundaryLayer" checked>
                <label for="boundaryLayer"><i class="fas fa-vector-square"></i> Boundary</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="polygonLayer" checked>
                <label for="polygonLayer"><i class="fas fa-draw-polygon"></i> Polygons</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="lineLayer" checked>
                <label for="lineLayer"><i class="fas fa-road"></i> Lines</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="pointLayer" checked>
                <label for="pointLayer"><i class="fas fa-map-marker-alt"></i> Points</label>
            </div>
        </div>
    </div>
    <div class="search-container desktop-only">
        <h4><i class="fas fa-search me-2"></i>Search & Route</h4>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Enter GIS ID...">
            <button id="searchBtn"><i class="fas fa-search"></i></button>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>
    <div class="route-info desktop-only" id="routeInfo">
        <button class="close-directions" id="closeDirections">&times;</button>
        <h4><i class="fas fa-route me-2"></i>Route Information</h4>
        <div id="desktopRouteSummary" class="route-summary"></div>
        <div id="desktopDirectionsList" class="directions-list"></div>
    </div>

    <button class="live-location-btn desktop-only" id="liveLocationBtn">
        <i class="fas fa-location-arrow me-2"></i>Live Location
    </button>
    <!-- Add this button next to live location button in desktop view -->
<button class="live-location-btn desktop-only" id="centerLocationBtn" style="left: 470px; background: #28a745;">
    <i class="fas fa-crosshairs me-2"></i>Center on Me
</button>

    <div class="navigation-controls desktop-only" id="navigationControls">
        <button id="startNavigation"><i class="fas fa-route me-2"></i>Start Navigation</button>
        <button id="clearNavigation"><i class="fas fa-times me-2"></i>Clear Route</button>
    </div>

    <div class="distance-info" id="distanceInfo"></div>

    <div class="feature-info desktop-only" id="featureInfo">
        <button class="close-btn" id="closeFeatureInfo">&times;</button>
        <h4>Feature Details</h4>
        <div id="featureDetails"></div>
    </div>

    <!-- Simple Editing Tools -->
    <div class="editing-tools desktop-only">
        <h4><i class="fas fa-edit me-2"></i>Editing Tools</h4>
        <div class="tool-group">
            <h5>Draw Features</h5>
            <select class="form-select" id="editToolSelect">
                <option value="none">Select Tool</option>
                <option value="Polygon">Draw Polygon</option>
                <option value="Line">Draw Line</option>
                <option value="Point">Draw Point</option>
                <option value="Modify">Modify Feature</option>
                <option value="Delete">Delete Feature</option>
            </select>
        </div>
        <div id="editForms" class="mt-3">
            <!-- Forms will be shown here based on selection -->
        </div>
    </div>

    <!-- Mobile Editing Tools -->
    <div class="mobile-editing-tools" id="mobileEditingTools">
        <div class="mobile-tool-grid">
            <select class="form-select" id="mobileEditToolSelect">
                <option value="none">Select Tool</option>
                <option value="Polygon">Draw Polygon</option>
                <option value="Line">Draw Line</option>
                <option value="Point">Draw Point</option>
                <option value="Modify">Modify</option>
                <option value="Delete">Delete</option>
            </select>
        </div>
    </div>

    <!-- Point Data Modal -->
    <div class="modal fade" id="pointModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Point Data Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="pointForm">
                    <div class="modal-body">
                        <input type="hidden" id="pointgis" name="point_gisid">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Assessment No</label>
                                    <input type="text" class="form-control" id="assessment" name="assessment">
                                    <div class="error-message text-danger" id="assessment_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Old Assessment</label>
                                    <input type="text" class="form-control" id="old_assessment"
                                        name="old_assessment">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Owner Name</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Present Owner Name</label>
                                    <input type="text" class="form-control" id="present_owner_name"
                                        name="present_owner_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="pointSubmit">Save Point Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Building Data Modal -->
    <div class="modal fade" id="buildingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Building Data Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="buildingForm">
                    <input type="hidden" id="gisIdInput" name="gisid">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Number of Bills</label>
                                    <input type="number" class="form-control" id="number_bill" name="number_bill">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Number of Shops</label>
                                    <input type="number" class="form-control" id="number_shop" name="number_shop">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Number of Floors</label>
                                    <input type="number" class="form-control" id="number_floor" name="number_floor">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="buildingsubmitBtn">Save Building Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Feature Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
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
                            Warning: This action cannot be undone. Please confirm the GIS ID you want to delete.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Enter GIS ID to Delete</label>
                            <input type="text" class="form-control" id="deleteGisIdInput" name="gisid"
                                placeholder="e.g., A1001 or 1001" required>
                            <div class="form-text">Enter the exact GIS ID of the feature you want to delete</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feature Type</label>
                            <select class="form-select" id="deleteFeatureType" name="feature_type">
                                {{-- <option value="auto">Auto Detect</option> --}}
                                <option value="Polygon">Polygon</option>
                                {{-- <option value="Line">Line</option> --}}
                                <option value="Point">Point</option>
                            </select>
                            <div class="form-text">Select the type of feature or let it auto-detect</div>
                        </div>
                        <div id="featurePreview" class="mt-3 p-3 border rounded" style="display: none;">
                            <h6>Feature Details:</h6>
                            <p id="previewText">No feature selected</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="fas fa-trash-alt me-2"></i>Delete Feature
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Line Data Modal -->
    <div class="modal fade" id="lineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Line Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="lineForm">
                    <div class="modal-body">
                        <input type="hidden" id="linegisid" name="gisid">
                        <div class="mb-3">
                            <label class="form-label">Road Name</label>
                            <input type="text" class="form-control" name="road_name" id="lineRoadName">
                        </div>
                        <div id="featureline"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="lineSubmit">Update Road Name</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Convert Laravel data into JS variables
        let polygons = @json($polygons);
        let lines = @json($lines);
        let points = @json($points);
        let pointDatas = @json($pointDatas ?? []);
        let polygonDatas = @json($polygonDatas ?? []);
        let ward = @json($ward ?? []);
        console.log('Polygon Data:', lines);
        let mis = @json($mis ?? []);
        let routes = {
            surveyorPolygonDatasUpload: "{{ route('surveyor.polygon.datas.upload') }}",
            surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}",
            updateRoadName: "{{ route('surveyor.update.road.name') }}",
            delgisid: "{{ route('surveyor.delgisid') }}",
            addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
            addLineFeature: "{{ route('surveyor.add.line.feature') }}",
            addPointFeature: "{{ route('surveyor.add.point.feature') }}",
            surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}"
        };

        // Drone image
        let droneImageURL = "{{ asset($ward->drone_image) }}";

        // Extent
        let imageExtent = [
            {{ $ward->extent_left ?? 0 }},
            {{ $ward->extent_bottom ?? 0 }},
            {{ $ward->extent_right ?? 0 }},
            {{ $ward->extent_top ?? 0 }}
        ];

        // Global variables
        let currentLocationMarker = null;
        let locationWatchId = null;
        let isLiveLocationActive = false;
        let currentRoute = null;
        let routeSteps = [];
        let currentStepIndex = 0;
        let navigationMode = false;
        let navigationInterval = null;
        let isMobile = window.innerWidth <= 768;
        let draw = null;
        let modify = null;
        let select = null;
        let isModifyMode = false;

        // ===========================================================
        //  STYLE FUNCTIONS
        // ===========================================================
        function createPointStyle(feature) {
            const gisid = feature.get("gisid");
            const pointData = pointDatas.find(data => data.point_gisid == gisid);

            // Color based on data presence - red if data exists, blue if not
            const color = pointData ? "red" : "blue";

            return new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 8,
                    fill: new ol.style.Fill({
                        color: "white"
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
            const polygonData = polygonDatas.find(data => data.gisid == gisid);

            // Color based on data presence
            const color = polygonData ? "red" : "blue";

            return new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: color,
                    width: 4,
                    lineJoin: "round",
                    lineCap: "round"
                })
            });
        }

        function createLineStyle(feature) {
            const road_name = feature.get("road_name");

            return new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: "yellow", // YELLOW LINE
                    width: 4, // Slightly thicker for visibility
                    lineDash: [], // SOLID (no dash)
                    lineJoin: "round",
                    lineCap: "round"
                }),
                text: new ol.style.Text({
                    text: road_name ? String(road_name) : "",
                    font: "bold 14px Calibri, sans-serif",
                    placement: "line", // <<< IMPORTANT: Label follows the line path
                    overflow: true,
                    fill: new ol.style.Fill({
                        color: "#000" // Black text
                    }),
                    stroke: new ol.style.Stroke({
                        color: "#fff", // White outline for readability
                        width: 3
                    })
                })
            });
        }


        function createHighlightStyle(feature) {
            const geometryType = feature.getGeometry().getType();
            if (geometryType === 'Point') {
                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 10,
                        fill: new ol.style.Fill({
                            color: "rgba(255,255,0,0.5)"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "red",
                            width: 3
                        })
                    })
                });
            } else if (geometryType === 'LineString') {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "red",
                        width: 5
                    })
                });
            } else {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "red",
                        width: 6
                    }),
                    fill: new ol.style.Fill({
                        color: "rgba(255,0,0,0.1)"
                    })
                });
            }
        }

        function createLocationMarkerStyle() {
            return new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 8,
                    fill: new ol.style.Fill({
                        color: 'rgba(0, 150, 255, 0.8)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: '#fff',
                        width: 2
                    })
                })
            });
        }

        // ===========================================================
        //  LAYER DEFINITIONS
        // ===========================================================
        const osmLayer = new ol.layer.Tile({
            source: new ol.source.OSM(),
            visible: true
        });

        const terrainLayer = new ol.layer.Tile({
            source: new ol.source.OSM({
                url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
            }),
            visible: false
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

        const polygonSource = new ol.source.Vector();
        polygons.forEach(poly => {
            let coords = JSON.parse(poly.coordinates);
            polygonSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Polygon(coords),
                gisid: poly.gisid,
                type: "Polygon"
            }));
        });
        const polygonLayer = new ol.layer.Vector({
            source: polygonSource,
            style: createPolygonStyle,
            visible: true
        });

        const lineSource = new ol.source.Vector();
        lines.forEach(l => {
            let coords = JSON.parse(l.coordinates);
            lineSource.addFeature(new ol.Feature({
                geometry: new ol.geom.LineString(coords),
                gisid: l.gisid,
                type: "Line",
                road_name: l.road_name
            }));
        });
        const lineLayer = new ol.layer.Vector({
            source: lineSource,
            style: createLineStyle,
            visible: true
        });

        const pointSource = new ol.source.Vector();
        points.forEach(p => {
            let coords = JSON.parse(p.coordinates);
            pointSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Point(coords),
                gisid: p.gisid,
                type: "Point"
            }));
        });
        const pointLayer = new ol.layer.Vector({
            source: pointSource,
            style: createPointStyle,
            visible: true
        });

        // Add ward boundary
        const boundary = ward.boundary[0];
        const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
        const boundarys = new ol.geom.Polygon([transformedBoundary]);

        const boundaryLayer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [new ol.Feature({
                    geometry: boundarys
                })]
            }),
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: "red",
                    width: 3
                })
            })
        });

        const highlightSource = new ol.source.Vector();
        const highlightLayer = new ol.layer.Vector({
            source: highlightSource,
            style: createHighlightStyle
        });

        const routeSource = new ol.source.Vector();
        const routeLayer = new ol.layer.Vector({
            source: routeSource,
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: '#ff0000',
                    width: 4,
                    lineDash: [5, 5]
                })
            })
        });

        const locationSource = new ol.source.Vector();
        const locationLayer = new ol.layer.Vector({
            source: locationSource,
            style: createLocationMarkerStyle
        });

        // ===========================================================
        //  MAP INITIALIZATION
        // ===========================================================
        const map = new ol.Map({
            target: 'map',
            layers: [
                osmLayer,
                terrainLayer,
                satelliteLayer,
                droneLayer,
                boundaryLayer,
                polygonLayer,
                lineLayer,
                pointLayer,
                highlightLayer,
                routeLayer,
                locationLayer
            ],
            view: new ol.View({
                projection: "EPSG:3857",
                center: ol.extent.getCenter(imageExtent),
                zoom: 17
            })
        });

        // ===========================================================
        //  EDITING TOOLS - MODIFY FEATURE WITH API SUPPORT
        // ===========================================================
        function removeDrawInteractions() {
            map.getInteractions().forEach((interaction) => {
                if (interaction instanceof ol.interaction.Draw ||
                    interaction instanceof ol.interaction.Modify ||
                    interaction instanceof ol.interaction.Select) {
                    map.removeInteraction(interaction);
                }
            });

            // Reset modify mode state
            isModifyMode = false;
        }

        // Store the original click handler
        let originalClickHandler = null;

        // Edit tool selection handler
        $("#editToolSelect, #mobileEditToolSelect").change(function() {
            const value = $(this).val();
            removeDrawInteractions();
            $("#editForms").empty();

            if (value === "Polygon") {
                activateDrawPolygon();
            } else if (value === "Line") {
                activateDrawLine();
            } else if (value === "Point") {
                activateDrawPoint();
            } else if (value === "Modify") {
                activateModify();
            } else if (value === "Delete") {
                activateDelete();
            } else if (value === "none") {
                // When "Select Tool" is chosen, ensure modals work
                isModifyMode = false;
            }
        });

        function activateDrawPolygon() {
            draw = new ol.interaction.Draw({
                source: polygonSource,
                type: "Polygon",
            });
            map.addInteraction(draw);

            draw.on("drawend", function(event) {
                const coordinates = event.feature.getGeometry().getCoordinates();
                console.log('New Polygon Coordinates:', coordinates);

                $.ajax({
                    url: routes.addPolygonFeature,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        type: "Polygon",
                        coordinates: JSON.stringify(coordinates),
                    },
                    success: function(response) {
                        polygons = response.polygons;
                        points = response.points;
                        refreshVectorLayer();
                        showFlashMessage(response.message, "success");
                        $("#editToolSelect, #mobileEditToolSelect").val("none");
                        removeDrawInteractions();
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            const errorMessage = xhr.responseJSON?.error ||
                                "An unknown error occurred.";
                            showFlashMessage(errorMessage, "error");
                        } else {
                            showFlashMessage("An error occurred. Please try again later.", "error");
                        }
                        removeDrawInteractions();
                    },
                });
            });
        }

        function activateDrawLine() {
            draw = new ol.interaction.Draw({
                source: lineSource,
                type: "LineString",
            });
            map.addInteraction(draw);

            draw.on("drawend", function(event) {
                const coordinates = event.feature.getGeometry().getCoordinates();
                console.log('New Line Coordinates:', coordinates);

                $.ajax({
                    url: routes.addLineFeature,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        type: "Line",
                        coordinates: JSON.stringify(coordinates),
                    },
                    success: function(response) {
                        lines = response.lines;
                        refreshVectorLayer();
                        showFlashMessage(response.message, "success");
                        removeDrawInteractions();
                        $("#editToolSelect, #mobileEditToolSelect").val("none");
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            const errorMessage = xhr.responseJSON?.error ||
                                "An unknown error occurred.";
                            showFlashMessage(errorMessage, "error");
                        } else {
                            showFlashMessage("An error occurred. Please try again later.", "error");
                        }
                        removeDrawInteractions();
                    },
                });
            });
        }

        function activateDrawPoint() {
            draw = new ol.interaction.Draw({
                source: pointSource,
                type: "Point",
            });
            map.addInteraction(draw);

            draw.on("drawend", function(event) {
                const coordinates = event.feature.getGeometry().getCoordinates();
                console.log('New Point Coordinates:', coordinates);

                $.ajax({
                    url: routes.addPointFeature,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        type: "Point",
                        coordinates: JSON.stringify(coordinates),
                    },
                    success: function(response) {
                        points = response.points;
                        refreshVectorLayer();
                        showFlashMessage(response.message, "success");
                        removeDrawInteractions();
                        $("#editToolSelect, #mobileEditToolSelect").val("none");
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            const errorMessage = xhr.responseJSON?.error ||
                                "An unknown error occurred.";
                            showFlashMessage(errorMessage, "error");
                        } else {
                            showFlashMessage("An error occurred. Please try again later.", "error");
                        }
                        removeDrawInteractions();
                    },
                });
            });
        }

        function activateModify() {
            // Remove any existing interactions first
            removeDrawInteractions();

            // Store the original click handler if not already stored
            if (!originalClickHandler) {
                const clickListeners = map.getListeners('click');
                if (clickListeners && clickListeners.length > 0) {
                    originalClickHandler = clickListeners[0];
                    map.un('click', originalClickHandler);
                }
            } else {
                // Remove the current click handler
                map.un('click', originalClickHandler);
            }

            // Set modify mode to true
            isModifyMode = true;

            // Select interaction for modification
            select = new ol.interaction.Select({
                layers: [polygonLayer, lineLayer, pointLayer],
                condition: ol.events.condition.click
            });

            modify = new ol.interaction.Modify({
                features: select.getFeatures()
            });

            map.addInteraction(select);
            map.addInteraction(modify);

            // Add new click handler for modify mode only
            map.on('click', function(evt) {
                // In modify mode, we only want to select features, not open modals
                const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                    return feature;
                });

                if (feature) {
                    // Clear previous selection and select the clicked feature
                    select.getFeatures().clear();
                    select.getFeatures().push(feature);

                    // Don't open any modals
                    evt.stopPropagation();
                    return false;
                }
            });

            modify.on('modifyend', function(evt) {
                evt.features.forEach(function(feature) {
                    const geometry = feature.getGeometry();
                    const coordinates = geometry.getCoordinates();
                    const type = feature.get('type');
                    const gisid = feature.get('gisid');

                    console.log('Modified feature:', {
                        gisid,
                        type,
                        coordinates
                    });

                    // Send update to backend
                    $.ajax({
                        url: routes.surveyorModifyFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            gisid: gisid,
                            type: type,
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            if (response.success) {
                                showFlashMessage('Feature updated successfully', 'success');

                                // Update local data
                                if (response.polygons) polygons = response.polygons;
                                if (response.lines) lines = response.lines;
                                if (response.points) points = response.points;

                                refreshVectorLayer();
                            } else {
                                showFlashMessage(response.message, 'error');
                                // Revert the change if needed
                                refreshVectorLayer();
                            }
                        },
                        error: function(xhr) {
                            showFlashMessage('Error updating feature', 'error');
                            // Revert the change
                            refreshVectorLayer();
                        }
                    });
                });
            });
        }

        function activateDelete() {
            // Remove any existing interactions
            removeDrawInteractions();

            // Reset edit tool selection
            $("#editToolSelect, #mobileEditToolSelect").val("none");

            // Show delete modal
            $("#deleteModal").modal("show");
        }

        // ===========================================================
        //  DELETE FEATURE FUNCTIONALITY
        // ===========================================================
        function setupDeleteFunctionality() {
            // Handle delete form submission
            $("#deleteForm").submit(function(e) {
                e.preventDefault();

                const gisid = $("#deleteGisIdInput").val().trim();
                const featureType = $("#deleteFeatureType").val();

                if (!gisid) {
                    showFlashMessage("Please enter a GIS ID", "error");
                    return;
                }

                // Disable submit button and show loading
                $("#confirmDeleteBtn").prop("disabled", true);
                $("#confirmDeleteBtn").html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                // Prepare data for backend
                const formData = {
                    gisid: gisid,
                    feature_type: featureType,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Send delete request
                $.ajax({
                    url: "{{ route('surveyor.delete.feature') }}", // You'll need to create this route
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showFlashMessage(response.message, "success");
                            $("#deleteModal").modal("hide");

                            // Update local data
                            if (response.polygons) polygons = response.polygons;
                            if (response.lines) lines = response.lines;
                            if (response.points) points = response.points;
                            if (response.polygonDatas) polygonDatas = response.polygonDatas;
                            if (response.pointDatas) pointDatas = response.pointDatas;

                            // Refresh the map
                            refreshVectorLayer();

                            // Clear highlight
                            highlightSource.clear();

                            // Reset form
                            $("#deleteForm")[0].reset();
                            $("#featurePreview").hide();
                        } else {
                            showFlashMessage(response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "An error occurred while deleting the feature";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showFlashMessage(errorMessage, "error");
                    },
                    complete: function() {
                        // Re-enable button
                        $("#confirmDeleteBtn").prop("disabled", false);
                        $("#confirmDeleteBtn").html(
                            '<i class="fas fa-trash-alt me-2"></i>Delete Feature');
                    }
                });
            });

            // Auto-detect feature when GIS ID is entered
            $("#deleteGisIdInput").on('input', function() {
                const gisid = $(this).val().trim();
                if (!gisid) {
                    $("#featurePreview").hide();
                    return;
                }

                // Search for the feature
                let foundFeature = null;
                let foundType = null;

                // Check point source
                pointSource.forEachFeature(function(feature) {
                    if (feature.get('gisid') && feature.get('gisid').toString() === gisid) {
                        foundFeature = feature;
                        foundType = "Point";
                        return true; // Break loop
                    }
                });

                // Check line source
                if (!foundFeature) {
                    lineSource.forEachFeature(function(feature) {
                        if (feature.get('gisid') && feature.get('gisid').toString() === gisid) {
                            foundFeature = feature;
                            foundType = "Line";
                            return true;
                        }
                    });
                }

                // Check polygon source
                if (!foundFeature) {
                    polygonSource.forEachFeature(function(feature) {
                        if (feature.get('gisid') && feature.get('gisid').toString() === gisid) {
                            foundFeature = feature;
                            foundType = "Polygon";
                            return true;
                        }
                    });
                }

                // Update preview
                if (foundFeature) {
                    const geometryType = foundFeature.getGeometry().getType();
                    const type = foundType || foundFeature.get('type') || geometryType;

                    // Highlight the feature on map
                    highlightSource.clear();
                    highlightSource.addFeature(foundFeature.clone());

                    // Show preview
                    $("#featurePreview").show();
                    $("#previewText").html(`
                <strong>GIS ID:</strong> ${gisid}<br>
                <strong>Type:</strong> ${type}<br>
                <strong>Geometry:</strong> ${geometryType}
            `);

                    // Auto-set feature type in dropdown
                    $("#deleteFeatureType").val(type);
                } else {
                    $("#featurePreview").show();
                    $("#previewText").html(`
                <span class="text-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    No feature found with GIS ID: ${gisid}
                </span>
            `);
                }
            });

            // Clear highlight when modal is closed
            $("#deleteModal").on('hidden.bs.modal', function() {
                highlightSource.clear();
                $("#deleteForm")[0].reset();
                $("#featurePreview").hide();
            });

            // Click on map to auto-fill GIS ID
            map.on('click', function(evt) {
                // Only if delete modal is open
                if ($("#deleteModal").hasClass('show')) {
                    const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                        return feature;
                    });

                    if (feature && feature.get('gisid')) {
                        const gisid = feature.get('gisid').toString();
                        $("#deleteGisIdInput").val(gisid).trigger('input');

                        // Zoom to feature
                        const view = map.getView();
                        const geometry = feature.getGeometry();

                        if (geometry.getType() === 'Point') {
                            view.animate({
                                center: geometry.getCoordinates(),
                                zoom: 19,
                                duration: 500
                            });
                        } else {
                            view.fit(geometry.getExtent(), {
                                padding: [50, 50, 50, 50],
                                duration: 500
                            });
                        }
                    }
                }
            });
        }

        // Initialize delete functionality when DOM is loaded
        $(document).ready(function() {
            setupDeleteFunctionality();
        });
        // ===========================================================
        //  ORIGINAL CLICK HANDLER FOR MODALS (NON-EDITING MODE)
        // ===========================================================
        function setupOriginalClickHandler() {
            map.on('click', function(evt) {
                // Skip if we're in modify mode
                if (isModifyMode) {
                    return;
                }

                const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                    return feature;
                });

                if (feature) {
                    const properties = feature.getProperties();
                    const geometryType = feature.getGeometry().getType();

                    if (geometryType === "Point") {
                        handlePointClick(properties);
                    } else if (geometryType === "Polygon") {
                        handlePolygonClick(properties);
                    } else if (geometryType === "LineString" || geometryType === "MultiLineString") {
                        handleLineClick(properties);
                    }
                }
            });
        }

        // Initialize the original click handler
        setupOriginalClickHandler();
        originalClickHandler = map.getListeners('click')[0];

        // ===========================================================
        //  MOBILE FUNCTIONALITY
        // ===========================================================
        document.getElementById('mobileSearchBtn').addEventListener('click', function() {
            document.getElementById('mobileSearchOverlay').style.display = 'flex';
        });

        document.getElementById('mobileLocationBtn').addEventListener('click', function() {
            toggleLiveLocation();
            this.classList.toggle('active', isLiveLocationActive);
        });

        document.getElementById('mobileLayersBtn').addEventListener('click', function() {
            document.getElementById('mobileLayerSwitcher').style.display = 'flex';
        });

        document.getElementById('mobileRouteBtn').addEventListener('click', function() {
            if (currentRoute) {
                document.getElementById('routeBottomSheet').classList.add('open');
            } else {
                alert('Please search for a GIS ID and calculate a route first');
            }
        });

        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            showMobileMenu();
        });

        // Mobile search functionality
        document.getElementById('mobileSearchSubmit').addEventListener('click', function() {
            const gisid = document.getElementById('mobileSearchInput').value.trim();
            if (gisid) {
                searchGISID(gisid, true);
            }
        });

        document.getElementById('mobileSearchInput').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                const gisid = this.value.trim();
                if (gisid) {
                    searchGISID(gisid, true);
                }
            }
        });

        document.getElementById('closeMobileSearch').addEventListener('click', function() {
            document.getElementById('mobileSearchOverlay').style.display = 'none';
        });

        document.getElementById('closeMobileLayers').addEventListener('click', function() {
            document.getElementById('mobileLayerSwitcher').style.display = 'none';
        });

        document.getElementById('startNavigationFromSheet').addEventListener('click', function() {
            startNavigation();
            document.getElementById('routeBottomSheet').classList.remove('open');
        });

        document.getElementById('closeRouteSheet').addEventListener('click', function() {
            document.getElementById('routeBottomSheet').classList.remove('open');
        });

        // Mobile layer switcher functionality
        document.querySelectorAll('input[name="mobileBaseLayer"]').forEach(radio => {
            radio.addEventListener('change', function() {
                osmLayer.setVisible(this.value === 'osm');
                terrainLayer.setVisible(this.value === 'terrain');
                satelliteLayer.setVisible(this.value === 'satellite');
            });
        });

        document.getElementById('mobileDroneLayer').addEventListener('change', function() {
            droneLayer.setVisible(this.checked);
        });

        document.getElementById('mobileBoundaryLayer').addEventListener('change', function() {
            boundaryLayer.setVisible(this.checked);
        });

        document.getElementById('mobilePolygonLayer').addEventListener('change', function() {
            polygonLayer.setVisible(this.checked);
        });

        document.getElementById('mobileLineLayer').addEventListener('change', function() {
            lineLayer.setVisible(this.checked);
        });

        document.getElementById('mobilePointLayer').addEventListener('change', function() {
            pointLayer.setVisible(this.checked);
        });

        // Swipe to close bottom sheet
        let startY = 0;
        document.getElementById('routeBottomSheet').addEventListener('touchstart', function(e) {
            startY = e.touches[0].clientY;
        });

        document.getElementById('routeBottomSheet').addEventListener('touchmove', function(e) {
            const currentY = e.touches[0].clientY;
            const diff = currentY - startY;

            if (diff > 50) {
                this.classList.remove('open');
            }
        });

        function showMobileMenu() {
            const menu = `
        <div class="bottom-sheet open" id="mobileMenuSheet">
            <div class="swipe-handle"></div>
            <div class="bottom-sheet-content">
                <h4 class="mb-3"><i class="fas fa-bars me-2"></i>Menu</h4>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="recenterMap()">
                        <i class="fas fa-crosshairs me-2"></i>Recenter Map
                    </button>
                    <button class="btn btn-outline-success" onclick="toggleEditingTools()">
                        <i class="fas fa-edit me-2"></i>Editing Tools
                    </button>
                    <button class="btn btn-outline-secondary" onclick="toggleFullscreen()">
                        <i class="fas fa-expand me-2"></i>Fullscreen
                    </button>
                    <button class="btn btn-outline-info" onclick="shareLocation()">
                        <i class="fas fa-share me-2"></i>Share Location
                    </button>
                    <button class="btn btn-outline-warning" onclick="clearAll()">
                        <i class="fas fa-broom me-2"></i>Clear All
                    </button>
                </div>
                <button class="btn btn-secondary w-100 mt-3" onclick="closeMobileMenu()">
                    Close
                </button>
            </div>
        </div>
        `;

            document.body.insertAdjacentHTML('beforeend', menu);
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobileMenuSheet');
            if (menu) menu.remove();
        }

        function toggleEditingTools() {
            document.getElementById('mobileEditingTools').style.display =
                document.getElementById('mobileEditingTools').style.display === 'none' ? 'block' : 'none';
            closeMobileMenu();
        }

        function recenterMap() {
            map.getView().animate({
                center: ol.extent.getCenter(imageExtent),
                zoom: 17,
                duration: 1000
            });
            closeMobileMenu();
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen();
            }
            closeMobileMenu();
        }

        function shareLocation() {
            if (navigator.share && currentLocationMarker) {
                const coords = ol.proj.toLonLat(currentLocationMarker.getGeometry().getCoordinates());
                navigator.share({
                    title: 'My Current Location',
                    text: `I'm at latitude ${coords[1].toFixed(6)}, longitude ${coords[0].toFixed(6)}`,
                    url: window.location.href
                }).catch(err => {
                    console.log('Share error:', err);
                });
            } else {
                alert('Web Share API not supported or location not available');
            }
            closeMobileMenu();
        }

        function clearAll() {
            highlightSource.clear();
            routeSource.clear();
            document.getElementById('navigationControls').style.display = 'none';
            document.getElementById('featureInfo').style.display = 'none';
            document.getElementById('distanceInfo').style.display = 'none';
            document.getElementById('routeInfo').style.display = 'none';
            document.getElementById('routeBottomSheet').classList.remove('open');
            document.getElementById('navigationHeader').style.display = 'none';
            document.getElementById('navigationInstruction').style.display = 'none';
            navigationMode = false;
            currentRoute = null;
            closeMobileMenu();
        }

        // ===========================================================
        //  DESKTOP FUNCTIONALITY
        // ===========================================================
        document.querySelectorAll('input[name="baseLayer"]').forEach(radio => {
            radio.addEventListener('change', function() {
                osmLayer.setVisible(this.value === 'osm');
                terrainLayer.setVisible(this.value === 'terrain');
                satelliteLayer.setVisible(this.value === 'satellite');
            });
        });

        document.getElementById('droneLayer').addEventListener('change', function() {
            droneLayer.setVisible(this.checked);
        });

        document.getElementById('boundaryLayer').addEventListener('change', function() {
            boundaryLayer.setVisible(this.checked);
        });

        document.getElementById('polygonLayer').addEventListener('change', function() {
            polygonLayer.setVisible(this.checked);
        });

        document.getElementById('lineLayer').addEventListener('change', function() {
            lineLayer.setVisible(this.checked);
        });

        document.getElementById('pointLayer').addEventListener('change', function() {
            pointLayer.setVisible(this.checked);
        });

        document.getElementById('searchBtn').addEventListener('click', function() {
            const gisid = document.getElementById('searchInput').value.trim();
            if (gisid) {
                searchGISID(gisid, false);
            }
        });

        document.getElementById('searchInput').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                const gisid = this.value.trim();
                if (gisid) {
                    searchGISID(gisid, false);
                }
            }
        });

        document.getElementById('liveLocationBtn').addEventListener('click', function() {
            toggleLiveLocation();
        });

        document.getElementById('startNavigation').addEventListener('click', function() {
            startNavigation();
        });

        document.getElementById('clearNavigation').addEventListener('click', function() {
            clearAll();
        });

        document.getElementById('closeFeatureInfo').addEventListener('click', function() {
            document.getElementById('featureInfo').style.display = 'none';
        });

        document.getElementById('closeDirections').addEventListener('click', function() {
            document.getElementById('routeInfo').style.display = 'none';
        });

        // ===========================================================
        //  CORE FUNCTIONALITY
        // ===========================================================
        function toggleLiveLocation() {
    if (isLiveLocationActive) {
        // Stop live location
        if (locationWatchId) {
            navigator.geolocation.clearWatch(locationWatchId);
            locationWatchId = null;
        }
        locationSource.clear();
        currentLocationMarker = null;
        isLiveLocationActive = false;

        // Update UI
        if (isMobile) {
            const mobileBtn = document.getElementById('mobileLocationBtn');
            if (mobileBtn) {
                mobileBtn.classList.remove('active');
                mobileBtn.querySelector('span').textContent = 'Location';
                mobileBtn.querySelector('i').className = 'fas fa-location-arrow';
            }
        } else {
            const desktopBtn = document.getElementById('liveLocationBtn');
            if (desktopBtn) {
                desktopBtn.classList.remove('active');
                desktopBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>Live Location';
            }
        }

        showFlashMessage('Location tracking stopped', 'info');

        // Stop navigation if active
        if (navigationMode) {
            navigationMode = false;
            if (navigationInterval) {
                clearInterval(navigationInterval);
                navigationInterval = null;
            }
        }
    } else {
        // Start live location
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser');
            return;
        }

        // Check if location permissions are already granted
        navigator.permissions.query({ name: 'geolocation' }).then(function(permissionStatus) {
            if (permissionStatus.state === 'denied') {
                alert('Location permission is denied. Please enable location access in your browser settings to use this feature.');
                return;
            }
        });

        isLiveLocationActive = true;

        // Update UI
        if (isMobile) {
            const mobileBtn = document.getElementById('mobileLocationBtn');
            if (mobileBtn) {
                mobileBtn.classList.add('active');
                mobileBtn.querySelector('span').textContent = 'Stop';
                mobileBtn.querySelector('i').className = 'fas fa-stop';
            }
        } else {
            const desktopBtn = document.getElementById('liveLocationBtn');
            if (desktopBtn) {
                desktopBtn.classList.add('active');
                desktopBtn.innerHTML = '<i class="fas fa-stop me-2"></i>Stop Location';
            }
        }

        // Show loading indicator
        showFlashMessage('Getting your current location...', 'info');

        // Flag to track if we've already zoomed to location
        let hasZoomedToLocation = false;

        locationWatchId = navigator.geolocation.watchPosition(
            (position) => {
                try {
                    const coords = ol.proj.fromLonLat([
                        position.coords.longitude,
                        position.coords.latitude
                    ]);

                    console.log('Location updated:', coords); // Debug log

                    // Update or create location marker
                    locationSource.clear();
                    currentLocationMarker = new ol.Feature({
                        geometry: new ol.geom.Point(coords)
                    });
                    locationSource.addFeature(currentLocationMarker);

                    // ZOOM INTO LOCATION - THIS IS THE KEY PART
                    if (!hasZoomedToLocation) {
                        // First time getting location - zoom with animation
                        map.getView().animate({
                            center: coords,
                            zoom: 19,  // Zoom level 19 for detailed view
                            duration: 1500,  // Smooth animation over 1.5 seconds
                            easing: ol.easing.easeOut  // Smooth easing function
                        });
                        hasZoomedToLocation = true;

                        // Show success message
                        showFlashMessage('Location found! Zooming to your current location.', 'success');

                        // Optional: Add a temporary marker effect
                        setTimeout(() => {
                            // Add a subtle highlight effect
                            const originalStyle = createLocationMarkerStyle();
                            const highlightStyle = new ol.style.Style({
                                image: new ol.style.Circle({
                                    radius: 12,
                                    fill: new ol.style.Fill({
                                        color: 'rgba(0, 150, 255, 0.6)'
                                    }),
                                    stroke: new ol.style.Stroke({
                                        color: '#fff',
                                        width: 3
                                    })
                                })
                            });

                            // Temporarily change marker style for emphasis
                            locationSource.getFeatures()[0].setStyle(highlightStyle);
                            setTimeout(() => {
                                if (locationSource.getFeatures()[0]) {
                                    locationSource.getFeatures()[0].setStyle(null);
                                }
                            }, 1000);
                        }, 500);
                    } else {
                        // Subsequent location updates - just update marker without zoom
                        // But you can optionally recenter if user moves far
                        const currentCenter = map.getView().getCenter();
                        const distance = Math.sqrt(
                            Math.pow(coords[0] - currentCenter[0], 2) +
                            Math.pow(coords[1] - currentCenter[1], 2)
                        );

                        // If user moved more than 100 meters (adjust based on your map scale)
                        if (distance > 100) {
                            // Smoothly follow the user
                            map.getView().animate({
                                center: coords,
                                duration: 500
                            });
                        }
                    }

                    // Update route if navigation is active
                    if (navigationMode && currentRoute) {
                        updateRouteIfActive();
                    }
                } catch (error) {
                    console.error('Error updating location marker:', error);
                }
            },
            (error) => {
                console.error('Geolocation error:', error);
                let errorMessage = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location permission denied. Please enable location access.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'The request to get your location timed out.';
                        break;
                    default:
                        errorMessage = 'An unknown error occurred.';
                }
                showFlashMessage(errorMessage, 'error');
                toggleLiveLocation(); // Turn off live location on error
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
}
// Function to re-center on current location (useful for mobile)
function recenterOnLocation() {
    if (currentLocationMarker) {
        const coords = currentLocationMarker.getGeometry().getCoordinates();
        map.getView().animate({
            center: coords,
            zoom: 19,
            duration: 800,
            easing: ol.easing.easeOut
        });
        showFlashMessage('Centered on your location', 'info');

        // Add temporary highlight effect
        const feature = locationSource.getFeatures()[0];
        if (feature) {
            const originalStyle = feature.getStyle();
            const highlightStyle = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 12,
                    fill: new ol.style.Fill({
                        color: 'rgba(0, 150, 255, 0.8)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: '#fff',
                        width: 3
                    })
                })
            });
            feature.setStyle(highlightStyle);
            setTimeout(() => {
                if (feature) {
                    feature.setStyle(originalStyle);
                }
            }, 1000);
        }
    } else {
        showFlashMessage('Location not available. Please enable live location first.', 'warning');
        // Optionally trigger location activation
        if (!isLiveLocationActive) {
            if (confirm('Live location is not active. Would you like to enable it?')) {
                toggleLiveLocation();
            }
        }
    }
}
        // Add this function to check if location is available
        function checkLocationAvailability() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return false;
            }

            // Check if location is already tracking
            if (currentLocationMarker) {
                return true;
            }

            return false;
        }

        function searchGISID(gisid, isMobile = false) {
            const searchResults = isMobile ?
                document.getElementById('mobileSearchResults') :
                document.getElementById('searchResults');

            searchResults.innerHTML = '';
            highlightSource.clear();
            routeSource.clear();

            const allSources = [pointSource, lineSource, polygonSource];
            let foundFeatures = [];

            allSources.forEach(source => {
                source.forEachFeature(feature => {
                    if (feature.get('gisid') && feature.get('gisid').toString() === gisid) {
                        foundFeatures.push(feature);
                    }
                });
            });

            if (foundFeatures.length > 0) {
                searchResults.style.display = 'block';

                foundFeatures.forEach(feature => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result-item';
                    resultItem.innerHTML = `
                <strong>GIS ID:</strong> ${feature.get('gisid')} <br>
                <strong>Type:</strong> ${feature.get('type')}
            `;

                    resultItem.addEventListener('click', function() {
                        selectFeature(feature, isMobile);
                        if (isMobile) {
                            document.getElementById('mobileSearchOverlay').style.display = 'none';
                        }
                        searchResults.style.display = 'none';
                    });

                    searchResults.appendChild(resultItem);
                });
            } else {
                searchResults.style.display = 'block';
                const noResult = document.createElement('div');
                noResult.className = 'search-result-item';
                noResult.textContent = 'No features found with this GIS ID';
                searchResults.appendChild(noResult);
            }
        }

        async function selectFeature(feature, isMobile = false) {
            highlightSource.clear();
            routeSource.clear();

            const featureClone = feature.clone();
            highlightSource.addFeature(featureClone);

            const view = map.getView();
            const geometry = feature.getGeometry();

            if (geometry.getType() === 'Point') {
                view.animate({
                    center: geometry.getCoordinates(),
                    zoom: 19,
                    duration: 1000
                });
            } else {
                view.fit(geometry.getExtent(), {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });
            }

            if (!isMobile) {
                showFeatureInfo(feature);
            }

            // If location is not available, prompt user to enable it
            if (!currentLocationMarker) {
                const enableLocation = confirm(
                    'Live location is not enabled. Would you like to enable it for route calculation?');
                if (enableLocation) {
                    toggleLiveLocation();
                    // Wait a moment for location to be acquired
                    setTimeout(async () => {
                        if (currentLocationMarker) {
                            await calculateAndDisplayRoute(feature, geometry, isMobile);
                        } else {
                            alert(
                                'Unable to get your location. Please enable location services and try again.');
                        }
                    }, 2000);
                }
                return;
            }

            await calculateAndDisplayRoute(feature, geometry, isMobile);
        }

        // Helper function to calculate and display route
        async function calculateAndDisplayRoute(feature, geometry, isMobile) {
            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';

            try {
                const currentCoords = currentLocationMarker.getGeometry().getCoordinates();
                const targetCoords = geometry.getType() === 'Point' ?
                    geometry.getCoordinates() :
                    ol.extent.getCenter(geometry.getExtent());

                const currentLonLat = ol.proj.toLonLat(currentCoords);
                const targetLonLat = ol.proj.toLonLat(targetCoords);

                const route = await calculateEnhancedRoute(currentLonLat, targetLonLat,
                    `GIS ID: ${feature.get('gisid')}`);
                currentRoute = route;

                // Show route info in appropriate container
                if (isMobile) {
                    document.getElementById('routeBottomSheet').classList.add('open');
                } else {
                    document.getElementById('navigationControls').style.display = 'block';
                }
            } catch (error) {
                console.error('Route calculation error:', error);
                alert('Error calculating route: ' + error.message);
            } finally {
                document.getElementById('loadingSpinner').style.display = 'none';
            }
        }

        function showFeatureInfo(feature) {
            const featureInfo = document.getElementById('featureInfo');
            const featureDetails = document.getElementById('featureDetails');

            featureDetails.innerHTML = `
        <p><strong>GIS ID:</strong> ${feature.get('gisid')}</p>
        <p><strong>Type:</strong> ${feature.get('type')}</p>
        <p><strong>Coordinates:</strong> ${feature.getGeometry().getType()}</p>
    `;

            featureInfo.style.display = 'block';
        }

        // ===========================================================
        //  ROUTE FUNCTIONS
        // ===========================================================
        function transformGisId(gisid) {
            if (gisid.startsWith("A")) {
                gisid = gisid.substring(1);
                gisid = (parseInt(gisid) + 5000).toString();
            }
            return gisid.trim();
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
                console.error('Error getting route from OSRM:', error);
                return getStraightLineRoute(startCoord, endCoord);
            }
        }

        function getStraightLineRoute(startCoord, endCoord) {
            const distance = calculateDistance(startCoord, endCoord);
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

        function calculateDistance(coord1, coord2) {
            return ol.sphere.getDistance(
                ol.proj.fromLonLat(coord1),
                ol.proj.fromLonLat(coord2)
            );
        }

        function formatDistance(meters) {
            if (meters < 1000) {
                return meters.toFixed(0) + ' meters';
            } else {
                return (meters / 1000).toFixed(2) + ' km';
            }
        }

        function formatDuration(seconds) {
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) {
                return minutes + ' min';
            } else {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                return hours + 'h ' + remainingMinutes + 'm';
            }
        }

        function getDirectionIcon(maneuverType, modifier = '') {
            const icons = {
                'depart': 'fas fa-play',
                'arrive': 'fas fa-flag-checkered',
                'turn': 'fas fa-undo',
                'new name': 'fas fa-sign',
                'continue': 'fas fa-arrow-up',
                'roundabout': 'fas fa-sync',
                'rotary': 'fas fa-sync-alt',
                'exit roundabout': 'fas fa-sign-out-alt',
                'exit rotary': 'fas fa-sign-out-alt',
                'fork': 'fas fa-code-branch',
                'merge': 'fas fa-arrow-right',
                'on ramp': 'fas fa-arrow-up',
                'off ramp': 'fas fa-arrow-down',
                'end of road': 'fas fa-road'
            };

            if (modifier) {
                const turnIcons = {
                    'left': 'fas fa-arrow-left',
                    'right': 'fas fa-arrow-right',
                    'sharp left': 'fas fa-arrow-left',
                    'sharp right': 'fas fa-arrow-right',
                    'slight left': 'fas fa-arrow-left',
                    'slight right': 'fas fa-arrow-right',
                    'straight': 'fas fa-arrow-up',
                    'uturn': 'fas fa-undo'
                };
                return turnIcons[modifier] || icons[maneuverType] || 'fas fa-arrow-up';
            }

            return icons[maneuverType] || 'fas fa-arrow-up';
        }

        async function calculateEnhancedRoute(startCoord, endCoord, placeName) {
            try {
                // Show loading state
                if (isMobile) {
                    document.getElementById('mobileRouteSummary').innerHTML = '<div>Calculating route...</div>';
                } else {
                    document.getElementById('desktopRouteSummary').innerHTML = '<div>Calculating route...</div>';
                    document.getElementById('routeInfo').style.display = 'block';
                }

                const route = await getRouteFromOSRM(startCoord, endCoord);

                const totalDistance = route.distance;
                const totalDuration = route.duration;

                routeSteps = [];
                let accumulatedDistance = 0;

                route.legs[0].steps.forEach((step, index) => {
                    const maneuver = step.maneuver;
                    const distance = step.distance;
                    accumulatedDistance += distance;

                    routeSteps.push({
                        instruction: maneuver.instruction || getStepInstruction(maneuver),
                        distance: formatDistance(accumulatedDistance),
                        icon: getDirectionIcon(maneuver.type, maneuver.modifier),
                        type: maneuver.type,
                        geometry: step.geometry
                    });
                });

                drawRouteOnMap(route.geometry);
                displayRouteInfo(totalDistance, totalDuration, placeName);

                return {
                    distance: totalDistance,
                    duration: totalDuration,
                    geometry: route.geometry,
                    endCoord: endCoord,
                    placeName: placeName
                };

            } catch (error) {
                console.error('Error calculating route:', error);

                const distance = calculateDistance(startCoord, endCoord);
                const duration = distance / 1.39;

                routeSteps = [{
                        instruction: "Start from your current location",
                        distance: "0.0 km",
                        icon: "fas fa-play",
                        type: "depart"
                    },
                    {
                        instruction: "Continue straight to destination",
                        distance: formatDistance(distance),
                        icon: "fas fa-arrow-up",
                        type: "continue"
                    },
                    {
                        instruction: "Arrive at your destination",
                        distance: formatDistance(distance),
                        icon: "fas fa-flag-checkered",
                        type: "arrive"
                    }
                ];

                drawRouteOnMap({
                    type: "LineString",
                    coordinates: [startCoord, endCoord]
                });

                displayRouteInfo(distance, duration, placeName);

                return {
                    distance: distance,
                    duration: duration,
                    geometry: {
                        type: "LineString",
                        coordinates: [startCoord, endCoord]
                    },
                    endCoord: endCoord,
                    placeName: placeName
                };
            }
        }

        function getStepInstruction(maneuver) {
            const baseInstruction = maneuver.type.replace(/_/g, ' ');
            if (maneuver.modifier) {
                return `${maneuver.modifier} ${baseInstruction}`;
            }
            return baseInstruction;
        }

        function drawRouteOnMap(geometry) {
            const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
            const routeFeature = new ol.Feature({
                geometry: new ol.geom.LineString(coordinates)
            });

            routeSource.clear();
            routeSource.addFeature(routeFeature);

            const extent = routeFeature.getGeometry().getExtent();
            map.getView().fit(extent, {
                padding: [50, 50, 50, 50],
                duration: 1000
            });
        }

        function displayRouteInfo(distance, duration, placeName) {
            const summaryHtml = `
        <div><strong>Total Distance:</strong> ${formatDistance(distance)}</div>
        <div><strong>Estimated Time:</strong> ${formatDuration(duration)}</div>
        <div><strong>Destination:</strong> ${placeName}</div>
    `;

            if (isMobile) {
                document.getElementById('mobileRouteSummary').innerHTML = summaryHtml;
                displayTurnByTurnDirections(true);
            } else {
                document.getElementById('desktopRouteSummary').innerHTML = summaryHtml;
                displayTurnByTurnDirections(false);
                document.getElementById('navigationControls').style.display = 'block';
            }
        }

        function displayTurnByTurnDirections(isMobile = false) {
            const directionsList = isMobile ?
                document.getElementById('mobileDirectionsList') :
                document.getElementById('desktopDirectionsList');

            directionsList.innerHTML = '';

            routeSteps.forEach((step, index) => {
                const stepElement = document.createElement('div');
                stepElement.className = 'direction-step';
                stepElement.innerHTML = `
            <div class="step-number">${index + 1}</div>
            <div class="step-content">
                <div class="step-instruction"><i class="${step.icon} me-2"></i>${step.instruction}</div>
                <div class="step-distance">${step.distance}</div>
            </div>
        `;
                directionsList.appendChild(stepElement);
            });
        }

        function startNavigation() {
            if (!currentRoute) {
                alert('Please select a feature and calculate a route first');
                return;
            }

            if (!currentLocationMarker) {
                alert('Please enable live location first to start navigation');
                return;
            }

            navigationMode = true;
            currentStepIndex = 0;

            // Show navigation UI
            if (isMobile) {
                document.getElementById('navigationHeader').style.display = 'block';
                document.getElementById('navigationInstruction').style.display = 'block';
                document.getElementById('routeBottomSheet').classList.remove('open');
            }

            document.getElementById('etaTime').textContent = formatDuration(currentRoute.duration);
            document.getElementById('etaDistance').textContent = formatDistance(currentRoute.distance);
            document.getElementById('destinationAddress').textContent = currentRoute.placeName;

            updateNavigationInstruction();
            navigationInterval = setInterval(updateNavigationStatus, 5000);

            if (isMobile) {
                // Center on user location for mobile navigation
                map.getView().animate({
                    center: currentLocationMarker.getGeometry().getCoordinates(),
                    zoom: 18,
                    duration: 1000
                });
            }

            alert('Navigation started! Follow the route instructions.' + (isMobile ? ' Navigation UI is now active.' : ''));
        }

        function updateNavigationInstruction() {
            if (currentStepIndex < routeSteps.length) {
                const currentStep = routeSteps[currentStepIndex];
                document.getElementById('instructionText').textContent = currentStep.instruction;
                document.getElementById('instructionDistance').textContent = currentStep.distance;
                document.getElementById('instructionIcon').className = currentStep.icon;
            }
        }

        function updateNavigationStatus() {
            if (!navigationMode || !currentRoute) return;

            const progress = Math.min(1, currentStepIndex / (routeSteps.length - 1));
            const remainingDistance = currentRoute.distance * (1 - progress);
            const remainingDuration = currentRoute.duration * (1 - progress);

            document.getElementById('etaTime').textContent = formatDuration(remainingDuration);
            document.getElementById('etaDistance').textContent = formatDistance(remainingDistance);

            // Simulate step progression (in real app, this would be based on actual location)
            if (Math.random() > 0.7 && currentStepIndex < routeSteps.length - 1) {
                currentStepIndex++;
                updateNavigationInstruction();
            }
        }

        function updateRouteIfActive() {
            if (currentRoute && currentLocationMarker && navigationMode) {
                const currentCoords = currentLocationMarker.getGeometry().getCoordinates();
                const currentLonLat = ol.proj.toLonLat(currentCoords);

                // Recalculate route with updated current location
                calculateEnhancedRoute(currentLonLat, currentRoute.endCoord, currentRoute.placeName)
                    .then(route => {
                        currentRoute = route;
                    });
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            isMobile = window.innerWidth <= 768;
        });

        // Initialize mobile layer switcher state
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial mobile layer states to match desktop
            document.getElementById('mobileOsm').checked = true;
            document.getElementById('mobileDroneLayer').checked = true;
            document.getElementById('mobileBoundaryLayer').checked = true;
            document.getElementById('mobilePolygonLayer').checked = true;
            document.getElementById('mobileLineLayer').checked = true;
            document.getElementById('mobilePointLayer').checked = true;
        });

        // ===========================================================
        //  FORM HANDLING FUNCTIONS
        // ===========================================================
        function handlePointClick(properties) {
            const gisid = properties["gisid"];

            // Reset form fields
            resetPointFormFields();

            // Find polygon data for this point
            const polygonData = polygonDatas.find(data => data.gisid === gisid);
            const polygonNumOfBill = polygonData ? polygonData.number_bill : null;

            // Count how many points already have data for this GIS ID
            const matchingPointsCount = pointDatas.filter(data => data.point_gisid === gisid).length;

            if (polygonNumOfBill > matchingPointsCount) {
                // Show point modal
                $("#pointgis").val(gisid);
                $("#pointModal").modal("show");
            } else {
                showFlashMessage(`Already this building have ${matchingPointsCount} bills`, "error");
            }
        }

        function handlePolygonClick(properties) {
            const gisId = properties["gisid"];
            console.log("Polygon clicked - GIS ID:", gisId);

            let valueFound = false;

            // Find polygon data for this GIS ID
            if (polygonDatas && polygonDatas.length > 0) {
                polygonDatas.forEach(function(item) {
                    if (item.gisid == gisId) {
                        console.log("Found polygon data:", item);
                        // Populate building form fields
                        populateBuildingForm(item);
                        valueFound = true;
                        return false; // Break loop
                    }
                });
            }

            if (!valueFound) {
                console.log("No polygon data found for GIS ID:", gisId);
                // Reset form if no data found
                resetBuildingForm();
                // Only set road_name if the element exists
                const roadNameInput = document.getElementById("road_name");
                if (roadNameInput) {
                    roadNameInput.value = localStorage.getItem("road") || "";
                }
            }

            // Set GIS ID and show modal
            const gisIdInput = document.getElementById("gisIdInput");
            if (gisIdInput) {
                gisIdInput.value = gisId;
                $("#buildingModal").modal("show");
            } else {
                console.error("GIS ID input element not found");
            }
        }

        function handleLineClick(properties) {
            const gisid = properties["gisid"];
            console.log("Line feature properties:", properties);

            if (gisid) {
                $("#linegisid").val(gisid);
                const roadName = properties["road_name"] || "No road name";
                document.getElementById("featureline").innerHTML = roadName;
                $("#lineModal").modal("show");
            } else {
                console.error("GIS ID not found for the selected line.");
            }
        }

        // Form reset functions
        function resetPointFormFields() {
            const fieldsToReset = [
                "pointgis", "assessment", "old_assessment", "owner_name", "present_owner_name",
                "floor", "old_door_no", "eb", "new_door_no", "bill_usage", "water_tax",
                "old_water_tax", "phone", "remarks"
            ];

            fieldsToReset.forEach(function(id) {
                const element = document.getElementById(id);
                if (element) {
                    element.value = "";
                }
            });
        }

        function resetBuildingForm() {
            const fields = {
                "number_bill": "",
                "number_shop": "",
                "number_floor": "",
                "building_name": "",
                "building_usage": "",
                "construction_type": "",
                "ugd": "",
                "rainwater_harvesting": "",
                "parking": "",
                "ramp": "",
                "hoarding": "",
                "liftroom": "",
                "overhead_tank": "",
                "headroom": "",
                "cell_tower": "",
                "percentage": "",
                "new_address": "",
                "cctv": "",
                "water_connection": "",
                "phone": "",
                "remarks": "",
                "solar_panel": ""
            };

            Object.keys(fields).forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.value = fields[field];
                }
            });

            // Only reset image if element exists
            const buildingImg = document.getElementById("building_img");
            if (buildingImg) {
                buildingImg.src = "";
            }
        }

        function populateBuildingForm(item) {
            const fieldMappings = {
                "number_bill": item.number_bill,
                "number_shop": item.number_shop,
                "number_floor": item.number_floor,
                "building_name": item.building_name,
                "building_usage": item.building_usage,
                "construction_type": item.construction_type,
                "road_name": item.road_name,
                "ugd": item.ugd,
                "rainwater_harvesting": item.rainwater_harvesting,
                "parking": item.parking,
                "ramp": item.ramp,
                "hoarding": item.hoarding,
                "building_type": item.building_type,
                "basement": item.basement,
                "liftroom": item.liftroom,
                "overhead_tank": item.overhead_tank,
                "headroom": item.headroom,
                "cell_tower": item.cell_tower,
                "percentage": item.percentage,
                "new_address": item.new_address,
                "cctv": item.cctv,
                "water_connection": item.water_connection,
                "phone": item.phone,
                "remarks": item.remarks,
                "solar_panel": item.solar_panel
            };

            Object.keys(fieldMappings).forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.value = fieldMappings[field] || "";
                }
            });

            // Set image if available and element exists
            const buildingImg = document.getElementById("building_img");
            if (buildingImg && item.gisid) {
                const numericPart = item.gisid.match(/\d+$/)?.[0];
                if (numericPart) {
                    const image = numericPart + ".jpg";
                    // Adjust base path as needed
                    const basePath =
                        "{{ $ward->corporation_id == 100 ? 'public/corporation/ss' : 'public/corporation/coimbatore' }}";
                    const imagePath = `${basePath}/{{ $ward->zone }}/{{ $ward->ward_no }}/images/${image}`;
                    buildingImg.src = imagePath;
                }
            }
        }

        // ===========================================================
        //  FORM SUBMISSION HANDLERS
        // ===========================================================
        $("#pointForm").submit(function(e) {
            e.preventDefault();
            $(".error-message").text("");
            $("input").removeClass("is-invalid");

            const formData = $(this).serialize();
            $("#pointSubmit").prop("disabled", true);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: routes.surveyorPointDataUpload,
                data: formData,
                success: function(response) {
                    showFlashMessage(response.message, "success");
                    $("#pointModal").modal("hide");

                    // Update local data and refresh layer
                    if (response.pointDatas) pointDatas = response.pointDatas;
                    if (response.points) points = response.points;

                    refreshVectorLayer();
                },
                error: function(xhr) {
                    handleFormError(xhr, "pointSubmit");
                }
            });
        });

        $("#buildingForm").submit(function(e) {
            e.preventDefault();
            $(".error-message").text("");
            $("input").removeClass("is-invalid");

            const formData = new FormData(this);
            $("#buildingsubmitBtn").prop("disabled", true);

            $.ajax({
                type: "POST",
                url: routes.surveyorPolygonDatasUpload,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showFlashMessage(response.message, "success");
                    $("#buildingModal").modal("hide");

                    // Update local data
                    if (response.polygonDatas) polygonDatas = response.polygonDatas;
                    if (response.polygon) polygons = response.polygon;
                    if (response.point) points = response.point;

                    refreshVectorLayer();
                },
                error: function(xhr) {
                    handleFormError(xhr, "buildingsubmitBtn");
                }
            });
        });

        $("#lineForm").submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $("#lineSubmit").prop("disabled", true);

            $.ajax({
                url: routes.updateRoadName,
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    showFlashMessage(response.message, "success");
                    $("#lineModal").modal("hide");

                    if (response.lines) lines = response.lines;
                    refreshVectorLayer();
                },
                error: function(xhr) {
                    handleFormError(xhr, "lineSubmit");
                }
            });
        });

        // Utility functions
        function handleFormError(xhr, buttonId) {
            let errorMsg = "An error occurred while processing your request.";

            if (xhr.responseJSON && xhr.responseJSON.msg) {
                errorMsg = xhr.responseJSON.msg;
            }

            showFlashMessage(errorMsg, "error");
            $("#" + buttonId).prop("disabled", false);

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, value) {
                    $("#" + key).addClass("is-invalid");
                    $("#" + key + "_error").text(value[0]);
                });
            }
        }

        function refreshVectorLayer() {
            // Clear and rebuild vector sources with updated data
            pointSource.clear();
            points.forEach(p => {
                let coords = JSON.parse(p.coordinates);
                pointSource.addFeature(new ol.Feature({
                    geometry: new ol.geom.Point(coords),
                    gisid: p.gisid,
                    type: "Point"
                }));
            });

            polygonSource.clear();
            polygons.forEach(poly => {
                let coords = JSON.parse(poly.coordinates);
                polygonSource.addFeature(new ol.Feature({
                    geometry: new ol.geom.Polygon(coords),
                    gisid: poly.gisid,
                    type: "Polygon"
                }));
            });

            lineSource.clear();
            lines.forEach(l => {
                let coords = JSON.parse(l.coordinates);
                lineSource.addFeature(new ol.Feature({
                    geometry: new ol.geom.LineString(coords),
                    gisid: l.gisid,
                    type: "Line",
                    road_name: l.road_name
                }));
            });

            // Refresh map
            map.render();
        }

        // ===========================================================
        //  AUTO-COMPLETE FUNCTIONALITY
        // ===========================================================
        $("#assessment").keyup(function() {
            const inputValue = $(this).val();
            const matchingData = mis.filter(row => row.assessment === inputValue);

            if (matchingData.length > 0) {
                const data = matchingData[0];
                $("#old_assessment").val(data.old_assessment || "");
                $("#owner_name").val(data.owner_name || "");
                $("#old_door_no").val(data.old_door_no || "");
                $("#water_tax").val(data.water_tax || "");
                $("#new_door_no").val(data.OLD_door_no || "");
                $("#road_name").val(data.road_name || "");
                $("#phone").val(data.phone || "");
                $("#water_tax").val(data.watertax || "");
                $("#old_water_tax").val(data.ward || "");
            }
        });

        $("#old_assessment").keyup(function() {
            const inputValue = $(this).val();
            const matchingData = mis.filter(row => row.old_assessment === inputValue);

            if (matchingData.length > 0) {
                const data = matchingData[0];
                $("#assessment").val(data.assessment || "");
                $("#owner_name").val(data.owner_name || "");
                $("#old_door_no").val(data.old_door_no || "");
                $("#water_tax").val(data.water_tax || "");
                $("#new_door_no").val(data.OLD_door_no || "");
                $("#road_name").val(data.road_name || "");
                $("#phone").val(data.phone || "");
                $("#water_tax").val(data.watertax || "");
                $("#old_water_tax").val(data.ward || "");
            }
        });

        // ===========================================================
        //  FLASH MESSAGE FUNCTION
        // ===========================================================
        function showFlashMessage(message, type = 'info') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            } [type] || 'alert-info';

            const flashHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

            $('body').append(flashHtml);

            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // ===========================================================
        //  ADDITIONAL FEATURES
        // ===========================================================
        // Add this function to handle tool switching
        function restoreOriginalClickHandler() {
            if (originalClickHandler) {
                // Remove any existing click handlers
                const currentListeners = map.getListeners('click');
                currentListeners.forEach(listener => {
                    map.un('click', listener);
                });

                // Restore the original click handler
                map.on('click', originalClickHandler);
            }
        }

        // Modify the removeDrawInteractions function to also restore click handler
        function completeRemoveDrawInteractions() {
            removeDrawInteractions();
            restoreOriginalClickHandler();
            isModifyMode = false;
        }
        // Add to mobile menu function
        function showMobileMenu() {
            const menu = `
    <div class="bottom-sheet open" id="mobileMenuSheet">
        <div class="swipe-handle"></div>
        <div class="bottom-sheet-content">
            <h4 class="mb-3"><i class="fas fa-bars me-2"></i>Menu</h4>
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary" onclick="recenterMap()">
                    <i class="fas fa-crosshairs me-2"></i>Recenter Map
                </button>
                <button class="btn btn-outline-success" onclick="toggleEditingTools()">
                    <i class="fas fa-edit me-2"></i>Editing Tools
                </button>
                <button class="btn btn-outline-danger" onclick="showDeleteModal()">
                    <i class="fas fa-trash-alt me-2"></i>Delete Feature
                </button>
                <button class="btn btn-outline-secondary" onclick="toggleFullscreen()">
                    <i class="fas fa-expand me-2"></i>Fullscreen
                </button>
                <button class="btn btn-outline-info" onclick="shareLocation()">
                    <i class="fas fa-share me-2"></i>Share Location
                </button>
                <button class="btn btn-outline-warning" onclick="clearAll()">
                    <i class="fas fa-broom me-2"></i>Clear All
                </button>
            </div>
            <button class="btn btn-secondary w-100 mt-3" onclick="closeMobileMenu()">
                Close
            </button>
        </div>
    </div>
    `;
            document.body.insertAdjacentHTML('beforeend', menu);
        }

        // Function to show delete modal from mobile
        function showDeleteModal() {
            closeMobileMenu();
            $("#deleteModal").modal("show");
        }

        console.log("Mobile-Optimized Map Application with Complete Editing Tools Loaded Successfully");
    </script>
@endsection
