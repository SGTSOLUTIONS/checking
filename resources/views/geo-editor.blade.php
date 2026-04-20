<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGT Map Dashboard - Geo Editor</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #map {
            width: 100%;
            height: calc(100vh - 56px);
        }

        /* Sidebar styling */
        .sidebar {
            position: absolute;
            top: 56px;
            left: 0;
            width: 400px;
            height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            background-color: #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-content {
            padding: 15px;
        }

        .sidebar-toggle {
            position: absolute;
            top: 70px;
            left: 400px;
            width: 24px;
            height: 24px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-left: none;
            border-radius: 0 4px 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            transition: left 0.3s ease;
        }

        .sidebar-toggle.collapsed {
            left: 0;
        }

        /* Panel styling */
        .panel {
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            overflow: hidden;
        }

        .panel-header {
            padding: 10px 15px;
            background-color: #f1f1f1;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            cursor: move;
        }

        .panel-body {
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        /* Layer list styling */
        .layer-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .layer-item:last-child {
            border-bottom: none;
        }

        .layer-checkbox {
            margin-right: 10px;
        }

        .layer-name {
            flex-grow: 1;
            cursor: pointer;
        }

        .layer-actions {
            display: flex;
            gap: 5px;
        }

        .layer-attributes {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            display: none;
        }

        .attribute-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .attribute-item:last-child {
            border-bottom: none;
        }

        .attribute-name {
            font-weight: 500;
        }

        /* Tools panel */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .tool-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tool-btn:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        .tool-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tool-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }

        /* Map controls */
        .map-controls {
            position: absolute;
            top: 70px;
            right: 10px;
            z-index: 1000;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2);
            padding: 5px;
        }

        .map-control-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 2px;
        }

        .map-control-btn:hover {
            background-color: #f1f1f1;
        }

        /* Feature form */
        .feature-form {
            position: absolute;
            bottom: 20px;
            left: 420px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        }

        .feature-form.hidden {
            display: none;
        }

        /* Project manager */
        .project-item {
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .project-item:hover {
            background-color: #f8f9fa;
        }

        .project-item.active {
            border-color: #007bff;
            background-color: #e7f1ff;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 9999;
        }

        /* Attribute form styles */
        .attribute-form-group {
            margin-bottom: 10px;
        }

        .layer-editable-badge {
            font-size: 0.7em;
            padding: 2px 6px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SGT Geo Editor</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0">Geo Editor</h5>
            <button class="btn btn-sm btn-outline-secondary" id="close-sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="sidebar-content">
            <!-- Projects Panel -->
            <div class="panel" id="projects-panel">
                <div class="panel-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-folder me-2"></i>Projects</span>
                    <button class="btn btn-sm btn-primary" id="create-project-btn">
                        <i class="bi bi-plus"></i> New
                    </button>
                </div>
                <div class="panel-body" id="projects-list">
                    <div class="text-center text-muted">Loading projects...</div>
                </div>
            </div>

            <!-- Layers Panel -->
            <div class="panel" id="layers-panel">
                <div class="panel-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-layers me-2"></i>Layers</span>
                    <button class="btn btn-sm btn-primary" id="create-layer-btn" disabled>
                        <i class="bi bi-plus"></i> New
                    </button>
                </div>
                <div class="panel-body" id="layers-container">
                    <div class="text-center text-muted">Select a project to view layers</div>
                </div>
            </div>

            <!-- Tools Panel -->
            <div class="panel" id="tools-panel">
                <div class="panel-header">
                    <i class="bi bi-tools me-2"></i>Drawing Tools
                </div>
                <div class="panel-body">
                    <div class="tools-grid">
                        <div class="tool-btn" data-tool="point" title="Point">
                            <i class="bi bi-geo-alt tool-icon"></i>
                            <small>Point</small>
                        </div>
                        <div class="tool-btn" data-tool="linestring" title="Line">
                            <i class="bi bi-slash-lg tool-icon"></i>
                            <small>Line</small>
                        </div>
                        <div class="tool-btn" data-tool="polygon" title="Polygon">
                            <i class="bi bi-square tool-icon"></i>
                            <small>Polygon</small>
                        </div>
                        <div class="tool-btn" data-tool="modify" title="Modify">
                            <i class="bi bi-pencil tool-icon"></i>
                            <small>Modify</small>
                        </div>
                        <div class="tool-btn" data-tool="select" title="Select">
                            <i class="bi bi-cursor tool-icon"></i>
                            <small>Select</small>
                        </div>
                        <div class="tool-btn" data-tool="delete" title="Delete">
                            <i class="bi bi-trash tool-icon"></i>
                            <small>Delete</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Properties Panel -->
            <div class="panel" id="properties-panel">
                <div class="panel-header">
                    <i class="bi bi-card-text me-2"></i>Feature Properties
                </div>
                <div class="panel-body" id="properties-container">
                    <div class="text-center text-muted">Select a feature to edit properties</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-toggle" id="sidebar-toggle">
        <i class="bi bi-chevron-right"></i>
    </div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="map-control-btn" title="Home" id="home-btn">
            <i class="bi bi-house"></i>
        </button>
        <button class="map-control-btn" title="Search">
            <i class="bi bi-search"></i>
        </button>
        <button class="map-control-btn" title="Basemap" id="basemap-btn">
            <i class="bi bi-map"></i>
        </button>
        <button class="map-control-btn" title="Bookmarks">
            <i class="bi bi-bookmark"></i>
        </button>
    </div>

    <!-- Feature Form -->
    <div class="feature-form hidden" id="feature-form">
        <h6>Edit Feature Properties</h6>
        <form id="feature-properties-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="feature-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="feature-name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="feature-description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="feature-description">
                    </div>
                </div>
            </div>
            <div id="dynamic-attributes">
                <!-- Dynamic attributes will be added here -->
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" id="save-feature">Save</button>
                <button type="button" class="btn btn-secondary" id="cancel-edit">Cancel</button>
                <button type="button" class="btn btn-danger ms-auto" id="delete-feature">Delete</button>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Fullscreen Map -->
    <div id="map"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>

    <script>
        // Global variables
        let map;
        let currentProject = null;
        let currentLayer = null;
        let selectedFeature = null;
        let drawInteraction = null;
        let modifyInteraction = null;
        let selectInteraction = null;
        let vectorSource = new ol.source.Vector();
        let vectorLayer = new ol.layer.Vector({
            source: vectorSource,
            style: new ol.style.Style({
                fill: new ol.style.Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                }),
                stroke: new ol.style.Stroke({
                    color: '#ffcc33',
                    width: 2
                }),
                image: new ol.style.Circle({
                    radius: 7,
                    fill: new ol.style.Fill({
                        color: '#ffcc33'
                    })
                })
            })
        });

        // Initialize OpenLayers Map
        function initializeMap() {
            map = new ol.Map({
                target: 'map',
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    }),
                    vectorLayer
                ],
                view: new ol.View({
                    center: ol.proj.fromLonLat([78.8209, 10.3833]), // Pudukkottai approx.
                    zoom: 12
                })
            });

            // Add select interaction
            selectInteraction = new ol.interaction.Select({
                layers: [vectorLayer]
            });
            map.addInteraction(selectInteraction);

            // Handle feature selection
            selectInteraction.on('select', function(e) {
                if (e.selected.length > 0) {
                    selectedFeature = e.selected[0];
                    showFeatureForm(selectedFeature);
                } else {
                    hideFeatureForm();
                }
            });
        }

        // Layer Manager Class
        class LayerManager {
            constructor() {
                this.layers = [];
                this.currentLayer = null;
            }

            async loadLayers(projectId) {
                try {
                    showLoading();
                    const response = await fetch(`/api/projects/${projectId}/layers`);
                    if (!response.ok) throw new Error('Failed to load layers');
                    
                    this.layers = await response.json();
                    this.currentProject = projectId;
                    this.renderLayers();
                    hideLoading();
                    return this.layers;
                } catch (error) {
                    console.error('Error loading layers:', error);
                    this.showError('Failed to load layers: ' + error.message);
                    hideLoading();
                }
            }

            async createLayer(layerData) {
                try {
                    showLoading();
                    const response = await fetch('/api/layers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(layerData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.layers.push(result.layer);
                        this.renderLayers();
                        this.showSuccess('Layer created successfully');
                        hideLoading();
                        return result.layer;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error creating layer:', error);
                    this.showError('Failed to create layer: ' + error.message);
                    hideLoading();
                }
            }

            async updateLayer(layerId, layerData) {
                try {
                    showLoading();
                    const response = await fetch(`/api/layers/${layerId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(layerData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        const index = this.layers.findIndex(l => l.id === layerId);
                        if (index !== -1) {
                            this.layers[index] = result.layer;
                            this.renderLayers();
                        }
                        this.showSuccess('Layer updated successfully');
                        hideLoading();
                        return result.layer;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error updating layer:', error);
                    this.showError('Failed to update layer: ' + error.message);
                    hideLoading();
                }
            }

            async deleteLayer(layerId) {
                try {
                    showLoading();
                    const response = await fetch(`/api/layers/${layerId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.layers = this.layers.filter(l => l.id !== layerId);
                        this.renderLayers();
                        this.showSuccess('Layer deleted successfully');
                        hideLoading();
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error deleting layer:', error);
                    this.showError('Failed to delete layer: ' + error.message);
                    hideLoading();
                }
            }

            async toggleLayerEditing(layerId) {
                try {
                    showLoading();
                    const response = await fetch(`/api/layers/${layerId}/toggle-editing`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        const layer = this.layers.find(l => l.id === layerId);
                        if (layer) {
                            layer.is_editable = result.layer.is_editable;
                            this.renderLayers();
                        }
                        this.showSuccess(result.message);
                        hideLoading();
                        return result.layer;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error toggling layer editing:', error);
                    this.showError('Failed to toggle layer editing: ' + error.message);
                    hideLoading();
                }
            }

            // Attribute Management
            async addAttribute(layerId, attributeData) {
                try {
                    showLoading();
                    const response = await fetch(`/api/layers/${layerId}/attributes`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(attributeData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        const layer = this.layers.find(l => l.id === layerId);
                        if (layer) {
                            layer.attributes.push(result.attribute);
                            this.renderLayers();
                        }
                        this.showSuccess('Attribute added successfully');
                        hideLoading();
                        return result.attribute;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error adding attribute:', error);
                    this.showError('Failed to add attribute: ' + error.message);
                    hideLoading();
                }
            }

            async updateAttribute(attributeId, attributeData) {
                try {
                    showLoading();
                    const response = await fetch(`/api/attributes/${attributeId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(attributeData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        // Update attribute in all layers
                        this.layers.forEach(layer => {
                            const attrIndex = layer.attributes.findIndex(attr => attr.id === attributeId);
                            if (attrIndex !== -1) {
                                layer.attributes[attrIndex] = result.attribute;
                            }
                        });
                        this.renderLayers();
                        this.showSuccess('Attribute updated successfully');
                        hideLoading();
                        return result.attribute;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error updating attribute:', error);
                    this.showError('Failed to update attribute: ' + error.message);
                    hideLoading();
                }
            }

            async deleteAttribute(attributeId) {
                try {
                    showLoading();
                    const response = await fetch(`/api/attributes/${attributeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        // Remove attribute from all layers
                        this.layers.forEach(layer => {
                            layer.attributes = layer.attributes.filter(attr => attr.id !== attributeId);
                        });
                        this.renderLayers();
                        this.showSuccess('Attribute deleted successfully');
                        hideLoading();
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error deleting attribute:', error);
                    this.showError('Failed to delete attribute: ' + error.message);
                    hideLoading();
                }
            }

            // Feature Management
            async addFeature(layerId, featureData) {
                try {
                    const response = await fetch(`/api/layers/${layerId}/features`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(featureData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.showSuccess('Feature added successfully');
                        return result.feature;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error adding feature:', error);
                    this.showError('Failed to add feature: ' + error.message);
                }
            }

            async updateFeature(layerId, featureId, featureData) {
                try {
                    const response = await fetch(`/api/layers/${layerId}/features/${featureId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(featureData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.showSuccess('Feature updated successfully');
                        return result.feature;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error updating feature:', error);
                    this.showError('Failed to update feature: ' + error.message);
                }
            }

            async deleteFeature(layerId, featureId) {
                try {
                    const response = await fetch(`/api/layers/${layerId}/features/${featureId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.showSuccess('Feature deleted successfully');
                        return result;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error deleting feature:', error);
                    this.showError('Failed to delete feature: ' + error.message);
                }
            }

            renderLayers() {
                const layersContainer = document.getElementById('layers-container');
                if (!layersContainer) return;

                if (this.layers.length === 0) {
                    layersContainer.innerHTML = '<div class="text-center text-muted">No layers found</div>';
                    return;
                }

                layersContainer.innerHTML = this.layers.map(layer => `
                    <div class="layer-item" data-layer-id="${layer.id}">
                        <input type="checkbox" class="layer-checkbox" id="layer-${layer.id}" checked>
                        <div class="layer-name">
                            <div>${layer.name}</div>
                            <div class="small text-muted">
                                ${layer.type} • ${layer.feature_count} features
                                <span class="badge ${layer.is_editable ? 'bg-success' : 'bg-warning'} layer-editable-badge">
                                    ${layer.is_editable ? 'Editable' : 'Locked'}
                                </span>
                            </div>
                        </div>
                        <div class="layer-actions">
                            <button class="btn btn-sm ${layer.is_editable ? 'btn-warning' : 'btn-success'} toggle-editing" 
                                    title="${layer.is_editable ? 'Disable Editing' : 'Enable Editing'}">
                                <i class="bi ${layer.is_editable ? 'bi-lock' : 'bi-unlock'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary view-attributes" title="Attributes">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary edit-layer" title="Edit Layer">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-layer" title="Delete Layer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="layer-attributes">
                            ${this.renderAttributes(layer)}
                        </div>
                    </div>
                `).join('');

                this.attachLayerEvents();
            }

            renderAttributes(layer) {
                if (!layer.attributes || layer.attributes.length === 0) {
                    return '<div class="text-muted small">No attributes defined</div>';
                }

                return `
                    <div class="attributes-list">
                        <h6>Attributes for ${layer.name}</h6>
                        ${layer.attributes.map(attr => `
                            <div class="attribute-item">
                                <div>
                                    <span class="attribute-name">${attr.name}</span>
                                    <span class="badge bg-secondary">${attr.type}</span>
                                    ${attr.is_required ? '<span class="badge bg-danger">Required</span>' : ''}
                                </div>
                                <button class="btn btn-sm btn-outline-danger delete-attribute" data-attribute-id="${attr.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `).join('')}
                        <button class="btn btn-sm btn-primary add-attribute mt-2" data-layer-id="${layer.id}">
                            <i class="bi bi-plus"></i> Add Attribute
                        </button>
                    </div>
                `;
            }

            attachLayerEvents() {
                // Layer visibility toggle
                document.querySelectorAll('.layer-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', (e) => {
                        const layerId = e.target.closest('.layer-item').dataset.layerId;
                        const layer = this.layers.find(l => l.id == layerId);
                        console.log(`Layer "${layer.name}" visibility: ${e.target.checked ? 'ON' : 'OFF'}`);
                        // Implement map layer visibility logic here
                    });
                });

                // Toggle layer editing
                document.querySelectorAll('.toggle-editing').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const layerId = e.target.closest('.layer-item').dataset.layerId;
                        this.toggleLayerEditing(layerId);
                    });
                });

                // View attributes
                document.querySelectorAll('.view-attributes').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const attributesDiv = e.target.closest('.layer-item').querySelector('.layer-attributes');
                        attributesDiv.style.display = attributesDiv.style.display === 'none' ? 'block' : 'none';
                    });
                });

                // Add attribute
                document.querySelectorAll('.add-attribute').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const layerId = e.target.closest('.add-attribute').dataset.layerId;
                        this.showAddAttributeModal(layerId);
                    });
                });

                // Delete attribute
                document.querySelectorAll('.delete-attribute').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const attributeId = e.target.closest('.delete-attribute').dataset.attributeId;
                        if (confirm('Are you sure you want to delete this attribute? This will remove it from all features.')) {
                            this.deleteAttribute(attributeId);
                        }
                    });
                });

                // Delete layer
                document.querySelectorAll('.delete-layer').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const layerId = e.target.closest('.layer-item').dataset.layerId;
                        if (confirm('Are you sure you want to delete this layer? This will delete all features and attributes.')) {
                            this.deleteLayer(layerId);
                        }
                    });
                });
            }

            showAddAttributeModal(layerId) {
                const modalHtml = `
                    <div class="modal fade" id="addAttributeModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Attribute</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addAttributeForm">
                                        <div class="mb-3">
                                            <label for="attributeName" class="form-label">Attribute Name</label>
                                            <input type="text" class="form-control" id="attributeName" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="attributeType" class="form-label">Data Type</label>
                                            <select class="form-select" id="attributeType" required>
                                                <option value="string">String</option>
                                                <option value="number">Number</option>
                                                <option value="boolean">Boolean</option>
                                                <option value="date">Date</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="defaultValue" class="form-label">Default Value</label>
                                            <input type="text" class="form-control" id="defaultValue">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="isRequired">
                                            <label class="form-check-label" for="isRequired">Required Field</label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveAttribute">Save Attribute</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                const existingModal = document.getElementById('addAttributeModal');
                if (existingModal) {
                    existingModal.remove();
                }

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modal = new bootstrap.Modal(document.getElementById('addAttributeModal'));
                modal.show();

                document.getElementById('saveAttribute').addEventListener('click', () => {
                    const formData = {
                        name: document.getElementById('attributeName').value,
                        type: document.getElementById('attributeType').value,
                        default_value: document.getElementById('defaultValue').value,
                        is_required: document.getElementById('isRequired').checked
                    };

                    this.addAttribute(layerId, formData);
                    modal.hide();
                });
            }

            showSuccess(message) {
                showToast(message, 'success');
            }

            showError(message) {
                showToast(message, 'error');
            }
        }

        // Project Manager Class
        class ProjectManager {
            constructor() {
                this.projects = [];
                this.currentProject = null;
            }

            async loadProjects() {
                try {
                    showLoading();
                    const response = await fetch('/api/projects');
                    if (!response.ok) throw new Error('Failed to load projects');
                    
                    this.projects = await response.json();
                    this.renderProjects();
                    hideLoading();
                    return this.projects;
                } catch (error) {
                    console.error('Error loading projects:', error);
                    this.showError('Failed to load projects: ' + error.message);
                    hideLoading();
                }
            }

            async createProject(projectData) {
                try {
                    showLoading();
                    const response = await fetch('/api/projects', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(projectData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.projects.push(result.project);
                        this.renderProjects();
                        this.showSuccess('Project created successfully');
                        hideLoading();
                        return result.project;
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error creating project:', error);
                    this.showError('Failed to create project: ' + error.message);
                    hideLoading();
                }
            }

            renderProjects() {
                const projectsList = document.getElementById('projects-list');
                if (!projectsList) return;

                if (this.projects.length === 0) {
                    projectsList.innerHTML = '<div class="text-center text-muted">No projects found</div>';
                    return;
                }

                projectsList.innerHTML = this.projects.map(project => `
                    <div class="project-item ${this.currentProject?.id === project.id ? 'active' : ''}" 
                         data-project-id="${project.id}">
                        <div class="fw-bold">${project.name}</div>
                        <div class="small text-muted">
                            ${project.description || 'No description'}
                        </div>
                        <div class="small">
                            <span class="badge bg-secondary">${project.layer_count} layers</span>
                            <span class="badge bg-info">${project.image_count} images</span>
                            ${project.is_public ? '<span class="badge bg-success">Public</span>' : ''}
                        </div>
                    </div>
                `).join('');

                this.attachProjectEvents();
            }

            attachProjectEvents() {
                document.querySelectorAll('.project-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        const projectId = e.currentTarget.dataset.projectId;
                        this.selectProject(projectId);
                    });
                });
            }

            selectProject(projectId) {
                this.currentProject = this.projects.find(p => p.id == projectId);
                this.renderProjects();
                
                // Enable layer creation button
                document.getElementById('create-layer-btn').disabled = false;
                
                // Load layers for this project
                layerManager.loadLayers(projectId);
                
                // Update map with project data
                this.loadProjectToMap();
            }

            loadProjectToMap() {
                // Clear current features
                vectorSource.clear();
                
                // Load layers and their features to the map
                if (this.currentProject && layerManager.layers.length > 0) {
                    layerManager.layers.forEach(layer => {
                        if (layer.geojson_data && layer.geojson_data.features) {
                            const features = new ol.format.GeoJSON().readFeatures(
                                {
                                    type: 'FeatureCollection',
                                    features: layer.geojson_data.features
                                },
                                {
                                    featureProjection: 'EPSG:3857'
                                }
                            );
                            vectorSource.addFeatures(features);
                        }
                    });
                }
            }

            showSuccess(message) {
                showToast(message, 'success');
            }

            showError(message) {
                showToast(message, 'error');
            }
        }

        // Initialize managers
        const projectManager = new ProjectManager();
        const layerManager = new LayerManager();

        // UI Helper Functions
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loading-overlay').style.display = 'none';
        }

        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            
            const toastHtml = `
                <div class="toast align-items-center text-bg-${type === 'error' ? 'danger' : type} border-0" id="${toastId}">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            // Remove toast from DOM after it's hidden
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        function showFeatureForm(feature) {
            const form = document.getElementById('feature-form');
            form.classList.remove('hidden');
            
            // Get the layer for this feature to show attributes
            const layer = layerManager.layers.find(l => 
                l.geojson_data.features.some(f => f.id === feature.get('id'))
            );
            
            if (layer) {
                populateFeatureForm(feature, layer);
            }
        }

        function hideFeatureForm() {
            const form = document.getElementById('feature-form');
            form.classList.add('hidden');
            selectedFeature = null;
        }

        function populateFeatureForm(feature, layer) {
            const properties = feature.getProperties();
            
            // Set basic properties
            document.getElementById('feature-name').value = properties.name || '';
            document.getElementById('feature-description').value = properties.description || '';
            
            // Create dynamic attributes
            const dynamicAttributes = document.getElementById('dynamic-attributes');
            dynamicAttributes.innerHTML = '';
            
            if (layer.attributes) {
                layer.attributes.forEach(attr => {
                    const value = properties[attr.name] || attr.default_value || '';
                    const inputId = `attr-${attr.name}`;
                    
                    const attributeHtml = `
                        <div class="attribute-form-group">
                            <label for="${inputId}" class="form-label">${attr.name}</label>
                            <input type="${getInputType(attr.type)}" 
                                   class="form-control" 
                                   id="${inputId}" 
                                   value="${value}"
                                   ${attr.is_required ? 'required' : ''}>
                        </div>
                    `;
                    dynamicAttributes.insertAdjacentHTML('beforeend', attributeHtml);
                });
            }
        }

        function getInputType(attributeType) {
            switch(attributeType) {
                case 'number': return 'number';
                case 'date': return 'date';
                case 'boolean': return 'checkbox';
                default: return 'text';
            }
        }

        // Drawing Tools
        function setupDrawingTools() {
            const toolButtons = document.querySelectorAll('.tool-btn[data-tool]');
            
            toolButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const tool = e.currentTarget.dataset.tool;
                    activateTool(tool);
                    
                    // Update active state
                    toolButtons.forEach(b => b.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                });
            });
        }

        function activateTool(tool) {
            // Remove existing interactions
            if (drawInteraction) {
                map.removeInteraction(drawInteraction);
            }
            if (modifyInteraction) {
                map.removeInteraction(modifyInteraction);
            }

            switch(tool) {
                case 'point':
                    drawInteraction = new ol.interaction.Draw({
                        source: vectorSource,
                        type: 'Point'
                    });
                    map.addInteraction(drawInteraction);
                    break;
                    
                case 'linestring':
                    drawInteraction = new ol.interaction.Draw({
                        source: vectorSource,
                        type: 'LineString'
                    });
                    map.addInteraction(drawInteraction);
                    break;
                    
                case 'polygon':
                    drawInteraction = new ol.interaction.Draw({
                        source: vectorSource,
                        type: 'Polygon'
                    });
                    map.addInteraction(drawInteraction);
                    break;
                    
                case 'modify':
                    modifyInteraction = new ol.interaction.Modify({
                        source: vectorSource
                    });
                    map.addInteraction(modifyInteraction);
                    break;
                    
                case 'select':
                    // Select interaction is always active
                    break;
                    
                case 'delete':
                    if (selectedFeature) {
                        vectorSource.removeFeature(selectedFeature);
                        hideFeatureForm();
                    }
                    break;
            }

            // Handle draw end event
            if (drawInteraction) {
                drawInteraction.on('drawend', async (e) => {
                    const feature = e.feature;
                    const geometry = feature.getGeometry();
                    
                    // Convert to GeoJSON format
                    const geoJSONFormat = new ol.format.GeoJSON();
                    const geoJSON = geoJSONFormat.writeGeometryObject(geometry);
                    
                    // Get current layer (for simplicity, use first editable layer)
                    const currentLayer = layerManager.layers.find(layer => layer.is_editable);
                    if (currentLayer) {
                        const featureData = {
                            geometry: geoJSON,
                            properties: {
                                name: 'New Feature',
                                description: ''
                            }
                        };
                        
                        const result = await layerManager.addFeature(currentLayer.id, featureData);
                        if (result) {
                            // Update the feature with the ID from the server
                            feature.set('id', result.id);
                        }
                    }
                });
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            initializeMap();
            
            // Load projects
            projectManager.loadProjects();
            
            // Setup drawing tools
            setupDrawingTools();
            
            // Create project button
            document.getElementById('create-project-btn').addEventListener('click', () => {
                const projectName = prompt('Enter project name:');
                if (projectName) {
                    projectManager.createProject({
                        name: projectName,
                        description: prompt('Enter project description (optional):') || '',
                        is_public: false
                    });
                }
            });
            
            // Create layer button
            document.getElementById('create-layer-btn').addEventListener('click', () => {
                const layerName = prompt('Enter layer name:');
                if (layerName && projectManager.currentProject) {
                    layerManager.createLayer({
                        name: layerName,
                        type: 'Mixed',
                        project_id: projectManager.currentProject.id,
                        geojson_data: {
                            type: 'FeatureCollection',
                            features: []
                        },
                        is_editable: true
                    });
                }
            });
            
            // Save feature button
            document.getElementById('save-feature').addEventListener('click', async () => {
                if (selectedFeature && projectManager.currentProject) {
                    const layer = layerManager.layers.find(l => 
                        l.geojson_data.features.some(f => f.id === selectedFeature.get('id'))
                    );
                    
                    if (layer) {
                        const properties = {
                            name: document.getElementById('feature-name').value,
                            description: document.getElementById('feature-description').value
                        };
                        
                        // Get dynamic attributes
                        const dynamicInputs = document.querySelectorAll('#dynamic-attributes input');
                        dynamicInputs.forEach(input => {
                            const attrName = input.id.replace('attr-', '');
                            let value = input.value;
                            
                            // Convert value based on attribute type
                            if (input.type === 'checkbox') {
                                value = input.checked;
                            } else if (input.type === 'number') {
                                value = parseFloat(value);
                            }
                            
                            properties[attrName] = value;
                        });
                        
                        const featureData = {
                            properties: properties
                        };
                        
                        await layerManager.updateFeature(layer.id, selectedFeature.get('id'), featureData);
                        hideFeatureForm();
                    }
                }
            });
            
            // Cancel edit button
            document.getElementById('cancel-edit').addEventListener('click', hideFeatureForm);
            
            // Delete feature button
            document.getElementById('delete-feature').addEventListener('click', async () => {
                if (selectedFeature && projectManager.currentProject) {
                    const layer = layerManager.layers.find(l => 
                        l.geojson_data.features.some(f => f.id === selectedFeature.get('id'))
                    );
                    
                    if (layer && confirm('Are you sure you want to delete this feature?')) {
                        await layerManager.deleteFeature(layer.id, selectedFeature.get('id'));
                        vectorSource.removeFeature(selectedFeature);
                        hideFeatureForm();
                    }
                }
            });
            
            // Home button
            document.getElementById('home-btn').addEventListener('click', () => {
                map.getView().setCenter(ol.proj.fromLonLat([78.8209, 10.3833]));
                map.getView().setZoom(12);
            });
            
            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const closeSidebar = document.getElementById('close-sidebar');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                sidebarToggle.classList.toggle('collapsed');
                
                const icon = sidebarToggle.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.className = 'bi bi-chevron-right';
                } else {
                    icon.className = 'bi bi-chevron-left';
                }
            });

            closeSidebar.addEventListener('click', function() {
                sidebar.classList.add('collapsed');
                sidebarToggle.classList.add('collapsed');
                sidebarToggle.querySelector('i').className = 'bi bi-chevron-right';
            });
        });
    </script>
</body>
</html>