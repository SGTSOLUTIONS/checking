@extends('layouts.commissioner')

@section('title', 'Ward Map')

@section('content')

<div class="container-fluid p-0">
    <div id="map"></div>
</div>

@endsection

@push('styles')

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    /* Prevent body scrolling and zoom */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: fixed;
        touch-action: none; /* Prevents browser zoom/pinch on entire page */
    }

    #map {
        width: 100%;
        height: 100vh;
        height: 100dvh; /* Dynamic viewport height for mobile */
        position: relative;
        touch-action: pan-x pan-y pinch-zoom; /* Allows map to handle touch events */
    }

    /* Layer Switcher */
    .layer-switcher {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-size: 12px;
        min-width: 160px;
        backdrop-filter: blur(5px);
        touch-action: auto;
        pointer-events: auto;
    }

    @media (max-width: 768px) {
        .layer-switcher {
            top: 10px;
            right: 10px;
            padding: 10px;
            min-width: 140px;
        }

        .layer-switcher label {
            padding: 8px 0;
            font-size: 13px;
        }

        .layer-switcher input {
            width: 18px;
            height: 18px;
        }

        .map-legend {
            bottom: 70px;
            right: 10px;
            font-size: 10px;
            padding: 8px;
            min-width: 110px;
        }

        .zoom-controls {
            bottom: 20px;
            left: 10px;
        }

        .zoom-btn {
            width: 44px;
            height: 44px;
            font-size: 20px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
        }
    }

    .layer-switcher h5 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #ddd;
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
        color: #555;
        touch-action: manipulation;
    }

    .layer-group input {
        cursor: pointer;
        margin: 0;
    }

    .layer-group .group-title {
        font-weight: 600;
        color: #1679AB;
        margin-bottom: 5px;
        font-size: 11px;
        text-transform: uppercase;
    }

    /* Legend */
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
        min-width: 140px;
        backdrop-filter: blur(5px);
        pointer-events: none;
    }

    .map-legend h5 {
        margin: 0 0 8px 0;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border-radius: 3px;
    }

    .legend-color.building {
        background: rgba(255, 68, 68, 0.3);
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
        touch-action: manipulation;
    }

    .zoom-btn:active {
        background: #1679AB;
        color: white;
        transform: scale(0.95);
    }

    .zoom-btn:first-child {
        border-bottom: 1px solid #ddd;
    }

    /* Ensure OpenLayers map captures touch events properly */
    .ol-viewport {
        touch-action: pan-x pan-y pinch-zoom;
    }

    .ol-viewport canvas {
        touch-action: pan-x pan-y pinch-zoom;
    }

    /* Loading message */
    .map-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        z-index: 2000;
        font-size: 14px;
        pointer-events: none;
    }
</style>

@endpush

@push('scripts')

<script>
    // Prevent default touch zoom on entire page
    (function() {
        // Prevent touchmove on non-map elements
        document.addEventListener('touchmove', function(e) {
            // Allow touchmove only on map and controls
            const isMap = e.target.closest('#map');
            const isControl = e.target.closest('.layer-switcher') || e.target.closest('.zoom-controls');

            if (!isMap && !isControl) {
                e.preventDefault();
            }
        }, { passive: false });

        // Prevent pinch zoom on non-map elements
        document.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) {
                // Allow pinch only on map
                if (!e.target.closest('#map')) {
                    e.preventDefault();
                }
            }
        }, { passive: false });

        // Prevent double-tap zoom on entire page
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                if (!e.target.closest('#map')) {
                    e.preventDefault();
                }
            }
            lastTouchEnd = now;
        }, false);
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

<script>
    let map;
    let polygonLayer;
    let lineLayer;
    let imageLayer;
    let boundaryLayer;
    let osmLayer;
    let satelliteLayer;
    let isLoading = false;

    // =========================
    // MAP INIT
    // =========================

    function initMap() {
        showLoading(true);

        // =========================
        // BASE LAYERS
        // =========================

        // OSM Layer
        osmLayer = new ol.layer.Tile({
            source: new ol.source.OSM(),
            visible: true
        });

        // Satellite Layer (ArcGIS Imagery)
        satelliteLayer = new ol.layer.Tile({
            source: new ol.source.XYZ({
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                attributions: 'Tiles &copy; Esri'
            }),
            visible: false
        });

        // =========================
        // DRONE IMAGE LAYER
        // =========================

        let droneImage = @json($ward->drone_image ?? null);
        let extentLeft   = @json($ward->extent_left ?? null);
        let extentBottom = @json($ward->extent_bottom ?? null);
        let extentRight  = @json($ward->extent_right ?? null);
        let extentTop    = @json($ward->extent_top ?? null);

        let imageUrl = null;
        if (droneImage) {
            let cleanPath = droneImage.replace(/^\/+/, '');
            imageUrl = "{{ asset('') }}" + cleanPath;
        }

        imageLayer = new ol.layer.Image({
            visible: false,
            opacity: 0.7
        });

        if (imageUrl && extentLeft !== null && extentBottom !== null &&
            extentRight !== null && extentTop !== null) {
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
                    visible: false
                });
                console.log('Drone image loaded');
            } catch(e) {
                console.error('Error loading drone image:', e);
            }
        }

        // =========================
        // BOUNDARY LAYER
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
                            color: 'rgba(255, 0, 0, 0)'
                        })
                    }),
                    visible: true
                });
            } catch(e) {
                console.error('Error creating boundary:', e);
            }
        }

        // =========================
        // CALCULATE MAP CENTER
        // =========================

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

        // =========================
        // CREATE MAP with touch interactions
        // =========================

        map = new ol.Map({
            target: 'map',
            layers: [osmLayer, satelliteLayer],
            view: new ol.View({
                center: center,
                zoom: zoom,
                smoothExtentConstraint: true,
                smoothResolutionConstraint: true,
                constrainResolution: true
            }),
            interactions: ol.interaction.defaults({
                pinchRotate: false,
                pinchZoom: true,
                doubleClickZoom: true,
                dragPan: true,
                keyboard: false,
                mouseWheelZoom: true
            })
        });

        // Add additional layers after map creation
        if (imageLayer) map.addLayer(imageLayer);
        if (boundaryLayer) map.addLayer(boundaryLayer);

        // =========================
        // ADD UI CONTROLS
        // =========================

        addLayerSwitcher();
        addLegend();
        addZoomControls();

        // =========================
        // LOAD VECTOR LAYERS
        // =========================

        refreshLayers();

        showLoading(false);
    }

    // =========================
    // ADD LAYER SWITCHER
    // =========================

    function addLayerSwitcher() {
        const switcher = document.createElement('div');
        switcher.className = 'layer-switcher';
        switcher.innerHTML = `
            <h5><i class="fas fa-layer-group"></i> Layers</h5>

            <div class="layer-group">
                <div class="group-title">Base Maps</div>
                <label>
                    <input type="radio" name="baseLayer" value="osm" checked>
                    <i class="fas fa-map"></i> OpenStreetMap
                </label>
                <label>
                    <input type="radio" name="baseLayer" value="satellite">
                    <i class="fas fa-satellite"></i> Satellite
                </label>
            </div>

            <div class="layer-group">
                <div class="group-title">Overlays</div>
                <label>
                    <input type="checkbox" id="toggleBuildings" checked>
                    <i class="fas fa-building"></i> Buildings
                </label>
                <label>
                    <input type="checkbox" id="toggleRoads" checked>
                    <i class="fas fa-road"></i> Roads
                </label>
                <label>
                    <input type="checkbox" id="toggleBoundary" checked>
                    <i class="fas fa-draw-polygon"></i> Ward Boundary
                </label>
                ${imageLayer && imageLayer.get('visible') !== undefined ? `
                <label>
                    <input type="checkbox" id="toggleDrone">
                    <i class="fas fa-drone"></i> Drone Image
                </label>
                ` : ''}
            </div>
        `;
        document.body.appendChild(switcher);

        // Base layer switcher
        document.querySelectorAll('input[name="baseLayer"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                osmLayer.setVisible(e.target.value === 'osm');
                satelliteLayer.setVisible(e.target.value === 'satellite');
            });
        });

        // Overlay toggles
        document.getElementById('toggleBuildings').addEventListener('change', (e) => {
            if (polygonLayer) polygonLayer.setVisible(e.target.checked);
        });

        document.getElementById('toggleRoads').addEventListener('change', (e) => {
            if (lineLayer) lineLayer.setVisible(e.target.checked);
        });

        document.getElementById('toggleBoundary').addEventListener('change', (e) => {
            if (boundaryLayer) boundaryLayer.setVisible(e.target.checked);
        });

        if (document.getElementById('toggleDrone')) {
            document.getElementById('toggleDrone').addEventListener('change', (e) => {
                if (imageLayer) imageLayer.setVisible(e.target.checked);
            });
        }
    }

    // =========================
    // ADD LEGEND
    // =========================

    function addLegend() {
        const legend = document.createElement('div');
        legend.className = 'map-legend';
        legend.innerHTML = `
            <h5><i class="fas fa-info-circle"></i> Legend</h5>
            <div class="legend-item">
                <div class="legend-color building"></div>
                <span>Buildings</span>
            </div>
            <div class="legend-item">
                <div class="legend-color road"></div>
                <span>Roads</span>
            </div>
            <div class="legend-item">
                <div class="legend-color boundary"></div>
                <span>Ward Boundary</span>
            </div>
            ${imageLayer ? `
            <div class="legend-item">
                <div class="legend-color drone"></div>
                <span>Drone Imagery</span>
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

        document.getElementById('zoomInBtn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const view = map.getView();
            view.setZoom(view.getZoom() + 1);
        });

        document.getElementById('zoomOutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const view = map.getView();
            view.setZoom(view.getZoom() - 1);
        });

        // Prevent touch events on buttons from bubbling to map
        const buttons = document.querySelectorAll('.zoom-btn');
        buttons.forEach(btn => {
            btn.addEventListener('touchstart', (e) => {
                e.stopPropagation();
            });
            btn.addEventListener('touchend', (e) => {
                e.stopPropagation();
            });
        });
    }

    // =========================
    // SHOW/HIDE LOADING
    // =========================

    function showLoading(show) {
        isLoading = show;
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
        } else {
            if (loadingEl) {
                loadingEl.style.display = 'none';
            }
        }
    }

    // =========================
    // REFRESH LAYERS
    // =========================

    function refreshLayers() {

        // Remove old layers
        if (polygonLayer) map.removeLayer(polygonLayer);
        if (lineLayer) map.removeLayer(lineLayer);

        // =========================
        // GET DATA
        // =========================

        let polygons = @json($polygons ?? []);
        let lines    = @json($lines ?? []);

        console.log(`Loading: ${polygons.length} polygons, ${lines.length} lines`);

        // =========================
        // POLYGON SOURCE (BUILDINGS)
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
        // LINE SOURCE (ROADS)
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

        // =========================
        // FIT TO POLYGONS (only if no polygons loaded before)
        // =========================

        if (polygonSource.getFeatures().length > 0) {
            try {
                const extent = polygonSource.getExtent();
                if (extent && !isNaN(extent[0]) && !isNaN(extent[1]) &&
                    extent[0] !== Infinity && extent[1] !== -Infinity) {
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
    // HANDLE ORIENTATION CHANGE
    // =========================

    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            if (map) {
                map.updateSize();
            }
        }, 100);
    });

    window.addEventListener('resize', function() {
        if (map) {
            setTimeout(function() {
                map.updateSize();
            }, 100);
        }
    });

    // =========================
    // DOCUMENT READY
    // =========================

    document.addEventListener("DOMContentLoaded", function () {
        initMap();
    });

</script>

@endpush
