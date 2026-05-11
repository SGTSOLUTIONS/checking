{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #D4A13E, #E86A5F);
            --dark-gradient: linear-gradient(135deg, #0B2B40, #1A6B6E);
            --success-gradient: linear-gradient(135deg, #28a745, #20c997);
            --warning-gradient: linear-gradient(135deg, #ffc107, #fd7e14);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }

        .map-wrapper {
            position: relative;
            width: 100%;
            height: calc(100vh - 80px);
            min-height: 500px;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
        }

        #map {
            width: 100%;
            height: 100%;
            background: #e8e8e8;
        }

        /* Search Container - Enhanced */
        .search-container {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 16px;
            width: 360px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 161, 62, 0.3);
            transition: var(--transition);
        }

        @media (max-width: 768px) {
            .search-container {
                width: calc(100% - 40px);
                max-width: 100%;
                top: 10px;
                left: 10px;
                right: 10px;
            }
        }

        .search-container h4 {
            margin: 0 0 12px 0;
            font-size: 16px;
            font-weight: 600;
            color: #0B2B40;
            border-left: 4px solid #D4A13E;
            padding-left: 12px;
        }

        .search-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            background: #f5f5f5;
            border-radius: 16px;
            padding: 4px;
        }

        .search-tab {
            flex: 1;
            padding: 8px 12px;
            cursor: pointer;
            border: none;
            background: transparent;
            font-weight: 500;
            color: #666;
            transition: var(--transition);
            border-radius: 12px;
            font-size: 13px;
        }

        .search-tab.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 2px 6px rgba(212, 161, 62, 0.3);
        }

        .search-panel {
            display: none;
        }

        .search-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-box {
            display: flex;
            gap: 8px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 14px;
            border: 2px solid #e8e8e8;
            border-radius: 14px;
            font-size: 14px;
            transition: var(--transition);
            background: white;
        }

        .search-box input:focus {
            outline: none;
            border-color: #D4A13E;
            box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
        }

        .search-box button {
            background: var(--dark-gradient);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 0 20px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .search-box button:hover {
            background: var(--primary-gradient);
            transform: scale(1.02);
        }

        .search-results {
            max-height: 250px;
            overflow-y: auto;
            margin-top: 12px;
            border: 1px solid #eee;
            border-radius: 14px;
            display: none;
            background: white;
        }

        .search-result-item {
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-result-item:hover {
            background: rgba(212, 161, 62, 0.1);
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 14px;
            z-index: 1001;
            width: 200px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 161, 62, 0.3);
        }

        @media (max-width: 768px) {
            .layer-switcher {
                top: auto;
                bottom: 80px;
                right: 10px;
                width: 180px;
            }
        }

        .layer-switcher h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #0B2B40;
            border-bottom: 2px solid #D4A13E;
            padding-bottom: 6px;
        }

        .layer-group {
            margin-bottom: 12px;
        }

        .layer-group h5 {
            font-size: 11px;
            color: #D4A13E;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .layer-option {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            cursor: pointer;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .layer-option:hover {
            background: rgba(212, 161, 62, 0.1);
        }

        .layer-option input {
            margin-right: 8px;
            cursor: pointer;
            width: 14px;
            height: 14px;
        }

        .layer-option label {
            cursor: pointer;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #333;
        }

        /* Feature Info Panel - Enhanced with Assessment Form */
        .feature-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 16px;
            z-index: 1001;
            width: 420px;
            max-width: calc(100% - 40px);
            display: none;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 161, 62, 0.3);
            max-height: 85vh;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .feature-info {
                bottom: 10px;
                right: 10px;
                left: 10px;
                width: auto;
                max-height: 70vh;
            }
        }

        .feature-info h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
            color: #0B2B40;
            border-left: 3px solid #D4A13E;
            padding-left: 12px;
        }

        .feature-info .close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.05);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .feature-info .close-btn:hover {
            background: #E86A5F;
            color: white;
        }

        /* Assessment Form Styles */
        .assessment-form {
            background: linear-gradient(135deg, #f8f9fa, #fff);
            border-radius: 16px;
            padding: 16px;
            margin-top: 15px;
            border: 1px solid rgba(212, 161, 62, 0.2);
        }

        .assessment-form h5 {
            margin: 0 0 12px 0;
            font-size: 14px;
            font-weight: 600;
            color: #1A6B6E;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #0B2B40;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 13px;
            transition: var(--transition);
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #D4A13E;
            box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
        }

        .form-group input:disabled,
        .form-group select:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .btn-update {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            transition: var(--transition);
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-update:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .update-status {
            margin-top: 10px;
            font-size: 12px;
            text-align: center;
        }

        .update-status.success {
            color: #28a745;
        }

        .update-status.error {
            color: #dc3545;
        }

        /* Info Tabs */
        .info-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            flex-wrap: wrap;
        }

        .info-tab {
            padding: 6px 14px;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 500;
            color: #666;
            border-radius: 20px;
            transition: var(--transition);
            font-size: 12px;
        }

        .info-tab.active {
            background: var(--primary-gradient);
            color: white;
        }

        .info-tab-content {
            display: none;
        }

        .info-tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .info-row {
            margin-bottom: 10px;
            font-size: 12px;
            display: flex;
            flex-wrap: wrap;
        }

        .info-label {
            font-weight: 600;
            color: #0B2B40;
            width: 130px;
            font-size: 11px;
        }

        @media (max-width: 480px) {
            .info-label {
                width: 100px;
            }
        }

        .info-value {
            color: #555;
            flex: 1;
            word-break: break-word;
        }

        /* Shop List Styles */
        .shop-list,
        .assessment-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .shop-item,
        .assessment-item {
            background: #f8f9fa;
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 3px solid #D4A13E;
            transition: var(--transition);
        }

        .shop-item:hover,
        .assessment-item:hover {
            transform: translateX(4px);
            box-shadow: var(--shadow-sm);
        }

        .shop-item h6,
        .assessment-item h6 {
            margin: 0 0 8px 0;
            color: #0B2B40;
            font-weight: 600;
            font-size: 13px;
        }

        .shop-detail-row,
        .assessment-detail-row {
            font-size: 11px;
            margin-bottom: 4px;
            display: flex;
        }

        .shop-detail-label,
        .assessment-detail-label {
            width: 100px;
            font-weight: 500;
            color: #666;
        }

        .shop-detail-value,
        .assessment-detail-value {
            color: #333;
            flex: 1;
        }

        .badge-shop {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            background: #1A6B6E;
            color: white;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-completed {
            background: var(--success-gradient);
            color: white;
        }

        .badge-pending {
            background: var(--warning-gradient);
            color: #333;
        }

        /* Controls */
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1001;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .zoom-btn {
            width: 42px;
            height: 42px;
            border: none;
            background: white;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
            color: #0B2B40;
        }

        .zoom-btn:hover {
            background: var(--primary-gradient);
            color: white;
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .zoom-controls {
                bottom: 80px;
                left: 10px;
            }

            .zoom-btn {
                width: 36px;
                height: 36px;
            }
        }

        .live-location-btn {
            position: absolute;
            bottom: 20px;
            left: 80px;
            z-index: 1001;
            background: var(--dark-gradient);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .live-location-btn:hover {
            background: var(--primary-gradient);
            transform: translateY(-2px);
        }

        .live-location-btn.active {
            background: var(--success-gradient);
        }

        @media (max-width: 768px) {
            .live-location-btn {
                bottom: 80px;
                left: 60px;
                padding: 8px 14px;
                font-size: 11px;
            }
        }

        .route-btn {
            position: absolute;
            bottom: 20px;
            left: 220px;
            z-index: 1001;
            background: var(--dark-gradient);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            display: none;
        }

        .route-btn:hover {
            background: var(--primary-gradient);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .route-btn {
                bottom: 80px;
                left: 160px;
                padding: 8px 14px;
                font-size: 11px;
            }
        }

        .route-info {
            position: absolute;
            bottom: 20px;
            left: 320px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 16px;
            z-index: 1001;
            max-width: 340px;
            min-width: 280px;
            display: none;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 161, 62, 0.3);
        }

        @media (max-width: 768px) {
            .route-info {
                bottom: 80px;
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }

        .route-info h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #0B2B40;
            border-left: 3px solid #D4A13E;
            padding-left: 10px;
        }

        .route-summary {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .directions-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .direction-step {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
            display: flex;
            gap: 10px;
        }

        .step-number {
            background: #1A6B6E;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
        }

        .step-instruction {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .step-distance {
            font-size: 9px;
            color: #888;
        }

        .close-route {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            font-size: 18px;
        }

        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(11, 43, 64, 0.95);
            padding: 20px 30px;
            border-radius: 20px;
            z-index: 2000;
            display: none;
            color: white;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 280px;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 14px 16px;
            margin-bottom: 10px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.success {
            border-left-color: #28a745;
        }

        .toast-notification.error {
            border-left-color: #dc3545;
        }

        .toast-notification.warning {
            border-left-color: #ffc107;
        }

        .toast-notification.info {
            border-left-color: #17a2b8;
        }

        .toast-notification .toast-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .toast-notification .toast-message {
            font-size: 12px;
            color: #666;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #D4A13E;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #E86A5F;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12">
                <div class="map-wrapper">
                    <div id="map"></div>

                    <!-- Search Container -->
                    <div class="search-container">
                        <h4><i class="fas fa-search me-2"></i>Search Property</h4>

                        <div class="search-tabs">
                            <button class="search-tab active" data-tab="gisid">GIS ID</button>
                            <button class="search-tab" data-tab="assessment">Assessment No</button>
                        </div>

                        <div class="search-panel active" id="gisidPanel">
                            <div class="search-box">
                                <input type="text" id="gisidSearchInput" placeholder="Enter GIS ID..."
                                    autocomplete="off">
                                <button id="gisidSearchBtn"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="search-results" id="gisidResults"></div>
                        </div>

                        <div class="search-panel" id="assessmentPanel">
                            <div class="search-box">
                                <input type="text" id="assessmentSearchInput" placeholder="Enter Assessment Number..."
                                    autocomplete="off">
                                <button id="assessmentSearchBtn"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="search-results" id="assessmentResults"></div>
                        </div>
                    </div>

                    <!-- Layer Switcher -->
                    <div class="layer-switcher">
                        <h4><i class="fas fa-layer-group me-2"></i>Layers</h4>
                        <div class="layer-group">
                            <h5>Base Maps</h5>
                            <div class="layer-option">
                                <input type="radio" name="baseLayer" value="osm" checked>
                                <label><i class="fas fa-map"></i> Street Map</label>
                            </div>
                            <div class="layer-option">
                                <input type="radio" name="baseLayer" value="satellite">
                                <label><i class="fas fa-satellite"></i> Satellite</label>
                            </div>
                            <div class="layer-option">
                                <input type="radio" name="baseLayer" value="terrain">
                                <label><i class="fas fa-mountain"></i> Terrain</label>
                            </div>
                        </div>
                        <div class="layer-group">
                            <h5>Overlays</h5>
                            <div class="layer-option">
                                <input type="checkbox" id="showDroneImage" checked>
                                <label><i class="fas fa-drone"></i> Drone Image</label>
                            </div>
                            <div class="layer-option">
                                <input type="checkbox" id="showBoundary" checked>
                                <label><i class="fas fa-vector-square"></i> Boundary</label>
                            </div>
                            <div class="layer-option">
                                <input type="checkbox" id="showPolygons" checked>
                                <label><i class="fas fa-draw-polygon"></i> Buildings</label>
                            </div>
                            <div class="layer-option">
                                <input type="checkbox" id="showLines" checked>
                                <label><i class="fas fa-road"></i> Roads</label>
                            </div>
                            <div class="layer-option">
                                <input type="checkbox" id="showPoints" checked>
                                <label><i class="fas fa-map-marker-alt"></i> Points</label>
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="zoom-controls">
                        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                    </div>

                    <button class="live-location-btn" id="liveLocationBtn">
                        <i class="fas fa-location-dot me-2"></i>Live Location
                    </button>

                    <button class="route-btn" id="routeBtn" style="display: none;">
                        <i class="fas fa-route me-2"></i>Get Route
                    </button>

                    <!-- Info Panels -->
                    <div class="feature-info" id="featureInfo">
                        <button class="close-btn" id="closeFeatureInfo">&times;</button>
                        <h4><i class="fas fa-info-circle me-2"></i>Property Details</h4>

                        <div class="info-tabs">
                            <button class="info-tab active" data-tab="buildingDetails">Building Details</button>
                            <button class="info-tab" data-tab="shopsList">Shops List</button>
                            <button class="info-tab" data-tab="assessmentsList">Assessments</button>
                        </div>

                        <div class="info-tab-content active" id="buildingDetails">
                            <div id="featureDetails"></div>
                        </div>

                        <div class="info-tab-content" id="shopsList">
                            <div id="shopsDetails"></div>
                        </div>

                        <div class="info-tab-content" id="assessmentsList">
                            <div id="assessmentsDetails"></div>
                        </div>

                        <!-- Assessment Update Form -->
                        <div class="assessment-form" id="assessmentForm" style="display: none;">
                            <h5><i class="fas fa-edit me-2"></i>Update Assessment</h5>
                            <form id="updateAssessmentForm">
                                <input type="hidden" id="currentAssessmentNo">
                                <input type="hidden" id="pointDataTableName">
                                <div class="form-group">
                                    <label for="squareFeet">Square Feet (sq.ft)</label>
                                    <input type="number" id="squareFeet" class="form-control"
                                        placeholder="Enter square feet" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="usage">Usage Type</label>
                                    <select id="usage" class="form-control">
                                        <option value="">Select Usage</option>
                                        <option value="residential">Residential</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="industrial">Industrial</option>
                                        <option value="mixed">Mixed Use</option>
                                        <option value="institutional">Institutional</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-update" id="updateAssessmentBtn">
                                    <i class="fas fa-save me-2"></i>Update Assessment
                                </button>
                                <div id="updateStatus" class="update-status"></div>
                            </form>
                        </div>
                    </div>

                    <div class="route-info" id="routeInfo">
                        <button class="close-route" id="closeRouteInfo">&times;</button>
                        <h4><i class="fas fa-route me-2"></i>Route Information</h4>
                        <div id="routeSummary" class="route-summary"></div>
                        <div id="directionsList" class="directions-list"></div>
                    </div>

                    <div class="loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-white mb-2"></div>
                        <div>Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toast notification function
        function showToast(message, type = 'info') {
            const toastHtml = `
        <div class="toast-notification ${type}">
            <div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;
            $('body').append(toastHtml);
            const $toast = $('.toast-notification').last();
            setTimeout(() => $toast.addClass('show'), 10);
            setTimeout(() => {
                $toast.removeClass('show');
                setTimeout(() => $toast.remove(), 300);
            }, 4000);
        }

        // Main initialization
        (function() {
            function isOpenLayersLoaded() {
                return typeof ol !== 'undefined' && typeof ol.Map !== 'undefined';
            }

            function initMap() {
                console.log("Initializing Commissioner Ward Map...");

                if (!document.getElementById('map')) {
                    console.error("Map element not found!");
                    return;
                }

                // Data from server
                let polygons = [];
                let lines = [];
                let points = [];
                let pointDatas = [];
                let polygonDatas = [];
                let shopDatas = [];
                let misData = [];
                let ward = {};

                try {
                    polygons = @json($polygons ?? []);
                    lines = @json($lines ?? []);
                    points = @json($points ?? []);
                    pointDatas = @json($pointDatas ?? []);
                    polygonDatas = @json($polygonDatas ?? []);
                    shopDatas = @json($shopDatas ?? []);
                    misData = @json($misData ?? []);
                    ward = @json($ward ?? []);
                    console.log("Data loaded - Polygons:", polygons.length, "Points:", points.length, "Shops:",
                        shopDatas.length, "Assessments:", pointDatas.length);
                } catch (e) {
                    console.error("Error parsing JSON data:", e);
                }

                let currentLocationMarker = null;
                let locationWatchId = null;
                let isLiveLocationActive = false;
                let selectedFeature = null;
                let currentGisid = null;
                let highlightSource = null;
                let currentPointDataTable = null;

                // Helper functions
                function getShopsByPointGisid(pointGisid) {
                    const relatedPoints = pointDatas.filter(pd => pd.point_gisid == pointGisid);
                    const pointDataIds = relatedPoints.map(pd => pd.id);
                    return shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
                }

                function getShopsByBuildingGisid(gisid) {
                    const buildingPoints = pointDatas.filter(pd => pd.point_gisid == gisid);
                    const pointDataIds = buildingPoints.map(pd => pd.id);
                    return shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
                }

                function getAssessmentsByGisid(gisid) {
                    return pointDatas.filter(pd => pd.point_gisid == gisid);
                }

                // Style Functions
                function getPointStyle(feature) {
                    const gisid = feature.get("gisid");
                    const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                    const polygonData = polygonDatas.find(d => d.gisid == gisid);
                    let color = "#1679AB";
                    if (polygonData && pointCount > 0) {
                        color = (polygonData.number_bill == pointCount) ? "#28a745" : "#dc3545";
                    }
                    return new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 7,
                            fill: new ol.style.Fill({
                                color: color
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#fff",
                                width: 2
                            })
                        }),
                        text: new ol.style.Text({
                            text: gisid ? String(gisid) : "",
                            font: "10px Arial",
                            offsetY: -12,
                            fill: new ol.style.Fill({
                                color: "#333"
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#fff",
                                width: 2
                            })
                        })
                    });
                }

                function getPolygonStyle(feature) {
                    const gisid = feature.get("gisid");
                    const sqft = feature.get("sqfeet") || "0";
                    const polygonData = polygonDatas.find(data => data.gisid == gisid);
                    const color = polygonData ? "#E86A5F" : "#D4A13E";

                    const geometry = feature.getGeometry();
                    const centerPoint = geometry.getInteriorPoint();

                    return [
                        new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: color,
                                width: 3,
                                lineJoin: "round",
                                lineCap: "round"
                            }),
                            fill: new ol.style.Fill({
                                color: "rgba(212, 161, 62, 0.1)"
                            })
                        }),
                        new ol.style.Style({
                            geometry: centerPoint,
                            text: new ol.style.Text({
                                text: sqft + " SQFT",
                                font: "bold 12px Arial",
                                fill: new ol.style.Fill({
                                    color: "#ffffff"
                                }),
                                stroke: new ol.style.Stroke({
                                    color: "#000000",
                                    width: 3
                                }),
                                overflow: true,
                                textAlign: "center",
                                offsetY: 0
                            })
                        })
                    ];
                }

                function getLineStyle() {
                    return new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "#ffc107",
                            width: 2
                        })
                    });
                }

                function getHighlightStyle() {
                    return new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "#ff6600",
                            width: 4
                        }),
                        fill: new ol.style.Fill({
                            color: "rgba(255, 102, 0, 0.2)"
                        }),
                        image: new ol.style.Circle({
                            radius: 10,
                            fill: new ol.style.Fill({
                                color: "#ff6600"
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#fff",
                                width: 2
                            })
                        })
                    });
                }

                function getHumanLocationStyle() {
                    return new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 10,
                            fill: new ol.style.Fill({
                                color: "#0066cc"
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#fff",
                                width: 2
                            })
                        })
                    });
                }

                // Layer Definitions
                const osmLayer = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });

                const satelliteLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    }),
                    visible: false
                });

                const terrainLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
                    }),
                    visible: false
                });

                // Drone Image Layer
                let droneLayer = null;
                if (ward && ward.drone_image && ward.extent_left) {
                    const imageExtent = [
                        parseFloat(ward.extent_left),
                        parseFloat(ward.extent_bottom),
                        parseFloat(ward.extent_right),
                        parseFloat(ward.extent_top)
                    ];
                    const droneImageURL = "{{ asset($ward->drone_image ?? '') }}";
                    if (droneImageURL && imageExtent[0] !== 0 && !isNaN(imageExtent[0])) {
                        droneLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: droneImageURL,
                                imageExtent: imageExtent,
                                imageSmoothing: false
                            }),
                            opacity: 0.8,
                            visible: true
                        });
                    }
                }
                if (!droneLayer) {
                    droneLayer = new ol.layer.Image({
                        source: new ol.source.ImageStatic({
                            url: "",
                            imageExtent: [0, 0, 0, 0]
                        }),
                        visible: false
                    });
                }

                // Vector Sources
                const polygonSource = new ol.source.Vector();
                if (polygons && polygons.length) {
                    polygons.forEach(poly => {
                        try {
                            let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) :
                                poly.coordinates;
                            if (coords && coords.length) {
                                polygonSource.addFeature(new ol.Feature({
                                    geometry: new ol.geom.Polygon(coords),
                                    gisid: poly.gisid,
                                    type: "Polygon",
                                    sqfeet: poly.sqfeet || "0"
                                }));
                            }
                        } catch (e) {
                            console.error("Error adding polygon:", e);
                        }
                    });
                }
                const polygonLayer = new ol.layer.Vector({
                    source: polygonSource,
                    style: getPolygonStyle,
                    visible: true
                });

                const lineSource = new ol.source.Vector();
                if (lines && lines.length) {
                    lines.forEach(l => {
                        try {
                            let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                                .coordinates;
                            if (coords && coords.length >= 2) {
                                if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
                                lineSource.addFeature(new ol.Feature({
                                    geometry: new ol.geom.LineString(coords),
                                    gisid: l.gisid,
                                    type: "Line"
                                }));
                            }
                        } catch (e) {
                            console.error("Error adding line:", e);
                        }
                    });
                }
                const lineLayer = new ol.layer.Vector({
                    source: lineSource,
                    style: getLineStyle,
                    visible: true
                });

                const pointSource = new ol.source.Vector();
                if (points && points.length) {
                    points.forEach(p => {
                        try {
                            let coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p
                                .coordinates;
                            if (coords && coords.length === 2) {
                                pointSource.addFeature(new ol.Feature({
                                    geometry: new ol.geom.Point(coords),
                                    gisid: p.gisid,
                                    type: "Point"
                                }));
                            }
                        } catch (e) {
                            console.error("Error adding point:", e);
                        }
                    });
                }
                const pointLayer = new ol.layer.Vector({
                    source: pointSource,
                    style: getPointStyle,
                    visible: true
                });

                // Boundary Layer
                let boundaryLayer = null;
                if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
                    try {
                        const boundary = ward.boundary[0];
                        const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([transformedBoundary])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: "#ff0000",
                                    width: 2
                                }),
                                fill: new ol.style.Fill({
                                    color: "rgba(255, 0, 0, 0.03)"
                                })
                            }),
                            visible: true
                        });
                    } catch (e) {
                        console.error("Error creating boundary layer:", e);
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector(),
                            visible: true
                        });
                    }
                } else {
                    boundaryLayer = new ol.layer.Vector({
                        source: new ol.source.Vector(),
                        visible: true
                    });
                }

                // Highlight Layer
                highlightSource = new ol.source.Vector();
                const highlightLayer = new ol.layer.Vector({
                    source: highlightSource,
                    style: getHighlightStyle
                });

                // Location Layer
                const locationSource = new ol.source.Vector();
                const locationLayer = new ol.layer.Vector({
                    source: locationSource,
                    style: getHumanLocationStyle
                });

                // Route Layer
                const routeSource = new ol.source.Vector();
                const routeLayer = new ol.layer.Vector({
                    source: routeSource,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "#0066cc",
                            width: 4,
                            lineDash: [8, 8]
                        })
                    })
                });

                // Set default center
                let defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);
                if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
                    try {
                        const boundary = ward.boundary[0];
                        const lons = boundary.map(pt => pt[0]);
                        const lats = boundary.map(pt => pt[1]);
                        const centerLon = (Math.min(...lons) + Math.max(...lons)) / 2;
                        const centerLat = (Math.min(...lats) + Math.max(...lats)) / 2;
                        defaultCenter = ol.proj.fromLonLat([centerLon, centerLat]);
                    } catch (e) {
                        console.error("Error calculating center from boundary:", e);
                    }
                }

                // Initialize Map
                const map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer, terrainLayer, droneLayer, boundaryLayer, polygonLayer,
                        lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer
                    ],
                    view: new ol.View({
                        projection: "EPSG:3857",
                        center: defaultCenter,
                        zoom: 16
                    }),
                    controls: []
                });

                console.log("Map initialized successfully");

                // Layer Switchers
                $('input[name="baseLayer"]').on('change', function() {
                    const val = $(this).val();
                    osmLayer.setVisible(val === 'osm');
                    satelliteLayer.setVisible(val === 'satellite');
                    terrainLayer.setVisible(val === 'terrain');
                });

                $('#showDroneImage').on('change', (e) => droneLayer.setVisible(e.target.checked));
                $('#showBoundary').on('change', (e) => boundaryLayer.setVisible(e.target.checked));
                $('#showPolygons').on('change', (e) => polygonLayer.setVisible(e.target.checked));
                $('#showLines').on('change', (e) => lineLayer.setVisible(e.target.checked));
                $('#showPoints').on('change', (e) => pointLayer.setVisible(e.target.checked));

                // Zoom Controls
                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));

                // Search Tabs
                $('.search-tab').on('click', function() {
                    const tab = $(this).data('tab');
                    $('.search-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.search-panel').removeClass('active');
                    $(`#${tab}Panel`).addClass('active');
                    $(`#${tab}Results`).hide();
                });

                // Function to display full property info including shops and assessment form
                function displayFullPropertyInfo(gisid, pointDataTable = null) {
                    currentGisid = gisid;
                    if (pointDataTable) {
                        currentPointDataTable = pointDataTable;
                        $('#pointDataTableName').val(pointDataTable);
                    }

                    const polygonData = polygonDatas.find(d => d.gisid == gisid);
                    const assessments = getAssessmentsByGisid(gisid);
                    const shops = getShopsByBuildingGisid(gisid);
                    const pointCount = assessments.length;

                    // Building Details HTML
                    let buildingHtml =
                        `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;
                    if (polygonData) {
                        buildingHtml += `
                    <div class="info-row"><span class="info-label">Building Name:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Building Usage:</span><span class="info-value">${polygonData.building_usage || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Construction Type:</span><span class="info-value">${polygonData.construction_type || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Road Name:</span><span class="info-value">${polygonData.road_name || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Shops/Units:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Total Bills:</span><span class="info-value">${polygonData.number_bill || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Assessments Done:</span><span class="info-value">${pointCount}</span></div>
                    <div class="info-row"><span class="info-label">Square Feet:</span><span class="info-value">${polygonData.sqfeet || 'N/A'} sqft</span></div>
                    <div class="info-row"><span class="info-label">Zone:</span><span class="info-value">${polygonData.zone || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">UGD:</span><span class="info-value">${polygonData.ugd || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Rainwater Harvesting:</span><span class="info-value">${polygonData.rainwater_harvesting || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">CCTV:</span><span class="info-value">${polygonData.cctv || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Status:</span><span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}</span></div>
                `;
                    } else {
                        buildingHtml +=
                            `<div class="info-row"><span class="info-label">Note:</span><span class="info-value">No building data available for this GIS ID</span></div>`;
                    }
                    $('#featureDetails').html(buildingHtml);

                    // Shops List HTML
                    let shopsHtml = '';
                    if (shops && shops.length > 0) {
                        shopsHtml = `<div class="shop-list">`;
                        shops.forEach((shop, index) => {
                            shopsHtml += `
                        <div class="shop-item">
                            <h6><span class="badge-shop">Shop ${index + 1}</span> ${shop.shop_name || 'Unnamed Shop'}</h6>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">Floor:</span>
                                <span class="shop-detail-value">${shop.shop_floor || 'N/A'}</span>
                            </div>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">Owner Name:</span>
                                <span class="shop-detail-value">${shop.shop_owner_name || 'N/A'}</span>
                            </div>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">Category:</span>
                                <span class="shop-detail-value">${shop.shop_category || 'N/A'}</span>
                            </div>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">Mobile:</span>
                                <span class="shop-detail-value">${shop.shop_mobile || 'N/A'}</span>
                            </div>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">License No:</span>
                                <span class="shop-detail-value">${shop.license || 'N/A'}</span>
                            </div>
                            <div class="shop-detail-row">
                                <span class="shop-detail-label">Employees:</span>
                                <span class="shop-detail-value">${shop.number_of_employee || 'N/A'}</span>
                            </div>
                        </div>
                    `;
                        });
                        shopsHtml += `</div>`;
                    } else {
                        shopsHtml = `<div class="text-muted text-center p-3">No shops found for this building</div>`;
                    }
                    $('#shopsDetails').html(shopsHtml);

                    // Assessments List HTML with clickable assessments to populate form
                    let assessmentsHtml = '';
                    if (assessments && assessments.length > 0) {
                        assessmentsHtml = `<div class="assessment-list">`;
                        assessments.forEach((assessment, index) => {
                            assessmentsHtml += `
                        <div class="assessment-item" data-assessment="${assessment.assessment}" ata-assessment="${assessment.id}" data-point-data-table="${assessment.table_name || ''}" style="cursor: pointer;">
                            <h6><span class="badge-shop">Assessment ${index + 1}</span> ${assessment.assessment || 'N/A'}</h6>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Owner Name:</span>
                                <span class="assessment-detail-value">${assessment.owner_name || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Present Owner:</span>
                                <span class="assessment-detail-value">${assessment.present_owner_name || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Phone:</span>
                                <span class="assessment-detail-value">${assessment.phone_number || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Floor:</span>
                                <span class="assessment-detail-value">${assessment.floor || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Bill Usage:</span>
                                <span class="assessment-detail-value">${assessment.bill_usage || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Door No:</span>
                                <span class="assessment-detail-value">${assessment.new_door_no || assessment.old_door_no || 'N/A'}</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Square Feet:</span>
                                <span class="assessment-detail-value">${assessment.sqfeet || 'N/A'} sqft</span>
                            </div>
                            <div class="assessment-detail-row">
                                <span class="assessment-detail-label">Usage:</span>
                                <span class="assessment-detail-value">${assessment.usage || 'N/A'}</span>
                            </div>
                        </div>
                    `;
                        });
                        assessmentsHtml += `</div>`;
                    } else {
                        assessmentsHtml =
                            `<div class="text-muted text-center p-3">No assessments found for this building</div>`;
                    }
                    $('#assessmentsDetails').html(assessmentsHtml);

                    // Add click handler for assessment items to populate form
                    $('.assessment-item').on('click', function() {
                        const assessmentNo = $(this).data('assessment');
                        const assessmentId = $(this).data('id');
                        const pointDataTable = $(this).data('point-data-table');
                        loadAssessmentForEdit(assessmentNo, pointDataTable,assessmentId);
                    });

                    $('#featureInfo').fadeIn();
                }

                // Load assessment data into edit form
                function loadAssessmentForEdit(assessmentNo, pointDataTable,assessmentId) {
                    $('#loadingSpinner').fadeIn();
                    $('#updateStatus').html('');

                    // Get assessment details from server
                    $.ajax({
                        url: '{{ route('corporation.get.assessment.details') }}',
                        method: 'GET',
                        data: {
                            assessment_no: assessmentNo,
                            point_data_table: pointDataTable,
                            assessment_id : assessmentId
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                $('#currentAssessmentNo').val(assessmentNo);
                                $('#pointDataTableName').val(pointDataTable);
                                $('#squareFeet').val(response.data.sqfeet || '');
                                $('#usage').val(response.data.usage || '');
                                $('#assessmentForm').slideDown();
                                showToast('Assessment loaded for editing', 'info');
                            } else {
                                showToast(response.message || 'Error loading assessment', 'error');
                            }
                        },
                        error: function() {
                            showToast('Error loading assessment details', 'error');
                        },
                        complete: function() {
                            $('#loadingSpinner').fadeOut();
                        }
                    });
                }

                // Update assessment via AJAX
                function updateAssessment(assessmentNo, squareFeet, usage, pointDataTable) {
                    $('#updateAssessmentBtn').prop('disabled', true);
                    $('#updateStatus').html('<i class="fas fa-spinner fa-spin"></i> Updating...').removeClass(
                        'success error');

                    $.ajax({
                        url: '{{ route('corporation.update.assessment') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            assessment_no: assessmentNo,
                            square_feet: squareFeet,
                            usage: usage,
                            point_data_table: pointDataTable
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#updateStatus').html('<i class="fas fa-check-circle"></i> ' + response
                                    .message).addClass('success');
                                showToast(response.message, 'success');
                                setTimeout(() => $('#assessmentForm').slideUp(), 2000);
                            } else {
                                $('#updateStatus').html('<i class="fas fa-exclamation-circle"></i> ' +
                                    response.message).addClass('error');
                                showToast(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Error updating assessment';
                            $('#updateStatus').html('<i class="fas fa-exclamation-circle"></i> ' + message)
                                .addClass('error');
                            showToast(message, 'error');
                        },
                        complete: function() {
                            $('#updateAssessmentBtn').prop('disabled', false);
                            setTimeout(() => $('#updateStatus').html(''), 3000);
                        }
                    });
                }

                // Tab switching
                $('.info-tab').on('click', function() {
                    const tabId = $(this).data('tab');
                    $('.info-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.info-tab-content').removeClass('active');
                    $(`#${tabId}`).addClass('active');
                });

                // Search Functions
                function searchByGISID(gisid) {
                    if (!gisid) {
                        showToast('Please enter GIS ID', 'warning');
                        return;
                    }

                    $('#loadingSpinner').fadeIn();
                    highlightSource.clear();
                    $('#assessmentForm').slideUp();

                    let foundFeature = null;
                    polygonSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) {
                            foundFeature = f;
                            return true;
                        }
                    });
                    if (!foundFeature) {
                        pointSource.forEachFeature(f => {
                            if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) {
                                foundFeature = f;
                                return true;
                            }
                        });
                    }

                    if (foundFeature) {
                        highlightSource.addFeature(foundFeature.clone());
                        map.getView().fit(foundFeature.getGeometry().getExtent(), {
                            padding: [50, 50, 50, 50],
                            duration: 1000
                        });
                        displayFullPropertyInfo(gisid);
                        selectedFeature = foundFeature;
                        $('#routeBtn').show();
                        $('#gisidResults').hide();
                        $('#gisidSearchInput').val('');
                        showToast(`GIS ID "${gisid}" found`, 'success');
                    } else {
                        showToast(`GIS ID "${gisid}" not found`, 'error');
                        $('#gisidResults').html('<div class="search-result-item text-danger">No results found</div>')
                            .show();
                        setTimeout(() => $('#gisidResults').fadeOut(), 2000);
                    }
                    $('#loadingSpinner').fadeOut();
                }

                function searchByAssessment(assessmentNo) {
                    if (!assessmentNo) {
                        showToast('Please enter Assessment Number', 'warning');
                        return;
                    }

                    $('#loadingSpinner').fadeIn();
                    highlightSource.clear();

                    const pointData = pointDatas.find(d => d.assessment == assessmentNo);
                    if (pointData && pointData.point_gisid) {
                        let foundFeature = null;
                        pointSource.forEachFeature(f => {
                            if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid
                            .toString()) {
                                foundFeature = f;
                                return true;
                            }
                        });
                        if (foundFeature) {
                            highlightSource.addFeature(foundFeature.clone());
                            map.getView().fit(foundFeature.getGeometry().getExtent(), {
                                padding: [50, 50, 50, 50],
                                duration: 1000
                            });
                            displayFullPropertyInfo(pointData.point_gisid, pointData.table_name);
                            selectedFeature = foundFeature;
                            $('#routeBtn').show();
                            $('#assessmentResults').hide();
                            $('#assessmentSearchInput').val('');
                            showToast(`Assessment "${assessmentNo}" found`, 'success');
                        } else {
                            showToast(`Assessment "${assessmentNo}" not found on map`, 'error');
                        }
                    } else {
                        showToast(`Assessment "${assessmentNo}" not found`, 'error');
                    }
                    $('#loadingSpinner').fadeOut();
                }

                // Live Location
                function toggleLiveLocation() {
                    if (isLiveLocationActive) {
                        if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                        locationSource.clear();
                        currentLocationMarker = null;
                        isLiveLocationActive = false;
                        $('#liveLocationBtn').removeClass('active').html(
                            '<i class="fas fa-location-dot me-2"></i>Live Location');
                        showToast('Location tracking stopped', 'info');
                    } else {
                        if (!navigator.geolocation) {
                            showToast('Geolocation not supported', 'error');
                            return;
                        }
                        isLiveLocationActive = true;
                        $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');

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
                            },
                            (error) => {
                                showToast('Location error: ' + error.message, 'error');
                                toggleLiveLocation();
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000
                            }
                        );
                        showToast('Location tracking started', 'success');
                    }
                }

                // Calculate Route
                async function calculateRoute() {
                    if (!selectedFeature) {
                        showToast('Please select a property first by searching or clicking on map', 'warning');
                        return;
                    }
                    if (!currentLocationMarker) {
                        showToast('Please enable Live Location first', 'warning');
                        return;
                    }

                    $('#loadingSpinner').fadeIn();
                    routeSource.clear();

                    try {
                        const startCoord = ol.proj.toLonLat(currentLocationMarker.getGeometry().getCoordinates());
                        const targetGeom = selectedFeature.getGeometry();
                        const endCoord = targetGeom.getType() === 'Point' ?
                            ol.proj.toLonLat(targetGeom.getCoordinates()) :
                            ol.proj.toLonLat(ol.extent.getCenter(targetGeom.getExtent()));

                        const url =
                            `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;
                        const response = await fetch(url);
                        const data = await response.json();

                        if (data.code === 'Ok' && data.routes.length > 0) {
                            const route = data.routes[0];
                            const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                            routeSource.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(coords)
                            }));

                            const distance = route.distance < 1000 ? route.distance.toFixed(0) + ' meters' : (route
                                .distance / 1000).toFixed(2) + ' km';
                            const duration = Math.floor(route.duration / 60) + ' min ' + Math.floor(route.duration %
                                60) + ' sec';

                            let stepsHtml = '';
                            if (route.legs && route.legs[0] && route.legs[0].steps) {
                                route.legs[0].steps.forEach((step, i) => {
                                    if (step.maneuver && step.maneuver.instruction) {
                                        stepsHtml +=
                                            `<div class="direction-step"><div class="step-number">${i + 1}</div><div><div class="step-instruction">${step.maneuver.instruction}</div><div class="step-distance">${step.distance.toFixed(0)} m</div></div></div>`;
                                    }
                                });
                            }

                            $('#routeSummary').html(
                                `<strong>Distance:</strong> ${distance}<br><strong>Duration:</strong> ${duration}`
                                );
                            $('#directionsList').html(stepsHtml ||
                                '<div class="text-muted">No step-by-step directions available</div>');
                            $('#routeInfo').fadeIn();
                            map.getView().fit(routeSource.getExtent(), {
                                padding: [50, 50, 50, 50],
                                duration: 1000
                            });
                            showToast('Route calculated successfully', 'success');
                        } else {
                            showToast('No route found', 'error');
                        }
                    } catch (error) {
                        console.error("Route error:", error);
                        showToast('Error calculating route', 'error');
                    }
                    $('#loadingSpinner').fadeOut();
                }

                // Map Click Handler
                map.on('click', function(evt) {
                    const originalEvent = evt.originalEvent;
                    const target = originalEvent.target;

                    if (target.tagName === 'INPUT' ||
                        target.tagName === 'BUTTON' ||
                        target.closest('.search-container') ||
                        target.closest('.layer-switcher') ||
                        target.closest('.zoom-controls') ||
                        target.closest('.feature-info') ||
                        target.closest('.route-info')) {
                        return;
                    }

                    const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    if (feature && feature.get('gisid')) {
                        const gisid = feature.get('gisid');
                        highlightSource.clear();
                        highlightSource.addFeature(feature.clone());
                        displayFullPropertyInfo(gisid);
                        selectedFeature = feature;
                        $('#routeBtn').show();
                    } else {
                        $('#featureInfo').fadeOut();
                        $('#assessmentForm').slideUp();
                        highlightSource.clear();
                        selectedFeature = null;
                        $('#routeBtn').hide();
                    }
                });

                // Form Submit Handler
                $('#updateAssessmentForm').on('submit', function(e) {
                    e.preventDefault();
                    const assessmentNo = $('#currentAssessmentNo').val();
                    const squareFeet = $('#squareFeet').val();
                    const usage = $('#usage').val();
                    const pointDataTable = $('#pointDataTableName').val();

                    if (!assessmentNo) {
                        showToast('No assessment selected', 'warning');
                        return;
                    }
                    if (!squareFeet) {
                        showToast('Please enter square feet', 'warning');
                        return;
                    }
                    if (!usage) {
                        showToast('Please select usage type', 'warning');
                        return;
                    }

                    updateAssessment(assessmentNo, squareFeet, usage, pointDataTable);
                });

                // Button Events
                $(document).on('click', '#gisidSearchBtn', function() {
                    searchByGISID($('#gisidSearchInput').val().trim());
                });

                $(document).on('keypress', '#gisidSearchInput', function(e) {
                    if (e.key === 'Enter') {
                        searchByGISID($('#gisidSearchInput').val().trim());
                    }
                });

                $(document).on('click', '#assessmentSearchBtn', function() {
                    searchByAssessment($('#assessmentSearchInput').val().trim());
                });

                $(document).on('keypress', '#assessmentSearchInput', function(e) {
                    if (e.key === 'Enter') {
                        searchByAssessment($('#assessmentSearchInput').val().trim());
                    }
                });

                $('#liveLocationBtn').on('click', toggleLiveLocation);
                $('#routeBtn').on('click', calculateRoute);
                $('#closeFeatureInfo').on('click', () => {
                    $('#featureInfo').fadeOut();
                    $('#assessmentForm').slideUp();
                });
                $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());

                // Auto-fit map
                setTimeout(() => {
                    try {
                        const extent = ol.extent.createEmpty();
                        let hasExtent = false;

                        polygonSource.forEachFeature(f => {
                            ol.extent.extend(extent, f.getGeometry().getExtent());
                            hasExtent = true;
                        });
                        pointSource.forEachFeature(f => {
                            ol.extent.extend(extent, f.getGeometry().getExtent());
                            hasExtent = true;
                        });

                        if (hasExtent && extent[0] !== Infinity && extent[0] !== -Infinity) {
                            map.getView().fit(extent, {
                                padding: [30, 30, 30, 30],
                                duration: 1000
                            });
                        }
                    } catch (e) {
                        console.log("Could not auto-fit extent, using default view");
                    }
                }, 800);

                window.addEventListener('resize', function() {
                    setTimeout(function() {
                        map.updateSize();
                    }, 200);
                });

                console.log("Commissioner Ward Map Loaded Successfully");
            }

            // Wait for DOM and OpenLayers
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        if (isOpenLayersLoaded()) {
                            initMap();
                        } else {
                            console.error("OpenLayers not loaded");
                            const mapEl = document.getElementById('map');
                            if (mapEl) {
                                mapEl.innerHTML =
                                    '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f8f9fa;color:#dc3545;">Error: OpenLayers library failed to load.</div>';
                            }
                        }
                    }, 500);
                });
            } else {
                setTimeout(function() {
                    if (isOpenLayersLoaded()) {
                        initMap();
                    } else {
                        console.error("OpenLayers not loaded");
                        const mapEl = document.getElementById('map');
                        if (mapEl) {
                            mapEl.innerHTML =
                                '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f8f9fa;color:#dc3545;">Error: OpenLayers library failed to load.</div>';
                        }
                    }
                }, 500);
            }
        })();
    </script>
@endpush
