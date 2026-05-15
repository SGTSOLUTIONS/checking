<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <title>Ward Map - Smart Navigation</title>
    <!-- OpenLayers & Font Awesome -->
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
            position: relative;
        }

        #map {
            width: 100%;
            height: 100vh;
            position: relative;
            touch-action: pan-x pan-y pinch-zoom;
        }

        /* ========= PANEL & BUTTONS ========= */
        .mobile-btn {
            position: fixed;
            z-index: 1002;
            backdrop-filter: blur(12px);
            color: white;
            border: none;
            border-radius: 50%;
            width: 52px;
            height: 52px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            font-size: 22px;
            transition: 0.2s ease;
        }

        .mobile-btn:active {
            transform: scale(0.94);
        }

        .menu-btn {
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
        }

        .legend-btn {
            bottom: 20px;
            right: 85px;
            background: rgba(255, 193, 7, 0.9);
        }

        .search-btn {
            bottom: 20px;
            right: 150px;
            background: rgba(23, 162, 184, 0.9);
        }

        .filter-btn {
            bottom: 20px;
            right: 215px;
            background: rgba(40, 167, 69, 0.9);
        }

        .location-btn {
            bottom: 20px;
            left: 20px;
            background: rgba(220, 53, 69, 0.9);
        }

        @media (max-width: 768px) {
            .mobile-btn {
                display: flex;
            }
        }

        .panel {
            background: rgba(0, 0, 0, 0.94);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 18px;
            color: white;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.5);
        }

        .panel h5 {
            margin: 0 0 12px 0;
            font-size: 16px;
            font-weight: 700;
            color: #ffc107;
            border-bottom: 2px solid #ff4444;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Layer Switcher (Desktop right, Mobile bottom-right slide) */
        .layer-switcher {
            position: absolute;
            top: 100px;
            right: 20px;
            min-width: 190px;
        }

        @media (max-width: 768px) {
            .layer-switcher {
                position: fixed;
                bottom: 90px;
                right: 20px;
                top: auto;
                transform: translateX(130%);
                opacity: 0;
                visibility: hidden;
            }

            .layer-switcher.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            min-width: 150px;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .map-legend {
                position: fixed;
                bottom: 150px;
                right: 20px;
                transform: translateX(130%);
                opacity: 0;
                visibility: hidden;
                pointer-events: auto;
            }

            .map-legend.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        .search-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 360px;
            max-width: calc(100% - 40px);
        }

        @media (max-width: 768px) {
            .search-panel {
                position: fixed;
                top: auto;
                bottom: 110px;
                left: 20px;
                right: 20px;
                width: auto;
                transform: translateY(150%);
                opacity: 0;
                visibility: hidden;
            }

            .search-panel.open {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }
        }

        .filter-panel {
            position: absolute;
            top: 100px;
            right: 20px;
            width: 280px;
        }

        @media (max-width: 768px) {
            .filter-panel {
                position: fixed;
                bottom: 110px;
                right: 20px;
                top: auto;
                transform: translateX(130%);
                opacity: 0;
                visibility: hidden;
            }

            .filter-panel.open {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 90px;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            border-radius: 40px;
            display: flex;
            z-index: 1000;
            overflow: hidden;
        }

        .zoom-btn {
            width: 48px;
            height: 48px;
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: 0.1s;
        }

        .zoom-btn:active {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Direction Panel */
        .direction-panel {
            position: fixed;
            bottom: 100px;
            left: 20px;
            right: 20px;
            max-width: 420px;
            display: none;
            z-index: 1003;
        }

        .direction-panel.show {
            display: block;
            animation: slideUp 0.25s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close-direction {
            float: right;
            background: none;
            border: none;
            color: #ff4444;
            font-size: 24px;
            cursor: pointer;
        }

        /* Popup (mobile bottom sheet, desktop absolute) */
        .ol-popup {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(145deg, #12121c, #1e1e2f);
            color: white;
            border-radius: 28px 28px 0 0 !important;
            width: 100% !important;
            max-height: 70vh;
            z-index: 9999 !important;
            overflow-y: auto;
            animation: slideUpPopup 0.3s ease-out;
        }

        @keyframes slideUpPopup {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @media (min-width: 769px) {
            .ol-popup {
                position: absolute !important;
                bottom: auto !important;
                width: auto !important;
                min-width: 380px;
                max-width: 500px;
                border-radius: 24px !important;
                animation: none;
            }

            .ol-popup:after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                border-width: 10px 10px 0;
                border-style: solid;
                border-color: #1e1e2f transparent transparent;
            }
        }

        .popup-header {
            background: #0f0f1a;
            padding: 16px 20px;
            border-bottom: 2px solid #ff4444;
            display: flex;
            justify-content: space-between;
        }

        .popup-tabs {
            display: flex;
            background: #181826;
        }

        .popup-tab {
            flex: 1;
            background: none;
            border: none;
            color: #aaa;
            padding: 12px;
            font-weight: 600;
        }

        .popup-tab.active {
            color: #ff4444;
            border-bottom: 3px solid #ff4444;
        }

        .popup-tab-content {
            display: none;
            padding: 18px;
            max-height: 55vh;
            overflow-y: auto;
        }

        .popup-tab-content.active {
            display: block;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 0;
        }

        .detail-label {
            width: 110px;
            color: #ffc107;
            font-weight: 600;
            font-size: 12px;
        }

        .detail-value {
            flex: 1;
            font-size: 13px;
        }

        .assessment-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            margin-bottom: 12px;
            border-left: 3px solid #ffc107;
            cursor: pointer;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
        }

        .badge-success {
            background: #28a745;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
        }

        .shop-item {
            background: rgba(255, 68, 68, 0.12);
            border-radius: 14px;
            padding: 12px;
            margin-top: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #aaa;
        }

        .search-result-item {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 3px solid #ffc107;
        }

        .direction-btn {
            margin-top: 8px;
            background: #28a745;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
        }

        @media (max-width: 768px) {
            .detail-label {
                width: 90px;
            }
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- Floating toggles -->
    <button class="mobile-btn menu-btn" id="mobileMenuBtn"><i class="fas fa-layer-group"></i></button>
    <button class="mobile-btn legend-btn" id="mobileLegendBtn"><i class="fas fa-info-circle"></i></button>
    <button class="mobile-btn search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
    <button class="mobile-btn filter-btn" id="mobileFilterBtn"><i class="fas fa-filter"></i></button>
    <button class="mobile-btn location-btn" id="mobileLocationBtn"><i class="fas fa-location-dot"></i></button>

    <div class="zoom-controls">
        <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
        <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script>
        $(function() {
            // ==================== MOCK DATA (simulating backend) ====================
            const wardData = {
                ward_no: "12",
                drone_image: null, // no drone for simplicity
                extent_left: null,
                extent_bottom: null,
                extent_right: null,
                extent_top: null,
                boundary: [
                    [
                        [80.2715, 13.0835],
                        [80.2728, 13.0822],
                        [80.2700, 13.0815],
                        [80.2690, 13.0830],
                        [80.2715, 13.0835]
                    ]
                ] // ward polygon approx
            };

            // building polygons (mock)
            const polygons = [{
                    gisid: "B001",
                    coordinates: [
                        [
                            [80.2707, 13.0829],
                            [80.2710, 13.0829],
                            [80.2710, 13.0832],
                            [80.2707, 13.0832],
                            [80.2707, 13.0829]
                        ]
                    ],
                    sqfeet: 1250
                },
                {
                    gisid: "B002",
                    coordinates: [
                        [
                            [80.2715, 13.0825],
                            [80.2719, 13.0825],
                            [80.2719, 13.0829],
                            [80.2715, 13.0829],
                            [80.2715, 13.0825]
                        ]
                    ],
                    sqfeet: 980
                },
                {
                    gisid: "B003",
                    coordinates: [
                        [
                            [80.2700, 13.0835],
                            [80.2705, 13.0835],
                            [80.2705, 13.0839],
                            [80.2700, 13.0839],
                            [80.2700, 13.0835]
                        ]
                    ],
                    sqfeet: 2100
                }
            ];
            const lines = []; // roads mock (optional)

            const polygonDatas = [{
                    gisid: "B001",
                    building_usage: "Residential",
                    building_type: "House",
                    number_floor: 2,
                    road_name: "Main Street",
                    zone: "A",
                    number_bill: 3,
                    total_shops: 1,
                    pointdata: [{
                        assessment: "AS101",
                        owner_name: "Rajesh Kumar",
                        phone_number: "9876543210",
                        floor: "1",
                        bill_usage: "Self",
                        shops: [{
                            shop_name: "General Store",
                            shop_category: "Retail",
                            shop_owner_name: "Rajesh",
                            shop_mobile: "9876543210"
                        }],
                        qcsqfeet: 1200,
                        qcusage: "Residential"
                    }]
                },
                {
                    gisid: "B002",
                    building_usage: "Commercial",
                    building_type: "Shop",
                    number_floor: 1,
                    road_name: "Market Road",
                    zone: "B",
                    number_bill: 1,
                    total_shops: 2,
                    pointdata: [{
                        assessment: "AS202",
                        owner_name: "Meera Gupta",
                        phone_number: "9988776655",
                        floor: "G",
                        bill_usage: "Rent",
                        shops: [{
                            shop_name: "Bakery",
                            shop_category: "Food",
                            shop_owner_name: "Meera",
                            shop_mobile: "9988776655"
                        }],
                        qcsqfeet: null,
                        qcusage: null
                    }]
                },
                {
                    gisid: "B003",
                    building_usage: "Mixed",
                    building_type: "Apartment",
                    number_floor: 3,
                    road_name: "Park Lane",
                    zone: "A",
                    number_bill: 5,
                    total_shops: 0,
                    pointdata: [{
                        assessment: "AS303",
                        owner_name: "Sundar Pichai",
                        phone_number: "9123456789",
                        floor: "2",
                        bill_usage: "Residential",
                        shops: [],
                        qcsqfeet: 1800,
                        qcusage: "Residential"
                    }]
                }
            ];

            // ==================== GLOBAL ====================
            let map, polygonLayer, lineLayer, boundaryLayer, osmLayer, satelliteLayer, imageLayer;
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';
            let allBuildings = [];
            let currentPosition = null;
            let currentLocationLayer = null,
                accuracyLayer = null;
            let locationTracking = false,
                watchId = null;
            let directionLineLayer = null,
                destinationMarkerLayer = null;

            function buildSearchIndex() {
                allBuildings = [];
                polygonDatas.forEach(b => {
                    let polyInfo = polygons.find(p => p.gisid === b.gisid);
                    let coords = null;
                    if (polyInfo && polyInfo.coordinates && polyInfo.coordinates[0] && polyInfo.coordinates[
                            0].length) {
                        let sumX = 0,
                            sumY = 0,
                            pts = polyInfo.coordinates[0];
                        pts.forEach(c => {
                            sumX += c[0];
                            sumY += c[1];
                        });
                        coords = [sumX / pts.length, sumY / pts.length];
                    }
                    allBuildings.push({
                        gisid: b.gisid,
                        building_usage: b.building_usage,
                        road_name: b.road_name,
                        zone: b.zone,
                        number_floor: b.number_floor,
                        coordinates: coords,
                        assessments: (b.pointdata || []).map(a => ({
                            assessment_no: a.assessment,
                            owner_name: a.owner_name,
                            phone: a.phone_number
                        }))
                    });
                });
            }

            // ==================== LOCATION ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    alert("Geolocation not supported");
                    return;
                }
                $('#mobileLocationBtn').css('background', '#28a745');
                locationTracking = true;
                navigator.geolocation.getCurrentPosition(pos => updateLocationOnMap(pos.coords.longitude, pos.coords
                    .latitude, pos.coords.accuracy), err => {
                    alert("Location error");
                    stopLocationTracking();
                });
                watchId = navigator.geolocation.watchPosition(pos => updateLocationOnMap(pos.coords.longitude, pos
                    .coords.latitude, pos.coords.accuracy), null, {
                    enableHighAccuracy: true,
                    maximumAge: 5000
                });
            }

            function stopLocationTracking() {
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                locationTracking = false;
                $('#mobileLocationBtn').css('background', 'rgba(220,53,69,0.9)');
            }

            function updateLocationOnMap(lon, lat, acc) {
                let coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];
                if (currentLocationLayer) map.removeLayer(currentLocationLayer);
                if (accuracyLayer) map.removeLayer(accuracyLayer);
                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, acc)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,68,68,0.15)'
                        })
                    })
                });
                map.addLayer(accuracyLayer);
                currentLocationLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            fill: new ol.style.Fill({
                                color: '#ff4444'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#fff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(currentLocationLayer);
                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            // ==================== DIRECTION ====================
            function haversineDistance(lon1, lat1, lon2, lat2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180,
                    dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI /
                    180) * Math.sin(dLon / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function showDirectionToBuilding(gisid, lonLat) {
                if (!currentPosition) {
                    alert("Enable location tracking first");
                    startLocationTracking();
                    return;
                }
                if (directionLineLayer) map.removeLayer(directionLineLayer);
                if (destinationMarkerLayer) map.removeLayer(destinationMarkerLayer);
                let fromProj = ol.proj.fromLonLat(currentPosition);
                let toProj = ol.proj.fromLonLat(lonLat);
                directionLineLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.LineString([fromProj, toProj])
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#28a745',
                            width: 4,
                            lineDash: [8, 8]
                        })
                    })
                });
                destinationMarkerLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Point(toProj)
                        })]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 14,
                            fill: new ol.style.Fill({
                                color: '#28a745'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#fff',
                                width: 3
                            })
                        })
                    })
                });
                map.addLayer(directionLineLayer);
                map.addLayer(destinationMarkerLayer);
                let dist = haversineDistance(currentPosition[0], currentPosition[1], lonLat[0], lonLat[1]);
                let html = `<div class="direction-panel panel show" id="directionPanel">
                    <button class="close-direction" onclick="$('#directionPanel').remove();">&times;</button>
                    <h5><i class="fas fa-directions"></i> Direction to ${gisid}</h5>
                    <div class="direction-info"><p><strong>Distance:</strong> ${dist.toFixed(2)} km</p><p><strong>Walking:</strong> ${Math.round(dist/5*60)} min</p><p><strong>Driving:</strong> ${Math.round(dist/40*60)} min</p></div>
                    <button id="fitBothBtn" style="width:100%; background:#ff4444; border:none; padding:10px; border-radius:12px; color:white; margin-top:10px;"><i class="fas fa-map-marked-alt"></i> Show Full Route</button>
                </div>`;
                $('#directionPanel').remove();
                $('body').append(html);
                $('#fitBothBtn').on('click', () => {
                    map.getView().fit(ol.extent.boundingExtent([fromProj, toProj]), {
                        padding: [60, 60, 60, 60],
                        duration: 800
                    });
                });
                map.getView().fit(ol.extent.boundingExtent([fromProj, toProj]), {
                    padding: [60, 60, 60, 60],
                    duration: 800
                });
            }

            // ==================== POPUP ====================
            function createPopup() {
                popupElement = $('<div class="ol-popup" style="display:none"></div>')[0];
                document.body.appendChild(popupElement);
                return new ol.Overlay({
                    element: popupElement,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -10]
                });
            }
            window.closePopup = function() {
                $('.ol-popup').hide();
            };
            window.switchTab = function(tab) {
                $('.popup-tab-content, .popup-tab').removeClass('active');
                $(`#tab-${tab}`).addClass('active');
                $(`.popup-tab[data-tab="${tab}"]`).addClass('active');
                currentActiveTab = tab;
            };

            function showPopup(gisid, coord) {
                let bld = polygonDatas.find(p => p.gisid == gisid);
                if (!bld) return;
                let assessments = bld.pointdata || [];
                let shops = [];
                assessments.forEach(a => {
                    if (a.shops) a.shops.forEach(s => shops.push({
                        ...s,
                        assessmentNumber: a.assessment
                    }));
                });
                let buildingHtml =
                    `<div>${[
                    ['fingerprint','GIS ID',bld.gisid],['building','Usage',bld.building_usage],['home','Type',bld.building_type],
                    ['layer-group','Floors',bld.number_floor],['road','Road',bld.road_name],['map-pin','Zone',bld.zone]
                ].map(([i,l,v])=>`<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v||'N/A'}</div></div>`).join('')}</div>`;
                let assessmentsHtml = assessments.length ? assessments.map((a, i) => `<div class="assessment-card" data-assessment-id="${a.assessment || ''}" data-gisid="${bld.gisid}">
                    <div class="assessment-header" style="display:flex; justify-content:space-between; padding:10px;"><span><i class="fas fa-file-invoice"></i> ${a.assessment || 'Bill'}</span><span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span></div>
                    <div class="assessment-body" style="padding:10px">${[['Owner',a.owner_name],['Phone',a.phone_number],['Floor',a.floor],['Usage',a.bill_usage]].map(([l,v])=>`<div class="detail-row"><div class="detail-label">${l}:</div><div class="detail-value">${v||'N/A'}</div></div>`).join('')}</div>
                </div>`).join('') : '<div class="empty-state">No assessments</div>';
                let shopsHtml = shops.length ? shops.map(s =>
                    `<div class="shop-item"><div class="shop-name"><i class="fas fa-store"></i> ${s.shop_name}</div><div>${s.shop_category} | ${s.shop_owner_name}</div></div>`
                    ).join('') : '<div class="empty-state">No shops</div>';
                let html =
                    `<div class="popup-header"><h4><i class="fas fa-building"></i> ${bld.gisid}</h4><button class="popup-close" onclick="closePopup()">&times;</button></div>
                <div class="popup-tabs">
                    <button class="popup-tab ${currentActiveTab=='building'?'active':''}" data-tab="building" onclick="switchTab('building')">Building</button>
                    <button class="popup-tab ${currentActiveTab=='assessments'?'active':''}" data-tab="assessments" onclick="switchTab('assessments')">Assessments</button>
                    <button class="popup-tab ${currentActiveTab=='shops'?'active':''}" data-tab="shops" onclick="switchTab('shops')">Shops</button>
                </div>
                <div id="tab-building" class="popup-tab-content ${currentActiveTab=='building'?'active':''}">${buildingHtml}</div>
                <div id="tab-assessments" class="popup-tab-content ${currentActiveTab=='assessments'?'active':''}">${assessmentsHtml}</div>
                <div id="tab-shops" class="popup-tab-content ${currentActiveTab=='shops'?'active':''}">${shopsHtml}</div>`;
                $(popupElement).html(html).show();
                if ($(window).width() > 768 && popupOverlay) popupOverlay.setPosition(coord);
                $('.assessment-card').on('click', function() {
                    alert(`QC details: tap to edit form (simulated)`);
                });
            }

            // ==================== STYLE & LAYERS ====================
            function polygonStyleFn(feature) {
                let gisid = feature.get('gisid'),
                    sq = feature.get('sqfeet');
                let geom = feature.getGeometry();
                let center;
                try {
                    center = geom.getInteriorPoint();
                } catch (e) {
                    let ext = geom.getExtent();
                    center = new ol.geom.Point([(ext[0] + ext[2]) / 2, (ext[1] + ext[3]) / 2]);
                }
                if (feature.get('visible') === false) return null;
                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ff4444',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgba(255,68,68,0.2)'
                        })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}\n${sq || 0}ft²`,
                            font: 'bold 10px Arial',
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            stroke: new ol.style.Stroke({
                                color: '#000',
                                width: 2
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: 'rgba(0,0,0,0.6)'
                            }),
                            padding: [3, 6, 3, 6]
                        })
                    })
                ];
            }

            function refreshLayers() {
                if (polygonLayer) map.removeLayer(polygonLayer);
                if (lineLayer) map.removeLayer(lineLayer);
                let ps = new ol.source.Vector();
                polygons.forEach(p => {
                    try {
                        ps.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(p.coordinates),
                            gisid: p.gisid,
                            sqfeet: p.sqfeet,
                            visible: true
                        }));
                    } catch (e) {}
                });
                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFn,
                    visible: true
                });
                let ls = new ol.source.Vector();
                lines.forEach(l => {
                    try {
                        ls.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(l.coordinates)
                        }));
                    } catch (e) {}
                });
                lineLayer = new ol.layer.Vector({
                    source: ls,
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
                map.on('click', e => {
                    let feature = map.forEachFeatureAtPixel(e.pixel, f => f);
                    if (feature && feature.get('gisid')) showPopup(feature.get('gisid'), e.coordinate);
                    else if (popupElement) $(popupElement).hide();
                });
            }

            // ==================== MAP INIT ====================
            function initMap() {
                osmLayer = new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    visible: true
                });
                satelliteLayer = new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    }),
                    visible: false
                });
                let boundaryFeature = null;
                if (wardData.boundary && wardData.boundary[0]) {
                    let coords3857 = wardData.boundary[0].map(c => ol.proj.fromLonLat(c));
                    boundaryLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.Feature({
                                geometry: new ol.geom.Polygon([coords3857])
                            })]
                        }),
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: '#ff0000',
                                width: 3,
                                lineDash: [8, 6]
                            }),
                            fill: new ol.style.Fill({
                                color: 'rgba(255,0,0,0.05)'
                            })
                        }),
                        visible: true
                    });
                }
                let center = ol.proj.fromLonLat([80.2710, 13.0828]);
                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: center,
                        zoom: 17
                    })
                });
                if (boundaryLayer) map.addLayer(boundaryLayer);
                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);
                refreshLayers();

                // Append UI Panels
                $('body').append(
                    `<div class="layer-switcher panel" id="layerSwitcher"><h5><i class="fas fa-layer-group"></i> Layers</h5><div class="layer-group"><div class="group-title">Base</div><label><input type="radio" name="baseLayer" value="osm" checked> OSM</label><label><input type="radio" name="baseLayer" value="satellite"> Satellite</label></div><div class="layer-group"><div class="group-title">Overlays</div><label><input type="checkbox" id="toggleBuildings" checked> Buildings</label><label><input type="checkbox" id="toggleRoads" checked> Roads</label><label><input type="checkbox" id="toggleBoundary" checked> Ward Boundary</label></div></div>`
                    );
                $('body').append(
                    `<div class="map-legend panel" id="mapLegend"><h5><i class="fas fa-info-circle"></i> Legend</h5><div class="legend-item"><div style="width:24px;height:24px;background:rgba(255,68,68,0.5);border:1px solid #ff4444;"></div><span>Buildings</span></div><div class="legend-item"><div style="width:24px;height:3px;background:#ffc107;"></div><span>Roads</span></div><div class="legend-item"><div style="width:24px;height:3px;border:1px dashed red;"></div><span>Boundary</span></div></div>`
                    );
                $('body').append(
                    `<div class="search-panel panel" id="searchPanel"><h5><i class="fas fa-search"></i> Search</h5><div class="search-box"><input type="text" id="searchInput" placeholder="GIS ID / Owner / Assessment"><button id="searchBtn">Go</button></div><div id="searchResults" class="search-results"></div></div>`
                    );
                $('body').append(
                    `<div class="filter-panel panel" id="filterPanel"><h5><i class="fas fa-filter"></i> Filter</h5><div class="filter-group"><label>QC Status</label><select id="filterType"><option value="all">All</option><option value="completed">QC Complete</option><option value="pending">QC Pending</option></select></div><div><label>Min Floors</label><input type="number" id="filterMinFloors" placeholder="Min"></div><div style="margin-top:8px"><label>Max Floors</label><input type="number" id="filterMaxFloors" placeholder="Max"></div><div class="filter-actions" style="margin-top:15px; display:flex; gap:8px"><button class="apply-btn" id="applyFilterBtn">Apply</button><button class="reset-btn" id="resetFilterBtn">Reset</button></div><div class="filter-count" id="filterCount"></div></div>`
                    );

                // event bindings
                $('input[name="baseLayer"]').change(function() {
                    osmLayer.setVisible($(this).val() === 'osm');
                    satelliteLayer.setVisible($(this).val() === 'satellite');
                });
                $('#toggleBuildings').change(function() {
                    if (polygonLayer) polygonLayer.setVisible($(this).is(':checked'));
                });
                $('#toggleRoads').change(function() {
                    if (lineLayer) lineLayer.setVisible($(this).is(':checked'));
                });
                $('#toggleBoundary').change(function() {
                    if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked'));
                });
                $('#searchBtn').click(() => {
                    let txt = $('#searchInput').val();
                    if (txt) searchBuildings(txt);
                });
                $('#applyFilterBtn').click(applyFilters);
                $('#resetFilterBtn').click(resetFilters);
                $('#zoomInBtn').click(() => map.getView().setZoom(map.getView().getZoom() + 1));
                $('#zoomOutBtn').click(() => map.getView().setZoom(map.getView().getZoom() - 1));
                // mobile panels
                $('#mobileMenuBtn').click(e => {
                    e.stopPropagation();
                    closeAllPanels();
                    $('#layerSwitcher').toggleClass('open');
                });
                $('#mobileLegendBtn').click(e => {
                    e.stopPropagation();
                    closeAllPanels();
                    $('#mapLegend').toggleClass('open');
                });
                $('#mobileSearchBtn').click(e => {
                    e.stopPropagation();
                    closeAllPanels();
                    $('#searchPanel').toggleClass('open');
                    if ($('#searchPanel').hasClass('open')) setTimeout(() => $('#searchInput').focus(),
                    200);
                });
                $('#mobileFilterBtn').click(e => {
                    e.stopPropagation();
                    closeAllPanels();
                    $('#filterPanel').toggleClass('open');
                });
                $('#mobileLocationBtn').click(() => {
                    if (locationTracking) stopLocationTracking();
                    else startLocationTracking();
                });
                $(document).on('click touchstart', function(e) {
                    if ($(window).width() <= 768 && !$(e.target).closest('.panel, .mobile-btn').length)
                        closeAllPanels();
                });
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel').removeClass('open');
            }

            function searchBuildings(term) {
                let lower = term.toLowerCase();
                let results = allBuildings.filter(b => b.gisid.toLowerCase().includes(lower) || (b.assessments.some(
                    a => a.owner_name?.toLowerCase().includes(lower))));
                let $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html('<div class="empty-state">No buildings</div>');
                    return;
                }
                results.forEach(r => {
                    let coords = r.coordinates;
                    $res.append(
                        `<div class="search-result-item" data-gisid="${r.gisid}" data-lon="${coords?coords[0]:''}" data-lat="${coords?coords[1]:''}"><div><strong>${r.gisid}</strong> | ${r.building_usage}</div><div>${r.road_name}</div><button class="direction-btn" data-gisid="${r.gisid}" data-lon="${coords?coords[0]:''}" data-lat="${coords?coords[1]:''}"><i class="fas fa-directions"></i> Directions</button></div>`
                        );
                });
                $('.search-result-item').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn')) zoomToBuilding($(this).data('gisid'));
                });
                $('.direction-btn').on('click', function(e) {
                    e.stopPropagation();
                    let lon = $(this).data('lon'),
                        lat = $(this).data('lat');
                    if (lon && lat) showDirectionToBuilding($(this).data('gisid'), [parseFloat(lon),
                        parseFloat(lat)
                    ]);
                    else alert("No coordinates");
                });
            }

            function zoomToBuilding(gisid) {
                let feat = polygonLayer.getSource().getFeatures().find(f => f.get('gisid') == gisid);
                if (feat) {
                    map.getView().fit(feat.getGeometry().getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 700
                    });
                    showPopup(gisid, ol.extent.getCenter(feat.getGeometry().getExtent()));
                } else alert("Not found");
            }

            function applyFilters() {
                let type = $('#filterType').val();
                let min = parseInt($('#filterMinFloors').val()) || 0;
                let max = parseInt($('#filterMaxFloors').val()) || 999;
                let features = polygonLayer.getSource().getFeatures();
                let count = 0;
                features.forEach(f => {
                    let gis = f.get('gisid');
                    let bdata = polygonDatas.find(p => p.gisid == gis);
                    let visible = true;
                    if (type !== 'all' && bdata) {
                        let hasQC = (bdata.pointdata || []).some(a => a.qcsqfeet || a.qcusage);
                        if (type === 'completed' && !hasQC) visible = false;
                        if (type === 'pending' && hasQC) visible = false;
                    }
                    let floors = bdata ? parseInt(bdata.number_floor) || 0 : 0;
                    if (floors < min || floors > max) visible = false;
                    f.set('visible', visible);
                    if (visible) count++;
                });
                polygonLayer.setStyle(polygonStyleFn);
                polygonLayer.changed();
                $('#filterCount').text(`Showing ${count} of ${features.length}`);
                closeAllPanels();
            }

            function resetFilters() {
                $('#filterType').val('all');
                $('#filterMinFloors,#filterMaxFloors').val('');
                let features = polygonLayer.getSource().getFeatures();
                features.forEach(f => f.set('visible', true));
                polygonLayer.setStyle(polygonStyleFn);
                polygonLayer.changed();
                $('#filterCount').text(`Showing ${features.length} of ${features.length}`);
                closeAllPanels();
            }
            buildSearchIndex();
            initMap();
        });
    </script>
</body>

</html>
