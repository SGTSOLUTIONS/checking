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
        height: 100vh;
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100%;
        background: #e8e8e8;
    }

    /* Mobile First Approach */
    .search-container {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        z-index: 1001;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        padding: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
        transition: var(--transition);
    }

    @media (min-width: 768px) {
        .search-container {
            top: 20px;
            left: 20px;
            right: auto;
            width: 360px;
            padding: 16px;
            border-radius: 24px;
        }
    }

    .search-container h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 3px solid #D4A13E;
        padding-left: 10px;
    }

    @media (min-width: 768px) {
        .search-container h4 {
            font-size: 16px;
            margin-bottom: 12px;
            border-left-width: 4px;
            padding-left: 12px;
        }
    }

    .search-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
        background: #f5f5f5;
        border-radius: 12px;
        padding: 3px;
    }

    @media (min-width: 768px) {
        .search-tabs {
            gap: 8px;
            margin-bottom: 15px;
            border-radius: 16px;
            padding: 4px;
        }
    }

    .search-tab {
        flex: 1;
        padding: 6px 10px;
        cursor: pointer;
        border: none;
        background: transparent;
        font-weight: 500;
        color: #666;
        transition: var(--transition);
        border-radius: 10px;
        font-size: 11px;
    }

    @media (min-width: 768px) {
        .search-tab {
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 13px;
        }
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
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .search-box {
        display: flex;
        gap: 8px;
    }

    .search-box input {
        flex: 1;
        padding: 8px 12px;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 13px;
        transition: var(--transition);
        background: white;
    }

    @media (min-width: 768px) {
        .search-box input {
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 14px;
        }
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
        border-radius: 12px;
        padding: 0 16px;
        cursor: pointer;
        transition: var(--transition);
        font-weight: 500;
        font-size: 13px;
    }

    @media (min-width: 768px) {
        .search-box button {
            padding: 0 20px;
            border-radius: 14px;
        }
    }

    .layer-switcher {
        position: absolute;
        bottom: 80px;
        right: 10px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        padding: 10px;
        z-index: 1001;
        width: 160px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
    }

    @media (min-width: 768px) {
        .layer-switcher {
            top: 20px;
            bottom: auto;
            right: 20px;
            width: 200px;
            padding: 14px;
            border-radius: 24px;
        }
    }

    .layer-switcher h4 {
        margin: 0 0 8px 0;
        font-size: 12px;
        font-weight: 600;
        color: #0B2B40;
        border-bottom: 2px solid #D4A13E;
        padding-bottom: 5px;
    }

    @media (min-width: 768px) {
        .layer-switcher h4 {
            font-size: 14px;
            margin-bottom: 10px;
            padding-bottom: 6px;
        }
    }

    .layer-group {
        margin-bottom: 10px;
    }

    .layer-group h5 {
        font-size: 10px;
        color: #D4A13E;
        margin-bottom: 5px;
        font-weight: 600;
    }

    @media (min-width: 768px) {
        .layer-group h5 {
            font-size: 11px;
            margin-bottom: 6px;
        }
    }

    .layer-option {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        cursor: pointer;
        font-size: 10px;
        padding: 3px 5px;
        border-radius: 6px;
        transition: var(--transition);
    }

    @media (min-width: 768px) {
        .layer-option {
            margin-bottom: 6px;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 8px;
        }
    }

    .layer-option input {
        margin-right: 6px;
        cursor: pointer;
        width: 12px;
        height: 12px;
    }

    @media (min-width: 768px) {
        .layer-option input {
            margin-right: 8px;
            width: 14px;
            height: 14px;
        }
    }

    .feature-info {
        position: absolute;
        bottom: 10px;
        left: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        padding: 12px;
        z-index: 1001;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
        max-height: 70vh;
        overflow-y: auto;
    }

    @media (min-width: 768px) {
        .feature-info {
            bottom: 20px;
            right: 20px;
            left: auto;
            width: 450px;
            max-width: calc(100% - 40px);
            padding: 16px;
            border-radius: 20px;
            max-height: 85vh;
        }
    }

    .feature-info h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 3px solid #D4A13E;
        padding-left: 10px;
    }

    @media (min-width: 768px) {
        .feature-info h4 {
            font-size: 16px;
            border-left-width: 3px;
            padding-left: 12px;
        }
    }

    .info-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
        border-bottom: 1px solid #eee;
        padding-bottom: 6px;
        flex-wrap: wrap;
    }

    @media (min-width: 768px) {
        .info-tabs {
            gap: 8px;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }
    }

    .info-tab {
        padding: 5px 10px;
        cursor: pointer;
        border: none;
        background: none;
        font-weight: 500;
        color: #666;
        border-radius: 16px;
        transition: var(--transition);
        font-size: 11px;
    }

    @media (min-width: 768px) {
        .info-tab {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
        }
    }

    .info-tab.active {
        background: var(--primary-gradient);
        color: white;
    }

    .info-row {
        margin-bottom: 8px;
        font-size: 11px;
        display: flex;
        flex-wrap: wrap;
    }

    @media (min-width: 768px) {
        .info-row {
            margin-bottom: 10px;
            font-size: 12px;
        }
    }

    .info-label {
        font-weight: 600;
        color: #0B2B40;
        width: 100px;
        font-size: 10px;
    }

    @media (min-width: 768px) {
        .info-label {
            width: 130px;
            font-size: 11px;
        }
    }

    .info-value {
        color: #555;
        flex: 1;
        word-break: break-word;
    }

    /* Assessment Form */
    .assessment-form {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 12px;
        padding: 12px;
        margin-top: 12px;
        border: 1px solid rgba(212, 161, 62, 0.2);
    }

    @media (min-width: 768px) {
        .assessment-form {
            border-radius: 16px;
            padding: 16px;
            margin-top: 15px;
        }
    }

    .assessment-form h5 {
        margin: 0 0 10px 0;
        font-size: 13px;
        font-weight: 600;
        color: #1A6B6E;
    }

    @media (min-width: 768px) {
        .assessment-form h5 {
            font-size: 14px;
            margin-bottom: 12px;
        }
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 11px;
        font-weight: 500;
        color: #0B2B40;
    }

    @media (min-width: 768px) {
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-size: 12px;
            margin-bottom: 6px;
        }
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px 10px;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
        }
    }

    .btn-update {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        cursor: pointer;
        font-weight: 500;
        width: 100%;
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .btn-update {
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 13px;
        }
    }

    /* Building Images */
    .building-images-section {
        margin-bottom: 12px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    @media (min-width: 768px) {
        .building-images-section {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 12px;
        }
    }

    .image-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 8px;
    }

    @media (min-width: 480px) {
        .image-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .image-item {
        position: relative;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: var(--shadow-sm);
    }

    .building-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    @media (min-width: 768px) {
        .building-image {
            height: 120px;
        }
    }

    .image-caption {
        text-align: center;
        padding: 5px;
        font-size: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-item:hover .image-caption {
        opacity: 1;
    }

    /* Assessment Items */
    .assessment-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px;
        margin-bottom: 10px;
        border-left: 3px solid #D4A13E;
        transition: var(--transition);
    }

    @media (min-width: 768px) {
        .assessment-item {
            border-radius: 14px;
            padding: 12px;
        }
    }

    .assessment-detail-row {
        font-size: 10px;
        margin-bottom: 4px;
        display: flex;
        flex-wrap: wrap;
    }

    @media (min-width: 768px) {
        .assessment-detail-row {
            font-size: 11px;
        }
    }

    .assessment-detail-label {
        width: 90px;
        font-weight: 500;
        color: #666;
    }

    @media (min-width: 768px) {
        .assessment-detail-label {
            width: 100px;
        }
    }

    /* Search Filter */
    .assessment-search-filter {
        margin-bottom: 10px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .assessment-search-filter input {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 11px;
    }

    /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 80px;
        left: 10px;
        z-index: 1001;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    @media (min-width: 768px) {
        .zoom-controls {
            bottom: 20px;
            left: 20px;
            border-radius: 16px;
        }
    }

    .zoom-btn {
        width: 36px;
        height: 36px;
        border: none;
        background: white;
        cursor: pointer;
        font-size: 14px;
        transition: var(--transition);
        color: #0B2B40;
    }

    @media (min-width: 768px) {
        .zoom-btn {
            width: 42px;
            height: 42px;
            font-size: 16px;
        }
    }

    .live-location-btn {
        position: absolute;
        bottom: 80px;
        left: 56px;
        z-index: 1001;
        background: var(--dark-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 500;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }

    @media (min-width: 768px) {
        .live-location-btn {
            bottom: 20px;
            left: 80px;
            border-radius: 14px;
            padding: 10px 18px;
            font-size: 13px;
        }
    }

    .route-btn {
        position: absolute;
        bottom: 80px;
        left: 160px;
        z-index: 1001;
        background: var(--dark-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 500;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
        display: none;
    }

    @media (min-width: 768px) {
        .route-btn {
            bottom: 20px;
            left: 220px;
            border-radius: 14px;
            padding: 10px 18px;
            font-size: 13px;
        }
    }

    .route-info {
        position: absolute;
        bottom: 80px;
        left: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        padding: 12px;
        z-index: 1001;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
    }

    @media (min-width: 768px) {
        .route-info {
            bottom: 20px;
            left: 320px;
            right: auto;
            width: 340px;
            border-radius: 20px;
            padding: 16px;
        }
    }

    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(11, 43, 64, 0.95);
        padding: 15px 25px;
        border-radius: 16px;
        z-index: 2000;
        display: none;
        color: white;
        text-align: center;
    }

    @media (min-width: 768px) {
        .loading-spinner {
            padding: 20px 30px;
            border-radius: 20px;
        }
    }

    .toast-notification {
        position: fixed;
        top: 10px;
        right: 10px;
        left: 10px;
        z-index: 10000;
        background: white;
        border-radius: 10px;
        box-shadow: var(--shadow-lg);
        padding: 10px 12px;
        transform: translateY(-100px);
        transition: transform 0.3s ease;
        border-left: 3px solid;
    }

    @media (min-width: 768px) {
        .toast-notification {
            top: 20px;
            right: 20px;
            left: auto;
            min-width: 280px;
            max-width: 400px;
            border-radius: 12px;
            padding: 14px 16px;
            border-left-width: 4px;
        }
    }

    .toast-notification.show {
        transform: translateY(0);
    }

    .badge-shop {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 16px;
        font-size: 9px;
        background: #1A6B6E;
        color: white;
    }

    @media (min-width: 768px) {
        .badge-shop {
            padding: 2px 10px;
            font-size: 10px;
            border-radius: 20px;
        }
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
        width: 95%;
        height: 90vh;
        margin: 5vh auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .modal-content img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 8px;
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 20px;
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    @media (min-width: 768px) {
        .close-modal {
            top: 20px;
            right: 40px;
            font-size: 40px;
        }
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

                <!-- Layer Switcher -->
                <div class="layer-switcher">
                    <h4><i class="fas fa-layer-group me-2"></i>Layers</h4>
                    <div class="layer-group">
                        <h5>Base Maps</h5>
                        <div class="layer-option"><input type="radio" name="baseLayer" value="osm" checked><label>Street Map</label></div>
                        <div class="layer-option"><input type="radio" name="baseLayer" value="satellite"><label>Satellite</label></div>
                        <div class="layer-option"><input type="radio" name="baseLayer" value="terrain"><label>Terrain</label></div>
                    </div>
                    <div class="layer-group">
                        <h5>Overlays</h5>
                        <div class="layer-option"><input type="checkbox" id="showDroneImage" checked><label>Drone Image</label></div>
                        <div class="layer-option"><input type="checkbox" id="showBoundary" checked><label>Boundary</label></div>
                        <div class="layer-option"><input type="checkbox" id="showPolygons" checked><label>Buildings</label></div>
                        <div class="layer-option"><input type="checkbox" id="showLines" checked><label>Roads</label></div>
                        <div class="layer-option"><input type="checkbox" id="showPoints" checked><label>Points</label></div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="zoom-controls">
                    <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                    <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                </div>

                <button class="live-location-btn" id="liveLocationBtn"><i class="fas fa-location-dot me-2"></i>Live Location</button>
                <button class="route-btn" id="routeBtn" style="display: none;"><i class="fas fa-route me-2"></i>Get Route</button>

                <!-- Info Panels -->
                <div class="feature-info" id="featureInfo">
                    <button class="close-btn" id="closeFeatureInfo" style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.05); border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer;">&times;</button>
                    <h4><i class="fas fa-info-circle me-2"></i>Property Details</h4>
                    <div class="info-tabs">
                        <button class="info-tab active" data-tab="buildingDetails">Building</button>
                        <button class="info-tab" data-tab="shopsList">Shops</button>
                        <button class="info-tab" data-tab="assessmentsList">Assessments</button>
                    </div>
                    <div class="info-tab-content active" id="buildingDetails"><div id="featureDetails"></div></div>
                    <div class="info-tab-content" id="shopsList"><div id="shopsDetails"></div></div>
                    <div class="info-tab-content" id="assessmentsList"><div id="assessmentsDetails"></div></div>

                    <div class="assessment-form" id="assessmentForm" style="display: none;">
                        <h5><i class="fas fa-edit me-2"></i>Update QC Values</h5>
                        <form id="updateAssessmentForm">
                            <input type="hidden" id="currentAssessmentNo">
                            <input type="hidden" id="currentid">
                            <input type="hidden" id="pointDataTableName">
                            <div class="form-group">
                                <label for="squareFeet">QC Square Feet (sq.ft)</label>
                                <input type="number" id="squareFeet" class="form-control" placeholder="Enter square feet" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="usage">QC Usage Type</label>
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
                            <button type="submit" class="btn-update"><i class="fas fa-save me-2"></i>Update QC Values</button>
                            <div id="updateStatus" class="update-status" style="margin-top: 8px; font-size: 11px; text-align: center;"></div>
                        </form>
                    </div>
                </div>

                <div class="route-info" id="routeInfo">
                    <button class="close-route" id="closeRouteInfo" style="position: absolute; top: 8px; right: 8px; background: none; border: none; cursor: pointer;">&times;</button>
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
function showToast(message, type = 'info') {
    const toastHtml = `<div class="toast-notification ${type}"><div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div><div class="toast-message">${message}</div></div>`;
    $('body').append(toastHtml);
    const $toast = $('.toast-notification').last();
    setTimeout(() => $toast.addClass('show'), 10);
    setTimeout(() => { $toast.removeClass('show'); setTimeout(() => $toast.remove(), 300); }, 4000);
}

function openImageModal(imageUrl) {
    if (!$('#imageModal').length) {
        $('body').append(`<div id="imageModal" class="image-modal"><span class="close-modal">&times;</span><div class="modal-content"><img id="modalImage" src="" alt="Building Image"><div class="modal-caption" id="modalCaption"></div></div></div>`);
        $('#imageModal').on('click', function(e) { if (e.target === this || $(e.target).hasClass('close-modal')) $('#imageModal').fadeOut(); });
        $(document).on('keydown', function(e) { if (e.key === 'Escape' && $('#imageModal').is(':visible')) $('#imageModal').fadeOut(); });
    }
    $('#modalImage').attr('src', imageUrl);
    $('#modalCaption').text(imageUrl.includes('image1') ? 'Side/Back View' : 'Front View');
    $('#imageModal').fadeIn();
}

(function() {
    function isOpenLayersLoaded() { return typeof ol !== 'undefined' && typeof ol.Map !== 'undefined'; }

    function initMap() {
        console.log("Initializing Commissioner Ward Map...");
        if (!document.getElementById('map')) { console.error("Map element not found!"); return; }

        let polygons = [], lines = [], points = [], pointDatas = [], polygonDatas = [], shopDatas = [], ward = {};
        try {
            polygons = @json($polygons ?? []);
            lines = @json($lines ?? []);
            points = @json($points ?? []);
            pointDatas = @json($pointDatas ?? []);
            polygonDatas = @json($polygonDatas ?? []);
            shopDatas = @json($shopDatas ?? []);
            ward = @json($ward ?? []);
        } catch(e) { console.error("Error parsing JSON data:", e); }

        let currentLocationMarker = null, locationWatchId = null, isLiveLocationActive = false, selectedFeature = null, currentGisid = null, highlightSource = null;

        function getAssessmentsByGisid(gisid) { return pointDatas.filter(pd => pd.point_gisid == gisid); }
        function getShopsByBuildingGisid(gisid) {
            const buildingPoints = pointDatas.filter(pd => pd.point_gisid == gisid);
            const pointDataIds = buildingPoints.map(pd => pd.id);
            return shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
        }

        function getPointStyle(feature) {
            const gisid = feature.get("gisid");
            const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
            const polygonData = polygonDatas.find(d => d.gisid == gisid);
            let color = "#1679AB";
            if (polygonData && pointCount > 0) color = (polygonData.number_bill == pointCount) ? "#28a745" : "#dc3545";
            return new ol.style.Style({
                image: new ol.style.Circle({ radius: 7, fill: new ol.style.Fill({ color: color }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) }),
                text: new ol.style.Text({ text: gisid ? String(gisid) : "", font: "10px Arial", offsetY: -12, fill: new ol.style.Fill({ color: "#333" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) })
            });
        }

        function getPolygonStyle(feature) {
            const gisid = feature.get("gisid");
            const sqft = feature.get("sqfeet") || "0";
            const polygonData = polygonDatas.find(data => data.gisid == gisid);
            const color = polygonData ? "#E86A5F" : "#D4A13E";
            const geometry = feature.getGeometry();
            const centerPoint = geometry.getInteriorPoint();
            return [new ol.style.Style({ stroke: new ol.style.Stroke({ color: color, width: 3 }), fill: new ol.style.Fill({ color: "rgba(212, 161, 62, 0.1)" }) }),
                    new ol.style.Style({ geometry: centerPoint, text: new ol.style.Text({ text: sqft + " SQFT", font: "bold 12px Arial", fill: new ol.style.Fill({ color: "#ffffff" }), stroke: new ol.style.Stroke({ color: "#000000", width: 3 }), overflow: true }) })];
        }

        function getLineStyle() { return new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#ffc107", width: 2 }) }); }
        function getHighlightStyle() { return new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#ff6600", width: 4 }), fill: new ol.style.Fill({ color: "rgba(255, 102, 0, 0.2)" }), image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: "#ff6600" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) }) }); }
        function getHumanLocationStyle() { return new ol.style.Style({ image: new ol.style.Circle({ radius: 10, fill: new ol.style.Fill({ color: "#0066cc" }), stroke: new ol.style.Stroke({ color: "#fff", width: 2 }) }) }); }

        const osmLayer = new ol.layer.Tile({ source: new ol.source.OSM(), visible: true });
        const satelliteLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' }), visible: false });
        const terrainLayer = new ol.layer.Tile({ source: new ol.source.XYZ({ url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png' }), visible: false });

        let droneLayer = null;
        if (ward && ward.drone_image && ward.extent_left) {
            const imageExtent = [parseFloat(ward.extent_left), parseFloat(ward.extent_bottom), parseFloat(ward.extent_right), parseFloat(ward.extent_top)];
            const droneImageURL = "{{ asset($ward->drone_image ?? '') }}";
            if (droneImageURL && imageExtent[0] !== 0) droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: droneImageURL, imageExtent: imageExtent, imageSmoothing: false }), opacity: 0.8, visible: true });
        }
        if (!droneLayer) droneLayer = new ol.layer.Image({ source: new ol.source.ImageStatic({ url: "", imageExtent: [0, 0, 0, 0] }), visible: false });

        const polygonSource = new ol.source.Vector();
        polygons.forEach(poly => { try { let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates; if (coords && coords.length) polygonSource.addFeature(new ol.Feature({ geometry: new ol.geom.Polygon(coords), gisid: poly.gisid, type: "Polygon", sqfeet: poly.sqfeet || "0" })); } catch(e) { console.error(e); } });
        const polygonLayer = new ol.layer.Vector({ source: polygonSource, style: getPolygonStyle, visible: true });

        const lineSource = new ol.source.Vector();
        lines.forEach(l => { try { let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates; if (coords && coords.length >= 2) { if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0]; lineSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords), gisid: l.gisid, type: "Line" })); } } catch(e) { console.error(e); } });
        const lineLayer = new ol.layer.Vector({ source: lineSource, style: getLineStyle, visible: true });

        const pointSource = new ol.source.Vector();
        points.forEach(p => { try { let coords = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates; if (coords && coords.length === 2) pointSource.addFeature(new ol.Feature({ geometry: new ol.geom.Point(coords), gisid: p.gisid, type: "Point" })); } catch(e) { console.error(e); } });
        const pointLayer = new ol.layer.Vector({ source: pointSource, style: getPointStyle, visible: true });

        let boundaryLayer = null;
        if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
            try { const boundary = ward.boundary[0]; const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt)); boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector({ features: [new ol.Feature({ geometry: new ol.geom.Polygon([transformedBoundary]) })] }), style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#ff0000", width: 2 }), fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.03)" }) }), visible: true }); }
            catch(e) { boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector(), visible: true }); }
        } else { boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector(), visible: true }); }

        highlightSource = new ol.source.Vector();
        const highlightLayer = new ol.layer.Vector({ source: highlightSource, style: getHighlightStyle });
        const locationSource = new ol.source.Vector();
        const locationLayer = new ol.layer.Vector({ source: locationSource, style: getHumanLocationStyle });
        const routeSource = new ol.source.Vector();
        const routeLayer = new ol.layer.Vector({ source: routeSource, style: new ol.style.Style({ stroke: new ol.style.Stroke({ color: "#0066cc", width: 4, lineDash: [8, 8] }) }) });

        let defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);
        if (ward && ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
            try { const boundary = ward.boundary[0]; const lons = boundary.map(pt => pt[0]); const lats = boundary.map(pt => pt[1]); const centerLon = (Math.min(...lons) + Math.max(...lons)) / 2; const centerLat = (Math.min(...lats) + Math.max(...lats)) / 2; defaultCenter = ol.proj.fromLonLat([centerLon, centerLat]); } catch(e) { console.error(e); }
        }

        const map = new ol.Map({ target: 'map', layers: [osmLayer, satelliteLayer, terrainLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer], view: new ol.View({ projection: "EPSG:3857", center: defaultCenter, zoom: 16 }), controls: [] });

        $('input[name="baseLayer"]').on('change', function() { const val = $(this).val(); osmLayer.setVisible(val === 'osm'); satelliteLayer.setVisible(val === 'satellite'); terrainLayer.setVisible(val === 'terrain'); });
        $('#showDroneImage').on('change', (e) => droneLayer.setVisible(e.target.checked));
        $('#showBoundary').on('change', (e) => boundaryLayer.setVisible(e.target.checked));
        $('#showPolygons').on('change', (e) => polygonLayer.setVisible(e.target.checked));
        $('#showLines').on('change', (e) => lineLayer.setVisible(e.target.checked));
        $('#showPoints').on('change', (e) => pointLayer.setVisible(e.target.checked));
        $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
        $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));

        $('.search-tab').on('click', function() { const tab = $(this).data('tab'); $('.search-tab').removeClass('active'); $(this).addClass('active'); $('.search-panel').removeClass('active'); $(`#${tab}Panel`).addClass('active'); $(`#${tab}Results`).hide(); });

        function displayFullPropertyInfo(gisid, pointDataTable = null) {
            currentGisid = gisid;
            const polygonData = polygonDatas.find(d => d.gisid == gisid);
            const assessments = getAssessmentsByGisid(gisid);
            const shops = getShopsByBuildingGisid(gisid);
            const pointCount = assessments.length;

            let buildingHtml = `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;
            if (polygonData) {
                if (polygonData.image || polygonData.image1) {
                    buildingHtml += `<div class="building-images-section"><div class="info-label" style="width:100%;margin-bottom:8px;">Building Images:</div><div class="image-grid">`;
                    if (polygonData.image) { const imgUrl = polygonData.image; buildingHtml += `<div class="image-item"><img src="${imgUrl}" alt="Building Image" class="building-image" onclick="openImageModal('${imgUrl}')" onerror="this.src='/images/no-image.png'"><div class="image-caption">Front View</div></div>`; }
                    if (polygonData.image1) { const imgUrl = polygonData.image1; buildingHtml += `<div class="image-item"><img src="${imgUrl}" alt="Building Image" class="building-image" onclick="openImageModal('${imgUrl}')" onerror="this.src='/images/no-image.png'"><div class="image-caption">Side/Back View</div></div>`; }
                    buildingHtml += `</div></div>`;
                }
                buildingHtml += `<div class="building-info-section"><div class="info-row"><span class="info-label">Building Name:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Building Usage:</span><span class="info-value">${polygonData.building_usage || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Shops/Units:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                    <div class="info-row"><span class="info-label">Assessments:</span><span class="info-value">${pointCount}/${polygonData.number_bill || 0}</span></div>
                    <div class="info-row"><span class="info-label">Status:</span><span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}</span></div></div>`;
            } else { buildingHtml += `<div class="info-row"><span class="info-label">Note:</span><span class="info-value">No building data available</span></div>`; }
            $('#featureDetails').html(buildingHtml);

            let shopsHtml = '';
            if (shops && shops.length > 0) {
                shopsHtml = `<div class="shop-list">`;
                shops.forEach((shop, index) => { shopsHtml += `<div class="shop-item"><h6><span class="badge-shop">Shop ${index+1}</span> ${shop.shop_name || 'Unnamed'}</h6><div class="shop-detail-row"><span class="shop-detail-label">Owner:</span><span class="shop-detail-value">${shop.shop_owner_name || 'N/A'}</span></div><div class="shop-detail-row"><span class="shop-detail-label">Mobile:</span><span class="shop-detail-value">${shop.shop_mobile || 'N/A'}</span></div></div>`; });
                shopsHtml += `</div>`;
            } else { shopsHtml = `<div class="text-muted text-center p-3">No shops found</div>`; }
            $('#shopsDetails').html(shopsHtml);

            let assessmentsHtml = '';
            if (assessments && assessments.length > 0) {
                assessmentsHtml = `<div class="assessment-list"><div class="assessment-search-filter"><input type="text" id="assessmentSearchFilter" placeholder="🔍 Search by Assessment No, Owner Name, or Phone..."><div style="margin-top:5px;font-size:10px;color:#666;">Total: ${assessments.length}</div></div><div id="assessmentsListContainer">`;
                assessments.forEach((assessment, index) => {
                    const qcSqfeet = assessment.qcsqfeet || assessment.sqfeet || 'N/A';
                    const qcUsage = assessment.qcusage || assessment.usage || assessment.bill_usage || 'N/A';
                    const originalSqfeet = assessment.sqfeet || 'N/A';
                    const originalUsage = assessment.bill_usage || assessment.usage || 'N/A';
                    const hasQC = (assessment.qcsqfeet || assessment.qcusage);
                    assessmentsHtml += `<div class="assessment-item" data-assessment="${assessment.assessment || ''}" data-id="${assessment.id || ''}" data-point-data-table="${assessment.table_name || ''}" data-search="${assessment.assessment || ''} ${assessment.owner_name || ''} ${assessment.phone_number || ''}">
                        <h6><span class="badge-shop">Assessment ${index+1}</span> ${assessment.assessment || 'N/A'} ${hasQC ? '<span style="background:#28a745;display:inline-block;padding:2px 8px;border-radius:16px;font-size:9px;color:white;margin-left:5px;">QC Done</span>' : '<span style="background:#ffc107;display:inline-block;padding:2px 8px;border-radius:16px;font-size:9px;margin-left:5px;">QC Pending</span>'}</h6>
                        <div class="assessment-detail-row"><span class="assessment-detail-label">Owner:</span><span class="assessment-detail-value">${assessment.owner_name || 'N/A'}</span></div>
                        <div class="assessment-detail-row"><span class="assessment-detail-label">Phone:</span><span class="assessment-detail-value">${assessment.phone_number || 'N/A'}</span></div>
                        <div style="margin-top:6px;padding:5px;background:#e8f4f8;border-radius:6px;"><div style="font-size:9px;color:#1A6B6E;">Original:</div><div class="assessment-detail-row"><span class="assessment-detail-label">Sq.Feet:</span><span class="assessment-detail-value">${originalSqfeet} sqft</span></div><div class="assessment-detail-row"><span class="assessment-detail-label">Usage:</span><span class="assessment-detail-value">${originalUsage}</span></div></div>
                        <div style="margin-top:6px;padding:5px;background:#fff8e7;border-radius:6px;"><div style="font-size:9px;color:#D4A13E;">QC Values:</div><div class="assessment-detail-row"><span class="assessment-detail-label">QC Sq.Feet:</span><span class="assessment-detail-value"><strong style="color:#1A6B6E;">${qcSqfeet}</strong> sqft</span></div><div class="assessment-detail-row"><span class="assessment-detail-label">QC Usage:</span><span class="assessment-detail-value"><strong style="color:#1A6B6E;">${qcUsage}</strong></span></div></div>
                        <button class="btn-edit-assessment" data-id="${assessment.id}" data-assessment="${assessment.assessment}" style="margin-top:6px;padding:5px 10px;background:#D4A13E;color:white;border:none;border-radius:6px;cursor:pointer;width:100%;font-size:10px;"><i class="fas fa-edit"></i> Edit QC</button>
                    </div>`;
                });
                assessmentsHtml += `</div></div>`;
            } else { assessmentsHtml = `<div class="text-muted text-center p-3">No assessments found</div>`; }
            $('#assessmentsDetails').html(assessmentsHtml);

            $('#assessmentSearchFilter').off('keyup').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.assessment-item').each(function() { $(this).toggle($(this).data('search').toLowerCase().includes(searchTerm)); });
            });

            $('.btn-edit-assessment').off('click').on('click', function(e) {
                e.stopPropagation();
                loadAssessmentForEdit($(this).data('assessment'), $(this).closest('.assessment-item').data('point-data-table'), $(this).data('id'));
            });

            $('#featureInfo').fadeIn();
        }

        function loadAssessmentForEdit(assessmentNo, pointDataTable, assessmentId) {
            $('#loadingSpinner').fadeIn();
            $.ajax({
                url: '{{ route("corporation.get.assessment.details") }}', method: 'GET', data: { assessment_no: assessmentNo, point_data_table: pointDataTable, assessment_id: assessmentId },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#currentAssessmentNo').val(assessmentNo);
                        $('#currentid').val(assessmentId || response.data.id);
                        $('#pointDataTableName').val(pointDataTable);
                        $('#squareFeet').val(response.data.qcsqfeet || response.data.sqfeet || '');
                        $('#usage').val(response.data.qcusage || response.data.usage || response.data.bill_usage || '');
                        const origSq = response.data.sqfeet || 'N/A', origUsg = response.data.bill_usage || response.data.usage || 'N/A';
                        $('#assessmentForm h5').html(`<i class="fas fa-edit me-2"></i>Update QC Values<br><small style="font-size:10px;">Original: ${origSq} sqft | ${origUsg}</small>`);
                        $('#assessmentForm').slideDown();
                        showToast('Assessment loaded for QC editing', 'info');
                    } else { showToast(response.message || 'Error loading assessment', 'error'); }
                }, error: function() { showToast('Error loading assessment details', 'error'); },
                complete: function() { $('#loadingSpinner').fadeOut(); }
            });
        }

        function updateAssessmentUI(assessmentId, newSqfeet, newUsage, pointDataTable) {
            $('.assessment-item').each(function() {
                if ($(this).data('id') == assessmentId) {
                    $(this).find('.assessment-detail-row').each(function() {
                        const label = $(this).find('.assessment-detail-label').text();
                        if (label === 'QC Sq.Feet:') $(this).find('.assessment-detail-value').html(`<strong style="color:#1A6B6E;">${newSqfeet}</strong> sqft`);
                        if (label === 'QC Usage:') $(this).find('.assessment-detail-value').html(`<strong style="color:#1A6B6E;">${newUsage}</strong>`);
                    });
                    $(this).find('.badge-shop').each(function() { if ($(this).text().includes('QC Pending') || $(this).text().includes('QC Done')) $(this).remove(); });
                    $(this).find('h6').append('<span style="background:#28a745;display:inline-block;padding:2px 8px;border-radius:16px;font-size:9px;color:white;margin-left:5px;">QC Done</span>');
                    return false;
                }
            });
        }

        function updateAssessment(assessmentNo, squareFeet, usage, pointDataTable, id) {
            $('#updateAssessmentBtn').prop('disabled', true);
            $('#updateStatus').html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            $.ajax({
                url: '{{ route("corporation.update.assessment") }}', method: 'POST', data: { _token: '{{ csrf_token() }}', assessment_no: assessmentNo, square_feet: squareFeet, usage: usage, point_data_table: pointDataTable, id: id },
                success: function(response) {
                    if (response.success) {
                        $('#updateStatus').html('<i class="fas fa-check-circle"></i> ' + response.message).addClass('success');
                        showToast(response.message, 'success');
                        updateAssessmentUI(id, squareFeet, usage, pointDataTable);
                        setTimeout(() => { $('#assessmentForm').slideUp(); $('#updateStatus').html('').removeClass('success'); }, 2000);
                    } else { $('#updateStatus').html('<i class="fas fa-exclamation-circle"></i> ' + response.message).addClass('error'); showToast(response.message, 'error'); }
                }, error: function(xhr) { showToast('Error updating assessment', 'error'); },
                complete: function() { $('#updateAssessmentBtn').prop('disabled', false); setTimeout(() => $('#updateStatus').html('').removeClass('error'), 3000); }
            });
        }

        $('.info-tab').on('click', function() { const tabId = $(this).data('tab'); $('.info-tab').removeClass('active'); $(this).addClass('active'); $('.info-tab-content').removeClass('active'); $(`#${tabId}`).addClass('active'); });

        function searchByGISID(gisid) {
            if (!gisid) { showToast('Please enter GIS ID', 'warning'); return; }
            $('#loadingSpinner').fadeIn(); highlightSource.clear(); $('#assessmentForm').slideUp();
            let foundFeature = null;
            polygonSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) { foundFeature = f; return true; } });
            if (!foundFeature) { pointSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) { foundFeature = f; return true; } }); }
            if (foundFeature) {
                highlightSource.addFeature(foundFeature.clone());
                map.getView().fit(foundFeature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
                displayFullPropertyInfo(gisid); selectedFeature = foundFeature; $('#routeBtn').show();
                showToast(`GIS ID "${gisid}" found`, 'success');
            } else { showToast(`GIS ID "${gisid}" not found`, 'error'); }
            $('#loadingSpinner').fadeOut();
        }

        function searchByAssessment(assessmentNo) {
            if (!assessmentNo) { showToast('Please enter Assessment Number', 'warning'); return; }
            $('#loadingSpinner').fadeIn(); highlightSource.clear();
            const pointData = pointDatas.find(d => d.assessment == assessmentNo);
            if (pointData && pointData.point_gisid) {
                let foundFeature = null;
                pointSource.forEachFeature(f => { if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid.toString()) { foundFeature = f; return true; } });
                if (foundFeature) {
                    highlightSource.addFeature(foundFeature.clone());
                    map.getView().fit(foundFeature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
                    displayFullPropertyInfo(pointData.point_gisid, pointData.table_name);
                    selectedFeature = foundFeature; $('#routeBtn').show();
                    showToast(`Assessment "${assessmentNo}" found`, 'success');
                } else { showToast(`Assessment "${assessmentNo}" not found on map`, 'error'); }
            } else { showToast(`Assessment "${assessmentNo}" not found`, 'error'); }
            $('#loadingSpinner').fadeOut();
        }

        function toggleLiveLocation() {
            if (isLiveLocationActive) {
                if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
                locationSource.clear(); currentLocationMarker = null; isLiveLocationActive = false;
                $('#liveLocationBtn').removeClass('active').html('<i class="fas fa-location-dot me-2"></i>Live Location');
                showToast('Location tracking stopped', 'info');
            } else {
                if (!navigator.geolocation) { showToast('Geolocation not supported', 'error'); return; }
                isLiveLocationActive = true;
                $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');
                locationWatchId = navigator.geolocation.watchPosition(
                    (position) => { const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]); locationSource.clear(); currentLocationMarker = new ol.Feature({ geometry: new ol.geom.Point(coords) }); locationSource.addFeature(currentLocationMarker); },
                    (error) => { showToast('Location error: ' + error.message, 'error'); toggleLiveLocation(); },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
                showToast('Location tracking started', 'success');
            }
        }

        async function calculateRoute() {
            if (!selectedFeature) { showToast('Select a property first', 'warning'); return; }
            if (!currentLocationMarker) { showToast('Enable Live Location first', 'warning'); return; }
            $('#loadingSpinner').fadeIn(); routeSource.clear();
            try {
                const startCoord = ol.proj.toLonLat(currentLocationMarker.getGeometry().getCoordinates());
                const targetGeom = selectedFeature.getGeometry();
                const endCoord = targetGeom.getType() === 'Point' ? ol.proj.toLonLat(targetGeom.getCoordinates()) : ol.proj.toLonLat(ol.extent.getCenter(targetGeom.getExtent()));
                const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`);
                const data = await response.json();
                if (data.code === 'Ok' && data.routes.length > 0) {
                    const route = data.routes[0];
                    const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                    routeSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));
                    const distance = route.distance < 1000 ? route.distance.toFixed(0) + ' meters' : (route.distance / 1000).toFixed(2) + ' km';
                    const duration = Math.floor(route.duration / 60) + ' min ' + Math.floor(route.duration % 60) + ' sec';
                    $('#routeSummary').html(`<strong>Distance:</strong> ${distance}<br><strong>Duration:</strong> ${duration}`);
                    $('#routeInfo').fadeIn();
                    map.getView().fit(routeSource.getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
                    showToast('Route calculated', 'success');
                } else { showToast('No route found', 'error'); }
            } catch (error) { showToast('Error calculating route', 'error'); }
            $('#loadingSpinner').fadeOut();
        }

        map.on('click', function(evt) {
            const target = evt.originalEvent.target;
            if (target.tagName === 'INPUT' || target.tagName === 'BUTTON' || target.closest('.search-container') || target.closest('.layer-switcher')) return;
            const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
            if (feature && feature.get('gisid')) {
                const gisid = feature.get('gisid');
                highlightSource.clear(); highlightSource.addFeature(feature.clone());
                displayFullPropertyInfo(gisid); selectedFeature = feature; $('#routeBtn').show();
            } else { $('#featureInfo').fadeOut(); $('#assessmentForm').slideUp(); highlightSource.clear(); selectedFeature = null; $('#routeBtn').hide(); }
        });

        $('#updateAssessmentForm').on('submit', function(e) {
            e.preventDefault();
            const assessmentNo = $('#currentAssessmentNo').val(), id = $('#currentid').val(), squareFeet = $('#squareFeet').val(), usage = $('#usage').val(), pointDataTable = $('#pointDataTableName').val();
            if (!assessmentNo && !id) { showToast('No assessment selected', 'warning'); return; }
            if (!squareFeet) { showToast('Please enter square feet', 'warning'); return; }
            if (!usage) { showToast('Please select usage type', 'warning'); return; }
            updateAssessment(assessmentNo, squareFeet, usage, pointDataTable, id);
        });

        $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
        $('#gisidSearchInput').on('keypress', (e) => { if (e.key === 'Enter') searchByGISID($('#gisidSearchInput').val().trim()); });
        $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
        $('#assessmentSearchInput').on('keypress', (e) => { if (e.key === 'Enter') searchByAssessment($('#assessmentSearchInput').val().trim()); });
        $('#liveLocationBtn').on('click', toggleLiveLocation);
        $('#routeBtn').on('click', calculateRoute);
        $('#closeFeatureInfo').on('click', () => { $('#featureInfo').fadeOut(); $('#assessmentForm').slideUp(); });
        $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());

        setTimeout(() => {
            try {
                const extent = ol.extent.createEmpty(); let hasExtent = false;
                polygonSource.forEachFeature(f => { ol.extent.extend(extent, f.getGeometry().getExtent()); hasExtent = true; });
                pointSource.forEachFeature(f => { ol.extent.extend(extent, f.getGeometry().getExtent()); hasExtent = true; });
                if (hasExtent && extent[0] !== Infinity) map.getView().fit(extent, { padding: [30, 30, 30, 30], duration: 1000 });
            } catch(e) { console.log("Could not auto-fit extent"); }
        }, 800);

        window.addEventListener('resize', () => setTimeout(() => map.updateSize(), 200));
        console.log("Map Loaded Successfully");
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => setTimeout(() => { if (isOpenLayersLoaded()) initMap(); else console.error("OpenLayers not loaded"); }, 500));
    else setTimeout(() => { if (isOpenLayersLoaded()) initMap(); else console.error("OpenLayers not loaded"); }, 500);
})();
</script>
@endpush
