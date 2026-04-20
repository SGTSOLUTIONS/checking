<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D World Map - No Token Required</title>
    <script src="https://cdn.jsdelivr.net/npm/cesium@1.105/Build/Cesium/Cesium.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/cesium@1.105/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #2a3c7a);
            color: white;
            overflow: hidden;
        }
        
        #cesiumContainer {
            width: 100%;
            height: 100vh;
            position: relative;
        }
        
        .header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .controls {
            position: absolute;
            top: 80px;
            left: 20px;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 12px;
            width: 280px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .control-group {
            margin-bottom: 15px;
        }
        
        .control-group h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #00c6ff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 5px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 8px;
            background: rgba(0, 120, 215, 0.7);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-align: left;
        }
        
        .btn:hover {
            background: rgba(0, 150, 255, 0.9);
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .info-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 12px;
            width: 300px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: none;
        }
        
        .info-panel h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #00c6ff;
        }
        
        .info-panel p {
            margin-bottom: 8px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        
        .status-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 8px 15px;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .coordinates {
            font-family: monospace;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2000;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .loading-spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid #00c6ff;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            z-index: 1001;
            display: none;
        }
        
        .legend {
            position: absolute;
            top: 80px;
            right: 20px;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 12px;
            width: 200px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>3D Terrain Map - No Token Required</h1>
        <p>Natural landscape visualization • No API token needed</p>
    </div>
    
    <div id="cesiumContainer"></div>
    
    <div class="controls">
        <div class="control-group">
            <h3>Navigation</h3>
            <button class="btn" id="flyToNY"><i>🗽</i> New York</button>
            <button class="btn" id="flyToParis"><i>🗼</i> Paris</button>
            <button class="btn" id="flyToTokyo"><i>🗾</i> Tokyo</button>
            <button class="btn" id="flyToSydney"><i>⚓</i> Sydney</button>
            <button class="btn" id="resetView"><i>🌎</i> Reset View</button>
        </div>
        
        <div class="control-group">
            <h3>Terrain & Visualization</h3>
            <button class="btn" id="toggleTerrain"><i>⛰️</i> Toggle Terrain</button>
            <button class="btn" id="changeBaseMap"><i>🗺️</i> Change Base Map</button>
            <button class="btn" id="toggleDayNight"><i>🌙</i> Toggle Day/Night</button>
        </div>
        
        <div class="control-group">
            <h3>Map Features</h3>
            <button class="btn" id="toggleMarkers"><i>📍</i> Toggle Markers</button>
            <button class="btn" id="toggleLabels"><i>🔤</i> Toggle Labels</button>
            <button class="btn" id="addRandomMarker"><i>➕</i> Add Random Marker</button>
        </div>
    </div>
    
    <div class="legend">
        <h3 style="color: #00c6ff; margin-bottom: 10px; font-size: 16px;">Map Legend</h3>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #1e90ff;"></div>
            <span>City Markers</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #32cd32;"></div>
            <span>Natural Features</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #ff4500;"></div>
            <span>Landmarks</span>
        </div>
    </div>
    
    <div class="info-panel" id="infoPanel">
        <button class="close-btn" id="closeInfo">×</button>
        <h3 id="infoTitle">Location Information</h3>
        <p id="infoCoordinates">Coordinates: </p>
        <p id="infoElevation">Elevation: </p>
        <p id="infoDescription">Description: </p>
    </div>
    
    <div class="status-bar">
        <div class="coordinates">Longitude: 0.0000° • Latitude: 0.0000° • Elevation: 0m</div>
        <div class="fps">FPS: 60</div>
    </div>
    
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
        <p>Loading 3D Terrain Map...</p>
    </div>
    
    <div class="tooltip" id="tooltip"></div>

    <script>
        // No Cesium Ion token needed - using OpenStreetMap and other free data sources
        // No 3D buildings included - only terrain and imagery
        
        // Wait for Cesium to load
        window.addEventListener('load', function() {
            // Hide loading screen after a short delay
            setTimeout(() => {
                document.getElementById('loading').style.display = 'none';
                initCesium();
            }, 1500);
        });
        
        let viewer;
        let terrainEnabled = false;
        let markersEnabled = true;
        let labelsEnabled = true;
        let dayMode = true;
        let baseMapIndex = 0;
        
        // Base map providers (no token required)
        const baseMaps = [
            { name: "OpenStreetMap", provider: new Cesium.OpenStreetMapImageryProvider({ 
                url: 'https://a.tile.openstreetmap.org/'
            })},
            { name: "ESRI World Imagery", provider: new Cesium.ArcGisMapServerImageryProvider({
                url: 'https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer'
            })},
            { name: "Stamen Terrain", provider: new Cesium.OpenStreetMapImageryProvider({
                url: 'https://stamen-tiles.a.ssl.fastly.net/terrain/',
                credit: 'Map tiles by Stamen Design, under CC BY 3.0. Data by OpenStreetMap, under ODbL'
            })},
            { name: "CartoDB Dark", provider: new Cesium.UrlTemplateImageryProvider({
                url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png',
                credit: '© OpenStreetMap contributors, © CartoDB'
            })}
        ];
        
        // Sample locations with coordinates and descriptions
        const locations = [
            { name: "New York City", lon: -74.006, lat: 40.7128, desc: "The most populous city in the United States.", type: "city" },
            { name: "Paris", lon: 2.3522, lat: 48.8566, desc: "Capital city of France, known as the City of Light.", type: "city" },
            { name: "Tokyo", lon: 139.6917, lat: 35.6895, desc: "Capital of Japan, the most populous metropolitan area in the world.", type: "city" },
            { name: "Sydney", lon: 151.2093, lat: -33.8688, desc: "Largest city in Australia, known for its iconic opera house.", type: "city" },
            { name: "Mount Everest", lon: 86.9250, lat: 27.9881, desc: "Earth's highest mountain above sea level.", type: "natural" },
            { name: "Grand Canyon", lon: -112.1121, lat: 36.1069, desc: "Steep-sided canyon carved by the Colorado River.", type: "natural" },
            { name: "Amazon River", lon: -55.0000, lat: -2.0000, desc: "Largest river by discharge volume of water in the world.", type: "natural" },
            { name: "Sahara Desert", lon: 0.0000, lat: 25.0000, desc: "Largest hot desert in the world.", type: "natural" },
            { name: "Statue of Liberty", lon: -74.0445, lat: 40.6892, desc: "Iconic neoclassical sculpture on Liberty Island.", type: "landmark" },
            { name: "Eiffel Tower", lon: 2.2945, lat: 48.8584, desc: "Wrought-iron lattice tower in Paris, France.", type: "landmark" }
        ];
        
        // Initialize Cesium without requiring a token
        function initCesium() {
            // Create the Cesium Viewer with OpenStreetMap as the default base layer
            viewer = new Cesium.Viewer('cesiumContainer', {
                imageryProvider: baseMaps[0].provider,
                terrainProvider: new Cesium.EllipsoidTerrainProvider(),
                timeline: false,
                animation: false,
                baseLayerPicker: false,
                geocoder: false,
                homeButton: false,
                sceneModePicker: true,
                navigationHelpButton: false,
                infoBox: false,
                selectionIndicator: false,
                skyBox: false,
                skyAtmosphere: false
            });
            
            // Set initial view to show the whole Earth
            viewer.camera.setView({
                destination: Cesium.Cartesian3.fromDegrees(-74.006, 40.7128, 10000000)
            });
            
            // Add sample markers
            addSampleMarkers();
            
            // Setup event handlers
            setupEventHandlers();
            
            // Update coordinates in real-time
            viewer.scene.postRender.addEventListener(updateCoordinates);
            
            // Setup FPS counter
            setupFPSCounter();
        }
        
        // Add sample markers to the map
        function addSampleMarkers() {
            const pinBuilder = new Cesium.PinBuilder();
            
            locations.forEach(location => {
                // Choose color based on type
                let color;
                if (location.type === "city") color = Cesium.Color.ROYALBLUE;
                else if (location.type === "natural") color = Cesium.Color.LIME;
                else color = Cesium.Color.ORANGE;
                
                viewer.entities.add({
                    name: location.name,
                    position: Cesium.Cartesian3.fromDegrees(location.lon, location.lat),
                    billboard: {
                        image: pinBuilder.fromColor(color, 48).toDataURL(),
                        verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                        scale: 1.2,
                        heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
                    },
                    description: location.desc,
                    label: {
                        text: location.name,
                        font: '14pt sans-serif',
                        pixelOffset: new Cesium.Cartesian2(0, -40),
                        fillColor: Cesium.Color.WHITE,
                        outlineColor: Cesium.Color.BLACK,
                        outlineWidth: 2,
                        style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                        heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                        show: labelsEnabled
                    }
                });
            });
        }
        
        // Setup all event handlers
        function setupEventHandlers() {
            // Navigation buttons
            document.getElementById('flyToNY').addEventListener('click', () => {
                flyToLocation(-74.006, 40.7128, 5000, "New York City");
            });
            
            document.getElementById('flyToParis').addEventListener('click', () => {
                flyToLocation(2.3522, 48.8566, 5000, "Paris");
            });
            
            document.getElementById('flyToTokyo').addEventListener('click', () => {
                flyToLocation(139.6917, 35.6895, 5000, "Tokyo");
            });
            
            document.getElementById('flyToSydney').addEventListener('click', () => {
                flyToLocation(151.2093, -33.8688, 5000, "Sydney");
            });
            
            document.getElementById('resetView').addEventListener('click', () => {
                viewer.camera.setView({
                    destination: Cesium.Cartesian3.fromDegrees(-74.006, 40.7128, 10000000)
                });
            });
            
            // Map features buttons
            document.getElementById('toggleTerrain').addEventListener('click', () => {
                terrainEnabled = !terrainEnabled;
                if (terrainEnabled) {
                    // For demonstration, we'll use a simple ellipsoid terrain
                    // In a real implementation with token, you would use Cesium.createWorldTerrain()
                    viewer.terrainProvider = new Cesium.EllipsoidTerrainProvider();
                    viewer.scene.globe.depthTestAgainstTerrain = true;
                } else {
                    viewer.terrainProvider = new Cesium.EllipsoidTerrainProvider();
                    viewer.scene.globe.depthTestAgainstTerrain = false;
                }
                document.getElementById('toggleTerrain').innerHTML = terrainEnabled ? 
                    '<i>⛰️</i> Disable Terrain' : '<i>⛰️</i> Enable Terrain';
            });
            
            document.getElementById('toggleMarkers').addEventListener('click', () => {
                markersEnabled = !markersEnabled;
                // Show/hide all billboards (markers)
                viewer.entities.values.forEach(entity => {
                    if (entity.billboard) {
                        entity.billboard.show = markersEnabled;
                    }
                });
                document.getElementById('toggleMarkers').innerHTML = markersEnabled ? 
                    '<i>📍</i> Hide Markers' : '<i>📍</i> Show Markers';
            });
            
            document.getElementById('toggleLabels').addEventListener('click', () => {
                labelsEnabled = !labelsEnabled;
                // Show/hide all labels
                viewer.entities.values.forEach(entity => {
                    if (entity.label) {
                        entity.label.show = labelsEnabled;
                    }
                });
                document.getElementById('toggleLabels').innerHTML = labelsEnabled ? 
                    '<i>🔤</i> Hide Labels' : '<i>🔤</i> Show Labels';
            });
            
            document.getElementById('addRandomMarker').addEventListener('click', () => {
                addRandomMarker();
            });
            
            // Visualization buttons
            document.getElementById('toggleDayNight').addEventListener('click', () => {
                dayMode = !dayMode;
                if (dayMode) {
                    viewer.scene.light = new Cesium.SunLight();
                    viewer.scene.skyAtmosphere.show = true;
                    viewer.scene.skyBox.show = true;
                } else {
                    viewer.scene.light = new Cesium.DirectionalLight({
                        direction: new Cesium.Cartesian3(0.5, 0.5, 1.0)
                    });
                    viewer.scene.skyAtmosphere.show = false;
                    viewer.scene.skyBox.show = false;
                }
                document.getElementById('toggleDayNight').innerHTML = dayMode ? 
                    '<i>🌙</i> Switch to Night' : '<i>☀️</i> Switch to Day';
            });
            
            document.getElementById('changeBaseMap').addEventListener('click', () => {
                baseMapIndex = (baseMapIndex + 1) % baseMaps.length;
                viewer.imageryLayers.removeAll();
                viewer.imageryLayers.addImageryProvider(baseMaps[baseMapIndex].provider);
                document.getElementById('changeBaseMap').innerHTML = 
                    `<i>🗺️</i> Base: ${baseMaps[baseMapIndex].name}`;
            });
            
            // Close info panel
            document.getElementById('closeInfo').addEventListener('click', () => {
                document.getElementById('infoPanel').style.display = 'none';
            });
            
            // Click handler for the globe
            viewer.cesiumWidget.screenSpaceEventHandler.setInputAction(function onLeftClick(event) {
                const picked = viewer.scene.pick(event.position);
                if (Cesium.defined(picked) && Cesium.defined(picked.id)) {
                    // If a marker was clicked, show its info
                    showLocationInfo(picked.id);
                } else {
                    // Otherwise, get the position on the globe
                    const ray = viewer.camera.getPickRay(event.position);
                    const position = viewer.scene.globe.pick(ray, viewer.scene);
                    
                    if (position) {
                        const cartographic = Cesium.Cartographic.fromCartesian(position);
                        const lon = Cesium.Math.toDegrees(cartographic.longitude).toFixed(4);
                        const lat = Cesium.Math.toDegrees(cartographic.latitude).toFixed(4);
                        const height = cartographic.height.toFixed(2);
                        
                        // Show position info
                        showPositionInfo(lon, lat, height);
                    }
                }
            }, Cesium.ScreenSpaceEventType.LEFT_CLICK);
            
            // Mouse move handler for tooltips
            viewer.cesiumWidget.screenSpaceEventHandler.setInputAction(function onMouseMove(movement) {
                const picked = viewer.scene.pick(movement.endPosition);
                const tooltip = document.getElementById('tooltip');
                
                if (Cesium.defined(picked) && Cesium.defined(picked.id)) {
                    tooltip.style.display = 'block';
                    tooltip.style.left = (movement.endPosition.x + 10) + 'px';
                    tooltip.style.top = (movement.endPosition.y + 10) + 'px';
                    tooltip.textContent = picked.id.name;
                } else {
                    tooltip.style.display = 'none';
                }
            }, Cesium.ScreenSpaceEventType.MOUSE_MOVE);
        }
        
        // Fly to a specific location
        function flyToLocation(longitude, latitude, height, locationName) {
            viewer.camera.flyTo({
                destination: Cesium.Cartesian3.fromDegrees(longitude, latitude, height),
                orientation: {
                    heading: Cesium.Math.toRadians(0.0),
                    pitch: Cesium.Math.toRadians(-30.0),
                    roll: 0.0
                },
                duration: 3,
                complete: function() {
                    console.log(`Arrived at ${locationName}`);
                }
            });
        }
        
        // Add a random marker to the map
        function addRandomMarker() {
            const pinBuilder = new Cesium.PinBuilder();
            
            // Generate random coordinates
            const lon = (Math.random() * 360) - 180;
            const lat = (Math.random() * 180) - 90;
            
            viewer.entities.add({
                name: "Random Location",
                position: Cesium.Cartesian3.fromDegrees(lon, lat),
                billboard: {
                    image: pinBuilder.fromColor(Cesium.Color.PURPLE, 48).toDataURL(),
                    verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                    scale: 1.2,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
                },
                description: "This is a randomly placed marker.",
                label: {
                    text: "Random Location",
                    font: '14pt sans-serif',
                    pixelOffset: new Cesium.Cartesian2(0, -40),
                    fillColor: Cesium.Color.WHITE,
                    outlineColor: Cesium.Color.BLACK,
                    outlineWidth: 2,
                    style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                    show: labelsEnabled
                }
            });
            
            // Fly to the new marker
            flyToLocation(lon, lat, 5000, "Random Location");
        }
        
        // Show location information in the info panel
        function showLocationInfo(entity) {
            const location = locations.find(loc => loc.name === entity.name);
            if (location) {
                document.getElementById('infoTitle').textContent = location.name;
                document.getElementById('infoCoordinates').textContent = 
                    `Coordinates: ${location.lon.toFixed(4)}, ${location.lat.toFixed(4)}`;
                document.getElementById('infoElevation').textContent = 'Elevation: Varies';
                document.getElementById('infoDescription').textContent = `Description: ${location.desc}`;
                document.getElementById('infoPanel').style.display = 'block';
            } else if (entity.name === "Random Location") {
                const position = entity.position.getValue(Cesium.JulianDate.now());
                const cartographic = Cesium.Cartographic.fromCartesian(position);
                const lon = Cesium.Math.toDegrees(cartographic.longitude).toFixed(4);
                const lat = Cesium.Math.toDegrees(cartographic.latitude).toFixed(4);
                
                document.getElementById('infoTitle').textContent = "Random Location";
                document.getElementById('infoCoordinates').textContent = `Coordinates: ${lon}, ${lat}`;
                document.getElementById('infoElevation').textContent = 'Elevation: Varies';
                document.getElementById('infoDescription').textContent = "This is a randomly placed marker.";
                document.getElementById('infoPanel').style.display = 'block';
            }
        }
        
        // Show position information in the info panel
        function showPositionInfo(lon, lat, height) {
            document.getElementById('infoTitle').textContent = 'Selected Location';
            document.getElementById('infoCoordinates').textContent = `Coordinates: ${lon}, ${lat}`;
            document.getElementById('infoElevation').textContent = `Elevation: ${height} meters`;
            document.getElementById('infoDescription').textContent = 'Description: Click on a marker for more information';
            document.getElementById('infoPanel').style.display = 'block';
        }
        
        // Update coordinates in the status bar
        function updateCoordinates() {
            const cartographic = viewer.camera.positionCartographic;
            const lon = Cesium.Math.toDegrees(cartographic.longitude).toFixed(4);
            const lat = Cesium.Math.toDegrees(cartographic.latitude).toFixed(4);
            const height = cartographic.height.toFixed(0);
            
            document.querySelector('.coordinates').textContent = 
                `Longitude: ${lon}° • Latitude: ${lat}° • Elevation: ${height}m`;
        }
        
        // Setup FPS counter
        function setupFPSCounter() {
            const fpsElement = document.querySelector('.fps');
            
            viewer.scene.postRender.addEventListener(function() {
                const fps = viewer.scene.frameState.lastFramesPerSecond;
                fpsElement.textContent = `FPS: ${Math.round(fps)}`;
            });
        }
    </script>
</body>
</html>