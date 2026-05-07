{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $ward->ward_no . ' Map View - ' . ($corporation->name ?? 'Tamil Nadu Municipal Corporation'))

@section('styles')
@parent
<style>
    /* Map Container - Important:必须有明确的高度 */
    #map {
        width: 100%;
        height: 550px;
        border-radius: 12px;
        border: 2px solid #ddd;
        background: #f0f0f0;
        position: relative;
    }

    /* Ensure map container is visible */
    .map-wrapper {
        position: relative;
        width: 100%;
        min-height: 550px;
    }

    /* Layer Switcher Panel */
    .layer-switcher {
        position: absolute;
        top: 80px;
        right: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        padding: 15px;
        z-index: 1000;
        width: 240px;
        font-size: 13px;
    }

    .layer-switcher h4 {
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #102C57;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }

    .layer-group {
        margin-bottom: 15px;
    }

    .layer-group h5 {
        margin: 0 0 8px 0;
        font-size: 12px;
        font-weight: 600;
        color: #1679AB;
    }

    .layer-option {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        cursor: pointer;
    }

    .layer-option input {
        margin-right: 8px;
        cursor: pointer;
    }

    .layer-option label {
        cursor: pointer;
        margin: 0;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .layer-option i {
        width: 18px;
        font-size: 12px;
    }

    /* Search Panel */
    .search-panel {
        position: absolute;
        top: 80px;
        left: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        padding: 15px;
        z-index: 1000;
        width: 320px;
    }

    .search-panel h4 {
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #102C57;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }

    .search-type-group {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .search-type-group .form-check {
        margin-right: 15px;
    }

    .search-type-group .form-check-label {
        font-size: 12px;
    }

    .search-box {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .search-box input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
    }

    .search-box button {
        background: #1679AB;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 13px;
    }

    .search-box button:hover {
        background: #102C57;
    }

    .search-results {
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 8px;
        display: none;
    }

    .search-result-item {
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        font-size: 12px;
        transition: background 0.2s;
    }

    .search-result-item:hover {
        background: #FFCBCB;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .result-gisid {
        font-weight: 600;
        color: #1679AB;
    }

    .result-assessment {
        font-size: 11px;
        color: #666;
    }

    /* Feature Info Panel */
    .feature-info {
        position: absolute;
        bottom: 20px;
        right: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        padding: 15px;
        z-index: 1000;
        max-width: 320px;
        max-height: 400px;
        overflow-y: auto;
        display: none;
    }

    .feature-info h4 {
        margin-top: 0;
        margin-bottom: 10px;
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
        font-size: 16px;
        cursor: pointer;
        color: #999;
    }

    .feature-detail-row {
        margin-bottom: 8px;
        font-size: 12px;
    }

    .feature-detail-label {
        font-weight: 600;
        color: #1679AB;
        width: 100px;
        display: inline-block;
    }

    /* Statistics Cards */
    .stat-card-mini {
        background: white;
        border-radius: 12px;
        padding: 10px 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stat-card-mini h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        color: #102C57;
    }

    .stat-card-mini p {
        margin: 0;
        font-size: 11px;
        color: #666;
    }

    /* Popup Styling */
    .ol-popup {
        position: absolute;
        background: white;
        border-radius: 12px;
        padding: 12px;
        min-width: 250px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        border-left: 4px solid #1679AB;
        z-index: 1000;
    }

    .ol-popup:after {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-top-color: white;
        border-width: 8px;
        left: 20px;
        margin-left: -8px;
    }

    /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        left: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        z-index: 1000;
    }

    .zoom-controls button {
        width: 36px;
        height: 36px;
        border: none;
        background: white;
        cursor: pointer;
        font-size: 18px;
        transition: background 0.2s;
    }

    .zoom-controls button:first-child {
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #ddd;
    }

    .zoom-controls button:last-child {
        border-radius: 0 0 8px 8px;
    }

    .zoom-controls button:hover {
        background: #FFCBCB;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .search-panel {
            width: 280px;
        }
        .layer-switcher {
            width: 200px;
        }
        .feature-info {
            max-width: 280px;
        }
    }

    @media (max-width: 768px) {
        .search-panel, .layer-switcher {
            display: none;
        }
        #map {
            height: 65vh;
        }
    }

    /* Loading Spinner */
    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px;
        border-radius: 12px;
        z-index: 2000;
        display: none;
        text-align: center;
    }

    /* Error message */
    .map-error {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        z-index: 10;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Statistics Row -->
    <div class="row g-2 mb-3 px-3">
        <div class="col-md-3 col-6">
            <div class="stat-card-mini">
                <h3 id="totalBuildingsCount">0</h3>
                <p>Total Buildings</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-mini">
                <h3 id="gisIdCount">0</h3>
                <p>GIS ID Assigned</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-mini">
                <h3 id="totalRoadsCount">0</h3>
                <p>Road Segments</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-mini">
                <h3 id="totalPointsCount">0</h3>
                <p>Point Features</p>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="position-relative px-3 map-wrapper">
        <div id="map"></div>
    </div>

    <!-- Layer Switcher Panel -->
    <div class="layer-switcher">
        <h4><i class="fas fa-layer-group me-2"></i>Map Layers</h4>
        <div class="layer-group">
            <h5>Base Maps</h5>
            <div class="layer-option">
                <input type="radio" id="osmLayer" name="baseLayer" value="osm" checked>
                <label for="osmLayer"><i class="fas fa-map"></i> OpenStreetMap</label>
            </div>
            <div class="layer-option">
                <input type="radio" id="terrainLayer" name="baseLayer" value="terrain">
                <label for="terrainLayer"><i class="fas fa-mountain"></i> Terrain</label>
            </div>
            <div class="layer-option">
                <input type="radio" id="satelliteLayer" name="baseLayer" value="satellite">
                <label for="satelliteLayer"><i class="fas fa-satellite"></i> Satellite</label>
            </div>
        </div>
        <div class="layer-group">
            <h5>Overlays</h5>
            <div class="layer-option">
                <input type="checkbox" id="droneLayerCheck" checked>
                <label for="droneLayerCheck"><i class="fas fa-drone"></i> Drone Image</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="boundaryLayerCheck" checked>
                <label for="boundaryLayerCheck"><i class="fas fa-vector-square"></i> Ward Boundary</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="polygonLayerCheck" checked>
                <label for="polygonLayerCheck"><i class="fas fa-draw-polygon"></i> Buildings</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="lineLayerCheck" checked>
                <label for="lineLayerCheck"><i class="fas fa-road"></i> Roads</label>
            </div>
            <div class="layer-option">
                <input type="checkbox" id="pointLayerCheck" checked>
                <label for="pointLayerCheck"><i class="fas fa-map-marker-alt"></i> Points</label>
            </div>
        </div>
    </div>

    <!-- Search Panel -->
    <div class="search-panel">
        <h4><i class="fas fa-search me-2"></i>Search Building</h4>
        <div class="search-type-group">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="searchType" id="searchAssessment" value="assessment" checked>
                <label class="form-check-label" for="searchAssessment">Assessment No</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="searchType" id="searchGisid" value="gisid">
                <label class="form-check-label" for="searchGisid">GIS ID</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="searchType" id="searchBoth" value="both">
                <label class="form-check-label" for="searchBoth">Both</label>
            </div>
        </div>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Enter Assessment No or GIS ID...">
            <button id="searchBtn"><i class="fas fa-search"></i> Search</button>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>

    <!-- Feature Info Panel -->
    <div class="feature-info" id="featureInfo">
        <button class="close-btn" id="closeFeatureInfo">&times;</button>
        <h4><i class="fas fa-info-circle me-2"></i>Feature Details</h4>
        <div id="featureDetails"></div>
    </div>

    <!-- Zoom Controls -->
    <div class="zoom-controls">
        <button id="zoomInBtn" title="Zoom In"><i class="fas fa-plus"></i></button>
        <button id="zoomOutBtn" title="Zoom Out"><i class="fas fa-minus"></i></button>
        <button id="fitViewBtn" title="Fit to View"><i class="fas fa-expand"></i></button>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-light mb-2"></div>
        <div>Loading Map...</div>
    </div>
</div>

<!-- Charts and Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #102C57, #1679AB); color: white;">
                <h5 class="modal-title"><i class="fas fa-chart-bar me-2"></i>Building Statistics</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Building Type Distribution</h6>
                        <canvas id="buildingTypeChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Construction Type Distribution</h6>
                        <canvas id="constructionTypeChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h6 class="fw-bold">Building Usage Distribution</h6>
                        <canvas id="usageTypeChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h6 class="fw-bold">Area Variation</h6>
                        <canvas id="areaVariationChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        // Show loading spinner
        $('#loadingSpinner').show();

        // Data from Laravel
        let polygonsData = @json($polygons);
        let linesData = @json($lines);
        let pointsData = @json($points);
        let buildingData = @json($buildingData);
        let misData = @json($misData);
        let ward = @json($ward);
        let corporation = @json($corporation);

        // Update statistics display
        $('#totalBuildingsCount').text(polygonsData.length);
        $('#totalRoadsCount').text(linesData.length);
        $('#totalPointsCount').text(pointsData.length);

        let gisIdCountValue = polygonsData.filter(p => p.gisid && p.gisid !== null).length;
        $('#gisIdCount').text(gisIdCountValue);

        // Building statistics
        const buildingTypes = @json($buildingTypes);
        const constructionTypes = @json($constructionTypes);
        const usageTypes = @json($usageTypes);
        const areaVariations = @json($areaVariations);

        // Drone image data
        const droneImageURL = "{{ asset($ward->drone_image ?? '') }}";

        // Calculate image extent from boundary
        let imageExtent = null;
        let boundaryCoordinates = [];

        @if($ward->boundary && is_array($ward->boundary) && count($ward->boundary) > 0)
            @php
                $firstBoundary = $ward->boundary[0] ?? null;
                if ($firstBoundary && is_array($firstBoundary) && count($firstBoundary) > 0) {
                    $minLon = min(array_column($firstBoundary, 0));
                    $maxLon = max(array_column($firstBoundary, 0));
                    $minLat = min(array_column($firstBoundary, 1));
                    $maxLat = max(array_column($firstBoundary, 1));
                    $imageExtent = [$minLon, $minLat, $maxLon, $maxLat];
                    $boundaryCoordinates = $firstBoundary;
                }
            @endphp
            imageExtent = @json($imageExtent);
            boundaryCoordinates = @json($boundaryCoordinates);
        @endif

        // Map variables
        let map = null;
        let currentPopup = null;
        let buildingFeatures = [];

        // Wait for OpenLayers to be fully loaded
        function waitForOpenLayers() {
            if (typeof ol !== 'undefined') {
                initMap();
            } else {
                console.log('Waiting for OpenLayers to load...');
                setTimeout(waitForOpenLayers, 100);
            }
        }

        // Initialize Map
        function initMap() {
            try {
                console.log('Initializing map...');

                // Check if map container exists
                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error('Map container not found');
                    $('#loadingSpinner').hide();
                    return;
                }

                // Calculate center from boundary
                let center = [78.1198, 9.9252]; // Default Tamil Nadu center
                if (boundaryCoordinates && boundaryCoordinates.length > 0) {
                    let sumLon = 0, sumLat = 0;
                    boundaryCoordinates.forEach(coord => {
                        sumLon += coord[0];
                        sumLat += coord[1];
                    });
                    center = [sumLon / boundaryCoordinates.length, sumLat / boundaryCoordinates.length];
                }

                // Create layers
                const osmLayerObj = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });

                const terrainLayerObj = new ol.layer.Tile({
                    source: new ol.source.OSM({
                        url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
                    }),
                    visible: false
                });

                const satelliteLayerObj = new ol.layer.Tile({
                    source: new ol.source.OSM({
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    }),
                    visible: false
                });

                // Drone image layer
                let droneLayerObj = new ol.layer.Image({
                    visible: false
                });

                if (droneImageURL && droneImageURL !== '' && imageExtent && imageExtent.length === 4) {
                    try {
                        const minCoord = ol.proj.fromLonLat([imageExtent[0], imageExtent[1]]);
                        const maxCoord = ol.proj.fromLonLat([imageExtent[2], imageExtent[3]]);
                        const extent3857 = [minCoord[0], minCoord[1], maxCoord[0], maxCoord[1]];

                        droneLayerObj = new ol.layer.Image({
                            source: new ol.source.ImageStatic({
                                url: droneImageURL,
                                imageExtent: extent3857,
                                imageSmoothing: false
                            }),
                            opacity: 0.85,
                            visible: true
                        });
                        console.log('Drone layer added successfully');
                    } catch(e) {
                        console.error('Drone layer error:', e);
                    }
                }

                // Boundary layer
                let boundaryLayerObj = new ol.layer.Vector({
                    source: new ol.source.Vector(),
                    visible: true
                });

                if (boundaryCoordinates && boundaryCoordinates.length > 0) {
                    const transformedBoundary = boundaryCoordinates.map(coord => ol.proj.fromLonLat(coord));
                    const boundaryGeometry = new ol.geom.Polygon([transformedBoundary]);
                    boundaryLayerObj = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.Feature({ geometry: boundaryGeometry })]
                        }),
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({ color: '#FF0000', width: 3, lineDash: [8, 4] }),
                            fill: new ol.style.Fill({ color: 'rgba(255, 0, 0, 0.05)' })
                        }),
                        visible: true
                    });
                }

                // Polygon layer (Buildings)
                const polygonSourceObj = new ol.source.Vector();

                polygonsData.forEach(poly => {
                    if (poly.geojson) {
                        try {
                            const geojson = JSON.parse(poly.geojson);
                            const format = new ol.format.GeoJSON();
                            const feature = format.readFeature(geojson, {
                                dataProjection: 'EPSG:4326',
                                featureProjection: 'EPSG:3857'
                            });
                            if (feature) {
                                feature.set('id', poly.id);
                                feature.set('gisid', poly.gisid);
                                feature.set('type', 'building');

                                const buildingInfo = buildingData.find(b => b.gisid == poly.gisid);
                                if (buildingInfo) {
                                    feature.set('building_type', buildingInfo.building_type);
                                    feature.set('construction_type', buildingInfo.construction_type);
                                    feature.set('building_usage', buildingInfo.building_usage);
                                    feature.set('owner_name', buildingInfo.owner_name);
                                    feature.set('plot_area', buildingInfo.plot_area);
                                }

                                polygonSourceObj.addFeature(feature);
                                buildingFeatures.push(feature);
                            }
                        } catch(e) {
                            console.error('Polygon parse error:', e);
                        }
                    }
                });

                console.log('Added ' + buildingFeatures.length + ' building features');

                const polygonLayerObj = new ol.layer.Vector({
                    source: polygonSourceObj,
                    style: function(feature) {
                        const gisid = feature.get('gisid');
                        const buildingType = feature.get('building_type');

                        let fillColor = 'rgba(22, 121, 171, 0.3)';
                        let strokeColor = '#1679AB';

                        if (buildingType) {
                            if (buildingType.includes('Residential') || buildingType.includes('Independent')) {
                                fillColor = 'rgba(40, 167, 69, 0.3)';
                                strokeColor = '#28a745';
                            } else if (buildingType.includes('Commercial') || buildingType.includes('Shop')) {
                                fillColor = 'rgba(255, 193, 7, 0.3)';
                                strokeColor = '#ffc107';
                            } else if (buildingType.includes('Industrial')) {
                                fillColor = 'rgba(23, 162, 184, 0.3)';
                                strokeColor = '#17a2b8';
                            } else if (buildingType.includes('Government')) {
                                fillColor = 'rgba(111, 66, 193, 0.3)';
                                strokeColor = '#6f42c1';
                            } else if (buildingType.includes('Educational')) {
                                fillColor = 'rgba(220, 53, 69, 0.3)';
                                strokeColor = '#dc3545';
                            } else if (buildingType.includes('Vacant')) {
                                fillColor = 'rgba(108, 117, 125, 0.3)';
                                strokeColor = '#6c757d';
                            }
                        }

                        return new ol.style.Style({
                            fill: new ol.style.Fill({ color: fillColor }),
                            stroke: new ol.style.Stroke({ color: strokeColor, width: 2 }),
                            text: new ol.style.Text({
                                text: gisid ? gisid.toString() : '',
                                font: '10px Poppins',
                                fill: new ol.style.Fill({ color: '#102C57' }),
                                stroke: new ol.style.Stroke({ color: 'white', width: 2 }),
                                offsetY: -8
                            })
                        });
                    },
                    visible: true
                });

                // Line layer (Roads)
                const lineSourceObj = new ol.source.Vector();
                linesData.forEach(line => {
                    if (line.geojson) {
                        try {
                            const geojson = JSON.parse(line.geojson);
                            const format = new ol.format.GeoJSON();
                            const feature = format.readFeature(geojson, {
                                dataProjection: 'EPSG:4326',
                                featureProjection: 'EPSG:3857'
                            });
                            if (feature) {
                                feature.set('id', line.id);
                                feature.set('gisid', line.gisid);
                                feature.set('road_name', line.road_name);
                                feature.set('type', 'road');
                                lineSourceObj.addFeature(feature);
                            }
                        } catch(e) {
                            console.error('Line parse error:', e);
                        }
                    }
                });

                const lineLayerObj = new ol.layer.Vector({
                    source: lineSourceObj,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#FFB1B1',
                            width: 3,
                            lineDash: [8, 6]
                        })
                    }),
                    visible: true
                });

                // Point layer
                const pointSourceObj = new ol.source.Vector();
                pointsData.forEach(point => {
                    if (point.geojson) {
                        try {
                            const geojson = JSON.parse(point.geojson);
                            const format = new ol.format.GeoJSON();
                            const feature = format.readFeature(geojson, {
                                dataProjection: 'EPSG:4326',
                                featureProjection: 'EPSG:3857'
                            });
                            if (feature) {
                                feature.set('id', point.id);
                                feature.set('gisid', point.gisid);
                                feature.set('type', 'point');
                                pointSourceObj.addFeature(feature);
                            }
                        } catch(e) {
                            console.error('Point parse error:', e);
                        }
                    }
                });

                const pointLayerObj = new ol.layer.Vector({
                    source: pointSourceObj,
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 6,
                            fill: new ol.style.Fill({ color: '#FFB1B1' }),
                            stroke: new ol.style.Stroke({ color: '#1679AB', width: 2 })
                        })
                    }),
                    visible: true
                });

                // Highlight layer
                const highlightSourceObj = new ol.source.Vector();
                const highlightLayerObj = new ol.layer.Vector({
                    source: highlightSourceObj,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#FF0000', width: 4 }),
                        fill: new ol.style.Fill({ color: 'rgba(255, 0, 0, 0.2)' }),
                        image: new ol.style.Circle({
                            radius: 10,
                            fill: new ol.style.Fill({ color: '#FF0000' }),
                            stroke: new ol.style.Stroke({ color: '#FFFFFF', width: 2 })
                        })
                    })
                });

                // Create map
                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayerObj, terrainLayerObj, satelliteLayerObj, droneLayerObj, boundaryLayerObj, polygonLayerObj, lineLayerObj, pointLayerObj, highlightLayerObj],
                    view: new ol.View({
                        center: ol.proj.fromLonLat(center),
                        zoom: 17,
                        projection: 'EPSG:3857'
                    })
                });

                console.log('Map created successfully');

                // Fit to boundary if available
                if (boundaryCoordinates && boundaryCoordinates.length > 0) {
                    const extent = ol.extent.boundingExtent(
                        boundaryCoordinates.map(coord => ol.proj.fromLonLat(coord))
                    );
                    map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 1000 });
                }

                // Layer switcher event handlers
                setupLayerSwitchers(osmLayerObj, terrainLayerObj, satelliteLayerObj, droneLayerObj, boundaryLayerObj, polygonLayerObj, lineLayerObj, pointLayerObj);

                // Map click handler
                map.on('click', function(evt) {
                    const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                        return feature;
                    });

                    if (feature && feature.get('type')) {
                        showPopup(feature, evt.coordinate);
                    } else {
                        hidePopup();
                    }
                });

                // Zoom controls
                setupZoomControls(map);

                // Hide loading spinner
                $('#loadingSpinner').hide();

            } catch(error) {
                console.error('Map initialization error:', error);
                $('#loadingSpinner').hide();
                $('#map').html('<div class="map-error"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p>Error loading map: ' + error.message + '</p></div>');
            }
        }

        // Setup layer switchers
        function setupLayerSwitchers(osm, terrain, satellite, drone, boundary, polygon, line, point) {
            $('#osmLayer').on('change', function() {
                osm.setVisible(true);
                terrain.setVisible(false);
                satellite.setVisible(false);
            });

            $('#terrainLayer').on('change', function() {
                osm.setVisible(false);
                terrain.setVisible(true);
                satellite.setVisible(false);
            });

            $('#satelliteLayer').on('change', function() {
                osm.setVisible(false);
                terrain.setVisible(false);
                satellite.setVisible(true);
            });

            $('#droneLayerCheck').on('change', function(e) {
                drone.setVisible(e.target.checked);
            });

            $('#boundaryLayerCheck').on('change', function(e) {
                boundary.setVisible(e.target.checked);
            });

            $('#polygonLayerCheck').on('change', function(e) {
                polygon.setVisible(e.target.checked);
            });

            $('#lineLayerCheck').on('change', function(e) {
                line.setVisible(e.target.checked);
            });

            $('#pointLayerCheck').on('change', function(e) {
                point.setVisible(e.target.checked);
            });
        }

        // Setup zoom controls
        function setupZoomControls(mapObj) {
            $('#zoomInBtn').on('click', function() {
                const view = mapObj.getView();
                view.setZoom(view.getZoom() + 1);
            });

            $('#zoomOutBtn').on('click', function() {
                const view = mapObj.getView();
                view.setZoom(view.getZoom() - 1);
            });

            $('#fitViewBtn').on('click', function() {
                if (buildingFeatures.length > 0) {
                    const source = buildingFeatures[0].getGeometry().getSource ?
                        buildingFeatures[0].getGeometry().getSource() : null;
                    if (source) {
                        const extent = source.getExtent();
                        if (extent && !ol.extent.isEmpty(extent)) {
                            mapObj.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });
                        }
                    }
                } else if (boundaryCoordinates && boundaryCoordinates.length > 0) {
                    const extent = ol.extent.boundingExtent(
                        boundaryCoordinates.map(coord => ol.proj.fromLonLat(coord))
                    );
                    mapObj.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });
                }
            });
        }

        // Show popup
        function showPopup(feature, coordinate) {
            if (currentPopup) {
                map.removeOverlay(currentPopup);
            }

            const gisid = feature.get('gisid');
            const type = feature.get('type');
            let content = '';

            if (type === 'building') {
                const buildingInfo = buildingData.find(b => b.gisid == gisid);
                content = `
                    <div style="min-width: 260px;">
                        <h6 style="color:#102C57; margin-bottom:10px;"><i class="fas fa-building"></i> Building Details</h6>
                        <hr style="margin:5px 0">
                        <div><strong>GIS ID:</strong> ${gisid || 'N/A'}</div>
                        <div><strong>Building Type:</strong> ${buildingInfo?.building_type || 'N/A'}</div>
                        <div><strong>Construction Type:</strong> ${buildingInfo?.construction_type || 'N/A'}</div>
                        <div><strong>Usage:</strong> ${buildingInfo?.building_usage || 'N/A'}</div>
                        <div><strong>Owner:</strong> ${buildingInfo?.owner_name || 'N/A'}</div>
                        <div><strong>Area:</strong> ${buildingInfo?.plot_area || 'N/A'} sq ft</div>
                        <button class="btn btn-sm btn-primary mt-2 w-100" onclick="viewBuildingDetails('${gisid}')">
                            <i class="fas fa-info-circle"></i> View Full Details
                        </button>
                    </div>
                `;
            } else if (type === 'road') {
                content = `
                    <div style="min-width: 200px;">
                        <h6 style="color:#102C57;"><i class="fas fa-road"></i> Road Details</h6>
                        <hr>
                        <div><strong>GIS ID:</strong> ${gisid || 'N/A'}</div>
                        <div><strong>Road Name:</strong> ${feature.get('road_name') || 'N/A'}</div>
                    </div>
                `;
            } else if (type === 'point') {
                content = `
                    <div style="min-width: 220px;">
                        <h6 style="color:#102C57;"><i class="fas fa-map-pin"></i> Point Feature</h6>
                        <hr>
                        <div><strong>GIS ID:</strong> ${gisid || 'N/A'}</div>
                    </div>
                `;
            }

            const popupElement = document.createElement('div');
            popupElement.className = 'ol-popup';
            popupElement.innerHTML = content;

            const popup = new ol.Overlay({
                element: popupElement,
                positioning: 'bottom-center',
                stopEvent: true,
                offset: [0, -10]
            });

            map.addOverlay(popup);
            popup.setPosition(coordinate);
            currentPopup = popup;
        }

        function hidePopup() {
            if (currentPopup) {
                map.removeOverlay(currentPopup);
                currentPopup = null;
            }
        }

        // Zoom to building by GIS ID
        function zoomToBuilding(gisid) {
            const feature = buildingFeatures.find(f => f.get('gisid') == gisid);
            if (feature && map) {
                const geometry = feature.getGeometry();
                const extent = geometry.getExtent();
                map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });

                const center = ol.extent.getCenter(extent);
                showPopup(feature, center);

                showFlashMessage('Zoomed to GIS ID: ' + gisid, 'success');
            } else {
                showFlashMessage('Building with GIS ID ' + gisid + ' not found', 'warning');
            }
        }

        // Search function
        function searchBuilding(searchTerm, searchType) {
            if (!searchTerm || searchTerm.trim() === '') {
                showFlashMessage('Please enter a search term', 'warning');
                return;
            }

            $('#loadingSpinner').show();
            $('#searchResults').hide().empty();

            searchTerm = searchTerm.trim();
            let foundResults = [];

            // Search in building data by GIS ID
            if (searchType === 'gisid' || searchType === 'both') {
                const buildingMatch = buildingData.filter(b =>
                    b.gisid && b.gisid.toString().toLowerCase().includes(searchTerm.toLowerCase())
                );
                buildingMatch.forEach(b => {
                    foundResults.push({
                        type: 'gisid',
                        value: b.gisid,
                        label: `GIS ID: ${b.gisid}`,
                        data: b
                    });
                });
            }

            // Search in MIS data by Assessment
            if (searchType === 'assessment' || searchType === 'both') {
                const misMatch = misData.filter(m =>
                    m.assessment && m.assessment.toString().toLowerCase().includes(searchTerm.toLowerCase())
                );
                misMatch.forEach(m => {
                    foundResults.push({
                        type: 'assessment',
                        value: m.assessment,
                        label: `Assessment: ${m.assessment}`,
                        data: m
                    });
                });
            }

            $('#loadingSpinner').hide();

            if (foundResults.length === 0) {
                showFlashMessage('No results found for: ' + searchTerm, 'info');
                return;
            }

            // Display results
            const resultsContainer = $('#searchResults');
            resultsContainer.empty();

            foundResults.slice(0, 10).forEach(result => {
                const resultItem = $(`
                    <div class="search-result-item">
                        <div class="result-gisid">${result.label}</div>
                        <div class="result-assessment">${result.type === 'assessment' ? 'Click to view building' : 'Click to zoom to building'}</div>
                    </div>
                `);

                resultItem.on('click', function() {
                    if (result.type === 'assessment') {
                        const building = buildingData.find(b => b.assessment == result.value);
                        if (building && building.gisid) {
                            zoomToBuilding(building.gisid);
                        } else {
                            showFlashMessage('No GIS ID found for this assessment', 'warning');
                        }
                    } else {
                        zoomToBuilding(result.value);
                    }
                    resultsContainer.hide();
                });

                resultsContainer.append(resultItem);
            });

            resultsContainer.show();
        }

        // Search button handler
        $('#searchBtn').on('click', function() {
            const searchTerm = $('#searchInput').val();
            const searchType = $('input[name="searchType"]:checked').val();
            searchBuilding(searchTerm, searchType);
        });

        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                const searchTerm = $(this).val();
                const searchType = $('input[name="searchType"]:checked').val();
                searchBuilding(searchTerm, searchType);
            }
        });

        // Feature info panel close
        $('#closeFeatureInfo').on('click', function() {
            $('#featureInfo').hide();
        });

        // Initialize charts
        function initCharts() {
            // Building Type Chart
            if (Object.keys(buildingTypes).length > 0) {
                const ctx1 = document.getElementById('buildingTypeChart')?.getContext('2d');
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(buildingTypes),
                            datasets: [{
                                data: Object.values(buildingTypes),
                                backgroundColor: ['#1679AB', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545', '#6c757d']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            }

            // Construction Type Chart
            if (Object.keys(constructionTypes).length > 0) {
                const ctx2 = document.getElementById('constructionTypeChart')?.getContext('2d');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(constructionTypes),
                            datasets: [{
                                label: 'Count',
                                data: Object.values(constructionTypes),
                                backgroundColor: '#1679AB'
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: true }
                    });
                }
            }

            // Usage Type Chart
            if (Object.keys(usageTypes).length > 0) {
                const ctx3 = document.getElementById('usageTypeChart')?.getContext('2d');
                if (ctx3) {
                    new Chart(ctx3, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(usageTypes),
                            datasets: [{
                                data: Object.values(usageTypes),
                                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            }

            // Area Variation Chart
            if (Object.keys(areaVariations).length > 0) {
                const ctx4 = document.getElementById('areaVariationChart')?.getContext('2d');
                if (ctx4) {
                    new Chart(ctx4, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(areaVariations),
                            datasets: [{
                                label: 'Number of Buildings',
                                data: Object.values(areaVariations),
                                backgroundColor: '#FFB1B1',
                                borderColor: '#1679AB',
                                borderWidth: 1
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: true }
                    });
                }
            }
        }

        // Flash message function
        function showFlashMessage(message, type = 'info') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            }[type] || 'alert-info';

            const flashHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 80px; right: 20px; z-index: 10000; min-width: 250px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(flashHtml);
            setTimeout(() => $('.alert').alert('close'), 4000);
        }

        // Global function for building details
        window.viewBuildingDetails = function(gisid) {
            const building = buildingData.find(b => b.gisid == gisid);
            if (building) {
                let details = `
                    <div class="feature-detail-row"><span class="feature-detail-label">GIS ID:</span> ${building.gisid || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Building Type:</span> ${building.building_type || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Construction Type:</span> ${building.construction_type || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Building Usage:</span> ${building.building_usage || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Number of Floors:</span> ${building.number_floor || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Number of Shops:</span> ${building.number_shop || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Number of Bills:</span> ${building.number_bill || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Owner Name:</span> ${building.owner_name || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Phone:</span> ${building.phone || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Plot Area:</span> ${building.plot_area || 'N/A'} sq ft</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Road Name:</span> ${building.road_name || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Water Connection:</span> ${building.water_connection || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">UGD:</span> ${building.ugd || 'N/A'}</div>
                    <div class="feature-detail-row"><span class="feature-detail-label">Remarks:</span> ${building.remarks || 'N/A'}</div>
                `;
                $('#featureDetails').html(details);
                $('#featureInfo').show();
                hidePopup();
            } else {
                showFlashMessage('Building details not found', 'error');
            }
        };

        // Start map initialization
        waitForOpenLayers();
        setTimeout(initCharts, 1000);

        // Add statistics button to sidebar
        if ($('#statsNavLink').length === 0) {
            $('.sidebar .nav').append(`
                <a class="nav-link" href="#" id="statsNavLink">
                    <i class="fas fa-chart-pie"></i> Statistics
                </a>
            `);
        }

        $('#statsNavLink').off('click').on('click', function(e) {
            e.preventDefault();
            $('#statisticsModal').modal('show');
        });

        console.log('Ward Map View Loaded Successfully');
    });
</script>
@endsection
