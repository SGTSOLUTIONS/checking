{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@push('styles')
<style>
    #map {
        width: 100%;
        height: 600px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        /* background: #e8e8e8; */
    }

    .map-container {
        background: #fff;
        padding: 15px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        position: relative;
    }

    /* Search Container */
    .search-container {
        position: absolute;
        top: 30px;
        left: 30px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        padding: 20px;
        width: 360px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.2);
    }

    .search-container h4 {
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 4px solid #D4A13E;
        padding-left: 12px;
    }

    .search-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
        background: #f5f5f5;
        border-radius: 10px;
        padding: 4px;
    }

    .search-tab {
        flex: 1;
        padding: 10px;
        cursor: pointer;
        border: none;
        background: transparent;
        font-weight: 500;
        color: #666;
        transition: all 0.3s;
        border-radius: 8px;
        font-size: 14px;
    }

    .search-tab.active {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        color: white;
        box-shadow: 0 2px 8px rgba(212, 161, 62, 0.3);
    }

    .search-panel {
        display: none;
    }

    .search-panel.active {
        display: block;
    }

    .search-box {
        display: flex;
        gap: 10px;
    }

    .search-box input {
        flex: 1;
        padding: 12px 15px;
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
        padding: 0 20px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .search-box button:hover {
        background: linear-gradient(135deg, #D4A13E, #E86A5F);
        transform: translateY(-2px);
    }

    .search-results {
        max-height: 280px;
        overflow-y: auto;
        margin-top: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        display: none;
        background: white;
    }

    .search-result-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .search-result-item:hover {
        background: rgba(212, 161, 62, 0.1);
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .result-gisid {
        font-weight: bold;
        color: #1A6B6E;
        font-size: 14px;
    }

    .result-type {
        font-size: 11px;
        color: #D4A13E;
        margin-left: 8px;
    }

    /* Layer Switcher */
    .layer-switcher {
        position: absolute;
        top: 30px;
        right: 30px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        padding: 15px;
        z-index: 1000;
        width: 200px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.2);
    }

    .layer-switcher h4 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        color: #0B2B40;
        border-bottom: 2px solid #D4A13E;
        padding-bottom: 8px;
    }

    .layer-group {
        margin-bottom: 12px;
    }

    .layer-group h5 {
        font-size: 12px;
        color: #D4A13E;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .layer-option {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        cursor: pointer;
        font-size: 13px;
        padding: 5px 8px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .layer-option:hover {
        background: rgba(212, 161, 62, 0.1);
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
        display: flex;
        align-items: center;
        gap: 8px;
        color: #333;
    }

    /* Feature Info Panel */
    .feature-info {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        padding: 18px;
        z-index: 1000;
        max-width: 340px;
        min-width: 280px;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.2);
    }

    .feature-info h4 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 4px solid #D4A13E;
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
        color: #666;
        transition: all 0.2s;
    }

    .feature-info .close-btn:hover {
        background: #E86A5F;
        color: white;
    }

    .info-row {
        margin-bottom: 12px;
        font-size: 13px;
        display: flex;
        flex-wrap: wrap;
    }

    .info-label {
        font-weight: 600;
        color: #0B2B40;
        width: 110px;
        font-size: 12px;
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
        width: 44px;
        height: 44px;
        border: none;
        background: white;
        cursor: pointer;
        font-size: 18px;
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

    /* Route Info Panel */
    .route-info {
        position: absolute;
        bottom: 20px;
        left: 80px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        padding: 18px;
        z-index: 1000;
        max-width: 320px;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 161, 62, 0.2);
    }

    .route-info h4 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        color: #0B2B40;
        border-left: 4px solid #D4A13E;
        padding-left: 12px;
    }

    .route-summary {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 15px;
        font-size: 13px;
    }

    .directions-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .direction-step {
        padding: 8px;
        border-bottom: 1px solid #eee;
        font-size: 12px;
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
        font-size: 11px;
        flex-shrink: 0;
    }

    .step-instruction {
        font-weight: 500;
        margin-bottom: 3px;
    }

    .step-distance {
        font-size: 10px;
        color: #888;
    }

    .close-route {
        position: absolute;
        top: 12px;
        right: 12px;
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
        padding: 25px 35px;
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
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
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

    .badge-missing {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
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
        padding: 10px 15px;
        cursor: pointer;
        font-size: 13px;
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .search-container {
            top: 10px;
            left: 10px;
            right: 10px;
            width: auto;
            padding: 15px;
        }

        .layer-switcher {
            top: auto;
            bottom: 80px;
            right: 10px;
            width: 180px;
        }

        .feature-info {
            bottom: 80px;
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .zoom-controls {
            bottom: 80px;
            left: 10px;
        }

        .live-location-btn {
            bottom: 80px;
            left: 70px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="map-container position-relative">
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

                <!-- Zoom Controls -->
                <div class="zoom-controls">
                    <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                    <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                </div>

                <!-- Live Location Button -->
                <button class="live-location-btn" id="liveLocationBtn">
                    <i class="fas fa-location-dot me-2"></i>Live Location
                </button>

                <!-- Feature Info Panel -->
                <div class="feature-info" id="featureInfo">
                    <button class="close-btn" id="closeFeatureInfo">&times;</button>
                    <h4><i class="fas fa-info-circle me-2"></i>Property Details</h4>
                    <div id="featureDetails"></div>
                </div>

                <!-- Route Info Panel -->
                <div class="route-info" id="routeInfo">
                    <button class="close-route" id="closeRouteInfo">&times;</button>
                    <h4><i class="fas fa-route me-2"></i>Route Information</h4>
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
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
<script>
// Wait for DOM and jQuery to be fully loaded
$(document).ready(function() {
    console.log("Document ready, initializing map...");

    // Data from server
    let polygons = @json($polygons ?? []);
    let lines = @json($lines ?? []);
    let points = @json($points ?? []);
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

    let currentLocationMarker = null;
    let locationWatchId = null;
    let isLiveLocationActive = false;
    let currentRoute = null;
    let selectedFeature = null;
    let highlightSource = null;

    // Style Functions
    function getPointStyle(feature) {
        const gisid = feature.get("gisid");
        const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
        const polygonData = polygonDatas.find(d => d.gisid == gisid);
        let color = "#1679AB";

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

    function getHumanLocationStyle() {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 12,
                fill: new ol.style.Fill({ color: "#0066cc" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 3 })
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

    let droneLayer = null;
    if (droneImageURL && imageExtent[0] !== 0) {
        droneLayer = new ol.layer.Image({
            source: new ol.source.ImageStatic({
                url: droneImageURL,
                imageExtent: imageExtent,
                imageSmoothing: false
            }),
            opacity: 0.85,
            visible: true
        });
    } else {
        droneLayer = new ol.layer.Image({
            source: new ol.source.ImageStatic({
                url: "",
                imageExtent: [0, 0, 0, 0]
            }),
            visible: false
        });
    }

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
        } catch(e) { console.error('Polygon error:', e); }
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
        } catch(e) { console.error('Line error:', e); }
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
        } catch(e) { console.error('Point error:', e); }
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
                stroke: new ol.style.Stroke({ color: "#ff0000", width: 3 }),
                fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.05)" })
            }),
            visible: true
        });
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
            stroke: new ol.style.Stroke({ color: "#0066cc", width: 4, lineDash: [10, 10] })
        })
    });

    // Set default center
    let defaultCenter;
    if (imageExtent[0] !== 0) {
        defaultCenter = ol.extent.getCenter(imageExtent);
    } else {
        defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);
    }

    // Initialize Map
    const map = new ol.Map({
        target: 'map',
        layers: [osmLayer, satelliteLayer, terrainLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer, highlightLayer, locationLayer, routeLayer],
        view: new ol.View({
            projection: "EPSG:3857",
            center: defaultCenter,
            zoom: 15
        }),
        controls: []
    });

    // Base Layer Switcher
    $('input[name="baseLayer"]').on('change', function() {
        const val = $(this).val();
        osmLayer.setVisible(val === 'osm');
        satelliteLayer.setVisible(val === 'satellite');
        terrainLayer.setVisible(val === 'terrain');
    });

    // Overlay Switchers
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
    });

    // GIS ID Search
    function searchByGISID(gisid) {
        if (!gisid) return;

        $('#loadingSpinner').fadeIn();
        highlightSource.clear();

        let foundFeature = null;

        polygonSource.forEachFeature(f => {
            if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                foundFeature = f;
                return true;
            }
        });

        if (!foundFeature) {
            pointSource.forEachFeature(f => {
                if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                    foundFeature = f;
                    return true;
                }
            });
        }

        if (foundFeature) {
            highlightSource.addFeature(foundFeature.clone());
            map.getView().fit(foundFeature.getGeometry().getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
            showFeatureInfo(gisid);
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
                $('#assessmentResults').hide();
            } else {
                showFlashMessage(`Assessment "${assessmentNo}" not found on map`, "error");
            }
        } else {
            showFlashMessage(`Assessment "${assessmentNo}" not found`, "error");
        }

        $('#loadingSpinner').fadeOut();
    }

    // Show Feature Info
    function showFeatureInfo(gisid) {
        const polygonData = polygonDatas.find(d => d.gisid == gisid);
        const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
        const pointData = pointDatas.find(d => d.point_gisid == gisid);

        let html = `<div class="info-row"><span class="info-label">GIS ID:</span><span class="info-value"><strong>${gisid}</strong></span></div>`;

        if (polygonData) {
            html += `
                <div class="info-row"><span class="info-label">Building Name:</span><span class="info-value">${polygonData.building_name || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Number of Floors:</span><span class="info-value">${polygonData.number_floor || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Number of Shops:</span><span class="info-value">${polygonData.number_shop || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Total Bills:</span><span class="info-value">${polygonData.number_bill || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Completed Bills:</span><span class="info-value">${pointCount}</span></div>
                <div class="info-row"><span class="info-label">Status:</span><span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}</span></div>
            `;
        }

        if (pointData) {
            html += `
                <div class="info-row"><span class="info-label">Assessment No:</span><span class="info-value">${pointData.assessment || 'N/A'}</span></div>
                <div class="info-row"><span class="info-label">Owner Name:</span><span class="info-value">${pointData.owner_name || 'N/A'}</span></div>
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
            if (!navigator.geolocation) {
                alert('Geolocation not supported');
                return;
            }
            isLiveLocationActive = true;
            $('#liveLocationBtn').addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');

            locationWatchId = navigator.geolocation.watchPosition(
                (position) => {
                    const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]);
                    locationSource.clear();
                    currentLocationMarker = new ol.Feature({ geometry: new ol.geom.Point(coords) });
                    locationSource.addFeature(currentLocationMarker);
                    map.getView().animate({ center: coords, zoom: 18, duration: 1000 });
                },
                (error) => {
                    showFlashMessage('Location error: ' + error.message, 'error');
                    toggleLiveLocation();
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }

    // Calculate Route
    async function calculateRouteToFeature(feature) {
        if (!currentLocationMarker) {
            showFlashMessage('Please enable live location first', 'warning');
            return;
        }

        $('#loadingSpinner').fadeIn();

        try {
            const startCoord = ol.proj.toLonLat(currentLocationMarker.getGeometry().getCoordinates());
            const targetGeom = feature.getGeometry();
            const endCoord = targetGeom.getType() === 'Point' ?
                ol.proj.toLonLat(targetGeom.getCoordinates()) :
                ol.proj.toLonLat(ol.extent.getCenter(targetGeom.getExtent()));

            const url = `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.code === 'Ok' && data.routes.length > 0) {
                const route = data.routes[0];
                const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                routeSource.clear();
                routeSource.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));

                const distance = route.distance < 1000 ? route.distance.toFixed(0) + ' meters' : (route.distance / 1000).toFixed(2) + ' km';
                const duration = Math.floor(route.duration / 60) + ' min';

                let stepsHtml = '';
                route.legs[0].steps.forEach((step, i) => {
                    stepsHtml += `
                        <div class="direction-step">
                            <div class="step-number">${i + 1}</div>
                            <div>
                                <div class="step-instruction">${step.maneuver.instruction}</div>
                                <div class="step-distance">${step.distance.toFixed(0)} m</div>
                            </div>
                        </div>
                    `;
                });

                $('#routeSummary').html(`<strong>Distance:</strong> ${distance}<br><strong>Duration:</strong> ${duration}`);
                $('#directionsList').html(stepsHtml);
                $('#routeInfo').fadeIn();
                map.getView().fit(routeSource.getExtent(), { padding: [50, 50, 50, 50], duration: 1000 });
            }
        } catch (error) {
            showFlashMessage('Error calculating route', 'error');
        }

        $('#loadingSpinner').fadeOut();
    }

    // Map Click Handler
    map.on('click', function(evt) {
        const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
        if (feature && feature.get('gisid')) {
            const gisid = feature.get('gisid');
            highlightSource.clear();
            highlightSource.addFeature(feature.clone());
            showFeatureInfo(gisid);
            selectedFeature = feature;

            // Show route option
            if (confirm('Calculate route to this location?')) {
                calculateRouteToFeature(feature);
            }
        } else {
            $('#featureInfo').fadeOut();
            highlightSource.clear();
        }
    });

    // Button Handlers
    $('#gisidSearchBtn').on('click', () => searchByGISID($('#gisidSearchInput').val().trim()));
    $('#gisidSearchInput').on('keypress', (e) => e.key === 'Enter' && searchByGISID($('#gisidSearchInput').val().trim()));

    $('#assessmentSearchBtn').on('click', () => searchByAssessment($('#assessmentSearchInput').val().trim()));
    $('#assessmentSearchInput').on('keypress', (e) => e.key === 'Enter' && searchByAssessment($('#assessmentSearchInput').val().trim()));

    $('#liveLocationBtn').on('click', toggleLiveLocation);
    $('#closeFeatureInfo').on('click', () => $('#featureInfo').fadeOut());
    $('#closeRouteInfo').on('click', () => $('#routeInfo').fadeOut());

    // Flash Message
    function showFlashMessage(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
        const flashHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 100px; right: 20px; z-index: 9999; min-width: 300px;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        $('body').append(flashHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Fit map to features
    setTimeout(() => {
        const extent = ol.extent.createEmpty();
        polygonSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
        pointSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
        if (!ol.extent.isEmpty(extent)) {
            map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 1000 });
        }
    }, 500);

    console.log("Commissioner Ward Map Loaded Successfully");
});
</script>
@endpush
