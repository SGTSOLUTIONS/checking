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
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: 'Segoe UI', 'Poppins', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        /* MAP CONTAINER */
        #map {
            width: 100%;
            height: 100vh;
            border-radius: 0;
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        /* PAGE HEADER */
        .page-header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .page-header h3,
        .page-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .page-header h3 {
            font-size: 22px;
        }

        .page-header h4 {
            font-size: 18px;
        }

        .page-header i {
            margin-right: 10px;
            color: #3b82f6;
        }

        /* ACTION BUTTONS CONTAINER - Responsive */
        .action-buttons {
            position: fixed;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* Desktop: Vertical on right side */
        @media (min-width: 769px) {
            .action-buttons {
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .action-btn {
                width: 55px;
                height: 55px;
                border-radius: 16px;
            }

            .action-btn span {
                display: none;
            }

            .action-btn i {
                font-size: 22px;
            }

            .page-header h3 {
                display: block;
            }

            .page-header h4 {
                display: none;
            }
        }

        /* Mobile: Horizontal at bottom */
        @media (max-width: 768px) {
            .action-buttons {
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                padding: 8px 12px;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
                border-top: 1px solid rgba(0, 0, 0, 0.05);
            }

            .action-btn {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 8px 12px;
                border-radius: 16px;
                background: transparent;
                width: auto;
                height: auto;
            }

            .action-btn span {
                display: block;
                font-size: 10px;
                font-weight: 500;
                margin-top: 4px;
                color: #64748b;
            }

            .action-btn i {
                font-size: 20px;
                margin-bottom: 2px;
            }

            .action-btn.active {
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
            }

            .action-btn.active i,
            .action-btn.active span {
                color: white;
            }

            .page-header h3 {
                display: none;
            }

            .page-header h4 {
                display: block;
            }

            #map {
                height: calc(100vh - 56px - 70px);
            }
        }

        /* Common button styles */
        .action-btn {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .action-btn:active {
            transform: scale(0.95);
        }

        /* Edit Panel */
        .edit-Lable {
            position: fixed;
            z-index: 1100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .edit-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: scale(0.95);
        }

        /* Desktop edit panel position */
        @media (min-width: 769px) {
            .edit-Lable {
                right: 90px;
                top: 50%;
                transform: translateY(-50%);
                width: 280px;
            }

            .edit-Lable.closed {
                transform: translateY(-50%) scale(0.95);
            }
        }

        /* Mobile edit panel position */
        @media (max-width: 768px) {
            .edit-Lable {
                left: 12px;
                right: 12px;
                bottom: 80px;
                width: auto;
                max-width: calc(100% - 24px);
            }

            .edit-Lable.closed {
                transform: translateY(20px) scale(0.95);
            }
        }

        .edit-Lable select {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .edit-Lable select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Search Label */
        .search-Lable {
            position: fixed;
            z-index: 1100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .search-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: scale(0.95);
        }

        /* Desktop search panel position */
        @media (min-width: 769px) {
            .search-Lable {
                right: 90px;
                top: 50%;
                transform: translateY(-50%);
                width: 350px;
            }

            .search-Lable.closed {
                transform: translateY(-50%) scale(0.95);
            }
        }

        /* Mobile search panel position */
        @media (max-width: 768px) {
            .search-Lable {
                left: 12px;
                right: 12px;
                bottom: 80px;
                width: auto;
                max-width: calc(100% - 24px);
            }

            .search-Lable.closed {
                transform: translateY(20px) scale(0.95);
            }
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .search-Lable input {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            font-size: 14px;
            flex: 1;
            outline: none;
            transition: all 0.3s ease;
            background: white;
        }

        .search-Lable input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-Lable button {
            border-radius: 12px;
            padding: 12px 24px;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        /* Search Suggestions */
        .search-suggestions {
            display: none;
            background: white;
            border-radius: 16px;
            max-height: 320px;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-top: 5px;
        }

        .search-suggestions.show {
            display: block;
            animation: fadeInDown 0.3s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            transform: translateX(4px);
        }

        .suggestion-item.selected {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        }

        .suggestion-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border-radius: 10px;
            color: #2563eb;
            font-size: 16px;
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
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            background: #e2e8f0;
            color: #475569;
            font-weight: 600;
        }

        /* Layer Switcher Panel */
        .layer-switcher {
            position: fixed;
            z-index: 1100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .layer-switcher.closed {
            opacity: 0;
            visibility: hidden;
            transform: scale(0.95);
        }

        /* Desktop layer panel position */
        @media (min-width: 769px) {
            .layer-switcher {
                right: 90px;
                top: 50%;
                transform: translateY(-50%);
                width: 280px;
            }

            .layer-switcher.closed {
                transform: translateY(-50%) scale(0.95);
            }
        }

        /* Mobile layer panel position */
        @media (max-width: 768px) {
            .layer-switcher {
                left: 12px;
                right: 12px;
                bottom: 80px;
                width: auto;
                max-width: calc(100% - 24px);
            }

            .layer-switcher.closed {
                transform: translateY(20px) scale(0.95);
            }
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
            font-size: 20px;
        }

        #closeLayerPanel {
            border: none;
            background: #eff6ff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            color: #1e40af;
            transition: all 0.3s ease;
        }

        #closeLayerPanel:hover {
            background: #dbeafe;
            transform: rotate(90deg);
        }

        .layer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 6px;
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
            transition: all 0.3s ease;
        }

        .layer-item input:checked+.checkmark {
            background: #2563eb;
            animation: pulse 0.3s ease;
        }

        .layer-item input:checked+.checkmark::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 13px;
            top: -1px;
            left: 3px;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Route Info Panel - Simplified (No Navigation Button) */
        .route-info-panel {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: white;
            border-radius: 20px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 1100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 350px;
        }

        @media (max-width: 768px) {
            .route-info-panel {
                left: 12px;
                right: 12px;
                bottom: 80px;
                max-width: none;
                padding: 12px 16px;
            }
        }

        .route-info-panel.closed {
            transform: translateY(150%);
        }

        .route-info-panel h5 {
            margin: 0 0 8px 0;
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
        }

        .route-stats {
            display: flex;
            gap: 20px;
        }

        .route-stat {
            flex: 1;
            text-align: center;
        }

        .route-stat-value {
            font-size: 20px;
            font-weight: 800;
            color: #2563eb;
        }

        .route-stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .close-route-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f1f5f9;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .close-route-btn:hover {
            background: #fee2e2;
            color: #dc2626;
            transform: rotate(90deg);
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            z-index: 1300;
            animation: slideUp 0.3s ease;
            pointer-events: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .toast-notification {
                bottom: 90px;
                font-size: 12px;
                padding: 10px 20px;
            }
        }

        .toast-notification.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .toast-notification.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .toast-notification.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
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
            width: 60px;
            height: 60px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1400;
            display: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Shop Forms Styling */
        .shop-item {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .shop-item:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .remove-shop-btn {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-shop-btn:hover {
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            transform: scale(1.05);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 24px;
            overflow: hidden;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-body {
            padding: 20px 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            padding: 14px 20px;
            font-weight: 600;
            border: none;
        }

        .card-body {
            padding: 20px;
        }

        /* Form Control Styling */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
            color: #475569;
        }

        /* Error Message Styling */
        .error-message {
            font-size: 11px;
            margin-top: 5px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .is-invalid {
            border-color: #dc2626 !important;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }
    </style>
@endsection

@section('content')
    <!-- Unified Header - Shows different content based on screen size -->
    <div class="page-header">
        <h3><i class="fas fa-map-marked-alt me-2"></i>Ward Map View - Ward {{ $ward->ward_no }}</h3>
        <h4><i class="fas fa-map-marked-alt me-2"></i>Ward {{ $ward->ward_no }} Map</h4>
    </div>

    <div id="map"></div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner"></div>

    {{-- <!-- Simplified Route Info Panel (No Navigation Button) -->
    <div id="routeInfoPanel" class="route-info-panel closed">
        <button class="close-route-btn" id="closeRouteBtn">&times;</button>
        <h5><i class="fas fa-route me-2"></i>Route Information</h5>
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
    </div> --}}

    <!-- Unified Action Buttons - Changes layout based on screen size -->
    <div class="action-buttons">
        <div class="action-btn" id="layerBtn" title="Toggle Layers">
            <i class="fas fa-layer-group"></i>
            <span>Layers</span>
        </div>
        <div class="action-btn" id="searchBtn" title="Search">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </div>
        <div class="action-btn" id="locationBtn" title="My Location">
            <i class="fas fa-location-dot"></i>
            <span>Location</span>
        </div>
        <div class="action-btn" id="routeBtn" title="Get Route">
            <i class="fas fa-route"></i>
            <span>Route</span>
        </div>
        <div class="action-btn" id="editBtn" title="Edit Tools">
            <i class="fas fa-pen-to-square"></i>
            <span>Edit</span>
        </div>
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
            <div><i class="fas fa-layer-group"></i><span>Map Layers</span></div>
            <button id="closeLayerPanel"><i class="fas fa-times"></i></button>
        </div>
        <label class="layer-item"><input type="checkbox" id="osmToggle" checked><span class="checkmark"></span><span>OSM
                Map</span></label>
        <label class="layer-item"><input type="checkbox" id="satelliteToggle"><span
                class="checkmark"></span><span>Satellite</span></label>
        <label class="layer-item"><input type="checkbox" id="droneToggle" checked><span class="checkmark"></span><span>Drone
                Image</span></label>
        <label class="layer-item"><input type="checkbox" id="boundaryToggle" checked><span
                class="checkmark"></span><span>Ward Boundary</span></label>
        <label class="layer-item"><input type="checkbox" id="polygonToggle" checked><span
                class="checkmark"></span><span>Buildings</span></label>
        <label class="layer-item"><input type="checkbox" id="lineToggle" checked><span
                class="checkmark"></span><span>Roads</span></label>
        <label class="layer-item"><input type="checkbox" id="pointToggle" checked><span
                class="checkmark"></span><span>Points</span></label>
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
                        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Warning: This
                            action cannot be undone.</div>
                        <div class="mb-3"><label class="form-label">Enter GIS ID to Delete</label><input type="text"
                                class="form-control" id="deleteGisIdInput" name="gisid"
                                placeholder="e.g., A1001 or 1001" required></div>
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
                surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}",
                surveyorPolygonDatasUpload: "{{ route('surveyor.polygon.datas.upload') }}"
            };

            // Helper Functions
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
                return searchIndex.filter(item => item.searchText.toLowerCase().includes(lowerQuery) || item.title
                    .toLowerCase().includes(lowerQuery)).slice(0, 10);
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

            // ==================== MAP INITIALIZATION ====================
            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [{{ $ward->extent_left ?? 0 }}, {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }}, {{ $ward->extent_top ?? 0 }}
            ];

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

            function createPointStyle(feature) {
                const gisid = feature.get("gisid");
                const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                let color = "blue";
                if (polygonData) color = pointCount > 0 ? (polygonData.number_bill == pointCount ? "green" :
                    "red") : "blue";
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
                    if (coords && coords.length >= 2) lineSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: l.gisid,
                        type: "Line",
                        road_name: l.road_name || null
                    }));
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
                        if (coords && coords.length >= 2) lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: l.gisid,
                            type: "Line",
                            road_name: l.road_name || null
                        }));
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

            // ==================== CLICK HANDLERS ====================
            let isModifyMode = false,
                isDrawingActive = false;

            function removeDrawInteractions() {
                map.getInteractions().forEach(interaction => {
                    if (interaction instanceof ol.interaction.Draw || interaction instanceof ol.interaction
                        .Modify || interaction instanceof ol.interaction.Select) map.removeInteraction(
                        interaction);
                });
                isModifyMode = false;
                isDrawingActive = false;
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
                        else if (geometryType === "LineString" || geometryType === "MultiLineString") {
                            selectedFeature = feature;
                            showToast(`Selected Road: ${properties.road_name || properties.gisid}`, 'success');
                        }
                    }
                };
                map.on('click', featureClickHandler);
            }

            // ==================== POINT HANDLER ====================
            function handlePointClick(properties) {
                const gisid = properties["gisid"];
                resetPointFormFields();
                $('#pointModal').remove();

                const polygonData = polygonDatas.find(data => data.gisid === gisid);
                const polygonNumOfBill = polygonData ? polygonData.number_bill : null;
                const matchingPointsCount = pointDatas.filter(data => data.point_gisid === gisid).length;

                if (polygonNumOfBill > matchingPointsCount) {
                    showPointModal(gisid);
                } else {
                    showFlashMessage(`Already this building has ${matchingPointsCount} bills`, "error");
                }
            }

            function showPointModal(gisid) {
                $("body").append(`
        <div class="modal fade" id="pointModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                        <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Point Data Collection - GIS ID: ${gisid}</h5>
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

                $("#pointgis").val(gisid);
                initDynamicShopDetails();
                setupPointFormSubmit();
                $("#pointModal").modal("show");
            }

            function setupPointFormSubmit() {
                $("#pointForm").off('submit').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const shopCount = parseInt($('#no_of_shop').val()) || 0;
                    formData.append('total_shops', shopCount);

                    // Collect all form data
                    formData.append('type', $('#type').val());
                    formData.append('point_gisid', $('#pointgis').val());
                    formData.append('assessment', $('#assessment').val());
                    formData.append('old_assessment', $('#old_assessment').val());
                    formData.append('owner_name', $('#owner_name').val());
                    formData.append('present_owner_name', $('#present_owner_name').val());
                    formData.append('no_of_shop', $('#no_of_shop').val());
                    formData.append('no_of_persons', $('#no_of_persons').val());

                    // Property Details
                    formData.append('floor', $('#floor').val());
                    formData.append('old_door_no', $('#old_door_no').val());
                    formData.append('new_door_no', $('#new_door_no').val());
                    formData.append('bill_usage', $('#bill_usage').val());
                    formData.append('eb', $('#eb').val());

                    // Tax Details
                    formData.append('water_tax', $('#water_tax').val());
                    formData.append('old_water_tax', $('#old_water_tax').val());
                    formData.append('professional_tax', $('#professional_tax').val());
                    formData.append('gst', $('#gst').val());
                    formData.append('trade_income', $('#trade_income').val());

                    // Documents
                    formData.append('aadhar_no', $('#aadhar_no').val());
                    formData.append('ration_no', $('#ration_no').val());
                    formData.append('phone_number', $('#phone').val());

                    // Quality Check
                    formData.append('qc_area', $('#qc_area').val());
                    formData.append('qc_usage', $('#qc_usage').val());
                    formData.append('qc_name', $('#qc_name').val());
                    formData.append('qc_remarks', $('#qc_remarks').val());

                    // Remarks
                    formData.append('establishment_remarks', $('#establishment_remarks').val());
                    formData.append('remarks', $('#remarks').val());

                    // Shop details
                    for (let i = 1; i <= shopCount; i++) {
                        formData.append(`shop_name_${i}`, $(`input[name="shop_name_${i}"]`).val() || '');
                        formData.append(`shop_owner_name_${i}`, $(`input[name="shop_owner_name_${i}"]`)
                        .val() || '');
                        formData.append(`shop_category_${i}`, $(`select[name="shop_category_${i}"]`)
                        .val() || '');
                        formData.append(`shop_mobile_${i}`, $(`input[name="shop_mobile_${i}"]`).val() ||
                        '');
                        formData.append(`prof_tax_assessment_${i}`, $(
                            `input[name="prof_tax_assessment_${i}"]`).val() || '');
                        formData.append(`old_prof_tax_assessment_${i}`, $(
                            `input[name="old_prof_tax_assessment_${i}"]`).val() || '');
                        formData.append(`shop_floor_${i}`, $(`input[name="shop_floor_${i}"]`).val() || '');
                        formData.append(`license_${i}`, $(`input[name="license_${i}"]`).val() || '');
                        formData.append(`number_of_employee_${i}`, $(
                            `input[name="number_of_employee_${i}"]`).val() || '');
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
                        },
                        error: function(xhr) {
                            let errorMsg = "An error occurred";
                            if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                                .responseJSON.message;
                            showFlashMessage(errorMsg, "error");
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $("#" + key + "_error").text(value[0]);
                                    $("#" + key).addClass("is-invalid");
                                });
                            }
                        },
                        complete: function() {
                            $("#pointSubmit").prop("disabled", false).html(
                                '<i class="fas fa-save me-2"></i>Save Point Data');
                        }
                    });
                });
            }

            function resetPointFormFields() {
                // Basic Information
                $("#pointgis, #assessment, #old_assessment, #owner_name, #present_owner_name, #no_of_persons").val(
                    "");
                $("#no_of_shop").val(0);
                $("#type").val("OLD");

                // Property Details
                $("#floor, #old_door_no, #new_door_no, #eb").val("");
                $("#bill_usage").val("");

                // Tax Details
                $("#water_tax, #old_water_tax, #professional_tax, #gst, #trade_income").val("");

                // Documents
                $("#aadhar_no, #ration_no, #phone").val("");

                // Quality Check
                $("#qc_area, #qc_name, #qc_remarks").val("");
                $("#qc_usage").val("");

                // Remarks
                $("#establishment_remarks, #remarks").val("");

                // Shop Details
                $('#append').empty();
                $('#shopDetailsContainer').empty();

                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
                currentShopCount = 0;
            }

            function initDynamicShopDetails() {
                $('#no_of_shop').off('change').on('change', function() {
                    const shopCount = parseInt($(this).val()) || 0;
                    const appendArea = $('#append');
                    appendArea.empty();
                    if (shopCount > 0) {
                        const container = $(`
                <div class="card mb-3">
                    <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white;">
                        <h6 class="mb-0"><i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount > 1 ? 's' : ''})</h6>
                    </div>
                    <div class="card-body" id="shopDetailsContainer"></div>
                </div>
            `);
                        appendArea.append(container);
                        const shopContainer = $('#shopDetailsContainer');
                        for (let i = 1; i <= shopCount; i++) {
                            shopContainer.append(`
                    <div class="shop-item" data-shop-id="${i}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-store me-2"></i>Shop ${i}</h6>
                            <button type="button" class="remove-shop-btn" data-shop-id="${i}">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prof Tax Assessment</label>
                                <input type="text" name="prof_tax_assessment_${i}" class="form-control" placeholder="Enter prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Old Prof Tax Assessment</label>
                                <input type="text" name="old_prof_tax_assessment_${i}" class="form-control" placeholder="Enter old prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Floor</label>
                                <input type="text" name="shop_floor_${i}" class="form-control" placeholder="Enter floor number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Name</label>
                                <input type="text" name="shop_name_${i}" class="form-control" placeholder="Enter shop name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Owner Name</label>
                                <input type="text" name="shop_owner_name_${i}" class="form-control" placeholder="Enter owner name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Category</label>
                                <select name="shop_category_${i}" class="form-control">
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
                                <input type="tel" name="shop_mobile_${i}" class="form-control" placeholder="Mobile number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_${i}" class="form-control" placeholder="License number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Number of Employees</label>
                                <input type="number" name="number_of_employee_${i}" class="form-control" placeholder="Employee count">
                            </div>
                        </div>
                    </div>
                `);
                        }

                        // Add remove button functionality
                        $('.remove-shop-btn').off('click').on('click', function() {
                            const shopId = $(this).data('shop-id');
                            $(`.shop-item[data-shop-id="${shopId}"]`).fadeOut(300, function() {
                                $(this).remove();
                                const currentCount = parseInt($('#no_of_shop').val()) || 0;
                                $('#no_of_shop').val(currentCount - 1).trigger('change');
                            });
                        });
                    }
                });
            }

            // ==================== POLYGON HANDLER ====================
            function handlePolygonClick(properties) {
                const gisId = properties["gisid"];
                resetBuildingForm();
                $("#building_gisid").val(gisId);
                let existingData = null;
                $("#buildingModal").remove();

                let roadNames = @json($uniqueRoadNames ?? []);
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
                                <h5 class="modal-title"><i class="fas fa-building me-2"></i>Building Data Collection - GIS ID: ${gisId}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form id="buildingForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="gisIdInput" name="gisid" value="${gisId}">
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto; background: #f8fafc;">
                                    <div class="card mb-4">
                                        <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;"><h6 class="mb-0"><i class="fas fa-image me-2"></i>Building Images</h6></div>
                                        <div class="card-body"><div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold mb-2"><i class="fas fa-camera me-1"></i>Image 1</label>
                                                <div class="border rounded p-3" style="background: #ffffff; min-height: 220px;">
                                                    <img id="buildingImagePreview" src="" alt="Building Image Preview" class="img-fluid" style="display: none; max-height: 200px; width: 100%; object-fit: contain; border-radius: 8px;">
                                                    <div id="noImagePlaceholder" class="text-center text-muted" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px;">
                                                        <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: #cbd5e1;"></i><p>No image selected</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2"><label class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-upload me-1"></i> Choose Image<input type="file" name="image" id="building_image" accept="image/*" style="display: none;"></label></div>
                                                <div id="building_image_error" class="error-message text-danger small mt-1"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold mb-2"><i class="fas fa-camera me-1"></i>Image 2</label>
                                                <div class="border rounded p-3" style="background: #ffffff; min-height: 220px;">
                                                    <img id="buildingImagePreview2" src="" alt="Building Image Preview 2" class="img-fluid" style="display: none; max-height: 200px; width: 100%; object-fit: contain; border-radius: 8px;">
                                                    <div id="noImagePlaceholder2" class="text-center text-muted" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px;">
                                                        <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: #cbd5e1;"></i><p>No image selected</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2"><label class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-upload me-1"></i> Choose Image<input type="file" name="image2" id="building_image2" accept="image/*" style="display: none;"></label></div>
                                                <div id="building_image2_error" class="error-message text-danger small mt-1"></div>
                                            </div>
                                        </div></div>
                                    </div>
                                    <div class="card mb-4">
                                        <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;"><h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6></div>
                                        <div class="card-body"><div class="row">
                                            <div class="col-md-3 mb-3"><label class="form-label">GIS ID</label><input type="text" class="form-control" name="building_gisid" id="building_gisid" value="${gisId}" readonly><div id="building_gisid_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Zone</label><select class="form-select" name="building_zone" id="building_zone"><option value="">Select Zone</option><option value="ZONE-A">ZONE-A</option><option value="ZONE-B">ZONE-B</option><option value="ZONE-C">ZONE-C</option><option value="ZONE-D">ZONE-D</option><option value="ZONE-E">ZONE-E</option></select><div id="building_zone_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Number of Bills</label><input type="number" class="form-control" name="number_bill" id="number_bill" min="0"><div id="number_bill_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Number of Shops</label><input type="number" class="form-control" name="number_shop" id="number_shop" min="0"><div id="number_shop_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Number of Floors</label><input type="number" class="form-control" name="number_floor" id="number_floor" min="0"><div id="number_floor_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Percentage</label><select class="form-select" name="percentage" id="percentage"><option value="">Select Percentage</option><option value="10">10%</option><option value="20">20%</option><option value="30">30%</option><option value="40">40%</option><option value="50">50%</option><option value="60">60%</option><option value="70">70%</option><option value="80">80%</option><option value="85">85%</option><option value="90">90%</option><option value="100">100%</option></select><div id="percentage_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Building Name</label><input type="text" class="form-control" name="building_name" id="building_name" placeholder="Enter building name"><div id="building_name_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Road Name</label><select class="form-select" id="road_name" name="road_name">${roadOptions}</select><div id="road_name_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Phone Number</label><input type="tel" class="form-control" name="phone" id="phone_building" placeholder="10-digit mobile number" maxlength="10"><div id="phone_building_error" class="error-message text-danger small"></div></div>
                                        </div></div>
                                    </div>
                                    <div class="card mb-4">
                                        <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;"><h6 class="mb-0"><i class="fas fa-building me-2"></i>Building Details</h6></div>
                                        <div class="card-body"><div class="row">
                                            <div class="col-md-4 mb-3"><label class="form-label">Building Usage</label><select class="form-select" name="building_usage" id="building_usage"><option value="">Select Usage</option><option value="RESIDENTIAL">Residential</option><option value="COMMERCIAL">Commercial</option><option value="INDUSTRIAL">Industrial</option><option value="INSTITUTIONAL">Institutional</option><option value="MIXED">Mixed</option><option value="GOVERNMENT">Government</option><option value="VACANT">Vacant</option></select><div id="building_usage_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-4 mb-3"><label class="form-label">Construction Type</label><select class="form-select" name="construction_type" id="construction_type"><option value="">Select Type</option><option value="PERMANENT">Permanent</option><option value="SEMI_PERMANENT">Semi Permanent</option><option value="VACANT_LAND">Vacant Land</option><option value="SHED">Shed</option><option value="CAR_SHED">Car Shed</option><option value="TEMPORARY">Temporary</option></select><div id="construction_type_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-4 mb-3"><label class="form-label">Building Type</label><select class="form-select" name="building_type" id="building_type"><option value="">Select Type</option><option value="Independent">Independent</option><option value="Flat">Flat</option><option value="Kalyana_Mandapam">Kalyana Mandapam</option><option value="Hotel">Hotel</option><option value="Cinema_Theatre">Cinema Theatre</option><option value="Central_Government_Building">Central Government Building</option><option value="State_Government_Building">State Government Building</option><option value="Municipality_Corporation">Municipality / Corporation</option><option value="Educational_Institution">Educational Institution</option><option value="Hospital">Hospital</option><option value="Commercial_Complex">Commercial Complex</option><option value="Shop">Shop</option><option value="Office">Office</option><option value="Temple">Temple</option><option value="Mosque">Mosque</option><option value="Church">Church</option><option value="Amma_Unavagam">Amma Unavagam</option><option value="Public_Toilet">Public Toilet</option><option value="Vacant Land">Vacant Land</option><option value="Under Construction">Under Construction</option><option value="Others">Others</option></select><div id="building_type_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-4 mb-3"><label class="form-label">UGD Status</label><select class="form-select" name="ugd" id="ugd"><option value="">Select Status</option><option value="No_Connection">No Connection</option><option value="Manhole_Available_but_Connection_Not_Given_to_House">Manhole Available but Connection Not Given</option><option value="Stage_1_Completed">Stage 1 Completed</option><option value="Stage_1_2_Completed">Stage 1 & 2 Completed</option><option value="Stage_1_2_Completed_but_Not_Connected">Stage 1 & 2 Completed but Not Connected</option><option value="Stage_1_2_3_Completed">Stage 1, 2 & 3 Completed</option><option value="Direct_Connection_Given">Direct Connection Given</option><option value="1_UGD_Connection_-_3_Stage_Completed">1 UGD Connection - 3 Stage Completed</option><option value="2_UGD_Connection_-_3_Stage_Completed">2 UGD Connection - 3 Stage Completed</option></select><div id="ugd_error" class="error-message text-danger small"></div></div>
                                        </div></div>
                                    </div>
                                    <div class="card mb-4">
                                        <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;"><h6 class="mb-0"><i class="fas fa-umbrella me-2"></i>Amenities</h6></div>
                                        <div class="card-body"><div class="row">
                                            <div class="col-md-3 mb-3"><label class="form-label">Lift Room</label><select class="form-select" name="liftroom" id="liftroom"><option value="No">No</option><option value="Yes">Yes</option></select><div id="liftroom_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Head Room</label><select class="form-select" name="headroom" id="headroom"><option value="No">No</option><option value="Yes">Yes</option></select><div id="headroom_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Overhead Tank</label><select class="form-select" name="overhead_tank" id="overhead_tank"><option value="No">No</option><option value="Yes">Yes</option></select><div id="overhead_tank_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Rainwater Harvesting</label><select class="form-select" name="rainwater_harvesting" id="rainwater_harvesting"><option value="No">No</option><option value="Yes">Yes</option></select><div id="rainwater_harvesting_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Parking</label><select class="form-select" name="parking" id="parking"><option value="No">No</option><option value="Yes">Yes</option></select><div id="parking_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Ramp</label><select class="form-select" name="ramp" id="ramp"><option value="No">No</option><option value="Yes">Yes</option></select><div id="ramp_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Hoarding</label><select class="form-select" name="hoarding" id="hoarding"><option value="No">No</option><option value="Yes">Yes</option></select><div id="hoarding_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">CCTV</label><select class="form-select" name="cctv" id="cctv"><option value="No">No</option><option value="Yes">Yes</option></select><div id="cctv_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Cell Tower</label><select class="form-select" name="cell_tower" id="cell_tower"><option value="No">No</option><option value="Yes">Yes</option></select><div id="cell_tower_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Solar Panel</label><select class="form-select" name="solar_panel" id="solar_panel"><option value="No">No</option><option value="Yes">Yes</option></select><div id="solar_panel_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Basement</label><input type="number" class="form-control" name="basement" id="basement" min="0" placeholder="Number of basements"><div id="basement_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-3 mb-3"><label class="form-label">Water Connection</label><select class="form-select" name="water_connection" id="water_connection"><option value="No">No</option><option value="Yes">Yes</option></select><div id="water_connection_error" class="error-message text-danger small"></div></div>
                                        </div></div>
                                    </div>
                                    <div class="card mb-4">
                                        <div class="card-header" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;"><h6 class="mb-0"><i class="fas fa-comment me-2"></i>Remarks</h6></div>
                                        <div class="card-body"><div class="row">
                                            <div class="col-md-6 mb-3"><label class="form-label">General Remarks</label><textarea class="form-control" name="remarks" id="remarks_building" rows="3" placeholder="Enter general remarks..."></textarea><div id="remarks_building_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Corporation Remarks</label><textarea class="form-control" name="corporationremarks" id="corporationremarks" rows="3" placeholder="Enter corporation remarks..."></textarea><div id="corporationremarks_error" class="error-message text-danger small"></div></div>
                                            <div class="col-md-12 mb-3"><label class="form-label">QC Remarks</label><textarea class="form-control" name="qc_remarks" id="qc_remarks" rows="2" placeholder="Enter QC remarks..."></textarea><div id="qc_remarks_error" class="error-message text-danger small"></div></div>
                                        </div></div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Close</button>
                                    <button type="submit" class="btn btn-primary" id="buildingsubmitBtn"><i class="fas fa-save me-2"></i>Save Building Data</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>`;

                $("body").append(modalHtml);
                setupImagePreview();
                setupBuildingFormSubmit();

                if (polygonDatas && polygonDatas.length > 0) existingData = polygonDatas.find(item => item.gisid ==
                    gisId);
                if (existingData) {
                    populateBuildingForm(existingData);
                    showFlashMessage('Loading existing building data...', 'info');
                } else {
                    $("#buildingImagePreview,#buildingImagePreview2").hide().attr("src", "");
                    showFlashMessage('Creating new building record...', 'info');
                }
                $("#buildingModal").modal("show");
            }

            function setupImagePreview() {
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

            function setupBuildingFormSubmit() {
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
                        url: routes.surveyorPolygonDatasUpload,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            showFlashMessage(response.message, "success");
                            $("#buildingModal").modal("hide");
                            if (response.polygonDatas) polygonDatas = response.polygonDatas;
                            if (response.polygons) polygons = response.polygons;
                            refreshVectorLayer();
                            resetBuildingForm();
                        },
                        error: function(xhr) {
                            let errorMsg = "An error occurred while saving building data";
                            if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                                .responseJSON.message;
                            showFlashMessage(errorMsg, "error");
                            if (xhr.responseJSON && xhr.responseJSON.errors) $.each(xhr
                                .responseJSON.errors,
                                function(key, value) {
                                    $("#" + key + "_error").html(value[0]);
                                    $("#" + key).addClass("is-invalid");
                                });
                        },
                        complete: function() {
                            $("#buildingsubmitBtn").prop("disabled", false).html(
                                '<i class="fas fa-save me-2"></i>Save Building Data');
                        }
                    });
                });
            }

            function populateBuildingForm(item) {
                $("#building_gisid").val(item.gisid || "");
                $("#number_bill").val(item.number_bill || "");
                $("#number_shop").val(item.number_shop || "");
                $("#number_floor").val(item.number_floor || "");
                $("#building_name").val(item.building_name || "");
                $("#road_name").val(item.road_name || "");
                $("#phone_building").val(item.phone || "");
                $("#building_zone").val(item.zone || item.building_zone || "");
                $("#percentage").val(item.percentage || "");
                $("#building_usage").val(item.building_usage || "");
                $("#construction_type").val(item.construction_type || "");
                $("#building_type").val(item.building_type || "");
                $("#ugd").val(item.ugd || "");
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
                $("#basement").val(item.basement || "");
                $("#water_connection").val(item.water_connection || "No");
                $("#remarks_building").val(item.remarks || "");
                $("#corporationremarks").val(item.corporationremarks || "");
                $("#qc_remarks").val(item.qc_remarks || "");
                const assetUrl = window.assetUrl || "{{ asset('') }}";
                if (item.image && item.image !== "") {
                    const imageUrl = item.image.startsWith('http') ? item.image : assetUrl + item.image;
                    $("#buildingImagePreview").attr("src", imageUrl).show();
                    $("#noImagePlaceholder").hide();
                }
                if (item.image2 && item.image2 !== "") {
                    const imageUrl2 = item.image2.startsWith('http') ? item.image2 : assetUrl + item.image2;
                    $("#buildingImagePreview2").attr("src", imageUrl2).show();
                    $("#noImagePlaceholder2").hide();
                }
            }

            function resetBuildingForm() {
                $("#building_gisid,#number_bill,#number_shop,#number_floor,#building_name,#road_name,#phone_building,#building_zone,#percentage")
                    .val("");
                $("#building_usage,#construction_type,#building_type,#ugd").val("");
                $("#liftroom,#headroom,#overhead_tank,#rainwater_harvesting,#parking,#ramp,#hoarding,#cctv,#cell_tower,#solar_panel")
                    .val("No");
                $("#basement,#water_connection").val("");
                $("#remarks_building,#corporationremarks,#qc_remarks").val("");
                $("#buildingImagePreview,#buildingImagePreview2").hide().attr("src", "");
                $("#building_image,#building_image2").val("");
                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
            }

            // ==================== DRAW FUNCTIONS ====================
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

            // ==================== DELETE FORM ====================
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

            // ==================== LAYER TOGGLES ====================
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

            // ==================== UNIFIED ACTION BUTTONS ====================
            $('#layerBtn').click(function() {
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

            $('#searchBtn').click(function(e) {
                e.stopPropagation();
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher, #editLabel').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(), 100);
            });

            $('#editBtn').click(function(e) {
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
                $('#editLabel').addClass('closed');
            });

            // ==================== SEARCH FUNCTIONALITY ====================
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

            // ==================== LOCATION & ROUTE ====================
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

            $('#locationBtn').click(toggleLiveLocation);

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

            // ==================== CLOSE PANELS ON OUTSIDE CLICK ====================
            $(document).click(function(event) {
                if (!$(event.target).closest(
                        '.action-buttons, #layerSwitcher, #searchLabel, #editLabel, #routeInfoPanel, #searchSuggestions'
                    ).length) {
                    $('#layerSwitcher, #searchLabel, #editLabel').addClass('closed');
                    $('#searchSuggestions').removeClass('show');
                }
            });

            // ==================== RESIZE HANDLER ====================
            $(window).resize(function() {
                isMobile = window.innerWidth <= 768;
            });

            // ==================== KEYBOARD SHORTCUTS ====================
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

            // ==================== INITIALIZE ====================
            setupOriginalClickHandler();
            showToast('Map loaded successfully', 'success');
        });
    </script>
@endsection
