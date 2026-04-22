<!-- resources/views/surveyor/ward-map.blade.php -->
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Base Map Styles */
        #map {
            width: 100%;
            height: 90vh;
            border-radius: 10px;
            border: 2px solid #ddd;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 12px 20px;
            font-weight: 600;
        }

        .card-header i {
            margin-right: 8px;
        }

        .card-body {
            padding: 20px;
            background: #fafafa;
        }

        /* Form Control Focus */
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* File Input Styling */
        input[type="file"] {
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
        }

        input[type="file"]:hover {
            border-color: #667eea;
        }

        /* Shop Item Styles */
        .shop-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            position: relative;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease;
        }

        .shop-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .remove-shop-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            font-size: 14px;
        }

        .remove-shop-btn:hover {
            background: #c82333;
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        }

        .remove-shop-btn:active {
            transform: scale(0.95);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .shop-item-removing {
            animation: slideOut 0.3s ease forwards;
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

            .mobile-editing-tools.show {
                display: block !important;
            }

            .mobile-tool-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                padding: 5px;
            }

            .mobile-tool-grid select {
                width: 100%;
                padding: 10px;
                border-radius: 8px;
                border: 1px solid #ddd;
                font-size: 14px;
                background: white;
            }

            .modal-dialog {
                margin: 10px;
                max-height: 95vh;
            }

            .modal-body {
                max-height: 70vh;
                overflow-y: auto;
            }
        }

        @media (min-width: 769px) {

            .mobile-toolbar,
            .mobile-search-overlay,
            .bottom-sheet,
            .navigation-header,
            .navigation-instruction,
            .mobile-editing-tools {
                display: none !important;
            }
        }

        .ol-layer div {
            border: none !important;
            outline: none !important;
        }

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

        #featurePreview {
            background-color: #f8f9fa;
            border-color: #dee2e6 !important;
        }

        #previewText {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .btn-danger:disabled {
            opacity: 0.7;
        }

        .ol-layer .ol-delete-highlight {
            stroke: #dc3545;
            stroke-width: 5;
            fill: rgba(220, 53, 69, 0.1);
        }

        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
        }

        .modal-footer .btn {
            border-radius: 8px;
            padding: 8px 20px;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
        }

        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46a0);
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

    <div class="mobile-editing-tools" id="mobileEditingTools">
        <div class="mobile-tool-grid">
            <select class="form-select" id="mobileEditToolSelect">
                <option value="none">Select Tool</option>
                <option value="Polygon">Draw Polygon</option>
                <option value="Line">Draw Line</option>
                <option value="Point">Draw Point</option>
                <option value="Modify">Modify Feature</option>
                <option value="Delete">Delete Feature</option>

            </select>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <div>Calculating route...</div>
        </div>
    </div>

    <div class="navigation-header" id="navigationHeader">
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
        <div id="editForms" class="mt-3"></div>
    </div>

    <div class="modal fade" id="pointModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Point Data Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="pointForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Basic Information Card -->
                        <div class="card mb-3">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Assessment Type <span
                                                class="text-danger">*</span></label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="OLD">OLD</option>
                                            <option value="NEW">NEW</option>
                                            <option value="OTHER">OTHER WARD</option>
                                        </select>
                                        <div id="type_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3" id="suveyedbtn"></div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pointgis" class="form-label">GIS ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="pointgis" name="point_gisid"
                                            readonly>
                                        <div id="point_gisid_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="assessment" class="form-label">Assessment No <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="assessment" class="form-control" id="assessment">
                                        <div id="assessment_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="old_assessment" class="form-label">Old Assessment</label>
                                        <input type="text" name="old_assessment" class="form-control"
                                            id="old_assessment">
                                        <div id="old_assessment_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="owner_name" class="form-label">Owner Name</label>
                                        <input type="text" name="owner_name" class="form-control" id="owner_name">
                                        <div id="owner_name_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="present_owner_name" class="form-label">Present Owner Name</label>
                                        <input type="text" name="present_owner_name" class="form-control"
                                            id="present_owner_name">
                                        <div id="present_owner_name_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="no_of_shop" class="form-label">Number of Shops</label>
                                        <input type="number" name="no_of_shop" class="form-control" id="no_of_shop"
                                            min="0" step="1" value="0">
                                        <div id="no_of_shop_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="number_persons" class="form-label">Number persons</label>
                                        <input type="number" name="number_persons" class="form-control"
                                            id="number_persons" min="0" step="1" value="0">
                                        <div id="number_persons_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Details Card -->
                        <div class="card mb-3">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                                <h6 class="mb-0"><i class="fas fa-building"></i> Property Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="floor" class="form-label">Floor</label>
                                        <input type="number" name="floor" class="form-control" id="floor"
                                            min="0" step="1">
                                        <div id="floor_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="old_door_no" class="form-label">Old Door No</label>
                                        <input type="text" name="old_door_no" class="form-control" id="old_door_no">
                                        <div id="old_door_no_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="new_door_no" class="form-label">New Door No</label>
                                        <input type="text" name="new_door_no" class="form-control" id="new_door_no">
                                        <div id="new_door_no_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="bill_usage" class="form-label">Bill Usage</label>
                                        <select name="bill_usage" id="bill_usage" class="form-control">
                                            <option value="">Select Bill Usage</option>
                                            <option value="Residential">Residential</option>
                                            <option value="Commercial">Commercial</option>
                                            <option value="Mixed">Mixed</option>
                                        </select>
                                        <div id="bill_usage_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="eb" class="form-label">EB Number</label>
                                        <input type="text" name="eb" class="form-control" id="eb">
                                        <div id="eb_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tax Details Card -->
                        <div class="card mb-3">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;">
                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Tax Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="water_tax" class="form-label">Water Tax</label>
                                        <input type="text" name="water_tax" class="form-control" id="water_tax"
                                            step="0.01" min="0">
                                        <div id="water_tax_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="old_water_tax" class="form-label">Old Water Tax</label>
                                        <input type="text" name="old_water_tax" class="form-control"
                                            id="old_water_tax" step="0.01" min="0">
                                        <div id="old_water_tax_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="professional_tax" class="form-label">Professional Tax</label>
                                        <input type="text" name="professional_tax" class="form-control"
                                            id="professional_tax">
                                        <div id="professional_tax_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="gst" class="form-label">GST</label>
                                        <input type="text" name="gst" class="form-control" id="gst"
                                            placeholder="GST Number">
                                        <div id="gst_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="trade_income" class="form-label">Trade Income</label>
                                        <input type="number" name="trade_income" class="form-control" id="trade_income"
                                            step="0.01" min="0">
                                        <div id="trade_income_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Card -->
                        <div class="card mb-3">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #17a2b8, #138496); color: white;">
                                <h6 class="mb-0"><i class="fas fa-id-card"></i> Documents & Contact</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="aadhar_no" class="form-label">Aadhar Number</label>
                                        <input type="text" name="aadhar_no" class="form-control" id="aadhar_no"
                                            maxlength="12" pattern="[0-9]{12}" placeholder="12-digit Aadhar">
                                        <div id="aadhar_no_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ration_no" class="form-label">Ration Number</label>
                                        <input type="text" name="ration_no" class="form-control" id="ration_no">
                                        <div id="ration_no_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" name="phone_number" class="form-control" id="phone"
                                            pattern="[0-9]{10}" maxlength="10" placeholder="10-digit mobile">
                                        <div id="phone_number_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quality Check Card -->
                        <div class="card mb-3 d-none" id="qualityCheckCard">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white;">
                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Quality Check</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="qc_area" class="form-label">QC Area</label>
                                        <input type="text" name="qc_area" class="form-control" id="qc_area">
                                        <div id="qc_area_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="qc_usage" class="form-label">QC Usage</label>
                                        <select name="qc_usage" id="qc_usage" class="form-control">
                                            <option value="">Select Usage</option>
                                            <option value="Residential">Residential</option>
                                            <option value="Commercial">Commercial</option>
                                            <option value="Mixed">Mixed</option>
                                        </select>
                                        <div id="qc_usage_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="qc_name" class="form-label">QC Name</label>
                                        <input type="text" name="qc_name" class="form-control" id="qc_name"
                                            placeholder="QC Officer Name">
                                        <div id="qc_name_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="qc_remarks" class="form-label">QC Remarks</label>
                                        <input type="text" name="qc_remarks" class="form-control" id="qc_remarks">
                                        <div id="qc_remarks_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Card -->
                        <div class="card mb-3">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;">
                                <h6 class="mb-0"><i class="fas fa-comment"></i> Remarks</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="establishment_remarks" class="form-label">Establishment
                                            Remarks</label>
                                        <textarea name="establishment_remarks" class="form-control" id="establishment_remarks" rows="3"
                                            placeholder="Enter establishment remarks..."></textarea>
                                        <div id="establishment_remarks_error" class="error-message text-danger"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="remarks" class="form-label">Office Remarks</label>
                                        <textarea name="remarks" class="form-control" id="remarks" rows="3" placeholder="Enter general remarks..."></textarea>
                                        <div id="remarks_error" class="error-message text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Shop Details Append Area -->
                        <div id="append"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
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

    <div class="modal fade" id="buildingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Building Data Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="buildingForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="gisIdInput" name="gisid">
                    <div class="modal-body">
                        <!-- Two Image Previews Side by Side -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="fw-bold mb-2">Image 1 Preview</label>
                                <div class="border rounded p-2" style="background: #f8f9fa; min-height: 200px;">
                                    <img id="buildingImagePreview" src="" alt="Building Image Preview"
                                        class="img-fluid"
                                        style="display: none; max-height: 250px; width: 100%; object-fit: contain; border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold mb-2">Image 2 Preview</label>
                                <div class="border rounded p-2" style="background: #f8f9fa; min-height: 200px;">
                                    <img id="buildingImagePreview2" src="" alt="Building Image Preview 2"
                                        class="img-fluid"
                                        style="display: none; max-height: 250px; width: 100%; object-fit: contain; border-radius: 8px;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Gisid</label>
                                <input type="text" class="form-control" name="building_gisid" id="building_gisid"
                                    readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Number of Bills</label>
                                <input type="number" class="form-control" name="number_bill" id="number_bill">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Number of Shops</label>
                                <input type="number" class="form-control" name="number_shop" id="number_shop">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Number of Floors</label>
                                <input type="number" class="form-control" name="number_floor" id="number_floor">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Building Name</label>
                                <input type="text" class="form-control" name="building_name" id="building_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Road Name</label>
                                <select class="form-control" id="point_road_name" name="road_name">
                                    <option value="">Select Road Name</option>
                                    @if (isset($uniqueRoadNames))
                                        @foreach ($uniqueRoadNames as $roadName)
                                            <option value="{{ $roadName }}">{{ $roadName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone_building">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Building Usage</label>
                                <select class="form-control" name="building_usage" id="building_usage">
                                    <option value="">Select</option>
                                    <option value="RESIDENTIAL">Residential</option>
                                    <option value="COMMERCIAL">Commercial</option>
                                    <option value="INDUSTRIAL">Industrial</option>
                                    <option value="INSTITUTIONAL">Institutional</option>
                                    <option value="MIXED">Mixed</option>
                                    <option value="GOVERNMENT">Government</option>
                                    <option value="VACANT">Vacant</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Construction Type</label>
                                <select class="form-control" name="construction_type" id="construction_type">
                                    <option value="">Select</option>
                                    <option value="PERMANENT">Permanent</option>
                                    <option value="SEMI_PERMANENT">Semi Permanent</option>
                                    <option value="VACANT_LAND">Vaccant Land</option>
                                    <option value="SHED">Shed</option>
                                    <option value="CAR_SHED">Car Shed</option>
                                    <option value="TEMPORARY">Temporary</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Building Type</label>
                                <select class="form-control" name="building_type" id="building_type">
                                    <option value="">Select</option>
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
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>UGD</label>
                                <select class="form-control" name="ugd" id="ugd">
                                    <option value="">Select</option>
                                    <option value="No_Connection">No Connection</option>
                                    <option value="Manhole_Available_but_Connection_Not_Given_to_House">Manhole Available
                                        but Connection Not Given to House</option>
                                    <option value="Stage_1_Completed">Stage 1 Completed</option>
                                    <option value="Stage_1_2_Completed">Stage 1, 2 Completed</option>
                                    <option value="Stage_1_2_Completed_but_Not_Connected">Stage 1, 2 Completed but Not
                                        Connected</option>
                                    <option value="Stage_1_2_3_Completed">Stage 1, 2, 3 Completed</option>
                                    <option value="Direct_Connection_Given">Direct Connection Given</option>
                                    <option value="1_UGD_Connection_-_3_Stage_Completed">1 UGD Connection - 3 Stage
                                        Completed</option>
                                    <option value="2_UGD_Connection_-_3_Stage_Completed">2 UGD Connection - 3 Stage
                                        Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Lift Room</label>
                                <select class="form-control" name="liftroom" id="liftroom">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Head Room</label>
                                <select class="form-control" name="headroom" id="headroom">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Overhead Tank</label>
                                <select class="form-control" name="overhead_tank" id="overhead_tank">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Rainwater Harvesting</label>
                                <select class="form-control" name="rainwater_harvesting" id="rainwater_harvesting">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Parking</label>
                                <select class="form-control" name="parking" id="parking">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Ramp</label>
                                <select class="form-control" name="ramp" id="ramp">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Hoarding</label>
                                <select class="form-control" name="hoarding" id="hoarding">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>CCTV</label>
                                <select class="form-control" name="cctv" id="cctv">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Cell Tower</label>
                                <select class="form-control" name="cell_tower" id="cell_tower">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Solar Panel</label>
                                <select class="form-control" name="solar_panel" id="solar_panel">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Basement</label>
                                <input type="number" class="form-control" name="basement" id="basement">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Water Connection</label>
                                <input type="text" class="form-control" name="water_connection"
                                    id="water_connection">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Percentage</label>
                                <select class="form-control mt-2" name="percentage" id="percentage">
                                    <option value=""></option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                    <option value="60">60</option>
                                    <option value="70">70</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Upload Image</label>
                                <input type="file" class="form-control" name="image" id="building_image"
                                    accept="image/*">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Upload Image2</label>
                                <input type="file" class="form-control" name="image2" id="building_image2"
                                    accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks_building"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Corporation Remarks</label>
                                <textarea class="form-control" name="corporationremarks" id="corporationremarks"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="buildingsubmitBtn">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                                <option value="Polygon">Polygon</option>
                                <option value="Point">Point</option>
                            </select>
                        </div>
                        <div id="featurePreview" class="mt-3 p-3 border rounded" style="display: none;">
                            <h6>Feature Details:</h6>
                            <p id="previewText">No feature selected</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="fas fa-trash-alt me-2"></i>Delete Feature
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="lineSubmit">
                            <i class="fas fa-save me-2"></i>Update Road Name
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Global variables
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let mis = @json($misData ?? []);

            let routes = {
                surveyorPolygonDatasUpload: "{{ route('surveyor.polygon.datas.upload') }}",
                surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}",
                updateRoadName: "{{ route('surveyor.update.road.name') }}",
                delgisid: "{{ route('surveyor.delgisid') }}",
                addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
                addLineFeature: "{{ route('surveyor.add.line.feature') }}",
                addPointFeature: "{{ route('surveyor.add.point.feature') }}",
                surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}",
                deleteFeature: "{{ route('surveyor.delete.feature') }}"
            };

            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            let currentLocationMarker = null;
            let locationWatchId = null;
            let isLiveLocationActive = false;
            let currentRoute = null;
            let routeSteps = [];
            let currentStepIndex = 0;
            let navigationMode = false;
            let navigationInterval = null;
            let isMobile = $(window).width() <= 768;
            let draw = null;
            let modify = null;
            let select = null;
            let isModifyMode = false;
            let selectedFeature = null;
            let isDrawingActive = false;
            let featureClickHandler = null;

            // Shop details variables
            let shopTimeout = null;
            let currentShopCount = 0;

            // Style Functions
            function createPointStyle(feature) {
                const gisid = feature.get("gisid");

                const pointCount = pointDatas.filter(data => data.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(data => data.gisid == gisid);

                let color = "blue"; // default

                if (polygonData) {
                    if (pointCount > 0) {
                        color = (polygonData.number_bill == pointCount) ? "green" : "red";
                    } else {
                        color = "blue";
                    }
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
                const polygonData = polygonDatas.find(data => data.gisid == gisid);
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

            function createHumanLocationMarkerStyle() {
                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 12,
                        fill: new ol.style.Fill({
                            color: 'rgba(0, 150, 255, 0.8)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#fff',
                            width: 3
                        })
                    }),
                    text: new ol.style.Text({
                        text: '👤',
                        font: 'bold 18px Arial',
                        fill: new ol.style.Fill({
                            color: '#fff'
                        }),
                        offsetY: -15
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

            // Layer Definitions
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

            // Polygon Layer
            const polygonSource = new ol.source.Vector();
            polygons.forEach(poly => {
                try {
                    let coords = JSON.parse(poly.coordinates);
                    polygonSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid,
                        type: "Polygon"
                    }));
                } catch (e) {
                    console.error('Polygon parse error:', e);
                }
            });
            const polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: createPolygonStyle,
                visible: true
            });

            // Line Layer
            const lineSource = new ol.source.Vector();
            lines.forEach(l => {
                try {
                    let coords;
                    if (typeof l.coordinates === 'string') {
                        coords = JSON.parse(l.coordinates);
                    } else if (Array.isArray(l.coordinates)) {
                        coords = l.coordinates;
                    } else {
                        console.warn('Invalid coordinates format for line:', l.gisid);
                        return;
                    }

                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                        .isArray(coords[0][0])) {
                        coords = coords[0];
                    }

                    if (!coords || coords.length < 2) {
                        console.warn('Line needs at least 2 coordinates:', l.gisid);
                        return;
                    }

                    const isValid = coords.every(coord =>
                        Array.isArray(coord) && coord.length >= 2 &&
                        typeof coord[0] === 'number' && typeof coord[1] === 'number' &&
                        !isNaN(coord[0]) && !isNaN(coord[1])
                    );

                    if (!isValid) {
                        console.warn('Invalid coordinate values for line:', l.gisid);
                        return;
                    }

                    lineSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: l.gisid,
                        type: "Line",
                        road_name: l.road_name || null
                    }));
                } catch (e) {
                    console.error('Line parse error:', e, l);
                }
            });
            console.log(`Loaded ${lineSource.getFeatures().length} line features`);
            const lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: createLineStyle,
                visible: true
            });

            // Point Layer
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
            const pointLayer = new ol.layer.Vector({
                source: pointSource,
                style: createPointStyle,
                visible: true
            });

            // Boundary Layer
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
                style: createHumanLocationMarkerStyle
            });

            // Map Initialization
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, terrainLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer,
                    lineLayer, pointLayer, highlightLayer, routeLayer, locationLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Fit view to show all features
            function fitViewToAllFeatures() {
                const extent = ol.extent.createEmpty();
                lineSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                polygonSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                pointSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                if (!ol.extent.isEmpty(extent)) {
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }
            setTimeout(fitViewToAllFeatures, 500);

            // Shop Details Functions - FIXED VERSION
            // Shop Details Functions - FIXED VERSION
            function addShopForm(shopNumber, container) {
                const shopHtml = `
        <div class="shop-item mb-4 p-3 border rounded position-relative" data-shop-index="${shopNumber}" style="background: #f8f9fa; transition: all 0.3s ease;">
            <button type="button" class="remove-shop-btn" data-shop-id="${shopNumber}" style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; z-index: 10;">
                <i class="fas fa-times"></i>
            </button>
            <h6 class="mb-3 text-primary" style="color: #4a6ee0 !important; font-weight: 600;">
                <i class="fas fa-store me-2"></i>Shop #${shopNumber}
            </h6>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Shop Floor</label>
                    <input type="number" name="shop_floor_${shopNumber}" class="form-control shop-floor" placeholder="Ground/First/Second" style="border-radius: 8px;">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Shop Name</label>
                    <input type="text" name="shop_name_${shopNumber}" class="form-control shop-name" placeholder="Enter shop name" style="border-radius: 8px;">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Shop Owner Name</label>
                    <input type="text" name="shop_owner_name_${shopNumber}" class="form-control shop-owner-name" placeholder="Owner name" style="border-radius: 8px;">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Shop Category</label>
                    <select name="shop_category_${shopNumber}" class="form-control shop-category" style="border-radius: 8px;">
                        <option value="">Select Category</option>
                        <option value="Grocery">Grocery</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Pharmacy">Pharmacy</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Stationery">Stationery</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Shop Mobile</label>
                    <input type="tel" name="shop_mobile_${shopNumber}" class="form-control shop-mobile" maxlength="10" pattern="[0-9]{10}" placeholder="10-digit number" style="border-radius: 8px;">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">License Number</label>
                    <input type="text" name="license_${shopNumber}" class="form-control shop-license" placeholder="Trade License No" style="border-radius: 8px;">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Number of Employees</label>
                    <input type="number" name="number_of_employee_${shopNumber}" class="form-control shop-employees" min="0" step="1" placeholder="0" style="border-radius: 8px;">
                </div>
            </div>
        </div>
    `;

                container.append(shopHtml);

                // Add smooth animation
                const newShop = container.children().last();
                newShop.hide().fadeIn(300);
            }

            function removeShopForm(shopNumber, container) {
                const shopElement = container.find(`.shop-item[data-shop-index="${shopNumber}"]`);
                if (shopElement.length) {
                    // Smooth removal animation
                    shopElement.fadeOut(300, function() {
                        $(this).remove();
                        // Renumber remaining shops after removal
                        renumberShops(container);
                        // Update the current shop count
                        updateCurrentShopCount();
                    });
                }
            }

            function renumberShops(container) {
                const shops = container.find('.shop-item');
                shops.each(function(index) {
                    const newNumber = index + 1;
                    const $this = $(this);

                    // Update data attribute
                    $this.attr('data-shop-index', newNumber);

                    // Update heading
                    $this.find('h6').html(`<i class="fas fa-store me-2"></i>Shop #${newNumber}`);

                    // Update all input names
                    $this.find('input, select').each(function() {
                        const $input = $(this);
                        const name = $input.attr('name');
                        if (name) {
                            // Extract the base name without the number
                            const baseName = name.replace(/_\d+$/, '');
                            const newName = `${baseName}_${newNumber}`;
                            $input.attr('name', newName);
                        }
                    });

                    // Update remove button data attribute
                    $this.find('.remove-shop-btn').attr('data-shop-id', newNumber);
                });
            }

            function updateCurrentShopCount() {
                const container = $('#shopDetailsContainer');
                if (container.length) {
                    const shopCount = container.find('.shop-item').length;
                    currentShopCount = shopCount;
                    $('#no_of_shop').val(shopCount);

                    // Update header text
                    const header = $('#append').find('.card-header h6');
                    if (header.length) {
                        header.html(
                            `<i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount !== 1 ? 's' : ''})`
                        );
                    }

                    // Remove card if no shops left
                    if (shopCount === 0) {
                        $('#append').find('.card.mb-3').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                }
            }

            function generateShopForms(shopCount) {
                const appendArea = $('#append');

                // Don't regenerate if count hasn't changed
                if (currentShopCount === shopCount) return;

                if (shopCount === 0) {
                    // Remove all shop forms with animation
                    const container = $('#shopDetailsContainer');
                    if (container.length) {
                        const shops = container.find('.shop-item');
                        if (shops.length > 0) {
                            shops.fadeOut(300, function() {
                                container.empty();
                                currentShopCount = 0;
                                $('#no_of_shop').val(0);
                                // Remove the entire card
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

                // Check if container exists
                let container = $('#shopDetailsContainer');
                if (container.length === 0) {
                    // Create new shop card
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

                    // Add add button handler
                    $('#addAllShopsBtn').off('click').on('click', function() {
                        const newCount = currentShopCount + 1;
                        $('#no_of_shop').val(newCount).trigger('change');
                    });
                }

                // Add new shops
                if (shopCount > currentShopCount) {
                    for (let i = currentShopCount + 1; i <= shopCount; i++) {
                        addShopForm(i, container);
                    }
                }
                // Remove shops if count decreased
                else if (shopCount < currentShopCount) {
                    for (let i = currentShopCount; i > shopCount; i--) {
                        removeShopForm(i, container);
                    }
                }

                currentShopCount = shopCount;

                // Update header text
                const header = appendArea.find('.card-header h6');
                if (header.length) {
                    header.html(
                        `<i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount !== 1 ? 's' : ''})`
                    );
                }
            }

            function initDynamicShopDetails() {
                $('#no_of_shop').off('change keyup').on('change keyup', function() {
                    // Clear previous timeout
                    if (shopTimeout) clearTimeout(shopTimeout);

                    // Use timeout to debounce
                    shopTimeout = setTimeout(() => {
                        let shopCount = parseInt($(this).val()) || 0;
                        if (shopCount < 0) shopCount = 0;
                        if ($(this).val() !== shopCount.toString()) {
                            $(this).val(shopCount);
                        }
                        generateShopForms(shopCount);
                    }, 300);
                });

                // Event delegation for remove buttons
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

            // Update resetPointFormFields to properly clear shops
            function resetPointFormFields() {
                $("#pointgis, #assessment, #old_assessment, #owner_name, #present_owner_name, #worker_name, #building_data_id")
                    .val("");
                $("#floor, #old_door_no, #new_door_no, #plot_area, #eb, #otsarea").val("");
                $("#water_tax, #old_water_tax, #halfyeartax, #balance, #professional_tax, #gst, #trade_income").val(
                    "");
                $("#aadhar_no, #ration_no, #phone").val("");
                $("#qc_area, #qc_name, #qc_remarks").val("");
                $("#establishment_remarks, #remarks").val("");
                $("#type").val("OLD");
                $("#bill_usage, #shop_category, #qc_usage").val("");

                // Clear shop details properly
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


            function resetBuildingForm() {
                $("#building_gisid").val("");
                $("#number_bill").val("");
                $("#number_shop").val("");
                $("#number_floor").val("");
                $("#building_name").val("");
                $("#point_road_name").val("");
                $("#phone_building").val("");
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
                $("#water_connection").val("");
                $("#percentage").val("");
                $("#remarks_building").val("");
                $("#corporationremarks").val("");

                // Reset both image previews
                $("#buildingImagePreview").hide().attr("src", "");
                $("#buildingImagePreview2").hide().attr("src", "");

                // Reset file inputs
                $("#building_image").val("");
                $("#building_image2").val("");

                // Clear any error messages
                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
            }

            // LIVE PREVIEW for first image when user selects a file
            $("#building_image").on("change", function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#buildingImagePreview").attr("src", e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no file selected, check if there was existing image
                    const existingImage = $("#building_gisid").val();
                    if (!existingImage) {
                        $("#buildingImagePreview").hide();
                    }
                }
            });

            // LIVE PREVIEW for second image when user selects a file
            $("#building_image2").on("change", function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#buildingImagePreview2").attr("src", e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no file selected, check if there was existing image
                    const existingImage = $("#building_gisid").val();
                    if (!existingImage) {
                        $("#buildingImagePreview2").hide();
                    }
                }
            });

            // Optional: Clear preview when modal is closed
            $("#buildingModal").on("hidden.bs.modal", function() {
                resetBuildingForm();
            });

            function populateBuildingForm(item) {
                // Basic Information
                $("#building_gisid").val(item.gisid || "");
                $("#number_bill").val(item.number_bill || "");
                $("#number_shop").val(item.number_shop || "");
                $("#number_floor").val(item.number_floor || "");
                $("#building_name").val(item.building_name || "");
                $("#point_road_name").val(item.road_name || "");
                $("#phone_building").val(item.phone || "");

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
                $("#water_connection").val(item.water_connection || "");
                $("#percentage").val(item.percentage || "");

                // Remarks
                $("#remarks_building").val(item.remarks || "");
                $("#corporationremarks").val(item.corporationremarks || "");

                // Image Previews
                // Define asset base URL (make sure this is defined globally)
                const assetUrl = window.assetUrl || "{{ asset('') }}";

                // Show existing first image if exists
                if (item.image && item.image !== "") {
                    const imageUrl = item.image.startsWith('http') ? item.image : assetUrl + item.image;
                    $("#buildingImagePreview").attr("src", imageUrl).show();
                } else {
                    $("#buildingImagePreview").hide().attr("src", "");
                }

                // Show existing second image if exists
                if (item.image2 && item.image2 !== "") {
                    const imageUrl2 = item.image2.startsWith('http') ? item.image2 : assetUrl + item.image2;
                    $("#buildingImagePreview2").attr("src", imageUrl2).show();
                } else {
                    $("#buildingImagePreview2").hide().attr("src", "");
                }
            }
            // Editing Tools Functions
            function removeDrawInteractions() {
                map.getInteractions().forEach((interaction) => {
                    if (interaction instanceof ol.interaction.Draw ||
                        interaction instanceof ol.interaction.Modify ||
                        interaction instanceof ol.interaction.Select) {
                        map.removeInteraction(interaction);
                    }
                });
                isModifyMode = false;
                isDrawingActive = false;
                enableFeatureClickHandler();
            }

            function disableFeatureClickHandler() {
                if (featureClickHandler) map.un('click', featureClickHandler);
            }

            function enableFeatureClickHandler() {
                if (featureClickHandler && !isModifyMode && !isDrawingActive) map.on('click', featureClickHandler);
            }

            function activateDrawPolygon() {
                disableFeatureClickHandler();
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
                            $("#editToolSelect, #mobileEditToolSelect").val("none");
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
                disableFeatureClickHandler();
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
                            removeDrawInteractions();
                            $("#editToolSelect, #mobileEditToolSelect").val("none");
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateDrawPoint() {
                disableFeatureClickHandler();
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
                            removeDrawInteractions();
                            $("#editToolSelect, #mobileEditToolSelect").val("none");
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
                disableFeatureClickHandler();
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
                $("#editToolSelect, #mobileEditToolSelect").val("none");
                $("#deleteModal").modal("show");
            }

            // Click Handlers
            function handlePolygonClick(properties) {
                const gisId = properties["gisid"];
                resetBuildingForm();

                $("#building_gisid").val(gisId);

                let existingData = null;
                if (polygonDatas && polygonDatas.length > 0) {
                    existingData = polygonDatas.find(item => item.gisid == gisId);
                }

                if (existingData) {
                    populateBuildingForm(existingData);
                    showFlashMessage('Loading existing building data...', 'info');
                } else {
                    $("#buildingImagePreview").hide().attr("src", "");
                    showFlashMessage('Creating new building record...', 'info');
                }

                $("#buildingModal").modal("show");
            }

            function handlePointClick(properties) {
                const gisid = properties["gisid"];
                resetPointFormFields();
                const polygonData = polygonDatas.find(data => data.gisid === gisid);
                const polygonNumOfBill = polygonData ? polygonData.number_bill : null;
                const matchingPointsCount = pointDatas.filter(data => data.point_gisid === gisid).length;

                if (polygonNumOfBill > matchingPointsCount) {
                    $("#pointgis").val(gisid);
                    $("#pointModal").modal("show");
                } else {
                    showFlashMessage(`Already this building have ${matchingPointsCount} bills`, "error");
                }
            }

            function handleLineClick(properties) {
                const gisid = properties["gisid"];
                if (gisid) {
                    $("#linegisid").val(gisid);
                    $("#lineRoadName").val(properties["road_name"] || "");
                    $("#lineModal").modal("show");
                }
            }

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
                        else if (geometryType === "LineString" || geometryType === "MultiLineString")
                            handleLineClick(properties);
                    }
                };
                map.on('click', featureClickHandler);
            }

            // Search and Route Functions
            function searchGISID(gisid, isMobileFlag = false) {
                const searchResults = isMobileFlag ? $('#mobileSearchResults') : $('#searchResults');
                searchResults.empty();
                highlightSource.clear();
                routeSource.clear();
                currentRoute = null;
                selectedFeature = null;
                const allSources = [pointSource, lineSource, polygonSource];
                let foundFeatures = [];
                allSources.forEach(source => {
                    source.forEachFeature(feature => {
                        if (feature.get('gisid') && feature.get('gisid').toString() === gisid)
                            foundFeatures.push(feature);
                    });
                });
                if (foundFeatures.length > 0) {
                    searchResults.show();
                    foundFeatures.forEach(feature => {
                        const resultItem = $('<div>').addClass('search-result-item').html(
                            `<strong>GIS ID:</strong> ${feature.get('gisid')}<br><strong>Type:</strong> ${feature.get('type')}`
                        );
                        resultItem.on('click', function() {
                            selectedFeature = feature;
                            highlightAndZoomToFeature(feature, isMobileFlag);
                            if (isMobileFlag) $('#mobileSearchOverlay').hide();
                            searchResults.hide();
                            showRouteOptions(isMobileFlag);
                        });
                        searchResults.append(resultItem);
                    });
                } else {
                    searchResults.show();
                    const noResult = $('<div>').addClass('search-result-item').text(
                        'No features found with this GIS ID');
                    searchResults.append(noResult);
                }
            }

            function highlightAndZoomToFeature(feature, isMobileFlag = false) {
                highlightSource.clear();
                routeSource.clear();
                highlightSource.addFeature(feature.clone());
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
                if (!isMobileFlag) showFeatureInfo(feature);
            }

            function showRouteOptions(isMobileFlag = false) {
                if (isMobileFlag) {
                    const sheet = $('#routeBottomSheet');
                    const content = sheet.find('.bottom-sheet-content');
                    content.html(
                        `<div class="swipe-handle"></div><h4 class="mb-3"><i class="fas fa-route me-2"></i>Route Options</h4><p>Do you want to calculate route to <strong>${selectedFeature?.get('gisid')}</strong>?</p><button class="btn btn-primary w-100 mb-2" id="calculateRouteBtn"><i class="fas fa-calculator me-2"></i>Calculate Route</button><button class="btn btn-secondary w-100" id="cancelRouteBtn">Cancel</button>`
                    );
                    $('#calculateRouteBtn').on('click', function() {
                        calculateRouteToSelectedFeature();
                    });
                    $('#cancelRouteBtn').on('click', function() {
                        closeRouteOptions();
                    });
                    sheet.addClass('open');
                } else {
                    closeRouteOptions();
                    const routePanel = $(`
                        <div id="routeOptionsPanel" class="position-absolute bg-white p-3 rounded shadow" style="top: 80px; left: 330px; z-index: 1000;">
                            <h5><i class="fas fa-route me-2"></i>Calculate Route</h5>
                            <p>Do you want to calculate route to this location?</p>
                            <button class="btn btn-primary me-2" id="calculateRouteBtnDesktop"><i class="fas fa-calculator me-2"></i>Calculate Route</button>
                            <button class="btn btn-secondary" id="cancelRouteBtnDesktop">Cancel</button>
                        </div>
                    `);
                    $('body').append(routePanel);
                    $('#calculateRouteBtnDesktop').on('click', function() {
                        calculateRouteToSelectedFeature();
                    });
                    $('#cancelRouteBtnDesktop').on('click', function() {
                        closeRouteOptions();
                    });
                }
            }

            window.calculateRouteToSelectedFeature = async function() {
                if (!selectedFeature) {
                    showFlashMessage('No feature selected. Please search for a GIS ID first.', 'error');
                    return;
                }
                if (!currentLocationMarker) {
                    const enableLocation = confirm(
                        'Live location is not enabled. Would you like to enable it for route calculation?'
                    );
                    if (enableLocation) {
                        toggleLiveLocation();
                        setTimeout(async () => {
                            if (currentLocationMarker) await calculateAndDisplayRoute(
                                selectedFeature);
                            else showFlashMessage(
                                'Unable to get your location. Please enable location services.',
                                'error');
                        }, 2000);
                    }
                    return;
                }
                await calculateAndDisplayRoute(selectedFeature);
            };

            window.closeRouteOptions = function() {
                $('#routeOptionsPanel').remove();
                const sheet = $('#routeBottomSheet');
                if (sheet.length) {
                    sheet.removeClass('open');
                    const content = sheet.find('.bottom-sheet-content');
                    content.html(
                        `<div class="swipe-handle"></div><h4 class="mb-3"><i class="fas fa-route me-2"></i>Route Information</h4><div id="mobileRouteSummary" class="route-summary"></div><div id="mobileDirectionsList" class="directions-list"></div><button class="btn btn-primary w-100 mt-3" id="startNavigationFromSheet"><i class="fas fa-play me-2"></i>Start Navigation</button><button class="btn btn-outline-secondary w-100 mt-2" id="closeRouteSheet">Close</button>`
                    );
                    $('#startNavigationFromSheet').off('click').on('click', startNavigation);
                    $('#closeRouteSheet').off('click').on('click', function() {
                        sheet.removeClass('open');
                    });
                }
            };

            async function calculateAndDisplayRoute(feature) {
                $('#loadingSpinner').show();
                try {
                    const currentCoords = currentLocationMarker.getGeometry().getCoordinates();
                    const geometry = feature.getGeometry();
                    const targetCoords = geometry.getType() === 'Point' ? geometry.getCoordinates() : ol.extent
                        .getCenter(geometry.getExtent());
                    const currentLonLat = ol.proj.toLonLat(currentCoords);
                    const targetLonLat = ol.proj.toLonLat(targetCoords);
                    const route = await calculateEnhancedRoute(currentLonLat, targetLonLat,
                        `GIS ID: ${feature.get('gisid')}`);
                    currentRoute = route;
                    if (isMobile) $('#routeBottomSheet').addClass('open');
                    else $('#navigationControls').show();
                    closeRouteOptions();
                } catch (error) {
                    console.error('Route calculation error:', error);
                    showFlashMessage('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            // Route Functions
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    const [startLon, startLat] = startCoord;
                    const [endLon, endLat] = endCoord;
                    const url =
                        `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) throw new Error(
                        'No route found');
                    return data.routes[0];
                } catch (error) {
                    return getStraightLineRoute(startCoord, endCoord);
                }
            }

            function getStraightLineRoute(startCoord, endCoord) {
                const distance = ol.sphere.getDistance(ol.proj.fromLonLat(startCoord), ol.proj.fromLonLat(
                    endCoord));
                const duration = distance / 1.39;
                return {
                    distance,
                    duration,
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
                            distance,
                            duration
                        }, {
                            maneuver: {
                                type: "arrive",
                                instruction: "Arrive at destination"
                            },
                            distance: 0,
                            duration: 0
                        }]
                    }]
                };
            }

            function formatDistance(meters) {
                return meters < 1000 ? meters.toFixed(0) + ' meters' : (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                const minutes = Math.floor(seconds / 60);
                return minutes < 60 ? minutes + ' min' : Math.floor(minutes / 60) + 'h ' + (minutes % 60) + 'm';
            }

            async function calculateEnhancedRoute(startCoord, endCoord, placeName) {
                try {
                    if (isMobile) $('#mobileRouteSummary').html('<div>Calculating route...</div>');
                    else {
                        $('#desktopRouteSummary').html('<div>Calculating route...</div>');
                        $('#routeInfo').show();
                    }
                    const route = await getRouteFromOSRM(startCoord, endCoord);
                    const totalDistance = route.distance;
                    const totalDuration = route.duration;
                    routeSteps = [];
                    let accumulatedDistance = 0;
                    if (route.legs && route.legs[0] && route.legs[0].steps) {
                        route.legs[0].steps.forEach(step => {
                            accumulatedDistance += step.distance;
                            routeSteps.push({
                                instruction: step.maneuver.instruction || step.maneuver.type,
                                distance: formatDistance(accumulatedDistance),
                                icon: 'fas fa-arrow-up',
                                type: step.maneuver.type
                            });
                        });
                    }
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
                    const distance = ol.sphere.getDistance(ol.proj.fromLonLat(startCoord), ol.proj.fromLonLat(
                        endCoord));
                    const duration = distance / 1.39;
                    routeSteps = [{
                        instruction: "Start from your current location",
                        distance: "0.0 km",
                        icon: "fas fa-play",
                        type: "depart"
                    }, {
                        instruction: "Continue straight to destination",
                        distance: formatDistance(distance),
                        icon: "fas fa-arrow-up",
                        type: "continue"
                    }, {
                        instruction: "Arrive at your destination",
                        distance: formatDistance(distance),
                        icon: "fas fa-flag-checkered",
                        type: "arrive"
                    }];
                    drawRouteOnMap({
                        type: "LineString",
                        coordinates: [startCoord, endCoord]
                    });
                    displayRouteInfo(distance, duration, placeName);
                    return {
                        distance,
                        duration,
                        geometry: {
                            type: "LineString",
                            coordinates: [startCoord, endCoord]
                        },
                        endCoord: endCoord,
                        placeName: placeName
                    };
                }
            }

            function drawRouteOnMap(geometry) {
                const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                routeSource.clear();
                routeSource.addFeature(new ol.Feature({
                    geometry: new ol.geom.LineString(coordinates)
                }));
                if (routeSource.getFeatures().length > 0) {
                    map.getView().fit(routeSource.getFeatures()[0].getGeometry().getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }

            function displayRouteInfo(distance, duration, placeName) {
                const summaryHtml =
                    `<div><strong>Total Distance:</strong> ${formatDistance(distance)}</div><div><strong>Estimated Time:</strong> ${formatDuration(duration)}</div><div><strong>Destination:</strong> ${placeName}</div>`;
                if (isMobile) {
                    $('#mobileRouteSummary').html(summaryHtml);
                    displayTurnByTurnDirections(true);
                } else {
                    $('#desktopRouteSummary').html(summaryHtml);
                    displayTurnByTurnDirections(false);
                    $('#navigationControls').show();
                }
            }

            function displayTurnByTurnDirections(isMobileFlag = false) {
                const directionsList = isMobileFlag ? $('#mobileDirectionsList') : $('#desktopDirectionsList');
                directionsList.empty();
                routeSteps.forEach((step, index) => {
                    const stepElement = $('<div>').addClass('direction-step').html(
                        `<div class="step-number">${index + 1}</div><div class="step-content"><div class="step-instruction"><i class="${step.icon} me-2"></i>${step.instruction}</div><div class="step-distance">${step.distance}</div></div>`
                    );
                    directionsList.append(stepElement);
                });
            }

            // Live Location
            function toggleLiveLocation() {
                if (isLiveLocationActive) {
                    if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                    locationSource.clear();
                    currentLocationMarker = null;
                    isLiveLocationActive = false;
                    if (isMobile) {
                        const btn = $('#mobileLocationBtn');
                        btn.removeClass('active');
                        btn.find('span').text('Location');
                        btn.find('i').attr('class', 'fas fa-location-arrow');
                    } else {
                        const btn = $('#liveLocationBtn');
                        btn.removeClass('active');
                        btn.html('<i class="fas fa-location-arrow me-2"></i>Live Location');
                    }
                    showFlashMessage('Location tracking stopped', 'info');
                    if (navigationMode) {
                        navigationMode = false;
                        if (navigationInterval) clearInterval(navigationInterval);
                    }
                } else {
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported');
                        return;
                    }
                    isLiveLocationActive = true;
                    if (isMobile) {
                        const btn = $('#mobileLocationBtn');
                        btn.addClass('active');
                        btn.find('span').text('Stop');
                        btn.find('i').attr('class', 'fas fa-stop');
                    } else {
                        const btn = $('#liveLocationBtn');
                        btn.addClass('active');
                        btn.html('<i class="fas fa-stop me-2"></i>Stop Location');
                    }
                    showFlashMessage('Getting your current location...', 'info');
                    let hasZoomedToLocation = false;
                    locationWatchId = navigator.geolocation.watchPosition(
                        (position) => {
                            const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords
                                .latitude
                            ]);
                            locationSource.clear();
                            currentLocationMarker = new ol.Feature({
                                geometry: new ol.geom.Point(coords)
                            });
                            locationSource.addFeature(currentLocationMarker);
                            if (!hasZoomedToLocation) {
                                map.getView().animate({
                                    center: coords,
                                    zoom: 19,
                                    duration: 1500
                                });
                                hasZoomedToLocation = true;
                                showFlashMessage('Location found!', 'success');
                            }
                            if (navigationMode && currentRoute) updateRouteIfActive();
                        },
                        (error) => {
                            showFlashMessage('Location error: ' + error.message, 'error');
                            toggleLiveLocation();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                }
            }

            function updateRouteIfActive() {
                if (currentRoute && currentLocationMarker && navigationMode) {
                    const currentCoords = currentLocationMarker.getGeometry().getCoordinates();
                    const currentLonLat = ol.proj.toLonLat(currentCoords);
                    calculateEnhancedRoute(currentLonLat, currentRoute.endCoord, currentRoute.placeName).then(
                        route => currentRoute = route);
                }
            }

            // Navigation Functions
            function startNavigation() {
                if (!currentRoute) {
                    alert('Please calculate a route first');
                    return;
                }
                if (!currentLocationMarker) {
                    alert('Please enable live location first');
                    return;
                }
                navigationMode = true;
                currentStepIndex = 0;
                if (isMobile) {
                    $('#navigationHeader').show();
                    $('#navigationInstruction').show();
                    $('#routeBottomSheet').removeClass('open');
                }
                $('#etaTime').text(formatDuration(currentRoute.duration));
                $('#etaDistance').text(formatDistance(currentRoute.distance));
                $('#destinationAddress').text(currentRoute.placeName);
                updateNavigationInstruction();
                navigationInterval = setInterval(updateNavigationStatus, 5000);
                if (isMobile) {
                    map.getView().animate({
                        center: currentLocationMarker.getGeometry().getCoordinates(),
                        zoom: 18,
                        duration: 1000
                    });
                }
                alert('Navigation started! Follow the route instructions.');
            }

            function updateNavigationInstruction() {
                if (currentStepIndex < routeSteps.length) {
                    $('#instructionText').text(routeSteps[currentStepIndex].instruction);
                    $('#instructionDistance').text(routeSteps[currentStepIndex].distance);
                    $('#instructionIcon').attr('class', routeSteps[currentStepIndex].icon);
                }
            }

            function updateNavigationStatus() {
                if (!navigationMode || !currentRoute) return;
                const progress = Math.min(1, currentStepIndex / (routeSteps.length - 1));
                $('#etaTime').text(formatDuration(currentRoute.duration * (1 - progress)));
                $('#etaDistance').text(formatDistance(currentRoute.distance * (1 - progress)));
                if (Math.random() > 0.7 && currentStepIndex < routeSteps.length - 1) {
                    currentStepIndex++;
                    updateNavigationInstruction();
                }
            }

            // Form Submissions using AJAX
            $("#pointForm").submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const shopCount = parseInt($('#no_of_shop').val()) || 0;
                formData.append('total_shops', shopCount);

                for (let i = 1; i <= shopCount; i++) {
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

                        if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        console.log(xhr);
                        showFlashMessage(errorMsg, "message");
                    }
                    complete: function() {
                        $("#pointSubmit").prop("disabled", false).html(
                            '<i class="fas fa-save me-2"></i>Save Point Data');
                    }
                });
            });

            $("#buildingForm").submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                $("#buildingsubmitBtn").prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                $.ajax({
                    type: "POST",
                    url: routes.surveyorPolygonDatasUpload,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showFlashMessage(response.message, "success");
                        $("#buildingModal").modal("hide");
                        if (response.polygonDatas) polygonDatas = response.polygonDatas;
                        if (response.polygon) polygons = response.polygon;
                        if (response.point) points = response.point;
                        refreshVectorLayer();
                        resetBuildingForm();
                    },
                    error: function(xhr) {
                        let errorMsg = "An error occurred while processing your request.";
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                            .responseJSON.message;
                        showFlashMessage(errorMsg, "error");
                    },
                    complete: function() {
                        $("#buildingsubmitBtn").prop("disabled", false).html(
                            '<i class="fas fa-save me-2"></i>Save');
                    }
                });
            });

            $("#lineForm").submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                $("#lineSubmit").prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

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
                        let errorMsg = "An error occurred while processing your request.";
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                            .responseJSON.message;
                        showFlashMessage(errorMsg, "error");
                    },
                    complete: function() {
                        $("#lineSubmit").prop("disabled", false).html(
                            '<i class="fas fa-save me-2"></i>Update Road Name');
                    }
                });
            });

            // Delete Functionality
            function setupDeleteFunctionality() {
                $("#deleteForm").submit(function(e) {
                    e.preventDefault();
                    const gisid = $("#deleteGisIdInput").val().trim();
                    const featureType = $("#deleteFeatureType").val();
                    if (!gisid) {
                        showFlashMessage("Please enter a GIS ID", "error");
                        return;
                    }
                    $("#confirmDeleteBtn").prop("disabled", true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                    $.ajax({
                        url: routes.deleteFeature,
                        type: "POST",
                        data: {
                            gisid: gisid,
                            feature_type: featureType,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                showFlashMessage(response.message, "success");
                                $("#deleteModal").modal("hide");
                                if (response.polygons) polygons = response.polygons;
                                if (response.lines) lines = response.lines;
                                if (response.points) points = response.points;
                                if (response.polygonDatas) polygonDatas = response.polygonDatas;
                                if (response.pointDatas) pointDatas = response.pointDatas;
                                refreshVectorLayer();
                                highlightSource.clear();
                                $("#deleteForm")[0].reset();
                                $("#featurePreview").hide();
                            } else showFlashMessage(response.message, "error");
                        },
                        error: function(xhr) {
                            let errorMessage = "An error occurred while deleting the feature";
                            if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr
                                .responseJSON.message;
                            showFlashMessage(errorMessage, "error");
                        },
                        complete: function() {
                            $("#confirmDeleteBtn").prop("disabled", false).html(
                                '<i class="fas fa-trash-alt me-2"></i>Delete Feature');
                        }
                    });
                });

                $("#deleteGisIdInput").on('input', function() {
                    const gisid = $(this).val().trim();
                    if (!gisid) {
                        $("#featurePreview").hide();
                        return;
                    }
                    let foundFeature = null,
                        foundType = null;
                    pointSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                            foundFeature = f;
                            foundType = "Point";
                            return true;
                        }
                    });
                    if (!foundFeature) lineSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                            foundFeature = f;
                            foundType = "Line";
                            return true;
                        }
                    });
                    if (!foundFeature) polygonSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                            foundFeature = f;
                            foundType = "Polygon";
                            return true;
                        }
                    });
                    if (foundFeature) {
                        highlightSource.clear();
                        highlightSource.addFeature(foundFeature.clone());
                        $("#featurePreview").show();
                        $("#previewText").html(
                            `<strong>GIS ID:</strong> ${gisid}<br><strong>Type:</strong> ${foundType}`);
                        $("#deleteFeatureType").val(foundType);
                    } else {
                        $("#featurePreview").show();
                        $("#previewText").html(
                            `<span class="text-danger">No feature found with GIS ID: ${gisid}</span>`);
                    }
                });

                $("#deleteModal").on('hidden.bs.modal', function() {
                    highlightSource.clear();
                    $("#deleteForm")[0].reset();
                    $("#featurePreview").hide();
                });
            }

            function refreshVectorLayer() {
                pointSource.clear();
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
                polygonSource.clear();
                polygons.forEach(poly => {
                    try {
                        let coords = JSON.parse(poly.coordinates);
                        polygonSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            type: "Polygon"
                        }));
                    } catch (e) {}
                });
                lineSource.clear();
                lines.forEach(l => {
                    try {
                        let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                            .coordinates;
                        if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                            .isArray(coords[0][0])) coords = coords[0];
                        if (coords && coords.length >= 2) lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: l.gisid,
                            type: "Line",
                            road_name: l.road_name
                        }));
                    } catch (e) {}
                });
                map.render();
            }

            function showFeatureInfo(feature) {
                $('#featureDetails').html(
                    `<p><strong>GIS ID:</strong> ${feature.get('gisid')}</p><p><strong>Type:</strong> ${feature.get('type')}</p>`
                );
                $('#featureInfo').show();
            }

            function showFlashMessage(message, type = 'info') {
                const alertClass = {
                    'success': 'alert-success',
                    'error': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                } [type] || 'alert-info';
                const flashHtml =
                    `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
                $('body').append(flashHtml);
                setTimeout(() => $('.alert').alert('close'), 5000);
            }

            function clearAll() {
                highlightSource.clear();
                routeSource.clear();
                $('#navigationControls').hide();
                $('#featureInfo').hide();
                $('#routeInfo').hide();
                $('#routeBottomSheet').removeClass('open');
                $('#navigationHeader').hide();
                $('#navigationInstruction').hide();
                navigationMode = false;
                currentRoute = null;
                selectedFeature = null;
                closeRouteOptions();
            }

            // Mobile Event Handlers
            $('#mobileSearchBtn').on('click', function() {
                $('#mobileSearchOverlay').show();
            });
            $('#mobileLocationBtn').on('click', toggleLiveLocation);
            $('#mobileLayersBtn').on('click', function() {
                $('#mobileLayerSwitcher').show();
            });
            $('#mobileRouteBtn').on('click', function() {
                if (selectedFeature && currentLocationMarker) calculateRouteToSelectedFeature();
                else if (selectedFeature && !currentLocationMarker) showFlashMessage(
                    'Please enable live location first', 'warning');
                else showFlashMessage('Please search for a GIS ID first', 'warning');
            });
            $('#mobileMenuBtn').on('click', showMobileMenu);
            $('#mobileSearchSubmit').on('click', function() {
                const gisid = $('#mobileSearchInput').val().trim();
                if (gisid) searchGISID(gisid, true);
            });
            $('#mobileSearchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    const gisid = $(this).val().trim();
                    if (gisid) searchGISID(gisid, true);
                }
            });
            $('#closeMobileSearch').on('click', function() {
                $('#mobileSearchOverlay').hide();
            });
            $('#closeMobileLayers').on('click', function() {
                $('#mobileLayerSwitcher').hide();
            });

            // Mobile Layer Switcher
            $('input[name="mobileBaseLayer"]').on('change', function() {
                osmLayer.setVisible($(this).val() === 'osm');
                terrainLayer.setVisible($(this).val() === 'terrain');
                satelliteLayer.setVisible($(this).val() === 'satellite');
            });
            $('#mobileDroneLayer').on('change', function(e) {
                droneLayer.setVisible(e.target.checked);
            });
            $('#mobileBoundaryLayer').on('change', function(e) {
                boundaryLayer.setVisible(e.target.checked);
            });
            $('#mobilePolygonLayer').on('change', function(e) {
                polygonLayer.setVisible(e.target.checked);
            });
            $('#mobileLineLayer').on('change', function(e) {
                lineLayer.setVisible(e.target.checked);
            });
            $('#mobilePointLayer').on('change', function(e) {
                pointLayer.setVisible(e.target.checked);
            });

            // Mobile Editing Tools
            $('#mobileEditToolSelect').on('change', function() {
                const value = $(this).val();
                removeDrawInteractions();
                $("#editForms").empty();

                if (value === "Polygon") {
                    activateDrawPolygon();
                    showFlashMessage('Draw Polygon mode activated. Click on map to draw.', 'info');
                } else if (value === "Line") {
                    activateDrawLine();
                    showFlashMessage('Draw Line mode activated. Click on map to draw.', 'info');
                } else if (value === "Point") {
                    activateDrawPoint();
                    showFlashMessage('Draw Point mode activated. Click on map to draw.', 'info');
                } else if (value === "Modify") {
                    activateModify();
                    showFlashMessage('Modify mode activated. Click and drag features to modify.', 'info');
                } else if (value === "Delete") {
                    activateDelete();
                } else if (value === "none") {
                    isModifyMode = false;
                    isDrawingActive = false;
                    enableFeatureClickHandler();
                    showFlashMessage('Editing tools disabled.', 'info');
                }
            });

            function showMobileMenu() {
                const menu = `
                    <div class="bottom-sheet open" id="mobileMenuSheet">
                        <div class="swipe-handle"></div>
                        <div class="bottom-sheet-content">
                            <h4 class="mb-3"><i class="fas fa-bars me-2"></i>Menu</h4>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" id="recenterMapBtn"><i class="fas fa-crosshairs me-2"></i>Recenter Map</button>
                                <button class="btn btn-outline-success" id="toggleEditingToolsBtn"><i class="fas fa-edit me-2"></i>Editing Tools</button>
                                <button class="btn btn-outline-danger" id="showDeleteModalBtn"><i class="fas fa-trash-alt me-2"></i>Delete Feature</button>
                                <button class="btn btn-outline-secondary" id="toggleFullscreenBtn"><i class="fas fa-expand me-2"></i>Fullscreen</button>
                                <button class="btn btn-outline-info" id="shareLocationBtn"><i class="fas fa-share me-2"></i>Share Location</button>
                                <button class="btn btn-outline-warning" id="clearAllBtn"><i class="fas fa-broom me-2"></i>Clear All</button>
                            </div>
                            <button class="btn btn-secondary w-100 mt-3" id="closeMobileMenuBtn">Close</button>
                        </div>
                    </div>
                `;
                $('body').append(menu);

                $('#recenterMapBtn').on('click', function() {
                    map.getView().animate({
                        center: ol.extent.getCenter(imageExtent),
                        zoom: 17,
                        duration: 1000
                    });
                    closeMobileMenu();
                });
                $('#toggleEditingToolsBtn').on('click', function() {
                    $('#mobileEditingTools').toggleClass('show');
                    closeMobileMenu();
                });
                $('#showDeleteModalBtn').on('click', function() {
                    closeMobileMenu();
                    $("#deleteModal").modal("show");
                });
                $('#toggleFullscreenBtn').on('click', function() {
                    if (!document.fullscreenElement) document.documentElement.requestFullscreen();
                    else document.exitFullscreen();
                    closeMobileMenu();
                });
                $('#shareLocationBtn').on('click', function() {
                    if (navigator.share && currentLocationMarker) {
                        const coords = ol.proj.toLonLat(currentLocationMarker.getGeometry()
                            .getCoordinates());
                        navigator.share({
                            title: 'My Current Location',
                            text: `I'm at ${coords[1].toFixed(6)}, ${coords[0].toFixed(6)}`
                        }).catch(e => console.log(e));
                    } else alert('Share not supported or location not available');
                    closeMobileMenu();
                });
                $('#clearAllBtn').on('click', function() {
                    clearAll();
                    closeMobileMenu();
                });
                $('#closeMobileMenuBtn').on('click', closeMobileMenu);
            }

            function closeMobileMenu() {
                $('#mobileMenuSheet').remove();
            }

            // Desktop Event Handlers
            $('input[name="baseLayer"]').on('change', function() {
                osmLayer.setVisible($(this).val() === 'osm');
                terrainLayer.setVisible($(this).val() === 'terrain');
                satelliteLayer.setVisible($(this).val() === 'satellite');
            });
            $('#droneLayer').on('change', function(e) {
                droneLayer.setVisible(e.target.checked);
            });
            $('#boundaryLayer').on('change', function(e) {
                boundaryLayer.setVisible(e.target.checked);
            });
            $('#polygonLayer').on('change', function(e) {
                polygonLayer.setVisible(e.target.checked);
            });
            $('#lineLayer').on('change', function(e) {
                lineLayer.setVisible(e.target.checked);
            });
            $('#pointLayer').on('change', function(e) {
                pointLayer.setVisible(e.target.checked);
            });
            $('#searchBtn').on('click', function() {
                const gisid = $('#searchInput').val().trim();
                if (gisid) searchGISID(gisid, false);
            });
            $('#searchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    const gisid = $(this).val().trim();
                    if (gisid) searchGISID(gisid, false);
                }
            });
            $('#liveLocationBtn').on('click', toggleLiveLocation);
            $('#centerLocationBtn').on('click', function() {
                if (currentLocationMarker) {
                    map.getView().animate({
                        center: currentLocationMarker.getGeometry().getCoordinates(),
                        zoom: 19,
                        duration: 800
                    });
                    showFlashMessage('Centered on your location', 'info');
                } else {
                    showFlashMessage('Location not available. Please enable live location first.',
                        'warning');
                    if (!isLiveLocationActive && confirm('Enable live location?')) toggleLiveLocation();
                }
            });
            $('#startNavigation').on('click', startNavigation);
            $('#clearNavigation').on('click', clearAll);
            $('#closeFeatureInfo').on('click', function() {
                $('#featureInfo').hide();
            });
            $('#closeDirections').on('click', function() {
                $('#routeInfo').hide();
            });
            $('#editToolSelect').on('change', function() {
                const value = $(this).val();
                removeDrawInteractions();
                $("#editForms").empty();
                if (value === "Polygon") activateDrawPolygon();
                else if (value === "Line") activateDrawLine();
                else if (value === "Point") activateDrawPoint();
                else if (value === "Modify") activateModify();
                else if (value === "Delete") activateDelete();
                else if (value === "none") {
                    isModifyMode = false;
                    isDrawingActive = false;
                    enableFeatureClickHandler();
                }
            });

            // Auto-complete
            $("#assessment").keyup(function() {
                const data = mis.find(row => row.assessment === $(this).val());
                if (data) {
                    $("#old_assessment").val(data.old_assessment || "");
                    $("#owner_name").val(data.owner_name || "");
                    $("#present_owner_name").val(data.present_owner_name || "");
                    $("#old_door_no").val(data.old_door_no || "");

                }
            });
            $("#old_assessment").keyup(function() {
                const data = mis.find(row => row.old_assessment === $(this).val());
                if (data) {
                    $("#assessment").val(data.assessment || "");
                    $("#owner_name").val(data.owner_name || "");
                    $("#present_owner_name").val(data.present_owner_name || "");
                    $("#old_door_no").val(data.old_door_no || "");
                }
            });

            // Initialize
            initDynamicShopDetails();
            setupDeleteFunctionality();
            setupOriginalClickHandler();

            // Set initial mobile layer states
            $('#mobileOsm').prop('checked', true);
            $('#mobileDroneLayer, #mobileBoundaryLayer, #mobilePolygonLayer, #mobileLineLayer, #mobilePointLayer')
                .prop('checked', true);

            // Window resize handler
            $(window).on('resize', function() {
                isMobile = $(window).width() <= 768;
            });

            console.log("Map Application Loaded Successfully");
        });
    </script>
@endsection
