@extends('layouts.commissioner')

@section('title', 'Ward Map')

@section('content')

<div class="container-fluid p-0">
    <div id="map"></div>
</div>

@endsection

@push('styles')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

<style>
    #map {
        width: 100%;
        height: 100vh;
    }

    /* Simple Legend */
    .map-legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-size: 12px;
        font-family: Arial, sans-serif;
        pointer-events: none;
    }

    .map-legend h5 {
        margin: 0 0 8px 0;
        font-size: 13px;
        font-weight: 600;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border-radius: 3px;
    }

    .legend-color.building {
        background: rgba(255, 0, 0, 0.2);
        border: 2px solid red;
    }

    .legend-color.road {
        background: none;
        border: 2px solid green;
    }

    .legend-color.point {
        background: blue;
        border-radius: 50%;
    }

    .legend-color.drone {
        background: rgba(128, 128, 128, 0.5);
        border: 1px solid #666;
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

    /* Layer Toggle */
    .layer-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-size: 12px;
    }

    .layer-toggle label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        margin: 5px 0;
    }

    .layer-toggle input {
        cursor: pointer;
    }
</style>

@endpush

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

<script>

    let map;
    let polygonLayer;
    let pointLayer;
    let lineLayer;
    let imageLayer;
    let boundaryLayer;

    // =========================
    // MAP INIT
    // =========================

    function initMap() {

        // =========================
        // OSM LAYER
        // =========================

        const osmLayer = new ol.layer.Tile({
            source: new ol.source.OSM()
        });

        // =========================
        // IMAGE LAYER (DRONE)
        // =========================

        let droneImage = @json($ward->drone_image ?? null);
        let extentLeft   = @json($ward->extent_left ?? null);
        let extentBottom = @json($ward->extent_bottom ?? null);
        let extentRight  = @json($ward->extent_right ?? null);
        let extentTop    = @json($ward->extent_top ?? null);

        // Build image URL properly
        let imageUrl = null;
        if (droneImage) {
            // Remove leading slashes if any
            let cleanPath = droneImage.replace(/^\/+/, '');
            imageUrl = "{{ asset('') }}" + cleanPath;
        }

        // Default Empty Layer
        imageLayer = new ol.layer.Image({
            visible: true,
            opacity: 0.7
        });

        // If image exists with valid extent
        if (
            imageUrl &&
            extentLeft !== null &&
            extentBottom !== null &&
            extentRight !== null &&
            extentTop !== null
        ) {
            try {
                imageLayer = new ol.layer.Image({
                    source: new ol.source.ImageStatic({
                        url: imageUrl,
                        imageExtent: [
                            parseFloat(extentLeft),
                            parseFloat(extentBottom),
                            parseFloat(extentRight),
                            parseFloat(extentTop)
                        ],
                        projection: 'EPSG:3857'
                    }),
                    opacity: 0.7,
                    visible: true
                });
                console.log('Drone image loaded:', imageUrl);
            } catch(e) {
                console.error('Error loading drone image:', e);
            }
        } else {
            console.log('No drone image or extent available');
        }

        // =========================
        // BOUNDARY LAYER (Optional)
        // =========================

        let boundary = @json($ward->boundary ?? null);

        if (boundary && boundary.length > 0 && boundary[0] && boundary[0].length) {
            try {
                const boundaryCoords = boundary[0].map(coord => ol.proj.fromLonLat(coord));
                const boundaryFeature = new ol.Feature({
                    geometry: new ol.geom.Polygon([boundaryCoords])
                });

                const boundarySource = new ol.source.Vector({
                    features: [boundaryFeature]
                });

                boundaryLayer = new ol.layer.Vector({
                    source: boundarySource,
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
            } catch(e) {
                console.error('Error creating boundary:', e);
            }
        }

        // =========================
        // CREATE MAP
        // =========================

        // Calculate center from boundary if available
        let center = ol.proj.fromLonLat([80.2707, 13.0827]);
        let zoom = 16;

        if (boundary && boundary[0] && boundary[0].length) {
            try {
                const lons = boundary[0].map(p => p[0]);
                const lats = boundary[0].map(p => p[1]);
                center = ol.proj.fromLonLat([
                    (Math.min(...lons) + Math.max(...lons)) / 2,
                    (Math.min(...lats) + Math.max(...lats)) / 2
                ]);
                zoom = 17;
            } catch(e) {}
        }

        // Build layers array
        let layers = [osmLayer];

        if (imageLayer) layers.push(imageLayer);
        if (boundaryLayer) layers.push(boundaryLayer);

        map = new ol.Map({
            target: 'map',
            layers: layers,
            view: new ol.View({
                center: center,
                zoom: zoom
            })
        });

        // =========================
        // ADD LEGEND TO DOM
        // =========================

        addLegend();
        addZoomControls();
        addLayerControls();

        // =========================
        // LOAD VECTOR LAYERS
        // =========================

        refreshLayers();

    }

    // =========================
    // ADD LEGEND
    // =========================

    function addLegend() {
        const legend = document.createElement('div');
        legend.className = 'map-legend';
        legend.innerHTML = `
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
                <span>Points of Interest</span>
            </div>
            ${imageLayer && imageLayer.get('visible') !== false ? `
            <div class="legend-item">
                <div class="legend-color drone"></div>
                <span>Drone Image</span>
            </div>
            ` : ''}
        `;
        document.body.appendChild(legend);
    }

    // =========================
    // ADD ZOOM CONTROLS
    // =========================

    function addZoomControls() {
        const controls = document.createElement('div');
        controls.className = 'zoom-controls';
        controls.innerHTML = `
            <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
            <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
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

    // =========================
    // ADD LAYER CONTROLS
    // =========================

    function addLayerControls() {
        const controls = document.createElement('div');
        controls.className = 'layer-toggle';
        controls.innerHTML = `
            <label>
                <input type="checkbox" id="toggleBuildings" checked>
                <i class="fas fa-building"></i> Buildings
            </label>
            <label>
                <input type="checkbox" id="toggleRoads" checked>
                <i class="fas fa-road"></i> Roads
            </label>
            <label>
                <input type="checkbox" id="togglePoints" checked>
                <i class="fas fa-map-marker-alt"></i> Points
            </label>
            ${imageLayer && imageLayer.get('visible') !== false ? `
            <label>
                <input type="checkbox" id="toggleDrone" checked>
                <i class="fas fa-drone"></i> Drone Image
            </label>
            ` : ''}
        `;
        document.body.appendChild(controls);

        document.getElementById('toggleBuildings').addEventListener('change', (e) => {
            if (polygonLayer) polygonLayer.setVisible(e.target.checked);
        });

        document.getElementById('toggleRoads').addEventListener('change', (e) => {
            if (lineLayer) lineLayer.setVisible(e.target.checked);
        });

        document.getElementById('togglePoints').addEventListener('change', (e) => {
            if (pointLayer) pointLayer.setVisible(e.target.checked);
        });

        if (document.getElementById('toggleDrone')) {
            document.getElementById('toggleDrone').addEventListener('change', (e) => {
                if (imageLayer) imageLayer.setVisible(e.target.checked);
            });
        }
    }

    // =========================
    // REFRESH LAYERS
    // =========================

    function refreshLayers() {

        // Remove old layers
        if (polygonLayer) map.removeLayer(polygonLayer);
        if (pointLayer) map.removeLayer(pointLayer);
        if (lineLayer) map.removeLayer(lineLayer);

        // =========================
        // GET DATA
        // =========================

        let polygons = @json($polygons ?? []);
        let points   = @json($points ?? []);
        let lines    = @json($lines ?? []);

        console.log(`Loading: ${polygons.length} polygons, ${points.length} points, ${lines.length} lines`);

        // =========================
        // POLYGON SOURCE
        // =========================

        const polygonSource = new ol.source.Vector();

        polygons.forEach(function(poly) {
            try {
                let coords = typeof poly.coordinates === 'string'
                    ? JSON.parse(poly.coordinates)
                    : poly.coordinates;

                if (coords && coords.length) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid,
                        sqfeet: poly.sqfeet
                    });
                    polygonSource.addFeature(feature);
                }
            } catch(e) {
                console.log('Error parsing polygon:', e);
            }
        });

        // =========================
        // POLYGON LAYER
        // =========================

        polygonLayer = new ol.layer.Vector({
            source: polygonSource,
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: '#ff4444',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(255, 68, 68, 0.15)'
                })
            }),
            visible: true
        });

        // =========================
        // POINT SOURCE
        // =========================

        const pointSource = new ol.source.Vector();

        points.forEach(function(point) {
            try {
                let coords = typeof point.coordinates === 'string'
                    ? JSON.parse(point.coordinates)
                    : point.coordinates;

                if (coords && coords.length === 2) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                        gisid: point.gisid
                    });
                    pointSource.addFeature(feature);
                }
            } catch(e) {
                console.log('Error parsing point:', e);
            }
        });

        // =========================
        // POINT LAYER
        // =========================

        pointLayer = new ol.layer.Vector({
            source: pointSource,
            style: new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 6,
                    fill: new ol.style.Fill({
                        color: '#1679AB'
                    }),
                    stroke: new ol.style.Stroke({
                        color: '#ffffff',
                        width: 2
                    })
                })
            }),
            visible: true
        });

        // =========================
        // LINE SOURCE
        // =========================

        const lineSource = new ol.source.Vector();

        lines.forEach(function(line) {
            try {
                let coords = typeof line.coordinates === 'string'
                    ? JSON.parse(line.coordinates)
                    : line.coordinates;

                if (coords && coords.length) {
                    // Multi line support
                    if (coords.length === 1 && Array.isArray(coords[0][0])) {
                        coords = coords[0];
                    }

                    const feature = new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: line.gisid
                    });
                    lineSource.addFeature(feature);
                }
            } catch(e) {
                console.log('Error parsing line:', e);
            }
        });

        // =========================
        // LINE LAYER
        // =========================

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

        // =========================
        // ADD LAYERS TO MAP
        // =========================

        map.addLayer(polygonLayer);
        map.addLayer(lineLayer);
        map.addLayer(pointLayer);

        // =========================
        // FIT TO POLYGONS
        // =========================

        if (polygonSource.getFeatures().length > 0) {
            try {
                const extent = polygonSource.getExtent();
                if (extent && !isNaN(extent[0]) && !isNaN(extent[1])) {
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            } catch(e) {
                console.log('Error fitting to polygons:', e);
            }
        }

        console.log('Layers Refreshed Successfully');

    }

    // =========================
    // DOCUMENT READY
    // =========================

    document.addEventListener("DOMContentLoaded", function () {
        initMap();
    });

</script>

@endpush
