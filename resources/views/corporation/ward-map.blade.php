{{-- resources/views/corporation/ward-map.blade.php --}}

@extends('layouts.commissioner')

@section('title', 'Ward Map')

@push('styles')
<style>
    #map {
        width: 100%;
        height: 600px;
        border-radius: 15px;
        overflow: hidden;
    }

    .map-container {
        background: #fff;
        padding: 15px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="map-container">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold">
                        <i class="fas fa-map text-primary me-2"></i>
                        OpenLayers Map
                    </h4>
                </div>

                <div id="map"></div>

            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {

    const map = new ol.Map({
        target: 'map',

        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],

        view: new ol.View({
            center: ol.proj.fromLonLat([80.2707, 13.0827]),
            zoom: 10
        })
    });

});
</script>

@endpush
