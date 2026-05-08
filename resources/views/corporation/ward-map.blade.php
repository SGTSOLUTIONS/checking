{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')

@section('title', 'Ward Map')

@section('styles')
<style>
    #map {
        width: 100%;
        height: 100vh;
        border-radius: 12px;
        overflow: hidden;
    }

    .map-card {
        background: #fff;
        border-radius: 20px;
        padding: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="map-card">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-dark">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                        Ward Map
                    </h4>

                    <button class="btn btn-primary">
                        <i class="fas fa-layer-group me-2"></i>
                        Map Layer
                    </button>
                </div>

                <div id="map"></div>

            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    // OpenLayers Map
    const map = new ol.Map({
        target: 'map',

        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],

        view: new ol.View({
            center: ol.proj.fromLonLat([80.2707, 13.0827]), // Chennai
            zoom: 10
        })
    });
</script>

@endsection
