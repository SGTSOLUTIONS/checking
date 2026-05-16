{{-- resources/views/surveyor/ward-map.blade.php --}}
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #f4f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        /* MAP */
        #map {
            width: 100%;
            height: 90vh;
            border-radius: 18px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* PAGE HEADER */
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

        /* FLOATING BUTTON */
        #layerToggleBtn {
            position: absolute;
            top: 130px;
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
        }

        #layerToggleBtn:hover {
            transform: scale(1.08);
        }

        /* Search Button */
        #searchToggleBtn {
            position: absolute;
            top: 195px;
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
        }

        #searchToggleBtn:hover {
            transform: scale(1.08);
        }

        /* Search Label */
        .search-Lable {
            position: absolute;
            top: 195px;
            right: 90px;
            z-index: 1100;
            width: 260px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 8px 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.35s ease;
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
        }

        /* PANEL */
        .layer-switcher {
            position: absolute;
            top: 125px;
            right: 90px;
            z-index: 1100;
            width: 260px;

            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);

            border-radius: 20px;
            padding: 18px;

            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);

            border: 1px solid rgba(255, 255, 255, 0.4);

            transition: all 0.35s ease;
        }

        /* CLOSED STATE */
        .layer-switcher.closed {
            opacity: 0;
            visibility: hidden;
            transform: translateX(30px) scale(0.95);
        }

        /* HEADER */
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

        /* CLOSE BUTTON */
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

        /* ITEMS */
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

        /* CHECKBOX */
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

        /* SEARCH RESULT HIGHLIGHT */
        .highlight-feature {
            animation: pulse 1.5s ease-in-out 3;
        }

        @keyframes pulse {
            0% {
                filter: drop-shadow(0 0 0px rgba(255, 0, 0, 0));
            }
            50% {
                filter: drop-shadow(0 0 15px rgba(255, 0, 0, 0.8));
            }
            100% {
                filter: drop-shadow(0 0 0px rgba(255, 0, 0, 0));
            }
        }

        /* MOBILE */
        @media(max-width:768px) {

            #layerToggleBtn, #searchToggleBtn {
                top: 95px;
                right: 12px;
                width: 50px;
                height: 50px;
            }

            #searchToggleBtn {
                top: 155px;
            }

            .layer-switcher {
                top: 90px;
                right: 70px;
                width: 220px;
            }

            .search-Lable {
                top: 155px;
                right: 70px;
                width: 200px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-3 desktop-only">
        <h3 class="fw-bold mb-3">
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward Map View - Ward {{ $ward->ward_no }}
        </h3>
    </div>

    <div id="map"></div>

    <!-- Floating Toggle Button -->
    <div id="layerToggleBtn">
        <i class="fas fa-layer-group"></i>
    </div>

    <div id="searchToggleBtn">
        <i class="fas fa-search"></i>
    </div>

    <div id="searchLabel" class="search-Lable closed">
        <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID or Road Name...">
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
                        fill: new ol.style.Fill({ color: color }),
                        stroke: new ol.style.Stroke({ color: color, width: 2 })
                    }),
                    text: new ol.style.Text({
                        text: gisid ? String(gisid) : "",
                        scale: 1.3,
                        offsetY: -15,
                        fill: new ol.style.Fill({ color: "#000" }),
                        stroke: new ol.style.Stroke({ color: "#fff", width: 3 })
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
                            fill: new ol.style.Fill({ color: "#000000" }),
                            backgroundFill: new ol.style.Fill({ color: "#ffffff" }),
                            backgroundStroke: new ol.style.Stroke({ color: "#000000", width: 1 }),
                            padding: [4, 6, 4, 6],
                            overflow: true,
                            textAlign: "center",
                            offsetY: 0
                        }),
                        image: new ol.style.Circle({
                            radius: 4,
                            fill: new ol.style.Fill({ color: "yellow" }),
                            stroke: new ol.style.Stroke({ color: "#000", width: 1 })
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
                        fill: new ol.style.Fill({ color: "#000" }),
                        stroke: new ol.style.Stroke({ color: "#fff", width: 3 })
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

                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array.isArray(coords[0][0])) {
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

            // Initialize map
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer, lineLayer, pointLayer],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // ================= PANEL TOGGLE ================= //
            $('#layerToggleBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
            });

            $('#closeLayerPanel').click(function() {
                $('#layerSwitcher').addClass('closed');
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

            // ================= SEARCH FUNCTIONALITY ================= //
            let currentHighlightFeature = null;
            let currentHighlightLayer = null;

            // Function to remove highlight
            function removeHighlight() {
                if (currentHighlightFeature && currentHighlightLayer) {
                    currentHighlightLayer.getSource().removeFeature(currentHighlightFeature);
                    currentHighlightFeature = null;
                    currentHighlightLayer = null;
                }
            }

            // Function to highlight feature
            function highlightFeature(feature, layer, color = '#ff0000') {
                removeHighlight();

                // Create a copy of the geometry for highlighting
                const geom = feature.getGeometry().clone();
                const highlightFeature = new ol.Feature({ geometry: geom });

                // Create highlight style based on geometry type
                let style;
                if (geom.getType() === 'Point') {
                    style = new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({ color: 'rgba(255, 0, 0, 0.6)' }),
                            stroke: new ol.style.Stroke({ color: '#ffffff', width: 3 })
                        })
                    });
                } else if (geom.getType() === 'LineString') {
                    style = new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 6,
                            lineDash: [10, 10]
                        })
                    });
                } else {
                    style = new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: color, width: 4 }),
                        fill: new ol.style.Fill({ color: 'rgba(255, 0, 0, 0.2)' })
                    });
                }

                highlightFeature.setStyle(style);

                // Create a temporary layer for highlight
                const highlightLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [highlightFeature]
                    }),
                    zIndex: 1000
                });

                map.addLayer(highlightLayer);
                currentHighlightFeature = highlightFeature;
                currentHighlightLayer = highlightLayer;

                // Zoom to feature
                const extent = geom.getExtent();
                map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });

                // Auto remove highlight after 5 seconds
                setTimeout(() => {
                    removeHighlight();
                }, 5000);
            }

            // Search function
            function searchFeature(searchTerm) {
                if (!searchTerm || searchTerm.trim() === '') {
                    removeHighlight();
                    return;
                }

                searchTerm = searchTerm.trim();
                let found = false;

                // Search in polygons
                polygonSource.getFeatures().forEach(feature => {
                    const gisid = feature.get('gisid');
                    if (gisid && String(gisid) === searchTerm) {
                        highlightFeature(feature, polygonLayer, '#ff0000');
                        found = true;
                        return;
                    }
                });

                if (!found) {
                    // Search in lines (roads) by gisid or road_name
                    lineSource.getFeatures().forEach(feature => {
                        const gisid = feature.get('gisid');
                        const roadName = feature.get('road_name');
                        if ((gisid && String(gisid) === searchTerm) ||
                            (roadName && roadName.toLowerCase().includes(searchTerm.toLowerCase()))) {
                            highlightFeature(feature, lineLayer, '#ff0000');
                            found = true;
                            return;
                        }
                    });
                }

                if (!found) {
                    // Search in points
                    pointSource.getFeatures().forEach(feature => {
                        const gisid = feature.get('gisid');
                        if (gisid && String(gisid) === searchTerm) {
                            highlightFeature(feature, pointLayer, '#ff0000');
                            found = true;
                            return;
                        }
                    });
                }

                if (!found) {
                    // Show toast or alert if no feature found
                    if (typeof toastr !== 'undefined') {
                        toastr.info('No feature found with GIS ID or Road Name: ' + searchTerm);
                    } else {
                        alert('No feature found with GIS ID or Road Name: ' + searchTerm);
                    }
                }
            }

            // Search toggle button
            $('#searchToggleBtn').click(function() {
                $('#searchLabel').toggleClass('closed');
                if (!$('#searchLabel').hasClass('closed')) {
                    $('#searchInput').focus();
                }
            });

            // Search input handler
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    searchFeature($(this).val());
                    $(this).val(''); // Clear input after search
                    $('#searchLabel').addClass('closed'); // Close search panel
                }
            });

            // Close search panel when clicking outside (optional)
            $(document).click(function(e) {
                if (!$(e.target).closest('#searchLabel').length && !$(e.target).closest('#searchToggleBtn').length) {
                    if (!$('#searchLabel').hasClass('closed')) {
                        $('#searchLabel').addClass('closed');
                    }
                }
            });
        });
    </script>
@endsection
