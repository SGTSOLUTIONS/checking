@extends('layouts.commissioner')

@section('title', 'Ward Map - ' . ($ward->ward_no ?? ''))

@section('content')
    <div class="container-fluid p-0">
        <div id="map"></div>

        <!-- Mobile Menu Buttons -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-layer-group"></i>
        </button>
        <button class="mobile-legend-btn" id="mobileLegendBtn">
            <i class="fas fa-info-circle"></i>
        </button>
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

        /* Mobile Menu Buttons */
        .mobile-menu-btn,
        .mobile-legend-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1002;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
            font-size: 20px;
        }

        .mobile-legend-btn {
            right: 80px;
            background: rgba(255, 193, 7, 0.9);
        }

        .mobile-menu-btn:active,
        .mobile-legend-btn:active {
            transform: scale(0.95);
        }

        @media (max-width: 768px) {
            .mobile-menu-btn,
            .mobile-legend-btn {
                display: flex;
            }
        }

        /* Layer Switcher - Desktop */
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
            transition: transform 0.3s ease;
        }

        /* Layer Switcher - Mobile */
        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: 80px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                transition: transform 0.3s ease;
                max-width: calc(100% - 40px);
                min-width: 200px;
                z-index: 1003;
            }

            .layer-switcher.open {
                transform: translateX(0);
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
            width: 16px;
            height: 16px;
        }

        .layer-group .group-title {
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 5px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Legend - Desktop */
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
            transition: transform 0.3s ease;
        }

        /* Legend - Mobile */
        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: 140px;
                right: 20px;
                top: auto;
                transform: translateX(120%);
                transition: transform 0.3s ease;
                min-width: 160px;
                pointer-events: auto;
                z-index: 1003;
            }

            .map-legend.open {
                transform: translateX(0);
                pointer-events: auto;
            }
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
            margin-bottom: 8px;
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

        .legend-color.popup {
            background: #1a1a2e;
            border: 1px solid #ff4444;
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

        @media (max-width: 768px) {
            .zoom-controls {
                bottom: 20px;
                left: 20px;
            }
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

        @media (max-width: 768px) {
            .ol-popup {
                max-width: 280px;
                max-height: 60vh;
            }
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
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            z-index: 1;
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .popup-close:hover,
        .popup-close:active {
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

        .popup-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
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
                const isControl = e.target.closest('.layer-switcher') || e.target.closest('.zoom-controls') ||
                                 e.target.closest('.mobile-menu-btn') || e.target.closest('.mobile-legend-btn') ||
                                 e.target.closest('.ol-popup');
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
            const polyData = polygonDatas.find(p => p.gisid == gisid);

            if (!polyData) {
                console.log('No data found for GIS ID:', gisid);
                return;
            }

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

                    if (assessment.shops && assessment.shops.length > 0) {
                        html += `<div class="popup-row"><span class="popup-label">Shops (${assessment.shops.length}):</span></div>`;
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

            // Drone Image Layer
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

                } catch (e) {
                    console.error('Error creating drone image layer:', e);
                    imageLayer = null;
                }
            } else {
                imageLayer = null;
            }

            // Boundary Layer
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

                } catch (e) {
                    console.error('Error creating boundary:', e);
                }
            }

            // Map Center
            let center = ol.proj.fromLonLat([80.2707, 13.0827]);
            let zoom = 24;

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

            addLayerSwitcher(hasDroneImage);
            addLegend(hasDroneImage);
            addZoomControls();
            addMobileControls();

            refreshLayers();

            showLoading(false);
        }

        function addLayerSwitcher(hasDroneImage) {
            const switcher = document.createElement('div');
            switcher.className = 'layer-switcher';
            switcher.id = 'layerSwitcher';
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
            legend.id = 'mapLegend';
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

        function addMobileControls() {
            const menuBtn = document.getElementById('mobileMenuBtn');
            const legendBtn = document.getElementById('mobileLegendBtn');
            const layerSwitcher = document.getElementById('layerSwitcher');
            const mapLegend = document.getElementById('mapLegend');

            if (menuBtn && layerSwitcher) {
                menuBtn.addEventListener('click', () => {
                    layerSwitcher.classList.toggle('open');
                    if (mapLegend) mapLegend.classList.remove('open');
                });
            }

            if (legendBtn && mapLegend) {
                legendBtn.addEventListener('click', () => {
                    mapLegend.classList.toggle('open');
                    if (layerSwitcher) layerSwitcher.classList.remove('open');
                });
            }

            // Close panels when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (layerSwitcher && !layerSwitcher.contains(e.target) && !menuBtn.contains(e.target)) {
                        layerSwitcher.classList.remove('open');
                    }
                    if (mapLegend && !mapLegend.contains(e.target) && !legendBtn.contains(e.target)) {
                        mapLegend.classList.remove('open');
                    }
                }
            });
        }

        function refreshLayers() {
            if (polygonLayer) map.removeLayer(polygonLayer);
            if (lineLayer) map.removeLayer(lineLayer);

            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);

            console.log(`Loading: ${polygons.length} polygons, ${lines.length} lines`);

            const polygonSource = new ol.source.Vector();
            polygons.forEach(function(poly) {
                try {
                    let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
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

            const lineSource = new ol.source.Vector();
            lines.forEach(function(line) {
                try {
                    let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line.coordinates;
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

            // Click handler for polygons
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

            // Hover cursor
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

            console.log('Layers Refreshed Successfully');
        }

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

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initMap();
            });
        } else {
            initMap();
        }
    </script>
@endpush
