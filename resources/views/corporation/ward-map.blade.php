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

            .ol-popup {
                max-width: 280px !important;
                max-height: 70vh !important;
                overflow-y: auto !important;
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

        /* Popup Styles */
        .ol-popup {
            position: absolute;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            border-radius: 12px;
            padding: 0;
            min-width: 280px;
            max-width: 380px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.5);
            z-index: 1100;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .ol-popup:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 10px 10px 0;
            border-style: solid;
            border-color: #1a1a2e transparent transparent;
        }

        .popup-header {
            background: rgba(255, 68, 68, 0.2);
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #ff4444;
        }

        .popup-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .popup-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .popup-content {
            padding: 15px;
        }

        .popup-section {
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }

        .popup-section-title {
            font-size: 12px;
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .popup-row {
            display: flex;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .popup-label {
            font-weight: 600;
            color: #aaa;
            width: 100px;
            flex-shrink: 0;
        }

        .popup-value {
            color: white;
            flex: 1;
            word-break: break-word;
        }

        .popup-value strong {
            color: #ff4444;
        }

        .assessment-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 8px;
        }

        .assessment-item:last-child {
            margin-bottom: 0;
        }

        .shop-item {
            background: rgba(255, 193, 7, 0.1);
            border-radius: 6px;
            padding: 6px 8px;
            margin-top: 5px;
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 5px;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
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
        let popupOverlay;
        let popupElement;

        // Polygon data from server
        let polygonDatas = @json($polygonDatas ?? []);

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

        // Create popup overlay
        function createPopup() {
            popupElement = document.createElement('div');
            popupElement.className = 'ol-popup';
            popupElement.style.display = 'none';
            document.body.appendChild(popupElement);

            popupOverlay = new ol.Overlay({
                element: popupElement,
                positioning: 'bottom-center',
                stopEvent: true,
                offset: [0, -10]
            });

            return popupOverlay;
        }

        // Show popup with building details
        function showPopup(gisid, coordinate) {
            // Find polygon data by GIS ID
            const polyData = polygonDatas.find(p => p.gisid == gisid);

            if (!polyData) {
                console.log('No data found for GIS ID:', gisid);
                return;
            }

            console.log('Showing popup for:', polyData);

            // Build popup HTML
            let html = `
                <div class="popup-header">
                    <h4><i class="fas fa-building"></i> Building Details</h4>
                    <button class="popup-close" onclick="document.querySelector('.ol-popup').style.display='none'">&times;</button>
                </div>
                <div class="popup-content">
                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                        <div class="popup-row">
                            <span class="popup-label">GIS ID:</span>
                            <span class="popup-value"><strong>${polyData.gisid || 'N/A'}</strong></span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Building Usage:</span>
                            <span class="popup-value">${polyData.building_usage || 'N/A'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Building Type:</span>
                            <span class="popup-value">${polyData.building_type || 'N/A'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Construction Type:</span>
                            <span class="popup-value">${polyData.construction_type || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-chart-simple"></i> Statistics</div>
                        <div class="popup-row">
                            <span class="popup-label">Number of Floors:</span>
                            <span class="popup-value">${polyData.number_floor || '0'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Floor Percentage:</span>
                            <span class="popup-value">${polyData.floor_percentage || '100'}%</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Basement:</span>
                            <span class="popup-value">${polyData.basement || '0'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Number of Bills:</span>
                            <span class="popup-value">${polyData.number_bill || '0'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Total Points:</span>
                            <span class="popup-value">${polyData.total_points || '0'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Total Shops:</span>
                            <span class="popup-value">${polyData.total_shops || '0'}</span>
                        </div>
                    </div>

                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-map-marker-alt"></i> Location</div>
                        <div class="popup-row">
                            <span class="popup-label">Road Name:</span>
                            <span class="popup-value">${polyData.road_name || 'N/A'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">Zone:</span>
                            <span class="popup-value">${polyData.zone || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-tint"></i> Utilities</div>
                        <div class="popup-row">
                            <span class="popup-label">Water Connection:</span>
                            <span class="popup-value">${polyData.water_connection || 'N/A'}</span>
                        </div>
                        <div class="popup-row">
                            <span class="popup-label">UGD:</span>
                            <span class="popup-value">${polyData.ugd || 'N/A'}</span>
                        </div>
                    </div>
            `;

            // Add Assessments/Bills section if exists
            if (polyData.pointdata && polyData.pointdata.length > 0) {
                html += `
                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-receipt"></i> Assessments / Bills (${polyData.pointdata.length})</div>
                `;

                polyData.pointdata.forEach((assessment, idx) => {
                    const hasQC = assessment.qcsqfeet || assessment.qcusage;
                    html += `
                        <div class="assessment-item">
                            <div class="popup-row">
                                <span class="popup-label">Assessment ${idx + 1}:</span>
                                <span class="popup-value"><strong>${assessment.assessment || 'N/A'}</strong> <span class="badge ${hasQC ? 'badge-success' : 'badge-warning'}">${hasQC ? 'QC Done' : 'QC Pending'}</span></span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">Owner:</span>
                                <span class="popup-value">${assessment.owner_name || assessment.present_owner_name || 'N/A'}</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">Phone:</span>
                                <span class="popup-value">${assessment.phone_number || 'N/A'}</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">Floor:</span>
                                <span class="popup-value">${assessment.floor || 'N/A'}</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">Usage:</span>
                                <span class="popup-value">${assessment.bill_usage || 'N/A'}</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">QC Sqft:</span>
                                <span class="popup-value">${assessment.qcsqfeet || assessment.sqfeet || 'N/A'} sqft</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">QC Usage:</span>
                                <span class="popup-value">${assessment.qcusage || assessment.usage || 'N/A'}</span>
                            </div>
                    `;

                    // Add shops if exists
                    if (assessment.shops && assessment.shops.length > 0) {
                        html +=
                            `<div class="popup-row"><span class="popup-label">Shops (${assessment.shops.length}):</span></div>`;
                        assessment.shops.forEach(shop => {
                            html += `
                                <div class="shop-item">
                                    <div><strong>${shop.shop_name || 'Shop'}</strong> - ${shop.shop_category || 'N/A'}</div>
                                    <div>Owner: ${shop.shop_owner_name || 'N/A'}</div>
                                    <div>Mobile: ${shop.shop_mobile || 'N/A'}</div>
                                </div>
                            `;
                        });
                    }

                    html += `</div>`;
                });

                html += `</div>`;
            }

            // Add Remarks if exists
            if (polyData.remarks) {
                html += `
                    <div class="popup-section">
                        <div class="popup-section-title"><i class="fas fa-comment"></i> Remarks</div>
                        <div class="popup-row">
                            <span class="popup-value">${polyData.remarks}</span>
                        </div>
                    </div>
                `;
            }

            html += `</div>`;

            popupElement.innerHTML = html;
            popupElement.style.display = 'block';
            popupOverlay.setPosition(coordinate);

            // Add close button event listener
            const closeBtn = popupElement.querySelector('.popup-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    popupElement.style.display = 'none';
                });
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

            let imageUrl = null;
            if (droneImage) {
                let cleanPath = droneImage.replace(/^\/+/, '');
                imageUrl = "{{ asset('') }}" + cleanPath;
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

            // Add popup overlay
            const popup = createPopup();
            map.addOverlay(popup);

            if (imageLayer) {
                map.addLayer(imageLayer);
            }

            if (boundaryLayer) {
                map.addLayer(boundaryLayer);
            }

            // Zoom to boundary
            setTimeout(function() {
                if (boundaryExtent && boundaryExtent.length === 4) {
                    map.getView().fit(boundaryExtent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
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
                <div class="legend-item"><div class="legend-color building"></div><span>Buildings (click for details)</span></div>
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
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 68, 68, 0.15)'
                        })
                    }),
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

            // Add click handler for polygons
            map.on('click', function(evt) {
                const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                    return feature;
                });

                if (feature && feature.get('gisid')) {
                    const gisid = feature.get('gisid');
                    const coordinate = evt.coordinate;
                    showPopup(gisid, coordinate);
                } else {
                    if (popupElement) {
                        popupElement.style.display = 'none';
                    }
                }
            });

            // Change cursor on hover
            map.on('pointermove', function(evt) {
                const pixel = evt.pixel;
                const feature = map.forEachFeatureAtPixel(pixel, function(feature) {
                    return feature;
                });

                if (feature && feature.get('gisid')) {
                    map.getTargetElement().style.cursor = 'pointer';
                } else {
                    map.getTargetElement().style.cursor = '';
                }
            });

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

            console.log('Layers Refreshed Successfully with Labels and Popup');
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
