{{-- resources/views/surveyor/ward-map.blade.php --}}
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ... (keep all existing styles from your original) ... */

        /* Mobile Bottom Navigation Bar */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1200;
            padding: 8px 12px;
            justify-content: space-around;
            align-items: center;
        }

        .mobile-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .mobile-nav-btn i {
            font-size: 22px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .mobile-nav-btn span {
            font-size: 10px;
            color: #64748b;
        }

        .mobile-nav-btn.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .mobile-nav-btn.active i,
        .mobile-nav-btn.active span {
            color: white;
        }

        /* Shop Forms Styling */
        .shop-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }

        .remove-shop-btn {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }

            .mobile-header {
                display: block;
            }

            .mobile-bottom-nav {
                display: flex;
            }

            #map {
                height: calc(100vh - 56px - 70px);
            }

            /* Hide floating buttons on mobile */
            #layerToggleBtn,
            #searchToggleBtn,
            #editToggleBtn,
            #liveToggleBtn,
            #routeBtn {
                display: none;
            }

            /* Panels on mobile - appear above bottom nav */
            .layer-switcher,
            .search-Lable,
            .edit-Lable {
                position: fixed;
                top: auto;
                bottom: 80px;
                left: 12px;
                right: 12px;
                width: auto;
                max-width: calc(100% - 24px);
                border-radius: 16px;
                padding: 15px;
                z-index: 1250;
                background: white;
            }

            .route-info-panel {
                left: 12px;
                right: 12px;
                bottom: 80px;
            }

            .toast-notification {
                bottom: 90px;
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

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <button class="mobile-nav-btn" id="mobileLayerBtn">
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
        <button class="mobile-nav-btn" id="mobileEditBtn">
            <i class="fas fa-pen-to-square"></i>
            <span>Edit</span>
        </button>
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

    <!-- Floating Action Buttons (Desktop only) -->
    <div id="layerToggleBtn" title="Toggle Layers"><i class="fas fa-layer-group"></i></div>
    <div id="searchToggleBtn" title="Search"><i class="fas fa-search"></i></div>
    <div id="liveToggleBtn" title="My Location"><i class="fas fa-location-dot"></i></div>
    <div id="routeBtn" title="Get Route"><i class="fas fa-route"></i></div>
    <div id="editToggleBtn" title="Edit Tools"><i class="fas fa-pen-to-square"></i></div>

    <!-- Search Panel -->
    <div id="searchLabel" class="search-Lable closed">
        <div class="search-input-wrapper">
            <input type="text" id="searchInput" class="form-control" placeholder="Enter GIS ID or Road Name..."
                autocomplete="off">
            <button id="searchGisidBtn"><i class="fas fa-search"></i> Search</button>
        </div>
        <div id="searchSuggestions" class="search-suggestions"></div>
    </div>

    <!-- Layer Switcher Panel -->
    <div id="layerSwitcher" class="layer-switcher closed">
        <div class="layer-header">
            <div><i class="fas fa-layer-group"></i><span>Map Layers</span></div>
            <button id="closeLayerPanel"><i class="fas fa-times"></i></button>
        </div>
        <label class="layer-item"><input type="checkbox" id="osmToggle" checked><span class="checkmark"></span><span>OSM
                Map</span></label>
        <label class="layer-item"><input type="checkbox" id="satelliteToggle"><span
                class="checkmark"></span><span>Satellite</span></label>
        <label class="layer-item"><input type="checkbox" id="droneToggle" checked><span class="checkmark"></span><span>Drone
                Image</span></label>
        <label class="layer-item"><input type="checkbox" id="boundaryToggle" checked><span
                class="checkmark"></span><span>Ward Boundary</span></label>
        <label class="layer-item"><input type="checkbox" id="polygonToggle" checked><span
                class="checkmark"></span><span>Buildings</span></label>
        <label class="layer-item"><input type="checkbox" id="lineToggle" checked><span
                class="checkmark"></span><span>Roads</span></label>
        <label class="layer-item"><input type="checkbox" id="pointToggle" checked><span
                class="checkmark"></span><span>Points</span></label>
    </div>

    <!-- Edit Panel -->
    <div id="editLabel" class="edit-Lable closed">
        <select class="form-select" id="editToolSelect">
            <option value="none">Select Tool</option>
            <option value="Polygon">Draw Polygon</option>
            <option value="Line">Draw Line</option>
            <option value="Point">Draw Point</option>
            <option value="Modify">Modify Feature</option>
            <option value="Delete">Delete Feature</option>
        </select>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash-alt me-2"></i>Delete Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Warning: This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter GIS ID to Delete</label>
                            <input type="text" class="form-control" id="deleteGisIdInput" name="gisid"
                                placeholder="e.g., A1001 or 1001" required>
                        </div>
                        <div id="featurePreview" class="mt-3 p-3 border rounded" style="display: none;">
                            <h6>Feature Details:</h6>
                            <p id="previewText"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Feature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Data passed from controller
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let selectedFeature = null;
            let currentRoute = null;
            let isMobile = window.innerWidth <= 768;
            let searchDebounceTimer = null;
            let currentSuggestions = [];
            let selectedSuggestionIndex = -1;
            let draw, modify, select;
            let featureClickHandler = null;
            let shopTimeout = null;
            let currentShopCount = 0;
            let shopDetailsArray = [];

            // Build search index
            let searchIndex = [];

            polygons.forEach(poly => {
                searchIndex.push({
                    id: poly.gisid,
                    type: 'polygon',
                    title: `GIS ID: ${poly.gisid}`,
                    subtitle: `Building (${poly.sqfeet || 0} sqft)`,
                    icon: 'fas fa-building',
                    data: poly,
                    searchText: `${poly.gisid} building polygon ${poly.sqfeet}`
                });
            });

            lines.forEach(line => {
                if (line.road_name) {
                    searchIndex.push({
                        id: line.gisid,
                        type: 'line',
                        title: line.road_name,
                        subtitle: `Road (GIS ID: ${line.gisid})`,
                        icon: 'fas fa-road',
                        data: line,
                        searchText: `${line.road_name} ${line.gisid} road`
                    });
                } else {
                    searchIndex.push({
                        id: line.gisid,
                        type: 'line',
                        title: `GIS ID: ${line.gisid}`,
                        subtitle: 'Road',
                        icon: 'fas fa-road',
                        data: line,
                        searchText: `${line.gisid} road`
                    });
                }
            });

            points.forEach(point => {
                searchIndex.push({
                    id: point.gisid,
                    type: 'point',
                    title: `GIS ID: ${point.gisid}`,
                    subtitle: 'Point Location',
                    icon: 'fas fa-map-marker-alt',
                    data: point,
                    searchText: `${point.gisid} point location`
                });
            });

            // Routes
            let routes = {
                addPolygonFeature: "{{ route('surveyor.add.polygon.feature') }}",
                addLineFeature: "{{ route('surveyor.add.line.feature') }}",
                addPointFeature: "{{ route('surveyor.add.point.feature') }}",
                surveyorModifyFeature: "{{ route('surveyor.modify.feature') }}",
                deleteFeature: "{{ route('surveyor.delete.feature') }}",
                surveyorPointDataUpload: "{{ route('surveyor.point.data.upload') }}"
            };

            function showToast(message, type = 'info') {
                $('.toast-notification').remove();
                const toast = $(`<div class="toast-notification ${type}">${message}</div>`);
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
            }

            function showFlashMessage(message, type) {
                showToast(message, type);
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function getSearchSuggestions(query) {
                if (!query || query.length < 2) return [];
                const lowerQuery = query.toLowerCase();
                return searchIndex.filter(item =>
                    item.searchText.toLowerCase().includes(lowerQuery) ||
                    item.title.toLowerCase().includes(lowerQuery)
                ).slice(0, 10);
            }

            function displaySuggestions(suggestions) {
                const container = $('#searchSuggestions');
                container.empty();
                if (suggestions.length === 0) {
                    container.removeClass('show');
                    return;
                }
                suggestions.forEach((suggestion, index) => {
                    container.append(`
                        <div class="suggestion-item" data-index="${index}">
                            <div class="suggestion-icon"><i class="${suggestion.icon}"></i></div>
                            <div class="suggestion-content">
                                <div class="suggestion-title">${escapeHtml(suggestion.title)}</div>
                                <div class="suggestion-subtitle">${escapeHtml(suggestion.subtitle)}</div>
                            </div>
                            <div class="suggestion-type">${suggestion.type === 'polygon' ? 'Building' : (suggestion.type === 'line' ? 'Road' : 'Point')}</div>
                        </div>
                    `);
                });
                container.addClass('show');
                currentSuggestions = suggestions;
            }

            function selectSuggestion(suggestion) {
                $('#searchInput').val(suggestion.title);
                $('#searchSuggestions').removeClass('show');
                navigateToFeature(suggestion.data, suggestion.type);
            }

            function navigateToFeature(data, type) {
                try {
                    let feature;
                    if (type === 'polygon') {
                        const coords = JSON.parse(data.coordinates);
                        feature = new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: data.gisid,
                            type: "Polygon",
                            sqfeet: data.sqfeet || "0"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 22
                        });
                        selectedFeature = feature;
                        showToast("Found GIS ID: " + data.gisid, 'success');
                    } else if (type === 'line') {
                        let coords = typeof data.coordinates === 'string' ? JSON.parse(data.coordinates) : data
                            .coordinates;
                        if (coords.length === 1 && Array.isArray(coords[0])) coords = coords[0];
                        feature = new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: data.gisid,
                            road_name: data.road_name,
                            type: "Line"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 20
                        });
                        selectedFeature = feature;
                        showToast("Found: " + (data.road_name || data.gisid), 'success');
                    } else if (type === 'point') {
                        const coords = JSON.parse(data.coordinates);
                        feature = new ol.Feature({
                            geometry: new ol.geom.Point(coords),
                            gisid: data.gisid,
                            type: "Point"
                        });
                        map.getView().fit(feature.getGeometry().getExtent(), {
                            duration: 1000,
                            padding: [50, 50, 50, 50],
                            maxZoom: 22
                        });
                        selectedFeature = feature;
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
                        highlightLayer.getSource().addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        }));
                        map.addLayer(highlightLayer);
                        setTimeout(() => map.removeLayer(highlightLayer), 3000);
                        showToast("Found GIS ID: " + data.gisid, 'success');
                    }
                    $('#searchLabel').addClass('closed');
                } catch (e) {
                    console.error('Error:', e);
                    showToast("Error displaying feature", 'error');
                }
            }

            // Drone image config
            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            // Create layers
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

            // Style functions
            function createPointStyle(feature) {
                const gisid = feature.get("gisid");
                const pointCount = pointDatas.filter(d => d.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
                let color = "blue";
                if (polygonData) {
                    color = pointCount > 0 ? (polygonData.number_bill == pointCount ? "green" : "red") : "blue";
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
                const polygonData = polygonDatas.find(d => d.gisid == gisid);
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
                                color: "#000"
                            }),
                            backgroundFill: new ol.style.Fill({
                                color: "#fff"
                            }),
                            backgroundStroke: new ol.style.Stroke({
                                color: "#000",
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

            // Vector sources
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

            const lineSource = new ol.source.Vector();
            lines.forEach(l => {
                try {
                    let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                        .coordinates;
                    if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                        .isArray(coords[0][0])) coords = coords[0];
                    if (coords && coords.length >= 2) {
                        lineSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.LineString(coords),
                            gisid: l.gisid,
                            type: "Line",
                            road_name: l.road_name || null
                        }));
                    }
                } catch (e) {
                    console.error('Line parse error:', e);
                }
            });

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

            const polygonLayer = new ol.layer.Vector({
                source: polygonSource,
                style: createPolygonStyle,
                visible: true
            });
            const lineLayer = new ol.layer.Vector({
                source: lineSource,
                style: createLineStyle,
                visible: true
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

            // Highlight layer for delete preview
            const highlightSource = new ol.source.Vector();
            const highlightLayer = new ol.layer.Vector({
                source: highlightSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#ff0000',
                        width: 4
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(255,0,0,0.2)'
                    })
                })
            });
            highlightLayer.setZIndex(1000);

            // Initialize map
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, satelliteLayer, droneLayer, polygonLayer, lineLayer, pointLayer,
                    routeLayer, highlightLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Add boundary layer if exists
            if (ward.boundary && ward.boundary[0]) {
                const boundary = ward.boundary[0];
                const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
                const boundaryLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [new ol.Feature({
                            geometry: new ol.geom.Polygon([transformedBoundary])
                        })]
                    }),
                    style: new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "red",
                            width: 3
                        })
                    })
                });
                map.addLayer(boundaryLayer);
            }

            // COMPLETE handlePointClick function with ALL form fields
            function handlePointClick(properties) {
                const gisid = properties["gisid"];
                resetPointFormFields();
                $('#pointModal').remove();

                $("body").append(`
                    <div class="modal fade" id="pointModal" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                    <h5 class="modal-title">
                                        <i class="fas fa-map-marker-alt me-2"></i>Point Data Collection - GIS ID: ${gisid}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" enctype="multipart/form-data" id="pointForm">
                                    @csrf
                                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                        <!-- Basic Information Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="type" class="form-label">Assessment Type <span class="text-danger">*</span></label>
                                                        <select name="type" id="type" class="form-control" required>
                                                            <option value="OLD">OLD</option>
                                                            <option value="NEW">NEW</option>
                                                            <option value="OTHER WARD">OTHER WARD</option>
                                                            <option value="NO_TAX">NO TAX</option>
                                                            <option value="VACCAND">VACCAND</option>
                                                        </select>
                                                        <div id="type_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="suveyedbtn"></div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="pointgis" class="form-label">GIS ID <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="pointgis" name="point_gisid" value="${gisid}" readonly>
                                                        <div id="point_gisid_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="assessment" class="form-label">Assessment No <span class="text-danger">*</span></label>
                                                        <input type="text" name="assessment" class="form-control" id="assessment">
                                                        <div id="assessment_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="old_assessment" class="form-label">Old Assessment</label>
                                                        <input type="text" name="old_assessment" class="form-control" id="old_assessment">
                                                        <div id="old_assessment_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="owner_name" class="form-label">Owner Name</label>
                                                        <input type="text" name="owner_name" class="form-control" id="owner_name">
                                                        <div id="owner_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="present_owner_name" class="form-label">Present Owner Name</label>
                                                        <input type="text" name="present_owner_name" class="form-control" id="present_owner_name">
                                                        <div id="present_owner_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="no_of_shop" class="form-label">Number of Shops</label>
                                                        <input type="number" name="no_of_shop" class="form-control" id="no_of_shop" min="0" step="1" value="0">
                                                        <div id="no_of_shop_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="no_of_persons" class="form-label">Number of Persons</label>
                                                        <input type="number" name="no_of_persons" class="form-control" id="no_of_persons" min="0" step="1" value="0">
                                                        <div id="no_of_persons_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Property Details Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-building"></i> Property Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="floor" class="form-label">Floor</label>
                                                        <input type="number" name="floor" class="form-control" id="floor" min="0" step="1">
                                                        <div id="floor_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="old_door_no" class="form-label">Old Door No</label>
                                                        <input type="text" name="old_door_no" class="form-control" id="old_door_no">
                                                        <div id="old_door_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="new_door_no" class="form-label">New Door No</label>
                                                        <input type="text" name="new_door_no" class="form-control" id="new_door_no">
                                                        <div id="new_door_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="bill_usage" class="form-label">Bill Usage</label>
                                                        <select name="bill_usage" id="bill_usage" class="form-control">
                                                            <option value="">SELECT USAGE</option>
                                                            <option value="RESIDENTIAL">RESIDENTIAL</option>
                                                            <option value="COMMERCIAL">COMMERCIAL</option>
                                                            <option value="EDUCATIONAL INSTITUTIONS">EDUCATIONAL INSTITUTIONS</option>
                                                            <option value="GOVERNMENT BUILDING">GOVERNMENT BUILDING</option>
                                                            <option value="INDUSTRIAL">INDUSTRIAL</option>
                                                            <option value="OFFICE / LODGE / THEATER / RESTAURANTS">OFFICE / LODGE / THEATER / RESTAURANTS</option>
                                                            <option value="STAR HOTEL">STAR HOTEL</option>
                                                        </select>
                                                        <div id="bill_usage_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="eb" class="form-label">EB Number</label>
                                                        <input type="text" name="eb" class="form-control" id="eb">
                                                        <div id="eb_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shop Details Container -->
                                        <div id="shopDetailsContainer"></div>

                                        <!-- Tax Details Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #333;">
                                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Tax Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="water_tax" class="form-label">Water Tax</label>
                                                        <input type="text" name="water_tax" class="form-control" id="water_tax" step="0.01" min="0">
                                                        <div id="water_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="old_water_tax" class="form-label">Old Water Tax</label>
                                                        <input type="text" name="old_water_tax" class="form-control" id="old_water_tax" step="0.01" min="0">
                                                        <div id="old_water_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="professional_tax" class="form-label">Professional Tax</label>
                                                        <input type="text" name="professional_tax" class="form-control" id="professional_tax">
                                                        <div id="professional_tax_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="gst" class="form-label">GST</label>
                                                        <input type="text" name="gst" class="form-control" id="gst" placeholder="GST Number">
                                                        <div id="gst_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="trade_income" class="form-label">Trade Income</label>
                                                        <input type="number" name="trade_income" class="form-control" id="trade_income" step="0.01" min="0">
                                                        <div id="trade_income_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Documents Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-id-card"></i> Documents & Contact</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="aadhar_no" class="form-label">Aadhar Number</label>
                                                        <input type="text" name="aadhar_no" class="form-control" id="aadhar_no" maxlength="12" pattern="[0-9]{12}" placeholder="12-digit Aadhar">
                                                        <div id="aadhar_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="ration_no" class="form-label">Ration Number</label>
                                                        <input type="text" name="ration_no" class="form-control" id="ration_no">
                                                        <div id="ration_no_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="phone" class="form-label">Phone Number</label>
                                                        <input type="tel" name="phone_number" class="form-control" id="phone" pattern="[0-9]{10}" maxlength="10" placeholder="10-digit mobile">
                                                        <div id="phone_number_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quality Check Card -->
                                        <div class="card mb-3 d-none" id="qualityCheckCard">
                                            <div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Quality Check</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_area" class="form-label">QC Area</label>
                                                        <input type="text" name="qc_area" class="form-control" id="qc_area">
                                                        <div id="qc_area_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_usage" class="form-label">QC Usage</label>
                                                        <select name="qc_usage" id="qc_usage" class="form-control">
                                                            <option value="">Select Usage</option>
                                                            <option value="Residential">Residential</option>
                                                            <option value="Commercial">Commercial</option>
                                                            <option value="Mixed">Mixed</option>
                                                        </select>
                                                        <div id="qc_usage_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_name" class="form-label">QC Name</label>
                                                        <input type="text" name="qc_name" class="form-control" id="qc_name" placeholder="QC Officer Name">
                                                        <div id="qc_name_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="qc_remarks" class="form-label">QC Remarks</label>
                                                        <input type="text" name="qc_remarks" class="form-control" id="qc_remarks">
                                                        <div id="qc_remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks Card -->
                                        <div class="card mb-3">
                                            <div class="card-header" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;">
                                                <h6 class="mb-0"><i class="fas fa-comment"></i> Remarks</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="establishment_remarks" class="form-label">Establishment Remarks</label>
                                                        <textarea name="establishment_remarks" class="form-control" id="establishment_remarks" rows="2" placeholder="Enter establishment remarks..."></textarea>
                                                        <div id="establishment_remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="remarks" class="form-label">Office Remarks</label>
                                                        <textarea name="remarks" class="form-control" id="remarks" rows="2" placeholder="Enter general remarks..."></textarea>
                                                        <div id="remarks_error" class="error-message text-danger small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Shop Details Append Area -->
                                        <div id="append"></div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Close
                                        </button>
                                        <button type="submit" id="pointSubmit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Point Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `);

                const polygonData = polygonDatas.find(data => data.gisid === gisid);
                const polygonNumOfBill = polygonData ? polygonData.number_bill : null;
                const matchingPointsCount = pointDatas.filter(data => data.point_gisid === gisid).length;

                if (polygonNumOfBill > matchingPointsCount) {
                    $("#pointgis").val(gisid);
                    initDynamicShopDetails();
                    $("#pointModal").modal("show");
                } else {
                    showFlashMessage(`Already this building has ${matchingPointsCount} bills`, "error");
                }
            }

            function resetPointFormFields() {
                $("#pointgis, #assessment, #old_assessment, #owner_name, #present_owner_name, #worker_name, #building_data_id,#no_of_persons")
                    .val("");
                $("#floor, #old_door_no, #new_door_no, #plot_area, #eb, #otsarea").val("");
                $("#water_tax, #old_water_tax, #halfyeartax, #balance, #professional_tax, #gst, #trade_income").val(
                    "");
                $("#aadhar_no, #ration_no, #phone").val("");
                $("#qc_area, #qc_name, #qc_remarks").val("");
                $("#establishment_remarks, #remarks").val("");
                $("#type").val("OLD");
                $("#bill_usage, #shop_category, #qc_usage").val("");

                const appendArea = $('#append');
                const container = $('#shopDetailsContainer');
                if (container.length) {
                    const shops = container.find('.shop-item');
                    if (shops.length > 0) {
                        shops.fadeOut(300, function() {
                            container.empty();
                            currentShopCount = 0;
                            $('#no_of_shop').val(0);
                            appendArea.find('.card.mb-3').fadeOut(300, function() {
                                $(this).remove();
                            });
                        });
                    } else {
                        appendArea.empty();
                        currentShopCount = 0;
                        $('#no_of_shop').val(0);
                    }
                } else {
                    appendArea.empty();
                    currentShopCount = 0;
                    $('#no_of_shop').val(0);
                }
                $(".error-message").html("");
                $(".is-invalid").removeClass("is-invalid");
            }

            function initDynamicShopDetails() {
                $('#no_of_shop').off('change keyup').on('change keyup', function() {
                    if (shopTimeout) clearTimeout(shopTimeout);
                    shopTimeout = setTimeout(() => {
                        let shopCount = parseInt($(this).val()) || 0;
                        if (shopCount < 0) shopCount = 0;
                        if ($(this).val() !== shopCount.toString()) $(this).val(shopCount);
                        generateShopForms(shopCount);
                    }, 300);
                });
                $(document).on('click', '.remove-shop-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const shopId = $(this).data('shop-id');
                    const currentCount = parseInt($('#no_of_shop').val()) || 0;
                    if (currentCount > 0) {
                        const newCount = currentCount - 1;
                        $('#no_of_shop').val(newCount).trigger('change');
                    }
                });
            }

            function generateShopForms(shopCount) {
                const appendArea = $('#append');
                if (currentShopCount === shopCount) return;
                if (shopCount === 0) {
                    const container = $('#shopDetailsContainer');
                    if (container.length) {
                        const shops = container.find('.shop-item');
                        if (shops.length > 0) {
                            shops.fadeOut(300, function() {
                                container.empty();
                                currentShopCount = 0;
                                $('#no_of_shop').val(0);
                                appendArea.find('.card.mb-3').fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            appendArea.empty();
                            currentShopCount = 0;
                        }
                    } else {
                        appendArea.empty();
                        currentShopCount = 0;
                    }
                    return;
                }
                let container = $('#shopDetailsContainer');
                if (container.length === 0) {
                    const shopCard = $(`
                        <div class="card mb-3">
                            <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; display: flex; justify-content: space-between; align-items: center;">
                                <h6 class="mb-0"><i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount > 1 ? 's' : ''})</h6>
                                <button type="button" class="btn btn-sm btn-light" id="addAllShopsBtn" style="border-radius: 20px;">
                                    <i class="fas fa-plus"></i> Add All
                                </button>
                            </div>
                            <div class="card-body" id="shopDetailsContainer"></div>
                        </div>
                    `);
                    appendArea.append(shopCard);
                    container = $('#shopDetailsContainer');
                    $('#addAllShopsBtn').off('click').on('click', function() {
                        const newCount = currentShopCount + 1;
                        $('#no_of_shop').val(newCount).trigger('change');
                    });
                }
                if (shopCount > currentShopCount) {
                    for (let i = currentShopCount + 1; i <= shopCount; i++) {
                        addShopForm(i, container);
                    }
                } else if (shopCount < currentShopCount) {
                    for (let i = currentShopCount; i > shopCount; i--) {
                        removeShopForm(i, container);
                    }
                }
                currentShopCount = shopCount;
                const header = appendArea.find('.card-header h6');
                if (header.length) {
                    header.html(
                        `<i class="fas fa-store"></i> Shop Details (${shopCount} Shop${shopCount !== 1 ? 's' : ''})`
                        );
                }
            }

            function addShopForm(shopNumber, container) {
                const shopHtml = `
                    <div class="shop-item" data-shop-id="${shopNumber}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-store me-2"></i>Shop ${shopNumber}</h6>
                            <button type="button" class="remove-shop-btn" data-shop-id="${shopNumber}">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prof Tax Assessment</label>
                                <input type="text" name="prof_tax_assessment_${shopNumber}" class="form-control" placeholder="Enter prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Old Prof Tax Assessment</label>
                                <input type="text" name="old_prof_tax_assessment_${shopNumber}" class="form-control" placeholder="Enter old prof tax assessment">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Floor</label>
                                <input type="text" name="shop_floor_${shopNumber}" class="form-control" placeholder="Enter floor number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Name</label>
                                <input type="text" name="shop_name_${shopNumber}" class="form-control" placeholder="Enter shop name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Owner Name</label>
                                <input type="text" name="shop_owner_name_${shopNumber}" class="form-control" placeholder="Enter owner name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Category</label>
                                <select name="shop_category_${shopNumber}" class="form-control">
                                    <option value="">Select Category</option>
                                    <option value="Grocery">Grocery</option>
                                    <option value="Clothing">Clothing</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Restaurant">Restaurant</option>
                                    <option value="Pharmacy">Pharmacy</option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Shop Mobile</label>
                                <input type="tel" name="shop_mobile_${shopNumber}" class="form-control" placeholder="Mobile number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_${shopNumber}" class="form-control" placeholder="License number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Number of Employees</label>
                                <input type="number" name="number_of_employee_${shopNumber}" class="form-control" placeholder="Employee count">
                            </div>
                        </div>
                    </div>
                `;
                container.append(shopHtml);
            }

            function removeShopForm(shopNumber, container) {
                $(`.shop-item[data-shop-id="${shopNumber}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            }

            // Point form submission with ALL data
            $("#pointForm").off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const shopCount = parseInt($('#no_of_shop').val()) || 0;
                formData.append('total_shops', shopCount);
                for (let i = 1; i <= shopCount; i++) {
                    formData.append(`prof_tax_assessment_${i}`, $(`input[name="prof_tax_assessment_${i}"]`)
                        .val() || '');
                    formData.append(`old_prof_tax_assessment_${i}`, $(
                        `input[name="old_prof_tax_assessment_${i}"]`).val() || '');
                    formData.append(`shop_floor_${i}`, $(`input[name="shop_floor_${i}"]`).val() || '');
                    formData.append(`shop_name_${i}`, $(`input[name="shop_name_${i}"]`).val() || '');
                    formData.append(`shop_owner_name_${i}`, $(`input[name="shop_owner_name_${i}"]`).val() ||
                        '');
                    formData.append(`shop_category_${i}`, $(`select[name="shop_category_${i}"]`).val() ||
                        '');
                    formData.append(`shop_mobile_${i}`, $(`input[name="shop_mobile_${i}"]`).val() || '');
                    formData.append(`license_${i}`, $(`input[name="license_${i}"]`).val() || '');
                    formData.append(`number_of_employee_${i}`, $(`input[name="number_of_employee_${i}"]`)
                        .val() || '');
                }
                $("#pointSubmit").prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    type: "POST",
                    url: routes.surveyorPointDataUpload,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showFlashMessage(response.message, "success");
                        $("#pointModal").modal("hide");
                        if (response.pointDatas) pointDatas = response.pointDatas;
                        if (response.points) points = response.points;
                        refreshVectorLayer();
                        resetPointFormFields();
                        $('#append').empty();
                        $('#no_of_shop').val('');
                        currentShopCount = 0;
                    },
                    error: function(xhr) {
                        let errorMsg = "An error occurred while processing your request.";
                        if (xhr.responseJSON && xhr.responseJSON.msg) errorMsg = xhr
                            .responseJSON.msg;
                        showFlashMessage(errorMsg, "error");
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(value[0]);
                            });
                        }
                    },
                    complete: function() {
                        $("#pointSubmit").prop("disabled", false).html(
                            '<i class="fas fa-save me-2"></i>Save Point Data');
                    }
                });
            });

            function refreshVectorLayer() {
                polygonSource.clear();
                lineSource.clear();
                pointSource.clear();
                polygons.forEach(poly => {
                    try {
                        let coords = JSON.parse(poly.coordinates);
                        polygonSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Polygon(coords),
                            gisid: poly.gisid,
                            type: "Polygon",
                            sqfeet: poly.sqfeet || "0"
                        }));
                    } catch (e) {}
                });
                lines.forEach(l => {
                    try {
                        let coords = typeof l.coordinates === 'string' ? JSON.parse(l.coordinates) : l
                            .coordinates;
                        if (coords.length === 1 && Array.isArray(coords[0]) && coords[0].length > 0 && Array
                            .isArray(coords[0][0])) coords = coords[0];
                        if (coords && coords.length >= 2) {
                            lineSource.addFeature(new ol.Feature({
                                geometry: new ol.geom.LineString(coords),
                                gisid: l.gisid,
                                type: "Line",
                                road_name: l.road_name || null
                            }));
                        }
                    } catch (e) {}
                });
                points.forEach(p => {
                    try {
                        let coords = JSON.parse(p.coordinates);
                        pointSource.addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords),
                            gisid: p.gisid,
                            type: "Point"
                        }));
                    } catch (e) {}
                });
                highlightSource.clear();
            }

            // ... (rest of the functions - removeDrawInteractions, activateDrawPolygon, activateDrawLine, activateDrawPoint, activateModify, activateDelete, delete functionality, panel toggles, search, live location, route functions remain the same as your original)

            // Setup click handler
            function setupOriginalClickHandler() {
                featureClickHandler = function(evt) {
                    if (isModifyMode || isDrawingActive) return;
                    let hasDrawingActive = false;
                    map.getInteractions().forEach((interaction) => {
                        if (interaction instanceof ol.interaction.Draw) hasDrawingActive = true;
                    });
                    if (hasDrawingActive) return;
                    const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
                    if (feature) {
                        const properties = feature.getProperties();
                        const geometryType = feature.getGeometry().getType();
                        if (geometryType === "Point") handlePointClick(properties);
                        else if (geometryType === "Polygon") {
                            selectedFeature = feature;
                            showToast(`Selected Polygon: ${properties.gisid}`, 'success');
                        } else if (geometryType === "LineString" || geometryType === "MultiLineString") {
                            selectedFeature = feature;
                            showToast(`Selected Road: ${properties.road_name || properties.gisid}`, 'success');
                        }
                    }
                };
                map.on('click', featureClickHandler);
            }

            let isModifyMode = false;
            let isDrawingActive = false;

            function removeDrawInteractions() {
                map.getInteractions().forEach(interaction => {
                    if (interaction instanceof ol.interaction.Draw || interaction instanceof ol.interaction
                        .Modify || interaction instanceof ol.interaction.Select) {
                        map.removeInteraction(interaction);
                    }
                });
                isModifyMode = false;
                isDrawingActive = false;
            }

            function activateDrawPolygon() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: polygonSource,
                    type: "Polygon"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addPolygonFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Polygon",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            polygons = response.polygons;
                            points = response.points;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateDrawLine() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: lineSource,
                    type: "LineString"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addLineFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Line",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            lines = response.lines;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateDrawPoint() {
                removeDrawInteractions();
                isDrawingActive = true;
                draw = new ol.interaction.Draw({
                    source: pointSource,
                    type: "Point"
                });
                map.addInteraction(draw);
                draw.on("drawend", function(event) {
                    const coordinates = event.feature.getGeometry().getCoordinates();
                    $.ajax({
                        url: routes.addPointFeature,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            type: "Point",
                            coordinates: JSON.stringify(coordinates)
                        },
                        success: function(response) {
                            points = response.points;
                            refreshVectorLayer();
                            showFlashMessage(response.message, "success");
                            $("#editToolSelect").val("none");
                            removeDrawInteractions();
                        },
                        error: function() {
                            showFlashMessage("An error occurred.", "error");
                            removeDrawInteractions();
                        }
                    });
                });
            }

            function activateModify() {
                removeDrawInteractions();
                isModifyMode = true;
                select = new ol.interaction.Select({
                    layers: [polygonLayer, lineLayer, pointLayer],
                    condition: ol.events.condition.click
                });
                modify = new ol.interaction.Modify({
                    features: select.getFeatures()
                });
                map.addInteraction(select);
                map.addInteraction(modify);
                modify.on('modifyend', function(evt) {
                    evt.features.forEach(function(feature) {
                        const geometry = feature.getGeometry();
                        const coordinates = geometry.getCoordinates();
                        const type = feature.get('type');
                        const gisid = feature.get('gisid');
                        $.ajax({
                            url: routes.surveyorModifyFeature,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                            },
                            data: {
                                gisid: gisid,
                                type: type,
                                coordinates: JSON.stringify(coordinates)
                            },
                            success: function(response) {
                                if (response.success) {
                                    showFlashMessage('Feature updated successfully',
                                        'success');
                                    if (response.polygons) polygons = response.polygons;
                                    if (response.lines) lines = response.lines;
                                    if (response.points) points = response.points;
                                    refreshVectorLayer();
                                } else showFlashMessage(response.message, 'error');
                            },
                            error: function() {
                                showFlashMessage('Error updating feature', 'error');
                                refreshVectorLayer();
                            }
                        });
                    });
                });
            }

            function activateDelete() {
                removeDrawInteractions();
                $("#editToolSelect").val("none");
                $("#deleteModal").modal("show");
            }

            // Delete functionality
            $("#deleteForm").submit(function(e) {
                e.preventDefault();
                const gisid = $("#deleteGisIdInput").val().trim();
                if (!gisid) {
                    showFlashMessage("Please enter a GIS ID", "error");
                    return;
                }
                $.ajax({
                    url: routes.deleteFeature,
                    type: "POST",
                    data: {
                        gisid: gisid,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showFlashMessage(response.message, "success");
                            $("#deleteModal").modal("hide");
                            if (response.polygons) polygons = response.polygons;
                            if (response.lines) lines = response.lines;
                            if (response.points) points = response.points;
                            refreshVectorLayer();
                            highlightSource.clear();
                            $("#deleteForm")[0].reset();
                        } else showFlashMessage(response.message, "error");
                    },
                    error: function() {
                        showFlashMessage("An error occurred", "error");
                    }
                });
            });

            $("#deleteGisIdInput").on('input', function() {
                const gisid = $(this).val().trim();
                if (!gisid) {
                    highlightSource.clear();
                    return;
                }
                let found = false;
                pointSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) lineSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) polygonSource.forEachFeature(f => {
                    if (f.get('gisid') && f.get('gisid').toString() === gisid) {
                        highlightSource.clear();
                        highlightSource.addFeature(f.clone());
                        found = true;
                    }
                });
                if (!found) highlightSource.clear();
            });

            $("#deleteModal").on('hidden.bs.modal', function() {
                highlightSource.clear();
                $("#deleteForm")[0].reset();
            });

            // Panel toggles for desktop
            $('#layerToggleBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
                $('#searchLabel, #editLabel').addClass('closed');
            });
            $('#closeLayerPanel').click(function() {
                $('#layerSwitcher').addClass('closed');
            });
            $('#closeRouteBtn').click(function() {
                $('#routeInfoPanel').addClass('closed');
                routeSource.clear();
            });
            $('#searchToggleBtn').click(function(e) {
                e.stopPropagation();
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher, #editLabel').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(), 100);
            });
            $('#editToggleBtn').click(function(e) {
                e.stopPropagation();
                $('#editLabel').toggleClass('closed');
                $('#searchLabel, #layerSwitcher').addClass('closed');
            });

            // Mobile bottom navigation handlers
            $('#mobileLayerBtn').click(function() {
                $('#layerSwitcher').toggleClass('closed');
                $('#searchLabel, #editLabel').addClass('closed');
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileSearchBtn').click(function() {
                $('#searchLabel').toggleClass('closed');
                $('#layerSwitcher, #editLabel').addClass('closed');
                if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(), 100);
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileLocationBtn').click(function() {
                toggleLiveLocation();
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileRouteBtn').click(function() {
                if (!selectedFeature) {
                    showToast('Please search for a location first', 'error');
                    return;
                }
                if (!currentLocationMarker) {
                    if (confirm('Enable location for route calculation?')) {
                        toggleLiveLocation();
                        setTimeout(() => {
                            if (currentLocationMarker) calculateAndDisplayRoute(selectedFeature);
                        }, 2500);
                    }
                    return;
                }
                calculateAndDisplayRoute(selectedFeature);
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });
            $('#mobileEditBtn').click(function() {
                $('#editLabel').toggleClass('closed');
                $('#searchLabel, #layerSwitcher').addClass('closed');
                $(this).addClass('active').siblings().removeClass('active');
                setTimeout(() => $(this).removeClass('active'), 200);
            });

            $('#editToolSelect').on('change', function() {
                const value = $(this).val();
                removeDrawInteractions();
                if (value === "Polygon") activateDrawPolygon();
                else if (value === "Line") activateDrawLine();
                else if (value === "Point") activateDrawPoint();
                else if (value === "Modify") activateModify();
                else if (value === "Delete") activateDelete();
                else {
                    isModifyMode = false;
                    isDrawingActive = false;
                }
                $('#editLabel').addClass('closed');
            });

            // Layer toggles
            $('#osmToggle').change(function() {
                osmLayer.setVisible($(this).is(':checked'));
            });
            $('#satelliteToggle').change(function() {
                satelliteLayer.setVisible($(this).is(':checked'));
            });
            $('#droneToggle').change(function() {
                droneLayer.setVisible($(this).is(':checked'));
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

            // Search input
            $('#searchInput').on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(searchDebounceTimer);
                if (query.length < 2) {
                    $('#searchSuggestions').removeClass('show');
                    return;
                }
                searchDebounceTimer = setTimeout(() => displaySuggestions(getSearchSuggestions(query)),
                300);
            });

            $('#searchGisidBtn').on('click', function() {
                const searchvalue = $("#searchInput").val().trim();
                if (!searchvalue) {
                    showToast("Please enter a GIS ID or Road Name", 'error');
                    return;
                }
                let found = polygons.find(p => p.gisid == searchvalue);
                if (found) {
                    navigateToFeature(found, 'polygon');
                    return;
                }
                found = lines.find(l => l.gisid == searchvalue || (l.road_name && l.road_name.toLowerCase()
                    .includes(searchvalue.toLowerCase())));
                if (found) {
                    navigateToFeature(found, 'line');
                    return;
                }
                found = points.find(p => p.gisid == searchvalue);
                if (found) {
                    navigateToFeature(found, 'point');
                    return;
                }
                showToast("GIS ID or Road Name not found", 'error');
            });

            $(document).on('click', '.suggestion-item', function() {
                const index = $(this).data('index');
                if (index !== undefined && currentSuggestions[index]) selectSuggestion(currentSuggestions[
                    index]);
            });

            // Live location
            let currentLocationMarker = null;

            function toggleLiveLocation() {
                if (!("geolocation" in navigator)) {
                    showToast("Geolocation not supported", 'error');
                    return;
                }
                showToast('Fetching your location...', 'info');
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords
                        .latitude]);
                        map.getView().animate({
                            center: coords,
                            zoom: 18,
                            duration: 1000
                        });
                        if (currentLocationMarker) map.removeLayer(currentLocationMarker);
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
                        currentLocationMarker.getSource().addFeature(new ol.Feature({
                            geometry: new ol.geom.Point(coords)
                        }));
                        map.addLayer(currentLocationMarker);
                        showToast('Location found!', 'success');
                    },
                    function(error) {
                        let msg = "Error getting location";
                        if (error.code === error.PERMISSION_DENIED) msg = "Please enable location permissions";
                        showToast(msg, 'error');
                    }
                );
            }
            $('#liveToggleBtn').click(toggleLiveLocation);

            // Route functions
            async function getRouteFromOSRM(startCoord, endCoord) {
                const url =
                    `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson`;
                const response = await fetch(url);
                const data = await response.json();
                if (data.code !== 'Ok' || !data.routes.length) throw new Error('No route found');
                return data.routes[0];
            }

            function drawRouteOnMap(geometry) {
                routeSource.clear();
                if (geometry.type === 'LineString') {
                    const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
                    routeSource.addFeature(new ol.Feature({
                        geometry: new ol.geom.LineString(coordinates)
                    }));
                    map.getView().fit(routeSource.getExtent(), {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }

            function formatDistance(meters) {
                return meters >= 1000 ? (meters / 1000).toFixed(1) + ' km' : Math.round(meters) + ' m';
            }

            function formatDuration(seconds) {
                return seconds >= 60 ? Math.floor(seconds / 60) + ' min' : Math.floor(seconds) + ' sec';
            }

            async function calculateAndDisplayRoute(feature) {
                if (!currentLocationMarker) {
                    showToast('Please enable your location first', 'error');
                    return;
                }
                $('#loadingSpinner').show();
                try {
                    const currentCoords = currentLocationMarker.getSource().getFeatures()[0].getGeometry()
                        .getCoordinates();
                    const geometry = feature.getGeometry();
                    let targetCoords = geometry.getType() === 'Point' ? geometry.getCoordinates() : ol.extent
                        .getCenter(geometry.getExtent());
                    const currentLonLat = ol.proj.toLonLat(currentCoords);
                    const targetLonLat = ol.proj.toLonLat(targetCoords);
                    let route;
                    try {
                        route = await getRouteFromOSRM(currentLonLat, targetLonLat);
                    } catch (e) {
                        route = {
                            distance: ol.sphere.getDistance(ol.proj.fromLonLat(currentLonLat), ol.proj
                                .fromLonLat(targetLonLat)),
                            geometry: {
                                type: "LineString",
                                coordinates: [currentLonLat, targetLonLat]
                            }
                        };
                    }
                    drawRouteOnMap(route.geometry);
                    $('#routeDistance').text(formatDistance(route.distance));
                    $('#routeDuration').text(formatDuration(route.duration / 1.39));
                    $('#destinationName').text(`GIS ID: ${feature.get('gisid') || 'Selected Location'}`);
                    $('#routeInfoPanel').removeClass('closed');
                    currentRoute = route;
                } catch (error) {
                    showToast('Error calculating route', 'error');
                } finally {
                    $('#loadingSpinner').hide();
                }
            }

            $('#routeBtn').click(async function() {
                if (!selectedFeature) {
                    showToast('Please search for a location first', 'error');
                    return;
                }
                if (!currentLocationMarker) {
                    if (confirm('Enable location for route calculation?')) {
                        toggleLiveLocation();
                        setTimeout(() => {
                            if (currentLocationMarker) calculateAndDisplayRoute(
                            selectedFeature);
                        }, 2500);
                    }
                    return;
                }
                await calculateAndDisplayRoute(selectedFeature);
            });

            $('#startNavigationBtn').click(function() {
                if (currentRoute && isMobile) {
                    window.open(
                        `https://www.google.com/maps/dir/?api=1&destination=${currentRoute.endCoord ? currentRoute.endCoord[1] : ''},${currentRoute.endCoord ? currentRoute.endCoord[0] : ''}`,
                        '_blank');
                }
            });

            $(document).click(function(event) {
                if (!$(event.target).closest(
                        '#layerSwitcher, #layerToggleBtn, #searchLabel, #searchToggleBtn, #routeInfoPanel, #routeBtn, #editLabel, #editToggleBtn, #searchSuggestions, .mobile-bottom-nav'
                        ).length) {
                    $('#layerSwitcher, #searchLabel, #editLabel').addClass('closed');
                    $('#searchSuggestions').removeClass('show');
                }
            });

            $(window).resize(function() {
                isMobile = window.innerWidth <= 768;
            });
            $(document).keydown(function(e) {
                if (e.key === 'l' || e.key === 'L') {
                    $('#layerSwitcher').toggleClass('closed');
                    $('#searchLabel, #editLabel').addClass('closed');
                }
                if (e.key === 's' || e.key === 'S') {
                    $('#searchLabel').toggleClass('closed');
                    $('#layerSwitcher, #editLabel').addClass('closed');
                    if (!$('#searchLabel').hasClass('closed')) setTimeout(() => $('#searchInput').focus(),
                        100);
                }
                if (e.key === 'Escape') {
                    $('#layerSwitcher, #searchLabel, #routeInfoPanel, #editLabel').addClass('closed');
                    $('#searchSuggestions').removeClass('show');
                }
            });

            setupOriginalClickHandler();
            showToast('Map loaded successfully', 'success');
        });
    </script>
@endsection
