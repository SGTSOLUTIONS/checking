{{-- resources/views/surveyor/ward-map.blade.php --}}
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* MAP */
        #map {
            width: 100%;
            height: calc(100vh - 60px);
            border-radius: 0;
            overflow: hidden;
            border: none;
        }

        /* PAGE HEADER - Desktop */
        .page-title {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 16px 25px;
            border-radius: 16px;
            margin-bottom: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .page-title h3 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        /* FLOATING BUTTONS - Desktop */
        #layerToggleBtn,
        #searchToggleBtn,
        #liveToggleBtn,
        #routeBtn {
            position: absolute;
            right: 20px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 16px;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            border: none;
        }

        #layerToggleBtn {
            top: 130px;
        }

        #searchToggleBtn {
            top: 195px;
        }

        #liveToggleBtn {
            top: 260px;
        }

        #routeBtn {
            top: 325px;
        }

        #layerToggleBtn:hover,
        #searchToggleBtn:hover,
        #liveToggleBtn:hover,
        #routeBtn:hover {
            transform: scale(1.08);
        }

        #routeBtn.closed {
            opacity: 0;
            visibility: hidden;
        }

        /* Search Label - Desktop */
        .search-Lable {
            position: absolute;
            top: 195px;
            right: 90px;
            z-index: 1100;
            width: 320px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 12px 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
            display: flex;
            gap: 10px;
        }

        .search-Lable.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .search-Lable input {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            font-size: 14px;
            flex: 1;
        }

        .search-Lable button {
            border-radius: 12px;
            padding: 10px 20px;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-Lable button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Layer Switcher Panel - Desktop */
        .layer-switcher {
            position: absolute;
            top: 125px;
            right: 90px;
            z-index: 1100;
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
        }

        .layer-switcher.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        .layer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .layer-header div {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .layer-header i {
            color: #2563eb;
        }

        #closeLayerPanel {
            border: none;
            background: #eff6ff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            color: #1e40af;
            transition: 0.3s;
        }

        #closeLayerPanel:hover {
            background: #dbeafe;
            transform: rotate(90deg);
        }

        .layer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .layer-item:hover {
            background: #eff6ff;
            transform: translateX(4px);
        }

        .layer-item input {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #2563eb;
            position: relative;
            transition: 0.3s;
        }

        .layer-item input:checked+.checkmark {
            background: #2563eb;
        }

        .layer-item input:checked+.checkmark::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 13px;
            top: -1px;
            left: 3px;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            z-index: 1300;
            animation: slideUp 0.3s ease;
            backdrop-filter: blur(10px);
            pointer-events: none;
        }

        .toast-notification.success {
            background: rgba(34, 197, 94, 0.95);
        }

        .toast-notification.error {
            background: rgba(239, 68, 68, 0.95);
        }

        /* Route Info Panel */
        .route-info-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: white;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
            z-index: 1100;
            transition: all 0.3s ease;
            max-width: 400px;
        }

        .route-info-panel.closed {
            transform: translateY(150%);
        }

        .route-info-panel h5 {
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .route-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .route-stat {
            flex: 1;
            text-align: center;
        }

        .route-stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        .route-stat-label {
            font-size: 12px;
            color: #64748b;
        }

        .close-route-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #64748b;
        }

        .start-navigation-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .start-navigation-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1400;
            display: none;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }

            .mobile-header {
                display: block;
            }

            #map {
                height: calc(100vh - 56px);
            }

            #layerToggleBtn,
            #searchToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                right: auto;
                left: 12px;
                width: 48px;
                height: 48px;
                font-size: 20px;
                border-radius: 14px;
            }

            #layerToggleBtn {
                top: 70px;
            }

            #searchToggleBtn {
                top: 128px;
            }

            #liveToggleBtn {
                top: 186px;
            }

            #routeBtn {
                top: 244px;
            }

            .layer-switcher {
                top: 60px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: calc(100% - 24px);
                border-radius: 16px;
                padding: 15px;
            }

            .search-Lable {
                top: 128px;
                right: 12px;
                left: 70px;
                width: auto;
                max-width: calc(100% - 90px);
                padding: 10px 12px;
                gap: 8px;
            }

            .route-info-panel {
                left: 12px;
                right: 12px;
                bottom: 12px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Mobile Header -->
    <div class="mobile-header">
        <h4>
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward {{ $ward->ward_no }} Map
        </h4>
    </div>

    <!-- Desktop Header -->
    <div class="container-fluid py-3 desktop-only">
        <div class="page-title">
            <h3>
                <i class="fas fa-map-marked-alt me-2"></i>
                Ward Map View - Ward {{ $ward->ward_no }}
            </h3>
        </div>
    </div>

    <div id="map"></div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner"></div>

    <!-- Route Info Panel -->
    <div id="routeInfoPanel" class="route-info-panel closed">
        <button class="close-route-btn" id="closeRouteBtn">&times;</button>
        <h5><i class="fas fa-route me-2"></i>Route to Destination</h5>
        <div id="destinationName" style="font-size: 14px; color: #64748b; margin-bottom: 10px;"></div>
        <div class="route-stats">
            <div class="route-stat">
                <div class="route-stat-value" id="routeDistance">0 km</div>
                <div class="route-stat-label">Distance</div>
            </div>
            <div class="route-stat">
                <div class="route-stat-value" id="routeDuration">0 min</div>
                <div class="route-stat-label">Est. Time</div>
            </div>
        </div>
        <button class="start-navigation-btn" id="startNavigationBtn">
            <i class="fas fa-directions me-2"></i>Start Navigation
        </button>
    </div>

    <!-- Floating Action Buttons -->
    <div id="layerToggleBtn" title="Toggle Layers">
        <i class="fas fa-layer-group"></i>
    </div>

    <div id="searchToggleBtn" title="Search">
        <i class="fas fa-search"></i>
    </div>

    <div id="liveToggleBtn" title="My Location">
        <i class="fas fa-location-dot"></i>
    </div>

    <div id="routeBtn" title="Get Route">
        <i class="fas fa-route"></i>
    </div>

    <!-- Search Panel -->
    <div id="searchLabel" class="search-Lable closed">
        <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID or Road Name...">
        <button id="searchGisidBtn"><i class="fas fa-search"></i> Search</button>
    </div>

    <!-- Layer Switcher Panel -->
    <div id="layerSwitcher" class="layer-switcher closed">
        <div class="layer-header">
            <div>
                <i class="fas fa-layer-group"></i>
                <span>Map Layers</span>
            </div>
            <button id="closeLayerPanel">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <label class="layer-item">
            <input type="checkbox" id="osmToggle" checked>
            <span class="checkmark"></span>
            <span>OSM Map</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="satelliteToggle">
            <span class="checkmark"></span>
            <span>Satellite</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="droneToggle" checked>
            <span class="checkmark"></span>
            <span>Drone Image</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="boundaryToggle" checked>
            <span class="checkmark"></span>
            <span>Ward Boundary</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="polygonToggle" checked>
            <span class="checkmark"></span>
            <span>Buildings</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="lineToggle" checked>
            <span class="checkmark"></span>
            <span>Roads</span>
        </label>

        <label class="layer-item">
            <input type="checkbox" id="pointToggle" checked>
            <span class="checkmark"></span>
            <span>Points</span>
        </label>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Data passed from controller
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let mis = @json($misData ?? []);
            let selectedFeature = null;
            let currentRoute = null;
            let isMobile = window.innerWidth <= 768;

            // Routes for AJAX calls
            let routes = {
                surveyorPolygonDatasUpload: "{{ route('surveyor.polygon.datas.upload') }}",
                surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}",
                updateRoadName: "{{ route('surveyor.update.road.name') }}",
                delgisid: "{{ route('surveyor.delgisid') }}",
                addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
                addLineFeature: "{{ route('surveyor.add.line.feature') }}",
                addPointFeature: "{{ route('surveyor.add.point.feature') }}",
                surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}",
                deleteFeature: "{{ route('surveyor.delete.feature') }}"
            };

            // Helper Functions
            function formatDistance(meters) {
                if (meters >= 1000) {
                    return (meters / 1000).toFixed(1) + ' km';
                }
                return Math.round(meters) + ' m';
            }

            function formatDuration(seconds) {
                if (seconds >= 3600) {
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    return hours + ' hr ' + minutes + ' min';
                }
                if (seconds >= 60) {
                    return Math.floor(seconds / 60) + ' min';
                }
                return Math.floor(seconds) + ' sec';
            }

            // Toast notification function
            function showToast(message, type = 'info') {
                const toast = $('<div class="toast-notification ' + type + '">' + message + '</div>');
                $('body').append(toast);
                setTimeout(() => {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            // Drone image configuration
            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            // Create map layers
            const osmLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                visible: true
            });

            const satelliteLayer = new ol.layer.Tile({
                source: new ol.source.OSM({
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
                opacity: 0.90,
                visible: true
            });

            // Ward boundary layer
            const boundary = ward.boundary[0];
            const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
            const boundarys = new ol.geom.Polygon([transformedBoundary]);
            const boundaryLayer = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: [new ol.Feature({
                        geometry: boundarys
                    })]
                }),
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "red",
                        width: 3
                    })
                })
            });

            // Style functions
            function createPointStyle(feature) {
                const gisid = feature.get("gisid");
                const pointCount = pointDatas.filter(data => data.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(data => data.gisid == gisid);
                let color = "blue";

                if (polygonData) {
                    if (pointCount > 0) {
                        color = (polygonData.number_bill == pointCount) ? "green" : "red";
                    } else {
                        color = "blue";
                    }
                }

                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 8,
                        fill: new ol.style.Fill({
                            color: color
                        }),
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 2
                        })
                    }),
                    text: new ol.style.Text({
                        text: gisid ? String(gisid) : "",
                        scale: 1.3,
                        offsetY: -15,
                        fill: new ol.style.Fill({
                            color: "#000"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#fff",
                            width: 3
                        })
                    })
                });
            }

            function createPolygonStyle(feature) {
                const gisid = feature.get("gisid");
                const sqft = feature.get("sqfeet") || "0";
                const polygonData = polygonDatas.find(data => data.gisid == gisid);
                const color = polygonData ? "red" : "blue";
                const geometry = feature.getGeometry();
                const centerPoint = geometry.getInteriorPoint();

                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 4,
                            lineJoin: "round",
                            lineCap: "round"
                        })
                    }),
                    new ol.style.Style({
                        geometry: centerPoint,
                        text: new ol.style.Text({
                            text: sqft + " SQFT",
                            font: "bold 14px Arial",
                            fill: new ol.style.Fill({
                                color: "#000000"
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: "#ffffff"
                            }),
                            backgroundStroke: new ol.style.Stroke({
                                color: "#000000",
                                width: 1
                            }),
                            padding: [4, 6, 4, 6],
                            overflow: true,
                            textAlign: "center",
                            offsetY: 0
                        }),
                        image: new ol.style.Circle({
                            radius: 4,
                            fill: new ol.style.Fill({
                                color: "yellow"
                            }),
                            stroke: new ol.style.Stroke({
                                color: "#000",
                                width: 1
                            })
                        })
                    })
                ];
            }

            function createLineStyle(feature) {
                const road_name = feature.get("road_name");
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "yellow",
                        width: 4,
                        lineJoin: "round",
                        lineCap: "round"
                    }),
                    text: new ol.style.Text({
                        text: road_name ? String(road_name) : "",
                        font: "bold 14px Calibri, sans-serif",
                        placement: "line",
                        overflow: true,
                        fill: new ol.style.Fill({
                            color: "#000"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#fff",
                            width: 3
                        })
                    })
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
                        type: "Polygon",
                        sqfeet: poly.sqfeet || "0"
                    }));
                } catch (e) {
                    console.error('Polygon parse error:', e);
                }
            });

            const polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: createPolygonStyle,
                visible: true
            });

            // Line Layer
            const lineSource = new ol.source.Vector();
            lines.forEach(l => {
                try {
                    let coords;
                    if (typeof l.coordinates === 'string') {
                        coords = JSON.parse(l.coordinates);
                    } else if (Array.isArray(l.coordinates)) {
                        coords = l.coordinates;
                    } else {
                        console.warn('Invalid coordinates format for line:', l.gisid);
                        return;
                    }

                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                        .isArray(coords[0][0])) {
                        coords = coords[0];
                    }

                    if (!coords || coords.length < 2) {
                        console.warn('Line needs at least 2 coordinates:', l.gisid);
                        return;
                    }

                    const isValid = coords.every(coord =>
                        Array.isArray(coord) && coord.length >= 2 &&
                        typeof coord[0] === 'number' && typeof coord[1] === 'number' &&
                        !isNaN(coord[0]) && !isNaN(coord[1])
                    );

                    if (!isValid) {
                        console.warn('Invalid coordinate values for line:', l.gisid);
                        return;
                    }

                    lineSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: l.gisid,
                        type: "Line",
                        road_name: l.road_name || null
                    }));
                } catch (e) {
                    console.error('Line parse error:', e, l);
                }
            });

            const lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: createLineStyle,
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
                } catch (e) {
                    console.error('Point parse error:', e);
                }
            });

            const pointLayer = new ol.layer.Vector({
                source: pointSource,
                style: createPointStyle,
                visible: true
            });

            // Route layer
            const routeSource = new ol.source.Vector();
            const routeLayer = new ol.layer.Vector({
                source: routeSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#2563eb',
                        width: 5,
                        lineDash: [10, 10]
                    })
                })
            });

            // Initialize map
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer,
                    pointLayer, routeLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Add touch-friendly interactions for mobile
            if ('ontouchstart' in window) {
                map.getInteractions().forEach(interaction => {
                    if (interaction instanceof ol.interaction.DoubleClickZoom) {
                        interaction.setActive(false);
                    }
                });
            }

            // ================= PANEL TOGGLE ================= //
            $('#layerToggleBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
                $('#searchLabel').addClass('closed');
            });

            $('#closeLayerPanel').click(function() {
                $('#layerSwitcher').addClass('closed');
            });

            $('#closeRouteBtn').click(function() {
                $('#routeInfoPanel').addClass('closed');
                // Clear route from map
                routeSource.clear();
            });

            // Close panels when clicking outside (for mobile)
            $(document).click(function(event) {
                if (!$(event.target).closest(
                        '#layerSwitcher, #layerToggleBtn, #searchLabel, #searchToggleBtn, #routeInfoPanel, #routeBtn').length) {
                    $('#layerSwitcher, #searchLabel').addClass('closed');
                }
            });

            // Layer visibility toggles
            $('#osmToggle').change(function() {
                osmLayer.setVisible($(this).is(':checked'));
            });

            $('#satelliteToggle').change(function() {
                satelliteLayer.setVisible($(this).is(':checked'));
            });

            $('#droneToggle').change(function() {
                droneLayer.setVisible($(this).is(':checked'));
            });

            $('#boundaryToggle').change(function() {
                boundaryLayer.setVisible($(this).is(':checked'));
            });

            $('#polygonToggle').change(function() {
                polygonLayer.setVisible($(this).is(':checked'));
            });

            $('#lineToggle').change(function() {
                lineLayer.setVisible($(this).is(':checked'));
            });

            $('#pointToggle').change(function() {
                pointLayer.setVisible($(this).is(':checked'));
            });

            // Search toggle button
            $('#searchToggleBtn').click(function(e) {
                e.stopPropagation();
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) {
                    setTimeout(() => {
                        $('#searchInput').focus();
                    }, 100);
                } else {
                    selectedFeature = null;
                }
            });

            // Search functionality
            $("#searchGisidBtn").on('click', function() {
                var searchvalue = $("#searchInput").val().trim();
                selectedFeature = null;

                if (!searchvalue) {
                    showToast("Please enter a GIS ID or Road Name", 'error');
                    return;
                }

                // Search in polygons
                var findpolygon = polygons.find(function(polygon) {
                    return polygon.gisid == searchvalue;
                });

                if (findpolygon) {
                    try {
                        var coordinates = JSON.parse(findpolygon.coordinates);
                        var feature = new ol.Feature({
                            geometry: new ol.geom.Polygon(coordinates),
                            gisid: findpolygon.gisid,
                            type: "Polygon",
                            sqfeet: findpolygon.sqfeet || "0"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 22
                        });

                        selectedFeature = feature;
                        showToast("Found GIS ID: " + findpolygon.gisid, 'success');
                        $('#searchLabel').addClass('closed');
                    } catch (e) {
                        console.error('Error parsing coordinates:', e);
                        showToast("Error displaying polygon", 'error');
                    }
                } else {
                    // Search in lines/roads
                    var findLine = lines.find(function(line) {
                        return line.gisid == searchvalue || (line.road_name && line.road_name
                            .toLowerCase().includes(searchvalue.toLowerCase()));
                    });

                    if (findLine) {
                        try {
                            let coords;
                            if (typeof findLine.coordinates === 'string') {
                                coords = JSON.parse(findLine.coordinates);
                            } else {
                                coords = findLine.coordinates;
                            }

                            if (coords.length === 1 && Array.isArray(coords[0])) {
                                coords = coords[0];
                            }

                            var feature = new ol.Feature({
                                geometry: new ol.geom.LineString(coords),
                                gisid: findLine.gisid,
                                road_name: findLine.road_name,
                                type: "Line"
                            });
                            map.getView().fit(feature.getGeometry().getExtent(), {
                                duration: 1000,
                                padding: [50, 50, 50, 50],
                                maxZoom: 20
                            });

                            selectedFeature = feature;
                            showToast("Found: " + (findLine.road_name || findLine.gisid), 'success');
                            $('#searchLabel').addClass('closed');
                        } catch (e) {
                            console.error('Error parsing line coordinates:', e);
                            showToast("Error displaying road", 'error');
                        }
                    } else {
                        // Search in points
                        var findPoint = points.find(function(point) {
                            return point.gisid == searchvalue;
                        });

                        if (findPoint) {
                            try {
                                var coordinates = JSON.parse(findPoint.coordinates);
                                var feature = new ol.Feature({
                                    geometry: new ol.geom.Point(coordinates),
                                    gisid: findPoint.gisid,
                                    type: "Point"
                                });
                                map.getView().fit(feature.getGeometry().getExtent(), {
                                    duration: 1000,
                                    padding: [50, 50, 50, 50],
                                    maxZoom: 22
                                });

                                selectedFeature = feature;

                                // Add a temporary highlight for point
                                const highlightLayer = new ol.layer.Vector({
                                    source: new ol.source.Vector(),
                                    style: new ol.style.Style({
                                        image: new ol.style.Circle({
                                            radius: 15,
                                            fill: new ol.style.Fill({
                                                color: 'rgba(255, 165, 0, 0.7)'
                                            }),
                                            stroke: new ol.style.Stroke({
                                                color: '#ffffff',
                                                width: 3
                                            })
                                        })
                                    })
                                });

                                const highlightMarker = new ol.Feature({
                                    geometry: new ol.geom.Point(coordinates)
                                });

                                highlightLayer.getSource().addFeature(highlightMarker);
                                map.addLayer(highlightLayer);

                                setTimeout(() => {
                                    map.removeLayer(highlightLayer);
                                }, 3000);

                                showToast("Found GIS ID: " + findPoint.gisid, 'success');
                                $('#searchLabel').addClass('closed');
                            } catch (e) {
                                console.error('Error parsing point coordinates:', e);
                                showToast("Error displaying point", 'error');
                            }
                        } else {
                            showToast("GIS ID or Road Name not found", 'error');
                        }
                    }
                }
            });

            // Live location button functionality
            let currentLocationMarker = null;
            let currentPosition = null;

            $('#liveToggleBtn').click(function() {
                toggleLiveLocation();
            });

            function toggleLiveLocation() {
                if (!("geolocation" in navigator)) {
                    showToast("Geolocation is not supported by your browser", 'error');
                    return;
                }

                showToast('Fetching your location...', 'info');

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        currentPosition = [lon, lat];
                        const coords = ol.proj.fromLonLat([lon, lat]);

                        map.getView().animate({
                            center: coords,
                            zoom: 18,
                            duration: 1000
                        });

                        // Remove old location marker
                        if (currentLocationMarker) {
                            map.removeLayer(currentLocationMarker);
                        }

                        // Create new location marker
                        currentLocationMarker = new ol.layer.Vector({
                            source: new ol.source.Vector(),
                            style: new ol.style.Style({
                                image: new ol.style.Circle({
                                    radius: 12,
                                    fill: new ol.style.Fill({
                                        color: '#ff4444'
                                    }),
                                    stroke: new ol.style.Stroke({
                                        color: '#ffffff',
                                        width: 3
                                    })
                                })
                            })
                        });

                        const marker = new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        });

                        currentLocationMarker.getSource().addFeature(marker);
                        map.addLayer(currentLocationMarker);

                        showToast('Location found!', 'success');
                    },
                    function(error) {
                        let message = "Error getting location";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                message = "Please enable location permissions";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = "Location information unavailable";
                                break;
                            case error.TIMEOUT:
                                message = "Location request timed out";
                                break;
                        }
                        showToast(message, 'error');
                    }
                );
            }

            // Get route from OSRM
            async function getRouteFromOSRM(startCoord, endCoord) {
                const [startLon, startLat] = startCoord;
                const [endLon, endLat] = endCoord;
                const url = `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true`;

                const response = await fetch(url);
                const data = await response.json();

                if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                    throw new Error('No route found');
                }

                return data.routes[0];
            }

            // Draw route on map
            function drawRouteOnMap(geometry) {
                routeSource.clear();

                if (geometry.type === 'LineString') {
                    const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                    const routeFeature = new ol.Feature({
                        geometry: new ol.geom.LineString(coordinates)
                    });
                    routeSource.addFeature(routeFeature);

                    // Fit map to route extent
                    const extent = routeFeature.getGeometry().getExtent();
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }

            // Display route information
            function displayRouteInfo(distance, duration, placeName) {
                $('#routeDistance').text(formatDistance(distance));
                $('#routeDuration').text(formatDuration(duration));
                $('#destinationName').text(placeName);
                $('#routeInfoPanel').removeClass('closed');
            }

            // Calculate enhanced route
            async function calculateEnhancedRoute(startCoord, endCoord, placeName) {
                try {
                    showToast('Calculating route...', 'info');

                    let route;
                    try {
                        route = await getRouteFromOSRM(startCoord, endCoord);
                    } catch (error) {
                        // Fallback to direct line if OSRM fails
                        console.warn('OSRM failed, using direct line:', error);
                        route = {
                            distance: ol.sphere.getDistance(
                                ol.proj.fromLonLat(startCoord),
                                ol.proj.fromLonLat(endCoord)
                            ),
                            duration: (ol.sphere.getDistance(
                                ol.proj.fromLonLat(startCoord),
                                ol.proj.fromLonLat(endCoord)
                            ) / 1.39), // 5 km/h walking speed
                            geometry: {
                                type: "LineString",
                                coordinates: [startCoord, endCoord]
                            }
                        };
                    }

                    const totalDistance = route.distance;
                    const totalDuration = route.duration;

                    drawRouteOnMap(route.geometry);
                    displayRouteInfo(totalDistance, totalDuration, placeName);

                    return {
                        distance: totalDistance,
                        duration: totalDuration,
                        geometry: route.geometry,
                        endCoord: endCoord,
                        placeName: placeName
                    };
                } catch (error) {
                    console.error('Route calculation error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                    throw error;
                }
            }

            // Calculate and display route
            async function calculateAndDisplayRoute(feature) {
                $('#loadingSpinner').show();

                try {
                    if (!currentLocationMarker) {
                        showToast('Please enable your location first', 'error');
                        return;
                    }

                    // Get current location coordinates
                    const currentCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry().getCoordinates();
                    const geometry = feature.getGeometry();

                    // Get target coordinates (center of feature)
                    let targetCoords;
                    if (geometry.getType() === 'Point') {
                        targetCoords = geometry.getCoordinates();
                    } else {
                        targetCoords = ol.extent.getCenter(geometry.getExtent());
                    }

                    const currentLonLat = ol.proj.toLonLat(currentCoords);
                    const targetLonLat = ol.proj.toLonLat(targetCoords);

                    const placeName = `GIS ID: ${feature.get('gisid') || 'Selected Location'}`;
                    const route = await calculateEnhancedRoute(currentLonLat, targetLonLat, placeName);
                    currentRoute = route;

                } catch (error) {
                    console.error('Route calculation error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            // Route button click handler
            $('#routeBtn').click(async function() {
                if (!selectedFeature) {
                    showToast('No feature selected. Please search for a location first.', 'error');
                    return;
                }

                if (!currentLocationMarker) {
                    const enableLocation = confirm('Live location is not enabled. Would you like to enable it for route calculation?');
                    if (enableLocation) {
                        toggleLiveLocation();
                        // Wait for location to be acquired
                        setTimeout(async () => {
                            if (currentLocationMarker) {
                                await calculateAndDisplayRoute(selectedFeature);
                            } else {
                                showToast('Unable to get your location. Please enable location services.', 'error');
                            }
                        }, 2500);
                    }
                    return;
                }

                await calculateAndDisplayRoute(selectedFeature);
            });

            // Start navigation button
            $('#startNavigationBtn').click(function() {
                if (currentRoute) {
                    // Create a simple navigation alert
                    const navMessage = `Navigation Started!\nDistance: ${$('#routeDistance').text()}\nEstimated time: ${$('#routeDuration').text()}`;
                    alert(navMessage);

                    // On mobile, could open Google Maps
                    if (isMobile && currentRoute.endCoord) {
                        const destLonLat = currentRoute.endCoord;
                        const url = `https://www.google.com/maps/dir/?api=1&destination=${destLonLat[1]},${destLonLat[0]}`;
                        if (confirm('Open in Google Maps for turn-by-turn navigation?')) {
                            window.open(url, '_blank');
                        }
                    }
                }
            });

            // Handle window resize for mobile detection
            $(window).resize(function() {
                isMobile = window.innerWidth <= 768;
            });

            // Keyboard shortcuts
            $(document).keydown(function(e) {
                if (e.key === 'l' || e.key === 'L') {
                    $('#layerSwitcher').toggleClass('closed');
                    $('#searchLabel').addClass('closed');
                }
                if (e.key === 's' || e.key === 'S') {
                    $('#searchLabel').toggleClass('closed');
                    $('#layerSwitcher').addClass('closed');
                    if (!$('#searchLabel').hasClass('closed')) {
                        setTimeout(() => {
                            $('#searchInput').focus();
                        }, 100);
                    }
                }
                if (e.key === 'Escape') {
                    $('#layerSwitcher, #searchLabel, #routeInfoPanel').addClass('closed');
                }
            });

            // Add scale line control
            const scaleLine = new ol.control.ScaleLine({
                units: 'metric',
                target: document.createElement('div')
            });
            map.addControl(scaleLine);

            // Handle map touch events for better mobile experience
            if ('ontouchstart' in window) {
                map.on('click', function() {
                    if (window.innerWidth <= 768) {
                        $('#layerSwitcher, #searchLabel').addClass('closed');
                    }
                });
            }

            showToast('Map loaded successfully', 'success');
        });
    </script>
@endsection
