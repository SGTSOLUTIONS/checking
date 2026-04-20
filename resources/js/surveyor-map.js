// ===========================================================
//  MAP APPLICATION WITH LIVE TRACKING
// ===========================================================

class SurveyorMap {
    constructor(config) {
        this.polygons = config.polygons;
        this.lines = config.lines;
        this.points = config.points;
        this.pointDatas = config.pointDatas;
        this.polygonDatas = config.polygonDatas;
        this.ward = config.ward;
        this.mis = config.mis;
        this.routes = config.routes;
        this.droneImageURL = config.droneImageURL;
        this.imageExtent = config.imageExtent;

        this.currentLocationMarker = null;
        this.locationWatchId = null;
        this.isLiveLocationActive = false;
        this.currentRoute = null;
        this.routeSteps = [];
        this.currentStepIndex = 0;
        this.navigationMode = false;
        this.navigationInterval = null;
        this.isMobile = window.innerWidth <= 768;
        this.draw = null;
        this.modify = null;
        this.select = null;
        this.isModifyMode = false;
        this.originalClickHandler = null;
        this.lastLocation = null;
        this.locationHistory = [];

        this.initMap();
        this.initEventListeners();
    }

    // ===========================================================
    //  STYLE FUNCTIONS
    // ===========================================================
    createPointStyle(feature) {
        const gisid = feature.get("gisid");
        const pointData = this.pointDatas.find(data => data.point_gisid == gisid);
        const color = pointData ? "red" : "blue";

        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 8,
                fill: new ol.style.Fill({ color: "white" }),
                stroke: new ol.style.Stroke({ color: color, width: 2 })
            }),
            text: new ol.style.Text({
                text: gisid ? String(gisid) : "",
                scale: 1.3,
                offsetY: -15,
                fill: new ol.style.Fill({ color: "#000" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 3 })
            })
        });
    }

    createPolygonStyle(feature) {
        const gisid = feature.get("gisid");
        const polygonData = this.polygonDatas.find(data => data.gisid == gisid);
        const color = polygonData ? "red" : "blue";

        return new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: color,
                width: 4,
                lineJoin: "round",
                lineCap: "round"
            })
        });
    }

    createLineStyle(feature) {
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
                fill: new ol.style.Fill({ color: "#000" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 3 })
            })
        });
    }

    createHighlightStyle(feature) {
        const geometryType = feature.getGeometry().getType();
        if (geometryType === 'Point') {
            return new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 10,
                    fill: new ol.style.Fill({ color: "rgba(255,255,0,0.5)" }),
                    stroke: new ol.style.Stroke({ color: "red", width: 3 })
                })
            });
        } else if (geometryType === 'LineString') {
            return new ol.style.Style({
                stroke: new ol.style.Stroke({ color: "red", width: 5 })
            });
        } else {
            return new ol.style.Style({
                stroke: new ol.style.Stroke({ color: "red", width: 6 }),
                fill: new ol.style.Fill({ color: "rgba(255,0,0,0.1)" })
            });
        }
    }

    createLocationMarkerStyle() {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 10,
                fill: new ol.style.Fill({ color: 'rgba(0, 150, 255, 0.8)' }),
                stroke: new ol.style.Stroke({ color: '#fff', width: 3 })
            })
        });
    }

    createAccuracyCircleStyle(radius) {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: radius,
                fill: new ol.style.Fill({ color: 'rgba(0, 150, 255, 0.2)' }),
                stroke: new ol.style.Stroke({ color: 'rgba(0, 150, 255, 0.5)', width: 2 })
            })
        });
    }

    // ===========================================================
    //  MAP INITIALIZATION
    // ===========================================================
    initMap() {
        // Layer definitions
        this.osmLayer = new ol.layer.Tile({
            source: new ol.source.OSM(),
            visible: true
        });

        this.terrainLayer = new ol.layer.Tile({
            source: new ol.source.OSM({
                url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
            }),
            visible: false
        });

        this.satelliteLayer = new ol.layer.Tile({
            source: new ol.source.OSM({
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
            }),
            visible: false
        });

        this.droneLayer = new ol.layer.Image({
            source: new ol.source.ImageStatic({
                url: this.droneImageURL,
                imageExtent: this.imageExtent,
                imageSmoothing: false
            }),
            opacity: 0.90,
            visible: true
        });

        // Vector sources
        this.polygonSource = new ol.source.Vector();
        this.polygons.forEach(poly => {
            let coords = JSON.parse(poly.coordinates);
            this.polygonSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Polygon(coords),
                gisid: poly.gisid,
                type: "Polygon"
            }));
        });

        this.polygonLayer = new ol.layer.Vector({
            source: this.polygonSource,
            style: this.createPolygonStyle.bind(this),
            visible: true
        });

        this.lineSource = new ol.source.Vector();
        this.lines.forEach(l => {
            let coords = JSON.parse(l.coordinates);
            this.lineSource.addFeature(new ol.Feature({
                geometry: new ol.geom.LineString(coords),
                gisid: l.gisid,
                type: "Line",
                road_name: l.road_name
            }));
        });

        this.lineLayer = new ol.layer.Vector({
            source: this.lineSource,
            style: this.createLineStyle.bind(this),
            visible: true
        });

        this.pointSource = new ol.source.Vector();
        this.points.forEach(p => {
            let coords = JSON.parse(p.coordinates);
            this.pointSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Point(coords),
                gisid: p.gisid,
                type: "Point"
            }));
        });

        this.pointLayer = new ol.layer.Vector({
            source: this.pointSource,
            style: this.createPointStyle.bind(this),
            visible: true
        });

        // Ward boundary
        const boundary = this.ward.boundary[0];
        const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
        const boundarys = new ol.geom.Polygon([transformedBoundary]);

        this.boundaryLayer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [new ol.Feature({ geometry: boundarys })]
            }),
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({ color: "red", width: 3 })
            })
        });

        this.highlightSource = new ol.source.Vector();
        this.highlightLayer = new ol.layer.Vector({
            source: this.highlightSource,
            style: this.createHighlightStyle.bind(this)
        });

        this.routeSource = new ol.source.Vector();
        this.routeLayer = new ol.layer.Vector({
            source: this.routeSource,
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({ color: '#ff0000', width: 4, lineDash: [5, 5] })
            })
        });

        this.locationSource = new ol.source.Vector();
        this.locationLayer = new ol.layer.Vector({
            source: this.locationSource,
            style: this.createLocationMarkerStyle.bind(this)
        });

        this.accuracySource = new ol.source.Vector();
        this.accuracyLayer = new ol.layer.Vector({
            source: this.accuracySource
        });

        // Create map
        this.map = new ol.Map({
            target: 'map',
            layers: [
                this.osmLayer,
                this.terrainLayer,
                this.satelliteLayer,
                this.droneLayer,
                this.boundaryLayer,
                this.polygonLayer,
                this.lineLayer,
                this.pointLayer,
                this.highlightLayer,
                this.routeLayer,
                this.locationLayer,
                this.accuracyLayer
            ],
            view: new ol.View({
                projection: "EPSG:3857",
                center: ol.extent.getCenter(this.imageExtent),
                zoom: 17
            })
        });

        this.setupClickHandlers();
    }

    // ===========================================================
    //  LIVE TRACKING WITH MOVEMENT
    // ===========================================================
    toggleLiveLocation() {
        if (this.isLiveLocationActive) {
            this.stopLiveTracking();
        } else {
            this.startLiveTracking();
        }
    }

    startLiveTracking() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser');
            return;
        }

        this.isLiveLocationActive = true;

        // Update UI
        if (this.isMobile) {
            document.getElementById('mobileLocationBtn').classList.add('active');
        } else {
            const btn = document.getElementById('liveLocationBtn');
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>Stop Location';
        }

        // Start watching position with high accuracy
        this.locationWatchId = navigator.geolocation.watchPosition(
            this.handleLocationUpdate.bind(this),
            this.handleLocationError.bind(this),
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0,
                distanceFilter: 1 // Update on every 1 meter movement
            }
        );

        // Center map on initial location
        navigator.geolocation.getCurrentPosition((position) => {
            const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]);
            this.map.getView().animate({
                center: coords,
                zoom: 18,
                duration: 1000
            });
        });
    }

    stopLiveTracking() {
        if (this.locationWatchId) {
            navigator.geolocation.clearWatch(this.locationWatchId);
            this.locationWatchId = null;
        }

        this.locationSource.clear();
        this.accuracySource.clear();
        this.currentLocationMarker = null;
        this.isLiveLocationActive = false;
        this.locationHistory = [];

        // Update UI
        if (this.isMobile) {
            document.getElementById('mobileLocationBtn').classList.remove('active');
        } else {
            const btn = document.getElementById('liveLocationBtn');
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>Live Location';
        }
    }

    handleLocationUpdate(position) {
        const { longitude, latitude } = position.coords;
        const { accuracy, speed, heading } = position.coords;

        const coords = ol.proj.fromLonLat([longitude, latitude]);

        // Store location history for path tracking
        this.locationHistory.push({ coords, timestamp: Date.now() });

        // Keep only last 100 points
        if (this.locationHistory.length > 100) {
            this.locationHistory.shift();
        }

        // Update location marker with animation
        if (this.currentLocationMarker) {
            const previousCoords = this.currentLocationMarker.getGeometry().getCoordinates();
            this.animateMarkerMovement(previousCoords, coords);
        } else {
            this.currentLocationMarker = new ol.Feature({
                geometry: new ol.geom.Point(coords)
            });
            this.locationSource.addFeature(this.currentLocationMarker);
        }

        // Add CSS class for animation
        const markerElement = document.querySelector('.ol-viewport canvas');
        if (markerElement) {
            markerElement.classList.add('live-tracking-marker');
        }

        // Update accuracy circle
        this.accuracySource.clear();
        const accuracyCircle = new ol.Feature({
            geometry: new ol.geom.Point(coords)
        });

        const accuracyRadius = accuracy / 0.705; // Approximate conversion to pixels
        accuracyCircle.setStyle(this.createAccuracyCircleStyle(accuracyRadius));
        this.accuracySource.addFeature(accuracyCircle);

        // Update speed and heading if available
        if (speed !== null) {
            this.updateSpeedAndHeading(speed, heading);
        }

        // Update navigation if active
        if (this.navigationMode && this.currentRoute) {
            this.updateNavigationWithLocation(coords);
        }

        // Draw path if enabled
        this.drawLocationPath();

        // Center map on user if navigation mode is active
        if (this.navigationMode) {
            this.map.getView().animate({
                center: coords,
                duration: 500
            });
        }
    }

    animateMarkerMovement(fromCoords, toCoords) {
        // Simple animation - update position directly
        // For smoother animation, we could use requestAnimationFrame
        this.currentLocationMarker.setGeometry(new ol.geom.Point(toCoords));
    }

    drawLocationPath() {
        // Optional: Draw path of user movement
        const pathCoordinates = this.locationHistory.map(point => point.coords);

        if (pathCoordinates.length > 1) {
            const pathLayer = this.map.getLayers().getArray().find(layer => layer.get('name') === 'pathLayer');

            if (pathLayer) {
                const pathSource = pathLayer.getSource();
                pathSource.clear();
                const pathFeature = new ol.Feature({
                    geometry: new ol.geom.LineString(pathCoordinates)
                });
                pathSource.addFeature(pathFeature);
            }
        }
    }

    updateSpeedAndHeading(speed, heading) {
        const speedKmh = speed * 3.6; // Convert m/s to km/h
        const speedMph = speed * 2.23694;

        // Update UI with speed info
        const speedInfo = document.getElementById('speedInfo');
        if (speedInfo) {
            speedInfo.innerHTML = `
                <div class="speed-display">
                    <i class="fas fa-tachometer-alt"></i>
                    ${speedKmh.toFixed(1)} km/h
                </div>
            `;
            speedInfo.style.display = 'block';
        }

        // Update marker rotation based on heading
        if (this.currentLocationMarker && heading !== null) {
            // Rotate marker icon (if using custom marker)
            const markerStyle = this.createLocationMarkerStyle();
            // Add rotation to style
            markerStyle.getImage().setRotation(heading * Math.PI / 180);
            this.currentLocationMarker.setStyle(markerStyle);
        }
    }

    updateNavigationWithLocation(currentCoords) {
        // Check if user is near the next navigation point
        if (this.routeSteps.length > 0 && this.currentStepIndex < this.routeSteps.length - 1) {
            const nextStep = this.routeSteps[this.currentStepIndex + 1];

            // Convert coordinates if needed
            const currentLonLat = ol.proj.toLonLat(currentCoords);

            // Check distance to next waypoint
            if (nextStep.geometry && nextStep.geometry.coordinates) {
                const nextCoord = nextStep.geometry.coordinates[0];
                const distance = this.calculateDistance(currentLonLat, nextCoord);

                if (distance < 50) { // Within 50 meters
                    this.currentStepIndex++;
                    this.updateNavigationInstruction();
                }
            }
        }

        // Update ETA based on current location
        if (this.currentRoute) {
            const remainingDistance = this.calculateRemainingDistance(currentCoords);
            const estimatedTime = remainingDistance / (5 * 1000 / 3600); // Assuming 5 km/h walking speed

            document.getElementById('etaTime').textContent = this.formatDuration(estimatedTime);
            document.getElementById('etaDistance').textContent = this.formatDistance(remainingDistance);
        }
    }

    calculateRemainingDistance(currentCoords) {
        if (!this.currentRoute || !this.currentRoute.geometry) return 0;

        const routeCoordinates = this.currentRoute.geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
        let minDistance = Infinity;

        // Find closest point on route
        routeCoordinates.forEach(coord => {
            const distance = this.calculateDistanceBetweenPoints(currentCoords, coord);
            if (distance < minDistance) {
                minDistance = distance;
            }
        });

        return minDistance;
    }

    calculateDistanceBetweenPoints(point1, point2) {
        const dx = point1[0] - point2[0];
        const dy = point1[1] - point2[1];
        return Math.sqrt(dx * dx + dy * dy);
    }

    handleLocationError(error) {
        console.error('Geolocation error:', error);

        let errorMessage = '';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Location permission denied. Please enable location services.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information unavailable.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Location request timed out.';
                break;
            default:
                errorMessage = 'An unknown error occurred.';
        }

        alert(errorMessage);
        this.stopLiveTracking();
    }

    // ===========================================================
    //  ROUTE FUNCTIONS
    // ===========================================================
    async calculateEnhancedRoute(startCoord, endCoord, placeName) {
        try {
            // Show loading state
            if (this.isMobile) {
                document.getElementById('mobileRouteSummary').innerHTML = '<div>Calculating route...</div>';
            } else {
                document.getElementById('desktopRouteSummary').innerHTML = '<div>Calculating route...</div>';
                document.getElementById('routeInfo').style.display = 'block';
            }

            const route = await this.getRouteFromOSRM(startCoord, endCoord);

            const totalDistance = route.distance;
            const totalDuration = route.duration;

            this.routeSteps = [];
            let accumulatedDistance = 0;

            route.legs[0].steps.forEach((step, index) => {
                const maneuver = step.maneuver;
                const distance = step.distance;
                accumulatedDistance += distance;

                this.routeSteps.push({
                    instruction: maneuver.instruction || this.getStepInstruction(maneuver),
                    distance: this.formatDistance(accumulatedDistance),
                    icon: this.getDirectionIcon(maneuver.type, maneuver.modifier),
                    type: maneuver.type,
                    geometry: step.geometry
                });
            });

            this.drawRouteOnMap(route.geometry);
            this.displayRouteInfo(totalDistance, totalDuration, placeName);

            return {
                distance: totalDistance,
                duration: totalDuration,
                geometry: route.geometry,
                endCoord: endCoord,
                placeName: placeName
            };

        } catch (error) {
            console.error('Error calculating route:', error);

            const distance = this.calculateDistance(startCoord, endCoord);
            const duration = distance / 1.39;

            this.routeSteps = [{
                instruction: "Start from your current location",
                distance: "0.0 km",
                icon: "fas fa-play",
                type: "depart"
            }, {
                instruction: "Continue straight to destination",
                distance: this.formatDistance(distance),
                icon: "fas fa-arrow-up",
                type: "continue"
            }, {
                instruction: "Arrive at your destination",
                distance: this.formatDistance(distance),
                icon: "fas fa-flag-checkered",
                type: "arrive"
            }];

            this.drawRouteOnMap({
                type: "LineString",
                coordinates: [startCoord, endCoord]
            });

            this.displayRouteInfo(distance, duration, placeName);

            return {
                distance: distance,
                duration: duration,
                geometry: {
                    type: "LineString",
                    coordinates: [startCoord, endCoord]
                },
                endCoord: endCoord,
                placeName: placeName
            };
        }
    }

    async getRouteFromOSRM(startCoord, endCoord) {
        try {
            const [startLon, startLat] = startCoord;
            const [endLon, endLat] = endCoord;

            const url = `https://router.project-osrm.org/route/v1/driving/${startLon},${startLat};${endLon},${endLat}?overview=full&geometries=geojson&steps=true`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                throw new Error('No route found');
            }

            return data.routes[0];
        } catch (error) {
            console.error('Error getting route from OSRM:', error);
            return this.getStraightLineRoute(startCoord, endCoord);
        }
    }

    getStraightLineRoute(startCoord, endCoord) {
        const distance = this.calculateDistance(startCoord, endCoord);
        const duration = distance / 1.39;

        return {
            distance: distance,
            duration: duration,
            geometry: {
                type: "LineString",
                coordinates: [startCoord, endCoord]
            },
            legs: [{
                steps: [{
                    maneuver: { type: "depart", instruction: "Start from your location" },
                    distance: distance,
                    duration: duration
                }, {
                    maneuver: { type: "arrive", instruction: "Arrive at destination" },
                    distance: 0,
                    duration: 0
                }]
            }]
        };
    }

    calculateDistance(coord1, coord2) {
        return ol.sphere.getDistance(
            ol.proj.fromLonLat(coord1),
            ol.proj.fromLonLat(coord2)
        );
    }

    formatDistance(meters) {
        if (meters < 1000) {
            return meters.toFixed(0) + ' meters';
        } else {
            return (meters / 1000).toFixed(2) + ' km';
        }
    }

    formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) {
            return minutes + ' min';
        } else {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            return hours + 'h ' + remainingMinutes + 'm';
        }
    }

    getDirectionIcon(maneuverType, modifier = '') {
        const icons = {
            'depart': 'fas fa-play',
            'arrive': 'fas fa-flag-checkered',
            'turn': 'fas fa-undo',
            'new name': 'fas fa-sign',
            'continue': 'fas fa-arrow-up',
            'roundabout': 'fas fa-sync',
            'rotary': 'fas fa-sync-alt',
            'exit roundabout': 'fas fa-sign-out-alt',
            'exit rotary': 'fas fa-sign-out-alt',
            'fork': 'fas fa-code-branch',
            'merge': 'fas fa-arrow-right',
            'on ramp': 'fas fa-arrow-up',
            'off ramp': 'fas fa-arrow-down',
            'end of road': 'fas fa-road'
        };

        if (modifier) {
            const turnIcons = {
                'left': 'fas fa-arrow-left',
                'right': 'fas fa-arrow-right',
                'sharp left': 'fas fa-arrow-left',
                'sharp right': 'fas fa-arrow-right',
                'slight left': 'fas fa-arrow-left',
                'slight right': 'fas fa-arrow-right',
                'straight': 'fas fa-arrow-up',
                'uturn': 'fas fa-undo'
            };
            return turnIcons[modifier] || icons[maneuverType] || 'fas fa-arrow-up';
        }

        return icons[maneuverType] || 'fas fa-arrow-up';
    }

    getStepInstruction(maneuver) {
        const baseInstruction = maneuver.type.replace(/_/g, ' ');
        if (maneuver.modifier) {
            return `${maneuver.modifier} ${baseInstruction}`;
        }
        return baseInstruction;
    }

    drawRouteOnMap(geometry) {
        const coordinates = geometry.coordinates.map(coord => ol.proj.fromLonLat(coord));
        const routeFeature = new ol.Feature({
            geometry: new ol.geom.LineString(coordinates)
        });

        this.routeSource.clear();
        this.routeSource.addFeature(routeFeature);

        const extent = routeFeature.getGeometry().getExtent();
        this.map.getView().fit(extent, {
            padding: [50, 50, 50, 50],
            duration: 1000
        });
    }

    displayRouteInfo(distance, duration, placeName) {
        const summaryHtml = `
            <div><strong>Total Distance:</strong> ${this.formatDistance(distance)}</div>
            <div><strong>Estimated Time:</strong> ${this.formatDuration(duration)}</div>
            <div><strong>Destination:</strong> ${placeName}</div>
        `;

        if (this.isMobile) {
            document.getElementById('mobileRouteSummary').innerHTML = summaryHtml;
            this.displayTurnByTurnDirections(true);
        } else {
            document.getElementById('desktopRouteSummary').innerHTML = summaryHtml;
            this.displayTurnByTurnDirections(false);
            document.getElementById('navigationControls').style.display = 'block';
        }
    }

    displayTurnByTurnDirections(isMobile = false) {
        const directionsList = isMobile ?
            document.getElementById('mobileDirectionsList') :
            document.getElementById('desktopDirectionsList');

        directionsList.innerHTML = '';

        this.routeSteps.forEach((step, index) => {
            const stepElement = document.createElement('div');
            stepElement.className = 'direction-step';
            stepElement.innerHTML = `
                <div class="step-number">${index + 1}</div>
                <div class="step-content">
                    <div class="step-instruction"><i class="${step.icon} me-2"></i>${step.instruction}</div>
                    <div class="step-distance">${step.distance}</div>
                </div>
            `;
            directionsList.appendChild(stepElement);
        });
    }

    startNavigation() {
        if (!this.currentRoute) {
            alert('Please select a feature and calculate a route first');
            return;
        }

        if (!this.currentLocationMarker) {
            alert('Please enable live location first to start navigation');
            return;
        }

        this.navigationMode = true;
        this.currentStepIndex = 0;

        // Show navigation UI
        if (this.isMobile) {
            document.getElementById('navigationHeader').style.display = 'block';
            document.getElementById('navigationInstruction').style.display = 'block';
            document.getElementById('routeBottomSheet').classList.remove('open');
        }

        document.getElementById('etaTime').textContent = this.formatDuration(this.currentRoute.duration);
        document.getElementById('etaDistance').textContent = this.formatDistance(this.currentRoute.distance);
        document.getElementById('destinationAddress').textContent = this.currentRoute.placeName;

        this.updateNavigationInstruction();

        // Clear any existing interval and start new one
        if (this.navigationInterval) {
            clearInterval(this.navigationInterval);
        }

        this.navigationInterval = setInterval(() => this.updateNavigationStatus(), 5000);

        if (this.isMobile) {
            // Center on user location for mobile navigation
            this.map.getView().animate({
                center: this.currentLocationMarker.getGeometry().getCoordinates(),
                zoom: 18,
                duration: 1000
            });
        }

        alert('Navigation started! Follow the route instructions.' + (this.isMobile ? ' Navigation UI is now active.' : ''));
    }

    updateNavigationInstruction() {
        if (this.currentStepIndex < this.routeSteps.length) {
            const currentStep = this.routeSteps[this.currentStepIndex];
            document.getElementById('instructionText').textContent = currentStep.instruction;
            document.getElementById('instructionDistance').textContent = currentStep.distance;
            document.getElementById('instructionIcon').className = currentStep.icon;
        }
    }

    updateNavigationStatus() {
        if (!this.navigationMode || !this.currentRoute) return;

        // Calculate remaining distance based on current location
        if (this.currentLocationMarker) {
            const currentCoords = this.currentLocationMarker.getGeometry().getCoordinates();
            const remainingDistance = this.calculateRemainingDistance(currentCoords);
            const remainingDuration = remainingDistance / (5 * 1000 / 3600); // 5 km/h walking speed

            document.getElementById('etaTime').textContent = this.formatDuration(remainingDuration);
            document.getElementById('etaDistance').textContent = this.formatDistance(remainingDistance);
        }
    }

    // ===========================================================
    //  EVENT HANDLERS AND UTILITIES
    // ===========================================================
    setupClickHandlers() {
        this.map.on('click', (evt) => {
            // Skip if we're in modify mode
            if (this.isModifyMode) return;

            const feature = this.map.forEachFeatureAtPixel(evt.pixel, feature => feature);

            if (feature) {
                const properties = feature.getProperties();
                const geometryType = feature.getGeometry().getType();

                if (geometryType === "Point") {
                    this.handlePointClick(properties);
                } else if (geometryType === "Polygon") {
                    this.handlePolygonClick(properties);
                } else if (geometryType === "LineString" || geometryType === "MultiLineString") {
                    this.handleLineClick(properties);
                }
            }
        });
    }

    handlePointClick(properties) {
        const gisid = properties["gisid"];
        this.resetPointFormFields();

        const polygonData = this.polygonDatas.find(data => data.gisid === gisid);
        const polygonNumOfBill = polygonData ? polygonData.number_bill : null;

        const matchingPointsCount = this.pointDatas.filter(data => data.point_gisid === gisid).length;

        if (polygonNumOfBill > matchingPointsCount) {
            $("#pointgis").val(gisid);
            $("#pointModal").modal("show");
        } else {
            this.showFlashMessage(`Already this building have ${matchingPointsCount} bills`, "error");
        }
    }

    handlePolygonClick(properties) {
        const gisId = properties["gisid"];
        console.log("Polygon clicked - GIS ID:", gisId);

        let valueFound = false;

        if (this.polygonDatas && this.polygonDatas.length > 0) {
            this.polygonDatas.forEach(item => {
                if (item.gisid == gisId) {
                    this.populateBuildingForm(item);
                    valueFound = true;
                    return false;
                }
            });
        }

        if (!valueFound) {
            this.resetBuildingForm();
        }

        $("#gisIdInput").val(gisId);
        $("#buildingModal").modal("show");
    }

    handleLineClick(properties) {
        const gisid = properties["gisid"];
        if (gisid) {
            $("#linegisid").val(gisid);
            const roadName = properties["road_name"] || "No road name";
            document.getElementById("featureline").innerHTML = roadName;
            $("#lineModal").modal("show");
        }
    }

    // Form reset functions
    resetPointFormFields() {
        const fieldsToReset = [
            "pointgis", "assessment", "old_assessment", "owner_name", "present_owner_name",
            "floor", "old_door_no", "eb", "new_door_no", "bill_usage", "water_tax",
            "old_water_tax", "phone", "remarks"
        ];

        fieldsToReset.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.value = "";
        });
    }

    resetBuildingForm() {
        const fields = {
            "number_bill": "", "number_shop": "", "number_floor": "", "building_name": "",
            "building_usage": "", "construction_type": "", "ugd": "", "rainwater_harvesting": "",
            "parking": "", "ramp": "", "hoarding": "", "liftroom": "", "overhead_tank": "",
            "headroom": "", "cell_tower": "", "percentage": "", "new_address": "", "cctv": "",
            "water_connection": "", "phone": "", "remarks": "", "solar_panel": ""
        };

        Object.keys(fields).forEach(field => {
            const element = document.getElementById(field);
            if (element) element.value = fields[field];
        });

        const buildingImg = document.getElementById("building_img");
        if (buildingImg) buildingImg.src = "";
    }

    populateBuildingForm(item) {
        const fieldMappings = {
            "number_bill": item.number_bill, "number_shop": item.number_shop,
            "number_floor": item.number_floor, "building_name": item.building_name,
            "building_usage": item.building_usage, "construction_type": item.construction_type,
            "road_name": item.road_name, "ugd": item.ugd, "rainwater_harvesting": item.rainwater_harvesting,
            "parking": item.parking, "ramp": item.ramp, "hoarding": item.hoarding,
            "building_type": item.building_type, "basement": item.basement, "liftroom": item.liftroom,
            "overhead_tank": item.overhead_tank, "headroom": item.headroom, "cell_tower": item.cell_tower,
            "percentage": item.percentage, "new_address": item.new_address, "cctv": item.cctv,
            "water_connection": item.water_connection, "phone": item.phone, "remarks": item.remarks,
            "solar_panel": item.solar_panel
        };

        Object.keys(fieldMappings).forEach(field => {
            const element = document.getElementById(field);
            if (element) element.value = fieldMappings[field] || "";
        });

        if (item.gisid) {
            const numericPart = item.gisid.match(/\d+$/)?.[0];
            if (numericPart) {
                const buildingImg = document.getElementById("building_img");
                if (buildingImg) {
                    const basePath = `{{ $ward->corporation_id == 100 ? 'public/corporation/ss' : 'public/corporation/coimbatore' }}`;
                    buildingImg.src = `${basePath}/{{ $ward->zone }}/{{ $ward->ward_no }}/images/${numericPart}.jpg`;
                }
            }
        }
    }

    showFlashMessage(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const flashHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('body').append(flashHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    refreshVectorLayer() {
        // Clear and rebuild vector sources with updated data
        this.pointSource.clear();
        this.points.forEach(p => {
            let coords = JSON.parse(p.coordinates);
            this.pointSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Point(coords),
                gisid: p.gisid,
                type: "Point"
            }));
        });

        this.polygonSource.clear();
        this.polygons.forEach(poly => {
            let coords = JSON.parse(poly.coordinates);
            this.polygonSource.addFeature(new ol.Feature({
                geometry: new ol.geom.Polygon(coords),
                gisid: poly.gisid,
                type: "Polygon"
            }));
        });

        this.lineSource.clear();
        this.lines.forEach(l => {
            let coords = JSON.parse(l.coordinates);
            this.lineSource.addFeature(new ol.Feature({
                geometry: new ol.geom.LineString(coords),
                gisid: l.gisid,
                type: "Line",
                road_name: l.road_name
            }));
        });

        this.map.render();
    }

    initEventListeners() {
        // Window resize handler
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth <= 768;
        });

        // Form submissions
        $("#pointForm").submit((e) => {
            e.preventDefault();
            this.submitPointForm(e);
        });

        $("#buildingForm").submit((e) => {
            e.preventDefault();
            this.submitBuildingForm(e);
        });

        $("#lineForm").submit((e) => {
            e.preventDefault();
            this.submitLineForm(e);
        });

        $("#deleteForm").submit((e) => {
            e.preventDefault();
            this.submitDeleteForm(e);
        });

        // Layer controls
        this.initLayerControls();
        this.initSearchControls();
        this.initMobileControls();
    }

    submitPointForm(e) {
        $(".error-message").text("");
        $("input").removeClass("is-invalid");

        const formData = $(e.target).serialize();
        $("#pointSubmit").prop("disabled", true);

        $.ajax({
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            type: "POST",
            url: this.routes.surveyorPointDataUpload,
            data: formData,
            success: (response) => {
                this.showFlashMessage(response.message, "success");
                $("#pointModal").modal("hide");
                if (response.pointDatas) this.pointDatas = response.pointDatas;
                if (response.points) this.points = response.points;
                this.refreshVectorLayer();
            },
            error: (xhr) => this.handleFormError(xhr, "pointSubmit"),
            complete: () => $("#pointSubmit").prop("disabled", false)
        });
    }

    submitBuildingForm(e) {
        $(".error-message").text("");
        $("input").removeClass("is-invalid");

        const formData = new FormData(e.target);
        $("#buildingsubmitBtn").prop("disabled", true);

        $.ajax({
            type: "POST",
            url: this.routes.surveyorPolygonDatasUpload,
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.showFlashMessage(response.message, "success");
                $("#buildingModal").modal("hide");
                if (response.polygonDatas) this.polygonDatas = response.polygonDatas;
                if (response.polygon) this.polygons = response.polygon;
                if (response.point) this.points = response.point;
                this.refreshVectorLayer();
            },
            error: (xhr) => this.handleFormError(xhr, "buildingsubmitBtn"),
            complete: () => $("#buildingsubmitBtn").prop("disabled", false)
        });
    }

    submitLineForm(e) {
        const formData = new FormData(e.target);
        $("#lineSubmit").prop("disabled", true);

        $.ajax({
            url: this.routes.updateRoadName,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            success: (response) => {
                this.showFlashMessage(response.message, "success");
                $("#lineModal").modal("hide");
                if (response.lines) this.lines = response.lines;
                this.refreshVectorLayer();
            },
            error: (xhr) => this.handleFormError(xhr, "lineSubmit"),
            complete: () => $("#lineSubmit").prop("disabled", false)
        });
    }

    submitDeleteForm(e) {
        const gisid = $("#deleteGisIdInput").val().trim();
        const featureType = $("#deleteFeatureType").val();

        if (!gisid) {
            this.showFlashMessage("Please enter a GIS ID", "error");
            return;
        }

        $("#confirmDeleteBtn").prop("disabled", true);
        $("#confirmDeleteBtn").html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

        const formData = {
            gisid: gisid,
            feature_type: featureType,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: this.routes.delgisid,
            type: "POST",
            data: formData,
            success: (response) => {
                if (response.success) {
                    this.showFlashMessage(response.message, "success");
                    $("#deleteModal").modal("hide");
                    if (response.polygons) this.polygons = response.polygons;
                    if (response.lines) this.lines = response.lines;
                    if (response.points) this.points = response.points;
                    if (response.polygonDatas) this.polygonDatas = response.polygonDatas;
                    if (response.pointDatas) this.pointDatas = response.pointDatas;
                    this.refreshVectorLayer();
                    this.highlightSource.clear();
                    $("#deleteForm")[0].reset();
                    $("#featurePreview").hide();
                } else {
                    this.showFlashMessage(response.message, "error");
                }
            },
            error: (xhr) => {
                let errorMessage = "An error occurred while deleting the feature";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                this.showFlashMessage(errorMessage, "error");
            },
            complete: () => {
                $("#confirmDeleteBtn").prop("disabled", false);
                $("#confirmDeleteBtn").html('<i class="fas fa-trash-alt me-2"></i>Delete Feature');
            }
        });
    }

    handleFormError(xhr, buttonId) {
        let errorMsg = "An error occurred while processing your request.";
        if (xhr.responseJSON && xhr.responseJSON.msg) {
            errorMsg = xhr.responseJSON.msg;
        }
        this.showFlashMessage(errorMsg, "error");
        $("#" + buttonId).prop("disabled", false);

        if (xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, (key, value) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(value[0]);
            });
        }
    }

    initLayerControls() {
        // Desktop base layer controls
        document.querySelectorAll('input[name="baseLayer"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.osmLayer.setVisible(e.target.value === 'osm');
                this.terrainLayer.setVisible(e.target.value === 'terrain');
                this.satelliteLayer.setVisible(e.target.value === 'satellite');
            });
        });

        // Desktop overlay controls
        document.getElementById('droneLayer')?.addEventListener('change', (e) => {
            this.droneLayer.setVisible(e.target.checked);
        });
        document.getElementById('boundaryLayer')?.addEventListener('change', (e) => {
            this.boundaryLayer.setVisible(e.target.checked);
        });
        document.getElementById('polygonLayer')?.addEventListener('change', (e) => {
            this.polygonLayer.setVisible(e.target.checked);
        });
        document.getElementById('lineLayer')?.addEventListener('change', (e) => {
            this.lineLayer.setVisible(e.target.checked);
        });
        document.getElementById('pointLayer')?.addEventListener('change', (e) => {
            this.pointLayer.setVisible(e.target.checked);
        });

        // Mobile base layer controls
        document.querySelectorAll('input[name="mobileBaseLayer"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.osmLayer.setVisible(e.target.value === 'osm');
                this.terrainLayer.setVisible(e.target.value === 'terrain');
                this.satelliteLayer.setVisible(e.target.value === 'satellite');
            });
        });

        // Mobile overlay controls
        document.getElementById('mobileDroneLayer')?.addEventListener('change', (e) => {
            this.droneLayer.setVisible(e.target.checked);
        });
        document.getElementById('mobileBoundaryLayer')?.addEventListener('change', (e) => {
            this.boundaryLayer.setVisible(e.target.checked);
        });
        document.getElementById('mobilePolygonLayer')?.addEventListener('change', (e) => {
            this.polygonLayer.setVisible(e.target.checked);
        });
        document.getElementById('mobileLineLayer')?.addEventListener('change', (e) => {
            this.lineLayer.setVisible(e.target.checked);
        });
        document.getElementById('mobilePointLayer')?.addEventListener('change', (e) => {
            this.pointLayer.setVisible(e.target.checked);
        });
    }

    initSearchControls() {
        // Desktop search
        document.getElementById('searchBtn')?.addEventListener('click', () => {
            const gisid = document.getElementById('searchInput').value.trim();
            if (gisid) this.searchGISID(gisid, false);
        });

        document.getElementById('searchInput')?.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') {
                const gisid = event.target.value.trim();
                if (gisid) this.searchGISID(gisid, false);
            }
        });

        // Desktop live location
        document.getElementById('liveLocationBtn')?.addEventListener('click', () => {
            this.toggleLiveLocation();
        });

        // Desktop navigation
        document.getElementById('startNavigation')?.addEventListener('click', () => {
            this.startNavigation();
        });

        document.getElementById('clearNavigation')?.addEventListener('click', () => {
            this.clearAll();
        });

        document.getElementById('closeFeatureInfo')?.addEventListener('click', () => {
            document.getElementById('featureInfo').style.display = 'none';
        });

        document.getElementById('closeDirections')?.addEventListener('click', () => {
            document.getElementById('routeInfo').style.display = 'none';
        });
    }

    initMobileControls() {
        // Mobile toolbar buttons
        document.getElementById('mobileSearchBtn')?.addEventListener('click', () => {
            document.getElementById('mobileSearchOverlay').style.display = 'flex';
        });

        document.getElementById('mobileLocationBtn')?.addEventListener('click', () => {
            this.toggleLiveLocation();
        });

        document.getElementById('mobileLayersBtn')?.addEventListener('click', () => {
            document.getElementById('mobileLayerSwitcher').style.display = 'flex';
        });

        document.getElementById('mobileRouteBtn')?.addEventListener('click', () => {
            if (this.currentRoute) {
                document.getElementById('routeBottomSheet').classList.add('open');
            } else {
                alert('Please search for a GIS ID and calculate a route first');
            }
        });

        document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
            this.showMobileMenu();
        });

        // Mobile search
        document.getElementById('mobileSearchSubmit')?.addEventListener('click', () => {
            const gisid = document.getElementById('mobileSearchInput').value.trim();
            if (gisid) this.searchGISID(gisid, true);
        });

        document.getElementById('mobileSearchInput')?.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') {
                const gisid = event.target.value.trim();
                if (gisid) this.searchGISID(gisid, true);
            }
        });

        document.getElementById('closeMobileSearch')?.addEventListener('click', () => {
            document.getElementById('mobileSearchOverlay').style.display = 'none';
        });

        document.getElementById('closeMobileLayers')?.addEventListener('click', () => {
            document.getElementById('mobileLayerSwitcher').style.display = 'none';
        });

        document.getElementById('startNavigationFromSheet')?.addEventListener('click', () => {
            this.startNavigation();
            document.getElementById('routeBottomSheet').classList.remove('open');
        });

        document.getElementById('closeRouteSheet')?.addEventListener('click', () => {
            document.getElementById('routeBottomSheet').classList.remove('open');
        });

        // Swipe to close bottom sheet
        const sheet = document.getElementById('routeBottomSheet');
        let startY = 0;

        sheet?.addEventListener('touchstart', (e) => {
            startY = e.touches[0].clientY;
        });

        sheet?.addEventListener('touchmove', (e) => {
            const currentY = e.touches[0].clientY;
            const diff = currentY - startY;
            if (diff > 50) sheet.classList.remove('open');
        });
    }

    searchGISID(gisid, isMobile = false) {
        const searchResults = isMobile ?
            document.getElementById('mobileSearchResults') :
            document.getElementById('searchResults');

        searchResults.innerHTML = '';
        this.highlightSource.clear();
        this.routeSource.clear();

        const allSources = [this.pointSource, this.lineSource, this.polygonSource];
        let foundFeatures = [];

        allSources.forEach(source => {
            source.forEachFeature(feature => {
                if (feature.get('gisid') && feature.get('gisid').toString() === gisid) {
                    foundFeatures.push(feature);
                }
            });
        });

        if (foundFeatures.length > 0) {
            searchResults.style.display = 'block';

            foundFeatures.forEach(feature => {
                const resultItem = document.createElement('div');
                resultItem.className = 'search-result-item';
                resultItem.innerHTML = `
                    <strong>GIS ID:</strong> ${feature.get('gisid')} <br>
                    <strong>Type:</strong> ${feature.get('type')}
                `;

                resultItem.addEventListener('click', () => {
                    this.selectFeature(feature, isMobile);
                    if (isMobile) {
                        document.getElementById('mobileSearchOverlay').style.display = 'none';
                    }
                    searchResults.style.display = 'none';
                });

                searchResults.appendChild(resultItem);
            });
        } else {
            searchResults.style.display = 'block';
            const noResult = document.createElement('div');
            noResult.className = 'search-result-item';
            noResult.textContent = 'No features found with this GIS ID';
            searchResults.appendChild(noResult);
        }
    }

    async selectFeature(feature, isMobile = false) {
        this.highlightSource.clear();
        this.routeSource.clear();

        const featureClone = feature.clone();
        this.highlightSource.addFeature(featureClone);

        const view = this.map.getView();
        const geometry = feature.getGeometry();

        if (geometry.getType() === 'Point') {
            view.animate({
                center: geometry.getCoordinates(),
                zoom: 19,
                duration: 1000
            });
        } else {
            view.fit(geometry.getExtent(), {
                padding: [50, 50, 50, 50],
                duration: 1000
            });
        }

        if (!isMobile) {
            this.showFeatureInfo(feature);
        }

        document.getElementById('loadingSpinner').style.display = 'block';

        if (this.currentLocationMarker) {
            const currentCoords = this.currentLocationMarker.getGeometry().getCoordinates();
            const targetCoords = geometry.getType() === 'Point' ?
                geometry.getCoordinates() :
                ol.extent.getCenter(geometry.getExtent());

            const currentLonLat = ol.proj.toLonLat(currentCoords);
            const targetLonLat = ol.proj.toLonLat(targetCoords);

            try {
                const route = await this.calculateEnhancedRoute(currentLonLat, targetLonLat,
                    `GIS ID: ${feature.get('gisid')}`);
                this.currentRoute = route;

                if (isMobile) {
                    document.getElementById('routeBottomSheet').classList.add('open');
                } else {
                    document.getElementById('navigationControls').style.display = 'block';
                }
            } catch (error) {
                console.error('Route calculation error:', error);
                alert('Error calculating route: ' + error.message);
            }
        } else {
            if (isMobile) {
                if (confirm('Enable live location for route calculation?')) {
                    this.toggleLiveLocation();
                }
            } else {
                alert('Please enable live location for route calculation');
            }
        }

        document.getElementById('loadingSpinner').style.display = 'none';
    }

    showFeatureInfo(feature) {
        const featureInfo = document.getElementById('featureInfo');
        const featureDetails = document.getElementById('featureDetails');

        featureDetails.innerHTML = `
            <p><strong>GIS ID:</strong> ${feature.get('gisid')}</p>
            <p><strong>Type:</strong> ${feature.get('type')}</p>
            <p><strong>Coordinates:</strong> ${feature.getGeometry().getType()}</p>
        `;

        featureInfo.style.display = 'block';
    }

    clearAll() {
        this.highlightSource.clear();
        this.routeSource.clear();
        document.getElementById('navigationControls').style.display = 'none';
        document.getElementById('featureInfo').style.display = 'none';
        document.getElementById('distanceInfo').style.display = 'none';
        document.getElementById('routeInfo').style.display = 'none';
        document.getElementById('routeBottomSheet')?.classList.remove('open');
        document.getElementById('navigationHeader').style.display = 'none';
        document.getElementById('navigationInstruction').style.display = 'none';
        this.navigationMode = false;
        this.currentRoute = null;

        if (this.navigationInterval) {
            clearInterval(this.navigationInterval);
            this.navigationInterval = null;
        }
    }

    showMobileMenu() {
        const menu = `
            <div class="bottom-sheet open" id="mobileMenuSheet">
                <div class="swipe-handle"></div>
                <div class="bottom-sheet-content">
                    <h4 class="mb-3"><i class="fas fa-bars me-2"></i>Menu</h4>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="window.mapApp.recenterMap()">
                            <i class="fas fa-crosshairs me-2"></i>Recenter Map
                        </button>
                        <button class="btn btn-outline-success" onclick="window.mapApp.toggleEditingTools()">
                            <i class="fas fa-edit me-2"></i>Editing Tools
                        </button>
                        <button class="btn btn-outline-danger" onclick="window.mapApp.showDeleteModal()">
                            <i class="fas fa-trash-alt me-2"></i>Delete Feature
                        </button>
                        <button class="btn btn-outline-secondary" onclick="window.mapApp.toggleFullscreen()">
                            <i class="fas fa-expand me-2"></i>Fullscreen
                        </button>
                        <button class="btn btn-outline-info" onclick="window.mapApp.shareLocation()">
                            <i class="fas fa-share me-2"></i>Share Location
                        </button>
                        <button class="btn btn-outline-warning" onclick="window.mapApp.clearAll()">
                            <i class="fas fa-broom me-2"></i>Clear All
                        </button>
                    </div>
                    <button class="btn btn-secondary w-100 mt-3" onclick="window.mapApp.closeMobileMenu()">
                        Close
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', menu);
    }

    closeMobileMenu() {
        const menu = document.getElementById('mobileMenuSheet');
        if (menu) menu.remove();
    }

    toggleEditingTools() {
        const tools = document.getElementById('mobileEditingTools');
        tools.style.display = tools.style.display === 'none' ? 'block' : 'none';
        this.closeMobileMenu();
    }

    showDeleteModal() {
        this.closeMobileMenu();
        $("#deleteModal").modal("show");
    }

    recenterMap() {
        this.map.getView().animate({
            center: ol.extent.getCenter(this.imageExtent),
            zoom: 17,
            duration: 1000
        });
        this.closeMobileMenu();
    }

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen error:', err);
            });
        } else {
            document.exitFullscreen();
        }
        this.closeMobileMenu();
    }

    shareLocation() {
        if (navigator.share && this.currentLocationMarker) {
            const coords = ol.proj.toLonLat(this.currentLocationMarker.getGeometry().getCoordinates());
            navigator.share({
                title: 'My Current Location',
                text: `I'm at latitude ${coords[1].toFixed(6)}, longitude ${coords[0].toFixed(6)}`,
                url: window.location.href
            }).catch(err => {
                console.log('Share error:', err);
            });
        } else {
            alert('Web Share API not supported or location not available');
        }
        this.closeMobileMenu();
    }
}

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const config = {
        polygons: window.polygonsData || [],
        lines: window.linesData || [],
        points: window.pointsData || [],
        pointDatas: window.pointDatasData || [],
        polygonDatas: window.polygonDatasData || [],
        ward: window.wardData || {},
        mis: window.misData || [],
        routes: window.routesData || {},
        droneImageURL: window.droneImageURLData || '',
        imageExtent: window.imageExtentData || [0, 0, 0, 0]
    };

    window.mapApp = new SurveyorMap(config);
});
