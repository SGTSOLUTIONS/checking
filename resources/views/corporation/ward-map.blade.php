{{-- resources/views/corporation/ward-map.blade.php --}}
@extends('layouts.commissioner')


@section('styles')
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        #map {
            width: 100%;
            height: 100vh;
        }
    </style>
@endsection

    <div id="map"></div>





@endsection
@section('scripts')
 <script>
        // Create map
        const map = new ol.Map({
            target: 'map',

            layers: [
                // OpenStreetMap layer
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],

            view: new ol.View({
                // Chennai coordinates
                center: ol.proj.fromLonLat([80.2707, 13.0827]),
                zoom: 10
            })
        });
    </script>

@endsection
