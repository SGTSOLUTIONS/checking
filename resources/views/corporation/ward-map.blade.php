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
        height: 100vh;
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100%;
        background: #e8e8e8;
    }

    /* Simple Legend */
    .simple-legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-size: 12px;
        min-width: 150px;
        backdrop-filter: blur(5px);
    }

    .simple-legend h5 {
        margin: 0 0 8px 0;
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 8px;
    }

    .legend-color.building {
        background: rgba(33, 150, 243, 0.5);
        border: 2px solid #2196F3;
    }

    .legend-color.road {
        background: none;
        border: 2px solid #ffc107;
    }

    .legend-color.point {
        background: #1679AB;
        border-radius: 50%;
    }

    /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        z-index: 1000;
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
        background: #1679AB;
        color: white;
    }

    .zoom-btn:first-child {
        border-bottom: 1px solid #ddd;
    }

    /* Loading */
    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        padding: 20px 30px;
        border-radius: 10px;
        z-index: 2000;
        display: none;
        color: white;
        text-align: center;
    }

    /* Info Popup */
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        padding: 12px;
        min-width: 200px;
        max-width: 300px;
        font-size: 12px;
        z-index: 1100;
        pointer-events: none;
    }

    .ol-popup:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 10px 10px 0;
        border-style: solid;
        border-color: white transparent transparent;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="map-wrapper">
        <div id="map"></div>

        <!-- Simple Legend -->
        <div class="simple-legend">
            <h5><i class="fas fa-layer-group"></i> Legend</h5>
            <div class="legend-item">
                <div class="legend-color building"></div>
                <span>Buildings</span>
            </div>
            <div class="legend-item">
                <div class="legend-color road"></div>
                <span>Roads</span>
            </div>
            <div class="legend-item">
                <div class="legend-color point"></div>
                <span>Assessment Points</span>
            </div>
        </div>

        <!-- Zoom Controls -->
        <div class="zoom-controls">
            <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
            <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-white mb-2"></div>
            <div>Loading map...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
<script>
(function() {
    let map = null;
    let popupElement = null;
    let popupOverlay = null;

    // Data from server
    let polygons = @json($polygons ?? []);
    let lines = @json($lines ?? []);
    let points = @json($points ?? []);
    let pointDatas = @json($pointDatas ?? []);
    let polygonDatas = @json($polygonDatas ?? []);
    let ward = @json($ward ?? []);

    // Helper function to get building color by usage
    function getBuildingColor(usage) {
        if (!usage) return '#D4A13E';
        const colors = {
            'RESIDENTIAL': '#4CAF50',
            'COMMERCIAL': '#2196F3',
            'INDUSTRIAL': '#FF9800',
            'MIXED': '#9C27B0',
            'INSTITUTIONAL': '#00BCD4',
            'GOVERNMENT': '#F44336'
        };
        return colors[usage.toUpperCase()] || '#D4A13E';
    }

    // Polygon style
    function getPolygonStyle(feature) {
        const gisid = feature.get('gisid');
        const polygonData = polygonDatas.find(d => d.gisid == gisid);
        const color = getBuildingColor(polygonData?.building_usage);

        return new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: color,
                width: 2
            }),
            fill: new ol.style.Fill({
                color: color + '33'
            })
        });
    }

    // Point style
    function getPointStyle() {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 6,
                fill: new ol.style.Fill({
                    color: '#1679AB'
                }),
                stroke: new ol.style.Stroke({
                    color: '#fff',
                    width: 2
                })
            })
        });
    }

    // Line/Road style
    function getLineStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: '#ffc107',
                width: 2
            })
        });
    }

    // Create polygon layer
    function createPolygonLayer(data) {
        const source = new ol.source.Vector();

        data.forEach(poly => {
            try {
                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                if (coords && coords.length) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid,
                        sqfeet: poly.sqfeet || '0'
                    });
                    source.addFeature(feature);
                }
            } catch(e) {
                console.error('Error parsing polygon:', e);
            }
        });

        return new ol.layer.Vector({
            source: source,
            style: getPolygonStyle,
            visible: true
        });
    }

    // Create line layer
    function createLineLayer(data) {
        const source = new ol.source.Vector();

        data.forEach(line => {
            try {
                let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line.coordinates;
                if (coords && coords.length >= 2) {
                    if (coords.length === 1 && Array.isArray(coords[0][0])) {
                        coords = coords[0];
                    }
                    const feature = new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: line.gisid
                    });
                    source.addFeature(feature);
                }
            } catch(e) {
                console.error('Error parsing line:', e);
            }
        });

        return new ol.layer.Vector({
            source: source,
            style: getLineStyle,
            visible: true
        });
    }

    // Create point layer
    function createPointLayer(data) {
        const source = new ol.source.Vector();

        data.forEach(point => {
            try {
                let coords = typeof point.coordinates === 'string' ? JSON.parse(point.coordinates) : point.coordinates;
                if (coords && coords.length === 2) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                        gisid: point.gisid
                    });
                    feature.set('gisid', point.gisid);
                    source.addFeature(feature);
                }
            } catch(e) {
                console.error('Error parsing point:', e);
            }
        });

        return new ol.layer.Vector({
            source: source,
            style: getPointStyle,
            visible: true
        });
    }

    // Create boundary layer
    function createBoundaryLayer() {
        const source = new ol.source.Vector();

        if (ward?.boundary && ward.boundary[0] && ward.boundary[0].length) {
            try {
                const boundary = ward.boundary[0].map(pt => ol.proj.fromLonLat(pt));
                const feature = new ol.Feature({
                    geometry: new ol.geom.Polygon([boundary])
                });
                source.addFeature(feature);

                return new ol.layer.Vector({
                    source: source,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff0000',
                            width: 2,
                            lineDash: [5, 5]
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,0,0,0.05)'
                        })
                    }),
                    visible: true
                });
            } catch(e) {
                console.error('Error creating boundary:', e);
            }
        }

        return new ol.layer.Vector({
            source: source,
            visible: false
        });
    }

    // Create popup overlay
    function createPopup() {
        popupElement = document.createElement('div');
        popupElement.className = 'ol-popup';
        popupElement.style.display = 'none';
        document.body.appendChild(popupElement);

        popupOverlay = new ol.Overlay({
            element: popupElement,
            positioning: 'bottom-center',
            stopEvent: false,
            offset: [0, -10]
        });

        return popupOverlay;
    }

    // Show popup with building info
    function showPopup(feature, coordinate) {
        const gisid = feature.get('gisid');
        const polygonData = polygonDatas.find(d => d.gisid == gisid);
        const assessments = pointDatas.filter(pd => pd.point_gisid == gisid);

        let html = `<strong>GIS ID: ${gisid}</strong><br>`;

        if (polygonData) {
            html += `
                <strong>Building Usage:</strong> ${polygonData.building_usage || 'N/A'}<br>
                <strong>Floors:</strong> ${polygonData.number_floor || 'N/A'}<br>
                <strong>Total Area:</strong> ${polygonData.sqfeet || 'N/A'} sqft<br>
                <strong>Assessments:</strong> ${assessments.length}
            `;
        } else {
            html += `<em>No building data available</em>`;
        }

        popupElement.innerHTML = html;
        popupElement.style.display = 'block';
        popupOverlay.setPosition(coordinate);
    }

    // Initialize map
    function initMap() {
        $('#loadingSpinner').fadeIn();

        // Create base layer (OSM)
        const baseLayer = new ol.layer.Tile({
            source: new ol.source.OSM(),
            visible: true
        });

        // Create data layers
        const polygonLayer = createPolygonLayer(polygons);
        const lineLayer = createLineLayer(lines);
        const pointLayer = createPointLayer(points);
        const boundaryLayer = createBoundaryLayer();

        // Create popup
        const popupOverlay = createPopup();

        // Calculate map center from boundary or default
        let center = ol.proj.fromLonLat([80.2707, 13.0827]);
        let zoom = 16;

        if (ward?.boundary && ward.boundary[0] && ward.boundary[0].length) {
            try {
                const lons = ward.boundary[0].map(p => p[0]);
                const lats = ward.boundary[0].map(p => p[1]);
                center = ol.proj.fromLonLat([
                    (Math.min(...lons) + Math.max(...lons)) / 2,
                    (Math.min(...lats) + Math.max(...lats)) / 2
                ]);
                zoom = 17;
            } catch(e) {}
        }

        // Create map
        map = new ol.Map({
            target: 'map',
            layers: [baseLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer],
            overlays: [popupOverlay],
            view: new ol.View({
                center: center,
                zoom: zoom
            }),
            controls: []
        });

        // Zoom controls
        $('#zoomInBtn').on('click', () => {
            const view = map.getView();
            view.setZoom(view.getZoom() + 1);
        });

        $('#zoomOutBtn').on('click', () => {
            const view = map.getView();
            view.setZoom(view.getZoom() - 1);
        });

        // Hide popup when clicking on map
        map.on('click', function(evt) {
            popupElement.style.display = 'none';
        });

        // Show popup on hover over polygons
        map.on('pointermove', function(evt) {
            const pixel = evt.pixel;
            const feature = map.forEachFeatureAtPixel(pixel, function(feature) {
                return feature;
            });

            if (feature && feature.get('gisid')) {
                const coordinate = evt.coordinate;
                showPopup(feature, coordinate);
                map.getTargetElement().style.cursor = 'pointer';
            } else {
                popupElement.style.display = 'none';
                map.getTargetElement().style.cursor = '';
            }
        });

        // Fit map to show all features
        if (polygons.length > 0) {
            const extent = polygonLayer.getSource().getExtent();
            if (extent && !isNaN(extent[0]) && !isNaN(extent[1])) {
                map.getView().fit(extent, {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });
            }
        }

        $('#loadingSpinner').fadeOut();

        // Log success
        console.log('Map initialized successfully');
        console.log(`Polygons: ${polygons.length}, Lines: ${lines.length}, Points: ${points.length}`);
    }

    // Initialize when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>
@endpush
