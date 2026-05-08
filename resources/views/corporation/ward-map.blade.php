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

    .map-wrapper {
        position: relative;
        width: 100%;
        height: calc(100vh - 80px);
        min-height: 500px;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    #map {
        width: 100%;
        height: 100%;
        background: #e8e8e8;
    }

    /* Search Container - Clean & Modern */
    .search-container {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        padding: 16px;
        width: 340px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
        transition: all 0.3s ease;
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
        background: #f0f0f0;
        border-radius: 12px;
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
        transition: all 0.3s;
        border-radius: 8px;
        font-size: 13px;
    }

    .search-tab.active {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        color: white;
        box-shadow: 0 2px 6px rgba(212, 161, 62, 0.3);
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
        padding: 10px 14px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s;
        background: white;
    }

    .search-box input:focus {
        outline: none;
        border-color: #D4A13E;
        box-shadow: 0 0 0 3px rgba(212, 161, 62, 0.1);
    }

    .search-box button {
        background: linear-gradient(135deg, #0B2B40, #1A6B6E);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0 18px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .search-box button:hover {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        transform: scale(1.02);
    }

    .search-results {
        max-height: 250px;
        overflow-y: auto;
        margin-top: 12px;
        border: 1px solid #eee;
        border-radius: 12px;
        display: none;
        background: white;
    }

    .search-result-item {
        padding: 10px 14px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
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
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        padding: 14px;
        z-index: 1000;
        width: 190px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
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
        margin-bottom: 10px;
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
        border-radius: 6px;
        transition: all 0.2s;
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

    /* Feature Info Panel */
    .feature-info {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        padding: 16px;
        z-index: 1000;
        max-width: 320px;
        min-width: 260px;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
    }

    .feature-info h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 3px solid #D4A13E;
        padding-left: 10px;
    }

    .feature-info .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.05);
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        color: #666;
        transition: all 0.2s;
    }

    .feature-info .close-btn:hover {
        background: #E86A5F;
        color: white;
    }

    .info-row {
        margin-bottom: 8px;
        font-size: 12px;
        display: flex;
        flex-wrap: wrap;
    }

    .info-label {
        font-weight: 600;
        color: #0B2B40;
        width: 100px;
        font-size: 11px;
    }

    .info-value {
        color: #555;
        flex: 1;
        word-break: break-word;
    }

    /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        left: 20px;
        z-index: 1000;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .zoom-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: white;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.2s;
        color: #0B2B40;
    }

    .zoom-btn:hover {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        color: white;
    }

    .zoom-btn:first-child {
        border-bottom: 1px solid #eee;
    }

    /* Route Button */
    .route-btn {
        position: absolute;
        bottom: 20px;
        left: 80px;
        z-index: 1000;
        background: linear-gradient(135deg, #1A6B6E, #0B2B40);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 16px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
        display: none;
    }

    .route-btn:hover {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        transform: translateY(-2px);
    }

    /* Live Location Button */
    .live-location-btn {
        position: absolute;
        bottom: 20px;
        left: 80px;
        z-index: 1000;
        background: linear-gradient(135deg, #1A6B6E, #0B2B40);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 16px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
    }

    .live-location-btn:hover {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        transform: translateY(-2px);
    }

    .live-location-btn.active {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    /* Route Info Panel */
    .route-info {
        position: absolute;
        bottom: 20px;
        left: 180px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        padding: 16px;
        z-index: 1000;
        max-width: 320px;
        min-width: 260px;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.3);
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
        border-radius: 10px;
        margin-bottom: 12px;
        font-size: 12px;
    }

    .directions-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .direction-step {
        padding: 6px;
        border-bottom: 1px solid #eee;
        font-size: 11px;
        display: flex;
        gap: 8px;
    }

    .step-number {
        background: #1A6B6E;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
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
    }

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

    /* Badge Styles */
    .badge-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
    }

    .badge-completed {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .badge-pending {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #333;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .map-wrapper {
            height: calc(100vh - 60px);
            border-radius: 0;
        }

        .search-container {
            top: 10px;
            left: 10px;
            right: 10px;
            width: auto;
            max-width: calc(100% - 20px);
            padding: 12px;
        }

        .search-container h4 {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .search-tab {
            padding: 6px 10px;
            font-size: 12px;
        }

        .search-box input {
            padding: 8px 12px;
            font-size: 13px;
        }

        .search-box button {
            padding: 0 14px;
        }

        .layer-switcher {
            top: auto;
            bottom: 70px;
            right: 10px;
            width: 160px;
            padding: 10px;
        }

        .feature-info {
            bottom: 70px;
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .zoom-controls {
            bottom: 70px;
            left: 10px;
        }

        .zoom-btn {
            width: 36px;
            height: 36px;
        }

        .live-location-btn {
            bottom: 70px;
            left: 60px;
            padding: 8px 14px;
            font-size: 11px;
        }

        .route-info {
            bottom: 70px;
            left: 10px;
            right: 10px;
            max-width: none;
        }

        .info-label {
            width: 80px;
        }
    }

    /* Desktop Large Screens */
    @media (min-width: 1200px) {
        .map-wrapper {
            height: calc(100vh - 80px);
        }
    }

    /* Projector / Large Display */
    @media (min-width: 1600px) {
        .map-wrapper {
            height: calc(100vh - 80px);
        }

        .search-container {
            width: 400px;
            padding: 20px;
        }

        .search-container h4 {
            font-size: 18px;
        }

        .search-tab {
            font-size: 14px;
            padding: 10px 15px;
        }

        .search-box input {
            font-size: 15px;
            padding: 12px 16px;
        }

        .layer-switcher {
            width: 220px;
            padding: 18px;
        }

        .feature-info {
            max-width: 380px;
        }
    }

    .ol-attribution {
        display: none;
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
                    <div id="featureDetails"></div>
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
<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
<script>
$(document).ready(function() {
    console.log("Initializing Commissioner Ward Map...");

    // Data from server
    let polygons = @json($polygons ?? []);
    let lines = @json($lines ?? []);
    let points = @json($points ?? []);
    let pointDatas = @json($pointDatas ?? []);
    let polygonDatas = @json($polygonDatas ?? []);
    let ward = @json($ward ?? []);

    let currentLocationMarker = null;
    let locationWatchId = null;
    let isLiveLocationActive = false;
    let selectedFeature = null;
    let routeLayer = null;
    let highlightSource = null;

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
                fill: new ol.style.Fill({ color: color }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            }),
            text: new ol.style.Text({
                text: gisid ? String(gisid) : "",
                font: "10px Arial",
                offsetY: -12,
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
            stroke: new ol.style.Stroke({ color: color, width: 2 }),
            fill: new ol.style.Fill({ color: "rgba(22, 121, 171, 0.05)" })
        });
    }

    function getLineStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#ffc107", width: 2 })
        });
    }

    function getHighlightStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#ff6600", width: 4 }),
            fill: new ol.style.Fill({ color: "rgba(255, 102, 0, 0.15)" }),
            image: new ol.style.Circle({
                radius: 10,
                fill: new ol.style.Fill({ color: "#ff6600" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            })
        });
    }

    function getHumanLocationStyle() {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 10,
                fill: new ol.style.Fill({ color: "#0066cc" }),
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
        if (droneImageURL && imageExtent[0] !== 0) {
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
            source: new ol.source.ImageStatic({ url: "", imageExtent: [0, 0, 0, 0] }),
            visible: false
        });
    }

    // Vector Sources
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

    const lineSource = new ol.source.Vector();
    lines.forEach(l => {
        try {
            let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
            if (coords && coords.length >= 2) {
                if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
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
    let boundaryLayer = null;
    if (ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
        const boundary = ward.boundary[0];
        const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
        boundaryLayer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [new ol.Feature({
                    geometry: new ol.geom.Polygon([transformedBoundary])
                })]
            }),
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({ color: "#ff0000", width: 2 }),
                fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.03)" })
            }),
            visible: true
        });
    } else {
        boundaryLayer = new ol.layer.Vector({ source: new ol.source.Vector(), visible: true });
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
    routeLayer = new ol.layer.Vector({
        source: routeSource,
        style: new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#0066cc", width: 4, lineDash: [8, 8] })
        })
    });

    // Set default center
    let defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);
    if (ward.boundary && ward.boundary[0] && ward.boundary[0].length > 0) {
        const center = ol.extent.getCenter(ol.proj.transformExtent(
            [ward.boundary[0][0][0], ward.boundary[0][0][1], ward.boundary[0][2][0], ward.boundary[0][2][1]],
            'EPSG:4326', 'EPSG:3857'
        ));
        defaultCenter = center;
    }

    // Initialize Map
    const map = new ol.Map({
        target: 'map',
        layers: [osmLayer, satelliteLayer, terrainLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer],
        view: new ol.View({
            projection: "EPSG:3857",
            center: defaultCenter,
            zoom: 16
        }),
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

    // Search Tabs
    $('.search-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.search-tab').removeClass('active');
        $(this).addClass('active');
        $('.search-panel').removeClass('active');
        $(`#${tab}Panel`).addClass('active');
        $(`#${tab}Results`).hide();
    });

    // Search Functions
    function searchByGISID(gisid) {
        if (!gisid) { showFlashMessage('Please enter GIS ID', 'warning'); return; }

        $('#loadingSpinner').fadeIn();
        highlightSource.clear();

        let foundFeature = null;
        polygonSource.forEachFeature(f => {
            if (f.get('gisid') && f.get('gisid').toString() === gisid) { foundFeature = f; return true; }
        });
        if (!foundFeature) {
            pointSource.forEachFeature(f => {
                if (f.get('gisid') && f.get('gisid').toString() === gisid) { foundFeature = f; return true; }
            });
        }

        if (foundFeature) {
            highlightSource.addFeature(foundFeature.clone());
            map.getView().fit(foundFeature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
            showFeatureInfo(gisid);
            selectedFeature = foundFeature;
            $('#routeBtn').show();
            $('#gisidResults').hide();
            $('#gisidSearchInput').val('');
        } else {
            showFlashMessage(`GIS ID "${gisid}" not found`, "error");
            $('#gisidResults').html('<div class="search-result-item text-danger">No results found</div>').show();
            setTimeout(() => $('#gisidResults').fadeOut(), 2000);
        }
        $('#loadingSpinner').fadeOut();
    }

    function searchByAssessment(assessmentNo) {
        if (!assessmentNo) { showFlashMessage('Please enter Assessment Number', 'warning'); return; }

        $('#loadingSpinner').fadeIn();
        highlightSource.clear();

        const pointData = pointDatas.find(d => d.assessment == assessmentNo);
        if (pointData && pointData.point_gisid) {
            let foundFeature = null;
            pointSource.forEachFeature(f => {
                if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid) {
                    foundFeature = f;
                    return true;
                }
            });
            if (foundFeature) {
                highlightSource.addFeature(foundFeature.clone());
                map.getView().fit(foundFeature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
                showFeatureInfo(pointData.point_gisid);
                selectedFeature = foundFeature;
                $('#routeBtn').show();
                $('#assessmentResults').hide();
                $('#assessmentSearchInput').val('');
            } else {
                showFlashMessage(`Assessment "${assessmentNo}" not found on map`, "error");
            }
        } else {
            showFlashMessage(`Assessment "${assessmentNo}" not found`, "error");
        }
        $('#loadingSpinner').fadeOut();
    }

    function showFeatureInfo(gisid) {
        const polygonData = polygonDatas.find(d => d.gisid == gisid);
        const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
        const pointData = pointDatas.find(d => d.point_gisid == gisid);

        let html = `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;
        if (polygonData) {
            html += `
                <div class="info-row"><span class="info-label">Building:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Shops:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Total Bills:</span><span class="info-value">${polygonData.number_bill || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Completed:</span><span class="info-value">${pointCount}</span></div>
                <div class="info-row"><span class="info-label">Status:</span><span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}</span></div>
            `;
        }
        if (pointData) {
            html += `
                <div class="info-row"><span class="info-label">Assessment:</span><span class="info-value">${pointData.assessment || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Owner:</span><span class="info-value">${pointData.owner_name || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Phone:</span><span class="info-value">${pointData.phone_number || 'N/A'}</span></div>
            `;
        }
        $('#featureDetails').html(html);
        $('#featureInfo').fadeIn();
    }

    // Live Location
    function toggleLiveLocation() {
        if (isLiveLocationActive) {
            if (locationWatchId) navigator.geolocation.clearWatch(locationWatchId);
            locationSource.clear();
            currentLocationMarker = null;
            isLiveLocationActive = false;
            $('#liveLocationBtn').removeClass('active').html('<i class="fas fa-location-dot me-2"></i>Live Location');
            showFlashMessage('Location tracking stopped', 'info');
        } else {
            if (!navigator.geolocation) { alert('Geolocation not supported'); return; }
            isLiveLocationActive = true;
            $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');

            locationWatchId = navigator.geolocation.watchPosition(
                (position) => {
                    const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]);
                    locationSource.clear();
                    currentLocationMarker = new ol.Feature({ geometry: new ol.geom.Point(coords) });
                    locationSource.addFeature(currentLocationMarker);
                },
                (error) => { showFlashMessage('Location error: ' + error.message, 'error'); toggleLiveLocation(); },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }

    // Calculate Route - Only when user clicks the route button
    async function calculateRoute() {
        if (!selectedFeature) {
            showFlashMessage('Please select a property first by searching or clicking on map', 'warning');
            return;
        }
        if (!currentLocationMarker) {
            showFlashMessage('Please enable Live Location first', 'warning');
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

            const url = `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.code === 'Ok' && data.routes.length > 0) {
                const route = data.routes[0];
                const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                routeSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));

                const distance = route.distance < 1000 ? route.distance.toFixed(0) + ' meters' : (route.distance / 1000).toFixed(2) + ' km';
                const duration = Math.floor(route.duration / 60) + ' min ' + Math.floor(route.duration % 60) + ' sec';

                let stepsHtml = '';
                route.legs[0].steps.forEach((step, i) => {
                    if (step.maneuver.instruction) {
                        stepsHtml += `<div class="direction-step"><div class="step-number">${i + 1}</div><div><div class="step-instruction">${step.maneuver.instruction}</div><div class="step-distance">${step.distance.toFixed(0)} m</div></div></div>`;
                    }
                });

                $('#routeSummary').html(`<strong>Distance:</strong> ${distance}<br><strong>Duration:</strong> ${duration}`);
                $('#directionsList').html(stepsHtml);
                $('#routeInfo').fadeIn();
                map.getView().fit(routeSource.getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
            } else {
                showFlashMessage('No route found', 'error');
            }
        } catch (error) {
            showFlashMessage('Error calculating route', 'error');
        }
        $('#loadingSpinner').fadeOut();
    }

    // Map Click Handler - No auto route prompt
    map.on('click', function(evt) {
        const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
        if (feature && feature.get('gisid')) {
            const gisid = feature.get('gisid');
            highlightSource.clear();
            highlightSource.addFeature(feature.clone());
            showFeatureInfo(gisid);
            selectedFeature = feature;
            $('#routeBtn').show();
        } else {
            $('#featureInfo').fadeOut();
            highlightSource.clear();
            selectedFeature = null;
            $('#routeBtn').hide();
        }
    });

    // Button Events
    $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
    $('#gisidSearchInput').on('keypress', (e) => e.key === 'Enter' && searchByGISID($('#gisidSearchInput').val().trim()));
    $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
    $('#assessmentSearchInput').on('keypress', (e) => e.key === 'Enter' && searchByAssessment($('#assessmentSearchInput').val().trim()));
    $('#liveLocationBtn').on('click', toggleLiveLocation);
    $('#routeBtn').on('click', calculateRoute);
    $('#closeFeatureInfo').on('click', () => $('#featureInfo').fadeOut());
    $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());

    // Flash Message
    function showFlashMessage(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : (type === 'warning' ? 'alert-warning' : 'alert-info');
        const flashHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 100px; right: 20px; z-index: 9999; min-width: 280px; max-width: 400px;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        $('body').append(flashHtml);
        setTimeout(() => $('.alert').alert('close'), 4000);
    }

    // Auto-fit map to show all features
    setTimeout(() => {
        const extent = ol.extent.createEmpty();
        polygonSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
        pointSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
        if (!ol.extent.isEmpty(extent) && extent[0] !== Infinity) {
            map.getView().fit(extent, { padding: [30, 30, 30, 30], duration: 1000 });
        }
    }, 800);

    console.log("Commissioner Ward Map Loaded Successfully");
});
</script>
@endpush
