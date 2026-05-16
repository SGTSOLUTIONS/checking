<!-- resources/views/surveyor/ward-map.blade.php -->
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ==========================================
   MAP CONTAINER
========================================== */

#map {
    width: 100%;
    height: 100vh;
    position: relative;
    background: #f5f5f5;
}

/* ==========================================
   DESKTOP HEADER
========================================== */

.desktop-only {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 1000;

    background: rgba(255, 255, 255, 0.95);

    padding: 12px 18px;

    border-radius: 12px;

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    backdrop-filter: blur(5px);
}

.desktop-only h3 {
    margin: 0;
    font-size: 20px;
    color: #222;
}

/* ==========================================
   OPENLAYERS CONTROLS
========================================== */

.ol-control button {
    background-color: #ffffff !important;
    color: #333 !important;

    border-radius: 8px !important;

    border: none !important;

    width: 36px !important;
    height: 36px !important;

    font-size: 18px !important;

    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.ol-control button:hover {
    background-color: #f0f0f0 !important;
}

/* ==========================================
   ZOOM CONTROL POSITION
========================================== */

.ol-zoom {
    top: 80px !important;
    left: 10px !important;
}

/* ==========================================
   ATTRIBUTION
========================================== */

.ol-attribution {
    background: rgba(255, 255, 255, 0.9) !important;

    border-radius: 8px;

    padding: 4px 8px;
}

/* ==========================================
   POPUP STYLE (OPTIONAL)
========================================== */

.ol-popup {
    position: absolute;

    background-color: white;

    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);

    padding: 15px;

    border-radius: 10px;

    border: 1px solid #cccccc;

    bottom: 12px;

    left: -50px;

    min-width: 220px;
}

.ol-popup:after,
.ol-popup:before {
    top: 100%;

    border: solid transparent;

    content: " ";

    height: 0;
    width: 0;

    position: absolute;

    pointer-events: none;
}

.ol-popup:after {
    border-top-color: white;

    border-width: 10px;

    left: 48px;

    margin-left: -10px;
}

.ol-popup:before {
    border-top-color: #cccccc;

    border-width: 11px;

    left: 48px;

    margin-left: -11px;
}

/* ==========================================
   MOBILE RESPONSIVE
========================================== */

@media (max-width: 768px) {

    .desktop-only {

        top: 8px;
        left: 8px;
        right: 8px;

        padding: 10px 14px;
    }

    .desktop-only h3 {

        font-size: 16px;
    }

    .ol-zoom {

        top: 70px !important;
    }

    .ol-control button {

        width: 32px !important;
        height: 32px !important;

        font-size: 16px !important;
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


@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
    $(document).ready(function() {

    // ==========================================
    // GLOBAL VARIABLES
    // ==========================================
    let polygons = @json($polygons);
    let lines = @json($lines);
    let points = @json($points);
    let pointDatas = @json($pointDatas ?? []);
    let polygonDatas = @json($polygonDatas ?? []);
    let ward = @json($ward ?? []);

    let droneImageURL = "{{ asset($ward->drone_image) }}";

    let imageExtent = [
        {{ $ward->extent_left ?? 0 }},
        {{ $ward->extent_bottom ?? 0 }},
        {{ $ward->extent_right ?? 0 }},
        {{ $ward->extent_top ?? 0 }}
    ];

    // ==========================================
    // POINT STYLE
    // ==========================================
    function createPointStyle(feature) {

        const gisid = feature.get("gisid");

        const pointCount = pointDatas.filter(
            data => data.point_gisid == gisid
        ).length;

        const polygonData = polygonDatas.find(
            data => data.gisid == gisid
        );

        let color = "blue";

        if (polygonData) {

            if (pointCount > 0) {

                color =
                    polygonData.number_bill == pointCount
                    ? "green"
                    : "red";

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

                scale: 1.2,

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

    // ==========================================
    // POLYGON STYLE
    // ==========================================
    function createPolygonStyle(feature) {

        const gisid = feature.get("gisid");

        const sqft = feature.get("sqfeet") || "0";

        const polygonData = polygonDatas.find(
            data => data.gisid == gisid
        );

        const color = polygonData ? "red" : "blue";

        const centerPoint = feature
            .getGeometry()
            .getInteriorPoint();

        return [

            // Border Style
            new ol.style.Style({

                stroke: new ol.style.Stroke({

                    color: color,

                    width: 4,

                    lineJoin: "round",

                    lineCap: "round"

                })

            }),

            // Label Style
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

                    textAlign: "center"

                })

            })

        ];

    }

    // ==========================================
    // LINE STYLE
    // ==========================================
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

                text: road_name
                    ? String(road_name)
                    : "",

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

    // ==========================================
    // COMMON FEATURE ADD FUNCTION
    // ==========================================
    function addFeatures(
        data,
        source,
        geometryType,
        extraProps = () => ({})
    ) {

        data.forEach(item => {

            try {

                let coords =
                    typeof item.coordinates === "string"
                    ? JSON.parse(item.coordinates)
                    : item.coordinates;

                if (!coords) return;

                let geometry;

                switch (geometryType) {

                    case "Polygon":

                        geometry = new ol.geom.Polygon(coords);

                        break;

                    case "LineString":

                        // Fix nested coordinates
                        if (
                            coords.length === 1 &&
                            Array.isArray(coords[0]) &&
                            Array.isArray(coords[0][0])
                        ) {
                            coords = coords[0];
                        }

                        if (coords.length < 2) return;

                        geometry = new ol.geom.LineString(coords);

                        break;

                    case "Point":

                        geometry = new ol.geom.Point(coords);

                        break;

                    default:
                        return;
                }

                source.addFeature(

                    new ol.Feature({

                        geometry: geometry,

                        gisid: item.gisid,

                        type: geometryType,

                        ...extraProps(item)

                    })

                );

            } catch (e) {

                console.error(
                    `${geometryType} parse error:`,
                    e,
                    item
                );

            }

        });

    }

    // ==========================================
    // BASE LAYERS
    // ==========================================
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

    // ==========================================
    // DRONE IMAGE LAYER
    // ==========================================
    const droneLayer = new ol.layer.Image({

        source: new ol.source.ImageStatic({

            url: droneImageURL,

            imageExtent: imageExtent,

            imageSmoothing: false

        }),

        opacity: 0.9,

        visible: true

    });

    // ==========================================
    // POLYGON LAYER
    // ==========================================
    const polygonSource = new ol.source.Vector();

    addFeatures(
        polygons,
        polygonSource,
        "Polygon",
        (item) => ({
            sqfeet: item.sqfeet || "0"
        })
    );

    const polygonLayer = new ol.layer.Vector({

        source: polygonSource,

        style: createPolygonStyle,

        visible: true

    });

    // ==========================================
    // LINE LAYER
    // ==========================================
    const lineSource = new ol.source.Vector();

    addFeatures(
        lines,
        lineSource,
        "LineString",
        (item) => ({
            road_name: item.road_name || null
        })
    );

    const lineLayer = new ol.layer.Vector({

        source: lineSource,

        style: createLineStyle,

        visible: true

    });

    // ==========================================
    // POINT LAYER
    // ==========================================
    const pointSource = new ol.source.Vector();

    addFeatures(
        points,
        pointSource,
        "Point"
    );

    const pointLayer = new ol.layer.Vector({

        source: pointSource,

        style: createPointStyle,

        visible: true

    });

    // ==========================================
    // BOUNDARY LAYER
    // ==========================================
    const boundary = ward.boundary[0];

    const transformedBoundary = boundary.map(
        pt => ol.proj.fromLonLat(pt)
    );

    const boundaryPolygon = new ol.geom.Polygon([
        transformedBoundary
    ]);

    const boundaryLayer = new ol.layer.Vector({

        source: new ol.source.Vector({

            features: [
                new ol.Feature({
                    geometry: boundaryPolygon
                })
            ]

        }),

        style: new ol.style.Style({

            stroke: new ol.style.Stroke({

                color: "red",

                width: 3

            })

        })

    });

    // ==========================================
    // MAP INITIALIZATION
    // ==========================================
    const map = new ol.Map({

        target: 'map',

        layers: [

            osmLayer,

            terrainLayer,

            satelliteLayer,

            droneLayer,

            boundaryLayer,

            polygonLayer,

            lineLayer,

            pointLayer

        ],

        view: new ol.View({

            projection: "EPSG:3857",

            center: ol.extent.getCenter(imageExtent),

            zoom: 17

        })

    });

    // ==========================================
    // FIT VIEW
    // ==========================================
    function fitViewToAllFeatures() {

        const extent = ol.extent.createEmpty();

        polygonSource.forEachFeature(feature => {

            ol.extent.extend(
                extent,
                feature.getGeometry().getExtent()
            );

        });

        lineSource.forEachFeature(feature => {

            ol.extent.extend(
                extent,
                feature.getGeometry().getExtent()
            );

        });

        pointSource.forEachFeature(feature => {

            ol.extent.extend(
                extent,
                feature.getGeometry().getExtent()
            );

        });

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
