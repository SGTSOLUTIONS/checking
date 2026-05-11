@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css">

<script>
// ============================================
// Ward Map Application - Object Oriented JavaScript
// ============================================

/**
 * Main Ward Map Application Class
 * Handles all map functionality for commissioner ward view
 */
class WardMapApp {
    constructor(config) {
        // Configuration
        this.wardId = config.wardId || null;
        this.apiUrl = config.apiUrl || '/api/ward-map-data';

        // Map Objects
        this.map = null;
        this.layers = {};
        this.sources = {};

        // Data Storage
        this.polygons = [];
        this.lines = [];
        this.points = [];
        this.pointDatas = [];
        this.polygonDatas = [];
        this.shopDatas = [];
        this.ward = {};

        // UI State
        this.currentLocationMarker = null;
        this.locationWatchId = null;
        this.isLiveLocationActive = false;
        this.selectedFeature = null;
        this.currentGisid = null;

        // DOM Elements
        this.elements = {};

        // Initialize
        this.init();
    }

    /**
     * Initialize the application
     */
    async init() {
        console.log('Initializing Ward Map Application...');

        this.cacheElements();
        this.bindEvents();
        await this.loadData();
        this.initMap();
        this.setupLayerControls();
        this.setupSearch();
        this.setupUIComponents();

        console.log('Ward Map Application Initialized Successfully');
    }

    /**
     * Cache DOM elements for better performance
     */
    cacheElements() {
        this.elements = {
            mapContainer: document.getElementById('map'),
            loadingSpinner: $('#loadingSpinner'),
            featureInfo: $('#featureInfo'),
            routeInfo: $('#routeInfo'),
            routeBtn: $('#routeBtn'),
            liveLocationBtn: $('#liveLocationBtn'),
            zoomInBtn: $('#zoomInBtn'),
            zoomOutBtn: $('#zoomOutBtn'),
            closeFeatureInfo: $('#closeFeatureInfo'),
            closeRouteInfo: $('#closeRouteInfo'),
            gisidSearchInput: $('#gisidSearchInput'),
            assessmentSearchInput: $('#assessmentSearchInput'),
            gisidSearchBtn: $('#gisidSearchBtn'),
            assessmentSearchBtn: $('#assessmentSearchBtn')
        };
    }

    /**
     * Bind global event handlers
     */
    bindEvents() {
        // Search buttons
        this.elements.gisidSearchBtn.on('click', () => this.searchByGISID());
        this.elements.assessmentSearchBtn.on('click', () => this.searchByAssessment());

        // Enter key presses
        this.elements.gisidSearchInput.on('keypress', (e) => {
            if (e.key === 'Enter') this.searchByGISID();
        });
        this.elements.assessmentSearchInput.on('keypress', (e) => {
            if (e.key === 'Enter') this.searchByAssessment();
        });

        // Zoom controls
        this.elements.zoomInBtn.on('click', () => this.zoomIn());
        this.elements.zoomOutBtn.on('click', () => this.zoomOut());

        // Location and route
        this.elements.liveLocationBtn.on('click', () => this.toggleLiveLocation());
        this.elements.routeBtn.on('click', () => this.calculateRoute());

        // Close panels
        this.elements.closeFeatureInfo.on('click', () => this.closeFeatureInfo());
        this.elements.closeRouteInfo.on('click', () => this.closeRouteInfo());

        // Search tabs
        $('.search-tab').on('click', (e) => this.switchSearchTab(e));

        // Info tabs
        $('.info-tab').on('click', (e) => this.switchInfoTab(e));
    }

    /**
     * Load data via AJAX
     */
    async loadData() {
        this.showLoading();

        try {
            // If you need to fetch data dynamically via AJAX
            const response = await $.ajax({
                url: this.apiUrl,
                method: 'GET',
                data: { ward_id: this.wardId },
                dataType: 'json'
            });

            if (response.success) {
                this.polygons = response.data.polygons || [];
                this.lines = response.data.lines || [];
                this.points = response.data.points || [];
                this.pointDatas = response.data.pointDatas || [];
                this.polygonDatas = response.data.polygonDatas || [];
                this.shopDatas = response.data.shopDatas || [];
                this.ward = response.data.ward || {};
            }
        } catch (error) {
            console.error('Error loading data via AJAX, using embedded data', error);

            // Fallback to embedded data from Blade
            this.polygons = @json($polygons ?? []);
            this.lines = @json($lines ?? []);
            this.points = @json($points ?? []);
            this.pointDatas = @json($pointDatas ?? []);
            this.polygonDatas = @json($polygonDatas ?? []);
            this.shopDatas = @json($shopDatas ?? []);
            this.ward = @json($ward ?? []);
        }

        this.hideLoading();
        console.log(`Data loaded: ${this.polygons.length} polygons, ${this.points.length} points, ${this.shopDatas.length} shops`);
    }

    /**
     * Initialize OpenLayers map
     */
    initMap() {
        // Create sources
        this.sources.polygon = new ol.source.Vector();
        this.sources.line = new ol.source.Vector();
        this.sources.point = new ol.source.Vector();
        this.sources.highlight = new ol.source.Vector();
        this.sources.location = new ol.source.Vector();
        this.sources.route = new ol.source.Vector();

        // Create layers
        this.layers.base = {
            osm: new ol.layer.Tile({ source: new ol.source.OSM(), visible: true }),
            satellite: new ol.layer.Tile({
                source: new ol.source.XYZ({
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                }),
                visible: false
            }),
            terrain: new ol.layer.Tile({
                source: new ol.source.XYZ({
                    url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'
                }),
                visible: false
            })
        };

        this.layers.polygon = new ol.layer.Vector({
            source: this.sources.polygon,
            style: (feature) => this.getPolygonStyle(feature),
            visible: true
        });

        this.layers.line = new ol.layer.Vector({
            source: this.sources.line,
            style: this.getLineStyle(),
            visible: true
        });

        this.layers.point = new ol.layer.Vector({
            source: this.sources.point,
            style: (feature) => this.getPointStyle(feature),
            visible: true
        });

        this.layers.highlight = new ol.layer.Vector({
            source: this.sources.highlight,
            style: this.getHighlightStyle()
        });

        this.layers.location = new ol.layer.Vector({
            source: this.sources.location,
            style: this.getHumanLocationStyle()
        });

        this.layers.route = new ol.layer.Vector({
            source: this.sources.route,
            style: this.getRouteStyle()
        });

        this.layers.boundary = this.createBoundaryLayer();
        this.layers.drone = this.createDroneLayer();

        // Add all layers to map
        const mapLayers = [
            this.layers.base.osm,
            this.layers.base.satellite,
            this.layers.base.terrain,
            this.layers.drone,
            this.layers.boundary,
            this.layers.polygon,
            this.layers.line,
            this.layers.point,
            this.layers.highlight,
            this.layers.location,
            this.layers.route
        ];

        // Calculate default center
        const defaultCenter = this.calculateDefaultCenter();

        // Create map
        this.map = new ol.Map({
            target: 'map',
            layers: mapLayers,
            view: new ol.View({
                projection: "EPSG:3857",
                center: defaultCenter,
                zoom: 16
            }),
            controls: []
        });

        // Add click handler
        this.map.on('click', (evt) => this.handleMapClick(evt));

        // Add features to layers
        this.addPolygons();
        this.addLines();
        this.addPoints();

        // Auto-fit bounds
        this.autoFitBounds();

        console.log('Map initialized successfully');
    }

    /**
     * Add polygons to map
     */
    addPolygons() {
        if (!this.polygons.length) return;

        this.polygons.forEach(poly => {
            try {
                let coords = typeof poly.coordinates === 'string' ? JSON.parse(poly.coordinates) : poly.coordinates;
                if (coords && coords.length) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Polygon(coords),
                        gisid: poly.gisid,
                        type: "Polygon",
                        sqfeet: poly.sqfeet || "0"
                    });
                    this.sources.polygon.addFeature(feature);
                }
            } catch(e) {
                console.error("Error adding polygon:", e);
            }
        });
    }

    /**
     * Add lines to map
     */
    addLines() {
        if (!this.lines.length) return;

        this.lines.forEach(line => {
            try {
                let coords = typeof line.coordinates === 'string' ? JSON.parse(line.coordinates) : line.coordinates;
                if (coords && coords.length >= 2) {
                    if (coords.length === 1 && Array.isArray(coords[0][0])) coords = coords[0];
                    const feature = new ol.Feature({
                        geometry: new ol.geom.LineString(coords),
                        gisid: line.gisid,
                        type: "Line"
                    });
                    this.sources.line.addFeature(feature);
                }
            } catch(e) {
                console.error("Error adding line:", e);
            }
        });
    }

    /**
     * Add points to map
     */
    addPoints() {
        if (!this.points.length) return;

        this.points.forEach(point => {
            try {
                let coords = typeof point.coordinates === 'string' ? JSON.parse(point.coordinates) : point.coordinates;
                if (coords && coords.length === 2) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                        gisid: point.gisid,
                        type: "Point"
                    });
                    this.sources.point.addFeature(feature);
                }
            } catch(e) {
                console.error("Error adding point:", e);
            }
        });
    }

    /**
     * Create boundary layer
     */
    createBoundaryLayer() {
        let boundaryLayer = new ol.layer.Vector({
            source: new ol.source.Vector(),
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({ color: "#ff0000", width: 2 }),
                fill: new ol.style.Fill({ color: "rgba(255, 0, 0, 0.03)" })
            }),
            visible: true
        });

        if (this.ward && this.ward.boundary && this.ward.boundary[0] && this.ward.boundary[0].length > 0) {
            try {
                const boundary = this.ward.boundary[0];
                const transformedBoundary = boundary.map(pt => ol.proj.fromLonLat(pt));
                const feature = new ol.Feature({
                    geometry: new ol.geom.Polygon([transformedBoundary])
                });
                boundaryLayer.getSource().addFeature(feature);
            } catch(e) {
                console.error("Error creating boundary layer:", e);
            }
        }

        return boundaryLayer;
    }

    /**
     * Create drone image layer
     */
    createDroneLayer() {
        if (this.ward && this.ward.drone_image && this.ward.extent_left) {
            const imageExtent = [
                parseFloat(this.ward.extent_left),
                parseFloat(this.ward.extent_bottom),
                parseFloat(this.ward.extent_right),
                parseFloat(this.ward.extent_top)
            ];
            const droneImageURL = "{{ asset($ward->drone_image ?? '') }}";

            if (droneImageURL && imageExtent[0] !== 0 && !isNaN(imageExtent[0])) {
                return new ol.layer.Image({
                    source: new ol.source.ImageStatic({
                        url: droneImageURL,
                        imageExtent: imageExtent,
                        imageSmoothing: false
                    }),
                    opacity: 0.8,
                    visible: true
                });
            }
        }

        return new ol.layer.Image({
            source: new ol.source.ImageStatic({ url: "", imageExtent: [0, 0, 0, 0] }),
            visible: false
        });
    }

    /**
     * Calculate default map center
     */
    calculateDefaultCenter() {
        let defaultCenter = ol.proj.fromLonLat([80.2707, 13.0827]);

        if (this.ward && this.ward.boundary && this.ward.boundary[0] && this.ward.boundary[0].length > 0) {
            try {
                const boundary = this.ward.boundary[0];
                const lons = boundary.map(pt => pt[0]);
                const lats = boundary.map(pt => pt[1]);
                const centerLon = (Math.min(...lons) + Math.max(...lons)) / 2;
                const centerLat = (Math.min(...lats) + Math.max(...lats)) / 2;
                defaultCenter = ol.proj.fromLonLat([centerLon, centerLat]);
            } catch(e) {
                console.error("Error calculating center from boundary:", e);
            }
        }

        return defaultCenter;
    }

    /**
     * Auto-fit map bounds to show all features
     */
    autoFitBounds() {
        setTimeout(() => {
            try {
                const extent = ol.extent.createEmpty();
                let hasExtent = false;

                this.sources.polygon.forEachFeature(f => {
                    ol.extent.extend(extent, f.getGeometry().getExtent());
                    hasExtent = true;
                });

                this.sources.point.forEachFeature(f => {
                    ol.extent.extend(extent, f.getGeometry().getExtent());
                    hasExtent = true;
                });

                if (hasExtent && extent[0] !== Infinity && extent[0] !== -Infinity) {
                    this.map.getView().fit(extent, { padding: [30, 30, 30, 30], duration: 1000 });
                }
            } catch(e) {
                console.log("Could not auto-fit extent, using default view");
            }
        }, 800);
    }

    /**
     * Get polygon style
     */
    getPolygonStyle(feature) {
        const gisid = feature.get("gisid");
        const polygonData = this.polygonDatas.find(data => data.gisid == gisid);
        const color = polygonData ? "red" : "blue";

        const geometry = feature.getGeometry();
        const centerPoint = geometry.getInteriorPoint();

        return [
            new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: color,
                    width: 4,
                    lineJoin: "round",
                    lineCap: "round"
                }),
                fill: new ol.style.Fill({
                    color: "rgba(0, 0, 0, 0.05)"
                })
            }),
            new ol.style.Style({
                geometry: centerPoint,
                text: new ol.style.Text({
                    text: feature.get("sqfeet") + " SQFT",
                    font: "bold 14px Arial",
                    fill: new ol.style.Fill({ color: "#ffffff" }),
                    stroke: new ol.style.Stroke({ color: "#000000", width: 3 }),
                    overflow: true,
                    textAlign: "center",
                    offsetY: 0
                })
            })
        ];
    }

    /**
     * Get point style
     */
    getPointStyle(feature) {
        const gisid = feature.get("gisid");
        const pointCount = this.pointDatas.filter(d => d.point_gisid == gisid).length;
        const polygonData = this.polygonDatas.find(d => d.gisid == gisid);
        let color = "#1679AB";

        if (polygonData && pointCount > 0) {
            color = (polygonData.number_bill == pointCount) ? "#28a745" : "#dc3545";
        }

        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 7,
                fill: new ol.style.Fill({ color: color }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            }),
            text: new ol.style.Text({
                text: gisid ? String(gisid) : "",
                font: "10px Arial",
                offsetY: -12,
                fill: new ol.style.Fill({ color: "#333" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            })
        });
    }

    /**
     * Get line style
     */
    getLineStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#ffc107", width: 2 })
        });
    }

    /**
     * Get highlight style
     */
    getHighlightStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#ff6600", width: 4 }),
            fill: new ol.style.Fill({ color: "rgba(255, 102, 0, 0.15)" }),
            image: new ol.style.Circle({
                radius: 10,
                fill: new ol.style.Fill({ color: "#ff6600" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            })
        });
    }

    /**
     * Get location style
     */
    getHumanLocationStyle() {
        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 10,
                fill: new ol.style.Fill({ color: "#0066cc" }),
                stroke: new ol.style.Stroke({ color: "#fff", width: 2 })
            })
        });
    }

    /**
     * Get route style
     */
    getRouteStyle() {
        return new ol.style.Style({
            stroke: new ol.style.Stroke({ color: "#0066cc", width: 4, lineDash: [8, 8] })
        });
    }

    /**
     * Setup layer visibility controls
     */
    setupLayerControls() {
        // Base layer switcher
        $('input[name="baseLayer"]').on('change', (e) => {
            const val = $(e.target).val();
            this.layers.base.osm.setVisible(val === 'osm');
            this.layers.base.satellite.setVisible(val === 'satellite');
            this.layers.base.terrain.setVisible(val === 'terrain');
        });

        // Overlay toggles
        $('#showDroneImage').on('change', (e) => this.layers.drone.setVisible(e.target.checked));
        $('#showBoundary').on('change', (e) => this.layers.boundary.setVisible(e.target.checked));
        $('#showPolygons').on('change', (e) => this.layers.polygon.setVisible(e.target.checked));
        $('#showLines').on('change', (e) => this.layers.line.setVisible(e.target.checked));
        $('#showPoints').on('change', (e) => this.layers.point.setVisible(e.target.checked));
    }

    /**
     * Setup search functionality
     */
    setupSearch() {
        // Search by GIS ID
        window.searchByGISID = () => this.searchByGISID();

        // Search by Assessment
        window.searchByAssessment = () => this.searchByAssessment();
    }

    /**
     * Setup UI components
     */
    setupUIComponents() {
        // Make sure UI elements are clickable
        $('.search-box input').on('click', function(e) {
            e.stopPropagation();
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            setTimeout(() => this.map.updateSize(), 200);
        });
    }

    /**
     * Switch search tab
     */
    switchSearchTab(event) {
        const tab = $(event.target).data('tab');
        $('.search-tab').removeClass('active');
        $(event.target).addClass('active');
        $('.search-panel').removeClass('active');
        $(`#${tab}Panel`).addClass('active');
        $(`#${tab}Results`).hide();
    }

    /**
     * Switch info tab
     */
    switchInfoTab(event) {
        const tabId = $(event.target).data('tab');
        $('.info-tab').removeClass('active');
        $(event.target).addClass('active');
        $('.info-tab-content').removeClass('active');
        $(`#${tabId}`).addClass('active');
    }

    /**
     * Search by GIS ID
     */
    searchByGISID() {
        const gisid = this.elements.gisidSearchInput.val().trim();

        if (!gisid) {
            this.showFlashMessage('Please enter GIS ID', 'warning');
            return;
        }

        this.showLoading();
        this.sources.highlight.clear();

        let foundFeature = null;

        // Search in polygons
        this.sources.polygon.forEachFeature(f => {
            if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) {
                foundFeature = f;
                return true;
            }
        });

        // Search in points if not found in polygons
        if (!foundFeature) {
            this.sources.point.forEachFeature(f => {
                if (f.get('gisid') && f.get('gisid').toString() === gisid.toString()) {
                    foundFeature = f;
                    return true;
                }
            });
        }

        if (foundFeature) {
            this.highlightFeature(foundFeature);
            this.displayFullPropertyInfo(gisid);
            this.selectedFeature = foundFeature;
            this.elements.routeBtn.show();
            this.hideLoading();
            this.elements.gisidSearchInput.val('');
        } else {
            this.hideLoading();
            this.showFlashMessage(`GIS ID "${gisid}" not found`, "error");
        }
    }

    /**
     * Search by Assessment Number
     */
    searchByAssessment() {
        const assessmentNo = this.elements.assessmentSearchInput.val().trim();

        if (!assessmentNo) {
            this.showFlashMessage('Please enter Assessment Number', 'warning');
            return;
        }

        this.showLoading();
        this.sources.highlight.clear();

        const pointData = this.pointDatas.find(d => d.assessment == assessmentNo);

        if (pointData && pointData.point_gisid) {
            let foundFeature = null;

            this.sources.point.forEachFeature(f => {
                if (f.get('gisid') && f.get('gisid').toString() === pointData.point_gisid.toString()) {
                    foundFeature = f;
                    return true;
                }
            });

            if (foundFeature) {
                this.highlightFeature(foundFeature);
                this.displayFullPropertyInfo(pointData.point_gisid);
                this.selectedFeature = foundFeature;
                this.elements.routeBtn.show();
                this.elements.assessmentSearchInput.val('');
            } else {
                this.showFlashMessage(`Assessment "${assessmentNo}" not found on map`, "error");
            }
        } else {
            this.showFlashMessage(`Assessment "${assessmentNo}" not found`, "error");
        }

        this.hideLoading();
    }

    /**
     * Highlight a feature on the map
     */
    highlightFeature(feature) {
        this.sources.highlight.clear();
        this.sources.highlight.addFeature(feature.clone());

        // Fit view to feature
        this.map.getView().fit(feature.getGeometry().getExtent(), {
            padding: [50, 50, 50, 50],
            duration: 1000
        });
    }

    /**
     * Display full property information including shops and assessments
     */
    displayFullPropertyInfo(gisid) {
        this.currentGisid = gisid;

        const polygonData = this.polygonDatas.find(d => d.gisid == gisid);
        const assessments = this.getAssessmentsByGisid(gisid);
        const shops = this.getShopsByBuildingGisid(gisid);
        const pointCount = assessments.length;

        // Building Details HTML
        let buildingHtml = `
            <div class="info-row">
                <span class="info-label">GIS ID:</span>
                <span class="info-value"><strong>${gisid}</strong></span>
            </div>
        `;

        if (polygonData) {
            buildingHtml += `
                <div class="info-row">
                    <span class="info-label">Building Name:</span>
                    <span class="info-value">${polygonData.building_name || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Building Usage:</span>
                    <span class="info-value">${polygonData.building_usage || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Construction Type:</span>
                    <span class="info-value">${polygonData.construction_type || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Road Name:</span>
                    <span class="info-value">${polygonData.road_name || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Floors:</span>
                    <span class="info-value">${polygonData.number_floor || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Shops/Units:</span>
                    <span class="info-value">${polygonData.number_shop || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Bills:</span>
                    <span class="info-value">${polygonData.number_bill || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Assessments Done:</span>
                    <span class="info-value">${pointCount}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Square Feet:</span>
                    <span class="info-value">${polygonData.sqfeet || 'N/A'} sqft</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="badge-status ${polygonData.number_bill == pointCount ? 'badge-completed' : 'badge-pending'}">
                        ${polygonData.number_bill == pointCount ? 'Completed' : (pointCount > 0 ? 'Partial' : 'Not Started')}
                    </span>
                </div>
            `;
        } else {
            buildingHtml += `<div class="info-row"><span class="info-label">Note:</span><span class="info-value">No building data available</span></div>`;
        }

        $('#featureDetails').html(buildingHtml);

        // Shops List HTML
        let shopsHtml = '';
        if (shops && shops.length > 0) {
            shopsHtml = `<div class="shop-list">`;
            shops.forEach((shop, index) => {
                shopsHtml += `
                    <div class="shop-item">
                        <h6><span class="badge-shop">Shop ${index + 1}</span> ${shop.shop_name || 'Unnamed Shop'}</h6>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Floor:</span>
                            <span class="shop-detail-value">${shop.shop_floor || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Owner Name:</span>
                            <span class="shop-detail-value">${shop.shop_owner_name || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Category:</span>
                            <span class="shop-detail-value">${shop.shop_category || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Mobile:</span>
                            <span class="shop-detail-value">${shop.shop_mobile || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">License No:</span>
                            <span class="shop-detail-value">${shop.license || 'N/A'}</span>
                        </div>
                    </div>
                `;
            });
            shopsHtml += `</div>`;
        } else {
            shopsHtml = `<div class="text-muted text-center p-3">No shops found for this building</div>`;
        }
        $('#shopsDetails').html(shopsHtml);

        // Assessments List HTML
        let assessmentsHtml = '';
        if (assessments && assessments.length > 0) {
            assessmentsHtml = `<div class="shop-list">`;
            assessments.forEach((assessment, index) => {
                assessmentsHtml += `
                    <div class="shop-item">
                        <h6><span class="badge-shop">Assessment ${index + 1}</span> ${assessment.assessment || 'N/A'}</h6>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Owner Name:</span>
                            <span class="shop-detail-value">${assessment.owner_name || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Present Owner:</span>
                            <span class="shop-detail-value">${assessment.present_owner_name || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Phone:</span>
                            <span class="shop-detail-value">${assessment.phone_number || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Floor:</span>
                            <span class="shop-detail-value">${assessment.floor || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Bill Usage:</span>
                            <span class="shop-detail-value">${assessment.bill_usage || 'N/A'}</span>
                        </div>
                        <div class="shop-detail-row">
                            <span class="shop-detail-label">Water Tax:</span>
                            <span class="shop-detail-value">${assessment.water_tax || 'N/A'}</span>
                        </div>
                    </div>
                `;
            });
            assessmentsHtml += `</div>`;
        } else {
            assessmentsHtml = `<div class="text-muted text-center p-3">No assessments found for this building</div>`;
        }
        $('#assessmentsDetails').html(assessmentsHtml);

        this.elements.featureInfo.fadeIn();
    }

    /**
     * Get shops by building GIS ID
     */
    getShopsByBuildingGisid(gisid) {
        const buildingPoints = this.pointDatas.filter(pd => pd.point_gisid == gisid);
        const pointDataIds = buildingPoints.map(pd => pd.id);
        return this.shopDatas.filter(shop => pointDataIds.includes(shop.point_data_id));
    }

    /**
     * Get assessments by GIS ID
     */
    getAssessmentsByGisid(gisid) {
        return this.pointDatas.filter(pd => pd.point_gisid == gisid);
    }

    /**
     * Handle map click
     */
    handleMapClick(evt) {
        // Don't process if clicking on UI elements
        const target = evt.originalEvent.target;
        if (target.tagName === 'INPUT' ||
            target.tagName === 'BUTTON' ||
            target.closest('.search-container') ||
            target.closest('.layer-switcher') ||
            target.closest('.zoom-controls')) {
            return;
        }

        const feature = this.map.forEachFeatureAtPixel(evt.pixel, f => f);

        if (feature && feature.get('gisid')) {
            const gisid = feature.get('gisid');
            this.highlightFeature(feature);
            this.displayFullPropertyInfo(gisid);
            this.selectedFeature = feature;
            this.elements.routeBtn.show();
        } else {
            this.closeFeatureInfo();
            this.selectedFeature = null;
            this.elements.routeBtn.hide();
        }
    }

    /**
     * Toggle live location tracking
     */
    toggleLiveLocation() {
        if (this.isLiveLocationActive) {
            // Stop tracking
            if (this.locationWatchId) {
                navigator.geolocation.clearWatch(this.locationWatchId);
            }
            this.sources.location.clear();
            this.currentLocationMarker = null;
            this.isLiveLocationActive = false;
            this.elements.liveLocationBtn.removeClass('active').html('<i class="fas fa-location-dot me-2"></i>Live Location');
            this.showFlashMessage('Location tracking stopped', 'info');
        } else {
            // Start tracking
            if (!navigator.geolocation) {
                this.showFlashMessage('Geolocation not supported', 'error');
                return;
            }

            this.isLiveLocationActive = true;
            this.elements.liveLocationBtn.addClass('active').html('<i class="fas fa-stop me-2"></i>Stop Location');

            this.locationWatchId = navigator.geolocation.watchPosition(
                (position) => {
                    const coords = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]);
                    this.sources.location.clear();
                    this.currentLocationMarker = new ol.Feature({ geometry: new ol.geom.Point(coords) });
                    this.sources.location.addFeature(this.currentLocationMarker);
                },
                (error) => {
                    this.showFlashMessage('Location error: ' + error.message, 'error');
                    this.toggleLiveLocation();
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }

    /**
     * Calculate route from current location to selected property
     */
    async calculateRoute() {
        if (!this.selectedFeature) {
            this.showFlashMessage('Please select a property first by searching or clicking on map', 'warning');
            return;
        }

        if (!this.currentLocationMarker) {
            this.showFlashMessage('Please enable Live Location first', 'warning');
            return;
        }

        this.showLoading();
        this.sources.route.clear();

        try {
            const startCoord = ol.proj.toLonLat(this.currentLocationMarker.getGeometry().getCoordinates());
            const targetGeom = this.selectedFeature.getGeometry();
            const endCoord = targetGeom.getType() === 'Point' ?
                ol.proj.toLonLat(targetGeom.getCoordinates()) :
                ol.proj.toLonLat(ol.extent.getCenter(targetGeom.getExtent()));

            const url = `https://router.project-osrm.org/route/v1/driving/${startCoord[0]},${startCoord[1]};${endCoord[0]},${endCoord[1]}?overview=full&geometries=geojson&steps=true`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.code === 'Ok' && data.routes.length > 0) {
                const route = data.routes[0];
                const coords = route.geometry.coordinates.map(c => ol.proj.fromLonLat(c));
                this.sources.route.addFeature(new ol.Feature({ geometry: new ol.geom.LineString(coords) }));

                const distance = route.distance < 1000 ? route.distance.toFixed(0) + ' meters' : (route.distance / 1000).toFixed(2) + ' km';
                const duration = Math.floor(route.duration / 60) + ' min ' + Math.floor(route.duration % 60) + ' sec';

                let stepsHtml = '';
                if (route.legs && route.legs[0] && route.legs[0].steps) {
                    route.legs[0].steps.forEach((step, i) => {
                        if (step.maneuver && step.maneuver.instruction) {
                            stepsHtml += `
                                <div class="direction-step">
                                    <div class="step-number">${i + 1}</div>
                                    <div>
                                        <div class="step-instruction">${step.maneuver.instruction}</div>
                                        <div class="step-distance">${step.distance.toFixed(0)} m</div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                }

                $('#routeSummary').html(`<strong>Distance:</strong> ${distance}<br><strong>Duration:</strong> ${duration}`);
                $('#directionsList').html(stepsHtml || '<div class="text-muted">No step-by-step directions available</div>');
                this.elements.routeInfo.fadeIn();

                // Fit view to route
                this.map.getView().fit(this.sources.route.getExtent(), {
                    padding: [50, 50, 50, 50],
                    duration: 1000
                });
            } else {
                this.showFlashMessage('No route found', 'error');
            }
        } catch (error) {
            console.error("Route error:", error);
            this.showFlashMessage('Error calculating route', 'error');
        }

        this.hideLoading();
    }

    /**
     * Zoom in
     */
    zoomIn() {
        this.map.getView().setZoom(this.map.getView().getZoom() + 1);
    }

    /**
     * Zoom out
     */
    zoomOut() {
        this.map.getView().setZoom(this.map.getView().getZoom() - 1);
    }

    /**
     * Close feature info panel
     */
    closeFeatureInfo() {
        this.elements.featureInfo.fadeOut();
        this.sources.highlight.clear();
    }

    /**
     * Close route info panel
     */
    closeRouteInfo() {
        this.elements.routeInfo.fadeOut();
    }

    /**
     * Show loading spinner
     */
    showLoading() {
        this.elements.loadingSpinner.fadeIn();
    }

    /**
     * Hide loading spinner
     */
    hideLoading() {
        this.elements.loadingSpinner.fadeOut();
    }

    /**
     * Show flash message
     */
    showFlashMessage(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : (type === 'warning' ? 'alert-warning' : 'alert-info');
        const flashHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 100px; right: 20px; z-index: 9999; min-width: 280px; max-width: 400px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('body').append(flashHtml);

        setTimeout(() => {
            $('.alert').alert('close');
        }, 4000);
    }
}

// ============================================
// Initialize Application
// ============================================
$(document).ready(function() {
    // Wait for OpenLayers to load
    const checkOpenLayers = setInterval(function() {
        if (typeof ol !== 'undefined' && typeof ol.Map !== 'undefined') {
            clearInterval(checkOpenLayers);

            // Initialize the map application
            window.wardMapApp = new WardMapApp({
                wardId: {{ $ward->id ?? 'null' }},
                apiUrl: '{{ route("api.ward-map-data") }}' // Define this route in your routes file
            });
        }
    }, 100);

    // Fallback after 5 seconds
    setTimeout(function() {
        if (typeof ol === 'undefined') {
            console.error("OpenLayers failed to load");
            $('#map').html('<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f8f9fa;color:#dc3545;">Error: OpenLayers library failed to load. Please refresh the page.</div>');
        }
    }, 5000);
});
</script>
@endpush
