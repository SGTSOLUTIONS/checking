@extends('layouts.admin-layout')

@section('title', 'Tracking Map View')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .tracking-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stats-card h3 {
            font-size: 1.8rem;
            margin: 10px 0;
        }

        .timeline {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            max-height: 400px;
            overflow-y: auto;
        }

        .timeline-item {
            padding: 12px;
            border-left: 3px solid #007bff;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .timeline-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background: #f0f7ff;
        }

        .timeline-item.active {
            background: #e3f2fd;
            border-left-color: #dc3545;
        }

        .loading {
            text-align: center;
            padding: 50px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .distance-badge {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .info-window {
            background: white;
            padding: 10px;
            border-radius: 8px;
            min-width: 220px;
        }

        .info-window p {
            margin: 5px 0;
        }

        .legend {
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .legend-line {
            width: 30px;
            height: 3px;
            margin-right: 8px;
        }

        .layer-control {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .layer-control select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-export {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background: #218838;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-primary">
                    <i class="fas fa-map-marked-alt me-2"></i>Location Tracking
                </h1>
                <p class="text-muted mb-0">
                    Tracking: <strong id="userNameDisplay">{{ $userName ?? 'Surveyor' }}</strong>
                    (Role: <span id="userRoleDisplay">{{ $userRole ?? 'surveyor' }}</span>)
                </p>
            </div>
            <div class="export-buttons">
                <button class="btn-export" onclick="exportAsGeoJSON()">
                    <i class="fas fa-download me-1"></i> Export as GeoJSON
                </button>
                <button class="btn btn-outline-primary" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Date Filter Card -->
        <div class="tracking-card">
            <div class="row">
                <div class="col-md-3">
                    <label class="fw-bold"><i class="fas fa-calendar-alt me-1"></i> Start Date</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold"><i class="fas fa-calendar-alt me-1"></i> End Date</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold"><i class="fas fa-clock me-1"></i> Start Time</label>
                    <input type="time" id="startTime" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="loadTrackingData()">
                        <i class="fas fa-search me-1"></i> Apply Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3 id="totalPoints">0</h3>
                    <p>Total Tracking Points</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-road"></i>
                    <h3 id="totalDistance">0</h3>
                    <p>Total Distance (km)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-clock"></i>
                    <h3 id="duration">0</h3>
                    <p>Duration (hours)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fas fa-chart-line"></i>
                    <h3 id="avgSpeed">0</h3>
                    <p>Avg Speed (km/h)</p>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="tracking-card">
            <div id="map"></div>
        </div>

        <!-- Timeline -->
        <div class="tracking-card">
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>Movement Timeline
                <span class="distance-badge ms-2" id="timelineDistance">Total: 0 km</span>
            </h5>
            <div id="timeline" class="timeline">
                <p class="text-muted text-center">Select a date range to view timeline</p>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        let polylines = [];
        let trackingData = [];
        let userId = {{ $userId ?? 'null' }};
        let currentLayer = 'satellite';

        // Initialize map with Google Satellite layer
        function initMap() {
            // Google Satellite Layer
            const googleSatellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
            });

            // Google Hybrid Layer (Satellite + Labels)
            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
            });

            // Google Streets Layer
            const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
            });

            // OpenStreetMap Layer (fallback)
            const osmLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB',
                subdomains: 'abcd',
                maxZoom: 19
            });

            // Initialize map with Satellite layer
            map = L.map('map').setView([13.0012773, 80.2047665], 18);
            googleSatellite.addTo(map);

            // Store layers for switching
            map.layers = {
                satellite: googleSatellite,
                hybrid: googleHybrid,
                streets: googleStreets,
                osm: osmLayer
            };

            // Add layer control
            const layerControl = L.control({
                position: 'topright'
            });
            layerControl.onAdd = function() {
                const div = L.DomUtil.create('div', 'layer-control');
                div.innerHTML = `
            <select id="layerSelect" onchange="changeLayer()" style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                <option value="satellite">🛰️ Satellite View</option>
                <option value="hybrid">🗺️ Hybrid View</option>
                <option value="streets">🏙️ Streets View</option>
                <option value="osm">🌍 OpenStreetMap</option>
            </select>
        `;
                return div;
            };
            layerControl.addTo(map);

            // Add scale bar
            L.control.scale({
                metric: true,
                imperial: false
            }).addTo(map);

            // Add legend
            const legend = L.control({
                position: 'bottomleft'
            });
            legend.onAdd = function() {
                const div = L.DomUtil.create('div', 'legend');
                div.innerHTML = `
            <div class="legend-item">
                <div class="legend-color" style="background: #28a745;"></div>
                <span>Start Point</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #dc3545;"></div>
                <span>End Point</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #007bff;"></div>
                <span>Tracking Points</span>
            </div>
            <div class="legend-item">
                <div class="legend-line" style="background: #007bff; height: 3px;"></div>
                <span>Route Path</span>
            </div>
        `;
                return div;
            };
            legend.addTo(map);
        }

        // Change map layer
        function changeLayer() {
            const selectedLayer = document.getElementById('layerSelect').value;
            currentLayer = selectedLayer;

            // Remove current layers
            for (let key in map.layers) {
                if (map.hasLayer(map.layers[key])) {
                    map.removeLayer(map.layers[key]);
                }
            }

            // Add selected layer
            map.addLayer(map.layers[selectedLayer]);
        }

        // Calculate distance between two points (Haversine formula)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Load tracking data
        function loadTrackingData() {
            if (!userId) {
                console.error('No user ID provided');
                alert('No user selected for tracking');
                return;
            }

            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const startTime = $('#startTime').val();
            const endTime = $('#endTime').val();

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            showLoading();

            let url = `/admin/tracking/data/${userId}?start_date=${startDate}&end_date=${endDate}`;
            if (startTime) url += `&start_time=${startTime}`;
            if (endTime) url += `&end_time=${endTime}`;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Tracking data received:', response);

                    if (response.success && response.data) {
                        trackingData = response.data;

                        if (trackingData.length === 0) {
                            alert('No tracking data found for selected date range');
                            $('#timeline').html(
                                '<p class="text-muted text-center">No tracking data available</p>');
                            return;
                        }

                        updateStatistics();
                        renderMap();
                        renderTimeline();
                    } else {
                        alert('Failed to load tracking data');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading tracking data:', xhr);
                    alert('Error loading tracking data. Please try again.');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        // Update statistics
        function updateStatistics() {
            $('#totalPoints').text(trackingData.length);

            // Calculate total distance and average speed
            let totalDistance = 0;
            let totalTimeHours = 0;

            for (let i = 0; i < trackingData.length - 1; i++) {
                const dist = calculateDistance(
                    parseFloat(trackingData[i].latitude),
                    parseFloat(trackingData[i].longitude),
                    parseFloat(trackingData[i + 1].latitude),
                    parseFloat(trackingData[i + 1].longitude)
                );
                totalDistance += dist;

                const timeDiff = new Date(trackingData[i + 1].tracked_at) - new Date(trackingData[i].tracked_at);
                totalTimeHours += timeDiff / (1000 * 60 * 60);
            }

            $('#totalDistance').text(totalDistance.toFixed(2));
            $('#timelineDistance').text(`Total: ${totalDistance.toFixed(2)} km`);

            // Calculate duration
            if (trackingData.length >= 2) {
                const startTime = new Date(trackingData[0].tracked_at);
                const endTime = new Date(trackingData[trackingData.length - 1].tracked_at);
                const durationHours = (endTime - startTime) / (1000 * 60 * 60);
                $('#duration').text(durationHours.toFixed(1));

                // Average speed
                const avgSpeed = totalDistance / (durationHours > 0 ? durationHours : 1);
                $('#avgSpeed').text(avgSpeed.toFixed(1));
            } else {
                $('#duration').text('0');
                $('#avgSpeed').text('0');
            }
        }

        // Render map with all points and route
        function renderMap() {
            // Clear existing layers
            markers.forEach(marker => map.removeLayer(marker));
            polylines.forEach(line => map.removeLayer(line));
            markers = [];
            polylines = [];

            if (trackingData.length === 0) return;

            // Create polyline coordinates
            const latlngs = trackingData.map(point => [parseFloat(point.latitude), parseFloat(point.longitude)]);

            // Add polyline with gradient effect
            const polyline = L.polyline(latlngs, {
                color: '#007bff',
                weight: 4,
                opacity: 0.8,
                smoothFactor: 1,
                lineCap: 'round',
                lineJoin: 'round'
            }).addTo(map);
            polylines.push(polyline);

            // Add animated dashed line effect (optional)
            const dashedLine = L.polyline(latlngs, {
                color: '#00ff00',
                weight: 2,
                opacity: 0.4,
                smoothFactor: 1,
                dashArray: '10, 10'
            }).addTo(map);
            polylines.push(dashedLine);

            // Fit bounds with padding
            map.fitBounds(polyline.getBounds(), {
                padding: [50, 50]
            });

            // Add markers for each point
            trackingData.forEach((point, index) => {
                const isStart = index === 0;
                const isEnd = index === trackingData.length - 1;

                let markerColor, markerSize, iconHtml;

                if (isStart) {
                    markerColor = '#28a745';
                    markerSize = '20px';
                    iconHtml = `<i class="fas fa-play" style="color: white; font-size: 10px;"></i>`;
                } else if (isEnd) {
                    markerColor = '#dc3545';
                    markerSize = '20px';
                    iconHtml = `<i class="fas fa-flag-checkered" style="color: white; font-size: 10px;"></i>`;
                } else {
                    markerColor = '#007bff';
                    markerSize = '12px';
                    iconHtml = '';
                }

                let markerIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${markerColor}; width: ${markerSize}; height: ${markerSize}; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">${iconHtml}</div>`,
                    iconSize: [parseInt(markerSize), parseInt(markerSize)],
                    popupAnchor: [0, -parseInt(markerSize) / 2]
                });

                const date = new Date(point.tracked_at);
                const marker = L.marker([parseFloat(point.latitude), parseFloat(point.longitude)], {
                        icon: markerIcon
                    })
                    .bindPopup(`
                <div class="info-window">
                    <strong>📍 Point ${index + 1}</strong>
                    <p><i class="fas fa-clock"></i> ${date.toLocaleString()}</p>
                    <p><i class="fas fa-map-marker-alt"></i> ${parseFloat(point.latitude).toFixed(6)}, ${parseFloat(point.longitude).toFixed(6)}</p>
                    ${index > 0 ? `<p><i class="fas fa-road"></i> Distance from previous: ${calculateDistance(
                            parseFloat(trackingData[index-1].latitude),
                            parseFloat(trackingData[index-1].longitude),
                            parseFloat(point.latitude),
                            parseFloat(point.longitude)
                        ).toFixed(2)} km</p>` : ''}
                </div>
            `);

                marker.addTo(map);
                markers.push(marker);
            });
        }

        // Render timeline
        function renderTimeline() {
            const timeline = $('#timeline');
            timeline.empty();

            if (trackingData.length === 0) {
                timeline.html('<p class="text-muted text-center">No tracking data available</p>');
                return;
            }

            trackingData.forEach((point, index) => {
                const date = new Date(point.tracked_at);
                const prevPoint = index > 0 ? trackingData[index - 1] : null;
                let distance = '';

                if (prevPoint) {
                    const dist = calculateDistance(
                        parseFloat(prevPoint.latitude),
                        parseFloat(prevPoint.longitude),
                        parseFloat(point.latitude),
                        parseFloat(point.longitude)
                    );
                    distance = `<span class="badge bg-info ms-2">+${dist.toFixed(2)} km</span>`;
                }

                timeline.append(`
            <div class="timeline-item" onclick="flyToPoint(${index})">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>📍 Point ${index + 1}</strong>
                        ${distance}
                    </div>
                    <small class="text-muted">${date.toLocaleString()}</small>
                </div>
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-location-dot"></i> ${parseFloat(point.latitude).toFixed(6)}, ${parseFloat(point.longitude).toFixed(6)}
                </small>
            </div>
        `);
            });
        }

        // Fly to specific point on map
        function flyToPoint(index) {
            if (trackingData[index]) {
                map.flyTo([parseFloat(trackingData[index].latitude), parseFloat(trackingData[index].longitude)], 19);
                if (markers[index]) {
                    markers[index].openPopup();
                }

                $('.timeline-item').removeClass('active');
                $('.timeline-item').eq(index).addClass('active');
            }
        }

        // Export as GeoJSON
        function exportAsGeoJSON() {
            if (trackingData.length === 0) {
                alert('No data to export');
                return;
            }

            const geojson = {
                type: "FeatureCollection",
                features: [{
                        type: "Feature",
                        properties: {
                            name: "Route Path",
                            description: "Tracking route"
                        },
                        geometry: {
                            type: "LineString",
                            coordinates: trackingData.map(point => [
                                parseFloat(point.longitude),
                                parseFloat(point.latitude)
                            ])
                        }
                    },
                    ...trackingData.map((point, index) => ({
                        type: "Feature",
                        properties: {
                            name: `Point ${index + 1}`,
                            time: point.tracked_at,
                            index: index,
                            isStart: index === 0,
                            isEnd: index === trackingData.length - 1
                        },
                        geometry: {
                            type: "Point",
                            coordinates: [parseFloat(point.longitude), parseFloat(point.latitude)]
                        }
                    }))
                ]
            };

            const dataStr = JSON.stringify(geojson, null, 2);
            const blob = new Blob([dataStr], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `tracking_data_${userId}_${new Date().toISOString().slice(0,19)}.geojson`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function refreshData() {
            loadTrackingData();
        }

        function showLoading() {
            $('#statsCards').css('opacity', '0.5');
        }

        function hideLoading() {
            $('#statsCards').css('opacity', '1');
        }

        // Set default dates and initialize
        $(document).ready(function() {
            initMap();

            const today = new Date().toISOString().split('T')[0];
            const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            $('#startDate').val(weekAgo);
            $('#endDate').val(today);

            if (userId) {
                setTimeout(() => {
                    loadTrackingData();
                }, 500);
            } else {
                $('#timeline').html(
                    '<p class="text-muted text-center">No user selected. Please go back and select a user.</p>');
            }
        });
    </script>
@endsection
