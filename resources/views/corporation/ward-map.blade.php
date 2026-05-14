@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Mobile Buttons -->
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i></button>
        <button class="mobile-legend-btn" id="mobileLegendBtn"><i class="fas fa-info-circle"></i></button>
        <button class="mobile-search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
        <button class="mobile-filter-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i></button>
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
            height: 100%;
            overflow: auto;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100vh;
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* Mobile Buttons */
        .mobile-menu-btn,
        .mobile-legend-btn,
        .mobile-search-btn,
        .mobile-filter-btn {
            position: fixed;
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
            font-size: 20px;
        }

        .mobile-menu-btn {
            bottom: 20px;
            right: 20px;
        }

        .mobile-legend-btn {
            bottom: 20px;
            right: 80px;
            background: rgba(255, 193, 7, 0.9);
        }

        .mobile-search-btn {
            bottom: 20px;
            right: 140px;
            background: rgba(23, 162, 184, 0.9);
        }

        .mobile-filter-btn {
            bottom: 20px;
            right: 200px;
            background: rgba(40, 167, 69, 0.9);
        }

        @media (max-width: 768px) {

            .mobile-menu-btn,
            .mobile-legend-btn,
            .mobile-search-btn,
            .mobile-filter-btn {
                display: flex;
            }
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            min-width: 160px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: 80px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                min-width: 200px;
                z-index: 1003;
            }

            .layer-switcher.open {
                transform: translateX(0);
            }
        }

        .layer-switcher h5 {
            margin-bottom: 10px;
            font-size: 14px;
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
            margin: 8px 0;
            font-size: 12px;
            cursor: pointer;
        }

        .group-title {
            font-weight: 600;
            color: #ffc107;
            font-size: 11px;
            margin-bottom: 5px;
        }

        /* Legend */
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            padding: 12px;
            min-width: 140px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            pointer-events: none;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: 140px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                pointer-events: auto;
                z-index: 1003;
            }

            .map-legend.open {
                transform: translateX(0);
            }
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

        /* Search Panel */
        .search-panel {
            position: absolute;
            top: 0px;
            left: 230px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 12px;
            padding: 15px;
            min-width: 300px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .search-panel {
                position: fixed;
                bottom: 200px;
                left: 20px;
                right: 20px;
                top: auto;
                transform: translateY(120%);
                min-width: auto;
                z-index: 1003;
            }

            .search-panel.open {
                transform: translateY(0);
            }
        }

        .search-panel h5 {
            margin-bottom: 10px;
            color: #ffc107;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
        }

        .search-box button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background: #ff4444;
            color: white;
            cursor: pointer;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 10px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
        }

        .search-result-item:hover {
            background: rgba(255, 68, 68, 0.3);
        }

        .result-gisid {
            font-weight: bold;
            color: #ffc107;
            font-size: 12px;
        }

        .result-owner {
            font-size: 11px;
            color: #ddd;
            margin-top: 4px;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #888;
        }

        /* Filter Panel */
        .filter-panel {
            position: absolute;
            top: 100px;
            right: 200px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 12px;
            padding: 15px;
            min-width: 250px;
            backdrop-filter: blur(10px);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .filter-panel {
                position: fixed;
                bottom: 200px;
                right: 20px;
                left: auto;
                top: auto;
                transform: translateX(120%);
                min-width: 220px;
                z-index: 1003;
            }

            .filter-panel.open {
                transform: translateX(0);
            }
        }

        .filter-panel h5 {
            margin-bottom: 10px;
            color: #ffc107;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 5px;
        }

        .filter-group {
            margin-bottom: 12px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            color: #ffc107;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ff4444;
            background: rgba(0, 0, 0, 0.5);
            color: white;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .filter-actions button {
            flex: 1;
            padding: 8px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .apply-btn {
            background: #28a745;
            color: white;
        }

        .reset-btn {
            background: #dc3545;
            color: white;
        }

        .filter-count {
            margin-top: 10px;
            font-size: 11px;
            color: #ffc107;
            text-align: center;
        }

        /* Zoom Controls */
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 12px;
            overflow: hidden;
            z-index: 1000;
        }

        .zoom-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .zoom-btn:active {
            background: #1679AB;
        }

        /* Loading */
        .map-loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 2000;
        }

        /* Popup */
        .ol-popup {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(135deg, #0f0f1a, #1a1a2e);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 0;
            width: 100% !important;
            max-height: 75vh !important;
            z-index: 9999 !important;
            overflow-y: auto;
            animation: slideUp 0.3s ease-out !important;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @media (min-width: 769px) {
            .ol-popup {
                position: absolute !important;
                bottom: auto !important;
                width: auto !important;
                min-width: 380px !important;
                max-width: 450px !important;
                border-radius: 16px !important;
                animation: none !important;
            }

            .ol-popup:after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                border-width: 10px 10px 0;
                border-style: solid;
                border-color: #1a1a2e transparent transparent;
            }
        }

        .popup-header {
            background: linear-gradient(135deg, #1a1a2e, #0f0f1a);
            padding: 16px 18px;
            border-bottom: 2px solid #ff4444;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
        }

        .popup-header h4 {
            margin: 0;
            font-size: 16px;
            color: #ff4444;
        }

        .popup-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
        }

        .popup-tabs {
            display: flex;
            background: #141424;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .popup-tab {
            flex: 1;
            background: none;
            border: none;
            color: #aaa;
            padding: 12px;
            cursor: pointer;
            font-weight: 600;
        }

        .popup-tab.active {
            color: #ff4444;
            border-bottom: 2px solid #ff4444;
        }

        .popup-tab-content {
            display: none;
            padding: 16px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .popup-tab-content.active {
            display: block;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                width: 100% !important;
                margin-bottom: 5px;
            }
        }

        .detail-label {
            font-weight: 600;
            color: #ffc107;
            width: 110px;
            font-size: 11px;
        }

        .detail-value {
            color: #eee;
            flex: 1;
            font-size: 12px;
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

        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 3px solid #ffc107;
            cursor: pointer;
        }

        .assessment-header {
            background: rgba(255, 193, 7, 0.15);
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
        }

        .assessment-number {
            font-weight: 700;
            font-size: 12px;
            color: #ffc107;
        }

        .assessment-body {
            padding: 12px;
        }

        .assessment-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 11px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .assessment-row {
                flex-direction: column;
            }

            .assessment-label {
                width: 100% !important;
                margin-bottom: 4px;
            }
        }

        .assessment-label {
            width: 80px;
            color: #aaa;
        }

        .assessment-value {
            color: #fff;
            flex: 1;
        }

        .shop-item {
            background: rgba(255, 68, 68, 0.1);
            border-radius: 10px;
            padding: 10px;
            margin-top: 8px;
        }

        .shop-name {
            font-weight: 700;
            color: #ff4444;
            font-size: 12px;
            margin-bottom: 6px;
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
            margin: 10px;
            padding: 15px;
            background: #1a1a2e;
            border-radius: 12px;
            border-left: 3px solid #ff4444;
        }

        .close-form-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            float: right;
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
            let wardData = {
                ward_no: @json($ward->ward_no ?? ''),
                drone_image: @json($ward->drone_image ?? null),
                extent_left: @json($ward->extent_left ?? null),
                extent_bottom: @json($ward->extent_bottom ?? null),
                extent_right: @json($ward->extent_right ?? null),
                extent_top: @json($ward->extent_top ?? null),
                boundary: @json($ward->boundary ?? null)
            };

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== SEARCH & FILTER VARIABLES ====================
            let allBuildings = [];

            // ==================== BUILD SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = [];
                $.each(polygonDatas, function(i, building) {
                    let buildingInfo = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        building_type: building.building_type,
                        road_name: building.road_name,
                        zone: building.zone,
                        number_floor: building.number_floor,
                        assessments: []
                    };
                    if (building.pointdata) {
                        $.each(building.pointdata, function(j, assessment) {
                            buildingInfo.assessments.push({
                                assessment_no: assessment.assessment,
                                owner_name: assessment.owner_name || assessment
                                    .present_owner_name,
                                phone: assessment.phone_number
                            });
                        });
                    }
                    allBuildings.push(buildingInfo);
                });
            }

            // ==================== SEARCH FUNCTION ====================
            function searchBuildings(searchText) {
                if (!searchText || searchText.trim() === '') {
                    $('#searchResults').html(
                        '<div class="no-results"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }
                let term = searchText.toLowerCase().trim();
                let results = [];
                $.each(allBuildings, function(i, building) {
                    let matchFound = false;
                    let matchType = '';
                    let matchValue = '';
                    if (building.gisid && building.gisid.toLowerCase().includes(term)) {
                        matchFound = true;
                        matchType = 'GIS ID';
                        matchValue = building.gisid;
                    } else if (building.building_usage && building.building_usage.toLowerCase().includes(
                            term)) {
                        matchFound = true;
                        matchType = 'Building Usage';
                        matchValue = building.building_usage;
                    } else if (building.road_name && building.road_name.toLowerCase().includes(term)) {
                        matchFound = true;
                        matchType = 'Road Name';
                        matchValue = building.road_name;
                    } else {
                        $.each(building.assessments, function(j, a) {
                            if (a.assessment_no && a.assessment_no.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Assessment No';
                                matchValue = a.assessment_no;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Owner Name';
                                matchValue = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                matchFound = true;
                                matchType = 'Phone';
                                matchValue = a.phone;
                                return false;
                            }
                        });
                    }
                    if (matchFound) {
                        results.push({
                            gisid: building.gisid,
                            matchType: matchType,
                            matchValue: matchValue,
                            building: building
                        });
                    }
                });
                displaySearchResults(results);
            }

            // ==================== DISPLAY SEARCH RESULTS ====================
            function displaySearchResults(results) {
                let $results = $('#searchResults');
                $results.empty();
                if (results.length === 0) {
                    $results.html(
                        '<div class="no-results"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }
                $.each(results, function(i, result) {
                    let html = `
                        <div class="search-result-item" data-gisid="${result.gisid}">
                            <div class="result-gisid"><i class="fas fa-building"></i> ${result.gisid}</div>
                            <div class="result-owner"><i class="fas fa-tag"></i> Match: ${result.matchType} - ${result.matchValue}</div>
                            <div class="result-owner"><i class="fas fa-location-dot"></i> ${result.building.road_name || 'No road'} | ${result.building.zone || 'No zone'}</div>
                        </div>
                    `;
                    $results.append(html);
                });
                $('.search-result-item').off('click').on('click', function() {
                    let gisid = $(this).data('gisid');
                    zoomToBuilding(gisid);
                    $('#searchPanel').removeClass('open');
                });
            }

            // ==================== ZOOM TO BUILDING ====================
            function zoomToBuilding(gisid) {
                let source = polygonLayer.getSource();
                let features = source.getFeatures();
                let targetFeature = null;
                $.each(features, function(i, feature) {
                    if (feature.get('gisid') == gisid) {
                        targetFeature = feature;
                        return false;
                    }
                });
                if (targetFeature) {
                    let extent = targetFeature.getGeometry().getExtent();
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                    let center = ol.extent.getCenter(extent);
                    showPopup(gisid, center);
                }
            }

            // ==================== APPLY FILTERS ====================
            function applyFilters() {
                let filterType = $('#filterType').val();
                let filterMinFloors = $('#filterMinFloors').val();
                let filterMaxFloors = $('#filterMaxFloors').val();
                let source = polygonLayer.getSource();
                let features = source.getFeatures();
                let visibleCount = 0;
                $.each(features, function(i, feature) {
                    let gisid = feature.get('gisid');
                    let buildingData = null;
                    $.each(polygonDatas, function(j, b) {
                        if (b.gisid == gisid) buildingData = b;
                    });
                    let show = true;
                    if (filterType === 'completed' && buildingData) {
                        let hasQC = false;
                        if (buildingData.pointdata) {
                            $.each(buildingData.pointdata, function(k, a) {
                                if (a.qcsqfeet || a.qcusage) {
                                    hasQC = true;
                                    return false;
                                }
                            });
                        }
                        if (!hasQC) show = false;
                    } else if (filterType === 'pending' && buildingData) {
                        let hasQC = false;
                        if (buildingData.pointdata) {
                            $.each(buildingData.pointdata, function(k, a) {
                                if (a.qcsqfeet || a.qcusage) {
                                    hasQC = true;
                                    return false;
                                }
                            });
                        }
                        if (hasQC) show = false;
                    }
                    if (show && buildingData && (filterMinFloors || filterMaxFloors)) {
                        let floors = parseInt(buildingData.number_floor) || 0;
                        if (filterMinFloors && floors < parseInt(filterMinFloors)) show = false;
                        if (filterMaxFloors && floors > parseInt(filterMaxFloors)) show = false;
                    }
                    feature.set('visible', show);
                    if (show) visibleCount++;
                });
                polygonLayer.setStyle(function(feature) {
                    if (feature.get('visible') === false) return null;
                    return polygonStyleFunction(feature);
                });
                $('#filterCount').text(`Showing ${visibleCount} of ${features.length} buildings`);
                $('#filterPanel').removeClass('open');
            }

            // ==================== RESET FILTERS ====================
            function resetFilters() {
                $('#filterType').val('all');
                $('#filterMinFloors').val('');
                $('#filterMaxFloors').val('');
                let source = polygonLayer.getSource();
                let features = source.getFeatures();
                $.each(features, function(i, feature) {
                    feature.set('visible', true);
                });
                polygonLayer.setStyle(polygonStyleFunction);
                $('#filterCount').text(`Showing ${features.length} of ${features.length} buildings`);
                $('#filterPanel').removeClass('open');
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', {
                    class: 'ol-popup',
                    style: 'display:none'
                })[0];
                $('body').append(popupElement);
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
            window.closePopup = function() {
                $('.ol-popup').hide();
            };
            window.switchTab = function(tabId) {
                $('.popup-tab-content').removeClass('active');
                $('.popup-tab').removeClass('active');
                $('#tab-' + tabId).addClass('active');
                $('.popup-tab[data-tab="' + tabId + '"]').addClass('active');
                currentActiveTab = tabId;
            };

            function showPopup(gisid, coordinate) {
                let polyData = null;
                $.each(polygonDatas, function(i, p) {
                    if (p.gisid == gisid) polyData = p;
                });
                if (!polyData) return;
                let assessments = polyData.pointdata || [];
                let allShops = [];
                $.each(assessments, function(i, a) {
                    if (a.shops && a.shops.length) {
                        $.each(a.shops, function(j, s) {
                            allShops.push({
                                ...s,
                                assessmentNumber: a.assessment || 'Bill ' + (i + 1)
                            });
                        });
                    }
                });

                let buildingHtml = `<div class="building-details-content">
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-fingerprint"></i> GIS ID:</div><div class="detail-value"><strong>${polyData.gisid || 'N/A'}</strong></div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-building"></i> Building Usage:</div><div class="detail-value">${polyData.building_usage || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-home"></i> Building Type:</div><div class="detail-value">${polyData.building_type || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-layer-group"></i> Floors:</div><div class="detail-value">${polyData.number_floor || '0'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-receipt"></i> Total Bills:</div><div class="detail-value">${polyData.number_bill || '0'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-store"></i> Total Shops:</div><div class="detail-value">${polyData.total_shops || '0'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-road"></i> Road Name:</div><div class="detail-value">${polyData.road_name || 'N/A'}</div></div>
                    <div class="detail-row"><div class="detail-label"><i class="fas fa-map-pin"></i> Zone:</div><div class="detail-value">${polyData.zone || 'N/A'}</div></div>
                </div>`;

                let assessmentsHtml = '';
                if (assessments.length === 0) {
                    assessmentsHtml =
                        '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments found</p></div>';
                } else {
                    $.each(assessments, function(i, a) {
                        let hasQC = a.qcsqfeet || a.qcusage;
                        assessmentsHtml += `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${a.assessment || 'Assessment ' + (i+1)}</span>
                                <span class="badge ${hasQC ? 'badge-success' : 'badge-warning'}">${hasQC ? 'QC Done' : 'QC Pending'}</span>
                            </div>
                            <div class="assessment-body">
                                <div class="assessment-row"><div class="assessment-label">Owner:</div><div class="assessment-value"><strong>${a.owner_name || a.present_owner_name || 'N/A'}</strong></div></div>
                                <div class="assessment-row"><div class="assessment-label">Phone:</div><div class="assessment-value">${a.phone_number || 'N/A'}</div></div>
                                <div class="assessment-row"><div class="assessment-label">Floor:</div><div class="assessment-value">${a.floor || 'N/A'}</div></div>
                                <div class="assessment-row"><div class="assessment-label">Usage:</div><div class="assessment-value">${a.bill_usage || 'N/A'}</div></div>
                                <div class="assessment-row"><div class="assessment-label">Shops:</div><div class="assessment-value">${(a.shops || []).length}</div></div>
                            </div>
                        </div>`;
                    });
                }

                let shopsHtml = '';
                if (allShops.length === 0) {
                    shopsHtml = '<div class="empty-state"><i class="fas fa-store"></i><p>No shops found</p></div>';
                } else {
                    $.each(allShops, function(i, s) {
                        shopsHtml += `<div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name || 'Shop ' + (i+1)}</div>
                            <div class="assessment-row"><div class="assessment-label">Category:</div><div class="assessment-value">${s.shop_category || 'N/A'}</div></div>
                            <div class="assessment-row"><div class="assessment-label">Owner:</div><div class="assessment-value">${s.shop_owner_name || 'N/A'}</div></div>
                            <div class="assessment-row"><div class="assessment-label">Mobile:</div><div class="assessment-value">${s.shop_mobile || 'N/A'}</div></div>
                        </div>`;
                    });
                }

                let html =
                    `<div class="popup-header">
                    <h4><i class="fas fa-building"></i> Building Details</h4>
                    <button class="popup-close" onclick="closePopup()">&times;</button>
                </div>
                <div class="popup-tabs">
                    <button class="popup-tab ${currentActiveTab === 'building' ? 'active' : ''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                    <button class="popup-tab ${currentActiveTab === 'assessments' ? 'active' : ''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button>
                    <button class="popup-tab ${currentActiveTab === 'shops' ? 'active' : ''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${allShops.length})</button>
                </div>
                <div id="tab-building" class="popup-tab-content ${currentActiveTab === 'building' ? 'active' : ''}">${buildingHtml}</div>
                <div id="tab-assessments" class="popup-tab-content ${currentActiveTab === 'assessments' ? 'active' : ''}"><div style="padding:12px">${assessmentsHtml}</div></div>
                <div id="tab-shops" class="popup-tab-content ${currentActiveTab === 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>`;

                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coordinate);

                $('.assessment-card').off('click').on('click', function() {
                    let assessmentId = $(this).data('id');
                    let assessmentNumber = $(this).data('assessment');
                    $('.assessment-form-container').remove();
                    $(this).after(`<div class="assessment-form-container">
                        <button class="close-form-btn">&times;</button>
                        <h4 style="color:#ffc107; margin-bottom:15px;">QC Form - ${assessmentNumber}</h4>
                        <form class="qc-form">
                            <input type="hidden" name="assessment_id" value="${assessmentId}">
                            <div style="margin-bottom:12px;"><label style="color:#ffc107">QC Square Feet:</label>
                            <input type="number" name="qc_sqfeet" style="width:100%; padding:8px; border-radius:5px; border:1px solid #ff4444; background:#0f0f1a; color:white;"></div>
                            <div style="margin-bottom:12px;"><label style="color:#ffc107">QC Usage:</label>
                            <select name="qc_usage" style="width:100%; padding:8px; border-radius:5px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                <option value="">Select</option><option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option><option value="Industrial">Industrial</option>
                            </select></div>
                            <div style="margin-bottom:12px;"><label style="color:#ffc107">Tax Amount (₹):</label>
                            <input type="number" name="tax_amount" style="width:100%; padding:8px; border-radius:5px; border:1px solid #ff4444; background:#0f0f1a; color:white;"></div>
                            <div style="display:flex; gap:10px;"><button type="submit" style="flex:1; background:#28a745; color:white; border:none; padding:10px; border-radius:5px;">Save</button>
                            <button type="button" class="cancel-form-btn" style="flex:1; background:#dc3545; color:white; border:none; padding:10px; border-radius:5px;">Cancel</button></div>
                        </form>
                    </div>`);
                    $('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        let isComplete = $(this).find('input[name="qc_sqfeet"]').val() && $(this)
                            .find('select[name="qc_usage"]').val() && $(this).find(
                                'input[name="tax_amount"]').val();
                        if (isComplete) {
                            $(this).closest('.assessment-card').find('.badge').removeClass(
                                'badge-warning').addClass('badge-success').html(
                                '<i class="fas fa-check-circle"></i> QC Complete');
                        } else {
                            $(this).closest('.assessment-card').find('.badge').removeClass(
                                'badge-success').addClass('badge-warning').html(
                                '<i class="fas fa-clock"></i> QC Pending');
                        }
                        alert('QC Saved! Status: ' + (isComplete ? 'QC Complete' : 'QC Pending'));
                        $('.assessment-form-container').remove();
                    });
                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== MAP FUNCTIONS ====================
            function showLoading(show) {
                if (show) {
                    if ($('#mapLoading').length === 0) $('body').append(
                        '<div id="mapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i> Loading map...</div>'
                        );
                    $('#mapLoading').show();
                } else {
                    $('#mapLoading').hide();
                }
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

                let droneImage = wardData.drone_image,
                    hasDroneImage = false;
                if (droneImage && wardData.extent_left) {
                    try {
                        imageLayer = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: "{{ asset('') }}" + droneImage.replace(/^\/+/, ''),
                                imageExtent: [parseFloat(wardData.extent_left), parseFloat(wardData
                                        .extent_bottom), parseFloat(wardData.extent_right),
                                    parseFloat(wardData.extent_top)
                                ],
                                projection: 'EPSG:3857'
                            }),
                            opacity: 1.0,
                            visible: true
                        });
                        hasDroneImage = true;
                    } catch (e) {
                        console.error('Drone error:', e);
                    }
                }

                let boundary = wardData.boundary,
                    boundaryExtent = null;
                if (boundary && boundary.length && boundary[0].length) {
                    try {
                        let boundaryCoords = boundary[0].map(c => ol.proj.fromLonLat(c));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([boundaryCoords])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: '#ff0000',
                                    width: 3,
                                    lineDash: [10, 5]
                                }),
                                fill: new ol.style.Fill({
                                    color: 'rgba(255,0,0,0.05)'
                                })
                            }),
                            visible: true
                        });
                        let lons = boundary[0].map(p => p[0]),
                            lats = boundary[0].map(p => p[1]);
                        boundaryExtent = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...
                            lons), Math.max(...lats)
                        ]);
                    } catch (e) {
                        console.error('Boundary error:', e);
                    }
                }

                let center = ol.proj.fromLonLat([80.2707, 13.0827]),
                    zoom = 24;
                if (boundary && boundary[0] && boundary[0].length) {
                    try {
                        let lons = boundary[0].map(p => p[0]),
                            lats = boundary[0].map(p => p[1]);
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...
                            lats) + Math.max(...lats)) / 2]);
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
                const popup = createPopup();
                map.addOverlay(popup);
                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);
                setTimeout(() => {
                    if (boundaryExtent) map.getView().fit(boundaryExtent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }, 500);

                addLayerSwitcher(hasDroneImage);
                addLegend(hasDroneImage);
                addSearchPanel();
                addFilterPanel();
                addZoomControls();
                addMobileControls();
                refreshLayers();
                buildSearchIndex();
            }

            function addLayerSwitcher(hasDroneImage) {
                $('body').append(`<div class="layer-switcher" id="layerSwitcher">
                    <h5><i class="fas fa-layer-group"></i> Layers</h5>
                    <div class="layer-group"><div class="group-title">Base Maps</div>
                        <label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label>
                        <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label>
                    </div>
                    <div class="layer-group"><div class="group-title">Overlays</div>
                        <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
                        <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
                        <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>
                        ${hasDroneImage ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}
                    </div>
                </div>`);
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
                if (hasDroneImage) $('#toggleDrone').on('change', function() {
                    if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                });
            }

            function addLegend(hasDroneImage) {
                $('body').append(`<div class="map-legend" id="mapLegend">
                    <h5><i class="fas fa-info-circle"></i> Legend</h5>
                    <div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div>
                    <div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div>
                    <div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div>
                    ${hasDroneImage ? '<div class="legend-item"><div class="legend-color drone"></div><span>Drone Imagery</span></div>' : ''}
                </div>`);
            }

            function addSearchPanel() {
                $('body').append(`<div class="search-panel" id="searchPanel">
                    <h5><i class="fas fa-search"></i> Search Building</h5>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by GIS ID, Owner, Assessment No...">
                        <button id="searchBtn"><i class="fas fa-search"></i> Go</button>
                    </div>
                    <div id="searchResults" class="search-results"></div>
                </div>`);
                $('#searchBtn').on('click', function() {
                    searchBuildings($('#searchInput').val());
                });
                $('#searchInput').on('keypress', function(e) {
                    if (e.which === 13) searchBuildings($(this).val());
                });
            }

            function addFilterPanel() {
                $('body').append(`<div class="filter-panel" id="filterPanel">
                    <h5><i class="fas fa-filter"></i> Filter Buildings</h5>
                    <div class="filter-group"><label>QC Status</label>
                        <select id="filterType"><option value="all">All Buildings</option><option value="completed">QC Complete</option><option value="pending">QC Pending</option></select>
                    </div>
                    <div class="filter-group"><label>Floors (Min)</label><input type="number" id="filterMinFloors" placeholder="Min floors"></div>
                    <div class="filter-group"><label>Floors (Max)</label><input type="number" id="filterMaxFloors" placeholder="Max floors"></div>
                    <div class="filter-actions"><button class="apply-btn" id="applyFilterBtn">Apply Filter</button><button class="reset-btn" id="resetFilterBtn">Reset</button></div>
                    <div class="filter-count" id="filterCount"></div>
                </div>`);
                $('#applyFilterBtn').on('click', applyFilters);
                $('#resetFilterBtn').on('click', resetFilters);
            }

            function addZoomControls() {
                $('body').append(
                    `<div class="zoom-controls"><button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button><button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button></div>`
                    );
                $('#zoomInBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').on('click', () => map.getView().setZoom(map.getView().getZoom() - 1));
            }

            function addMobileControls() {
                $('#mobileMenuBtn').on('click', function() {
                    $('#layerSwitcher').toggleClass('open');
                    $('#mapLegend').removeClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#filterPanel').removeClass('open');
                });
                $('#mobileLegendBtn').on('click', function() {
                    $('#mapLegend').toggleClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#filterPanel').removeClass('open');
                });
                $('#mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#searchPanel').toggleClass('open');
                    $('#filterPanel').removeClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#mapLegend').removeClass('open');
                });
                $('#mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#filterPanel').toggleClass('open');
                    $('#searchPanel').removeClass('open');
                    $('#layerSwitcher').removeClass('open');
                    $('#mapLegend').removeClass('open');
                });
                $(document).on('click', function(e) {
                    if ($(window).width() <= 2068) {
                        if (!$('#layerSwitcher').is(e.target) && !$('#layerSwitcher').has(e.target)
                            .length && !$('#mobileMenuBtn').is(e.target)) $('#layerSwitcher').removeClass(
                            'open');
                        if (!$('#mapLegend').is(e.target) && !$('#mapLegend').has(e.target).length && !$(
                                '#mobileLegendBtn').is(e.target)) $('#mapLegend').removeClass('open');
                        if (!$('#searchPanel').is(e.target) && !$('#searchPanel').has(e.target).length && !
                            $('#mobileSearchBtn').is(e.target)) $('#searchPanel').removeClass('open');
                        if (!$('#filterPanel').is(e.target) && !$('#filterPanel').has(e.target).length && !
                            $('#mobileFilterBtn').is(e.target)) $('#filterPanel').removeClass('open');
                    }
                });
            }

            function polygonStyleFunction(feature) {
                let gisid = feature.get('gisid'),
                    sqfeet = feature.get('sqfeet'),
                    geom = feature.getGeometry();
                let center;
                try {
                    center = geom.getInteriorPoint();
                    if (!center) {
                        let ex = geom.getExtent();
                        center = new ol.geom.Point([(ex[0] + ex[2]) / 2, (ex[1] + ex[3]) / 2]);
                    }
                } catch (e) {
                    let ex = geom.getExtent();
                    center = new ol.geom.Point([(ex[0] + ex[2]) / 2, (ex[1] + ex[3]) / 2]);
                }
                return [new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,68,68,0.15)'
                        })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}\n${sqfeet} sqft`,
                            font: 'bold 10px Arial',
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#000',
                                width: 2
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: 'rgba(0,0,0,0.7)'
                            }),
                            padding: [4, 8, 4, 8]
                        })
                    })
                ];
            }

            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);
                let polygonSource = new ol.source.Vector();
                $.each(polygons, function(i, poly) {
                    try {
                        let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) :
                            poly.coordinates;
                        if (coords && coords.length) polygonSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            sqfeet: poly.sqfeet
                        }));
                    } catch (e) {
                        console.log('Polygon error:', e);
                    }
                });
                polygonLayer = new ol.layer.Vector({
                    source: polygonSource,
                    style: polygonStyleFunction,
                    visible: true
                });
                let lineSource = new ol.source.Vector();
                $.each(lines, function(i, line) {
                    try {
                        let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) :
                            line.coordinates;
                        if (coords && coords.length) {
                            if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
                            lineSource.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(coords),
                                gisid: line.gisid
                            }));
                        }
                    } catch (e) {
                        console.log('Line error:', e);
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
                    let f = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    if (f && f.get('gisid')) showPopup(f.get('gisid'), evt.coordinate);
                    else if (popupElement) $(popupElement).hide();
                });
                map.on('pointermove', (evt) => {
                    let f = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    $('#map').css('cursor', f && f.get('gisid') ? 'pointer' : '');
                });
                showLoading(false);
            }

            initMap();
            $(window).on('resize', () => setTimeout(() => map?.updateSize(), 100));
        });
    </script>
@endpush
