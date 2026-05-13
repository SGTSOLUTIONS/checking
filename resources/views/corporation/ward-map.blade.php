@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>
    </div>
@endsection

@push('styles')
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
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
            overflow: hidden;
            position: fixed;
            touch-action: none;
        }

        #map {
            width: 100%;
            height: 100vh;
            height: 100dvh;
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* Layer Switcher */
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
            backdrop-filter: blur(10px);
            touch-action: auto;
            pointer-events: auto;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: white;
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
        }

        .layer-group .group-title {
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 5px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Legend */
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
            backdrop-filter: blur(10px);
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: white;
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
            margin-bottom: 6px;
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

        .legend-color.drone {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid #fff;
        }

        /* Zoom Controls */
        .zoom-controls {
            position: absolute;
            bottom: 20px;
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

        .ol-viewport {
            touch-action: pan-x pan-y pinch-zoom;
        }

        .ol-viewport canvas {
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* Loading indicator */
        .map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 2000;
            font-size: 14px;
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            backdrop-filter: blur(10px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Prevent default touch zoom on entire page
        (function() {
            document.addEventListener('touchmove', function(e) {
                const isMap = e.target.closest('#map');
                const isControl = e.target.closest('.layer-switcher') || e.target.closest('.zoom-controls');
                if (!isMap && !isControl) {
                    e.preventDefault();
                }
            }, {
                passive: false
            });

            document.addEventListener('touchstart', function(e) {
                if (e.touches.length > 1) {
                    if (!e.target.closest('#map')) {
                        e.preventDefault();
                    }
                }
            }, {
                passive: false
            });
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
        let currentBaseLayer = 'osm';

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
            } else {
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            }
        }

        function initMap() {
            showLoading(true);

            // Base Layers
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

            // ========== DRONE IMAGE LAYER ==========
            let droneImage = @json($ward->drone_image ?? null);
            let extentLeft = @json($ward->extent_left ?? null);
            let extentBottom = @json($ward->extent_bottom ?? null);
            let extentRight = @json($ward->extent_right ?? null);
            let extentTop = @json($ward->extent_top ?? null);

            console.log('Drone Image Data:', {
                droneImage: droneImage,
                extentLeft: extentLeft,
                extentBottom: extentBottom,
                extentRight: extentRight,
                extentTop: extentTop
            });

            let imageUrl = null;
            if (droneImage) {
                let cleanPath = droneImage.replace(/^\/+/, '');
                imageUrl = "{{ asset('') }}" + cleanPath;
                console.log('Constructed Image URL:', imageUrl);
            }

            let hasDroneImage = false;

            const hasValidExtent = extentLeft !== null && extentBottom !== null &&
                extentRight !== null && extentTop !== null &&
                !isNaN(parseFloat(extentLeft)) && !isNaN(parseFloat(extentBottom)) &&
                !isNaN(parseFloat(extentRight)) && !isNaN(parseFloat(extentTop));

            if (imageUrl && hasValidExtent) {
                try {
                    const imageExtent = [
                        parseFloat(extentLeft),
                        parseFloat(extentBottom),
                        parseFloat(extentRight),
                        parseFloat(extentTop)
                    ];

                    console.log('Image Extent:', imageExtent);

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
                    console.log('Drone image layer created successfully');

                } catch (e) {
                    console.error('Error creating drone image layer:', e);
                    imageLayer = null;
                }
            } else {
                console.log('Drone image not available or invalid extent');
                imageLayer = null;
            }

            // ========== BOUNDARY LAYER ==========
            let boundary = @json($ward->boundary ?? null);
            let boundaryExtent = null;

            if (boundary && boundary.length > 0 && boundary[0] && boundary[0].length) {
                try {
                    const boundaryCoords = boundary[0].map(coord => ol.proj.fromLonLat(coord));
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
                                color: 'rgba(255, 0, 0, 0.05)'
                            })
                        }),
                        visible: true
                    });

                    const lons = boundary[0].map(p => p[0]);
                    const lats = boundary[0].map(p => p[1]);
                    boundaryExtent = ol.proj.fromLonLat([
                        Math.min(...lons),
                        Math.min(...lats),
                        Math.max(...lons),
                        Math.max(...lats)
                    ]);

                    console.log('Boundary extent calculated:', boundaryExtent);
                } catch (e) {
                    console.error('Error creating boundary:', e);
                }
            }

            // ========== MAP CENTER ==========
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
                    zoom = 16;
                    console.log('Center from boundary:', center);
                } catch (e) {}
            }

            // Create Map
            let layers = [osmLayer, satelliteLayer];

            map = new ol.Map({
                target: 'map',
                layers: layers,
                view: new ol.View({
                    center: center,
                    zoom: zoom
                })
            });

            if (imageLayer) {
                map.addLayer(imageLayer);
                console.log('Drone image layer added to map');
            }

            if (boundaryLayer) {
                map.addLayer(boundaryLayer);
                console.log('Boundary layer added to map');
            }

            // Zoom to boundary
            setTimeout(function() {
                if (boundaryExtent && boundaryExtent.length === 4) {
                    map.getView().fit(boundaryExtent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                    console.log('Zooming to boundary');
                }
            }, 500);

            // Add UI controls
            addLayerSwitcher(hasDroneImage);
            addLegend(hasDroneImage);
            addZoomControls();

            // Load vector layers
            refreshLayers();

            showLoading(false);
        }

        function addLayerSwitcher(hasDroneImage) {
            const switcher = document.createElement('div');
            switcher.className = 'layer-switcher';
            switcher.innerHTML = `
                <h5><i class="fas fa-layer-group"></i> Layers</h5>
                <div class="layer-group">
                    <div class="group-title">Base Maps</div>
                    <label><input type="radio" name="baseLayer" value="osm" ${currentBaseLayer === 'osm' ? 'checked' : ''}> <i class="fas fa-map"></i> OpenStreetMap</label>
                    <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label>
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

            document.getElementById('toggleBuildings').addEventListener('change', (e) => {
                if (polygonLayer) polygonLayer.setVisible(e.target.checked);
            });
            document.getElementById('toggleRoads').addEventListener('change', (e) => {
                if (lineLayer) lineLayer.setVisible(e.target.checked);
            });
            document.getElementById('toggleBoundary').addEventListener('change', (e) => {
                if (boundaryLayer) boundaryLayer.setVisible(e.target.checked);
            });

            const droneToggle = document.getElementById('toggleDrone');
            if (droneToggle && imageLayer) {
                droneToggle.addEventListener('change', (e) => {
                    imageLayer.setVisible(e.target.checked);
                });
            }
        }

        function addLegend(hasDroneImage) {
            const legend = document.createElement('div');
            legend.className = 'map-legend';
            legend.innerHTML = `
                <h5><i class="fas fa-info-circle"></i> Legend</h5>
                <div class="legend-item"><div class="legend-color building"></div><span>Buildings (with labels)</span></div>
                <div class="legend-item"><div class="legend-color road"></div><span>Roads</span></div>
                <div class="legend-item"><div class="legend-color boundary"></div><span>Ward Boundary</span></div>
                ${hasDroneImage ? `<div class="legend-item"><div class="legend-color drone"></div><span>Drone Imagery</span></div>` : ''}
            `;
            document.body.appendChild(legend);
        }

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

            document.querySelectorAll('.zoom-btn').forEach(btn => {
                btn.addEventListener('touchstart', (e) => e.stopPropagation());
                btn.addEventListener('touchend', (e) => e.stopPropagation());
            });
        }

        function refreshLayers() {
            if (polygonLayer) map.removeLayer(polygonLayer);
            if (lineLayer) map.removeLayer(lineLayer);

            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);

            console.log(`Loading: ${polygons.length} polygons, ${lines.length} lines`);

            // Polygon Source
            const polygonSource = new ol.source.Vector();
            polygons.forEach(function(poly) {
                try {
                    let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly
                        .coordinates;
                    if (coords && coords.length) {
                        const feature = new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            sqfeet: poly.sqfeet
                        });
                        polygonSource.addFeature(feature);
                    }
                } catch (e) {
                    console.log('Error parsing polygon:', e);
                }
            });

            // Style function with labels for GISID and SqFeet
            function polygonStyleFunction(feature) {
                const gisid = feature.get('gisid');
                const sqfeet = feature.get('sqfeet');
                const geometry = feature.getGeometry();

                // Get center point for label placement
                let center;
                try {
                    center = geometry.getInteriorPoint();
                    if (!center) {
                        const extent = geometry.getExtent();
                        const x = (extent[0] + extent[2]) / 2;
                        const y = (extent[1] + extent[3]) / 2;
                        center = new ol.geom.Point([x, y]);
                    }
                } catch (e) {
                    const extent = geometry.getExtent();
                    const x = (extent[0] + extent[2]) / 2;
                    const y = (extent[1] + extent[3]) / 2;
                    center = new ol.geom.Point([x, y]);
                }

                return [
                    // Polygon outline and fill
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 68, 68, 0.5)'
                        })
                    }),
                    // Label with GIS ID and Sq Feet
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
                            offsetY: 0,
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

            polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: polygonStyleFunction,
                visible: true
            });

            // Line Source
            const lineSource = new ol.source.Vector();
            lines.forEach(function(line) {
                try {
                    let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line
                        .coordinates;
                    if (coords && coords.length) {
                        if (coords.length === 1 && Array.isArray(coords[0][0])) {
                            coords = coords[0];
                        }
                        const feature = new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: line.gisid
                        });
                        lineSource.addFeature(feature);
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

            // Fit to polygons if no boundary exists
            if (!boundaryLayer && polygonSource.getFeatures().length > 0) {
                try {
                    const extent = polygonSource.getExtent();
                    if (extent && !isNaN(extent[0]) && !isNaN(extent[1]) &&
                        extent[0] !== Infinity && extent[1] !== -Infinity) {
                        map.getView().fit(extent, {
                            padding: [50, 50, 50, 50],
                            duration: 1000
                        });
                    }
                } catch (e) {
                    console.log('Error fitting to polygons:', e);
                }
            }

            console.log('Layers Refreshed Successfully with Labels');
        }

        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                if (map) {
                    map.updateSize();
                }
            }, 100);
        });

        window.addEventListener('resize', function() {
            setTimeout(function() {
                if (map) {
                    map.updateSize();
                }
            }, 100);
        });

        // Initialize map when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initMap();
            });
        } else {
            initMap();
        }
    </script>
@endpush
