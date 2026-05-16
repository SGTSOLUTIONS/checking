<!-- resources/views/surveyor/ward-map.blade.php -->
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

@section('content')
    <div class="container-fluid py-3 desktop-only">
        <h3 class="fw-bold mb-3">
            <i class="fas fa-map-marked-alt me-2"></i>
            Ward Map View - Ward {{ $ward->ward_no }}
        </h3>
    </div>

    <div id="map"></div>


@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Global variables
            let polygons = @json($polygons);
            let lines = @json($lines);
            let points = @json($points);
            let pointDatas = @json($pointDatas ?? []);
            let polygonDatas = @json($polygonDatas ?? []);
            let ward = @json($ward ?? []);
            let mis = @json($misData ?? []);

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

            let droneImageURL = "{{ asset($ward->drone_image) }}";
            let imageExtent = [
                {{ $ward->extent_left ?? 0 }},
                {{ $ward->extent_bottom ?? 0 }},
                {{ $ward->extent_right ?? 0 }},
                {{ $ward->extent_top ?? 0 }}
            ];

            let currentLocationMarker = null;
            let locationWatchId = null;
            let isLiveLocationActive = false;
            let currentRoute = null;
            let routeSteps = [];
            let currentStepIndex = 0;
            let navigationMode = false;
            let navigationInterval = null;
            let isMobile = $(window).width() <= 768;
            let draw = null;
            let modify = null;
            let select = null;
            let isModifyMode = false;
            let selectedFeature = null;
            let isDrawingActive = false;
            let featureClickHandler = null;

            // Shop details variables
            let shopTimeout = null;
            let currentShopCount = 0;

            // Style Functions
            function createPointStyle(feature) {
                const gisid = feature.get("gisid");

                const pointCount = pointDatas.filter(data => data.point_gisid == gisid).length;
                const polygonData = polygonDatas.find(data => data.gisid == gisid);

                let color = "blue"; // default

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

                // Get polygon center point
                const geometry = feature.getGeometry();
                const centerPoint = geometry.getInteriorPoint();

                return [
                    // Polygon Border Style
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: color,
                            width: 4,
                            lineJoin: "round",
                            lineCap: "round"
                        })
                    }),

                    // Label Style
                    // Label Style
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



            // Layer Definitions
            const osmLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                visible: true
            });
            const terrainLayer = new ol.layer.Tile({
                source: new ol.source.OSM({
                    url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
                }),
                visible: false
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

            // Boundary Layer
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

            const routeSource = new ol.source.Vector();
            const routeLayer = new ol.layer.Vector({
                source: routeSource,
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: '#ff0000',
                        width: 4,
                        lineDash: [5, 5]
                    })
                })
            });
            const locationSource = new ol.source.Vector();
            const locationLayer = new ol.layer.Vector({
                source: locationSource,
                style: createHumanLocationMarkerStyle
            });

            // Map Initialization
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer, terrainLayer, satelliteLayer, droneLayer, boundaryLayer, polygonLayer,
                    lineLayer, pointLayer, highlightLayer, routeLayer, locationLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });

            // Fit view to show all features
            function fitViewToAllFeatures() {
                const extent = ol.extent.createEmpty();
                lineSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                polygonSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                pointSource.forEachFeature(f => ol.extent.extend(extent, f.getGeometry().getExtent()));
                if (!ol.extent.isEmpty(extent)) {
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        duration: 1000
                    });
                }
            }
            setTimeout(fitViewToAllFeatures, 500);


        });
    </script>
@endsection
