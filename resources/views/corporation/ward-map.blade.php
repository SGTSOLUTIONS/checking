<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simple OpenLayers Map</title>

    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

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
</head>
<body>

    <div id="map"></div>

    <!-- OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

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

</body>
</html>
