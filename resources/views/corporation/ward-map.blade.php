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
        --primary: #D4A13E;
        --primary-dark: #B8892E;
        --secondary: #E86A5F;
        --dark: #0B2B40;
        --dark-light: #1A6B6E;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 20px;
    }

    .map-wrapper {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100%;
        background: #e8e8e8;
    }

    /* Search Container */
    .search-container {
        position: absolute;
        top: 16px;
        left: 16px;
        right: 16px;
        z-index: 1001;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 16px;
        transition: all 0.3s ease;
    }

    @media (min-width: 768px) {
        .search-container {
            top: 20px;
            left: 20px;
            right: auto;
            width: 380px;
            padding: 20px;
        }
    }

    .search-container h4 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        background: var(--gray-100);
        border-radius: var(--radius-md);
        padding: 4px;
    }

    .search-tab {
        flex: 1;
        padding: 8px 12px;
        border: none;
        background: transparent;
        font-weight: 500;
        color: var(--gray-600);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
    }

    .search-tab.active {
        background: var(--primary);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .search-panel {
        display: none;
    }

    .search-panel.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .search-box {
        display: flex;
        gap: 8px;
    }

    .search-box input {
        flex: 1;
        padding: 12px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.2s;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
    }

    .search-box button {
        background: var(--dark);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 0 20px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }

    .search-box button:hover {
        background: var(--primary);
        transform: translateY(-1px);
    }

    /* Mobile Menu Button */
    .mobile-menu-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        z-index: 1002;
        background: var(--dark);
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-xl);
        transition: all 0.2s;
    }

    @media (max-width: 768px) {
        .mobile-menu-btn {
            display: flex;
        }
    }

    /* Layer Switcher */
    .layer-switcher {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        z-index: 1001;
        width: 280px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .layer-switcher {
            position: fixed;
            bottom: 80px;
            right: 20px;
            top: auto;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }
        .layer-switcher.open {
            transform: translateX(0);
        }
    }

    .layer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: white;
        border-bottom: 1px solid var(--gray-200);
        cursor: pointer;
    }

    .layer-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .layer-header i {
        color: var(--primary);
        transition: transform 0.3s;
    }

    .layer-content {
        padding: 16px;
        max-height: 400px;
        overflow-y: auto;
    }

    @media (max-width: 768px) {
        .layer-content {
            max-height: 60vh;
        }
    }

    .layer-group {
        margin-bottom: 20px;
    }

    .layer-group:last-child {
        margin-bottom: 0;
    }

    .layer-group h5 {
        font-size: 12px;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .layer-option {
        display: flex;
        align-items: center;
        padding: 8px 0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .layer-option:hover {
        background: var(--gray-100);
        padding-left: 8px;
    }

    .layer-option input {
        margin-right: 10px;
        cursor: pointer;
        width: 16px;
        height: 16px;
    }

    .layer-option label {
        cursor: pointer;
        margin: 0;
        font-size: 13px;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Color Legend */
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 12px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        margin-right: 10px;
    }

    /* Feature Info Panel */
    .feature-info {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-xl);
        z-index: 1001;
        width: 480px;
        max-width: calc(100% - 40px);
        display: none;
        max-height: 85vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
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

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .info-header {
        position: sticky;
        top: 0;
        background: white;
        padding: 16px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        z-index: 10;
    }

    .info-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-btn {
        background: var(--gray-100);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
    }

    .close-btn:hover {
        background: var(--danger);
        color: white;
    }

    .info-tabs {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
        background: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
    }

    .info-tab {
        flex: 1;
        padding: 10px 16px;
        border: none;
        background: transparent;
        font-weight: 500;
        color: var(--gray-600);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
    }

    .info-tab.active {
        background: white;
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    .info-content {
        padding: 16px;
    }

    .info-tab-content {
        display: none;
    }

    .info-tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    /* Assessment Form */
    .assessment-form {
        background: var(--gray-100);
        border-radius: var(--radius-lg);
        padding: 16px;
        margin-top: 16px;
        border-top: 3px solid var(--primary);
    }

    .assessment-form h5 {
        margin: 0 0 16px 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 13px;
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
    }

    .btn-update {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px;
        cursor: pointer;
        font-weight: 500;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-update:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Controls */
    .controls {
        position: absolute;
        bottom: 20px;
        left: 20px;
        z-index: 1001;
        display: flex;
        gap: 10px;
        flex-direction: column;
    }

    @media (max-width: 768px) {
        .controls {
            bottom: 80px;
            left: 10px;
        }
    }

    .zoom-controls {
        background: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .zoom-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: white;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
        color: var(--dark);
    }

    .zoom-btn:hover {
        background: var(--primary);
        color: white;
    }

    .zoom-btn:first-child {
        border-bottom: 1px solid var(--gray-200);
    }

    .live-location-btn {
        background: var(--dark);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px 18px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .live-location-btn.active {
        background: var(--success);
    }

    .route-btn {
        background: var(--dark);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px 18px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        transition: all 0.2s;
        display: none;
        align-items: center;
        gap: 8px;
    }

    /* Route Info */
    .route-info {
        position: absolute;
        bottom: 100px;
        left: 20px;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 16px;
        z-index: 1001;
        width: 340px;
        max-width: calc(100% - 40px);
        display: none;
        animation: slideUp 0.3s ease;
    }

    @media (max-width: 768px) {
        .route-info {
            bottom: 160px;
            left: 10px;
            right: 10px;
            width: auto;
        }
    }

    .route-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .route-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
    }

    .route-summary {
        background: var(--gray-100);
        padding: 12px;
        border-radius: var(--radius-md);
        margin-bottom: 12px;
        font-size: 13px;
    }

    .directions-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .direction-step {
        padding: 10px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 12px;
        display: flex;
        gap: 12px;
    }

    .step-number {
        background: var(--dark);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
    }

    /* Toast */
    .toast-notification {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 10000;
        background: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-xl);
        padding: 14px 18px;
        min-width: 280px;
        max-width: 400px;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        border-left: 4px solid;
    }

    @media (max-width: 768px) {
        .toast-notification {
            top: 70px;
            right: 10px;
            left: 10px;
            min-width: auto;
        }
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-notification.success { border-left-color: var(--success); }
    .toast-notification.error { border-left-color: var(--danger); }
    .toast-notification.warning { border-left-color: var(--warning); }
    .toast-notification.info { border-left-color: var(--info); }

    .toast-title {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .toast-message {
        font-size: 12px;
        color: var(--gray-600);
    }

    /* Loading */
    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(11, 43, 64, 0.95);
        padding: 24px 32px;
        border-radius: var(--radius-lg);
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
        background: rgba(0,0,0,0.95);
        z-index: 10000;
        cursor: pointer;
        align-items: center;
        justify-content: center;
    }

    .image-modal .modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
    }

    .image-modal img {
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
        border-radius: var(--radius-md);
    }

    .image-modal .close-modal {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 32px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .image-modal .close-modal {
            top: -50px;
            right: 0;
            font-size: 28px;
        }
    }

    /* Assessment Items */
    .assessment-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .assessment-search-filter {
        margin-bottom: 16px;
    }

    .assessment-search-filter input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 13px;
    }

    .assessment-item {
        background: var(--gray-100);
        border-radius: var(--radius-md);
        padding: 14px;
        margin-bottom: 12px;
        border-left: 3px solid var(--primary);
        transition: all 0.2s;
        cursor: pointer;
    }

    .assessment-item:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }

    .assessment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
    }

    .badge-primary { background: var(--dark); color: white; }
    .badge-success { background: var(--success); color: white; }
    .badge-warning { background: var(--warning); color: #333; }

    .info-row {
        display: flex;
        margin-bottom: 8px;
        font-size: 12px;
    }

    .info-label {
        font-weight: 600;
        color: var(--gray-700);
        width: 100px;
        flex-shrink: 0;
    }

    .info-value {
        color: var(--gray-800);
        flex: 1;
    }

    .original-values, .qc-values {
        padding: 8px;
        border-radius: var(--radius-sm);
        margin: 8px 0;
    }

    .original-values {
        background: #e8f4f8;
    }

    .qc-values {
        background: #fff8e7;
    }

    .section-title {
        font-size: 10px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .btn-edit {
        width: 100%;
        padding: 8px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        margin-top: 10px;
        font-size: 11px;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        background: var(--secondary);
    }

    /* Shop Items */
    .shop-item {
        background: var(--gray-100);
        border-radius: var(--radius-md);
        padding: 14px;
        margin-bottom: 12px;
        border-left: 3px solid var(--dark-light);
    }

    /* Building Images */
    .building-images-section {
        margin-bottom: 16px;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    @media (max-width: 480px) {
        .image-grid {
            grid-template-columns: 1fr;
        }
    }

    .building-image {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s;
    }

    .building-image:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-md);
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: var(--gray-200);
        border-radius: 3px;
    }
    ::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="map-wrapper">
        <div id="map"></div>

        <!-- Search Container -->
        <div class="search-container">
            <h4><i class="fas fa-search"></i> Search Property</h4>
            <div class="search-tabs">
                <button class="search-tab active" data-tab="gisid">GIS ID</button>
                <button class="search-tab" data-tab="assessment">Assessment No</button>
            </div>
            <div class="search-panel active" id="gisidPanel">
                <div class="search-box">
                    <input type="text" id="gisidSearchInput" placeholder="Enter GIS ID...">
                    <button id="gisidSearchBtn"><i class="fas fa-search"></i></button>
                </div>
                <div class="search-results" id="gisidResults"></div>
            </div>
            <div class="search-panel" id="assessmentPanel">
                <div class="search-box">
                    <input type="text" id="assessmentSearchInput" placeholder="Enter Assessment Number...">
                    <button id="assessmentSearchBtn"><i class="fas fa-search"></i></button>
                </div>
                <div class="search-results" id="assessmentResults"></div>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-layer-group"></i>
        </button>

        <!-- Layer Switcher -->
        <div class="layer-switcher" id="layerSwitcher">
            <div class="layer-header" id="layerHeader">
                <h4><i class="fas fa-layer-group"></i> Layers & Legend</h4>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="layer-content">
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
                </div>
                <div class="layer-group">
                    <h5>Overlays</h5>
                    <div class="layer-option">
                        <input type="checkbox" id="showDroneImage" checked>
                        <label><i class="fas fa-drone"></i> Drone Image</label>
                    </div>
                    <div class="layer-option">
                        <input type="checkbox" id="showBoundary" checked>
                        <label><i class="fas fa-border-all"></i> Boundary</label>
                    </div>
                    <div class="layer-option">
                        <input type="checkbox" id="showPolygons" checked>
                        <label><i class="fas fa-building"></i> Buildings</label>
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
                    <div class="legend-item"><div class="legend-color" style="background: #4CAF50;"></div><span>Residential</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #2196F3;"></div><span>Commercial</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #FF9800;"></div><span>Industrial</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #9C27B0;"></div><span>Mixed Use</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #00BCD4;"></div><span>Institutional</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #D4A13E;"></div><span>Other</span></div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <div class="zoom-controls">
                <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
            </div>
            <button class="live-location-btn" id="liveLocationBtn">
                <i class="fas fa-location-dot"></i> Live Location
            </button>
            <button class="route-btn" id="routeBtn">
                <i class="fas fa-route"></i> Get Route
            </button>
        </div>

        <!-- Feature Info Panel -->
        <div class="feature-info" id="featureInfo">
            <div class="info-header">
                <h4><i class="fas fa-info-circle"></i> Property Details</h4>
                <button class="close-btn" id="closeFeatureInfo">&times;</button>
            </div>
            <div class="info-tabs">
                <button class="info-tab active" data-tab="buildingDetails">Building</button>
                <button class="info-tab" data-tab="shopsList">Shops</button>
                <button class="info-tab" data-tab="assessmentsList">Assessments</button>
            </div>
            <div class="info-content">
                <div class="info-tab-content active" id="buildingDetails"><div id="featureDetails"></div></div>
                <div class="info-tab-content" id="shopsList"><div id="shopsDetails"></div></div>
                <div class="info-tab-content" id="assessmentsList"><div id="assessmentsDetails"></div></div>
            </div>

            <!-- Assessment Update Form -->
            <div class="assessment-form" id="assessmentForm" style="display: none;">
                <h5><i class="fas fa-edit"></i> Update QC Values</h5>
                <form id="updateAssessmentForm">
                    <input type="hidden" id="currentAssessmentNo">
                    <input type="hidden" id="currentId">
                    <input type="hidden" id="pointDataTableName">
                    <div class="form-group">
                        <label>QC Square Feet (sq.ft)</label>
                        <input type="number" id="squareFeet" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>QC Usage Type</label>
                        <select id="usage" class="form-control">
                            <option value="">Select Usage</option>
                            <option value="RESIDENTIAL">Residential</option>
                            <option value="COMMERCIAL">Commercial</option>
                            <option value="MIXED">Mixed Use</option>
                            <option value="INDUSTRIAL">Industrial</option>
                            <option value="INSTITUTIONAL">Institutional</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-update"><i class="fas fa-save"></i> Update QC Values</button>
                    <div id="updateStatus" class="update-status" style="margin-top: 10px; font-size: 12px; text-align: center;"></div>
                </form>
            </div>
        </div>

        <!-- Route Info -->
        <div class="route-info" id="routeInfo">
            <div class="route-header">
                <h4><i class="fas fa-route"></i> Route Information</h4>
                <button class="close-btn" id="closeRouteInfo" style="width: 28px; height: 28px;">&times;</button>
            </div>
            <div id="routeSummary" class="route-summary"></div>
            <div id="directionsList" class="directions-list"></div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-white mb-2"></div>
            <div>Loading...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Toast notification
function showToast(message, type = 'info') {
    const icons = { success: '✓', error: '✗', warning: '⚠', info: 'ℹ' };
    const toast = $(`<div class="toast-notification ${type}">
        <div class="toast-title">${icons[type]} ${type.charAt(0).toUpperCase() + type.slice(1)}</div>
        <div class="toast-message">${message}</div>
    </div>`);
    $('body').append(toast);
    setTimeout(() => toast.addClass('show'), 10);
    setTimeout(() => { toast.removeClass('show'); setTimeout(() => toast.remove(), 300); }, 3000);
}

// Open image modal
window.openImageModal = function(url) {
    if (!$('#imageModal').length) {
        $('body').append(`<div id="imageModal" class="image-modal"><div class="modal-content"><img id="modalImage" src=""><span class="close-modal">&times;</span></div></div>`);
        $('#imageModal').on('click', function(e) {
            if (e.target === this || $(e.target).hasClass('close-modal')) $(this).fadeOut();
        });
        $(document).on('keydown', e => { if (e.key === 'Escape') $('#imageModal').fadeOut(); });
    }
    $('#modalImage').attr('src', url);
    $('#imageModal').fadeIn();
};

$(document).ready(function() {
    // Mobile menu toggle
    $('#mobileMenuBtn').on('click', function() {
        $('#layerSwitcher').toggleClass('open');
    });

    // Layer switcher collapsible
    $('#layerHeader').on('click', function() {
        $('.layer-content').slideToggle();
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Close layer switcher when clicking outside on mobile
    $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('#layerSwitcher').length && !$(e.target).closest('#mobileMenuBtn').length) {
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

    // Close buttons
    $('#closeFeatureInfo').on('click', () => $('#featureInfo').fadeOut());
    $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());
});

// Main map initialization
(function() {
    function initMap() {
        if (typeof ol === 'undefined') {
            console.error('OpenLayers not loaded');
            setTimeout(initMap, 500);
            return;
        }

        console.log("Initializing Ward Map...");

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
        } catch(e) { console.error("Error parsing data:", e); }

        let currentLocationMarker = null, locationWatchId = null, isLiveLocationActive = false;
        let selectedFeature = null, currentGisid = null, highlightSource = null;

        // Helper functions
        function getAssessmentsByGisid(gisid) {
            return pointDatas.filter(pd => pd.point_gisid == gisid);
        }

        function getShopsByBuildingGisid(gisid) {
            const buildingPoints = pointDatas.filter(pd => pd.point_gisid == gisid);
            const pointDataIds = buildingPoints.map(pd => pd.id);
            return shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
        }

        function getBuildingColor(usage) {
            if (!usage) return '#D4A13E';
            const colors = {
                'RESIDENTIAL': '#4CAF50', 'COMMERCIAL': '#2196F3', 'INDUSTRIAL': '#FF9800',
                'MIXED': '#9C27B0', 'INSTITUTIONAL': '#00BCD4', 'GOVERNMENT': '#F44336'
            };
            return colors[usage.toUpperCase()] || '#D4A13E';
        }

        // Style functions
        function getPolygonStyle(feature) {
            const gisid = feature.get('gisid');
            const sqft = feature.get('sqfeet') || '0';
            const polygonData = polygonDatas.find(d => d.gisid == gisid);
            const color = getBuildingColor(polygonData?.building_usage);
            const geometry = feature.getGeometry();
            const center = geometry.getInteriorPoint();
            return [
                new ol.style.Style({
                    stroke: new ol.style.Stroke({ color: color, width: 3 }),
                    fill: new ol.style.Fill({ color: color + '33' })
                }),
                new ol.style.Style({
                    geometry: center,
                    text: new ol.style.Text({
                        text: sqft + ' sqft',
                        font: 'bold 10px Arial',
                        fill: new ol.style.Fill({ color: '#fff' }),
                        stroke: new ol.style.Stroke({ color: '#000', width: 2 }),
                        textAlign: 'center'
                    })
                })
            ];
        }

        function getPointStyle(feature) {
            const gisid = feature.get('gisid');
            const count = pointDatas.filter(d => d.point_gisid == gisid).length;
            const polygonData = polygonDatas.find(d => d.gisid == gisid);
            let color = '#1679AB';
            if (polygonData && count > 0) {
                color = polygonData.number_bill == count ? '#28a745' : '#dc3545';
            }
            return new ol.style.Style({
                image: new ol.style.Circle({ radius: 7, fill: new ol.style.Fill({ color: color }), stroke: new ol.style.Stroke({ color: '#fff', width: 2 }) }),
                text: new ol.style.Text({ text: gisid || '', font: '10px Arial', offsetY: -12, fill: new ol.style.Fill({ color: '#333' }), stroke: new ol.style.Stroke({ color: '#fff', width: 2 }) })
            });
        }

        function getLineStyle() {
            return new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ffc107', width: 2 }) });
        }

        function getHighlightStyle() {
            return new ol.style.Style({
                stroke: new ol.style.Stroke({ color: '#ff6600', width: 4 }),
                fill: new ol.style.Fill({ color: 'rgba(255,102,0,0.2)' }),
                image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: '#ff6600' }), stroke: new ol.style.Stroke({ color: '#fff', width: 2 }) })
            });
        }

        // Create layers
        const osmLayer = new ol.layer.Tile({ source: new ol.source.OSM(), visible: true });
        const satelliteLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' }), visible: false });

        // Drone layer
        let droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: '', imageExtent: [0,0,0,0] }), visible: false });
        if (ward?.drone_image && ward?.extent_left) {
            const extent = [parseFloat(ward.extent_left), parseFloat(ward.extent_bottom), parseFloat(ward.extent_right), parseFloat(ward.extent_top)];
            const url = "{{ asset($ward->drone_image ?? '') }}";
            if (url && extent[0]) {
                droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: url, imageExtent: extent }), opacity: 0.8, visible: true });
            }
        }

        // Vector sources
        const polygonSource = new ol.source.Vector();
        polygons.forEach(poly => {
            try {
                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                if (coords?.length) {
                    polygonSource.addFeature(new ol.Feature({ geometry: new ol.geom.Polygon(coords), gisid: poly.gisid, sqfeet: poly.sqfeet || '0' }));
                }
            } catch(e) {}
        });
        const polygonLayer = new ol.layer.Vector({ source: polygonSource, style: getPolygonStyle, visible: true });

        const lineSource = new ol.source.Vector();
        lines.forEach(line => {
            try {
                let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line.coordinates;
                if (coords?.length >= 2) {
                    if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
                    lineSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords), gisid: line.gisid }));
                }
            } catch(e) {}
        });
        const lineLayer = new ol.layer.Vector({ source: lineSource, style: getLineStyle, visible: true });

        const pointSource = new ol.source.Vector();
        points.forEach(point => {
            try {
                let coords = typeof point.coordinates === 'string' ? JSON.parse(point.coordinates) : point.coordinates;
                if (coords?.length === 2) {
                    pointSource.addFeature(new ol.Feature({ geometry: new ol.geom.Point(coords), gisid: point.gisid }));
                }
            } catch(e) {}
        });
        const pointLayer = new ol.layer.Vector({ source: pointSource, style: getPointStyle, visible: true });

        // Boundary layer
        let boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector(), visible: true });
        if (ward?.boundary?.[0]?.length) {
            try {
                const boundary = ward.boundary[0].map(pt => ol.proj.fromLonLat(pt));
                boundaryLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Polygon([boundary]) })] }),
                    style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#ff0000', width: 2 }), fill: new ol.style.Fill({ color: 'rgba(255,0,0,0.03)' }) }),
                    visible: true
                });
            } catch(e) {}
        }

        highlightSource = new ol.source.Vector();
        const highlightLayer = new ol.layer.Vector({ source: highlightSource, style: getHighlightStyle });
        const locationSource = new ol.source.Vector();
        const locationLayer = new ol.layer.Vector({ source: locationSource, style: new ol.style.Style({ image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: '#0066cc' }), stroke: new ol.style.Stroke({ color: '#fff', width: 2 }) }) }) });
        const routeSource = new ol.source.Vector();
        const routeLayer = new ol.layer.Vector({ source: routeSource, style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: '#0066cc', width: 4, lineDash: [8,8] }) }) });

        // Set center
        let center = ol.proj.fromLonLat([80.2707, 13.0827]);
        if (ward?.boundary?.[0]?.length) {
            try {
                const lons = ward.boundary[0].map(p => p[0]), lats = ward.boundary[0].map(p => p[1]);
                center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]);
            } catch(e) {}
        }

        const map = new ol.Map({
            target: 'map',
            layers: [osmLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer],
            view: new ol.View({ center: center, zoom: 16 }),
            controls: []
        });

        // Layer controls
        $('input[name="baseLayer"]').on('change', function() {
            osmLayer.setVisible($(this).val() === 'osm');
            satelliteLayer.setVisible($(this).val() === 'satellite');
        });
        $('#showDroneImage').on('change', e => droneLayer.setVisible(e.target.checked));
        $('#showBoundary').on('change', e => boundaryLayer.setVisible(e.target.checked));
        $('#showPolygons').on('change', e => polygonLayer.setVisible(e.target.checked));
        $('#showLines').on('change', e => lineLayer.setVisible(e.target.checked));
        $('#showPoints').on('change', e => pointLayer.setVisible(e.target.checked));

        // Zoom controls
        $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
        $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));

        // Display property info
        function displayPropertyInfo(gisid, table = null) {
            currentGisid = gisid;
            if (table) $('#pointDataTableName').val(table);

            const polygonData = polygonDatas.find(d => d.gisid == gisid);
            const assessments = getAssessmentsByGisid(gisid);
            const shops = getShopsByBuildingGisid(gisid);

            // Building details
            let html = `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;
            if (polygonData) {
                if (polygonData.image || polygonData.image1) {
                    html += `<div class="building-images-section"><div class="image-grid">`;
                    if (polygonData.image) {
                        const url = polygonData.image.startsWith('http') ? polygonData.image : '{{ asset('') }}' + polygonData.image.replace(/^\/+/, '');
                        html += `<img src="${url}" class="building-image" onclick="openImageModal('${url}')" onerror="this.style.display='none'">`;
                    }
                    if (polygonData.image1) {
                        const url = polygonData.image1.startsWith('http') ? polygonData.image1 : '{{ asset('') }}' + polygonData.image1.replace(/^\/+/, '');
                        html += `<img src="${url}" class="building-image" onclick="openImageModal('${url}')" onerror="this.style.display='none'">`;
                    }
                    html += `</div></div>`;
                }
                html += `<div class="info-row"><span class="info-label">Building Name:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Building Usage:</span><span class="info-value" style="color:${getBuildingColor(polygonData.building_usage)};font-weight:bold">${polygonData.building_usage || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Shops/Units:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Total Bills:</span><span class="info-value">${polygonData.number_bill || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Assessments:</span><span class="info-value">${assessments.length}</span></div>
                    <div class="info-row"><span class="info-label">Square Feet:</span><span class="info-value">${polygonData.sqfeet || 'N/A'} sqft</span></div>`;
            } else {
                html += `<div class="info-row"><span class="info-label">Note:</span><span class="info-value">No building data</span></div>`;
            }
            $('#featureDetails').html(html);

            // Shops
            let shopsHtml = '';
            if (shops.length) {
                shops.forEach((shop, i) => {
                    shopsHtml += `<div class="shop-item"><div class="info-row"><span class="info-label">Shop ${i+1}:</span><span class="info-value"><strong>${shop.shop_name || 'Unnamed'}</strong></span></div>
                        <div class="info-row"><span class="info-label">Owner:</span><span class="info-value">${shop.shop_owner_name || 'N/A'}</span></div>
                        <div class="info-row"><span class="info-label">Category:</span><span class="info-value">${shop.shop_category || 'N/A'}</span></div>
                        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-value">${shop.shop_mobile || 'N/A'}</span></div></div>`;
                });
            } else {
                shopsHtml = '<div class="text-muted text-center p-3">No shops found</div>';
            }
            $('#shopsDetails').html(shopsHtml);

            // Assessments
            let assessmentsHtml = '';
            if (assessments.length) {
                assessmentsHtml = `<div class="assessment-search-filter"><input type="text" id="assessmentFilter" placeholder="Search assessments..."></div>`;
                assessments.forEach((a, i) => {
                    const qcSq = a.qcsqfeet || a.sqfeet || 'N/A';
                    const qcUse = a.qcusage || a.usage || 'N/A';
                    const hasQC = a.qcsqfeet || a.qcusage;
                    assessmentsHtml += `<div class="assessment-item" data-id="${a.id}" data-assessment="${a.assessment}" data-table="${a.table_name}" data-search="${a.assessment} ${a.owner_name}">
                        <div class="assessment-header">
                            <span class="badge badge-primary">Assessment ${i+1}</span>
                            <span class="badge ${hasQC ? 'badge-success' : 'badge-warning'}">${hasQC ? 'QC Verified' : 'QC Pending'}</span>
                        </div>
                        <div class="info-row"><span class="info-label">Number:</span><span class="info-value"><strong>${a.assessment || 'N/A'}</strong></span></div>
                        <div class="info-row"><span class="info-label">Owner:</span><span class="info-value">${a.owner_name || 'N/A'}</span></div>
                        <div class="info-row"><span class="info-label">Phone:</span><span class="info-value">${a.phone_number || 'N/A'}</span></div>
                        <div class="original-values"><div class="section-title">📋 Original Values</div>
                            <div class="info-row"><span class="info-label">Sq.Feet:</span><span class="info-value">${a.sqfeet || 'N/A'} sqft</span></div>
                            <div class="info-row"><span class="info-label">Usage:</span><span class="info-value">${a.usage || 'N/A'}</span></div></div>
                        <div class="qc-values"><div class="section-title">✅ QC Values</div>
                            <div class="info-row"><span class="info-label">QC Sq.Feet:</span><span class="info-value"><strong>${qcSq}</strong> sqft</span></div>
                            <div class="info-row"><span class="info-label">QC Usage:</span><span class="info-value"><strong>${qcUse}</strong></span></div></div>
                        <button class="btn-edit" onclick="window.editAssessment('${a.assessment}', '${a.table_name}', ${a.id})"><i class="fas fa-edit"></i> Edit QC Values</button>
                    </div>`;
                });
                $('#assessmentsDetails').html(assessmentsHtml);
                $('#assessmentFilter').on('keyup', function() {
                    const term = $(this).val().toLowerCase();
                    $('.assessment-item').each(function() {
                        $(this).toggle($(this).data('search').toLowerCase().includes(term));
                    });
                });
            } else {
                $('#assessmentsDetails').html('<div class="text-muted text-center p-3">No assessments found</div>');
            }
            $('#featureInfo').fadeIn();
        }

        window.editAssessment = function(assessmentNo, table, id) {
            $('#loadingSpinner').fadeIn();
            $.ajax({
                url: '{{ route("corporation.get.assessment.details") }}',
                method: 'GET',
                data: { assessment_no: assessmentNo, point_data_table: table, assessment_id: id },
                success: function(res) {
                    if (res.success) {
                        $('#currentAssessmentNo').val(assessmentNo);
                        $('#currentId').val(id);
                        $('#pointDataTableName').val(table);
                        $('#squareFeet').val(res.data.qcsqfeet || res.data.sqfeet || '');
                        $('#usage').val(res.data.qcusage || res.data.usage || '');
                        $('#assessmentForm').slideDown();
                        showToast('Assessment loaded for editing', 'info');
                    } else {
                        showToast(res.message || 'Error loading assessment', 'error');
                    }
                },
                error: () => showToast('Error loading assessment', 'error'),
                complete: () => $('#loadingSpinner').fadeOut()
            });
        };

        // Update assessment
        $('#updateAssessmentForm').on('submit', function(e) {
            e.preventDefault();
            const assessmentNo = $('#currentAssessmentNo').val();
            const id = $('#currentId').val();
            const sqft = $('#squareFeet').val();
            const usage = $('#usage').val();
            const table = $('#pointDataTableName').val();

            if (!assessmentNo && !id) return showToast('No assessment selected', 'warning');
            if (!sqft) return showToast('Enter square feet', 'warning');
            if (!usage) return showToast('Select usage type', 'warning');

            $('#updateAssessmentBtn').prop('disabled', true);
            $.ajax({
                url: '{{ route("corporation.update.assessment") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', assessment_no: assessmentNo, square_feet: sqft, usage: usage, point_data_table: table, id: id },
                success: function(res) {
                    if (res.success) {
                        showToast(res.message, 'success');
                        const idx = pointDatas.findIndex(p => (id && p.id == id) || (assessmentNo && p.assessment == assessmentNo));
                        if (idx !== -1) {
                            pointDatas[idx].qcsqfeet = res.data.qcsqfeet;
                            pointDatas[idx].qcusage = res.data.qcusage;
                        }
                        if (currentGisid) displayPropertyInfo(currentGisid, table);
                        $('#assessmentForm').slideUp();
                        $('#currentAssessmentNo').val('');
                        $('#currentId').val('');
                        $('#squareFeet').val('');
                        $('#usage').val('');
                    } else {
                        showToast(res.message, 'error');
                    }
                },
                error: () => showToast('Error updating assessment', 'error'),
                complete: () => $('#updateAssessmentBtn').prop('disabled', false)
            });
        });

        // Search functions
        function searchByGISID(gisid) {
            if (!gisid) return showToast('Enter GIS ID', 'warning');
            $('#loadingSpinner').fadeIn();
            highlightSource.clear();
            let found = null;
            polygonSource.forEachFeature(f => { if (f.get('gisid') == gisid) { found = f; return true; } });
            if (!found) pointSource.forEachFeature(f => { if (f.get('gisid') == gisid) { found = f; return true; } });
            if (found) {
                highlightSource.addFeature(found.clone());
                map.getView().fit(found.getGeometry().getExtent(), { padding: [50,50,50,50], duration: 1000 });
                displayPropertyInfo(gisid);
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
            if (pointData?.point_gisid) {
                let found = null;
                pointSource.forEachFeature(f => { if (f.get('gisid') == pointData.point_gisid) { found = f; return true; } });
                if (found) {
                    highlightSource.addFeature(found.clone());
                    map.getView().fit(found.getGeometry().getExtent(), { padding: [50,50,50,50], duration: 1000 });
                    displayPropertyInfo(pointData.point_gisid, pointData.table_name);
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

        // Live location
        function toggleLiveLocation() {
            if (isLiveLocationActive) {
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                locationSource.clear();
                isLiveLocationActive = false;
                $('#liveLocationBtn').removeClass('active').html('<i class="fas fa-location-dot"></i> Live Location');
                showToast('Location tracking stopped', 'info');
            } else {
                if (!navigator.geolocation) return showToast('Geolocation not supported', 'error');
                isLiveLocationActive = true;
                $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop"></i> Stop Location');
                locationWatchId = navigator.geolocation.watchPosition(
                    pos => {
                        const coords = ol.proj.fromLonLat([pos.coords.longitude, pos.coords.latitude]);
                        locationSource.clear();
                        locationSource.addFeature(new ol.Feature({ geometry: new ol.geom.Point(coords) }));
                    },
                    err => showToast('Location error', 'error'),
                    { enableHighAccuracy: true }
                );
                showToast('Location tracking started', 'success');
            }
        }

        // Calculate route
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
                    const dur = Math.floor(route.duration/60) + ' min ' + Math.floor(route.duration%60) + ' sec';
                    $('#routeSummary').html(`<strong>Distance:</strong> ${dist}<br><strong>Duration:</strong> ${dur}`);
                    let stepsHtml = '';
                    if (route.legs?.[0]?.steps) {
                        route.legs[0].steps.forEach((step, i) => {
                            if (step.maneuver?.instruction) {
                                stepsHtml += `<div class="direction-step"><span class="step-number">${i+1}</span><span>${step.maneuver.instruction} (${step.distance.toFixed(0)}m)</span></div>`;
                            }
                        });
                    }
                    $('#directionsList').html(stepsHtml || '<div class="text-muted">No step-by-step directions</div>');
                    $('#routeInfo').show();
                    map.getView().fit(routeSource.getExtent(), { padding: [50,50,50,50] });
                    showToast('Route calculated', 'success');
                } else {
                    showToast('No route found', 'error');
                }
            } catch(e) { showToast('Route error', 'error'); }
            $('#loadingSpinner').fadeOut();
        }

        // Map click handler
        map.on('click', function(evt) {
            const target = evt.originalEvent.target;
            if (target.closest?.('.search-container') || target.closest?.('.layer-switcher') || target.closest?.('.controls') || target.closest?.('.feature-info')) return;
            const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
            if (feature?.get('gisid')) {
                highlightSource.clear();
                highlightSource.addFeature(feature.clone());
                displayPropertyInfo(feature.get('gisid'));
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

        // Button events
        $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
        $('#gisidSearchInput').on('keypress', e => { if (e.key === 'Enter') searchByGISID($('#gisidSearchInput').val().trim()); });
        $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
        $('#assessmentSearchInput').on('keypress', e => { if (e.key === 'Enter') searchByAssessment($('#assessmentSearchInput').val().trim()); });
        $('#liveLocationBtn').on('click', toggleLiveLocation);
        $('#routeBtn').on('click', calculateRoute);

        // Auto-fit
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

    initMap();
})();
</script>
@endpush
