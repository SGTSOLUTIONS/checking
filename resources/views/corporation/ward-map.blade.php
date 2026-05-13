@extends('layouts.commissioner')

@section('title', 'Ward Map')

@section('content')

<div class="container-fluid p-0">

    <div id="map" style="width:100%; height:100vh;"></div>

</div>

@endsection

@push('styles')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

<style>

    #map{
        width:100%;
        height:100vh;
    }

</style>

@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

<script>

    let map;

    let polygonLayer;
    let pointLayer;
    let lineLayer;

    // =========================
    // MAP INIT
    // =========================

    function initMap() {

        // Base Layer
        const osmLayer = new ol.layer.Tile({
            source: new ol.source.OSM()
        });

        // Map
        map = new ol.Map({
            target: 'map',

            layers: [
                osmLayer
            ],

            view: new ol.View({
                center: ol.proj.fromLonLat([80.2707, 13.0827]),
                zoom: 16
            })
        });

        // Load Layers
        refreshLayers();
    }

    // =========================
    // REFRESH LAYERS
    // =========================

    function refreshLayers() {

        // Remove old layers
        if (polygonLayer) {
            map.removeLayer(polygonLayer);
        }

        if (pointLayer) {
            map.removeLayer(pointLayer);
        }

        if (lineLayer) {
            map.removeLayer(lineLayer);
        }

        // =========================
        // DATA FROM LARAVEL
        // =========================

        let polygons = @json($polygons ?? []);
        let points = @json($points ?? []);
        let lines = @json($lines ?? []);

        // =========================
        // POLYGON SOURCE
        // =========================

        const polygonSource = new ol.source.Vector();

        polygons.forEach(function(poly) {

            try {

                let coords = typeof poly.coordinates === 'string'
                    ? JSON.parse(poly.coordinates)
                    : poly.coordinates;

                if(coords && coords.length){

                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid
                    });

                    polygonSource.addFeature(feature);
                }

            } catch(e){
                console.log(e);
            }

        });

        polygonLayer = new ol.layer.Vector({

            source: polygonSource,

            style: new ol.style.Style({

                stroke: new ol.style.Stroke({
                    color: 'red',
                    width: 2
                }),

                fill: new ol.style.Fill({
                    color: 'rgba(255,0,0,0.2)'
                })

            })

        });

        // =========================
        // POINT SOURCE
        // =========================

        const pointSource = new ol.source.Vector();

        points.forEach(function(point){

            try{

                let coords = typeof point.coordinates === 'string'
                    ? JSON.parse(point.coordinates)
                    : point.coordinates;

                if(coords && coords.length === 2){

                    const feature = new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                        gisid: point.gisid
                    });

                    pointSource.addFeature(feature);
                }

            }catch(e){
                console.log(e);
            }

        });

        pointLayer = new ol.layer.Vector({

            source: pointSource,

            style: new ol.style.Style({

                image: new ol.style.Circle({

                    radius: 5,

                    fill: new ol.style.Fill({
                        color: 'blue'
                    }),

                    stroke: new ol.style.Stroke({
                        color: '#fff',
                        width: 1
                    })

                })

            })

        });

        // =========================
        // LINE SOURCE
        // =========================

        const lineSource = new ol.source.Vector();

        lines.forEach(function(line){

            try{

                let coords = typeof line.coordinates === 'string'
                    ? JSON.parse(line.coordinates)
                    : line.coordinates;

                if(coords && coords.length){

                    const feature = new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: line.gisid
                    });

                    lineSource.addFeature(feature);
                }

            }catch(e){
                console.log(e);
            }

        });

        lineLayer = new ol.layer.Vector({

            source: lineSource,

            style: new ol.style.Style({

                stroke: new ol.style.Stroke({
                    color: 'green',
                    width: 3
                })

            })

        });

        // =========================
        // ADD LAYERS
        // =========================

        map.addLayer(polygonLayer);
        map.addLayer(lineLayer);
        map.addLayer(pointLayer);

        // =========================
        // FIT TO POLYGON
        // =========================

        if (polygonSource.getFeatures().length > 0) {

            map.getView().fit(
                polygonSource.getExtent(),
                {
                    padding: [20,20,20,20],
                    duration: 1000
                }
            );
        }

        console.log('Layers Refreshed');

    }

    // =========================
    // DOCUMENT READY
    // =========================

    $(document).ready(function(){

        initMap();

    });

</script>

@endpush
