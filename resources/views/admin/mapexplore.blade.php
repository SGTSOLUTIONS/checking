@extends('layouts.admin-layout')

@section('css')
<!-- ✅ Include OpenLayers CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
<style>
    #map-container {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
    }
    #map {
        width: 100%;
        height: 100vh;
    }
    #control-panel {
        position: absolute;
        top: 120px;
        right: 40px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        width: 220px;
    }
    #control-panel h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }
    #search-box {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }
    #coordinates {
        position: absolute;
        bottom: 20px;
        left: 465px;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 12px;
        z-index: 1000;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }
    #info-panel {
        position: absolute;
        bottom: 50px;
        left: 10px;
        background: rgba(255, 255, 255, 0.9);
        padding: 10px 15px;
        border-radius: 5px;
        font-size: 14px;
        z-index: 1000;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        max-width: 300px;
        display: none;
    }
    #info-panel h4 {
        margin: 0 0 8px 0;
        color: #4361ee;
    }
    #info-panel p {
        margin: 5px 0;
    }
    #current-layer {
        position: absolute;
        bottom: 20px;
        left: 300px;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 12px;
        z-index: 1000;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div id="map-container">
    <div id="map"></div>

    <!-- ✅ Control Panel -->
    <div id="control-panel">
        <h3 style="font-size: 16px; margin-bottom: 10px;">Map Controls</h3>
        
        <!-- Search box -->
        <input type="text" id="search-box" placeholder="Search location...">
        
        <!-- Layer dropdown -->
        <select id="layer-select" style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 5px;">
            <option value="osm" selected>Street Map</option>
            <option value="satellite">Satellite</option>
            <option value="topo">Topographic</option>
            <option value="dark">Dark Mode</option>
        </select>
    </div>

    <!-- Info Panels -->
    <div id="current-layer">Current Layer: Street Map</div>
    <div id="coordinates">Lat: 20.5937, Lon: 78.9629</div>
    <div id="info-panel">
        <h4>Location Information</h4>
        <p>Click on the map to see information about a location.</p>
    </div>
</div>
@endsection

@section('script')
<!-- ✅ Include OpenLayers JS -->
<script src="https://cdn.jsdelivr.net/npm/ol@latest/ol.js"></script>

<script>
$(document).ready(function() {

    // --- Define Layers ---
    const osmLayer = new ol.layer.Tile({
        source: new ol.source.OSM(),
        visible: true,
        name: 'OpenStreetMap'
    });

    const satelliteLayer = new ol.layer.Tile({
        source: new ol.source.XYZ({
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
        }),
        visible: false,
        name: 'Satellite'
    });

    const topoLayer = new ol.layer.Tile({
        source: new ol.source.XYZ({
            url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
        }),
        visible: false,
        name: 'Topographic'
    });

    const darkLayer = new ol.layer.Tile({
        source: new ol.source.XYZ({
            url: 'https://{a-c}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png'
        }),
        visible: false,
        name: 'Dark Mode'
    });

    // --- Create Map ---
    const map = new ol.Map({
        target: 'map',
        layers: [osmLayer, satelliteLayer, topoLayer, darkLayer],
        view: new ol.View({
            center: ol.proj.fromLonLat([78.9629, 20.5937]), // Center: India
            zoom: 5
        })
    });

    // --- Layer Switcher (Dropdown Based) ---
    $('#layer-select').on('change', function() {
        const selectedLayer = $(this).val();

        // Set layer visibility
        osmLayer.setVisible(selectedLayer === 'osm');
        satelliteLayer.setVisible(selectedLayer === 'satellite');
        topoLayer.setVisible(selectedLayer === 'topo');
        darkLayer.setVisible(selectedLayer === 'dark');

        // Update label text
        const selectedText = $("#layer-select option:selected").text();
        $('#current-layer').text('Current Layer: ' + selectedText);
    });

    // --- Show Coordinates on Mouse Move ---
    map.on('pointermove', function(evt) {
        const coord = ol.proj.toLonLat(evt.coordinate);
        $('#coordinates').text(
            'Lat: ' + coord[1].toFixed(4) + ', Lon: ' + coord[0].toFixed(4)
        );
    });
});
</script>
@endsection
