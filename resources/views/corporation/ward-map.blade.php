{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $ward->ward_no . ' Map View - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <style>
        #map {
            width: 100%;
            height: calc(100vh - 80px);
            border-radius: 10px;
            border: 2px solid #ddd;
        }

        /* Search Container */
        .search-container {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 15px;
            width: 350px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .search-container h4 {
            margin: 0 0 12px 0;
            font-size: 16px;
            font-weight: 600;
            color: #102C57;
            border-bottom: 2px solid #1679AB;
            padding-bottom: 8px;
        }

        .search-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .search-tab {
            padding: 8px 15px;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
            border-radius: 8px 8px 0 0;
        }

        .search-tab.active {
            color: #1679AB;
            border-bottom: 2px solid #1679AB;
        }

        .search-tab:hover {
            background: #f5f5f5;
        }

        .search-panel {
            display: none;
        }

        .search-panel.active {
            display: block;
        }

        .search-box {
            display: flex;
            gap: 8px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #1679AB;
            box-shadow: 0 0 0 2px rgba(22, 121, 171, 0.1);
        }

        .search-box button {
            background: #1679AB;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-box button:hover {
            background: #102C57;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            display: none;
        }

        .search-result-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.2s;
        }

        .search-result-item:hover {
            background: #f0f7ff;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .result-gisid {
            font-weight: bold;
            color: #1679AB;
        }

        .result-type {
            font-size: 12px;
            color: #666;
            margin-left: 8px;
        }

        .result-detail {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }

        /* Feature Info Panel */
        .feature-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 15px;
            z-index: 1000;
            max-width: 320px;
            min-width: 260px;
            display: none;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .feature-info h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #102C57;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .feature-info .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #999;
        }

        .feature-info .close-btn:hover {
            color: #333;
        }

        .info-row {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            width: 100px;
            display: inline-block;
        }

        .info-value {
            color: #333;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-completed {
            background: #28a745;
            color: white;
        }

        .badge-pending {
            background: #ffc107;
            color: #333;
        }

        .badge-missing {
            background: #dc3545;
            color: white;
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            padding: 20px 30px;
            border-radius: 12px;
            z-index: 2000;
            display: none;
            color: white;
            text-align: center;
        }

        /* Layer Switcher */
        .layer-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 12px;
            z-index: 1000;
            width: 180px;
            backdrop-filter: blur(10px);
        }

        .layer-switcher h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #102C57;
        }

        .layer-option {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .layer-option input {
            margin-right: 8px;
            cursor: pointer;
        }

        .layer-option label {
            cursor: pointer;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Zoom Controls */
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .zoom-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s;
        }

        .zoom-btn:hover {
            background: #f0f0f0;
        }

        .zoom-btn:first-child {
            border-bottom: 1px solid #eee;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .search-container {
                top: 10px;
                left: 10px;
                right: 10px;
                width: auto;
                max-width: none;
            }

            .layer-switcher {
                top: auto;
                bottom: 80px;
                right: 10px;
                width: 160px;
            }

            .feature-info {
                bottom: 80px;
                right: 10px;
                left: 10px;
                max-width: none;
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
                flex: 1;
            }

            .mobile-toolbar-btn.active {
                background: #1679AB;
                color: white;
            }

            .mobile-toolbar-btn i {
                font-size: 18px;
                margin-bottom: 4px;
            }

            .desktop-only {
                display: none !important;
            }
        }

        @media (min-width: 769px) {
            .mobile-only {
                display: none !important;
            }
        }

        .highlight-marker {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>

    <!-- Search Container -->
    <div class="search-container">
        <h4><i class="fas fa-search me-2"></i>Search Property</h4>

        <div class="search-tabs">
            <button class="search-tab active" data-tab="gisid">GIS ID</button>
            <button class="search-tab" data-tab="assessment">Assessment No</button>
        </div>

        <!-- GIS ID Search Panel -->
        <div class="search-panel active" id="gisidPanel">
            <div class="search-box">
                <input type="text" id="gisidSearchInput" placeholder="Enter GIS ID..." autocomplete="off">
                <button id="gisidSearchBtn"><i class="fas fa-search"></i></button>
            </div>
            <div class="search-results" id="gisidResults"></div>
        </div>

        <!-- Assessment Search Panel -->
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
        <div class="layer-option">
            <input type="checkbox" id="showPolygons" checked>
            <label><i class="fas fa-draw-polygon"></i> Buildings</label>
        </div>
        <div class="layer-option">
            <input type="checkbox" id="showPoints" checked>
            <label><i class="fas fa-map-marker-alt"></i> Points</label>
        </div>
        <div class="layer-option">
            <input type="checkbox" id="showLines" checked>
            <label><i class="fas fa-road"></i> Roads</label>
        </div>
        <div class="layer-option">
            <input type="checkbox" id="showBoundary" checked>
            <label><i class="fas fa-vector-square"></i> Boundary</label>
        </div>
        <div class="layer-option">
            <input type="checkbox" id="showDroneImage" checked>
            <label><i class="fas fa-drone"></i> Drone Image</label>
        </div>
        <hr class="my-2">
        <div class="layer-option">
            <input type="radio" name="baseLayer" value="osm" checked>
            <label><i class="fas fa-map"></i> Street</label>
        </div>
        <div class="layer-option">
            <input type="radio" name="baseLayer" value="satellite">
            <label><i class="fas fa-satellite"></i> Satellite</label>
        </div>
    </div>

    <!-- Feature Info Panel -->
    <div class="feature-info" id="featureInfo">
        <button class="close-btn" id="closeFeatureInfo">&times;</button>
        <h4><i class="fas fa-info-circle me-2"></i>Property Details</h4>
        <div id="featureDetails"></div>
    </div>

    <!-- Zoom Controls (Desktop) -->
    <div class="zoom-controls desktop-only">
        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
    </div>

    <!-- Mobile Toolbar -->
    <div class="mobile-toolbar mobile-only">
        <button class="mobile-toolbar-btn" id="mobileSearchBtn">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileLayersBtn">
            <i class="fas fa-layer-group"></i>
            <span>Layers</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileZoomInBtn">
            <i class="fas fa-plus"></i>
            <span>Zoom In</span>
        </button>
        <button class="mobile-toolbar-btn" id="mobileZoomOutBtn">
            <i class="fas fa-minus"></i>
            <span>Zoom Out</span>
        </button>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-white mb-2"></div>
        <div>Loading...</div>
    </div>

    <!-- Mobile Layer Switcher -->
    <div class="mobile-layer-switcher mobile-only" id="mobileLayerSwitcher" style="display: none;">
        <div class="mobile-layer-container">
            <h4 class="mb-3">Map Layers</h4>
            <div class="layer-group mb-3">
                <h5>Base Maps</h5>
                <div class="layer-option">
                    <input type="radio" id="mobileOsm" name="mobileBaseLayer" value="osm" checked>
                    <label>Street Map</label>
                </div>
                <div class="layer-option">
                    <input type="radio" id="mobileSatellite" name="mobileBaseLayer" value="satellite">
                    <label>Satellite</label>
                </div>
            </div>
            <div class="layer-group">
                <h5>Overlays</h5>
                <div class="layer-option">
                    <input type="checkbox" id="mobilePolygons" checked>
                    <label>Buildings</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobilePoints" checked>
                    <label>Points</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobileLines" checked>
                    <label>Roads</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobileBoundary" checked>
                    <label>Boundary</label>
                </div>
                <div class="layer-option">
                    <input type="checkbox" id="mobileDrone" checked>
                    <label>Drone Image</label>
                </div>
            </div>
            <button class="btn btn-primary w-100 mt-3" id="closeMobileLayers">Apply</button>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script>
        $(document).ready(function() {
            // Data from server
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let mis = @json($misData ?? []);

            let droneImageURL = "{{ asset($ward->drone_image ?? '') }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            let currentHighlight = null;
            let isMobile = $(window).width() <= 768;

            // Style Functions
            function getPointStyle(feature) {
                const gisid = feature.get("gisid");
                const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                let color = "#1679AB"; // default blue

                if (polygonData) {
                    if (pointCount > 0) {
                        color = (polygonData.number_bill == pointCount) ? "#28a745" : "#dc3545";
                    }
                }

                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 8,
                        fill: new ol.style.Fill({ color: color }),
                        stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
                    }),
                    text: new ol.style.Text({
                        text: gisid ? String(gisid) : "",
                        font: "12px Arial",
                        offsetY: -15,
                        fill: new ol.style.Fill({ color: "#333" }),
                        stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
                    })
                });
            }

            function getPolygonStyle(feature) {
                const gisid = feature.get("gisid");
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                const color = polygonData ? "#dc3545" : "#1679AB";
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({ color: color, width: 3 }),
                    fill: new ol.style.Fill({ color: "rgba(22, 121, 171, 0.1)" })
                });
            }

            function getLineStyle(feature) {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({ color: "#ffc107", width: 3 })
                });
            }

            function getHighlightStyle() {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({ color: "#ff6600", width: 5 }),
                    fill: new ol.style.Fill({ color: "rgba(255, 102, 0, 0.2)" }),
                    image: new ol.style.Circle({
                        radius: 12,
                        fill: new ol.style.Fill({ color: "#ff6600" }),
                        stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
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

            const droneLayer = new ol.layer.Image({
                source: new ol.source.ImageStatic({
                    url: droneImageURL,
                    imageExtent: imageExtent,
                    imageSmoothing: false
                }),
                opacity: 0.85,
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
                } catch(e) { console.error(e); }
            });
            const polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: getPolygonStyle,
                visible: true
            });

            // Line Layer
            const lineSource = new ol.source.Vector();
            lines.forEach(l => {
                try {
                    let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array.isArray(coords[0][0])) {
                        coords = coords[0];
                    }
                    if (coords && coords.length >= 2) {
                        lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: l.gisid,
                            type: "Line"
                        }));
                    }
                } catch(e) { console.error(e); }
            });
            const lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: getLineStyle,
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
                } catch(e) { console.error(e); }
            });
            const pointLayer = new ol.layer.Vector({
                source: pointSource,
                style: getPointStyle,
                visible: true
            });

            // Boundary Layer
            const boundary = ward.boundary && ward.boundary[0] ? ward.boundary[0] : [];
            const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
            const boundaryLayer = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: [new ol.Feature({
                        geometry: new ol.geom.Polygon([transformedBoundary])
                    })]
                }),
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({ color: "#ff0000", width: 3 }),
                    fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.05)" })
                }),
                visible: true
            });

            // Highlight Layer
            const highlightSource = new ol.source.Vector();
            const highlightLayer = new ol.layer.Vector({
                source: highlightSource,
                style: getHighlightStyle
            });

            // Initialize Map
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                }),
                controls: []
            });

            // Zoom Controls
            $('#zoomInBtn, #mobileZoomInBtn').on('click', () => {
                const view = map.getView();
                view.setZoom(view.getZoom() + 1);
            });
            $('#zoomOutBtn, #mobileZoomOutBtn').on('click', () => {
                const view = map.getView();
                view.setZoom(view.getZoom() - 1);
            });

            // Layer Switcher
            $('#showPolygons').on('change', (e) => polygonLayer.setVisible(e.target.checked));
            $('#showPoints').on('change', (e) => pointLayer.setVisible(e.target.checked));
            $('#showLines').on('change', (e) => lineLayer.setVisible(e.target.checked));
            $('#showBoundary').on('change', (e) => boundaryLayer.setVisible(e.target.checked));
            $('#showDroneImage').on('change', (e) => droneLayer.setVisible(e.target.checked));

            $('input[name="baseLayer"]').on('change', function() {
                osmLayer.setVisible($(this).val() === 'osm');
                satelliteLayer.setVisible($(this).val() === 'satellite');
            });

            // Mobile Layer Switcher
            $('#mobilePolygons').on('change', (e) => polygonLayer.setVisible(e.target.checked));
            $('#mobilePoints').on('change', (e) => pointLayer.setVisible(e.target.checked));
            $('#mobileLines').on('change', (e) => lineLayer.setVisible(e.target.checked));
            $('#mobileBoundary').on('change', (e) => boundaryLayer.setVisible(e.target.checked));
            $('#mobileDrone').on('change', (e) => droneLayer.setVisible(e.target.checked));

            $('input[name="mobileBaseLayer"]').on('change', function() {
                osmLayer.setVisible($(this).val() === 'osm');
                satelliteLayer.setVisible($(this).val() === 'satellite');
            });

            // Search Tabs
            $('.search-tab').on('click', function() {
                const tab = $(this).data('tab');
                $('.search-tab').removeClass('active');
                $(this).addClass('active');
                $('.search-panel').removeClass('active');
                $(`#${tab}Panel`).addClass('active');
            });

            // GIS ID Search
            function searchByGISID(gisid) {
                if (!gisid) return;

                $('#loadingSpinner').fadeIn();
                highlightSource.clear();

                let foundFeature = null;
                let foundType = null;

                // Search in polygon layer
                polygonSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        foundFeature = f;
                        foundType = "Building";
                        return true;
                    }
                });

                // Search in point layer
                if (!foundFeature) {
                    pointSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                            foundFeature = f;
                            foundType = "Point";
                            return true;
                        }
                    });
                }

                if (foundFeature) {
                    highlightSource.addFeature(foundFeature.clone());
                    const geometry = foundFeature.getGeometry();

                    if (geometry.getType() === 'Point') {
                        map.getView().animate({
                            center: geometry.getCoordinates(),
                            zoom: 20,
                            duration: 1000
                        });
                    } else {
                        map.getView().fit(geometry.getExtent(), {
                            padding: [50, 50, 50, 50],
                            duration: 1000
                        });
                    }

                    // Show feature info
                    showFeatureInfo(gisid, foundType);
                    $('#gisidResults').hide();
                } else {
                    showFlashMessage(`GIS ID "${gisid}" not found`, "error");
                    $('#gisidResults').html('<div class="search-result-item text-danger">No results found</div>').show();
                    setTimeout(() => $('#gisidResults').fadeOut(), 3000);
                }

                $('#loadingSpinner').fadeOut();
            }

            // Assessment Search
            function searchByAssessment(assessmentNo) {
                if (!assessmentNo) return;

                $('#loadingSpinner').fadeIn();
                highlightSource.clear();

                // Find point data with matching assessment
                const pointData = pointDatas.find(d => d.assessment == assessmentNo);

                if (pointData && pointData.point_gisid) {
                    // Find the feature with matching GIS ID
                    let foundFeature = null;
                    pointSource.forEachFeature(f => {
                        if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid) {
                            foundFeature = f;
                            return true;
                        }
                    });

                    if (foundFeature) {
                        highlightSource.addFeature(foundFeature.clone());
                        const geometry = foundFeature.getGeometry();
                        map.getView().animate({
                            center: geometry.getCoordinates(),
                            zoom: 20,
                            duration: 1000
                        });
                        showFeatureInfo(pointData.point_gisid, "Point", pointData);
                        $('#assessmentResults').hide();
                    } else {
                        showFlashMessage(`Assessment "${assessmentNo}" not found on map`, "error");
                        $('#assessmentResults').html('<div class="search-result-item text-danger">No results found</div>').show();
                        setTimeout(() => $('#assessmentResults').fadeOut(), 3000);
                    }
                } else {
                    showFlashMessage(`Assessment "${assessmentNo}" not found`, "error");
                    $('#assessmentResults').html('<div class="search-result-item text-danger">No results found</div>').show();
                    setTimeout(() => $('#assessmentResults').fadeOut(), 3000);
                }

                $('#loadingSpinner').fadeOut();
            }

            // Show Feature Info
            function showFeatureInfo(gisid, type, pointData = null) {
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                const actualPointData = pointData || pointDatas.find(d => d.point_gisid == gisid);

                let html = `
                    <div class="info-row">
                        <span class="info-label">GIS ID:</span>
                        <span class="info-value"><strong>${gisid}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Type:</span>
                        <span class="info-value">${type}</span>
                    </div>
                `;

                if (polygonData) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Building Name:</span>
                            <span class="info-value">${polygonData.building_name || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Number of Floors:</span>
                            <span class="info-value">${polygonData.number_floor || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Number of Shops:</span>
                            <span class="info-value">${polygonData.number_shop || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Bills:</span>
                            <span class="info-value">${polygonData.number_bill || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Completed Bills:</span>
                            <span class="info-value">${pointCount}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">
                                ${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}
                            </span>
                        </div>
                    `;
                }

                if (actualPointData) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Assessment No:</span>
                            <span class="info-value">${actualPointData.assessment || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Owner Name:</span>
                            <span class="info-value">${actualPointData.owner_name || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">${actualPointData.phone_number || 'N/A'}</span>
                        </div>
                    `;
                }

                $('#featureDetails').html(html);
                $('#featureInfo').fadeIn();
            }

            // Click handler for map features
            map.on('click', function(evt) {
                const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                if (feature) {
                    const gisid = feature.get('gisid');
                    const type = feature.get('type');
                    if (gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(feature.clone());
                        showFeatureInfo(gisid, type);
                    }
                } else {
                    $('#featureInfo').fadeOut();
                    highlightSource.clear();
                }
            });

            // Search button handlers
            $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
            $('#gisidSearchInput').on('keypress', (e) => {
                if (e.key === 'Enter') searchByGISID($('#gisidSearchInput').val().trim());
            });

            $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
            $('#assessmentSearchInput').on('keypress', (e) => {
                if (e.key === 'Enter') searchByAssessment($('#assessmentSearchInput').val().trim());
            });

            // Close feature info
            $('#closeFeatureInfo').on('click', () => {
                $('#featureInfo').fadeOut();
                highlightSource.clear();
            });

            // Mobile UI handlers
            $('#mobileSearchBtn').on('click', () => {
                $('.search-container').toggle();
            });

            $('#mobileLayersBtn').on('click', () => {
                $('#mobileLayerSwitcher').fadeIn();
            });

            $('#closeMobileLayers').on('click', () => {
                $('#mobileLayerSwitcher').fadeOut();
            });

            // Flash message
            function showFlashMessage(message, type = 'info') {
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
                const flashHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                         style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('body').append(flashHtml);
                setTimeout(() => $('.alert').alert('close'), 5000);
            }

            // Fit view to show all features
            setTimeout(() => {
                const extent = ol.extent.createEmpty();
                polygonSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                pointSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                if (!ol.extent.isEmpty(extent)) {
                    map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 1000 });
                }
            }, 500);

            console.log("Commissioner Map Loaded Successfully");
        });
    </script>
@endsection
