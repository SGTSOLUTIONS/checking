@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        $(document).ready(function() {
            // ==================== DATA FROM SERVER ====================
            let polygonDatas = @json($polygonDatas ?? []);
            let polygons = @json($polygons ?? []);
            let lines = @json($lines ?? []);
            let points = @json($points ?? []);
            let wardData = {
                ward_no: @json($ward->ward_no ?? ''),
                drone_image: @json($ward->drone_image ?? null),
                extent_left: @json($ward->extent_left ?? null),
                extent_bottom: @json($ward->extent_bottom ?? null),
                extent_right: @json($ward->extent_right ?? null),
                extent_top: @json($ward->extent_top ?? null),
                boundary: @json($ward->boundary ?? null)
            };

            // ==================== MAP VARIABLES ====================
            let map, polygonLayer, lineLayer, imageLayer, boundaryLayer, osmLayer, satelliteLayer;
            let currentBaseLayer = 'osm';
            let popupOverlay, popupElement;
            let currentActiveTab = 'building';

            // ==================== LOCATION VARIABLES ====================
            let currentLocationLayer = null,
                accuracyLayer = null,
                currentPosition = null;
            let locationTracking = false,
                watchId = null;

            // ==================== ROUTE VARIABLES ====================
            let currentRoute = null;
            let routeSteps = [];
            let routeSource = null;
            let routeLayer = null;
            let destinationMarker = null;
            let selectedBuilding = null;

            // ==================== SEARCH VARIABLES ====================
            let allBuildings = [];

            // Helper function to check if OpenLayers is loaded
            function isOLReady() {
                return typeof ol !== 'undefined' && ol.proj && ol.source && ol.layer;
            }

            // ==================== BUILDING USAGE COLORS ====================
            const usageColors = {
                'RESIDENTIAL': '#4CAF50',
                'COMMERCIAL': '#2196F3',
                'INDUSTRIAL': '#FF9800',
                'INSTITUTIONAL': '#9C27B0',
                'MIXED': '#FF5722',
                'GOVERNMENT': '#607D8B',
                'VACANT': '#9E9E9E',
                'EDUCATIONAL': '#00BCD4',
                'HOSPITAL': '#E91E63',
                'HOTEL': '#795548',
                'RELIGIOUS': '#FFC107',
                'default': '#ff4444'
            };

            function getBuildingColor(buildingUsage) {
                if (!buildingUsage) return usageColors.default;
                const upperUsage = buildingUsage.toUpperCase();
                for (const [key, color] of Object.entries(usageColors)) {
                    if (upperUsage.includes(key) || key === upperUsage) {
                        return color;
                    }
                }
                return usageColors.default;
            }

            // ==================== HELPER FUNCTIONS ====================
            function showLoading(show) {
                if (show) {
                    if ($('#mapLoading').length === 0) {
                        $('body').append(
                            '<div id="mapLoading" class="map-loading"><i class="fas fa-spinner fa-spin"></i> Loading map...</div>'
                        );
                    }
                    $('#mapLoading').show();
                } else {
                    $('#mapLoading').hide();
                }
            }

            function showToast(message, type = 'info') {
                const alertClass = {
                    'success': '#28a745',
                    'error': '#dc3545',
                    'warning': '#ffc107',
                    'info': '#17a2b8'
                } [type] || '#17a2b8';

                const toastId = 'toast_' + Date.now();
                const flashHtml = `<div id="${toastId}" class="alert alert-dismissible fade show position-fixed" style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000; background: ${alertClass}; color: white; padding: 12px 20px; border-radius: 10px; min-width: 200px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">${message}<button type="button" class="btn-close btn-close-white" style="float: right; margin-left: 10px; background: none; border: none; color: white; font-size: 20px;" onclick="$('#${toastId}').remove()">&times;</button></div>`;
                $('body').append(flashHtml);
                setTimeout(() => $(`#${toastId}`).fadeOut(300, function() {
                    $(this).remove();
                }), 3000);
            }

            function closeAllPanels() {
                $('#layerSwitcher, #mapLegend, #searchPanel, #filterPanel, #routeInfo').removeClass('open');
            }

            // ==================== FORMATTING FUNCTIONS ====================
            function formatDistance(meters) {
                if (!meters || isNaN(meters)) return '0 m';
                if (meters < 1000) return Math.round(meters) + ' m';
                return (meters / 1000).toFixed(2) + ' km';
            }

            function formatDuration(seconds) {
                if (!seconds || isNaN(seconds)) return '0 min';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + ' min';
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return hours + 'h ' + mins + 'm';
            }

            // ==================== ROUTE FUNCTIONS ====================
            async function getRouteFromOSRM(startCoord, endCoord) {
                try {
                    // OSRM expects [longitude, latitude] format
                    const [startLon, startLat] = startCoord;
                    const [endLon, endLat] = endCoord;

                    // FIXED: Correct coordinate order for OSRM
                    const url = `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true&alternatives=false`;

                    console.log('Fetching route from OSRM:', url);
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        throw new Error('No route found from OSRM');
                    }
                    return data.routes[0];
                } catch (error) {
                    console.warn('OSRM route failed:', error);
                    return getStraightLineRoute(startCoord, endCoord);
                }
            }

            function getStraightLineRoute(startCoord, endCoord) {
                // Convert from lon/lat to meters for distance calculation
                const R = 6371000; // Earth's radius in meters
                const toRad = (deg) => deg * Math.PI / 180;

                const [lon1, lat1] = startCoord;
                const [lon2, lat2] = endCoord;

                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                const distance = R * c;
                const duration = (distance / 1000 / 30) * 60; // 30 km/h average speed

                return {
                    distance: distance,
                    duration: duration,
                    geometry: {
                        type: "LineString",
                        coordinates: [startCoord, endCoord]
                    },
                    legs: [{
                        steps: [{
                            maneuver: { type: "depart", instruction: "Start from your location" },
                            distance: distance,
                            duration: duration
                        }, {
                            maneuver: { type: "arrive", instruction: "Arrive at destination" },
                            distance: 0,
                            duration: 0
                        }]
                    }]
                };
            }

            function parseOSRMSteps(route) {
                const steps = [];
                if (route.legs && route.legs[0] && route.legs[0].steps) {
                    route.legs[0].steps.forEach((step, index) => {
                        let instruction = step.maneuver.instruction || step.maneuver.type;
                        let icon = 'fas fa-arrow-right';
                        switch (step.maneuver.type) {
                            case 'depart': icon = 'fas fa-play'; break;
                            case 'arrive': icon = 'fas fa-flag-checkered'; break;
                            case 'turn':
                                if (step.maneuver.modifier === 'left') icon = 'fas fa-arrow-left';
                                else if (step.maneuver.modifier === 'right') icon = 'fas fa-arrow-right';
                                else icon = 'fas fa-turn-up';
                                break;
                            default: icon = 'fas fa-road';
                        }
                        steps.push({
                            instruction: instruction,
                            distance: formatDistance(step.distance),
                            icon: icon,
                            type: step.maneuver.type,
                            name: step.name || ''
                        });
                    });
                }
                return steps;
            }

            function drawRouteOnMap(geometry) {
                if (!map || !ol || !ol.source || !ol.layer) {
                    console.error('Map or OpenLayers not ready');
                    return;
                }

                if (routeLayer) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                if (routeSource) {
                    routeSource.clear();
                }

                routeSource = new ol.source.Vector();
                routeLayer = new ol.layer.Vector({
                    source: routeSource,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#0066cc',
                            width: 5,
                            lineDash: [10, 8]
                        })
                    }),
                    zIndex: 1000
                });

                if (geometry && geometry.type === 'LineString' && geometry.coordinates && geometry.coordinates.length >= 2) {
                    try {
                        // Convert geographic coordinates to map projection
                        const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                        const lineString = new ol.geom.LineString(coordinates);
                        const feature = new ol.Feature({ geometry: lineString });
                        routeSource.addFeature(feature);
                        map.addLayer(routeLayer);

                        // Fit view to route
                        if (routeSource.getFeatures().length > 0) {
                            const extent = routeSource.getExtent();
                            if (extent && isFinite(extent[0]) && isFinite(extent[1]) &&
                                isFinite(extent[2]) && isFinite(extent[3])) {
                                map.getView().fit(extent, {
                                    padding: [80, 80, 80, 80],
                                    duration: 800,
                                    maxZoom: 18
                                });
                            }
                        }
                        console.log('Route drawn successfully');
                    } catch (error) {
                        console.error('Error drawing route:', error);
                    }
                } else {
                    console.warn('Invalid route geometry:', geometry);
                }
            }

            async function calculateAndDisplayRoute(startCoord, endCoord, destinationName, buildingGisid = null) {
                $('#loadingSpinner').css('display', 'flex');

                try {
                    if (!endCoord || endCoord.length < 2) {
                        throw new Error('Invalid destination coordinates');
                    }

                    if (!startCoord || startCoord.length < 2) {
                        throw new Error('Current location not available. Please enable location tracking.');
                    }

                    let endLon = parseFloat(endCoord[0]);
                    let endLat = parseFloat(endCoord[1]);
                    let startLon = parseFloat(startCoord[0]);
                    let startLat = parseFloat(startCoord[1]);

                    if (isNaN(endLon) || isNaN(endLat) || isNaN(startLon) || isNaN(startLat)) {
                        throw new Error('Invalid coordinate values');
                    }

                    const startGeographic = [startLon, startLat];
                    const endGeographic = [endLon, endLat];

                    console.log("Calculating route from:", startGeographic, "to:", endGeographic);

                    const route = await getRouteFromOSRM(startGeographic, endGeographic);
                    const totalDistance = route.distance;
                    const totalDuration = route.duration;

                    routeSteps = parseOSRMSteps(route);
                    currentRoute = {
                        distance: totalDistance,
                        duration: totalDuration,
                        geometry: route.geometry,
                        endCoord: endGeographic,
                        placeName: destinationName,
                        gisid: buildingGisid
                    };

                    drawRouteOnMap(route.geometry);

                    $('#routeSummary').html(`
                        <div><strong>Total Distance:</strong> ${formatDistance(totalDistance)}</div>
                        <div><strong>Estimated Time:</strong> ${formatDuration(totalDuration)}</div>
                        <div><strong>Destination:</strong> ${escapeHtml(destinationName)}</div>
                        <div><strong>Start:</strong> Your Location</div>
                    `);

                    const directionsList = $('#directionsList');
                    directionsList.empty();

                    if (routeSteps.length === 0) {
                        directionsList.append('<div class="direction-step">No detailed directions available</div>');
                    } else {
                        routeSteps.forEach((step, index) => {
                            directionsList.append(`
                                <div class="direction-step">
                                    <div class="step-number">${index + 1}</div>
                                    <div class="step-content">
                                        <div class="step-instruction"><i class="${step.icon} me-2"></i> ${escapeHtml(step.instruction)}</div>
                                        <div class="step-distance">${step.distance}</div>
                                        ${step.name ? `<div class="step-name" style="font-size: 10px; color: #aaa;">${escapeHtml(step.name)}</div>` : ''}
                                    </div>
                                </div>
                            `);
                        });
                    }

                    // Add destination marker
                    if (destinationMarker) {
                        map.removeLayer(destinationMarker);
                        destinationMarker = null;
                    }

                    const destProjected = ol.proj.fromLonLat(endGeographic);
                    const destLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.Feature({
                                geometry: new ol.geom.Point(destProjected)
                            })]
                        }),
                        style: new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 14,
                                fill: new ol.style.Fill({ color: '#ff4444' }),
                                stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
                            })
                        }),
                        zIndex: 1001
                    });
                    map.addLayer(destLayer);
                    destinationMarker = destLayer;

                    $('#routeInfo').addClass('open');
                    showToast('Route calculated successfully!', 'success');

                } catch (error) {
                    console.error('Route calculation error:', error);
                    showToast('Error calculating route: ' + error.message, 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            function getRouteToBuilding(gisid, targetCoords) {
                if (!targetCoords || targetCoords.length < 2) {
                    showToast('Invalid building coordinates', 'error');
                    return;
                }

                let lon = parseFloat(targetCoords[0]);
                let lat = parseFloat(targetCoords[1]);

                if (isNaN(lon) || isNaN(lat)) {
                    showToast('Invalid coordinate values', 'error');
                    return;
                }

                if (!currentPosition) {
                    showToast('Please enable your location first by clicking the Location button', 'warning');
                    startLocationTracking();
                    return;
                }

                console.log("Getting route to building:", gisid, "at coordinates:", lon, lat);
                calculateAndDisplayRoute(currentPosition, [lon, lat], `Building GIS ID: ${gisid}`, gisid);
            }

            function clearRoute() {
                if (routeLayer && map) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                if (destinationMarker && map) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }
                if (routeSource) {
                    routeSource.clear();
                }
                currentRoute = null;
                $('#routeInfo').removeClass('open');
            }

            function startNavigation() {
                if (currentRoute && currentRoute.endCoord && currentPosition) {
                    const [lon, lat] = currentRoute.endCoord;
                    const url = `https://www.google.com/maps/dir/${currentPosition[1]},${currentPosition[0]}/${lat},${lon}`;
                    window.open(url, '_blank');
                } else {
                    showToast('No route available for navigation', 'warning');
                }
            }

            // ==================== BUILD SEARCH INDEX ====================
            function buildSearchIndex() {
                allBuildings = [];
                $.each(polygonDatas, function(i, building) {
                    let info = {
                        gisid: building.gisid,
                        building_usage: building.building_usage,
                        building_type: building.building_type,
                        road_name: building.road_name,
                        zone: building.zone,
                        number_floor: building.number_floor,
                        coordinates: null,
                        assessments: []
                    };

                    if (building.pointdata && building.pointdata.length) {
                        $.each(building.pointdata, function(j, assessment) {
                            info.assessments.push({
                                id: assessment.id,
                                assessment: assessment.assessment,
                                owner_name: assessment.owner_name || assessment.present_owner_name,
                                phone: assessment.phone || assessment.phone_number,
                                bill_usage: assessment.bill_usage,
                                floor: assessment.floor,
                                qcsqfeet: assessment.qcsqfeet,
                                qcusage: assessment.qcusage
                            });
                        });
                    }

                    // Find centroid coordinates for the building
                    $.each(polygons, function(j, poly) {
                        if (poly.gisid == building.gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0, cy = 0, count = 0;
                                    $.each(coords[0], function(k, c) {
                                        if (c && c.length >= 2 && !isNaN(c[0]) && !isNaN(c[1])) {
                                            cx += parseFloat(c[0]);
                                            cy += parseFloat(c[1]);
                                            count++;
                                        }
                                    });
                                    if (count > 0) {
                                        info.coordinates = [cx / count, cy / count];
                                    }
                                }
                            } catch (e) {
                                console.error("Error parsing coordinates for building:", building.gisid, e);
                            }
                            return false;
                        }
                    });
                    allBuildings.push(info);
                });
                console.log("Search index built with", allBuildings.length, "buildings");
            }

            // ==================== POLYGON STYLE FUNCTION ====================
            function polygonStyleFunction(feature) {
                if (!feature || !feature.getGeometry) {
                    return null;
                }

                let gisid = feature.get('gisid');
                let isVisible = feature.get('visible');
                if (isVisible === false) return null;

                // Find building usage from polygonDatas
                let buildingData = polygonDatas.find(p => p.gisid == gisid);
                let buildingUsage = buildingData ? buildingData.building_usage : null;
                let fillColor = getBuildingColor(buildingUsage);

                let geometry = feature.getGeometry();

                // Get center for text label
                let center;
                try {
                    if (geometry.getInteriorPoint) {
                        center = geometry.getInteriorPoint();
                    }
                    if (!center) {
                        let extent = geometry.getExtent();
                        center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                    }
                } catch (e) {
                    let extent = geometry.getExtent();
                    center = new ol.geom.Point([(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]);
                }

                return [
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#ffffff',
                            width: 2
                        }),
                        fill: new ol.style.Fill({
                            color: fillColor,
                            opacity: 0.7
                        })
                    }),
                    new ol.style.Style({
                        geometry: center,
                        text: new ol.style.Text({
                            text: `${gisid}`,
                            font: 'bold 10px Arial',
                            fill: new ol.style.Fill({ color: '#fff' }),
                            stroke: new ol.style.Stroke({ color: '#000', width: 2 }),
                            padding: [2, 4, 2, 4]
                        })
                    })
                ];
            }

            // ==================== LOCATION TRACKING ====================
            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showToast("Geolocation is not supported by your browser", 'error');
                    return;
                }

                $('#locationBtn').addClass('active');
                locationTracking = true;

                if ($('#centerMyLocationBtn').length === 0) {
                    $('body').append(
                        '<button id="centerMyLocationBtn" class="center-btn"><i class="fas fa-crosshairs"></i> Center to My Location</button>'
                    );
                    $('#centerMyLocationBtn').on('click', centerToMyLocation);
                }

                navigator.geolocation.getCurrentPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    currentPosition = [pos.coords.longitude, pos.coords.latitude];
                    showToast('Location tracking activated', 'success');
                }, function(err) {
                    showToast("Unable to get location: " + err.message, 'error');
                    locationTracking = false;
                    $('#locationBtn').removeClass('active');
                    $('#centerMyLocationBtn').remove();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });

                watchId = navigator.geolocation.watchPosition(function(pos) {
                    updateLocationOnMap(pos.coords.longitude, pos.coords.latitude, pos.coords.accuracy);
                    currentPosition = [pos.coords.longitude, pos.coords.latitude];
                }, function(err) {
                    console.warn('Watch position error:', err);
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 5000,
                    timeout: 10000
                });
            }

            function stopLocationTracking() {
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (currentLocationLayer && map) map.removeLayer(currentLocationLayer);
                if (accuracyLayer && map) map.removeLayer(accuracyLayer);
                locationTracking = false;
                $('#locationBtn').removeClass('active');
                $('#centerMyLocationBtn').remove();
                currentLocationLayer = null;
                accuracyLayer = null;
                currentPosition = null;
                showToast('Location tracking stopped', 'info');
            }

            function updateLocationOnMap(lon, lat, accuracy) {
                if (!map || !ol) return;

                const coords = ol.proj.fromLonLat([lon, lat]);
                currentPosition = [lon, lat];

                if (currentLocationLayer && map) map.removeLayer(currentLocationLayer);
                if (accuracyLayer && map) map.removeLayer(accuracyLayer);

                accuracyLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Circle(coords, accuracy || 20)
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ff4444', width: 2 }),
                        fill: new ol.style.Fill({ color: 'rgba(255,68,68,0.15)' })
                    }),
                    zIndex: 900
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
                            fill: new ol.style.Fill({ color: '#ff4444' }),
                            stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
                        })
                    }),
                    zIndex: 901
                });
                map.addLayer(currentLocationLayer);

                // Auto-center on first location fix
                if (!localStorage.getItem('mapCentered')) {
                    map.getView().setCenter(coords);
                    map.getView().setZoom(18);
                    localStorage.setItem('mapCentered', 'true');
                }
            }

            function centerToMyLocation() {
                if (currentPosition) {
                    const coords = ol.proj.fromLonLat(currentPosition);
                    map.getView().setCenter(coords);
                    map.getView().setZoom(19);
                    showToast('Centered on your location', 'info');
                } else {
                    showToast('Location not available. Please enable location tracking first.', 'warning');
                    startLocationTracking();
                }
            }

            // ==================== SEARCH FUNCTIONS ====================
            function searchBuildings(text) {
                if (!text || !text.trim()) {
                    $('#searchResults').html('<div class="empty-state"><i class="fas fa-search"></i><p>Enter search term</p></div>');
                    return;
                }

                let term = text.toLowerCase().trim();
                let results = [];

                $.each(allBuildings, function(i, b) {
                    let match = false, type = '', val = '';

                    if (b.gisid && b.gisid.toLowerCase().includes(term)) {
                        match = true; type = 'GIS ID'; val = b.gisid;
                    } else if (b.building_usage && b.building_usage.toLowerCase().includes(term)) {
                        match = true; type = 'Building Usage'; val = b.building_usage;
                    } else if (b.road_name && b.road_name.toLowerCase().includes(term)) {
                        match = true; type = 'Road Name'; val = b.road_name;
                    } else if (b.zone && b.zone.toLowerCase().includes(term)) {
                        match = true; type = 'Zone'; val = b.zone;
                    } else {
                        $.each(b.assessments, function(j, a) {
                            if (a.assessment && a.assessment.toString().toLowerCase().includes(term)) {
                                match = true; type = 'Assessment No'; val = a.assessment;
                                return false;
                            }
                            if (a.owner_name && a.owner_name.toLowerCase().includes(term)) {
                                match = true; type = 'Owner Name'; val = a.owner_name;
                                return false;
                            }
                            if (a.phone && a.phone.toLowerCase().includes(term)) {
                                match = true; type = 'Phone'; val = a.phone;
                                return false;
                            }
                        });
                    }

                    if (match) {
                        results.push({
                            gisid: b.gisid,
                            matchType: type,
                            matchValue: val,
                            building: b,
                            coordinates: b.coordinates
                        });
                    }
                });

                let $res = $('#searchResults').empty();
                if (!results.length) {
                    $res.html('<div class="empty-state"><i class="fas fa-search"></i><p>No buildings found</p></div>');
                    return;
                }

                $.each(results, function(i, r) {
                    let lon = r.coordinates && r.coordinates[0] ? r.coordinates[0] : '';
                    let lat = r.coordinates && r.coordinates[1] ? r.coordinates[1] : '';
                    $res.append(`<div class="search-result-item" data-gisid="${escapeHtml(r.gisid)}" data-lon="${lon}" data-lat="${lat}">
                        <div class="result-gisid"><i class="fas fa-building"></i> ${escapeHtml(r.gisid)}</div>
                        <div class="result-owner"><i class="fas fa-tag"></i> ${escapeHtml(r.matchType)}: ${escapeHtml(r.matchValue)}</div>
                        <div class="result-owner"><i class="fas fa-location-dot"></i> ${escapeHtml(r.building.road_name || 'No road')} | ${escapeHtml(r.building.zone || 'No zone')}</div>
                        <button class="direction-btn btn-sm mt-2"><i class="fas fa-directions"></i> Get Directions</button>
                    </div>`);
                });

                $('.search-result-item').off('click').on('click', function(e) {
                    if (!$(e.target).hasClass('direction-btn') && !$(e.target).closest('.direction-btn').length) {
                        const gisid = $(this).data('gisid');
                        if (gisid) {
                            zoomToBuilding(gisid);
                            closeAllPanels();
                        }
                    }
                });

                $('.direction-btn').off('click').on('click', function(e) {
                    e.stopPropagation();
                    let p = $(this).closest('.search-result-item');
                    let lon = parseFloat(p.data('lon'));
                    let lat = parseFloat(p.data('lat'));

                    if (lon && lat && !isNaN(lon) && !isNaN(lat) && lon !== 0 && lat !== 0) {
                        selectedBuilding = {
                            gisid: p.data('gisid'),
                            coords: [lon, lat]
                        };
                        getRouteToBuilding(p.data('gisid'), [lon, lat]);
                        closeAllPanels();
                    } else {
                        showToast("Coordinates not available for this building", 'error');
                    }
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function zoomToBuilding(gisid) {
                if (!polygonLayer || !polygonLayer.getSource()) {
                    showToast("Building layer not ready", 'error');
                    return;
                }

                let features = polygonLayer.getSource().getFeatures();
                let targetFeature = null;
                for (let i = 0; i < features.length; i++) {
                    if (features[i].get('gisid') == gisid) {
                        targetFeature = features[i];
                        break;
                    }
                }
                if (targetFeature) {
                    let extent = targetFeature.getGeometry().getExtent();
                    map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 800 });
                    // Show popup
                    const center = ol.extent.getCenter(extent);
                    showPopup(gisid, center);
                } else {
                    showToast("Building not found on map", 'error');
                }
            }

            // ==================== POPUP FUNCTIONS ====================
            function createPopup() {
                popupElement = $('<div>', { class: 'ol-popup', style: 'display:none' })[0];
                $('body').append(popupElement);
                return new ol.Overlay({
                    element: popupElement,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -10]
                });
            }

            window.closePopup = function() {
                if (popupElement) $(popupElement).hide();
            };

            window.switchTab = function(t) {
                $('.popup-tab-content, .popup-tab').removeClass('active');
                $('#tab-' + t).addClass('active');
                $('.popup-tab[data-tab="' + t + '"]').addClass('active');
                currentActiveTab = t;
            };

            function showPopup(gisid, coord) {
                if (!gisid) return;

                let pd = polygonDatas.find(p => p.gisid == gisid);
                if (!pd) {
                    console.warn('Building data not found for GIS ID:', gisid);
                    return;
                }

                let assessments = pd.pointdata || [];
                let shops = [];
                $.each(assessments, function(i, a) {
                    if (a.shops && a.shops.length) {
                        $.each(a.shops, function(j, s) {
                            shops.push({ ...s, assessmentNumber: a.assessment || 'Bill ' + (i + 1) });
                        });
                    }
                });

                let buildingHtml = `<div class="building-details-content">${[
                    ['fingerprint', 'GIS ID', pd.gisid],
                    ['building', 'Building Usage', pd.building_usage],
                    ['home', 'Building Type', pd.building_type],
                    ['layer-group', 'Floors', pd.number_floor],
                    ['receipt', 'Total Bills', pd.number_bill],
                    ['store', 'Total Shops', pd.total_shops],
                    ['road', 'Road Name', pd.road_name],
                    ['map-pin', 'Zone', pd.zone]
                ].map(([i,l,v]) => `<div class="detail-row"><div class="detail-label"><i class="fas fa-${i}"></i> ${l}:</div><div class="detail-value">${v || 'N/A'}</div></div>`).join('')}</div>`;

                let assessmentsHtml = !assessments.length ?
                    '<div class="empty-state"><i class="fas fa-receipt"></i><p>No assessments</p></div>' :
                    assessments.map((a, i) =>
                        `<div class="assessment-card" data-id="${a.id || ''}" data-assessment="${a.assessment || ''}">
                            <div class="assessment-header">
                                <span class="assessment-number"><i class="fas fa-file-invoice"></i> ${escapeHtml(a.assessment || 'Assessment ' + (i+1))}</span>
                                <span class="badge ${(a.qcsqfeet || a.qcusage) ? 'badge-success' : 'badge-warning'}">${(a.qcsqfeet || a.qcusage) ? 'QC Done' : 'QC Pending'}</span>
                            </div>
                            <div class="assessment-body">
                                ${[['Owner', a.owner_name || a.present_owner_name], ['Phone', a.phone_number], ['Floor', a.floor], ['Usage', a.bill_usage], ['Shops', (a.shops || []).length]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                            </div>
                        </div>`
                    ).join('');

                let shopsHtml = !shops.length ?
                    '<div class="empty-state"><i class="fas fa-store"></i><p>No shops</p></div>' :
                    shops.map(s => `
                        <div class="shop-item">
                            <div class="shop-name"><i class="fas fa-store"></i> ${escapeHtml(s.shop_name || 'Shop')}</div>
                            ${[['Category', s.shop_category], ['Owner', s.shop_owner_name], ['Mobile', s.shop_mobile]].map(([l,v]) => `<div class="assessment-row"><div class="assessment-label">${l}:</div><div class="assessment-value">${v || 'N/A'}</div></div>`).join('')}
                        </div>
                    `).join('');

                let html = `
                    <div class="popup-header">
                        <h4><i class="fas fa-building"></i> Building Details</h4>
                        <button class="popup-close" onclick="closePopup()">&times;</button>
                    </div>
                    <div class="popup-tabs">
                        <button class="popup-tab ${currentActiveTab == 'building' ? 'active' : ''}" data-tab="building" onclick="switchTab('building')"><i class="fas fa-info-circle"></i> Building</button>
                        <button class="popup-tab ${currentActiveTab == 'assessments' ? 'active' : ''}" data-tab="assessments" onclick="switchTab('assessments')"><i class="fas fa-receipt"></i> Assessments (${assessments.length})</button>
                        <button class="popup-tab ${currentActiveTab == 'shops' ? 'active' : ''}" data-tab="shops" onclick="switchTab('shops')"><i class="fas fa-store"></i> Shops (${shops.length})</button>
                    </div>
                    <div id="tab-building" class="popup-tab-content ${currentActiveTab == 'building' ? 'active' : ''}">${buildingHtml}</div>
                    <div id="tab-assessments" class="popup-tab-content ${currentActiveTab == 'assessments' ? 'active' : ''}"><div style="padding:12px">${assessmentsHtml}</div></div>
                    <div id="tab-shops" class="popup-tab-content ${currentActiveTab == 'shops' ? 'active' : ''}"><div style="padding:16px">${shopsHtml}</div></div>
                    <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <button class="btn-start-nav" id="routeFromPopupBtn"><i class="fas fa-route"></i> Get Directions to this Building</button>
                    </div>`;

                $(popupElement).html(html).show();

                // Position popup on desktop
                if ($(window).width() > 768 && popupOverlay && coord) {
                    popupOverlay.setPosition(coord);
                }

                $('#routeFromPopupBtn').off('click').on('click', function() {
                    closePopup();
                    // Find building center coordinates
                    let buildingCoords = null;
                    $.each(polygons, function(i, poly) {
                        if (poly.gisid == gisid) {
                            try {
                                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                                if (coords && coords[0] && coords[0][0]) {
                                    let cx = 0, cy = 0, count = 0;
                                    $.each(coords[0], function(k, c) {
                                        if (c && c.length >= 2 && !isNaN(c[0]) && !isNaN(c[1])) {
                                            cx += parseFloat(c[0]);
                                            cy += parseFloat(c[1]);
                                            count++;
                                        }
                                    });
                                    if (count > 0) {
                                        buildingCoords = [cx / count, cy / count];
                                    }
                                }
                            } catch (e) {}
                            return false;
                        }
                    });
                    if (buildingCoords) {
                        getRouteToBuilding(gisid, buildingCoords);
                    } else {
                        showToast('Could not get building coordinates', 'error');
                    }
                });

                $('.assessment-card').off('click').on('click', function() {
                    let id = $(this).data('id');
                    let num = $(this).data('assessment');
                    $(this).after(`
                        <div class="assessment-form-container">
                            <button class="close-form-btn" style="float:right; background:none; border:none; color:#ff4444; font-size:20px;">&times;</button>
                            <h4 style="color:#ffc107; margin-bottom:15px;">QC Form - ${escapeHtml(num)}</h4>
                            <form class="qc-form">
                                <input type="hidden" name="assessment_id" value="${id}">
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Square Feet:</label>
                                    <input type="number" name="qc_sqfeet" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">QC Usage:</label>
                                    <select name="qc_usage" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                        <option value="">Select</option>
                                        <option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Industrial">Industrial</option>
                                    </select>
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="color:#ffc107">Tax Amount (₹):</label>
                                    <input type="number" name="tax_amount" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ff4444; background:#0f0f1a; color:white;">
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button type="submit" style="flex:1; background:#28a745; color:white; border:none; padding:10px; border-radius:8px;">Save</button>
                                    <button type="button" class="cancel-form-btn" style="flex:1; background:#dc3545; color:white; border:none; padding:10px; border-radius:8px;">Cancel</button>
                                </div>
                            </form>
                        </div>
                    `);

                    $('.qc-form').on('submit', function(e) {
                        e.preventDefault();
                        let hasValues = $(this).find('input[name="qc_sqfeet"]').val() &&
                            $(this).find('select[name="qc_usage"]').val() &&
                            $(this).find('input[name="tax_amount"]').val();
                        let $badge = $(this).closest('.assessment-card').find('.badge');
                        if (hasValues) {
                            $badge.removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check-circle"></i> QC Complete');
                        } else {
                            $badge.removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-clock"></i> QC Pending');
                        }
                        showToast('QC Saved! Status: ' + (hasValues ? 'QC Complete' : 'QC Pending'), 'info');
                        $('.assessment-form-container').remove();
                    });

                    $('.close-form-btn, .cancel-form-btn').on('click', function() {
                        $('.assessment-form-container').remove();
                    });
                });
            }

            // ==================== REFRESH LAYERS ====================
            function refreshLayers() {
                if (!map || !ol) {
                    console.error('Map or OpenLayers not ready');
                    return;
                }

                if (polygonLayer && map) map.removeLayer(polygonLayer);
                if (lineLayer && map) map.removeLayer(lineLayer);

                let ps = new ol.source.Vector();
                let validPolygons = 0;

                $.each(polygons, function(i, p) {
                    try {
                        let c = typeof p.coordinates === 'string' ? JSON.parse(p.coordinates) : p.coordinates;
                        if (c && c.length && c[0] && c[0].length >= 3) {
                            // Ensure coordinates are valid numbers
                            let isValid = true;
                            $.each(c[0], function(k, coord) {
                                if (!coord || coord.length < 2 || isNaN(parseFloat(coord[0])) || isNaN(parseFloat(coord[1]))) {
                                    isValid = false;
                                    return false;
                                }
                            });
                            if (isValid) {
                                ps.addFeature(new ol.Feature({
                                    geometry: new ol.geom.Polygon(c),
                                    gisid: p.gisid,
                                    sqfeet: p.sqfeet,
                                    visible: true
                                }));
                                validPolygons++;
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing polygon:", p.gisid, e);
                    }
                });

                console.log(`Loaded ${validPolygons} valid polygons out of ${polygons.length}`);

                polygonLayer = new ol.layer.Vector({
                    source: ps,
                    style: polygonStyleFunction,
                    visible: true,
                    zIndex: 100
                });

                let ls = new ol.source.Vector();
                $.each(lines, function(i, l) {
                    try {
                        let c = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l.coordinates;
                        if (c && c.length) {
                            if (c.length === 1 && Array.isArray(c[0][0])) {
                                c = c[0];
                            }
                            // Validate coordinates
                            let isValid = true;
                            $.each(c, function(k, coord) {
                                if (!coord || coord.length < 2 || isNaN(parseFloat(coord[0])) || isNaN(parseFloat(coord[1]))) {
                                    isValid = false;
                                    return false;
                                }
                            });
                            if (isValid && c.length >= 2) {
                                ls.addFeature(new ol.Feature({
                                    geometry: new ol.geom.LineString(c),
                                    gisid: l.gisid
                                }));
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing line:", e);
                    }
                });

                lineLayer = new ol.layer.Vector({
                    source: ls,
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({ color: '#ffc107', width: 3 })
                    }),
                    visible: true,
                    zIndex: 50
                });

                map.addLayer(polygonLayer);
                map.addLayer(lineLayer);

                // Click handler for buildings
                map.on('click', function(e) {
                    let feature = map.forEachFeatureAtPixel(e.pixel, function(f) {
                        return f;
                    });
                    if (feature && feature.get('gisid')) {
                        showPopup(feature.get('gisid'), e.coordinate);
                    } else if (popupElement) {
                        $(popupElement).hide();
                    }
                });

                map.on('pointermove', function(e) {
                    let hasFeature = map.forEachFeatureAtPixel(e.pixel, function(f) {
                        return f && f.get('gisid');
                    });
                    $('#map').css('cursor', hasFeature ? 'pointer' : '');
                });

                showLoading(false);
                console.log('Layers refreshed successfully');
            }

            // ==================== MAP INITIALIZATION ====================
            function initMap() {
                if (!isOLReady()) {
                    console.error('OpenLayers not loaded, retrying...');
                    setTimeout(initMap, 500);
                    return;
                }

                showLoading(true);

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

                let droneImg = wardData.drone_image;
                let hasDrone = false;

                if (droneImg && droneImg !== 'null' && droneImg !== '') {
                    try {
                        let imageUrl = droneImg;
                        if (!imageUrl.startsWith('http') && !imageUrl.startsWith('//')) {
                            imageUrl = '/' + imageUrl.replace(/^\/+/, '');
                        }

                        const left = parseFloat(wardData.extent_left);
                        const bottom = parseFloat(wardData.extent_bottom);
                        const right = parseFloat(wardData.extent_right);
                        const top = parseFloat(wardData.extent_top);

                        if (!isNaN(left) && !isNaN(bottom) && !isNaN(right) && !isNaN(top)) {
                            imageLayer = new ol.layer.Image({
                                source: new ol.source.ImageStatic({
                                    url: imageUrl,
                                    imageExtent: [left, bottom, right, top],
                                    projection: 'EPSG:3857'
                                }),
                                visible: true,
                                opacity: 0.8
                            });
                            hasDrone = true;
                            console.log('Drone image loaded');
                        }
                    } catch (e) {
                        console.error("Error loading drone image:", e);
                    }
                }

                let bound = wardData.boundary;
                let boundExt = null;

                if (bound && bound.length && bound[0] && bound[0].length) {
                    try {
                        let bc = bound[0].map(c => ol.proj.fromLonLat([parseFloat(c[0]), parseFloat(c[1])]));
                        boundaryLayer = new ol.layer.Vector({
                            source: new ol.source.Vector({
                                features: [new ol.Feature({
                                    geometry: new ol.geom.Polygon([bc])
                                })]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({ color: '#ff0000', width: 3, lineDash: [10, 5] }),
                                fill: new ol.style.Fill({ color: 'rgba(255,0,0,0.05)' })
                            }),
                            visible: true,
                            zIndex: 200
                        });

                        let lons = bound[0].map(p => parseFloat(p[0]));
                        let lats = bound[0].map(p => parseFloat(p[1]));
                        boundExt = ol.proj.fromLonLat([Math.min(...lons), Math.min(...lats), Math.max(...lons), Math.max(...lats)]);
                        console.log('Boundary loaded');
                    } catch (e) {
                        console.error("Error parsing boundary:", e);
                    }
                }

                let center = ol.proj.fromLonLat([80.2707, 13.0827]);
                let zoom = 18;

                if (bound && bound[0] && bound[0].length) {
                    try {
                        let lons = bound[0].map(p => parseFloat(p[0]));
                        let lats = bound[0].map(p => parseFloat(p[1]));
                        center = ol.proj.fromLonLat([(Math.min(...lons) + Math.max(...lons)) / 2, (Math.min(...lats) + Math.max(...lats)) / 2]);
                        zoom = 17;
                    } catch (e) {}
                }

                map = new ol.Map({
                    target: 'map',
                    layers: [osmLayer, satelliteLayer],
                    view: new ol.View({
                        center: center,
                        zoom: zoom,
                        minZoom: 12,
                        maxZoom: 22
                    })
                });

                popupOverlay = createPopup();
                map.addOverlay(popupOverlay);

                if (imageLayer) map.addLayer(imageLayer);
                if (boundaryLayer) map.addLayer(boundaryLayer);

                if (boundExt) {
                    setTimeout(() => {
                        map.getView().fit(boundExt, { padding: [50, 50, 50, 50], duration: 1000 });
                    }, 500);
                }

                // Add UI panels after map is ready
                setTimeout(() => {
                    addUIPanels(hasDrone);
                }, 100);
            }

            function addUIPanels(hasDrone) {
                // Add Mobile Bottom Navigation
                if ($('.mobile-bottom-nav').length === 0) {
                    $('body').append(`
                        <div class="mobile-bottom-nav">
                            <button class="mobile-nav-btn" id="mobileMenuBtn">
                                <i class="fas fa-layer-group"></i>
                                <span>Layers</span>
                            </button>
                            <button class="mobile-nav-btn" id="mobileSearchBtn">
                                <i class="fas fa-search"></i>
                                <span>Search</span>
                            </button>
                            <button class="mobile-nav-btn" id="mobileLocationBtn">
                                <i class="fas fa-location-dot"></i>
                                <span>Location</span>
                            </button>
                            <button class="mobile-nav-btn" id="mobileRouteBtn">
                                <i class="fas fa-route"></i>
                                <span>Route</span>
                            </button>
                            <button class="mobile-nav-btn" id="mobileFilterBtn">
                                <i class="fas fa-filter"></i>
                                <span>Filter</span>
                            </button>
                        </div>
                    `);
                }

                // Add panels if not exists
                if ($('#layerSwitcher').length === 0) {
                    $('body').append(`
                        <div class="layer-switcher panel" id="layerSwitcher">
                            <button class="panel-close" onclick="$('#layerSwitcher').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-layer-group"></i> Layers</h5>
                            <div class="layer-group">
                                <div class="group-title">Base Maps</div>
                                <label><input type="radio" name="baseLayer" value="osm" checked> <i class="fas fa-map"></i> OpenStreetMap</label>
                                <label><input type="radio" name="baseLayer" value="satellite"> <i class="fas fa-satellite"></i> Satellite</label>
                            </div>
                            <div class="layer-group">
                                <div class="group-title">Overlays</div>
                                <label><input type="checkbox" id="toggleBuildings" checked> <i class="fas fa-building"></i> Buildings</label>
                                <label><input type="checkbox" id="toggleRoads" checked> <i class="fas fa-road"></i> Roads</label>
                                <label><input type="checkbox" id="toggleBoundary" checked> <i class="fas fa-draw-polygon"></i> Ward Boundary</label>
                                ${hasDrone ? '<label><input type="checkbox" id="toggleDrone" checked> <i class="fas fa-drone"></i> Drone Image</label>' : ''}
                            </div>
                        </div>
                    `);

                    $('body').append(`
                        <div class="map-legend panel" id="mapLegend">
                            <button class="panel-close" onclick="$('#mapLegend').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-info-circle"></i> Legend</h5>
                            <div class="legend-item"><div class="legend-color residential"></div><span>Residential</span></div>
                            <div class="legend-item"><div class="legend-color commercial"></div><span>Commercial</span></div>
                            <div class="legend-item"><div class="legend-color industrial"></div><span>Industrial</span></div>
                            <div class="legend-item"><div class="legend-color institutional"></div><span>Institutional</span></div>
                            <div class="legend-item"><div class="legend-color mixed"></div><span>Mixed Use</span></div>
                            <div class="legend-item"><div class="legend-color government"></div><span>Government</span></div>
                            <div class="legend-item"><div class="legend-color vacant"></div><span>Vacant</span></div>
                            <div class="legend-item"><div class="legend-color default"></div><span>Other/Unknown</span></div>
                            <div class="legend-item"><div style="width:24px; height:16px; background:#ffc107; border-radius:4px;"></div><span>Roads</span></div>
                            <div class="legend-item"><div style="width:24px; height:16px; background:#ff0000; border-radius:4px;"></div><span>Ward Boundary</span></div>
                        </div>
                    `);

                    $('body').append(`
                        <div class="search-panel panel" id="searchPanel">
                            <button class="panel-close" onclick="$('#searchPanel').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-search"></i> Search Building</h5>
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="GIS ID, Owner, Assessment...">
                                <button id="doSearchBtn"><i class="fas fa-search"></i> Go</button>
                            </div>
                            <div id="searchResults" class="search-results"></div>
                        </div>
                    `);

                    $('body').append(`
                        <div class="filter-panel panel" id="filterPanel">
                            <button class="panel-close" onclick="$('#filterPanel').removeClass('open')">&times;</button>
                            <h5><i class="fas fa-filter"></i> Filter Buildings</h5>
                            <div class="filter-group">
                                <label>Building Usage</label>
                                <select id="filterUsage">
                                    <option value="all">All Buildings</option>
                                    <option value="RESIDENTIAL">Residential</option>
                                    <option value="COMMERCIAL">Commercial</option>
                                    <option value="INDUSTRIAL">Industrial</option>
                                    <option value="INSTITUTIONAL">Institutional</option>
                                    <option value="MIXED">Mixed Use</option>
                                    <option value="GOVERNMENT">Government</option>
                                    <option value="VACANT">Vacant</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>QC Status</label>
                                <select id="filterType">
                                    <option value="all">All Buildings</option>
                                    <option value="completed">QC Complete</option>
                                    <option value="pending">QC Pending</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Min Floors</label>
                                <input type="number" id="filterMinFloors" placeholder="Min">
                            </div>
                            <div class="filter-group">
                                <label>Max Floors</label>
                                <input type="number" id="filterMaxFloors" placeholder="Max">
                            </div>
                            <div class="filter-actions">
                                <button class="apply-btn" id="applyFilterBtn">Apply</button>
                                <button class="reset-btn" id="resetFilterBtn">Reset</button>
                            </div>
                            <div class="filter-count" id="filterCount"></div>
                        </div>
                    `);

                    $('body').append(`
                        <div class="zoom-controls">
                            <button class="zoom-btn" id="zoomInBtn"><i class="fas fa-plus"></i></button>
                            <button class="zoom-btn" id="zoomOutBtn"><i class="fas fa-minus"></i></button>
                        </div>
                    `);
                }

                // Desktop button handlers
                $('#menuBtn, #mobileMenuBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#layerSwitcher').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#layerSwitcher').addClass('open');
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#legendBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#mapLegend').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#mapLegend').addClass('open');
                });

                $('#openSearchBtn, #mobileSearchBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#searchPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) {
                        $('#searchPanel').addClass('open');
                        setTimeout(() => $('#searchInput').focus(), 300);
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#filterBtn, #mobileFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    let isOpen = $('#filterPanel').hasClass('open');
                    closeAllPanels();
                    if (!isOpen) $('#filterPanel').addClass('open');
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $('#locationBtn, #mobileLocationBtn').on('click', function() {
                    if (locationTracking) {
                        stopLocationTracking();
                        clearRoute();
                    } else {
                        startLocationTracking();
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                    setTimeout(() => $(this).removeClass('active'), 500);
                });

                $('#routeBtn, #mobileRouteBtn').on('click', function() {
                    if (selectedBuilding && selectedBuilding.coords) {
                        getRouteToBuilding(selectedBuilding.gisid, selectedBuilding.coords);
                    } else {
                        showToast('Please search and select a building first', 'warning');
                        $('#openSearchBtn').click();
                    }
                    $('.mobile-nav-btn').removeClass('active');
                    $(this).addClass('active');
                    setTimeout(() => $(this).removeClass('active'), 500);
                });

                // Other event listeners
                $('input[name="baseLayer"]').on('change', function() {
                    currentBaseLayer = $(this).val();
                    if (osmLayer) osmLayer.setVisible(currentBaseLayer === 'osm');
                    if (satelliteLayer) satelliteLayer.setVisible(currentBaseLayer === 'satellite');
                });

                $('#toggleBuildings').on('change', function() {
                    if (polygonLayer) polygonLayer.setVisible($(this).is(':checked'));
                });

                $('#toggleRoads').on('change', function() {
                    if (lineLayer) lineLayer.setVisible($(this).is(':checked'));
                });

                $('#toggleBoundary').on('change', function() {
                    if (boundaryLayer) boundaryLayer.setVisible($(this).is(':checked'));
                });

                if (hasDrone && $('#toggleDrone').length) {
                    $('#toggleDrone').on('change', function() {
                        if (imageLayer) imageLayer.setVisible($(this).is(':checked'));
                    });
                }

                $('#doSearchBtn').on('click', function() {
                    searchBuildings($('#searchInput').val());
                });

                $('#searchInput').on('keypress', function(e) {
                    if (e.which === 13) {
                        searchBuildings($(this).val());
                    }
                });

                $('#applyFilterBtn').on('click', function() {
                    if (!polygonLayer || !polygonLayer.getSource()) return;

                    let usage = $('#filterUsage').val();
                    let type = $('#filterType').val();
                    let minF = $('#filterMinFloors').val();
                    let maxF = $('#filterMaxFloors').val();
                    let src = polygonLayer.getSource();
                    let fts = src.getFeatures();
                    let cnt = 0;

                    $.each(fts, function(i, f) {
                        let g = f.get('gisid');
                        let b = polygonDatas.find(p => p.gisid == g);
                        let show = true;

                        if (usage !== 'all' && b) {
                            let buildingUsage = (b.building_usage || '').toUpperCase();
                            if (buildingUsage !== usage && !buildingUsage.includes(usage)) {
                                show = false;
                            }
                        }

                        if (show && type !== 'all' && b) {
                            let hasQC = false;
                            if (b.pointdata) {
                                $.each(b.pointdata, function(k, a) {
                                    if (a.qcsqfeet || a.qcusage) {
                                        hasQC = true;
                                        return false;
                                    }
                                });
                            }
                            if (type === 'completed' && !hasQC) show = false;
                            if (type === 'pending' && hasQC) show = false;
                        }

                        if (show && b && (minF || maxF)) {
                            let fl = parseInt(b.number_floor) || 0;
                            if (minF && fl < parseInt(minF)) show = false;
                            if (maxF && fl > parseInt(maxF)) show = false;
                        }

                        f.set('visible', show);
                        if (show) cnt++;
                    });

                    polygonLayer.setStyle(polygonStyleFunction);
                    polygonLayer.changed();
                    $('#filterCount').text(`Showing ${cnt} of ${fts.length} buildings`);
                    closeAllPanels();
                    showToast(`Showing ${cnt} buildings`, 'info');
                });

                $('#resetFilterBtn').on('click', function() {
                    $('#filterUsage').val('all');
                    $('#filterType').val('all');
                    $('#filterMinFloors, #filterMaxFloors').val('');
                    if (polygonLayer && polygonLayer.getSource()) {
                        let src = polygonLayer.getSource();
                        $.each(src.getFeatures(), function(i, f) {
                            f.set('visible', true);
                        });
                        polygonLayer.setStyle(polygonStyleFunction);
                        polygonLayer.changed();
                        $('#filterCount').text(`Showing ${src.getFeatures().length} of ${src.getFeatures().length} buildings`);
                    }
                    closeAllPanels();
                    showToast('Filters reset', 'info');
                });

                $('#zoomInBtn').on('click', function() {
                    if (map) map.getView().setZoom(map.getView().getZoom() + 1);
                });

                $('#zoomOutBtn').on('click', function() {
                    if (map) map.getView().setZoom(map.getView().getZoom() - 1);
                });

                $('#closeRouteInfo').on('click', function() {
                    clearRoute();
                });

                $('#startNavigationBtn').on('click', function() {
                    startNavigation();
                });

                // Close panels when clicking outside
                $(document).on('click touchstart', function(e) {
                    if (!$(e.target).closest('.panel').length &&
                        !$(e.target).closest('.action-btn').length &&
                        !$(e.target).closest('.mobile-nav-btn').length &&
                        !$(e.target).closest('#centerMyLocationBtn').length &&
                        !$(e.target).closest('.zoom-btn').length) {
                        closeAllPanels();
                    }
                });
            }

            // Start the application
            initMap();
            buildSearchIndex();

            $(window).on('resize', function() {
                setTimeout(() => {
                    if (map) map.updateSize();
                }, 100);
            });
        });
    </script>
@endpush
