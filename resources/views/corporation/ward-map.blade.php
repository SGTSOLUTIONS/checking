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

        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1002;
            background: var(--dark-gradient);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .mobile-menu-toggle:hover {
            background: var(--primary-gradient);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
                top: 10px;
                right: 10px;
                width: 44px;
                height: 44px;
            }
        }

        /* Search Container - Mobile Responsive */
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
                width: calc(100% - 20px);
                max-width: 100%;
                top: 10px;
                left: 10px;
                right: 10px;
                padding: 12px;
            }
        }

        /* Layer Switcher - Collapsible for Mobile */
        .layer-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            z-index: 1001;
            width: 260px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 161, 62, 0.3);
            transition: var(--transition);
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                top: 70px;
                right: 10px;
                width: 280px;
                max-width: calc(100% - 20px);
                transform: translateX(120%);
                transition: transform 0.3s ease;
                z-index: 1003;
            }
            .layer-switcher.open {
                transform: translateX(0);
            }
        }

        .layer-switcher-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(212, 161, 62, 0.2);
        }

        .layer-switcher-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #0B2B40;
        }

        .layer-switcher-header i {
            color: #D4A13E;
            transition: var(--transition);
        }

        .layer-switcher-content {
            padding: 14px;
            max-height: 400px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .layer-switcher-content {
                max-height: 70vh;
            }
        }

        .layer-group {
            margin-bottom: 12px;
        }

        .layer-group h5 {
            font-size: 11px;
            color: #D4A13E;
            margin-bottom: 6px;
            font-weight: 600;
            margin-top: 10px;
        }

        .layer-group:first-child h5 {
            margin-top: 0;
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

        /* Color Legend */
        .color-legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            font-size: 10px;
        }

        .color-legend-item .color-box {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            margin-right: 8px;
        }

        /* Feature Info Panel - Mobile Responsive */
        .feature-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 16px;
            z-index: 1001;
            width: 450px;
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
                padding: 12px;
            }
        }

        /* Assessment Form */
        .assessment-form {
            background: linear-gradient(135deg, #f8f9fa, #fff);
            border-radius: 16px;
            padding: 16px;
            margin-top: 15px;
            border: 1px solid rgba(212, 161, 62, 0.2);
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

        .form-group select {
            cursor: pointer;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #D4A13E;
            box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
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

        /* Controls - Mobile Responsive */
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

        @media (max-width: 768px) {
            .route-btn {
                bottom: 80px;
                left: 160px;
                padding: 8px 14px;
                font-size: 11px;
            }
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

        @media (max-width: 768px) {
            .toast-notification {
                top: 10px;
                right: 10px;
                left: 10px;
                min-width: auto;
                max-width: none;
            }
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.success { border-left-color: #28a745; }
        .toast-notification.error { border-left-color: #dc3545; }
        .toast-notification.warning { border-left-color: #ffc107; }
        .toast-notification.info { border-left-color: #17a2b8; }

        /* Loading Spinner */
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

        /* Image Modal */
        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            cursor: pointer;
        }

        .modal-content {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 100px);
        }

        .modal-content img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Scrollbar */
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

        /* Utility Classes */
        .text-muted { color: #666; }
        .text-center { text-align: center; }
        .p-3 { padding: 12px; }
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }

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

        .info-value {
            color: #555;
            flex: 1;
            word-break: break-word;
        }

        .shop-item, .assessment-item {
            background: #f8f9fa;
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 3px solid #D4A13E;
            transition: var(--transition);
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }

        .building-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }

        @media (max-width: 480px) {
            .info-label { width: 100px; }
            .image-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12">
                <div class="map-wrapper">
                    <div id="map"></div>

                    <!-- Mobile Menu Toggle Button -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-layer-group"></i>
                    </button>

                    <!-- Search Container -->
                    <div class="search-container">
                        <h4><i class="fas fa-search me-2"></i>Search Property</h4>
                        <div class="search-tabs">
                            <button class="search-tab active" data-tab="gisid">GIS ID</button>
                            <button class="search-tab" data-tab="assessment">Assessment No</button>
                        </div>
                        <div class="search-panel active" id="gisidPanel">
                            <div class="search-box">
                                <input type="text" id="gisidSearchInput" placeholder="Enter GIS ID..." autocomplete="off">
                                <button id="gisidSearchBtn"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="search-results" id="gisidResults"></div>
                        </div>
                        <div class="search-panel" id="assessmentPanel">
                            <div class="search-box">
                                <input type="text" id="assessmentSearchInput" placeholder="Enter Assessment Number..." autocomplete="off">
                                <button id="assessmentSearchBtn"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="search-results" id="assessmentResults"></div>
                        </div>
                    </div>

                    <!-- Layer Switcher with Color Legend - Collapsible -->
                    <div class="layer-switcher" id="layerSwitcher">
                        <div class="layer-switcher-header" id="layerSwitcherHeader">
                            <h4><i class="fas fa-layer-group me-2"></i>Layers & Legend</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="layer-switcher-content">
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
                            <div class="layer-group">
                                <h5>Building Usage Colors</h5>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #4CAF50;"></div>
                                    <span>Residential</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #2196F3;"></div>
                                    <span>Commercial</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #FF9800;"></div>
                                    <span>Industrial</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #9C27B0;"></div>
                                    <span>Mixed</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #00BCD4;"></div>
                                    <span>Institutional</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #F44336;"></div>
                                    <span>Government</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #9E9E9E;"></div>
                                    <span>Vacant</span>
                                </div>
                                <div class="color-legend-item">
                                    <div class="color-box" style="background: #D4A13E;"></div>
                                    <span>Other/Unknown</span>
                                </div>
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

                    <!-- Feature Info Panel -->
                    <div class="feature-info" id="featureInfo">
                        <button class="close-btn" id="closeFeatureInfo" style="position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                        <h4><i class="fas fa-info-circle me-2"></i>Property Details</h4>

                        <div class="info-tabs" style="display: flex; gap: 8px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                            <button class="info-tab active" data-tab="buildingDetails" style="padding: 6px 14px; background: none; border: none; cursor: pointer;">Building Details</button>
                            <button class="info-tab" data-tab="shopsList" style="padding: 6px 14px; background: none; border: none; cursor: pointer;">Shops List</button>
                            <button class="info-tab" data-tab="assessmentsList" style="padding: 6px 14px; background: none; border: none; cursor: pointer;">Assessments</button>
                        </div>

                        <div class="info-tab-content active" id="buildingDetails"><div id="featureDetails"></div></div>
                        <div class="info-tab-content" id="shopsList"><div id="shopsDetails"></div></div>
                        <div class="info-tab-content" id="assessmentsList"><div id="assessmentsDetails"></div></div>

                        <!-- Assessment Update Form -->
                        <div class="assessment-form" id="assessmentForm" style="display: none;">
                            <h5><i class="fas fa-edit me-2"></i>Update QC Values</h5>
                            <form id="updateAssessmentForm">
                                <input type="hidden" id="currentAssessmentNo">
                                <input type="hidden" id="currentid">
                                <input type="hidden" id="pointDataTableName">
                                <div class="form-group">
                                    <label for="squareFeet">Square Feet (sq.ft) <small>(QC Value)</small></label>
                                    <input type="number" id="squareFeet" class="form-control" placeholder="Enter square feet" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="usage">Usage Type <small>(QC Value)</small></label>
                                    <select id="usage" class="form-control">
                                        <option value="">Select Usage</option>
                                        <option value="RESIDENTIAL">Residential</option>
                                        <option value="COMMERCIAL">Commercial</option>
                                        <option value="MIXED">Mixed</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-update"><i class="fas fa-save me-2"></i>Update QC Values</button>
                                <div id="updateStatus" class="update-status" style="margin-top: 10px; font-size: 12px; text-align: center;"></div>
                            </form>
                        </div>
                    </div>

                    <!-- Route Info -->
                    <div class="route-info" id="routeInfo" style="position: absolute; bottom: 20px; left: 340px; background: white; border-radius: 20px; padding: 16px; z-index: 1001; max-width: 340px; display: none;">
                        <button class="close-route" id="closeRouteInfo" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer;">&times;</button>
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
    <script>
        // Toast notification function
        function showToast(message, type = 'info') {
            const toastHtml = `<div class="toast-notification ${type}"><div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div><div class="toast-message">${message}</div></div>`;
            $('body').append(toastHtml);
            const $toast = $('.toast-notification').last();
            setTimeout(() => $toast.addClass('show'), 10);
            setTimeout(() => { $toast.removeClass('show'); setTimeout(() => $toast.remove(), 300); }, 4000);
        }

        // Mobile menu toggle
        $('#mobileMenuToggle').on('click', function() {
            $('#layerSwitcher').toggleClass('open');
        });

        // Layer switcher collapsible
        $('#layerSwitcherHeader').on('click', function() {
            $('.layer-switcher-content').slideToggle();
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        });

        // Close layer switcher when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('#layerSwitcher').length && !$(e.target).closest('#mobileMenuToggle').length) {
                    $('#layerSwitcher').removeClass('open');
                }
            }
        });

        // Search tabs
        $('.search-tab').on('click', function() {
            const tab = $(this).data('tab');
            $('.search-tab').removeClass('active');
            $(this).addClass('active');
            $('.search-panel').removeClass('active');
            $(`#${tab}Panel`).addClass('active');
        });

        // Info tabs
        $('.info-tab').on('click', function() {
            const tabId = $(this).data('tab');
            $('.info-tab').removeClass('active');
            $(this).addClass('active');
            $('.info-tab-content').removeClass('active');
            $(`#${tabId}`).addClass('active');
        });

        // Close feature info
        $('#closeFeatureInfo').on('click', () => $('#featureInfo').fadeOut());
        $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());

        // Main initialization
        (function() {
            function initMap() {
                console.log("Initializing Commissioner Ward Map...");

                // Data from server
                let polygons = [], lines = [], points = [], pointDatas = [], polygonDatas = [], shopDatas = [], ward = {};
                try {
                    polygons = @json($polygons ?? []);
                    lines = @json($lines ?? []);
                    points = @json($points ?? []);
                    pointDatas = @json($pointDatas ?? []);
                    polygonDatas = @json($polygonDatas ?? []);
                    shopDatas = @json($shopDatas ?? []);
                    ward = @json($ward ?? []);
                } catch (e) { console.error("Error parsing JSON data:", e); }

                let currentLocationMarker = null, locationWatchId = null, isLiveLocationActive = false;
                let selectedFeature = null, currentGisid = null, highlightSource = null;

                // Get building usage color based on building usage (from polygon data)
                function getBuildingUsageColor(gisid) {
                    const polygonData = polygonDatas.find(data => data.gisid == gisid);
                    if (!polygonData || !polygonData.building_usage) return "#D4A13E";

                    const usage = polygonData.building_usage.toUpperCase();
                    const usageColors = {
                        'RESIDENTIAL': '#4CAF50',
                        'COMMERCIAL': '#2196F3',
                        'INDUSTRIAL': '#FF9800',
                        'MIXED': '#9C27B0',
                        'INSTITUTIONAL': '#00BCD4',
                        'GOVERNMENT': '#F44336',
                        'VACANT': '#9E9E9E'
                    };
                    return usageColors[usage] || "#D4A13E";
                }

                function getAssessmentsByGisid(gisid) {
                    return pointDatas.filter(pd => pd.point_gisid == gisid);
                }

                function getShopsByBuildingGisid(gisid) {
                    const buildingPoints = pointDatas.filter(pd => pd.point_gisid == gisid);
                    const pointDataIds = buildingPoints.map(pd => pd.id);
                    return shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
                }

                // Style Functions
                function getPolygonStyle(feature) {
                    const gisid = feature.get("gisid");
                    const sqft = feature.get("sqfeet") || "0";
                    const fillColor = getBuildingUsageColor(gisid);
                    const polygonData = polygonDatas.find(data => data.gisid == gisid);
                    const buildingUsage = polygonData ? (polygonData.building_usage || 'N/A') : 'N/A';
                    const geometry = feature.getGeometry();
                    const centerPoint = geometry.getInteriorPoint();
                    const displayUsage = buildingUsage.length > 12 ? buildingUsage.substring(0, 10) + '...' : buildingUsage;

                    return [
                        new ol.style.Style({
                            stroke: new ol.style.Stroke({ color: fillColor, width: 3 }),
                            fill: new ol.style.Fill({ color: fillColor + "33" })
                        }),
                        new ol.style.Style({
                            geometry: centerPoint,
                            text: new ol.style.Text({
                                text: `${sqft}\n${displayUsage}`,
                                font: "bold 10px Arial",
                                fill: new ol.style.Fill({ color: "#ffffff" }),
                                stroke: new ol.style.Stroke({ color: "#000000", width: 3 }),
                                textAlign: "center"
                            })
                        })
                    ];
                }

                function getPointStyle(feature) {
                    const gisid = feature.get("gisid");
                    const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                    const polygonData = polygonDatas.find(d => d.gisid == gisid);
                    let color = "#1679AB";
                    if (polygonData && pointCount > 0) {
                        color = (polygonData.number_bill == pointCount) ? "#28a745" : "#dc3545";
                    }
                    return new ol.style.Style({
                        image: new ol.style.Circle({ radius: 7, fill: new ol.style.Fill({ color: color }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) }),
                        text: new ol.style.Text({ text: gisid ? String(gisid) : "", font: "10px Arial", offsetY: -12, fill: new ol.style.Fill({ color: "#333" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) })
                    });
                }

                function getLineStyle() {
                    return new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#ffc107", width: 2 }) });
                }

                function getHighlightStyle() {
                    return new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: "#ff6600", width: 4 }),
                        fill: new ol.style.Fill({ color: "rgba(255, 102, 0, 0.2)" }),
                        image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: "#ff6600" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) })
                    });
                }

                // Layer Definitions
                const osmLayer = new ol.layer.Tile({ source: new ol.source.OSM(), visible: true });
                const satelliteLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' }), visible: false });
                const terrainLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png' }), visible: false });

                // Drone Image Layer
                let droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: "", imageExtent: [0, 0, 0, 0] }), visible: false });
                if (ward && ward.drone_image && ward.extent_left) {
                    const imageExtent = [parseFloat(ward.extent_left), parseFloat(ward.extent_bottom), parseFloat(ward.extent_right), parseFloat(ward.extent_top)];
                    const droneImageURL = "{{ asset($ward->drone_image ?? '') }}";
                    if (droneImageURL && imageExtent[0] !== 0) {
                        droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: droneImageURL, imageExtent: imageExtent }), opacity: 0.8, visible: true });
                    }
                }

                // Vector Sources
                const polygonSource = new ol.source.Vector();
                polygons.forEach(poly => {
                    try {
                        let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                        if (coords && coords.length) {
                            polygonSource.addFeature(new ol.Feature({ geometry: new ol.geom.Polygon(coords), gisid: poly.gisid, sqfeet: poly.sqfeet || "0" }));
                        }
                    } catch (e) {}
                });
                const polygonLayer = new ol.layer.Vector({ source: polygonSource, style: getPolygonStyle, visible: true });

                const lineSource = new ol.source.Vector();
                lines.forEach(l => {
                    try {
                        let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (coords && coords.length >= 2) {
                            if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
                            lineSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords), gisid: l.gisid }));
                        }
                    } catch (e) {}
                });
                const lineLayer = new ol.layer.Vector({ source: lineSource, style: getLineStyle, visible: true });

                const pointSource = new ol.source.Vector();
                points.forEach(p => {
                    try {
                        let coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (coords && coords.length === 2) {
                            pointSource.addFeature(new ol.Feature({ geometry: new ol.geom.Point(coords), gisid: p.gisid }));
                        }
                    } catch (e) {}
                });
                const pointLayer = new ol.layer.Vector({ source: pointSource, style: getPointStyle, visible: true });

                // Boundary Layer
                let boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector(), visible: true });
                if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
                    try {
                        const boundary = ward.boundary[0].map(pt => ol.proj.fromLonLat(pt));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Polygon([boundary]) })] }),
                            style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#ff0000", width: 2 }), fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.03)" }) }),
                            visible: true
                        });
                    } catch (e) {}
                }

                highlightSource = new ol.source.Vector();
                const highlightLayer = new ol.layer.Vector({ source: highlightSource, style: getHighlightStyle });
                const locationSource = new ol.source.Vector();
                const locationLayer = new ol.layer.Vector({ source: locationSource, style: new ol.style.Style({ image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: "#0066cc" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) }) }) });
                const routeSource = new ol.source.Vector();
                const routeLayer = new ol.layer.Vector({ source: routeSource, style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#0066cc", width: 4, lineDash: [8, 8] }) }) });

                // Set default center
                let defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);
                if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
                    try {
                        const lons = ward.boundary[0].map(pt => pt[0]), lats = ward.boundary[0].map(pt => pt[1]);
                        defaultCenter = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]);
                    } catch (e) {}
                }

                const map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer, terrainLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer],
                    view: new ol.View({ center: defaultCenter, zoom: 16 }),
                    controls: []
                });

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

                // Display Property Info
                function displayFullPropertyInfo(gisid, pointDataTable = null) {
                    currentGisid = gisid;
                    if (pointDataTable) $('#pointDataTableName').val(pointDataTable);

                    const polygonData = polygonDatas.find(d => d.gisid == gisid);
                    const assessments = getAssessmentsByGisid(gisid);
                    const shops = getShopsByBuildingGisid(gisid);

                    // Building Details
                    let buildingHtml = `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;
                    if (polygonData) {
                        if (polygonData.image || polygonData.image1) {
                            buildingHtml += `<div class="building-images-section"><div class="image-grid">`;
                            if (polygonData.image) {
                                const imgUrl = polygonData.image.startsWith('http') ? polygonData.image : '{{ asset('') }}' + polygonData.image.replace(/^\/+/, '');
                                buildingHtml += `<img src="${imgUrl}" class="building-image" onclick="window.openImageModal('${imgUrl}')" onerror="this.src='/images/no-image.png'">`;
                            }
                            if (polygonData.image1) {
                                const imgUrl = polygonData.image1.startsWith('http') ? polygonData.image1 : '{{ asset('') }}' + polygonData.image1.replace(/^\/+/, '');
                                buildingHtml += `<img src="${imgUrl}" class="building-image" onclick="window.openImageModal('${imgUrl}')" onerror="this.src='/images/no-image.png'">`;
                            }
                            buildingHtml += `</div></div>`;
                        }
                        buildingHtml += `<div class="info-row"><span class="info-label">Building Name:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                            <div class="info-row"><span class="info-label">Building Usage:</span><span class="info-value" style="color: ${getBuildingUsageColor(gisid)}; font-weight: bold;">${polygonData.building_usage || 'N/A'}</span></div>
                            <div class="info-row"><span class="info-label">Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                            <div class="info-row"><span class="info-label">Shops/Units:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                            <div class="info-row"><span class="info-label">Total Bills:</span><span class="info-value">${polygonData.number_bill || 'N/A'}</span></div>
                            <div class="info-row"><span class="info-label">Assessments Done:</span><span class="info-value">${assessments.length}</span></div>
                            <div class="info-row"><span class="info-label">Square Feet:</span><span class="info-value">${polygonData.sqfeet || 'N/A'} sqft</span></div>`;
                    } else {
                        buildingHtml += `<div class="info-row"><span class="info-label">Note:</span><span class="info-value">No building data available</span></div>`;
                    }
                    $('#featureDetails').html(buildingHtml);

                    // Shops List
                    let shopsHtml = '';
                    if (shops.length > 0) {
                        shopsHtml = `<div class="shop-list">`;
                        shops.forEach((shop, idx) => {
                            shopsHtml += `<div class="shop-item"><h6><span class="badge-shop">Shop ${idx + 1}</span> ${shop.shop_name || 'Unnamed'}</h6>
                                <div class="info-row"><span class="info-label">Owner:</span><span class="info-value">${shop.shop_owner_name || 'N/A'}</span></div>
                                <div class="info-row"><span class="info-label">Category:</span><span class="info-value">${shop.shop_category || 'N/A'}</span></div>
                                <div class="info-row"><span class="info-label">Mobile:</span><span class="info-value">${shop.shop_mobile || 'N/A'}</span></div>
                                <div class="info-row"><span class="info-label">License:</span><span class="info-value">${shop.license || 'N/A'}</span></div></div>`;
                        });
                        shopsHtml += `</div>`;
                    } else {
                        shopsHtml = `<div class="text-muted text-center p-3">No shops found</div>`;
                    }
                    $('#shopsDetails').html(shopsHtml);

                    // Assessments List with QC values
                    let assessmentsHtml = '';
                    if (assessments.length > 0) {
                        assessmentsHtml = `<div class="assessment-list"><div class="assessment-search-filter" style="margin-bottom: 12px;"><input type="text" id="assessmentSearchFilter" placeholder="Search assessments..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;"></div>`;
                        assessments.forEach((assessment, idx) => {
                            const qcSqfeet = assessment.qcsqfeet || assessment.sqfeet || 'N/A';
                            const qcUsage = assessment.qcusage || assessment.usage || 'N/A';
                            const hasQC = (assessment.qcsqfeet || assessment.qcusage);
                            assessmentsHtml += `<div class="assessment-item" data-assessment="${assessment.assessment || ''}" data-id="${assessment.id || ''}" data-point-data-table="${assessment.table_name || ''}" data-search="${assessment.assessment || ''} ${assessment.owner_name || ''}">
                                <h6><span class="badge-shop">Assessment ${idx + 1}</span> ${assessment.assessment || 'N/A'} ${hasQC ? '<span style="background:#28a745;" class="badge-shop">QC Verified</span>' : '<span style="background:#ffc107;" class="badge-shop">QC Pending</span>'}</h6>
                                <div class="info-row"><span class="info-label">Owner:</span><span class="info-value">${assessment.owner_name || 'N/A'}</span></div>
                                <div class="info-row"><span class="info-label">Phone:</span><span class="info-value">${assessment.phone_number || 'N/A'}</span></div>
                                <div style="background:#e8f4f8;padding:6px;border-radius:6px;margin:8px 0;"><small>Original: ${assessment.sqfeet || 'N/A'} sqft | ${assessment.usage || 'N/A'}</small></div>
                                <div style="background:#fff8e7;padding:6px;border-radius:6px;margin:8px 0;"><small>QC: <strong>${qcSqfeet} sqft | ${qcUsage}</strong></small></div>
                                <button class="btn-edit-assessment" data-id="${assessment.id}" data-assessment="${assessment.assessment}" style="width:100%;padding:6px;background:#D4A13E;color:white;border:none;border-radius:6px;cursor:pointer;">Edit QC Values</button>
                            </div>`;
                        });
                        assessmentsHtml += `</div>`;
                        $('#assessmentsDetails').html(assessmentsHtml);

                        $('#assessmentSearchFilter').on('keyup', function() {
                            const term = $(this).val().toLowerCase();
                            $('.assessment-item').each(function() {
                                $(this).toggle($(this).data('search').toLowerCase().includes(term));
                            });
                        });
                        $('.btn-edit-assessment').on('click', function(e) {
                            e.stopPropagation();
                            loadAssessmentForEdit($(this).data('assessment'), $(this).closest('.assessment-item').data('point-data-table'), $(this).data('id'));
                        });
                    } else {
                        assessmentsHtml = `<div class="text-muted text-center p-3">No assessments found</div>`;
                        $('#assessmentsDetails').html(assessmentsHtml);
                    }
                    $('#featureInfo').fadeIn();
                }

                window.openImageModal = function(imgUrl) {
                    if (!$('#imageModal').length) {
                        $('body').append(`<div id="imageModal" class="image-modal"><span class="close-modal">&times;</span><div class="modal-content"><img id="modalImage" src=""><div id="modalCaption"></div></div></div>`);
                        $('#imageModal').on('click', function(e) { if (e.target === this || $(e.target).hasClass('close-modal')) $(this).fadeOut(); });
                        $(document).on('keydown', e => { if (e.key === 'Escape') $('#imageModal').fadeOut(); });
                    }
                    $('#modalImage').attr('src', imgUrl);
                    $('#imageModal').fadeIn();
                };

                function loadAssessmentForEdit(assessmentNo, pointDataTable, assessmentId) {
                    $('#loadingSpinner').fadeIn();
                    $.ajax({
                        url: '{{ route('corporation.get.assessment.details') }}',
                        method: 'GET',
                        data: { assessment_no: assessmentNo, point_data_table: pointDataTable, assessment_id: assessmentId },
                        success: function(response) {
                            if (response.success) {
                                $('#currentAssessmentNo').val(assessmentNo);
                                $('#currentid').val(assessmentId || response.data.id);
                                $('#pointDataTableName').val(pointDataTable);
                                $('#squareFeet').val(response.data.qcsqfeet || response.data.sqfeet || '');
                                $('#usage').val(response.data.qcusage || response.data.usage || '');
                                $('#assessmentForm').slideDown();
                                showToast('Assessment loaded for editing', 'info');
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: () => showToast('Error loading assessment', 'error'),
                        complete: () => $('#loadingSpinner').fadeOut()
                    });
                }

                function updateAssessment(assessmentNo, squareFeet, usage, pointDataTable, id) {
                    $('#updateAssessmentBtn').prop('disabled', true);
                    $.ajax({
                        url: '{{ route('corporation.update.assessment') }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', assessment_no: assessmentNo, square_feet: squareFeet, usage: usage, point_data_table: pointDataTable, id: id },
                        success: function(response) {
                            if (response.success) {
                                showToast(response.message, 'success');
                                const idx = pointDatas.findIndex(pd => (id && pd.id == id) || (assessmentNo && pd.assessment == assessmentNo));
                                if (idx !== -1) {
                                    pointDatas[idx].qcsqfeet = response.data.qcsqfeet;
                                    pointDatas[idx].qcusage = response.data.qcusage;
                                }
                                if (currentGisid) displayFullPropertyInfo(currentGisid, pointDataTable);
                                $('#assessmentForm').slideUp();
                                polygonLayer.setStyle(getPolygonStyle);
                                polygonLayer.changed();
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: () => showToast('Error updating assessment', 'error'),
                        complete: () => { $('#updateAssessmentBtn').prop('disabled', false); $('#currentAssessmentNo').val(''); $('#squareFeet').val(''); $('#usage').val(''); }
                    });
                }

                // Search functions
                function searchByGISID(gisid) {
                    if (!gisid) return showToast('Enter GIS ID', 'warning');
                    $('#loadingSpinner').fadeIn();
                    highlightSource.clear();
                    let found = null;
                    polygonSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) { found = f; return true; } });
                    if (!found) pointSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) { found = f; return true; } });
                    if (found) {
                        highlightSource.addFeature(found.clone());
                        map.getView().fit(found.getGeometry().getExtent(), { padding: [50,50,50,50], duration: 1000 });
                        displayFullPropertyInfo(gisid);
                        selectedFeature = found;
                        $('#routeBtn').show();
                        showToast(`GIS ID "${gisid}" found`, 'success');
                    } else {
                        showToast(`GIS ID "${gisid}" not found`, 'error');
                    }
                    $('#loadingSpinner').fadeOut();
                }

                function searchByAssessment(assessmentNo) {
                    if (!assessmentNo) return showToast('Enter Assessment Number', 'warning');
                    $('#loadingSpinner').fadeIn();
                    highlightSource.clear();
                    const pointData = pointDatas.find(d => d.assessment == assessmentNo);
                    if (pointData && pointData.point_gisid) {
                        let found = null;
                        pointSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid.toString()) { found = f; return true; } });
                        if (found) {
                            highlightSource.addFeature(found.clone());
                            map.getView().fit(found.getGeometry().getExtent(), { padding: [50,50,50,50], duration: 1000 });
                            displayFullPropertyInfo(pointData.point_gisid, pointData.table_name);
                            selectedFeature = found;
                            $('#routeBtn').show();
                            showToast(`Assessment "${assessmentNo}" found`, 'success');
                        } else {
                            showToast('Assessment not on map', 'error');
                        }
                    } else {
                        showToast('Assessment not found', 'error');
                    }
                    $('#loadingSpinner').fadeOut();
                }

                // Live Location
                function toggleLiveLocation() {
                    if (isLiveLocationActive) {
                        if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                        locationSource.clear();
                        isLiveLocationActive = false;
                        $('#liveLocationBtn').removeClass('active').html('<i class="fas fa-location-dot me-2"></i>Live Location');
                    } else {
                        if (!navigator.geolocation) return showToast('Geolocation not supported', 'error');
                        isLiveLocationActive = true;
                        $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');
                        locationWatchId = navigator.geolocation.watchPosition(
                            pos => {
                                const coords = ol.proj.fromLonLat([pos.coords.longitude, pos.coords.latitude]);
                                locationSource.clear();
                                locationSource.addFeature(new ol.Feature({ geometry: new ol.geom.Point(coords) }));
                            },
                            err => showToast('Location error', 'error'),
                            { enableHighAccuracy: true }
                        );
                    }
                }

                // Route calculation
                async function calculateRoute() {
                    if (!selectedFeature) return showToast('Select a property first', 'warning');
                    if (!locationSource.getFeatures().length) return showToast('Enable Live Location first', 'warning');
                    $('#loadingSpinner').fadeIn();
                    routeSource.clear();
                    const start = ol.proj.toLonLat(locationSource.getFeatures()[0].getGeometry().getCoordinates());
                    const targetGeom = selectedFeature.getGeometry();
                    const end = targetGeom.getType() === 'Point' ? ol.proj.toLonLat(targetGeom.getCoordinates()) : ol.proj.toLonLat(ol.extent.getCenter(targetGeom.getExtent()));
                    try {
                        const res = await fetch(`https://router.project-osrm.org/route/v1/driving/${start[0]},${start[1]};${end[0]},${end[1]}?overview=full&geometries=geojson`);
                        const data = await res.json();
                        if (data.code === 'Ok') {
                            const route = data.routes[0];
                            const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                            routeSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));
                            const dist = route.distance < 1000 ? route.distance.toFixed(0) + ' m' : (route.distance/1000).toFixed(2) + ' km';
                            const dur = Math.floor(route.duration/60) + ' min';
                            $('#routeSummary').html(`<strong>Distance:</strong> ${dist}<br><strong>Duration:</strong> ${dur}`);
                            $('#routeInfo').fadeIn();
                            map.getView().fit(routeSource.getExtent(), { padding: [50,50,50,50] });
                        } else {
                            showToast('No route found', 'error');
                        }
                    } catch(e) { showToast('Route error', 'error'); }
                    $('#loadingSpinner').fadeOut();
                }

                // Map click handler
                map.on('click', function(evt) {
                    const target = evt.originalEvent.target;
                    if (target.closest && (target.closest('.search-container') || target.closest('.layer-switcher') || target.closest('.zoom-controls') || target.closest('.feature-info'))) return;
                    const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    if (feature && feature.get('gisid')) {
                        highlightSource.clear();
                        highlightSource.addFeature(feature.clone());
                        displayFullPropertyInfo(feature.get('gisid'));
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

                // Form submit
                $('#updateAssessmentForm').on('submit', function(e) {
                    e.preventDefault();
                    const assessmentNo = $('#currentAssessmentNo').val();
                    const id = $('#currentid').val();
                    const sqft = $('#squareFeet').val();
                    const usage = $('#usage').val();
                    const table = $('#pointDataTableName').val();
                    if (!assessmentNo && !id) return showToast('No assessment selected', 'warning');
                    if (!sqft) return showToast('Enter square feet', 'warning');
                    if (!usage) return showToast('Select usage type', 'warning');
                    updateAssessment(assessmentNo, sqft, usage, table, id);
                });

                // Button events
                $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
                $('#gisidSearchInput').on('keypress', e => { if (e.key === 'Enter') searchByGISID($('#gisidSearchInput').val().trim()); });
                $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
                $('#assessmentSearchInput').on('keypress', e => { if (e.key === 'Enter') searchByAssessment($('#assessmentSearchInput').val().trim()); });
                $('#liveLocationBtn').on('click', toggleLiveLocation);
                $('#routeBtn').on('click', calculateRoute);

                // Auto-fit map
                setTimeout(() => {
                    try {
                        const extent = ol.extent.createEmpty();
                        let has = false;
                        polygonSource.forEachFeature(f => { ol.extent.extend(extent, f.getGeometry().getExtent()); has = true; });
                        pointSource.forEachFeature(f => { ol.extent.extend(extent, f.getGeometry().getExtent()); has = true; });
                        if (has) map.getView().fit(extent, { padding: [30,30,30,30] });
                    } catch(e) {}
                }, 800);
            }

            // Wait for OpenLayers
            if (typeof ol !== 'undefined') {
                initMap();
            } else {
                const checkInterval = setInterval(() => {
                    if (typeof ol !== 'undefined') {
                        clearInterval(checkInterval);
                        initMap();
                    }
                }, 100);
                setTimeout(() => clearInterval(checkInterval), 5000);
            }
        })();
    </script>
@endpush
