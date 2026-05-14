@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="page-refresh-space"></div>

    <div class="container-fluid p-0">
        <div id="map"></div>

        <button class="mobile-menu-btn" id="mobileMenuBtn" type="button">
            <i class="fas fa-layer-group"></i>
        </button>

        <button class="mobile-legend-btn" id="mobileLegendBtn" type="button">
            <i class="fas fa-info-circle"></i>
        </button>
    </div>
@endsection

@push('styles')
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
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
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            overscroll-behavior-y: auto;
            -webkit-overflow-scrolling: touch;
            background: #0b0f19;
        }

        body {
            padding: 0;
            margin: 0;
        }

        .page-refresh-space {
            display: none;
        }

        #map {
            width: 100%;
            height: 100vh;
            height: calc(100dvh - 1px);
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
            background: #101522;
        }

        @media (max-width: 768px) {
            .page-refresh-space {
                display: block;
                height: 56px;
                width: 100%;
            }

            #map {
                height: calc(100dvh - 56px);
                min-height: 500px;
            }
        }

        .mobile-menu-btn,
        .mobile-legend-btn {
            position: fixed;
            bottom: max(20px, env(safe-area-inset-bottom));
            right: 20px;
            z-index: 1002;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
            font-size: 20px;
        }

        .mobile-legend-btn {
            right: 80px;
            background: rgba(255, 193, 7, 0.9);
        }

        .mobile-menu-btn:active,
        .mobile-legend-btn:active {
            transform: scale(0.95);
        }

        @media (max-width: 768px) {

            .mobile-menu-btn,
            .mobile-legend-btn {
                display: flex;
            }
        }

        .layer-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            font-size: 12px;
            min-width: 160px;
            max-width: 260px;
            backdrop-filter: blur(10px);
            touch-action: auto;
            pointer-events: auto;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: white;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: calc(84px + env(safe-area-inset-bottom));
                right: 12px;
                left: 12px;
                top: auto;
                transform: translateY(120%);
                transition: transform 0.3s ease;
                min-width: 0;
                max-width: none;
                z-index: 1003;
            }

            .layer-switcher.open {
                transform: translateY(0);
            }
        }

        .layer-switcher h5 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
        }

        .layer-group {
            margin-bottom: 12px;
        }

        .layer-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 8px 0;
            font-size: 12px;
            color: #eee;
            touch-action: manipulation;
        }

        .layer-group input {
            cursor: pointer;
            margin: 0;
            width: 16px;
            height: 16px;
        }

        .layer-group .group-title {
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 5px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            font-size: 12px;
            min-width: 140px;
            max-width: 240px;
            backdrop-filter: blur(10px);
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: white;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: calc(148px + env(safe-area-inset-bottom));
                right: 12px;
                left: 12px;
                top: auto;
                transform: translateY(120%);
                transition: transform 0.3s ease;
                min-width: 0;
                max-width: none;
                pointer-events: auto;
                z-index: 1003;
            }

            .map-legend.open {
                transform: translateY(0);
                pointer-events: auto;
            }
        }

        .map-legend h5 {
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .legend-color.building {
            background: rgba(255, 68, 68, 0.5);
            border: 2px solid #ff4444;
        }

        .legend-color.road {
            background: none;
            border: 2px solid #ffc107;
            height: 3px;
            margin-top: 8px;
        }

        .legend-color.boundary {
            background: none;
            border: 2px dashed #ff0000;
        }

        .legend-color.drone {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid #fff;
        }

        .zoom-controls {
            position: fixed;
            bottom: max(20px, env(safe-area-inset-bottom));
            left: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .zoom-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s;
            touch-action: manipulation;
            color: white;
        }

        .zoom-btn:active {
            background: #1679AB;
            color: white;
            transform: scale(0.95);
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .ol-viewport,
        .ol-viewport canvas {
            touch-action: pan-x pan-y pinch-zoom;
        }

        .map-loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 5000;
            font-size: 14px;
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            backdrop-filter: blur(10px);
        }

        .ol-popup {
            position: absolute;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
            color: white;
            border-radius: 16px;
            padding: 0;
            width: min(92vw, 360px);
            min-width: 280px;
            max-width: 360px;
            max-height: min(78vh, 680px);
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            z-index: 3000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            border: 1px solid rgba(255, 68, 68, 0.4);
            backdrop-filter: blur(5px);
            display: none;
        }

        .ol-popup.show {
            display: block;
        }

        @media (max-width: 768px) {
            .ol-popup {
                position: fixed !important;
                left: 10px !important;
                right: 10px !important;
                bottom: calc(10px + env(safe-area-inset-bottom)) !important;
                top: auto !important;
                width: auto !important;
                min-width: 0 !important;
                max-width: none !important;
                max-height: min(68dvh, 520px) !important;
                border-radius: 18px !important;
                transform: none !important;
                overflow: hidden !important;
                z-index: 4000 !important;
            }

            .ol-popup:after,
            .ol-popup:before {
                display: none !important;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .ol-popup {
                width: min(50vw, 420px);
                max-width: 420px !important;
                max-height: 72vh !important;
            }
        }

        .popup-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%);
            padding: 14px 16px;
            border-bottom: 2px solid #ff4444;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .popup-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #ff4444;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .popup-header h4 i {
            font-size: 16px;
        }

        .popup-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .popup-close:active {
            background: #ff4444;
            transform: scale(1.05);
        }

        .popup-tabs {
            display: flex;
            background: #141424;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0 8px;
        }

        .popup-tab {
            flex: 1;
            min-width: 0;
            background: none;
            border: none;
            color: #aaa;
            font-size: 12px;
            font-weight: 600;
            padding: 10px 4px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            white-space: nowrap;
        }

        .popup-tab i {
            font-size: 13px;
        }

        .popup-tab.active {
            color: #ff4444;
        }

        .popup-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ff4444;
        }

        .popup-tab:active {
            background: rgba(255, 68, 68, 0.1);
        }

        @media (max-width: 768px) {
            .popup-tab {
                font-size: 10px;
                padding: 10px 2px;
                gap: 4px;
            }

            .popup-tab i {
                font-size: 11px;
            }
        }

        .popup-tab-content {
            display: none;
            padding: 14px;
            max-height: calc(min(68dvh, 520px) - 96px);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .popup-tab-content.active {
            display: block;
        }

        @media (min-width: 769px) {
            .popup-tab-content {
                max-height: calc(min(78vh, 680px) - 96px);
            }
        }

        .popup-tab-content::-webkit-scrollbar {
            width: 4px;
        }

        .popup-tab-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }

        .popup-tab-content::-webkit-scrollbar-thumb {
            background: #ff4444;
            border-radius: 4px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 8px;
            gap: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: #ffc107;
            width: 108px;
            flex-shrink: 0;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        .detail-value {
            color: #eee;
            flex: 1;
            word-break: break-word;
            overflow-wrap: anywhere;
            font-size: 12px;
            min-width: 0;
        }

        .detail-value strong {
            color: #ff4444;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
                margin-bottom: 10px;
                gap: 4px;
            }

            .detail-label {
                width: 100%;
            }
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 6px;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
            border-left: 3px solid #ffc107;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .assessment-card:active {
            transform: translateX(3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .assessment-header {
            background: rgba(255, 193, 7, 0.15);
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .assessment-number {
            font-weight: 700;
            font-size: 12px;
            color: #ffc107;
            word-break: break-word;
        }

        .assessment-status {
            font-size: 10px;
        }

        .assessment-body {
            padding: 12px;
        }

        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 11px;
            gap: 8px;
        }

        .assessment-label {
            width: 80px;
            color: #aaa;
            flex-shrink: 0;
        }

        .assessment-value {
            color: #fff;
            flex: 1;
            min-width: 0;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        @media (max-width: 768px) {
            .assessment-row {
                flex-direction: column;
                gap: 2px;
            }

            .assessment-label {
                width: 100%;
            }
        }

        .shop-item {
            background: rgba(255, 68, 68, 0.1);
            border-radius: 10px;
            padding: 10px 12px;
            margin-top: 8px;
            border: 1px solid rgba(255, 68, 68, 0.2);
        }

        .shop-name {
            font-weight: 700;
            color: #ff4444;
            font-size: 12px;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .shop-detail {
            font-size: 10px;
            display: flex;
            margin-bottom: 4px;
            gap: 8px;
        }

        .shop-detail-label {
            width: 55px;
            color: #aaa;
            flex-shrink: 0;
        }

        .shop-detail-value {
            color: #ddd;
            flex: 1;
            min-width: 0;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        @media (max-width: 768px) {
            .shop-detail {
                flex-direction: column;
                gap: 2px;
            }

            .shop-detail-label {
                width: 100%;
            }
        }

        .section-icon {
            margin-right: 8px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .assessment-form-container {
            margin-top: 10px;
            padding: 12px;
            background: #1a1a2e;
            border-radius: 12px;
            border-left: 3px solid #ff4444;
        }

        .assessment-form-container h4 {
            font-size: 14px;
        }

        .assessment-form-container input,
        .assessment-form-container select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ff4444;
            background: #0f0f1a;
            color: white;
            font-size: 14px;
        }

        .assessment-form-container button {
            padding: 11px;
            font-size: 14px;
        }

        .close-form-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0 8px;
        }

        .close-form-btn:active {
            color: #ff4444;
        }

        #map canvas {
            pointer-events: auto;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        let map;
        let polygonLayer;
        let lineLayer;
        let imageLayer;
        let boundaryLayer;
        let osmLayer;
        let satelliteLayer;
        let currentBaseLayer = 'osm';
        let popupOverlay;
        let popupElement;
        let currentActiveTab = 'building';
        let lastPopupCoordinate = null;
        let lastPopupGisId = null;

        let polygonDatas = @json($polygonDatas ?? []);
        let polygons = @json($polygons ?? []);
        let lines = @json($lines ?? []);

        let wardData = {
            ward_no: @json($ward->ward_no ?? ''),
            drone_image: @json($ward->drone_image ?? null),
            extent_left: @json($ward->extent_left ?? null),
            extent_bottom: @json($ward->extent_bottom ?? null),
            extent_right: @json($ward->extent_right ?? null),
            extent_top: @json($ward->extent_top ?? null),
            boundary: @json($ward->boundary ?? null)
        };

        function isMobileView() {
            return window.innerWidth <= 768;
        }

        function showLoading(show) {
            let loadingEl = document.getElementById('mapLoading');
            if (show) {
                if (!loadingEl) {
                    loadingEl = document.createElement('div');
                    loadingEl.id = 'mapLoading';
                    loadingEl.className = 'map-loading';
                    loadingEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading map...';
                    document.body.appendChild(loadingEl);
                }
                loadingEl.style.display = 'block';
            } else if (loadingEl) {
                loadingEl.style.display = 'none';
            }
        }

        function createPopup() {
            popupElement = document.createElement('div');
            popupElement.className = 'ol-popup';
            popupElement.style.display = 'none';
            document.body.appendChild(popupElement);

            popupOverlay = new ol.Overlay({
                element: popupElement,
                positioning: 'bottom-center',
                stopEvent: true,
                offset: [0, -10],
                autoPan: {
                    animation: {
                        duration: 250
                    }
                }
            });

            return popupOverlay;
        }

        function applyPopupMode(coordinate = null) {
            if (!popupElement) return;

            if (isMobileView()) {
                if (popupOverlay) popupOverlay.setPosition(undefined);

                popupElement.style.position = 'fixed';
                popupElement.style.left = '10px';
                popupElement.style.right = '10px';
                popupElement.style.bottom = '10px';
                popupElement.style.top = 'auto';
            } else {
                popupElement.style.position = 'absolute';
                popupElement.style.left = '';
                popupElement.style.right = '';
                popupElement.style.bottom = '';
                popupElement.style.top = '';

                if (popupOverlay && coordinate) {
                    popupOverlay.setPosition(coordinate);
                }
            }
        }

        window.switchTab = function(tabId) {
            document.querySelectorAll('.popup-tab-content').forEach(content => {
                content.classList.remove('active');
            });

            document.querySelectorAll('.popup-tab').forEach(tab => {
                tab.classList.remove('active');
            });

            const selectedContent = document.getElementById(`tab-${tabId}`);
            if (selectedContent) selectedContent.classList.add('active');

            const selectedTab = document.querySelector(`.popup-tab[data-tab="${tabId}"]`);
            if (selectedTab) selectedTab.classList.add('active');

            currentActiveTab = tabId;
        };

        window.closePopup = function() {
            if (!popupElement) return;

            popupElement.classList.remove('show');
            popupElement.style.display = 'none';
            popupElement.style.left = '';
            popupElement.style.right = '';
            popupElement.style.top = '';
            popupElement.style.bottom = '';
            popupElement.style.position = '';

            if (popupOverlay) {
                popupOverlay.setPosition(undefined);
            }
        };

        function bindAssessmentFormEvents() {
            $('.assessment-card').off('click').on('click', function(e) {
                e.stopPropagation();

                const assessmentId = $(this).data('id');
                const assessmentNumber = $(this).data('assessment');

                $('.assessment-form-container').remove();

                const formHtml = `
                    <div class="assessment-form-container">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;gap:10px;">
                            <h4 style="color:#ffc107;margin:0;">QC Form - ${assessmentNumber}</h4>
                            <button class="close-form-btn" type="button">&times;</button>
                        </div>
                        <form id="simpleQCForm_${assessmentId}">
                            <input type="hidden" name="assessment_id" value="${assessmentId}">
                            <div style="margin-bottom:12px;">
                                <label style="color:#ffc107;display:block;margin-bottom:5px;">QC Square Feet:</label>
                                <input type="number" name="qc_sqfeet">
                            </div>
                            <div style="margin-bottom:12px;">
                                <label style="color:#ffc107;display:block;margin-bottom:5px;">QC Usage:</label>
                                <select name="qc_usage">
                                    <option value="">Select</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Industrial">Industrial</option>
                                </select>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label style="color:#ffc107;display:block;margin-bottom:5px;">Tax Amount (₹):</label>
                                <input type="number" name="tax_amount">
                            </div>
                            <div style="display:flex;gap:10px;margin-top:15px;">
                                <button type="submit" style="flex:1;background:#28a745;color:white;border:none;border-radius:6px;">Save</button>
                                <button type="button" class="cancel-form-btn" style="flex:1;background:#dc3545;color:white;border:none;border-radius:6px;">Cancel</button>
                            </div>
                        </form>
                    </div>
                `;

                $(this).after(formHtml);

                $(`#simpleQCForm_${assessmentId}`).on('submit', function(e) {
                    e.preventDefault();

                    const qcSqfeet = $(this).find('input[name="qc_sqfeet"]').val();
                    const qcUsage = $(this).find('select[name="qc_usage"]').val();
                    const taxAmount = $(this).find('input[name="tax_amount"]').val();

                    const isComplete = qcSqfeet && qcUsage && taxAmount;
                    const statusText = isComplete ? 'QC Complete' : 'QC Pending';

                    if (isComplete) {
                        $(this).closest('.assessment-card').find('.badge')
                            .removeClass('badge-warning')
                            .addClass('badge-success')
                            .html('<i class="fas fa-check-circle"></i> QC Complete');
                    } else {
                        $(this).closest('.assessment-card').find('.badge')
                            .removeClass('badge-success')
                            .addClass('badge-warning')
                            .html('<i class="fas fa-clock"></i> QC Pending');
                    }

                    alert('QC Saved! Status: ' + statusText);
                    $('.assessment-form-container').remove();
                });

                $('.close-form-btn, .cancel-form-btn').off('click').on('click', function() {
                    $('.assessment-form-container').remove();
                });
            });
        }

        function showPopup(gisid, coordinate) {
            const polyData = polygonDatas.find(p => p.gisid == gisid);

            if (!polyData) {
                console.log('No data found for GIS ID:', gisid);
                return;
            }

            lastPopupCoordinate = coordinate;
            lastPopupGisId = gisid;

            const assessments = polyData.pointdata || [];
            const allShops = [];

            assessments.forEach((assessment, idx) => {
                if (assessment.shops && assessment.shops.length > 0) {
                    assessment.shops.forEach(shop => {
                        allShops.push({
                            ...shop,
                            assessmentNumber: assessment.assessment || `Bill ${idx + 1}`,
                            assessmentId: idx
                        });
                    });
                }
            });

            let buildingHtml = `
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-fingerprint section-icon"></i> GIS ID:</div>
                    <div class="detail-value"><strong>${polyData.gisid || 'N/A'}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-building section-icon"></i> Building Usage:</div>
                    <div class="detail-value">${polyData.building_usage || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-home section-icon"></i> Building Type:</div>
                    <div class="detail-value">${polyData.building_type || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-hard-hat section-icon"></i> Construction:</div>
                    <div class="detail-value">${polyData.construction_type || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-layer-group section-icon"></i> Floors:</div>
                    <div class="detail-value">${polyData.number_floor || '0'} (${polyData.floor_percentage || '100'}% area)</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-arrow-down section-icon"></i> Basement:</div>
                    <div class="detail-value">${polyData.basement || '0'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-receipt section-icon"></i> Total Bills:</div>
                    <div class="detail-value">${polyData.number_bill || '0'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-chart-line section-icon"></i> Total Points:</div>
                    <div class="detail-value">${polyData.total_points || '0'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-store section-icon"></i> Total Shops:</div>
                    <div class="detail-value">${polyData.total_shops || '0'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-road section-icon"></i> Road Name:</div>
                    <div class="detail-value">${polyData.road_name || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-map-pin section-icon"></i> Zone:</div>
                    <div class="detail-value">${polyData.zone || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-tint section-icon"></i> Water Connection:</div>
                    <div class="detail-value">${polyData.water_connection || 'N/A'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-trash-alt section-icon"></i> UGD:</div>
                    <div class="detail-value">${polyData.ugd || 'N/A'}</div>
                </div>
            `;

            if (polyData.remarks) {
                buildingHtml += `
                    <div class="detail-row">
                        <div class="detail-label"><i class="fas fa-comment section-icon"></i> Remarks:</div>
                        <div class="detail-value">${polyData.remarks}</div>
                    </div>
                `;
            }

            let assessmentsHtml = '';
            if (assessments.length === 0) {
                assessmentsHtml = `<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessment records found</p></div>`;
            } else {
                assessments.forEach((assessment, idx) => {
                    const hasQC = assessment.qcsqfeet || assessment.qcusage;
                    assessmentsHtml += `
                        <div class="assessment-card" data-id="${assessment.id || ''}" data-assessment="${assessment.assessment || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${assessment.assessment || `Assessment ${idx + 1}`}</span>
                                <span class="assessment-status">
                                    <span class="badge ${hasQC ? 'badge-success' : 'badge-warning'}">
                                        ${hasQC ? '<i class="fas fa-check-circle"></i> QC Done' : '<i class="fas fa-clock"></i> QC Pending'}
                                    </span>
                                </span>
                            </div>
                            <div class="assessment-body">
                                <div class="assessment-row">
                                    <div class="assessment-label">Owner:</div>
                                    <div class="assessment-value"><strong>${assessment.owner_name || assessment.present_owner_name || 'N/A'}</strong></div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">Phone:</div>
                                    <div class="assessment-value">${assessment.phone_number || 'N/A'}</div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">Floor:</div>
                                    <div class="assessment-value">${assessment.floor || 'N/A'}</div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">Usage:</div>
                                    <div class="assessment-value">${assessment.bill_usage || 'N/A'}</div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">QC Sqft:</div>
                                    <div class="assessment-value">${assessment.qcsqfeet || assessment.sqfeet || 'N/A'} sqft</div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">QC Usage:</div>
                                    <div class="assessment-value">${assessment.qcusage || assessment.usage || 'N/A'}</div>
                                </div>
                                <div class="assessment-row">
                                    <div class="assessment-label">Shops:</div>
                                    <div class="assessment-value">${(assessment.shops || []).length}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            let shopsHtml = '';
            if (allShops.length === 0) {
                shopsHtml = `<div class="empty-state"><i class="fas fa-store"></i><p>No shop records found</p></div>`;
            } else {
                allShops.forEach((shop, idx) => {
                    shopsHtml += `
                        <div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${shop.shop_name || `Shop ${idx + 1}`}</div>
                            <div class="shop-detail">
                                <div class="shop-detail-label">Category:</div>
                                <div class="shop-detail-value">${shop.shop_category || 'N/A'}</div>
                            </div>
                            <div class="shop-detail">
                                <div class="shop-detail-label">Owner:</div>
                                <div class="shop-detail-value">${shop.shop_owner_name || 'N/A'}</div>
                            </div>
                            <div class="shop-detail">
                                <div class="shop-detail-label">Mobile:</div>
                                <div class="shop-detail-value">${shop.shop_mobile || 'N/A'}</div>
                            </div>
                            <div class="shop-detail">
                                <div class="shop-detail-label">Assessment:</div>
                                <div class="shop-detail-value">${shop.assessmentNumber || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                });
            }

            const html = `
                <div class="popup-header">
                    <h4><i class="fas fa-building"></i> Building Details</h4>
                    <button class="popup-close" type="button" onclick="closePopup()">&times;</button>
                </div>
                <div class="popup-tabs">
                    <button class="popup-tab ${currentActiveTab === 'building' ? 'active' : ''}" data-tab="building" type="button" onclick="switchTab('building')">
                        <i class="fas fa-info-circle"></i> Building
                    </button>
                    <button class="popup-tab ${currentActiveTab === 'assessments' ? 'active' : ''}" data-tab="assessments" type="button" onclick="switchTab('assessments')">
                        <i class="fas fa-receipt"></i> Assessments ${assessments.length > 0 ? `<span class="badge badge-info">${assessments.length}</span>` : ''}
                    </button>
                    <button class="popup-tab ${currentActiveTab === 'shops' ? 'active' : ''}" data-tab="shops" type="button" onclick="switchTab('shops')">
                        <i class="fas fa-store"></i> Shops ${allShops.length > 0 ? `<span class="badge badge-info">${allShops.length}</span>` : ''}
                    </button>
                </div>
                <div id="tab-building" class="popup-tab-content ${currentActiveTab === 'building' ? 'active' : ''}">
                    ${buildingHtml}
                </div>
                <div id="tab-assessments" class="popup-tab-content ${currentActiveTab === 'assessments' ? 'active' : ''}">
                    ${assessmentsHtml}
                </div>
                <div id="tab-shops" class="popup-tab-content ${currentActiveTab === 'shops' ? 'active' : ''}">
                    ${shopsHtml}
                </div>
            `;

            popupElement.innerHTML = html;
            popupElement.style.display = 'block';
            popupElement.classList.add('show');

            applyPopupMode(coordinate);
            bindAssessmentFormEvents();
        }

        function initMap() {
            showLoading(true);

            osmLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                visible: true
            });

            satelliteLayer = new ol.layer.Tile({
                source: new ol.source.XYZ({
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    attributions: 'Tiles &copy; Esri'
                }),
                visible: false
            });

            let droneImage = wardData.drone_image;
            let extentLeft = wardData.extent_left;
            let extentBottom = wardData.extent_bottom;
            let extentRight = wardData.extent_right;
            let extentTop = wardData.extent_top;

            let imageUrl = null;
            if (droneImage) {
                let cleanPath = droneImage.replace(/^\/+/, '');
                imageUrl = "{{ asset('') }}" + cleanPath;
            }

            let hasDroneImage = false;
            const hasValidExtent = extentLeft !== null && extentBottom !== null &&
                extentRight !== null && extentTop !== null;

            if (imageUrl && hasValidExtent) {
                try {
                    const imageExtent = [
                        parseFloat(extentLeft),
                        parseFloat(extentBottom),
                        parseFloat(extentRight),
                        parseFloat(extentTop)
                    ];

                    imageLayer = new ol.layer.Image({
                        source: new ol.source.ImageStatic({
                            url: imageUrl,
                            imageExtent: imageExtent,
                            projection: 'EPSG:3857'
                        }),
                        opacity: 1.0,
                        visible: true
                    });
                    hasDroneImage = true;
                } catch (e) {
                    console.error('Error creating drone image layer:', e);
                    imageLayer = null;
                }
            }

            let boundary = wardData.boundary;
            let boundaryExtent = null;

            if (boundary && boundary.length > 0 && boundary[0] && boundary[0].length) {
                try {
                    const boundaryCoords = boundary[0].map(coord => ol.proj.fromLonLat(coord));
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon([boundaryCoords])
                    });

                    boundaryLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [feature]
                        }),
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: '#ff0000',
                                width: 3,
                                lineDash: [10, 5]
                            }),
                            fill: new ol.style.Fill({
                                color: 'rgba(255, 0, 0, 0.05)'
                            })
                        }),
                        visible: true
                    });

                    boundaryExtent = feature.getGeometry().getExtent();
                } catch (e) {
                    console.error('Error creating boundary:', e);
                }
            }

            let center = ol.proj.fromLonLat([80.2707, 13.0827]);
            let zoom = 18;

            map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer],
                view: new ol.View({
                    center: center,
                    zoom: zoom
                }),
                controls: []
            });

            const popup = createPopup();
            map.addOverlay(popup);

            if (imageLayer) map.addLayer(imageLayer);
            if (boundaryLayer) map.addLayer(boundaryLayer);

            addLayerSwitcher(hasDroneImage);
            addLegend(hasDroneImage);
            addZoomControls();
            addMobileControls();
            refreshLayers();

            setTimeout(() => {
                if (boundaryExtent && boundaryExtent.length === 4) {
                    map.getView().fit(boundaryExtent, {
                        padding: isMobileView() ? [30, 20, 120, 20] : [50, 50, 50, 50],
                        duration: 1000,
                        maxZoom: 20
                    });
                }
                showLoading(false);
            }, 500);
        }

        function addLayerSwitcher(hasDroneImage) {
            const switcher = document.createElement('div');
            switcher.className = 'layer-switcher';
            switcher.id = 'layerSwitcher';
            switcher.innerHTML = `
                <h5><i class="fas fa-layer-group"></i> Layers</h5>
                <div class="layer-group">
                    <div class="group-title">Base Maps</div>
                    <label><input type="radio" name="baseLayer" value="osm" ${currentBaseLayer === 'osm' ? 'checked' : ''}> <i class="fas fa-map"></i> OpenStreetMap</label>
                    <label><input type="radio" name="baseLayer" value="satellite" ${currentBaseLayer === 'satellite' ? 'checked' : ''}> <i class="fas fa-satellite"></i> Satellite</label>
                </div>
                <div class="layer-group">
                    <div class="group-title">Overlays</div>
                    <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
                    <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
                    <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>
                    ${hasDroneImage ? `<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>` : ''}
                </div>
            `;
            document.body.appendChild(switcher);

            document.querySelectorAll('input[name="baseLayer"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    currentBaseLayer = e.target.value;
                    osmLayer.setVisible(currentBaseLayer === 'osm');
                    satelliteLayer.setVisible(currentBaseLayer === 'satellite');
                });
            });

            document.getElementById('toggleBuildings')?.addEventListener('change', (e) => {
                if (polygonLayer) polygonLayer.setVisible(e.target.checked);
            });

            document.getElementById('toggleRoads')?.addEventListener('change', (e) => {
                if (lineLayer) lineLayer.setVisible(e.target.checked);
            });

            document.getElementById('toggleBoundary')?.addEventListener('change', (e) => {
                if (boundaryLayer) boundaryLayer.setVisible(e.target.checked);
            });

            const droneToggle = document.getElementById('toggleDrone');
            if (droneToggle && imageLayer) {
                droneToggle.addEventListener('change', (e) => imageLayer.setVisible(e.target.checked));
            }
        }

        function addLegend(hasDroneImage) {
            const legend = document.createElement('div');
            legend.className = 'map-legend';
            legend.id = 'mapLegend';
            legend.innerHTML = `
                <h5><i class="fas fa-info-circle"></i> Legend</h5>
                <div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div>
                <div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div>
                <div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div>
                ${hasDroneImage ? '<div class="legend-item"><div class="legend-color drone"></div><span>Drone Imagery</span></div>' : ''}
            `;
            document.body.appendChild(legend);
        }

        function addZoomControls() {
            const controls = document.createElement('div');
            controls.className = 'zoom-controls';
            controls.innerHTML = `
                <button class="zoom-btn" id="zoomInBtn" type="button"><i class="fas fa-plus"></i></button>
                <button class="zoom-btn" id="zoomOutBtn" type="button"><i class="fas fa-minus"></i></button>
            `;
            document.body.appendChild(controls);

            document.getElementById('zoomInBtn').addEventListener('click', () => {
                const view = map.getView();
                view.setZoom(view.getZoom() + 1);
            });

            document.getElementById('zoomOutBtn').addEventListener('click', () => {
                const view = map.getView();
                view.setZoom(view.getZoom() - 1);
            });
        }

        function addMobileControls() {
            const menuBtn = document.getElementById('mobileMenuBtn');
            const legendBtn = document.getElementById('mobileLegendBtn');
            const layerSwitcher = document.getElementById('layerSwitcher');
            const mapLegend = document.getElementById('mapLegend');

            if (menuBtn && layerSwitcher) {
                menuBtn.addEventListener('click', () => {
                    layerSwitcher.classList.toggle('open');
                    if (mapLegend) mapLegend.classList.remove('open');
                });
            }

            if (legendBtn && mapLegend) {
                legendBtn.addEventListener('click', () => {
                    mapLegend.classList.toggle('open');
                    if (layerSwitcher) layerSwitcher.classList.remove('open');
                });
            }
        }

        function polygonStyleFunction(feature) {
            const gisid = feature.get('gisid');
            const sqfeet = feature.get('sqfeet');
            const geometry = feature.getGeometry();
            let center;

            try {
                center = geometry.getInteriorPoint();
            } catch (e) {
                const extent = geometry.getExtent();
                center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
            }

            return [
                new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#ff4444',
                        width: 2
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 68, 68, 0.15)'
                    })
                }),
                new ol.style.Style({
                    geometry: center,
                    text: new ol.style.Text({
                        text: `${gisid}\n${sqfeet} sqft`,
                        font: 'bold 10px Arial, sans-serif',
                        fill: new ol.style.Fill({
                            color: '#ffffff'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#000000',
                            width: 2
                        }),
                        textAlign: 'center',
                        backgroundFill: new ol.style.Fill({
                            color: 'rgba(0, 0, 0, 0.7)'
                        }),
                        backgroundStroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 1
                        }),
                        padding: [4, 8, 4, 8]
                    })
                })
            ];
        }

        function refreshLayers() {
            if (polygonLayer) map.removeLayer(polygonLayer);
            if (lineLayer) map.removeLayer(lineLayer);

            const polygonSource = new ol.source.Vector();

            polygons.forEach(poly => {
                try {
                    let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                    if (coords && coords.length) {
                        polygonSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            sqfeet: poly.sqfeet
                        }));
                    }
                } catch (e) {
                    console.log('Error parsing polygon:', e);
                }
            });

            polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: polygonStyleFunction,
                visible: true
            });

            const lineSource = new ol.source.Vector();

            lines.forEach(line => {
                try {
                    let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line.coordinates;
                    if (coords && coords.length) {
                        if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];

                        lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: line.gisid
                        }));
                    }
                } catch (e) {
                    console.log('Error parsing line:', e);
                }
            });

            lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#ffc107',
                        width: 3
                    })
                }),
                visible: true
            });

            map.addLayer(polygonLayer);
            map.addLayer(lineLayer);

            map.on('click', (evt) => {
                const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                if (feature && feature.get('gisid')) {
                    showPopup(feature.get('gisid'), evt.coordinate);
                }
            });

            map.on('pointermove', (evt) => {
                const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                map.getTargetElement().style.cursor = feature && feature.get('gisid') ? 'pointer' : '';
            });
        }

        window.addEventListener('resize', () => {
            setTimeout(() => {
                map?.updateSize();

                if (popupElement && popupElement.classList.contains('show')) {
                    applyPopupMode(lastPopupCoordinate);
                }
            }, 100);
        });

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
@endpush
