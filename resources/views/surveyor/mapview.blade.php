<!-- resources/views/surveyor/ward-map.blade.php -->
@extends('layouts.surveyor-layout')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #map {
            width: 100%;
            height: 90vh;
            border-radius: 10px;
            border: 2px solid #ddd;
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
            const map = new ol.Map({
                target: 'map',
                layers: [osmLayer,, satelliteLayer, droneLayer, boundaryLayer
                ],
                view: new ol.View({
                    projection: "EPSG:3857",
                    center: ol.extent.getCenter(imageExtent),
                    zoom: 17
                })
            });
        });
    </script>
@endsection
