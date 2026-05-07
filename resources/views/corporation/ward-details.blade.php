{{-- resources/views/corporation/ward-details.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward ' . $ward->ward_no . ' Details - Tamil Nadu Municipal Corporation')

@section('styles')
@parent
<style>
    .map-toolbar {
        background: white;
        padding: 10px 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .building-info-card {
        background: white;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
        border-left: 4px solid #1679AB;
    }
    .search-box {
        border-radius: 25px;
        padding: 8px 16px;
        border: 1px solid #ddd;
    }
    .stat-badge {
        background: linear-gradient(135deg, #102C57, #1679AB);
        color: white;
        border-radius: 12px;
        padding: 8px 16px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="ward-details-content">
    <div class="animate__animated animate__fadeInUp">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h3 class="fw-bold" style="color:#ffffff;">
                <i class="fas fa-map-marker-alt me-2" style="color:#1679AB;"></i>
                Ward {{ $ward->ward_no }}: {{ ucfirst($ward->zone) }} Zone
            </h3>
            <div>
                <a href="{{ route('corporation.dashboard') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge">
                    <h5 class="mb-0">{{ $totalBuildings }}</h5>
                    <small>Total Buildings</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge" style="background: linear-gradient(135deg, #1679AB, #FFB1B1);">
                    <h5 class="mb-0">{{ $gisIdCount }}</h5>
                    <small>GIS ID Assigned</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge" style="background: linear-gradient(135deg, #102C57, #1679AB);">
                    <h5 class="mb-0">{{ $totalRoads }}</h5>
                    <small>Road Segments</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge" style="background: linear-gradient(135deg, #FFB1B1, #102C57);">
                    <h5 class="mb-0">{{ $totalPoints }}</h5>
                    <small>Point Features</small>
                </div>
            </div>
        </div>

        <!-- Map and Building List Row -->
        <div class="row g-4">
            <!-- Map Column -->
            <div class="col-lg-8">
                <div class="stat-card p-3">
                    <div class="map-toolbar">
                        <i class="fas fa-draw-polygon text-primary"></i>
                        <span class="fw-semibold">Building Polygons with GIS ID</span>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-secondary" id="zoomFitBtn">
                                <i class="fas fa-expand"></i> Fit to View
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="resetViewBtn">
                                <i class="fas fa-home"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div id="wardMap" style="height: 550px; width: 100%;"></div>
                </div>
            </div>

            <!-- Building List Column -->
            <div class="col-lg-4">
                <div class="stat-card p-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-building me-2" style="color:#1679AB;"></i>
                        Buildings with GIS ID
                        <span class="badge bg-primary ms-2">{{ $gisIdCount }}</span>
                    </h5>
                    <input type="text" id="buildingSearch" class="form-control search-box mb-3"
                           placeholder="🔍 Search by GIS ID or Owner Name...">
                    <div class="building-list" id="buildingList">
                        @php
                            $filteredBuildings = array_filter($polygons, function($polygon) {
                                return !empty($polygon->gisid);
                            });
                        @endphp
                        @forelse($filteredBuildings as $index => $polygon)
                        <div class="building-item" data-gisid="{{ $polygon->gisid }}" data-index="{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary">GIS ID: {{ $polygon->gisid }}</strong>
                                    <div class="small text-muted">
                                        Owner: {{ $polygon->owner_name ?? 'N/A' }}
                                    </div>
                                </div>
                                <i class="fas fa-location-dot text-danger"></i>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-draw-polygon fa-2x mb-2"></i>
                            <p>No buildings with GIS ID found in this ward.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    // Store building data for map
    const buildingsData = @json($polygons);
    const roadsData = @json($roads);
    const pointsData = @json($points);

    let map;
    let vectorSource;
    let vectorLayer;
    let currentPopup;
    let buildingFeatures = [];

    // Initialize OpenLayers Map
    function initMap() {
        // Create vector source
        vectorSource = new ol.source.Vector();

        // Create vector layer with styling
        vectorLayer = new ol.layer.Vector({
            source: vectorSource,
            style: function(feature) {
                const gisid = feature.get('gisid');
                // Buildings with GIS ID get special styling
                if (gisid && gisid !== '') {
                    return new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(22, 121, 171, 0.3)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#1679AB',
                            width: 2
                        }),
                        text: new ol.style.Text({
                            text: gisid.toString(),
                            font: '12px Poppins',
                            fill: new ol.style.Fill({ color: '#102C57' }),
                            stroke: new ol.style.Stroke({ color: 'white', width: 2 }),
                            offsetY: -10
                        })
                    });
                } else {
                    return new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(255, 177, 177, 0.2)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#FFB1B1',
                            width: 1.5
                        })
                    });
                }
            }
        });

        // Create map with OSM base layer
        map = new ol.Map({
            target: 'wardMap',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                vectorLayer
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat([78.1198, 9.9252]), // Default to Tamil Nadu center
                zoom: 12
            })
        });

        // Add buildings to map
        addBuildingsToMap();

        // Add roads to map
        addRoadsToMap();

        // Add points to map
        addPointsToMap();

        // Fit to buildings if any exist
        if (buildingFeatures.length > 0) {
            const extent = vectorSource.getExtent();
            if (extent && !ol.extent.isEmpty(extent)) {
                map.getView().fit(extent, { padding: [50, 50, 50, 50] });
            }
        }
    }

    // Parse WKT to OpenLayers geometry
    function wktToGeometry(wkt) {
        if (!wkt) return null;
        try {
            // Simple WKT parser for Polygon and MultiPolygon
            if (wkt.includes('POLYGON')) {
                // Extract coordinates
                const coordsMatch = wkt.match(/\(\((.*?)\)\)/);
                if (coordsMatch) {
                    const points = coordsMatch[1].split(',');
                    const coordinates = points.map(point => {
                        const [lng, lat] = point.trim().split(' ').map(Number);
                        return ol.proj.fromLonLat([lng, lat]);
                    });
                    return new ol.geom.Polygon([coordinates]);
                }
            } else if (wkt.includes('LINESTRING')) {
                const coordsMatch = wkt.match(/\((.*?)\)/);
                if (coordsMatch) {
                    const points = coordsMatch[1].split(',');
                    const coordinates = points.map(point => {
                        const [lng, lat] = point.trim().split(' ').map(Number);
                        return ol.proj.fromLonLat([lng, lat]);
                    });
                    return new ol.geom.LineString(coordinates);
                }
            } else if (wkt.includes('POINT')) {
                const coordsMatch = wkt.match(/\((.*?)\)/);
                if (coordsMatch) {
                    const [lng, lat] = coordsMatch[1].trim().split(' ').map(Number);
                    return new ol.geom.Point(ol.proj.fromLonLat([lng, lat]));
                }
            }
        } catch(e) {
            console.error('WKT parsing error:', e);
        }
        return null;
    }

    // Add buildings (polygons) to map
    function addBuildingsToMap() {
        buildingsData.forEach((building, index) => {
            if (building.geojson) {
                try {
                    const geojson = JSON.parse(building.geojson);
                    const format = new ol.format.GeoJSON();
                    const feature = format.readFeature(geojson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    });

                    if (feature) {
                        feature.set('id', building.id);
                        feature.set('gisid', building.gisid);
                        feature.set('owner_name', building.owner_name);
                        feature.set('type', 'building');

                        vectorSource.addFeature(feature);
                        buildingFeatures.push(feature);
                    }
                } catch(e) {
                    console.error('Error parsing GeoJSON:', e);
                }
            }
        });
    }

    // Add roads (lines) to map
    function addRoadsToMap() {
        roadsData.forEach(road => {
            if (road.geojson) {
                try {
                    const geojson = JSON.parse(road.geojson);
                    const format = new ol.format.GeoJSON();
                    const feature = format.readFeature(geojson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    });

                    if (feature) {
                        feature.set('id', road.id);
                        feature.set('road_name', road.road_name);
                        feature.set('type', 'road');

                        feature.setStyle(new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: '#FFB1B1',
                                width: 3,
                                lineDash: [5, 5]
                            })
                        }));

                        vectorSource.addFeature(feature);
                    }
                } catch(e) {
                    console.error('Error parsing road GeoJSON:', e);
                }
            }
        });
    }

    // Add points to map
    function addPointsToMap() {
        pointsData.forEach(point => {
            if (point.geojson) {
                try {
                    const geojson = JSON.parse(point.geojson);
                    const format = new ol.format.GeoJSON();
                    const feature = format.readFeature(geojson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    });

                    if (feature) {
                        feature.set('id', point.id);
                        feature.set('gisid', point.gisid);
                        feature.set('owner_name', point.owner_name);
                        feature.set('door_no', point.new_door_no);
                        feature.set('type', 'point');

                        feature.setStyle(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 6,
                                fill: new ol.style.Fill({ color: '#FFB1B1' }),
                                stroke: new ol.style.Stroke({ color: '#1679AB', width: 2 })
                            })
                        }));

                        vectorSource.addFeature(feature);
                    }
                } catch(e) {
                    console.error('Error parsing point GeoJSON:', e);
                }
            }
        });
    }

    // Show popup with building details
    function showPopup(feature, coordinate) {
        if (currentPopup) {
            map.removeOverlay(currentPopup);
        }

        const gisid = feature.get('gisid');
        const ownerName = feature.get('owner_name') || 'N/A';
        const type = feature.get('type');

        let content = '';
        if (type === 'building') {
            content = `
                <div class="building-info-card" style="min-width: 220px;">
                    <h6 class="fw-bold mb-2" style="color:#102C57;">
                        <i class="fas fa-building"></i> Building Details
                    </h6>
                    <hr class="my-2">
                    <div><strong>GIS ID:</strong> ${gisid || 'Not Assigned'}</div>
                    <div><strong>Owner Name:</strong> ${ownerName}</div>
                    <div><strong>Feature Type:</strong> Building Polygon</div>
                    ${gisid ? `<button class="btn btn-sm btn-primary mt-2" onclick="alert('Viewing details for GIS ID: ${gisid}')">
                        <i class="fas fa-info-circle"></i> View Details
                    </button>` : ''}
                </div>
            `;
        } else if (type === 'road') {
            content = `
                <div class="building-info-card" style="min-width: 200px;">
                    <h6 class="fw-bold mb-2" style="color:#102C57;">
                        <i class="fas fa-road"></i> Road Details
                    </h6>
                    <hr class="my-2">
                    <div><strong>Road Name:</strong> ${feature.get('road_name') || 'N/A'}</div>
                    <div><strong>Feature Type:</strong> Road Line</div>
                </div>
            `;
        } else if (type === 'point') {
            content = `
                <div class="building-info-card" style="min-width: 220px;">
                    <h6 class="fw-bold mb-2" style="color:#102C57;">
                        <i class="fas fa-map-pin"></i> Point Feature
                    </h6>
                    <hr class="my-2">
                    <div><strong>GIS ID:</strong> ${gisid || 'N/A'}</div>
                    <div><strong>Owner Name:</strong> ${ownerName}</div>
                    <div><strong>Door No:</strong> ${feature.get('door_no') || 'N/A'}</div>
                </div>
            `;
        }

        const popupElement = document.createElement('div');
        popupElement.className = 'ol-popup';
        popupElement.innerHTML = content;

        const popup = new ol.Overlay({
            element: popupElement,
            positioning: 'bottom-center',
            stopEvent: true,
            offset: [0, -10]
        });

        map.addOverlay(popup);
        popup.setPosition(coordinate);
        currentPopup = popup;
    }

    // Hide popup
    function hidePopup() {
        if (currentPopup) {
            map.removeOverlay(currentPopup);
            currentPopup = null;
        }
    }

    // Zoom to a specific building by GIS ID
    function zoomToBuilding(gisid) {
        const feature = buildingFeatures.find(f => f.get('gisid') == gisid);
        if (feature) {
            const geometry = feature.getGeometry();
            const extent = geometry.getExtent();
            map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });

            // Show popup
            const center = ol.extent.getCenter(extent);
            showPopup(feature, center);

            // Highlight the feature temporarily
            const originalStyle = feature.getStyle();
            feature.setStyle(new ol.style.Style({
                fill: new ol.style.Fill({ color: 'rgba(255, 177, 177, 0.8)' }),
                stroke: new ol.style.Stroke({ color: '#FF0000', width: 3 })
            }));
            setTimeout(() => {
                feature.setStyle(originalStyle);
            }, 2000);
        }
    }

    // Map click handler
    function setupMapClick() {
        map.on('click', function(evt) {
            const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
                return feature;
            });

            if (feature) {
                const coordinate = evt.coordinate;
                showPopup(feature, coordinate);
            } else {
                hidePopup();
            }
        });
    }

    // Setup building list click handlers
    function setupBuildingList() {
        const buildingItems = document.querySelectorAll('.building-item');
        buildingItems.forEach(item => {
            item.addEventListener('click', function() {
                const gisid = this.getAttribute('data-gisid');
                if (gisid) {
                    zoomToBuilding(gisid);

                    // Highlight active item
                    buildingItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('buildingSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                buildingItems.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }

    // Setup map controls
    function setupMapControls() {
        document.getElementById('zoomFitBtn')?.addEventListener('click', () => {
            if (buildingFeatures.length > 0) {
                const extent = vectorSource.getExtent();
                if (extent && !ol.extent.isEmpty(extent)) {
                    map.getView().fit(extent, { padding: [50, 50, 50, 50], duration: 500 });
                }
            }
        });

        document.getElementById('resetViewBtn')?.addEventListener('click', () => {
            map.getView().setCenter(ol.proj.fromLonLat([78.1198, 9.9252]));
            map.getView().setZoom(12);
            hidePopup();
        });
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setupMapClick();
        setupBuildingList();
        setupMapControls();
    });
</script>
@endsection
